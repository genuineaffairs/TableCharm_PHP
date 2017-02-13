<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagepoll_AdminController extends Core_Controller_Action_Admin {

  //ACTION FOR APPROVED AND DIS-APPROVED SITEPAGE-POLL
  public function approvedAction() {

    //GET THE PAGEPOLL MODEL
    $sitepagepoll = Engine_Api::_()->getItem('sitepagepoll_poll', $this->_getParam('poll_id'));

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      if ($sitepagepoll->approved == 0)
        $sitepagepoll->approved = 1;
      else 
        $sitepagepoll->approved = 0;
      
      $sitepagepoll->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitepagepoll/manage');
  }

  //ACTION FOR DELETE THE SITEPAGE-POLL
  public function deleteAction() {

    //RENDER LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET THE POLL ID
    $this->view->poll_id = $poll_id = $this->_getParam('poll_id');

    //GET THE PAGEPOLL MODEL
    $sitepagepoll = Engine_Api::_()->getItem('sitepagepoll_poll', $poll_id);

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //FINALLY DELETE POLL MODEL
        $sitepagepoll->delete();
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
    $this->renderScript('admin/delete.tpl');
  }

}
?>