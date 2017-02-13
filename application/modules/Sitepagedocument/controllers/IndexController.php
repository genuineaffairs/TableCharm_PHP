<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
include_once APPLICATION_PATH . '/application/modules/Sitepagedocument/Api/Scribdsitepage.php';

class Sitepagedocument_IndexController extends Seaocore_Controller_Action_Standard {

  public function init() {
    //SET SCRIBD API AND SCECRET KEY
    $this->scribd_api_key = Engine_Api::_()->getApi('settings', 'core')->sitepagedocument_api_key;
    $this->scribd_secret = Engine_Api::_()->getApi('settings', 'core')->sitepagedocument_secret_key;
    $this->scribdsitepage = new Scribdsitepage($this->scribd_api_key, $this->scribd_secret);
  }

  //ACTION FOR CREATE THE NEW DOCUMENT
  public function createAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    $page_id = $this->_getParam('page_id');
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    $this->view->tab_selected_id = $this->_getParam('tab');

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagedocument")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdcreate');
      if (empty($isPageOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = $can_edit = 0;
    } else {
      $this->view->can_edit = $can_edit = 1;
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdcreate');
    if (empty($isManageAdmin) && empty($can_edit)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $sitepageModHostName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));

    $sitepagedocument_isCreate = Zend_Registry::isRegistered('sitepagedocument_isCreate') ? Zend_Registry::get('sitepagedocument_isCreate') : null;
    if (empty($sitepagedocument_isCreate)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

    //SHOW DOCUMENT CREATE FORM
    $this->view->form = $form = new Sitepagedocument_Form_Create();
    
    // Populate with categories
    $categories = Engine_Api::_()->getDbtable('categories', 'sitepagedocument')->getCategoriesAssoc();
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

    //CHECK THAT CREATOR ALREADY HAVE DOCUMENTS AT SCRIBD SITE
    try {
      $result = $this->scribdsitepage->getList();
    } catch (Exception $e) {
      $code = $e->getCode();
      if ($code == 401) {
        $message = $e->getMessage();

        $error = $message . $this->view->translate(': API key is not correct');
        $error = Zend_Registry::get('Zend_Translate')->_($error);

        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }
    }

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $translate = Zend_Registry::get('Zend_Translate');

      //CONNET WITH DOCUMENT TABLE
      $sitepagedocumentTable = Engine_Api::_()->getItemTable('sitepagedocument_document');
      $db = $sitepagedocumentTable->getAdapter();
      $db->beginTransaction();

      //GET POSTED VALUES FROM CREATE FORM
      $values = $form->getValues();

      $isModType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.set.type', 0);
      if (empty($isModType)) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepagedocument.api.info', convert_uuencode($sitepageModHostName));
      }

      //SET SCRIBD MY_USER_ID
      $this->scribdsitepage->my_user_id = $viewer_id;

      //DOCUMENT CREATION CODE
      try {
        $values = array_merge($form->getValues(), array(
            'owner_id' => $viewer_id,
                ));
        $this->view->is_error = 0;
        $this->view->excep_error = 0;

        //MAKE SURE THAT FILE SIZE SHOULD NOT EXCEED FROM ALLOWED LIMIT DEPEND ON USER LEVEL
        $filesize = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.filesize', 2048);
        $filesize = $filesize * 1024;
        if ($filesize < 0) {
          $filesize = (int) ini_get('upload_max_filesize') * 1024 * 1024;
        }
        if ($_FILES['filename']['size'] > $filesize) {
          $error = $this->view->translate('File size can not be exceed from ') . ($filesize / 1024) . $this->view->translate(' KB');
          $error = Zend_Registry::get('Zend_Translate')->_($error);

          $form->getDecorator('errors')->setOption('escape', false);
          $form->addError($error);
          return;
        }

        //MAKE SURE THAT FILE EXTENSION SHOULD NOT DIFFER FROM ALLOWED TYPE
        $ext = str_replace(".", "", strrchr($_FILES['filename']['name'], "."));
        if (!in_array($ext, array('pdf', 'txt', 'ps', 'rtf', 'epub', 'odt', 'odp', 'ods', 'odg', 'odf', 'sxw', 'sxc', 'sxi', 'sxd', 'doc', 'ppt', 'pps', 'xls', 'docx', 'pptx', 'ppsx', 'xlsx', 'tif', 'tiff'))) {
          $error = $this->view->translate("Invalid file extension. Allowed extensions are :'pdf', 'txt', 'ps', 'rtf', 'epub', 'odt', 'odp', 'ods', 'odg', 'odf', 'sxw', 'sxc', 'sxi', 'sxd', 'doc', 'ppt', 'pps', 'xls', 'docx', 'pptx', 'ppsx', 'xlsx', 'tif', 'tiff'");
          $error = Zend_Registry::get('Zend_Translate')->_($error);

          $form->getDecorator('errors')->setOption('escape', false);
          $form->addError($error);
          return;
        }

        //CHECKS FOR SCRIBD LICENSE AND LICENSE OPTION
        $licensing_option = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.licensing.option', 1);
        $licensing_scribd = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.licensing.scribd', 'ns');

        if ($licensing_option == 0) {
          $license_sitepagedocument = $licensing_scribd;
					$values['sitepagedocument_license'] = $licensing_scribd;
        } else {
          $license_sitepagedocument = $values['sitepagedocument_license'];
        }

        if ($license_sitepagedocument == 'ns') {
          $scribd_license = null;
        } else {
          $scribd_license = $license_sitepagedocument;
        }

        $sitepagedocument_download = $values['download_allow'];

        $download_allow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.download.allow', 1);
        if ($download_allow == 0) {
          $download = "view-only";
        } else {
          $download = "download-pdf";
        }

        //CREATE THE MAIN DOCUMENT
        $sitepagedocumentRow = $sitepagedocumentTable->createRow();
        $sitepagedocumentRow->setFromArray($values);
        $sitepagedocumentRow->owner_id = $viewer_id;
        $sitepagedocumentRow->page_id = $page_id;
        $sitepagedocumentRow->views = 1;
        $sitepagedocumentRow->save();

        //SECURE IPAPER CHECK
        $secure_allow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.secure.allow', 0);
        $secure_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.secure.show', 1);
        if (empty($secure_allow)) {
          $sitepagedocumentRow->secure_allow = 0;
        } elseif (empty($secure_show)) {
          $sitepagedocumentRow->secure_allow = 1;
        } else {
          $sitepagedocumentRow->secure_allow = $values['secure_allow'];
        }

        //DOWNLOAD ALLOW CHECK
        $download_allow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.download.allow', 1);
        $download_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.download.show', 1);
        if (empty($download_allow)) {
          $sitepagedocumentRow->download_allow = 0;
        } elseif (empty($download_show)) {
          $sitepagedocumentRow->download_allow = 1;
        } else {
          $sitepagedocumentRow->download_allow = $values['download_allow'];
        }

        $sitepagedocument_download = $sitepagedocumentRow->download_allow;

        $rev_id = NULL;

        //CHECKS FOR DEFAULT VISIBILITY
        $sitepagedocument_default_visibility = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.default.visibility', 'private');
        $sitepagedocument_visibility_option = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.visibility.option', 1);
        if ($sitepagedocument_default_visibility == 'private') {
          $access = 'private';
        } elseif ($sitepagedocument_visibility_option == 1) {
          $access = $values['default_visibility'];
        } else {
          $access = 'public';
        }
        $sitepagedocumentRow->sitepagedocument_private = $access;

        //IF FILENAME IS NOT EMPTY THAN BEGIN THE SCRIBD WORK
        if (!empty($values['filename'])) {
          $local_info = $sitepagedocumentRow->setFile($form->filename);
          $secure_allow = $sitepagedocumentRow->secure_allow;
          $data = $this->scribdUpload($local_info, $rev_id, $access, $secure_allow, $download, $sitepagedocumentRow->filename_id);
        }
        $doc_title = $values['sitepagedocument_title'];
        $description = $values['sitepagedocument_description'];
        try {
          $changesetting = $this->scribdsitepage->changeSettings($data['doc_id'], $doc_title, $description, $access, $scribd_license, $sitepagedocument_download);

          $setting = $this->scribdsitepage->getSettings($data['doc_id']);
        } catch (Exception $e) {
          $message = $e->getMessage();
          $this->view->excep_error = 1;
          $this->view->excep_message = $message;
        }

        //EMAIL ALLOW CHECK
        $email_allow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.email.allow', 1);
        $email_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.email.show', 1);
        if (empty($email_allow)) {
          $sitepagedocumentRow->email_allow = 0;
        } elseif (empty($email_show)) {
          $sitepagedocumentRow->email_allow = 1;
        } else {
          $sitepagedocumentRow->email_allow = $values['email_allow'];
        }

        $sitepagedocumentRow->approved = 1;
        $sitepagedocumentRow->featured = 0;
        $sitepagedocumentRow->doc_id = $data['doc_id'];
        $sitepagedocumentRow->access_key = $data['access_key'];
        $sitepagedocumentRow->secret_password = $data['secret_password'];
        $sitepagedocumentRow->filemime = $_FILES['filename']['type'];
        $sitepagedocumentRow->filesize = $_FILES['filename']['size'];
        $sitepagedocumentRow->save();

        //COMMENT PRIVACY
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        $commentMax = array_search("everyone", $roles);
        foreach ($roles as $i => $role) {
          $auth->setAllowed($sitepagedocumentRow, $role, 'comment', ($i <= $commentMax));
        }

        //SAVE CUSTOM FIELDS VALUES
        $customfieldform = $form->getSubForm('fields');
        $customfieldform->setItem($sitepagedocumentRow);
        $customfieldform->saveValues();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      //GO TO PAGE PROFILE PAGE WITH PAGE DOCUMENT SELECTED TAB
      return $this->_redirectCustom($sitepagedocumentRow->getHref(), array('prependBase' => false));
    }
  }

