<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_AdminManageController extends Core_Controller_Action_Admin {

  //ACTION FOR MANAGE NOTES
  public function indexAction() {

    //CREATE NAVIGATION TABS
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepagenote_admin_main', array(), 'sitepagenote_admin_main_manage');

    //HIDDEN SEARCH FORM CONTAIN ORDER AND ORDER DIRECTION  
    $this->view->formFilter = $formFilter = new Sitepagenote_Form_Admin_Manage_Filter();
    $page = $this->_getParam('page', 1);

    //GET NOTES
    $tableUser = Engine_Api::_()->getItemTable('user')->info('name');
    $tablesitepage = Engine_Api::_()->getItemTable('sitepage_page')->info('name');
    $table = Engine_Api::_()->getDbtable('notes', 'sitepagenote');
    $rName = $table->info('name');
    $select = $table->select()
            ->setIntegrityCheck(false)
            ->from($rName, array('note_id', 'page_id', 'owner_id', 'title', 'creation_date', 'view_count', 'comment_count', 'total_photos', 'like_count', 'creation_date','featured'))
            ->joinLeft($tableUser, "$rName.owner_id = $tableUser.user_id", 'username')
            ->joinLeft($tablesitepage, "$rName.page_id = $tablesitepage.page_id", 'title AS sitepage_title');
    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array('order' => 'note_id', 'order_direction' => 'DESC'), $values);

    $this->view->assign($values);
    $select->order((!empty($values['order']) ? $values['order'] : 'note_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    $this->view->paginator = array();
    include_once APPLICATION_PATH . '/application/modules/Sitepagenote/controllers/license/license2.php';
  }

  //ACTION FOR MULTI DELETE NOTES
  public function multiDeleteAction() {

    //GET THE NOTE IDS WHICH NOTES WE WANT TO DELETE
    $this->view->ids = $note_ids = $this->_getParam('ids', null);
    $confirm = $this->_getParam('confirm', false);

    //COUNTING THE NOTE IDS
    $this->view->count = count(explode(",", $note_ids));

    //IF NOT POST OR FORM NOT VALID, RETURN
    if ($this->getRequest()->isPost() && $confirm == true) {

      //MAKING THE NOTE ID ARRAY.
      $note_ids_array = explode(",", $note_ids);

      foreach ($note_ids_array as $note_id) {
				//DELETE NOTE, ALBUM AND NOTE IMAGES
				Engine_Api::_()->sitepagenote()->deleteContent($note_id);
      }
      //REDIRECTING TO THE MANAGE NOTES PAGE
      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }

  //ACTION FOR DELETE THE NOTE
  public function deleteAction() {

    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET THE NOTE ID WHICH WE WANT TO DELETE
    $this->view->note_id = $note_id = $this->_getParam('id');

    //IF NOT POST OR FORM NOT VALID, RETURN
    if ($this->getRequest()->isPost()) {
      //PROCESS
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
 
        //DELETE NOTE ALBUM AND NOTE IMAGES
        Engine_Api::_()->sitepagenote()->deleteContent($note_id);

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('')
      ));
    }

    //RENDER THE DELETE TPL FILE
    $this->renderScript('admin-manage/delete.tpl');
  }

  //ACTION FOR MAKE NOTE FEATURED AND REMOVE FEATURED NOTE 
  public function featurednoteAction() {

    //GET OFFER ID
    $noteId = $this->_getParam('id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $noteId);
      if ($sitepagenote->featured == 0) {
        $sitepagenote->featured = 1;
      } else {
        $sitepagenote->featured = 0;
      }
      $sitepagenote->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitepagenote/manage');
  }

}

?>