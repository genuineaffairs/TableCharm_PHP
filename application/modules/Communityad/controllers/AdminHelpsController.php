<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminHelpsController.php  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_AdminHelpsController extends Core_Controller_Action_Admin {

  public function helpAndLearnmoreAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('communityad_admin_main', array(), 'communityad_admin_user_manage');

    $page = $this->_getParam('page', 1);
    $sortingColumnName = $this->_getParam('idSorting', 0);
    $pagesettingsTable = Engine_Api::_()->getItemTable('communityad_infopage');
    $pagesettingsSelect = $pagesettingsTable->select()->where('package != 1');
    $this->view->paginator = Zend_Paginator::factory($pagesettingsSelect);

    $this->view->paginator->setItemCountPerPage(10);
    $this->view->paginator->setCurrentPageNumber($page);
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $contentObject = Engine_Api::_()->getItem('communityad_infopage', $value);
          if (!empty($contentObject->delete)) {
            $contentObject->delete();
          }
        }
      }
    }
  }

  // Function: When approved or disapproved page (Help & Learn more page).
  public function statusAction() {
    $status = $this->_getParam('status');
    $infoId = $this->_getParam('id');
    if (empty($status)) {
      $this->view->title = $this->view->translate("Disable Page?");
      $this->view->discription = $this->view->translate("Are you sure that you want to disable this 'Help & Learn More' page? After being disabled this will not be shown in 'Help & Learn More' options.");
      $this->view->bouttonLink = $this->view->translate("Disable");
    } else {
      $this->view->title = $this->view->translate("Enable Page?");
      $this->view->discription = $this->view->translate("Are you sure that you want to enable this 'Help & Learn More' page? After being enabled this will be shown in 'Help & Learn More' options.");
      $this->view->bouttonLink = $this->view->translate("Enable");
    }
    // Check post
    if ($this->getRequest()->isPost()) {
      $pagesettingsTable = Engine_Api::_()->getDbTable('infopages', 'communityad');
      $pagesettingsTable->update(array('status' => $status), array('infopage_id =?' => $infoId));

      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => 10,
              'parentRefresh' => 10,
              'messages' => array('Successfully done.')
      ));
    }
  }

  public function helpPageCreateAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('communityad_admin_main', array(), 'communityad_admin_user_manage');

    $this->view->page_id = $page_id = $this->_getParam('page_id', 0);
    // Condition: For check if selected page is faq page then show the manage page for the faq page.
    if (!empty($page_id)) {
      $this->view->faqCheck = $faqCheck = Engine_Api::_()->getItem('communityad_infopage', $page_id)->faq;
    } else {
      $this->view->faqCheck = $faqCheck = 0;
    }
    // Condition: Only page allow which are not faq.
    if (empty($faqCheck)) {
			
      $this->view->form = $form = new Communityad_Form_Admin_Helppagecreate();


		$textFlag = $this->_getParam('textFlag', 0);
		if( empty( $textFlag ) ) {
			$form->removeElement('text_description');
			$textLinkFlag = $this->view->url(array('module' => 'communityad', 'controller' => 'helps', 'action' => 'help-page-create', 'textFlag' => 1, 'page_id' => $page_id), 'admin_default', true);
			$textDescription = $this->view->translate("If your site supports multiple laguage then <a href='%s'> click here </a> for the compatible Text input box.", $textLinkFlag);
		}else {
			$form->removeElement('description');
			$textLinkFlag = $this->view->url(array('module' => 'communityad', 'controller' => 'helps', 'action' => 'help-page-create', 'textFlag' => 0, 'page_id' => $page_id), 'admin_default', true);
			$textDescription = $this->view->translate("If your site supports only one laguage then <a href='%s'> click here </a> for the compatible Text input box.", $textLinkFlag);
		}

		$form->text_flag->setDescription($textDescription);

		$form->text_flag->addDecorator( 'Description' , array ( 'placement' => Zend_Form_Decorator_Abstract::PREPEND , 'escape' => false ) ) ;



      if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
        $values = $form->getValues();
				if( !empty($values['text_description']) ){ 
					$values['description'] = $values['text_description'];
					unset($values['text_description']);
				}
        $contentTable = Engine_Api::_()->getDbTable('infopages', 'communityad');
				include_once(APPLICATION_PATH ."/application/modules/Communityad/controllers/license/license2.php");
        $this->_helper->redirector->gotoRoute(array('module' => 'communityad', 'controller' => 'helps', 'action' => 'help-and-learnmore'), 'admin_default', true);
      }
    } else { // Condition: Faq page come here.
      // Show the navigation bar.
      $page = $this->_getParam('page', 1); // Page id: Controll pagination.
			$this->view->page_title = Engine_Api::_()->getItem('communityad_infopage', $page_id)->title;
      $socialengineTable = Engine_Api::_()->getItemTable('communityad_faq');
      $socialengineSelect = $socialengineTable->select()
              ->where('status =?', 1)->where('type =?', $faqCheck)->order('faq_id DESC');

      $this->view->paginator = Zend_Paginator::factory($socialengineSelect);

      $this->view->paginator->setItemCountPerPage(10);
      $this->view->paginator->setCurrentPageNumber($page);
      if ($this->getRequest()->isPost()) {
        $values = $this->getRequest()->getPost();
        foreach ($values as $key => $value) {
          if ($key == 'delete_' . $value) {
            Engine_Api::_()->getItem('communityad_faq', $value)->delete();
          }
        }
      }
    }
  }

  public function helpPageDeleteAction() {
    $pagesetting_id = $this->_getParam('page_id');
    $this->view->pagesetting_id = $pagesetting_id;
    // Check post
    if ($this->getRequest()->isPost()) {
      $pagesettingTable = Engine_Api::_()->getItem('communityad_infopage', $pagesetting_id);
      if (!empty($pagesettingTable)) {
        $pagesettingTable->delete();
      }
      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => 10,
              'parentRefresh' => 10,
              'messages' => array('Deleted Successsfully.')
      ));
    }
  }

	public function defaultHelpMsgAction(){
		$pagesetting_id = $this->_getParam('page_id');
		if( !empty($pagesetting_id) ) {
			$this->view->item = $item = Engine_Api::_()->getItem('communityad_infopage', $pagesetting_id);
		}
	}

}