   //ACTION FOR RETURN THE SCRIBD THUMBNAIL IMAGE
   public function sslAction() {
 
 		$url = urldecode($_GET['url']);
 		header("Cache-Control: no-cache");
 		header("Content-type:image/jpeg");
 		$image2 = imagecreatefromjpeg($url);
 		$width = imagesx($image2);
 		$height = imagesy($image2);
 		$imgh = 84;
 		$imgw = $width / $height * $imgh;
 		$thumb=imagecreatetruecolor($imgw,$imgh);
 		imagecopyresampled($thumb,$image2,0,0,0,0,$imgw,$imgh,$width,$height);
 		imagejpeg($thumb);
 		die;
 	}

  //ACTION FOR EDIT THE DOCUMENT
  public function editAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET DOCUMENT INFO
    $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', $this->_getParam('document_id'));

    //GET USER LEVEL
    $level_id = $viewer->level_id;

    //PAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagedocument->page_id);

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagedocument")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdcreate');
      if (empty($isPageOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //SUPERADMIN, DOCUMENT OWNER AND SITEPAGE OWNER CAN EDIT DOCUMENT
    if ($viewer_id != $sitepagedocument->owner_id && $can_edit != 1) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

    //GENERATE EDIT FORM
    $this->view->form = $form = new Sitepagedocument_Form_Edit(array('item' => $sitepagedocument));

    //SEND TAB AND PAGE ID TO TPL FILE
    $page_id = $this->_getParam('page_id');
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    $this->view->tab_selected_id = $this->_getParam('tab');

    //IF DOCUMENT IN PUBLISHED STAT THAN REMOVE ELEMENT
    if ($sitepagedocument->draft == "0") {
      $form->removeElement('draft');
    }

    //SET SCRIBD MY_USER_ID
    $this->scribdsitepage->my_user_id = $sitepagedocument->owner_id;

    //SAVE DOCUMENT DETAIL
    $saved = $this->_getParam('saved');
    if (!$this->getRequest()->isPost() || $saved) {
      if ($saved) {
        $url = $this->_helper->url->url(array('user_id' => $sitepagedocument->owner_id, 'document_id' => $sitepagedocument->getIdentity()), 'sitepagedocument_detail_view');
        $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes were saved. Click <a href=\'%1$s\'>here</a> to view your document.', $url));
      }

      //SHOW PREFIELD
      $form->populate($sitepagedocument->toArray());

      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $values = $form->getValues();
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      $sitepagedocument->setFromArray($values);
      $sitepagedocument->modified_date = new Zend_Db_Expr('NOW()');
      $doc_title = $values['sitepagedocument_title'];
      $description = $values['sitepagedocument_description'];

      $sitepagedocument_download = $values['download_allow'];
      $download = "view-only";

      $license_sitepagedocument = $values['sitepagedocument_license'];

      if ($license_sitepagedocument == 'ns') {
        $scribd_license = null;
      } else {
        $scribd_license = $license_sitepagedocument;
      }

      //CHECKS FOR DEFAULT VISIBILITY
      $sitepagedocument_default_visibility = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.default.visibility', 'private');
      $sitepagedocument_visibility_option = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.visibility.option', 1);
      if ($sitepagedocument_default_visibility == 'private') {
        $access = 'private';
      } elseif ($sitepagedocument_visibility_option == 1) {
        $access = $values['default_visibility'];
      } else {
        $access = 'public';
      }
      $sitepagedocument->sitepagedocument_private = $access;

      //MAKE SURE THAT FILE SIZE SHOULD NOT EXCEED FROM ALLOWED LIMIT DEPEND ON USER LEVEL
      if (!empty($_FILES['filename']['name'])) {
        $filesize = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.filesize', 2048);
        $filesize = $filesize * 1024;
        if ($filesize < 0) {
          $filesize = (int) ini_get('upload_max_filesize') * 1024 * 1024;
        }
        if ($_FILES['filename']['size'] > $filesize) {
          $error = $this->view->translate('Filesize can not be exceed from ') . $filesize . $this->view->translate(' KB for this user level');
          $error = Zend_Registry::get('Zend_Translate')->_($error);

          $form->getDecorator('errors')->setOption('escape', false);
          $form->addError($error);
          return;
        }

        //MAKE SURE THAT FILE EXTENSION SHOULD NOT DIFFER FROM ALLOWED TYPE
        $ext = str_replace(".", "", strrchr($_FILES['filename']['name'], "."));
        if (!in_array($ext, array('pdf', 'txt', 'ps', 'rtf', 'epub', 'odt', 'odp', 'ods', 'odg', 'odf', 'sxw', 'sxc', 'sxi', 'sxd', 'doc', 'ppt', 'pps', 'xls', 'docx', 'pptx', 'ppsx', 'xlsx', 'tif', 'tiff'))) {
          $error = $this->view->translate('Invalid file extension!');
          $error = Zend_Registry::get('Zend_Translate')->_($error);

          $form->getDecorator('errors')->setOption('escape', false);
          $form->addError($error);
          return;
        }

        $rev_id = $sitepagedocument->doc_id;

        //IF FILENAME IS NOT EMPTY THAN BEGIN THE SCRIBD WORK
        if (!empty($values['filename'])) {

          $filename_id_prev = $sitepagedocument->filename_id;

          $local_info = $sitepagedocument->setFile($form->filename);
          $secure_allow = $sitepagedocument->secure_allow;
          $data = $this->scribdUpload($local_info, $rev_id, $access, $secure_allow, $download, $sitepagedocument->filename_id);

          //FIRST DELETE FILE ENTRY FROM engine4_storage_files
          Engine_Api::_()->getDbtable('files', 'storage')->delete(array(
              'file_id = ?' => $filename_id_prev,
          ));
        }
        try {
          $stat = $this->scribdsitepage->getConversionStatus($data['doc_id']);
        } catch (Exception $e) {
          $message = $e->getMessage();
          $this->view->excep_error = 1;
          $this->view->excep_message = $message;
        }
        try {
          $changesetting = $this->scribdsitepage->changeSettings($data['doc_id'], $doc_title, $description, $access, $scribd_license, $sitepagedocument_download);
          $setting = $this->scribdsitepage->getSettings($data['doc_id']);
        } catch (Exception $e) {
          $message = $e->getMessage();
          $this->view->excep_error = 1;
          $this->view->excep_message = $message;
        }
        $sitepagedocument->status = 0;
        $sitepagedocument->doc_id = $data['doc_id'];
        $sitepagedocument->access_key = $data['access_key'];
        $sitepagedocument->secret_password = $data['secret_password'];
        $sitepagedocument->filemime = $_FILES['filename']['type'];
        $sitepagedocument->filesize = $_FILES['filename']['size'];
        $sitepagedocument->thumbnail = NULL;
        //FILE UPLOADING WORK END HERE
      }
      $sitepagedocument->save();

      $customfieldform = $form->getSubForm('fields');
      $customfieldform->setItem($sitepagedocument);
      $customfieldform->saveValues();

      $draft_values = 0;
      if (isset($values['draft'])) {
        $draft_values = $values['draft'];
      }

      //INSERT NEW ACTIVITY IF DOCUMENT IS JUST GETTING PUBLISHED
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionsByObject($sitepagedocument);
      if (count($action->toArray()) <= 0 && $draft_values == 0 && $sitepagedocument->approved == 1 && $sitepagedocument->status == 1 && $sitepagedocument->status == 1 && $sitepagedocument->activity_feed == 0) {
        $creator = Engine_Api::_()->getItem('user', $sitepagedocument->owner_id);
        $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
        $activityFeedType = null;
        if (Engine_Api::_()->sitepage()->isPageOwner($sitepage,$creator) && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
          $activityFeedType = 'sitepagedocument_admin_new';
        elseif ($sitepage->all_post || Engine_Api::_()->sitepage()->isPageOwner($sitepage,$creator))
          $activityFeedType = 'sitepagedocument_new';

        if ($activityFeedType) {
          $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($creator, $sitepage, $activityFeedType);
          Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
        }

        //MAKE SURE ACTION EXISTS BEFOR ATTACHING THE DOCUMENT TO THE ACTIVITY
        if ($action != null) {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $sitepagedocument);
          $sitepagedocument->activity_feed = 1;
          $sitepagedocument->save();
        }
      }

      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actionTable->getActionsByObject($sitepagedocument) as $action) {
        $actionTable->resetActivityBindings($action);
      }
      $db->commit();

