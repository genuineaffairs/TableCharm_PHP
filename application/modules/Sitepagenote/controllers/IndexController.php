<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_IndexController extends Seaocore_Controller_Action_Standard {

  public function init() {

    //GET PAGE ID
    $page_id = $this->_getParam('page_id');

    //PACKAGE BASE PRIYACY START
    if (!empty($page_id)) {
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagenote")) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        }
      } else {
        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sncreate');
        if (empty($isPageOwnerAllow)) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        }
      }
    }
    //PACKAGE BASE PRIYACY END    
    else {
      if ($this->_getParam('note_id') != null) {
        $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $this->_getParam('note_id'));
        $page_id = $sitepagenote->page_id;
      }
    }

    //GET NOTE ID
    $note_id = $this->_getParam('note_id');
    if ($note_id) {
      $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $note_id);
      if ($sitepagenote) {
        Engine_Api::_()->core()->setSubject($sitepagenote);
      }
    }
  }

  //ACTION FOR CREATE THE NEW NOTE
  public function createAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();

    //PAGE ID 
    $page_id = $this->_getParam('page_id');

    //SEND TAB TO TPL FILE
    $this->view->tab_selected_id = $this->_getParam('tab');

    //GET SITEPAGE ITEM
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    $sitepagenote_viewPer = Zend_Registry::isRegistered('sitepagenote_viewPer') ? Zend_Registry::get('sitepagenote_viewPer') : null;
    $getPackageNoteCreate = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepagenote');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin) || empty($getPackageNoteCreate)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = $can_edit = 0;
    } else {
      $this->view->can_edit = $can_edit = 1;
    }
 
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sncreate');
    if (empty($isManageAdmin) && empty($can_edit)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //CHECKING THE HOSTNAME
    $sitepageModHostName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
    if (empty($sitepagenote_viewPer)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

    //SHOW NOTE CREATE FORM
    $this->view->form = $form = new Sitepagenote_Form_Create();
    
    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
			$form->addElement("dummy", "dummy", array('label' => 'Main Photo', 'description' => 'Sorry, the browser you are using does not support Photo uploading. We recommend you to create a Note from your mobile / tablet without uploading a main photo for it. You can later upload the main photo from your Desktop.', 'order' => 6, 'style' => 'display:none;'));
			if (preg_match('/' . 'iPad' . '/i', $_SERVER['HTTP_USER_AGENT'])) {
				if (isset($form->photo)){
					$form->removeElement('photo');
				}	
			} else {
         if (isset($form->photo)){
           $form->photo->setAttrib('accept', "image/*");
         }
      }
    }

    // Populate with categories
    $categories = Engine_Api::_()->getDbtable('categories', 'sitepagenote')->getCategoriesAssoc();
    asort($categories, SORT_LOCALE_STRING);
    $categoryOptions = array('0' => '');
    foreach ($categories as $k => $v) {
      $categoryOptions[$k] = $v;
    }
    if (sizeof($categoryOptions) <= 1) {
      $form->removeElement('category_id');
    } else {
      $form->category_id->setMultiOptions($categoryOptions);
    }    

    //CHECKING THE TYPE OF THE NOTE
    $get_note_type = Engine_Api::_()->getItemTable('sitepagenote_note')->getNoteType();
    $isModType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagenote.set.type', 0);
    if (empty($isModType)) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepagenote.draft.type', convert_uuencode($sitepageModHostName));
    }

    //CHECK FORM VALIDATION
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //CONNECT WITH NOTE TABLE
      $tableNote = Engine_Api::_()->getItemTable('sitepagenote_note');
      $db = $tableNote->getAdapter();
      $db->beginTransaction();
      try {
        //GET POSTED VALUES FROM CREATE FORM
        $values = array_merge($form->getValues(), array(
            'owner_id' => $viewer->getIdentity(),
            'page_id' => $page_id,
            'parent_type' => 'sitepage_page',
            'view_count' => 1
                ));

        if (empty($get_note_type)) {
          return;
        }

        //CREATE THE NOTE
        $sitepagenote = $tableNote->createRow();
        $sitepagenote->setFromArray($values);
        $sitepagenote->body = $_POST['body'];
        $sitepagenote->save();

        //COMMENT PRIVACY
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        $commentMax = array_search("everyone", $roles);
        foreach ($roles as $i => $role) {
          $auth->setAllowed($sitepagenote, $role, 'comment', ($i <= $commentMax));
        }

        //CHECKING THE PHOTO FIELD EMPTY OR NOT.
        if (!empty($values['photo'])) {
          $sitepagenote->setPhoto($form->photo);
          $sitepagenote->total_photos++;
          $sitepagenote->save();
        }

        //CHECKING THE TAG FIELD EMPTY OR NOT.
        if (!empty($values['tags'])) {
          $sitepagenote->tags()->addTagMaps($viewer, preg_split('/[,]+/', $values['tags']));
        }

        //INSERT ACTIVITY IF NOTE IS PUBLISHED
        if (isset($values['draft']) && $values['draft'] == 0 && $values['search'] == 1) {
          $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
          $activityFeedType = null;
          if (Engine_Api::_()->sitepage()->isPageOwner($sitepage) && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
            $activityFeedType = 'sitepagenote_admin_new';
          elseif ($sitepage->all_post || Engine_Api::_()->sitepage()->isPageOwner($sitepage))
            $activityFeedType = 'sitepagenote_new';
          if ($activityFeedType) {
            $action = $actionTable->addActivity($viewer, $sitepage, $activityFeedType);
            Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
          }
          //MAKE SURE ACTION EXISTS BEFOR ATTACHING THE NOTE TO THE ACTIVITY
          if ($action != null) {
            $actionTable->attachActivity($action, $sitepagenote);
          }

          //SENDING ACTIVITY FEED TO FACEBOOK.
          $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
          if (!empty($enable_Facebooksefeed)) {
            $note_array = array();
            $note_array['type'] = 'sitepagenote_new';
            $note_array['object'] = $sitepagenote;
            Engine_Api::_()->facebooksefeed()->sendFacebookFeed($note_array);
          }

					
					//PAGE NOTE CREATE NOTIFICATION AND EMAIL WORK
					$sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;
					if(!empty($action)) {
						if ($sitepageVersion >= '4.3.0p1') {
							Engine_Api::_()->sitepage()->sendNotificationEmail($sitepagenote, $action, 'sitepagenote_create', 'SITEPAGENOTE_CREATENOTIFICATION_EMAIL');
							
							$isPageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->isPageAdmins($viewer->getIdentity(), $page_id);
							if (!empty($isPageAdmins)) {

								//NOTIFICATION FOR ALL FOLLWERS.
								Engine_Api::_()->sitepage()->sendNotificationToFollowers($sitepagenote, $action, 'sitepagenote_create');
							}
						}
					}
        }

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      //CHECKING IF IMAGE UPLOAD IS ALLOWED OR NOT.IF ALLOWED THEN RETURN TO THE PHOTO SUCCESS PAGE AND IF THE IMAGE ALLOWED IS NOT TRUE THEN WE REDIRECTING TO THE NOTE VIEW PAGE.
      if (Engine_Api::_()->getApi('settings', 'core')->sitepagenote_allow_image) {
        return $this->_helper->redirector->gotoRoute(array('action' => 'success', 'note_id' => $sitepagenote->note_id, 'tab' => $this->view->tab_selected_id), 'sitepagenote_specific', true);
      } else {
        return $this->_helper->redirector->gotoUrl($sitepagenote->getHref(), array('prependBase' => false));
      }
    }
  }

  //ACTION FOR SHOWING THE ADD PHOTO FORM
  public function successAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET THE ITEM OF SITEPAGENOTE
    $this->view->sitepagenote = $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $this->_getParam('note_id'));

    //CHECKING WHETHER THE USER HAVE THE PERMISSION OR NOT.
    if (Engine_Api::_()->user()->getViewer()->getIdentity() != $sitepagenote->owner_id) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

    //PAGE ID
    $page_id = $sitepagenote->page_id;

    //SEND TAB TO TPL FILE
    $this->view->tab_selected_id = $this->_getParam('tab');

    //GET SITEPAGE ITEM
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //IF FORM IS POSTED SUCCESSFULLY THEN WE REDIRECT IT.
    if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {
      if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
				return $this->_gotoRouteCustom(array('note_id' => $sitepagenote->note_id, 'tab' => $this->view->tab_selected_id), 'sitepagenote_photoupload', true);
      } else {
				return $this->_gotoRouteCustom(array('note_id' => $sitepagenote->note_id, 'tab' => $this->view->tab_selected_id), 'sitepagenote_sitemobilephotoupload', true);
      }
    }
  }

  //ACTION FOR EDIT THE NOTE
  public function editAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();

    //SEND TAB ID TO TPL FILE
    $this->view->tab_selected_id = $this->_getParam('tab');

    //NOTE ITEM
    $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $this->_getParam('note_id'));
    $getPackageNoteEdit = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepagenote');

    //PAGE ID
    $page_id = $sitepagenote->page_id;

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

    //GENERATE EDIT FORM
    $this->view->form = $form = new Sitepagenote_Form_Edit(array('item' => $sitepagenote));

    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
			$form->addElement("dummy", "dummy", array('label' => 'Main Photo', 'description' => 'Sorry, the browser you are using does not support Photo uploading. We recommend you to edit a Note from your mobile / tablet without uploading a main photo for it. You can later upload the main photo from your Desktop.', 'order' => 7, 'style' => 'display:none;'));
			if (preg_match('/' . 'iPad' . '/i', $_SERVER['HTTP_USER_AGENT'])) {
				if (isset($form->photo)){
					$form->removeElement('photo');
				}	
			} else {
         if (isset($form->photo)){
           $form->photo->setAttrib('accept', "image/*");
         }
      }
    }

    //GET SITEPAGE ITEM
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //SUPERADMIN, NOTE OWNER AND SITEPAGE OWNER CAN EDIT VIDEO
    if ($viewer->getIdentity() != $sitepagenote->owner_id && $can_edit != 1) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //IF NOTE IN PUBLISHED STAT THAN REMOVE ELEMENT
    if ($sitepagenote->draft == "0") {
      $form->removeElement('draft');
    }

    //REMOVING THE PHOTO ELEMENT FROM THE FORM
    $form->removeElement('photo');

    //PREPARE TAGS
    $sitepageTags = $sitepagenote->tags()->getTagMaps();
    $tagString = '';
    foreach ($sitepageTags as $tagmap) {
      if ($tagString !== '') {
        $tagString .= ', ';
      }
      $tagString .= $tagmap->getTag()->getTitle();
    }
    $form->tags->setValue($tagString);

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$this->getRequest()->isPost()) {
      $form->populate($sitepagenote->toArray());
      return;
    }

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //GETTING THE FORM VALUES
    $values = empty($getPackageNoteEdit) ? null : $form->getValues();
    if (empty($values)) {
      return;
    }

    //PROCESS
    Engine_Api::_()->sitepagenote()->setNotePackages();
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      //SAVE NOTE DETAIL
      $sitepagenote->setFromArray($values);
      $sitepagenote->body = $_POST['body'];
      $sitepagenote->modified_date = date('Y-m-d H:i:s');
      $sitepagenote->tags()->setTagMaps($viewer, preg_split('/[,]+/', $values['tags']));
      $sitepagenote->save();

      //INSERT NEW ACTIVITY IF NOTE IS JUST GETTING PUBLISHED
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionsByObject($sitepagenote);
      if (count($action->toArray()) <= 0 && isset($values['draft'])) {
        if ($values['draft'] == 0 && $sitepagenote->search == 1) {
          $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
          $activityFeedType = null;
          if (Engine_Api::_()->sitepage()->isPageOwner($sitepage) && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
            $activityFeedType = 'sitepagenote_admin_new';
          elseif ($sitepage->all_post || Engine_Api::_()->sitepage()->isPageOwner($sitepage))
            $activityFeedType = 'sitepagenote_new';
          if ($activityFeedType) {
            $action = $actionTable->addActivity($viewer, $sitepage, $activityFeedType);
            Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
          }
          //MAKE SURE ACTION EXISTS BEFOR ATTACHING THE NOTE TO THE ACTIVITY
          if ($action != null) {
            $actionTable->attachActivity($action, $sitepagenote);
          }
        }
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //REDIRECTING TO THE NOTE VIEW PAGE
    return $this->_redirectCustom($sitepagenote->getHref(), array('prependBase' => false));
  }

  //ACTION FOR DELETE NOTES
  public function deleteAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //PAGE ID
    $page_id = $this->_getParam('page_id');

    //SEND TAB TO TPL FILE
    $this->view->tab_selected_id = $this->_getParam('tab');

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

    //GET NOTE ITEM
    $this->view->sitepagenote = $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $this->_getParam('note_id'));

    //GET SITEPAGE ITEM
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //NOTE OWNER AND PAGE OWNER CAN DELETE VIDEO
    if (Engine_Api::_()->user()->getViewer()->getIdentity() != $sitepagenote->owner_id && $can_edit != 1) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //IF NOT POST OR FORM NOT VALID
    if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {

      //DELETE NOTE ALBUM AND VIDEO PHOTOS
      Engine_Api::_()->sitepagenote()->deleteContent($this->_getParam('note_id'));

      //AFTER DELETING THE NOTE WE REDIRECT ON SITEPAGE VIEW PAGE
      return $this->_gotoRouteCustom(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id), 'tab' => $this->view->tab_selected_id), 'sitepage_entry_view', true);
    }
  }

  //ACTION FOR VIEW THE NOTE
  public function viewAction() {

     //IF SITEPAGENOTE SUBJECT IS NOT THEN RETURN
    if (!$this->_helper->requireSubject('sitepagenote_note')->isValid())
      return;

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET NOTE ITEM
    $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $this->getRequest()->getParam('note_id'));

    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagenote->page_id);

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagenote")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sncreate');
      if (empty($isPageOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sncreate');
    if (empty($isManageAdmin)) {
      $this->view->can_create = 0;
    } else {
      $this->view->can_create = 1;
    }
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //CHECKING THE USER HAVE THE PERMISSION TO VIEW THE NOTE OR NOT
    if ($viewer_id != $sitepagenote->owner_id && $can_edit != 1 && $sitepagenote->search != 1) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }   

    //NAVIGATION WORK FOR FOOTER.(DO NOT DISPLAY NAVIGATION IN FOOTER ON VIEW PAGE.)
    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
         if(!Zend_Registry::isRegistered('sitemobileNavigationName')){
         Zend_Registry::set('sitemobileNavigationName','setNoRender');
         }
    }
    //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }
  }

  //ACTION FOR NOTE PUBLISH
  public function publishAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET LOGGED IN USER INFORMATION
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //SMOOTHBOX
    if (null === $this->_helper->ajaxContext->getCurrentContext()) {
      $this->_helper->layout->setLayout('default-simple');
    } else {//NO LAYOUT
      $this->_helper->layout->disableLayout(true);
    }

    //SEND TAB AND NOTE ID TO TPL FILE
    $this->view->tab_selected_id = $this->_getParam('tab');
    $note_id = $this->view->note_id = $this->_getParam('note_id');

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$this->getRequest()->isPost())
      return;

    //GET NOTE ITEM
    $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $note_id);

    //PAGE ID
    $page_id = $sitepagenote->page_id;

    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK    
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //SUPERADMIN, NOTE OWNER AND SITEPAGE OWNER CAN PUBLISH NOTE
    if ($viewer_id != $sitepagenote->owner_id && $can_edit != 1) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //SUPERADMIN, VIDEO OWNER AND SITEPAGE OWNER CAN PUBLISH VIDEO
    if ($viewer_id == $sitepagenote->owner_id || $can_edit == 1) {
      $this->view->permission = true;
      $this->view->success = false;
      $db = Engine_Api::_()->getDbtable('notes', 'sitepagenote')->getAdapter();
      $db->beginTransaction();
      try {
        $sitepagenote->modified_date = new Zend_Db_Expr('NOW()');
        $sitepagenote->draft = 0;
        $sitepagenote->save();
        $db->commit();
        $this->view->success = true;
      } catch (Exception $e) {
        $db->rollback();
        throw $e;
      }

      //ADD ACTIVITY ONLY IF NOTES IS PUBLISHED
      if ($sitepagenote->draft == 0) {
        $api = Engine_Api::_()->getDbtable('actions', 'activity');
        $activityFeedType = null;
        $creator = Engine_Api::_()->getItem('user', $sitepagenote->owner_id);
        if (Engine_Api::_()->sitepage()->isPageOwner($sitepage) && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
          $activityFeedType = 'sitepagenote_admin_new';
        elseif ($sitepage->all_post || Engine_Api::_()->sitepage()->isPageOwner($sitepage))
          $activityFeedType = 'sitepagenote_new';

        if ($activityFeedType) {
          $action = $api->addActivity($creator, $sitepage, $activityFeedType);
          Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
        }
        //MAKE SURE ACTION EXISTS BEFORE ATTACHING THE NOTES TO THE ACTIVITY
        if ($action != null) {
          $api->attachActivity($action, $sitepagenote);
          $sitepagenote->save();
        }
      }
    } else {
      $this->view->permission = false;
    }

    $this->_forwardCustom('success', 'utility', 'core', array(
        'smoothboxClose' => 2,
        'parentRedirect' => $this->_helper->url->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id), 'tab' => $this->view->tab_selected_id), 'sitepage_entry_view'),
        'parentRedirectTime' => '2',
        'format' => 'smoothbox',
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Successfully Published !')
            )));
  }

  //ACTION FOR UPLOADING THE NOTE PHOTOS FROM THE EDITOR
  public function uploadPhotoAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //LAYOUT
    $this->_helper->layout->disableLayout();
    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    //PAGE ID
    $page_id = $this->_getParam('page_id');
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!isset($_FILES['userfile']) || !is_uploaded_file($_FILES['userfile']['tmp_name'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    //PROCESS
    $db = Engine_Api::_()->getDbtable('photos', 'sitepage')->getAdapter();
    $db->beginTransaction();
    try {
      //CREATE PHOTO
      $tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitepage');
      $photo = $tablePhoto->createRow();
      $photo->setFromArray(array(
          'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
          'page_id' => $page_id
      ));
      $photo->save();
      $photo->setPhoto($_FILES['userfile']);

      $this->view->status = true;
      $this->view->name = $_FILES['userfile']['name'];
      $this->view->photo_id = $photo->photo_id;
      $this->view->photo_url = $photo->getPhotoUrl();

      $album = Engine_Api::_()->getDbtable('albums', 'sitepage')->getSpecialAlbum($sitepage, 'note');

      //SET PAGE PHOTO PARAMS
      $paramsPhoto = array();
      $paramsPhoto['page_id'] = $page_id;
      $paramsPhoto['album_id'] = $album->album_id;
      $paramsPhoto['order'] = 'order ASC';
      $paramsPhoto['start'] = 1;
      $paramsPhoto['end'] = 1;

      //FETCHING PHOTOS
      $fecthPhotos = Engine_Api::_()->getDbtable('photos', 'sitepage')->getphotos($paramsPhoto)->toarray();

      $photo->collection_id = $album->album_id;
      $photo->album_id = $album->album_id;
      $order = 0;
      if (!empty($fecthPhotos)) {
        $order = $fecthPhotos[0]['order'] + 1;
      }
      $photo->order = $order;
      $photo->save();

      if (!$album->photo_id) {
        $album->photo_id = $photo->getIdentity();
        $album->save();
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      return;
    }
  }

  public function browseAction() {
   
    //CHECK VIEW PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'view')->isValid())
      return;

    //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }
  }

  //ACTION FOR CONSTRUCT TAG CLOUD
  public function tagsCloudAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

    //GENERATE TAG-CLOULD HIDDEN FROM
    $this->view->form = $form = new Sitepage_Form_Searchtagcloud();

    //CONSTRUCTING TAG CLOUD
    $tag_array = array();
    $tag_cloud_array = Engine_Api::_()->getDbtable('notes', 'sitepagenote')->getTagCloud('', 0);
    $tag_id_array = array();
    foreach ($tag_cloud_array as $vales) {
      $tag_array[$vales['text']] = $vales['Frequency'];
      $tag_id_array[$vales['text']] = $vales['tag_id'];
    }

    if (!empty($tag_array)) {
      $max_font_size = 18;
      $min_font_size = 12;
      $max_frequency = max(array_values($tag_array));
      $min_frequency = min(array_values($tag_array));
      $spread = $max_frequency - $min_frequency;

      if ($spread == 0) {
        $spread = 1;
      }

      $step = ($max_font_size - $min_font_size) / ($spread);

      $tag_data = array('min_font_size' => $min_font_size, 'max_font_size' => $max_font_size, 'max_frequency' => $max_frequency, 'min_frequency' => $min_frequency, 'step' => $step);

      $this->view->tag_data = $tag_data;
      $this->view->tag_id_array = $tag_id_array;
    }
    $this->view->tag_array = $tag_array;
  }

   //ACTION FOR MAKE THE SITEPAGENOTE FEATURED/UNFEATURED
  public function featuredAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET NOTE ID AND OBJECT
    $note_id = $this->view->note_id = $this->_getParam('note_id');
    $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $note_id);

    $this->view->featured = $sitepagenote->featured;

    //GET PAGE OBJECT
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagenote->page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    $this->view->canEdit = 0;
    if (!empty($isManageAdmin)) {
      $this->view->canEdit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //SMOOTHBOX
    if (null === $this->_helper->ajaxContext->getCurrentContext()) {
      $this->_helper->layout->setLayout('default-simple');
    } else {//NO LAYOUT
      $this->_helper->layout->disableLayout(true);
    }

    if (!$this->getRequest()->isPost())
      return;

    //GET VIEWER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $tab_selected_id = $this->_getParam('tab');

    //CHECK THAT FEATURED ACTION IS ALLOWED BY ADMIN OR NOT
    //CHECK CAN MAKE FEATURED OR NOT(ONLY SITEPAGE NOTE CAN MAKE FEATURED/UN-FEATURED)
    if ($viewer_id == $sitepagenote->owner_id || !empty($this->view->canEdit)) {
      $this->view->permission = true;
      $this->view->success = false;
      $db = Engine_Api::_()->getDbtable('notes', 'sitepagenote')->getAdapter();
      $db->beginTransaction();
      try {
        if ($sitepagenote->featured == 0) {
          $sitepagenote->featured = 1;
        } else {
          $sitepagenote->featured = 0;
        }

        $sitepagenote->save();
        $db->commit();
        $this->view->success = true;
      } catch (Exception $e) {
        $db->rollback();
        throw $e;
      }
    } else {
      $this->view->permission = false;
    }

    if ($sitepagenote->featured) {
      $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Note successfully made featured.'));
    } else {
      $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Note successfully made un-featured.'));
    }

    $this->_forwardCustom('success', 'utility', 'core', array(
        'smoothboxClose' => 2,
        'parentRedirect' => $this->_helper->url->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($sitepagenote->page_id), 'tab' => $tab_selected_id), 'sitepage_entry_view', true),
        'parentRedirectTime' => '2',
        'format' => 'smoothbox',
        'messages' => $suc_msg
    ));
  }

  //ACTION FOR ADDING NOTE OF THE DAY
  public function addNoteOfDayAction() {
    //FORM GENERATION
    $form = $this->view->form = new Sitepagenote_Form_ItemOfDayday();
    $note_id = $this->_getParam('note_id');
   // $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();

      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //GET ITEM OF THE DAY TABLE
        $dayItemTime = Engine_Api::_()->getDbtable('itemofthedays', 'sitepage');

				//FETCH RESULT FOR resource_id
        $select = $dayItemTime->select()->where('resource_id = ?', $note_id)->where('resource_type = ?', 'sitepagenote_note');
        $row = $dayItemTime->fetchRow($select);

        if (empty($row)) {
          $row = $dayItemTime->createRow();
          $row->resource_id = $note_id;
        }
        $row->start_date = $values["starttime"];
        $row->end_date = $values["endtime"];
				$row->resource_type = 'sitepagenote_note';
        $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Note of the Day has been added successfully.'))
              ));
    }
  }

  // ACTION FOR FEATURED NOTES CAROUSEL AFTER CLICK ON BUTTON 
  public function featuredNotesCarouselAction() {
    //RETRIVE THE VALUE OF ITEM VISIBLE
    $this->view->itemsVisible = $limit = (int) $_GET['itemsVisible'];

    //RETRIVE THE VALUE OF NUMBER OF ROW
    $this->view->noOfRow = (int) $_GET['noOfRow'];
    //RETRIVE THE VALUE OF ITEM VISIBLE IN ONE ROW
    $this->view->inOneRow = (int) $_GET['inOneRow'];

    // Total Count Featured Photos
    $totalCount = (int) $_GET['totalItem'];

    //RETRIVE THE VALUE OF START INDEX
    $startindex = $_GET['startindex'] * $limit;

    if ($startindex > $totalCount) {
      $startindex = $totalCount - $limit;
    }
    if ($startindex < 0)
      $startindex = 0;

    $params = array();
    $params['category_id'] = $_GET['category_id'];
    $params['zero_count'] = 'featured';

    //RETRIVE THE VALUE OF BUTTON DIRECTION
    $direction = $_GET['direction'];
    $this->view->offset = $params['start_index'] = $startindex;

    //GET Featured Photos with limit * 2
    $this->view->totalItemsInSlide = $params['limit'] = $limit * 2;
    $this->view->featuredNotes = $this->view->featuredNotes = $featuredNotes = Engine_Api::_()->getDbTable('notes', 'sitepagenote')->widgetNotesData($params);

    //Pass the total number of result in tpl file
    $this->view->count = count($featuredNotes);

    //Pass the direction of button in tpl file
    $this->view->direction = $direction;
  }

   public function homeAction() {
 
    //CHECK VIEW PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'view')->isValid())
      return;

     //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else { 
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }

  }
}

?>