<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Communityad
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminFaqsController.php 2010-08-010 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_AdminFaqsController extends Core_Controller_Action_Admin {

  public function faqcreateAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('communityad_admin_main', array(), 'communityad_admin_user_manage');
    $this->view->page_id = $page_id = $this->_getParam('page_id');
    $faq_type = Engine_Api::_()->getItem('communityad_infopage', $page_id)->faq;
    $faq_id = $this->_getParam('faq_id');
    $this->view->form = $form = new Communityad_Form_Admin_Faqcreate();

    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $this->getRequest()->getPost();
      $communityadFaqTable = Engine_Api::_()->getItemTable('communityad_faq');
      if (empty($faq_id)) {
        $communityadFaqInsert = $communityadFaqTable->createRow();
        $communityadFaqInsert->question = $values['faq_question'];
        $communityadFaqInsert->answer = $values['faq_answer'];
        $communityadFaqInsert->type = $faq_type;
        $communityadFaqInsert->poster_id = $user_id;
        $communityadFaqInsert->status = 1;
        $communityadFaqInsert->save();
      } else {
        $communityadFaqTable->update(array('question' => $values['faq_question'], 'answer' => $values['faq_answer']), array('faq_id =?' => $faq_id));
      }
      $this->_helper->redirector->gotoRoute(array('module' => 'communityad', 'controller' => 'helps', 'action' => 'help-page-create', 'page_id' => $page_id), 'admin_default', true);
    }
  }

  public function deleteAction() {
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('faq_id');
    $this->view->faq_id = $id;
    if ($this->getRequest()->getPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $featured = Engine_Api::_()->getItem('communityad_faq', $id);
        $featured->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => true,
              'parentRefresh' => true,
              'messages' => array('FAQ has been deleted')
      ));
    }
  }

	public function faqDefaultMsgAction() { 
		$faq_id = $this->_getParam('faq_id');
		if( !empty($faq_id) ) {
			$this->view->item = $item = Engine_Api::_()->getItem('communityad_faq', $faq_id);
		}
	}

}