      return $this->_redirectCustom($sitepagedocument->getHref(), array('prependBase' => false));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  //ACTION FOR VIEW THE DOCUMENT
  public function viewAction() {

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET DOCUMENT ITEM
    $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', $this->getRequest()->getParam('document_id'));

		if ($sitepagedocument) {
			Engine_Api::_()->core()->setSubject($sitepagedocument);
		}

    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagedocument->page_id);

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagedocument")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdcreate');
      if (empty($isPageOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdcreate');
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
    //CHECKING THE USER HAVE THE PERMISSION TO VIEW THE DOCUMENT OR NOT
    if (($can_edit != 1 && $viewer_id != $sitepagedocument->owner_id) && ($sitepagedocument->draft == 1 || $sitepagedocument->status != 1 || $sitepagedocument->approved != 1 || $sitepagedocument->search != 1)) {
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

  //ACTION FOR EMAIL THE DOCUMENT
  public function emailAction() {

    //GET VIEWER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //TO BE VERIFY THAT DOCUMNETS HAD ENABLED FOR THIS USER
    $document_id = $this->_getParam('id');
    if (empty($document_id)) {
      return $this->_redirect("sitepagedocuments/manage");
    } else {
      $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', $document_id);
    }

    $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagedocument->page_id);

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagedocument")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdcreate');
      if (empty($isPageOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //DO NOT SHOW DOCUMENT IF IT IS NOT PUBLISHED TO OTHER USER
    if ($viewer_id != $sitepagedocument->owner_id && $sitepagedocument->draft == 1) {
      $page = "error";
      $this->view->error_header = 639;
      $this->view->error_message = $this->view->translate('You do not have permission to view this document.');
      $this->error_submit = 641;
    }

    //DO NOT SHOW DOCUMENT IF IT IS NOT ACTIVE
    if ($viewer_id != $sitepagedocument->owner_id && $sitepagedocument->approved != 1) {
      $page = "error";
      $this->view->error_header = 639;
      $this->view->error_message = $this->view->translate('You do not have permission to view this document.');
      $this->error_submit = 641;
    }

    //CHECK THAT EMAIL IS ALLOW OR NOT
    $email_allow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.email.allow', 1);
    if (($sitepagedocument->email_allow != 1) || ($sitepagedocument->status != 1) || ($email_allow != 1 )) {
      $page = "error";
      $this->view->error_header = 639;
      $this->view->error_message = $this->view->translate('You do not have permission to view this document.');
      $this->error_submit = 641;
    }
    $time_out = 50000;
    $is_error = 0;
    $error_array = array();
    $excep_error = 0;
    $excep_message = '';

    //FILENAME FROM STORAGE TABLE
    $file_name = $sitepagedocument->getDocumentFileName();

    if (!empty($file_name)) {
      $this->view->attach = $file_name;
    } else {
      $this->view->attach = $sitepagedocument->storage_path;
    }

    $this->view->smoothbox_error = 0;
    if (isset($_POST['submit']) && $_POST['submit'] == 'Send') {
      $to = $_POST['to'];
      $subjectEmail = $_POST['subject'];
      $user_message = $_POST['message'];

      if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        $is_error = 1;
        $this->view->smoothbox_error = 1;
        $error = $this->view->translate('Please enter a valid email.');
        $error_array[] = $error;
      }

      if (empty($subjectEmail)) {
        $is_error = 1;
        $this->view->smoothbox_error = 1;
        $error = $this->view->translate('Please enter the subject.');
        $error_array[] = $error;
      }
      if ($is_error != 1) {

        $from = $viewer->displayname . '<' . $viewer->email . '>';

        $fileatt_type = $sitepagedocument->filemime;
        $fileatt_name = $file_name;
        $headers = "From: $from";

        //SET SCRIBD USER ID
        $this->scribdsitepage->my_user_id = $sitepagedocument->owner_id;

        try {
          $link = $this->scribdsitepage->getDownloadUrl($sitepagedocument->doc_id, 'original');
        } catch (Exception $e) {
          $message = $e->getMessage();
          $this->view->excep_error = 1;
          $this->view->excep_message = $message;
        }

        $link = trim($link['download_link']);
        $data = file_get_contents($link);

        if (empty($data)) {
          $ch = curl_init();
          $timeout = 0;
          curl_setopt($ch, CURLOPT_URL, $link);
          curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
          ob_start();
          curl_exec($ch);
          curl_close($ch);
          $data = ob_get_contents();
          ob_end_clean();
        }

        $semi_rand = md5(time());
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

        $headers .= "\nMIME-Version: 1.0\n" .
                "Content-Type: multipart/mixed;\n" .
                " boundary=\"{$mime_boundary}\"";

        $email_message = "This is a multi-part message in MIME format.\n\n" .
                "--{$mime_boundary}\n" .
                "Content-Type:text/html; charset=\"iso-8859-1\"\n" .
                "Content-Transfer-Encoding: 7bit\n\n" . $user_message . "\n\n";

        $data = chunk_split(base64_encode($data));

        $email_message .= "--{$mime_boundary}\n" .
                "Content-Type: {$fileatt_type};\n" .
                " name=\"{$fileatt_name}\"\n" .
                "Content-Transfer-Encoding: base64\n\n" .
                $data . "\n\n" .
                "--{$mime_boundary}--\n";

        $mail_sent = mail($to, $subjectEmail, $email_message, $headers);
        if ($mail_sent) {
          $this->view->msg = $this->view->translate('Email has been sent successfully.');
          $time_out = 7000;
          $this->view->no_form = 1;
        } else {
          $is_error = 1;
          $error_array[] = $this->view->translate('There was an error in sending your email. Please try again later.');
          $time_out = 50000;
          $this->view->no_form = 1;
        }
      }
      $this->view->to = $to;
      $this->view->subject = $subjectEmail;
      $this->view->user_message = $user_message;
    }
    $this->view->is_error = $is_error;
    $this->view->time_out = $time_out;
    $this->view->error_array = array_unique($error_array);
    $this->view->excep_error = $excep_error;
    $this->view->excep_message = $excep_message;
  }

  //ACTION FOR RATING DOCUMENTS
  public function ratingAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET VIEWER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET RATING AND DOCUMENT ID
    $rating = $this->_getParam('rating');
    $document_id = $this->_getParam('document_id');

    //GET RATTING DATA
    $db = Engine_Api::_()->getDbtable('ratings', 'sitepagedocument')->getAdapter();
    $db->beginTransaction();

    try {
      Engine_Api::_()->getDbTable('ratings', 'sitepagedocument')->doSitepagedocumentRating($document_id, $viewer_id, $rating);

      $total = Engine_Api::_()->getDbTable('ratings', 'sitepagedocument')->countRating($document_id);

      $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', $document_id);

      //GET AVERAGE RATING
      $rating = Engine_Api::_()->getDbTable('ratings', 'sitepagedocument')->avgRating($document_id);

      $sitepagedocument->rating = $rating;
      $sitepagedocument->save();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $data = array();
    $data[] = array(
        'total' => $total,
        'rating' => $rating,
    );
    return $this->_helper->json($data);
    $data = Zend_Json::encode($data);
    $this->getResponse()->setBody($data);
  }

  //ACTION FOR DOCUMENT PUBLISH
  public function publishAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //SMOOTHBOX
    if (null === $this->_helper->ajaxContext->getCurrentContext()) {
      $this->_helper->layout->setLayout('default-simple');
    } else {//NO LAYOUT
      $this->_helper->layout->disableLayout(true);
    }

    //GET DOCUMENT ID
    $document_id = $this->view->document_id = $this->_getParam('document_id');

    if (!$this->getRequest()->isPost())
      return;

    $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', $document_id);

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagedocument->page_id);

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagedocument")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdcreate');
      if (empty($isPageOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //WHO CAN PUBLISH THIS DOCUMENT
    if ($viewer_id != $sitepagedocument->owner_id && $can_edit != 1) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    if ($viewer_id == $sitepagedocument->owner_id || $can_edit == 1) {
      $this->view->permission = true;
      $this->view->success = false;
      $db = Engine_Api::_()->getDbtable('documents', 'sitepagedocument')->getAdapter();
      $db->beginTransaction();
      try {
        $sitepagedocument->modified_date = new Zend_Db_Expr('NOW()');
        $sitepagedocument->draft = 0;
        $sitepagedocument->save();
        $db->commit();
        $this->view->success = true;
      } catch (Exception $e) {
        $db->rollback();
        throw $e;
      }

      //ADD ACTIVITY ONLY IF DOCUMENT IS PUBLISHED
      if ($sitepagedocument->draft == 0 && $sitepagedocument->approved == 1 && $sitepagedocument->status == 1 && $sitepagedocument->activity_feed == 0 && $sitepagedocument->search == 1) {
        $api = Engine_Api::_()->getDbtable('actions', 'activity');
        $creator = Engine_Api::_()->getItem('user', $sitepagedocument->owner_id);
        $activityFeedType = null;
        if (Engine_Api::_()->sitepage()->isPageOwner($sitepage,$creator) && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
          $activityFeedType = 'sitepagedocument_admin_new';
        elseif ($sitepage->all_post || Engine_Api::_()->sitepage()->isPageOwner($sitepage ,$creator))
          $activityFeedType = 'sitepagedocument_new';

        if ($activityFeedType) {
          $action = $api->addActivity($creator, $sitepage, $activityFeedType);
          Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
        }
        //MAKE SURE ACTION EXISTS BEFORE ATTACHING THE DOUCMENT TO THE ACTIVITY
        if ($action != null) {
          $api->attachActivity($action, $sitepagedocument);
          $sitepagedocument->activity_feed = 1;
          $sitepagedocument->save();
        }
      }
    } else {
      $this->view->permission = false;
    }

    $this->_forwardCustom('success', 'utility', 'core', array(
        'smoothboxClose' => 2,
        'parentRedirect' => $this->_helper->url->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($sitepagedocument->page_id), 'tab' => $this->_getParam('tab')), 'sitepage_entry_view'),
        'parentRedirectTime' => '2',
        'format' => 'smoothbox',
        'messages' => array('')
    ));
  }

  //ACTION FOR MAKE THE PAGE-DOCUMENT FEATURED/UNFEATURED
  public function featuredAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET DOCUMENT ID
    $document_id = $this->view->document_id = $this->_getParam('document_id');
    $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', $document_id);
    $this->view->featured = $sitepagedocument->featured;

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

    //CHECK THAT FEATURED ACTION IS ALLOWED BY ADMIN OR NOT
    $this->view->canMakeFeatured = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.featured', 1);

    //PAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagedocument->page_id);

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagedocument")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdcreate');
      if (empty($isPageOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //CHECK CAN MAKE FEATURED OR NOT
    if ($can_edit == 1 && $this->view->canMakeFeatured == 1) {
      $this->view->permission = true;
      $this->view->success = false;
      $db = Engine_Api::_()->getDbtable('documents', 'sitepagedocument')->getAdapter();
      $db->beginTransaction();
      try {
        if ($sitepagedocument->featured == 0) {
          $sitepagedocument->featured = 1;
        } else {
          $sitepagedocument->featured = 0;
        }

        $sitepagedocument->save();
        $db->commit();
        $this->view->success = true;
      } catch (Exception $e) {
        $db->rollback();
        throw $e;
      }
    } else {
      $this->view->permission = false;
    }

    if ($sitepagedocument->featured) {
      $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Document successfully made featured.'));
    } else {
      $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Document successfully made un-featured.'));
    }

    $this->_forwardCustom('success', 'utility', 'core', array(
        'smoothboxClose' => 2,
        'parentRedirect' => $this->_helper->url->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($sitepagedocument->page_id), 'tab' => $this->_getParam('tab')), 'sitepage_entry_view'),
        'parentRedirectTime' => '2',
        'format' => 'smoothbox',
        'messages' => $suc_msg
    ));
  }

  //ACTION FOR MAKE THE PAGE-DOCUMENT FEATURED/UNFEATURED
  public function highlightedAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET DOCUMENT ID
    $document_id = $this->view->document_id = $this->_getParam('document_id');
    $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', $document_id);
    $this->view->highlighted = $sitepagedocument->highlighted;

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

    //CHECK THAT FEATURED ACTION IS ALLOWED BY ADMIN OR NOT
    $this->view->canMakeFeatured = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.featured', 1);

    //PAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagedocument->page_id);

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagedocument")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdcreate');
      if (empty($isPageOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //CHECK CAN MAKE FEATURED OR NOT
    if ($can_edit == 1) {
      $this->view->permission = true;
      $this->view->success = false;
      $db = Engine_Api::_()->getDbtable('documents', 'sitepagedocument')->getAdapter();
      $db->beginTransaction();
      try {
        if ($sitepagedocument->highlighted == 0) {
          $sitepagedocument->highlighted = 1;
        } else {
          $sitepagedocument->highlighted = 0;
        }

        $sitepagedocument->save();
        $db->commit();
        $this->view->success = true;
      } catch (Exception $e) {
        $db->rollback();
        throw $e;
      }
    } else {
      $this->view->permission = false;
    }

    if ($sitepagedocument->featured) {
      $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Document successfully made featured.'));
    } else {
      $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Document successfully made un-featured.'));
    }

    $this->_forwardCustom('success', 'utility', 'core', array(
        'smoothboxClose' => 2,
        'parentRedirect' => $this->_helper->url->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($sitepagedocument->page_id), 'tab' => $this->_getParam('tab')), 'sitepage_entry_view'),
        'parentRedirectTime' => '2',
        'format' => 'smoothbox',
        'messages' => $suc_msg
    ));
  }

