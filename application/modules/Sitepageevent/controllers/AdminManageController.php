<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_AdminManageController extends Core_Controller_Action_Admin {

  //ACTION FOR MANAGE EVENTS
  public function indexAction() {
    
    //CREATE NAVIGATION TABS
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepageevent_admin_main', array(), 'sitepageevent_admin_main_manage');

    //HIDDEN SEARCH FORM CONTAIN ORDER AND ORDER DIRECTION  
    $this->view->formFilter = $formFilter = new Sitepageevent_Form_Admin_Manage_Filter();
    $page = $this->_getParam('page', 1);

    //GET EVENTS
    $tableUser = Engine_Api::_()->getItemTable('user')->info('name');
    $tablesitepage = Engine_Api::_()->getItemTable('sitepage_page')->info('name');
    $table = Engine_Api::_()->getDbTable('events', 'sitepageevent');
    $rname = $table->info('name');
    $select = $table->select()
            ->setIntegrityCheck(false)
            ->from($rname, array('event_id', 'page_id', 'user_id', 'title', 'creation_date', 'view_count','featured'))
            ->joinLeft($tableUser, "$rname.user_id = $tableUser.user_id", 'username')
            ->joinLeft($tablesitepage, "$rname.page_id = $tablesitepage.page_id", 'title AS sitepage_title');
    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
        'order' => 'event_id',
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);
    $select->order((!empty($values['order']) ? $values['order'] : 'event_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    $this->view->paginator = array();
    include_once APPLICATION_PATH . '/application/modules/Sitepageevent/controllers/license/license2.php';
  }

  //ACTION FOR MULTI DELETE EVENTS
  public function multiDeleteAction() {
    
    //GET THE IDS WHICH EVNETS WE WANT TO DELETE
    $this->view->event_ids = $event_ids = $this->_getParam('event_ids', null);

    $confirm = $this->_getParam('confirm', false);

    //COUNTING THE EVENT IDS
    $this->view->count = count(explode(",", $event_ids));

    //IF NOT POST OR FORM NOT VALID, RETURN
    if ($this->getRequest()->isPost() && $confirm == true) {

      //MAKING THE EVENT ID ARRAY.
      $eventids_array = explode(",", $event_ids);

      foreach ($eventids_array as $event_id) {
				//DELETE EVENT, ALBUM AND EVENT IMAGES
				Engine_Api::_()->sitepageevent()->deleteContent($event_id);
      }
      //REDIRECTING TO THE MANAGE EVENTS PAGE
      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }

  //ACTION FOR DELETE THE EVENT
  public function deleteAction() {
  	
    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET THE EVENT ID WHICH WE WANT TO DELETE
    $this->view->event_id = $event_id = $this->_getParam('event_id');

    //IF NOT POST OR FORM NOT VALID, RETURN
    if ($this->getRequest()->isPost()) {
      //PROCESS
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //DELETE EVENT ALBUM AND EVENT IMAGES
        Engine_Api::_()->sitepageevent()->deleteContent($event_id);

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }

    //RENDER THE DELETE TPL FILE
    $this->renderScript('admin-manage/delete.tpl');
  }

   //ACTION FOR MAKE EVENT FEATURED AND REMOVE FEATURED EVENT 
  public function featuredeventAction() {

    //GET OFFER ID
    $eventId = $this->_getParam('id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', $eventId);
      if ($sitepageevent->featured == 0) {
        $sitepageevent->featured = 1;
      } else {
        $sitepageevent->featured = 0;
      }
      $sitepageevent->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitepageevent/manage');
  }


}

?>