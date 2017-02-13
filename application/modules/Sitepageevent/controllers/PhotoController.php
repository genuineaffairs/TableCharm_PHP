<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PhotoController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_PhotoController extends Seaocore_Controller_Action_Standard {

  public function init() {
    //CHECKING THE SUBJECT IS ALREADY PRESENT OR NOT
    if (!Engine_Api::_()->core()->hasSubject()) {
      if (0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
              null !== ($photo = Engine_Api::_()->getItem('sitepageevent_photo', $photo_id))) {
        Engine_Api::_()->core()->setSubject($photo);
      } else if (0 !== ($event_id = (int) $this->_getParam('event_id')) &&
              null !== ($sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', $event_id))) {
        Engine_Api::_()->core()->setSubject($sitepageevent);
      }
    }

    //PACKAGE BASE PRIYACY START
    $page_id = $this->_getParam('page_id');
    if (!empty($page_id)) {
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageevent")) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        }
      } else {
        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'secreate');
        if (empty($isPageOwnerAllow)) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        }
      }
    }
    //PACKAGE BASE PRIYACY END    
    else {
//      if ($this->_getParam('event_id') != null) {
//        $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', $this->_getParam('event_id'));
//        $page_id = $sitepageevent->page_id;
//      }
    }
  }

  //ACTION FOR VIEW THE NOTE PHOTO
  public function viewAction() {

    //GET LOGGED IN USER INFORMATION
    $this->view->viewer_id = $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET THE PHOTO SUBJECT
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    $this->view->isajax = (int) $this->_getParam('isajax', 0);
    if (!Engine_Api::_()->core()->hasSubject('sitepageevent_photo')) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //GET THE NOTE PHOTOS
    $this->view->sitepageeventPhoto = $sitepageeventphoto = $photo->getCollection();

    //GET THE NOTES
    $this->view->sitepageevent = $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', $sitepageeventphoto->event_id);

    //GET THE SITEPAGE ITEM
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepageevent->page_id);

    //SEND TAB ID TO TPL FILE
    $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'comment');
    if (empty($isManageAdmin)) {
      $this->view->can_comment = 0;
    } else {
      $this->view->can_comment = 1;
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = 0;
    } else {
      $this->view->can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //REPORT AND SHARE LINK SHOW OR NOT CHECKING FROM THE GLOBAL SETTING 
    $this->view->report = $report = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.report', 1);
    $this->view->share = $share = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.share', 1);
  }

  //ACTION FOR EDIT THE NOTE PHOTOS
  public function editPhotoAction() {
    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET CURRENT PAGE NUMBER
    $page = $this->_getParam('page', 1);

    //GET NOTE ID    
    $event_id = $this->_getParam('event_id');

    //GET THE NOTES
    $this->view->sitepageevent = $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', $event_id);

    //SEND TAB ID TO TPL FILE
    $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');
    $this->view->format = $this->_getParam('format');

    //GET THE SITEPAGE ITEM
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepageevent->page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //SUPERADMIN, NOTE OWNER AND SITEPAGE OWNER CAN EDIT NOTE PHOTOS
    if ((Engine_Api::_()->user()->getViewer()->getIdentity() != $sitepageevent->user_id && $can_edit == 0)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //GET NAVIGATION
    if (!$this->view->format) {
      $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');
    }

    //EDIT FORM
    $this->view->form = $form = new Sitepageevent_Form_Photo_Edit(array('item' => $sitepageevent));

    $this->view->album = $album = $sitepageevent->getSingletonAlbum($event_id);
    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber($page)->setItemCountPerPage(100);

    foreach ($paginator as $photo) {
      $subform = new Sitepageevent_Form_Photo_EditPhoto(array('elementsBelongTo' => $photo->getGuid()));
      $subform->populate($photo->toArray());
      $form->addSubForm($subform, $photo->getGuid());
      $form->cover->addMultiOption($photo->getIdentity(), $photo->getIdentity());
    }

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$form->isValid($this->getRequest()->getPost())) {
      //return;
    }

    $format = '';
    if (isset($_POST['format'])) {
      $format = $_POST['format'];
    }

    //PROCESS
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {
      //SAVE PHOTO 
      $values = $form->getValues();
      $sitepageevent->setFromArray($values);
      $sitepageevent->modified_date = date('Y-m-d H:i:s');
      $sitepageevent->save();
      $cover = $values['cover'];

      foreach ($paginator as $photo) {
        $subform = $form->getSubForm($photo->getGuid());
        $subValues = $subform->getValues();
        $subValues = $subValues[$photo->getGuid()];
        unset($subValues['photo_id']);
        if (isset($cover) && $cover == $photo->photo_id) {
          $sitepageevent->photo_id = $photo->file_id;
          $sitepageevent->save();
        }
        if (isset($subValues['delete']) && $subValues['delete'] == '1') {
          if ($sitepageevent->photo_id == $photo->file_id) {
            $sitepageevent->photo_id = 0;
            $sitepageevent->save();
          }
          $photo->delete();
        
        } else {
          $photo->setFromArray($subValues);
          $photo->save();
        }
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //REDIRECTING TO THE NOTE VIEW PAGE
    if ($format != 'smoothbox') {
      return $this->_helper->redirector->gotoRoute(array('user_id' => $sitepageevent->user_id, 'event_id' => $sitepageevent->event_id, 'slug' => $sitepageevent->getSlug(), 'tab_id' => $this->view->tab_selected_id), 'sitepageevent_detail_view', true);
    } else {
      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'messages' => array('Changes saved.'),
                  'layout' => 'default-simple',
                  'parentRefresh' => true,
                  'closeSmoothbox' => true,
              ));
    }
  }

  //ACTION FOR PHOTO UPLOAD
  public function uploadAction() {

    //GET SITEPAGENOTE SUBJECT
    $sitepageevent = Engine_Api::_()->core()->getSubject();

    //GET LOGGED IN USER INFORMATION    
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //IF ISSET FILEDATA MENAS PHOTOS THEN WE REDIRECTING IT TO THE UPLOAD PHOTO ACTION
    if (isset($_GET['ul']) || isset($_FILES['Filedata']))
      return $this->_forwardCustom('upload-photo', null, null, array('format' => 'json', 'event_id' => (int) $sitepageevent->getIdentity()));

    //SEND TAB ID TO TPL FILE  
    $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');



    //NOTE OWNER ONLY CAN UPLOAD THE PHOTO
    if ($viewer_id != $sitepageevent->user_id) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //GET THE ITEM OF SITEPAGE
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepageevent->page_id);
    if (Engine_Api::_()->core()->hasSubject()) {
      Engine_Api::_()->core()->clearSubject();
    }
    Engine_Api::_()->core()->setSubject($sitepage);

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

    //GET SITEPAGENOTE ALBUM
    $album = $sitepageevent->getSingletonAlbum();

    //SEND NOTE ID TO TPL FILE  
    $this->view->event_id = $sitepageevent->event_id;
    $this->view->form = $form = new Sitepageevent_Form_Photo_Upload();
    $form->file->setAttrib('data', array('event_id' => $sitepageevent->getIdentity()));

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //PROCESS
    $db = Engine_Api::_()->getItemTable('sitepageevent_photo')->getAdapter();
    $db->beginTransaction();

    try {
      //SAVE PHOTO DETAIL
      $values = $form->getValues();
      $params = array(
          'event_id' => $sitepageevent->getIdentity(),
          'user_id' => $viewer_id,
      );

      $count = 0;
      foreach ($values['file'] as $photo_id) {
        $photo = Engine_Api::_()->getItem("sitepageevent_photo", $photo_id);
        if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
          continue;
        $photo->collection_id = $album->album_id;
        $photo->album_id = $album->album_id;
        $photo->save();

        if ($sitepageevent->photo_id == 0) {
          $sitepageevent->photo_id = $photo->file_id;
          $sitepageevent->save();
        }

        if ($action instanceof Activity_Model_Action && $count < 8) {
          $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
        }
        $count++;
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //REDIRECTING TO THE EDIT NOTE PHOTO PAGE
    return $this->_helper->redirector->gotoRoute(array('action' => 'edit-photo', 'event_id' => $sitepageevent->event_id, 'page_id' => $sitepageevent->page_id, 'tab' => $this->view->tab_selected_id), 'sitepageevent_photo_extended', true);
  }

  //ACTION FOR PHOTO UPLOAD
  public function uploadPhotoAction() {

    //GET LOGGED IN USER INFORMATION 
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET THE SITEPAGE SUBJECT
    $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', (int) $this->_getParam('event_id'));

    //SEND TAB ID TO TPL FILE
    //   $this->view->tab_selected_id = $this->_getParam('tab');
    // $getPackageNoteUpload = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepageevent');
    //NOTE OWNER ONLY CAN UPLOAD THE PHOTO
    if ($viewer_id != $sitepageevent->user_id) {
      //return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //CHECK MAX FILE SIZE
    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    //FORM VALUES
    $values = $this->getRequest()->getPost();
    if (empty($values)) {
      return;
    }

    //CHECK THE FILE IS PRESENT OR NOT
    if (empty($values['Filename'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
      return;
    }

    //CHECK THE FILE IS VALID OR NOT
    if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    //PROCESS
    $db = Engine_Api::_()->getDbtable('photos', 'sitepageevent')->getAdapter();
    $db->beginTransaction();
    try {
      //SAVE NOTE PHOTO DETAIL
      $album = $sitepageevent->getSingletonAlbum();
      $params = array(
          //WE CAN SET THEM NOW SINCE ONLY ONE ALBUM IS ALLOWED
          'collection_id' => $album->getIdentity(),
          'album_id' => $album->getIdentity(),
          'event_id' => $sitepageevent->getIdentity(),
          'user_id' => $viewer_id,
      );
//      $sitepageevent->total_photos++;
//      $sitepageevent->save();

      $photo = Engine_Api::_()->getDbTable('photos', 'sitepageevent')->createPhoto($params, $_FILES['Filedata']);
      $photo_id = $photo->photo_id;
      if (!$sitepageevent->photo_id) {
        $sitepageevent->photo_id = $photo->file_id;
        $sitepageevent->save();
      }
      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->photo_id = $photo_id;

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      return;
    }
  }

  //ACTION FOR DELETE IMAGE
  public function removeAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET LOGGED IN USER INFORMATION
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET NAVIGATION
    $this->view->format = $this->_getParam('format');
    if (!$this->view->format) {
      $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');
    }

    //NOTE ID
    $event_id = (int) $this->_getParam('event_id');

    //PHOTO REMOVE 
    $photoremove = (int) $this->_getParam('photoremove');

    //NOTE ITEM
    $this->view->sitepageevent = $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', $event_id);

    //SEND TAB IT TO THE TPL
    $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');
    //GET SITEPAGE ITEM
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepageevent->page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //SUPERADMIN, NOTE OWNER AND SITEPAGE OWNER CAN EDIT NOTE
    if (($viewer_id != $sitepageevent->user_id) && $can_edit != 1) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //IF NOT POST OR FORM NOT VALID, RETURN
    if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true || $photoremove) {
      $photo = Engine_Api::_()->getItem('sitepageevent_photo', (int) $this->_getParam('photo_id'));
      $file_id = $photo->file_id;

      //PROCESS
      $db = $photo->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        //DELETE PHOTO
        $photo->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      if ($sitepageevent->photo_id == $file_id) {
        $sitepageevent->photo_id = 0;
        $sitepageevent->save();
      }
      if (isset($_POST['format'])) {
        $format = $_POST['format'];
      }

			$this->_forwardCustom('success', 'utility', 'core', array(
            'smoothboxClose' => 2,
            'redirect' => $this->_helper->url->url(array('user_id' => $sitepageevent->user_id, 'event_id' => $event_id, 'slug' => $sitepageevent->getSlug(), 'tab' => $this->view->tab_selected_id), 'sitepageevent_detail_view', true),
            'parentRedirectTime' => '2',
            'messages' => array('Photo Deleted.')
        ));
    }
  }

  //ACTION FOR PHOTO TITLE AND DESCRIPTION
  public function photoEditAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET LOGGED IN USER INFORMATION  
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET PHOTO SUBJECT
    $photo = Engine_Api::_()->core()->getSubject();

    //GET SITEPAGENOTE ITEM
    $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', (int) $this->_getParam('event_id'));

    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', (int) $this->_getParam('page_id'));

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //SUPERADMIN, NOTE OWNER AND SITEPAGE OWNER CAN EDIT NOTE PHOTO TITLE AND DESCRIPTION
    if ($viewer_id != $sitepageevent->user_id && $can_edit != 1) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //PHOTO EDIT FORM
    $this->view->form = $form = new Sitepageevent_Form_Photo_Photoedit();

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$this->getRequest()->isPost()) {
      //POPULATE FORM
      $form->populate($photo->toArray());
      return;
    }

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //Process
    $db = Engine_Api::_()->getDbtable('photos', 'sitepageevent')->getAdapter();
    $db->beginTransaction();

    try {
      //SAVE PHOTO TITLE AND DESCRIPTION
      $photo->setFromArray($form->getValues())->save();
      $values = $form->getValues();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //SEND TAB ID TO TPL FILE
    $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');

    $this->_redirectCustom($photo->getHref());
  }

  //ACTION FOR EDIT DESCRIPTION FOR THE NOTE PHOTOS IN THE LIGHTBOX
  public function editDescriptionAction() {

    //HERE WE CAN GET THE TEXT BY THE GET PARAM
    $text = $this->_getParam('text_string');

    //GET PHOTO ITEM 
    $photo = Engine_Api::_()->getItem('sitepageevent_photo', $this->_getParam('photo_id'));

    //PROCESS
    $db = Engine_Api::_()->getDbtable('photos', 'sitepage')->getAdapter();
    $db->beginTransaction();

    try {
      //SAVE NOTE PHOTO DESCRIPTION
      $value['description'] = $text;
      $photo->setFromArray($value);
      $photo->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    exit();
  }

  //ACTION FOR ROTATING THE NOTE PHOTOS
  public function rotateAction() {

    //CHECKING IF THE PHOTO SUBJECT IS THERE OR NOT
    if (!$this->_helper->requireSubject('sitepageevent_photo')->isValid())
      return;

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid method');
      return;
    }

    //GET PHOTO SUBECJT
    $photo = Engine_Api::_()->core()->getSubject('sitepageevent_photo');

    //GET THE ANGLE
    $angle = (int) $this->_getParam('angle', 90);
    if (!$angle || !($angle % 360)) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid angle, must not be empty');
      return;
    }
    if (!in_array((int) $angle, array(90, 270))) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid angle, must be 90 or 270');
      return;
    }

    $file = Engine_Api::_()->getItem('storage_file', $photo->file_id);
    if (!($file instanceof Storage_Model_File)) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Could not retrieve file');
      return;
    }

    $tmpFile = $file->temporary();

    $image = Engine_Image::factory();
    $image->open($tmpFile)
            ->rotate($angle)
            ->write()
            ->destroy();

    //PROCESS
    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $photo->setPhoto($tmpFile);
      @unlink($tmpFile);
      $db->commit();
    } catch (Exception $e) {
      @unlink($tmpFile);
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->href = $photo->getPhotoUrl();
  }

  //ACTION FOR FLIPING THE NOTE PHOTOS
  public function flipAction() {

    //CHECKING IF THE PHOTO SUBJECT IS THERE OR NOT
    if (!$this->_helper->requireSubject('sitepageevent_photo')->isValid())
      return;

    //IF NOT POST OR FORM NOT VALID, RETURN      
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid method');
      return;
    }

    //GET PHOTO SUBECJT
    $photo = Engine_Api::_()->core()->getSubject('sitepageevent_photo');

    //GET DIRECTION
    $direction = $this->_getParam('direction');
    if (!in_array($direction, array('vertical', 'horizontal'))) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid direction');
      return;
    }

    $file = Engine_Api::_()->getItem('storage_file', $photo->file_id);
    if (!($file instanceof Storage_Model_File)) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Could not retrieve file');
      return;
    }

    $tmpFile = $file->temporary();

    $image = Engine_Image::factory();
    $image->open($tmpFile)
            ->flip($direction != 'vertical')
            ->write()
            ->destroy();

    //PROCESS
    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $photo->setPhoto($tmpFile);
      @unlink($tmpFile);
      $db->commit();
    } catch (Exception $e) {
      @unlink($tmpFile);
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->href = $photo->getPhotoUrl();
  }


  //ACTION FOR PHOTO UPLOAD
  public function uploadSitemobilePhotoAction() {

    //GET SITEPAGENOTE SUBJECT
    $sitepageevent = Engine_Api::_()->core()->getSubject();

    //GET LOGGED IN USER INFORMATION    
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //SEND TAB ID TO TPL FILE  
    $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');

    //NOTE OWNER ONLY CAN UPLOAD THE PHOTO
    if ($viewer_id != $sitepageevent->user_id) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //GET THE ITEM OF SITEPAGE
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepageevent->page_id);
    if (Engine_Api::_()->core()->hasSubject()) {
      Engine_Api::_()->core()->clearSubject();
    }
    Engine_Api::_()->core()->setSubject($sitepage);

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

    //GET SITEPAGENOTE ALBUM
    $album = $sitepageevent->getSingletonAlbum();

    //SEND NOTE ID TO TPL FILE  
    $this->view->event_id = $sitepageevent->event_id;
    $this->view->form = $form = new Sitepageevent_Form_SitemobilePhoto_Upload();
    $form->file->setAttrib('data', array('event_id' => $sitepageevent->getIdentity()));

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //CHECK MAX FILE SIZE
    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    //FORM VALUES
    $values = $this->getRequest()->getPost();
    if (empty($values)) {
      return;
    }

    //PROCESS
    $tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitepageevent');
    $db = $tablePhoto->getAdapter();
    $db->beginTransaction();

    //COUNT NO. OF PHOTOS (CHECK ATLEAST SINGLE PHOTO UPLOAD).
    $count = 0;
    foreach ($_FILES['Filedata']['name'] as $data) {
      if (!empty($data)) {
        $count = 1;
        break;
      }
    }

    try {

			if (!isset($_FILES['Filedata']) || !isset($_FILES['Filedata']['name']) || $count == 0) {
        $this->view->status = false;
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid Upload'));
        return;
      }

      foreach ($_FILES['Filedata']['name'] as $key => $uploadFile) {
				$album = $sitepageevent->getSingletonAlbum();
				$params = array(
						//WE CAN SET THEM NOW SINCE ONLY ONE ALBUM IS ALLOWED
						'collection_id' => $album->getIdentity(),
						'album_id' => $album->getIdentity(),
						'event_id' => $sitepageevent->getIdentity(),
						'user_id' => $viewer_id,
				);

					$file = array('name' => $_FILES['Filedata']['name'][$key], 'tmp_name' => $_FILES['Filedata']['tmp_name'][$key], 'type' => $_FILES['Filedata']['type'][$key], 'size' => $_FILES['Filedata']['size'][$key], 'error' => $_FILES['Filedata']['error'][$key]);

					if (!is_uploaded_file($file['tmp_name'])) {
						continue;
					}
				$photo = Engine_Api::_()->getDbTable('photos', 'sitepageevent')->createPhoto($params, $file);
				$photo_id = $photo->photo_id;
				if (!$sitepageevent->photo_id) {
					$sitepageevent->photo_id = $photo->file_id;
					$sitepageevent->save();
				}
				$this->view->status = true;
				$this->view->name = $_FILES['Filedata']['name'];
				$this->view->photo_id = $photo_id;

				$db->commit();
				// $order++;
      }

    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      return;
    }

     //REDIRECTING TO THE EVENT VIEW PAGE
    if ($format != 'smoothbox') {
     return $this->_forwardCustom('success', 'utility', 'core', array(
                  'layout' => 'default-simple',
                  'parentRedirect' =>$this->_helper->redirector->gotoRoute(array('user_id' => $sitepageevent->user_id, 'event_id' => $sitepageevent->event_id, 'slug' => $sitepageevent->getSlug(), 'tab' => $this->view->tab_selected_id), 'sitepageevent_detail_view', true),
              ));
    } else {
      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'messages' => array('Changes saved.'),
                  'layout' => 'default-simple',
                  'parentRefresh' => true,
                  'closeSmoothbox' => true,
              ));
    }
  }

}

?>