  //ACTION FOR DELETE DOCUMENT
  public function deleteAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET LOGGED IN INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    $this->view->page_id = $page_id = $this->_getParam('page_id');

    //GET PAGE ITEM
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    $this->view->tab_selected_id = $tab_selected_id = $this->_getParam('tab');

    //GET DOCUMETN MODEL
    $this->view->sitepagedocument = $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', $this->_getParam('document_id'));

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagedocument")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdcreate');
      if (empty($isPageOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //DOCUMENT OWNER AND sitepage OWNER CAN DELETE DOCUMENT
    if ($viewer_id != $sitepagedocument->owner_id && $can_edit != 1) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

    //DELETE DOCUMENT FROM DATATBASE AND SCRIBD AFTER CONFIRMATION
    if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {
      Engine_Api::_()->sitepagedocument()->deleteContent($sitepagedocument->document_id);

      return $this->_gotoRouteCustom(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id), 'tab' => $tab_selected_id), 'sitepage_entry_view', true);
    }
  }

  /**
   * Upload a sitepagedocument from a file
   * @param string $local_info : relative path to file
   * @param int $rev_id : id of file to modify
   * @param string $access : public or private. Default is Public.
   * @return array containing doc_id, access_key, and secret_password if nessesary.
   */
  public function scribdUpload($local_info, $rev_id, $access, $secure_allow, $download, $filename_id) {
    global $sitepagedocument_isDocUpload;
    try {
      $base = Zend_Controller_Front::getInstance()->getBaseUrl();

      $storagemodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('storage');
      $storageversion = $storagemodule->version;
			if (!empty($_SERVER["HTTPS"]) && 'on' == strtolower($_SERVER["HTTPS"])) {
				if ($storageversion < '4.1.1') {
					$doc_base_url = "https://" . $_SERVER['HTTP_HOST'] . $base . "/";
				} else {
					$doc_base_url = "https://" . $_SERVER['HTTP_HOST'];
				}
			}
			else {
				if ($storageversion < '4.1.1') {
					$doc_base_url = "http://" . $_SERVER['HTTP_HOST'] . $base . "/";
				} else {
					$doc_base_url = "http://" . $_SERVER['HTTP_HOST'];
				}
			}

      $doc_base_url = str_replace("index.php/", '', $doc_base_url);
      $scribd_upload_url = $doc_base_url . $local_info['file_path'];

      $db = Engine_Db_Table::getDefaultAdapter();
      $type_array = $db->query("SHOW COLUMNS FROM engine4_storage_servicetypes LIKE 'enabled'")->fetch();

      if ($storageversion >= '4.1.6' && !empty($type_array)) {
        $storageServiceTypeTable = Engine_Api::_()->getDbtable('serviceTypes', 'storage');
        $storageServiceTypeTableName = $storageServiceTypeTable->info('name');

        $storageServiceTable = Engine_Api::_()->getDbtable('services', 'storage');
        $storageServiceTableName = $storageServiceTable->info('name');

        $select = $storageServiceTypeTable->select()
                ->setIntegrityCheck(false)
                ->from($storageServiceTypeTableName, array(''))
                ->join($storageServiceTableName, "$storageServiceTypeTableName.servicetype_id = $storageServiceTableName.servicetype_id", array('enabled', 'default'))
                ->where("$storageServiceTypeTableName.plugin = ?", "Storage_Service_Local")
                ->where("$storageServiceTypeTableName.enabled = ?", 1)
                ->limit(1);
        $storageCheck = $storageServiceTypeTable->fetchRow($select);
        if (!empty($storageCheck)) {
          if ($storageCheck->enabled == 1 && $storageCheck->default == 1) {
            $scribd_upload_url = $doc_base_url . $local_info['file_path'];
          } else {
            $scribd_upload_url = $local_info['file_path'];
          }
        }
      }

			//GET STORAGE FILE OBJECT	
			$storage_file = Engine_Api::_()->getItem('storage_file', $filename_id);
			$scribd_upload_url = $storage_file->temporary();

      $scribd_upload_url = empty($sitepagedocument_isDocUpload) ? null : $scribd_upload_url;
      $data = $this->scribdsitepage->upload("$scribd_upload_url", NULL, $access, $rev_id, $download, $secure_allow);
      $data['local_path'] = $scribd_upload_url;
    } catch (Exception $e) {
      $message = $e->getMessage();
      $this->view->excep_error = 1;
      $this->view->excep_message = $message;
    }
    return $data;
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

  // ACTION FOR FEATURED DOCUMENTS CAROUSEL AFTER CLICK ON BUTTON 
  public function featuredDocumentsCarouselAction() {
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
    $this->view->featuredDocuments = $this->view->featuredDocuments = $featuredDocuments = Engine_Api::_()->getDbTable('documents', 'sitepagedocument')->widgetDocumentsData($params);

    //Pass the total number of result in tpl file
    $this->view->count = count($featuredDocuments);

    //Pass the direction of button in tpl file
    $this->view->direction = $direction;
  }

   //ACTION FOR ADDING DOCUMENT OF THE DAY
  public function addDocumentOfDayAction() {
    //FORM GENERATION
    $form = $this->view->form = new Sitepagedocument_Form_ItemOfDayday();
    $document_id = $this->_getParam('document_id');
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
        $select = $dayItemTime->select()->where('resource_id = ?', $document_id)->where('resource_type = ?', 'sitepagedocument_document');
        $row = $dayItemTime->fetchRow($select);

        if (empty($row)) {
          $row = $dayItemTime->createRow();
          $row->resource_id = $document_id;
        }
        $row->start_date = $values["starttime"];
        $row->end_date = $values["endtime"];
				$row->resource_type = 'sitepagedocument_document';
        $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Document of the Day has been added successfully.'))
              ));
    }
  }
  
	//ACTION FOR UPLOADING IMAGES THROUGH WYSIWYG EDITOR
  public function uploadPhotoAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->_helper->layout->disableLayout();

    if( !Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create') ) {
      return false;
    }

    if( !$this->_helper->requireAuth()->setAuthParams('album', null, 'create')->isValid() ) return;

    if( !$this->_helper->requireUser()->checkRequire() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }
     $fileName = Engine_Api::_()->seaocore()->tinymceEditorPhotoUploadedFileName();
    if( !isset($_FILES[$fileName]) || !is_uploaded_file($_FILES[$fileName]['tmp_name']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    $db = Engine_Api::_()->getDbtable('photos', 'album')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = Engine_Api::_()->user()->getViewer();

      $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
      $photo = $photoTable->createRow();
      $photo->setFromArray(array(
        'owner_type' => 'user',
        'owner_id' => $viewer->getIdentity()
      ));
      $photo->save();

      $photo->setPhoto($_FILES[$fileName]);

      $this->view->status = true;
      $this->view->name = $_FILES[$fileName]['name'];
      $this->view->photo_id = $photo->photo_id;
      $this->view->photo_url = $photo->getPhotoUrl();

      $table = Engine_Api::_()->getDbtable('albums', 'album');
      $album = $table->getSpecialAlbum($viewer, 'message');

      $photo->album_id = $album->album_id;
      $photo->save();

      if( !$album->photo_id )
      {
        $album->photo_id = $photo->getIdentity();
        $album->save();
      }

      $auth      = Engine_Api::_()->authorization()->context;
      $auth->setAllowed($photo, 'everyone', 'view',    true);
      $auth->setAllowed($photo, 'everyone', 'comment', true);
      $auth->setAllowed($album, 'everyone', 'view',    true);
      $auth->setAllowed($album, 'everyone', 'comment', true);

      $db->commit();

    } catch( Album_Model_Exception $e ) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = $this->view->translate($e->getMessage());
      throw $e;
      return;

    } catch( Exception $e ) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      throw $e;
      return;
    }
  }  

}