<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PhotoController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_PhotoController extends Seaocore_Controller_Action_Standard {

  public function init() {

    //CHECKING THE SUBJECT IS ALREADY PRESENT OR NOT
    if (!Engine_Api::_()->core()->hasSubject()) {
      if (0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
              null !== ($photo = Engine_Api::_()->getItem('sitepagenote_photo', $photo_id))) {
        Engine_Api::_()->core()->setSubject($photo);
      } else if (0 !== ($note_id = (int) $this->_getParam('note_id')) &&
              null !== ($sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $note_id))) {
        Engine_Api::_()->core()->setSubject($sitepagenote);
      }
    }

    //PACKAGE BASE PRIYACY START
    $page_id = $this->_getParam('page_id');
    if (!empty($page_id)) {
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagenote")) {
          return $this->_forward('requireauth', 'error', 'core');
        }
      } else {
        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sncreate');
        if (empty($isPageOwnerAllow)) {
          return $this->_forward('requireauth', 'error', 'core');
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
  }

  //ACTION FOR VIEW THE NOTE PHOTO
  public function viewAction() {

    //GET LOGGED IN USER INFORMATION
    $this->view->viewer_id = $viewer_id =Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET THE PHOTO SUBJECT
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    $this->view->isajax = (int) $this->_getParam('isajax', 0);

    //GET THE NOTE PHOTOS
    $this->view->sitepagenotePhoto = $sitepagenotephoto = $photo->getCollection();

    //GET THE NOTES
    $this->view->sitepagenote = $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $sitepagenotephoto->note_id);

    //GET THE SITEPAGE ITEM
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagenote->page_id);

    //SEND TAB ID TO TPL FILE
    $this->view->tab_selected_id = $this->_getParam('tab');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
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
    $note_id = $this->_getParam('note_id');
    
    //GET THE NOTES
    $this->view->sitepagenote = $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $note_id);

    //SEND TAB ID TO TPL FILE
    $this->view->tab_selected_id = $this->_getParam('tab');
    $this->view->format = $this->_getParam('format');

    //GET THE SITEPAGE ITEM
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagenote->page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    
    //SUPERADMIN, NOTE OWNER AND SITEPAGE OWNER CAN EDIT NOTE PHOTOS
    if ((Engine_Api::_()->user()->getViewer()->getIdentity() != $sitepagenote->owner_id && $can_edit == 0)) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //GET NAVIGATION
    if (!$this->view->format) {
      $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');
    }

    //EDIT FORM
    $this->view->form = $form = new Sitepagenote_Form_Edit(array('item' => $sitepagenote));

    $this->view->album = $album = Engine_Api::_()->getDbTable('albums', 'sitepagenote')->getSingletonAlbum($note_id);
    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber($page)->setItemCountPerPage(100);

    foreach ($paginator as $photo) {
      $subform = new Sitepagenote_Form_Photo_Edit(array('elementsBelongTo' => $photo->getGuid()));
      $form->removeElement('title');
      $form->removeElement('body');
      $form->removeElement('draft');
      $form->removeElement('search');
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
      $sitepagenote->setFromArray($values);
      $sitepagenote->modified_date = date('Y-m-d H:i:s');
      $sitepagenote->save();
      $cover = $values['cover'];

      foreach ($paginator as $photo) {
        $subform = $form->getSubForm($photo->getGuid());
        $subValues = $subform->getValues();
        $subValues = $subValues[$photo->getGuid()];
        unset($subValues['photo_id']);
        if (isset($cover) && $cover == $photo->photo_id) {
          $sitepagenote->photo_id = $photo->file_id;
          $sitepagenote->save();
        }
        if (isset($subValues['delete']) && $subValues['delete'] == '1') {
          if ($sitepagenote->photo_id == $photo->file_id) {
            $sitepagenote->photo_id = 0;
            $sitepagenote->save();
          }
          $photo->delete();
          $sitepagenote->total_photos--;
          $sitepagenote->save();
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
      return $this->_helper->redirector->gotoRoute(array('user_id' => $sitepagenote->owner_id, 'note_id' => $sitepagenote->note_id, 'slug' => $sitepagenote->getSlug(), 'tab' => $this->view->tab_selected_id), 'sitepagenote_detail_view', true);
    } else {
      return $this->_forward('success', 'utility', 'core', array(
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
    $sitepagenote = Engine_Api::_()->core()->getSubject();

    //GET LOGGED IN USER INFORMATION    
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //IF ISSET FILEDATA MENAS PHOTOS THEN WE REDIRECTING IT TO THE UPLOAD PHOTO ACTION
    if (isset($_GET['ul']) || isset($_FILES['Filedata']))
      return $this->_forward('upload-photo', null, null, array('format' => 'json', 'note_id' => (int) $sitepagenote->getIdentity()));

    //SEND TAB ID TO TPL FILE  
    $this->view->tab_selected_id = $this->_getParam('tab');

    //Getting the item of the sitepagenote.
    $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', (int) $sitepagenote->getIdentity());

    //NOTE OWNER ONLY CAN UPLOAD THE PHOTO
    if ($viewer_id != $sitepagenote->owner_id) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //GET THE ITEM OF SITEPAGE
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagenote->page_id);
    if (Engine_Api::_()->core()->hasSubject()) {
      Engine_Api::_()->core()->clearSubject();
    }
    Engine_Api::_()->core()->setSubject($sitepage);

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

    //GET SITEPAGENOTE ALBUM
    $album = Engine_Api::_()->getDbTable('albums', 'sitepagenote')->getSingletonAlbum($sitepagenote->note_id, $sitepagenote->title);

    //SEND NOTE ID TO TPL FILE  
    $this->view->note_id = $sitepagenote->note_id;
    $this->view->form = $form = new Sitepagenote_Form_Photo_Upload();
    $form->file->setAttrib('data', array('note_id' => $sitepagenote->getIdentity()));

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //PROCESS
    $db = Engine_Api::_()->getItemTable('sitepagenote_photo')->getAdapter();
    $db->beginTransaction();

    try {
      //SAVE PHOTO DETAIL
      $values = $form->getValues();
      $params = array(
          'note_id' => $sitepagenote->getIdentity(),
          'user_id' => $viewer_id,
      );

      $count = 0;
      foreach ($values['file'] as $photo_id) {
        $photo = Engine_Api::_()->getItem("sitepagenote_photo", $photo_id);
        if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
          continue;
        $photo->collection_id = $album->album_id;
        $photo->album_id = $album->album_id;
        $photo->save();

        if ($sitepagenote->photo_id == 0) {
          $sitepagenote->photo_id = $photo->file_id;
          $sitepagenote->save();
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
    return $this->_helper->redirector->gotoRoute(array('note_id' => $sitepagenote->note_id, 'page_id' => $sitepagenote->page_id, 'tab' => $this->view->tab_selected_id), 'sitepagenote_editphoto', true);
  }

  //ACTION FOR PHOTO UPLOAD
  public function uploadPhotoAction() {

    //GET LOGGED IN USER INFORMATION 
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET THE SITEPAGE SUBJECT
    $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', (int) $this->_getParam('note_id'));

    //SEND TAB ID TO TPL FILE
    $this->view->tab_selected_id = $this->_getParam('tab');

    $getPackageNoteUpload = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepagenote');

    //NOTE OWNER ONLY CAN UPLOAD THE PHOTO
    if ($viewer_id != $sitepagenote->owner_id) {
      return $this->_forward('requireauth', 'error', 'core');
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
    $values = empty($getPackageNoteUpload) ? null : $this->getRequest()->getPost();
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
    $db = Engine_Api::_()->getDbtable('photos', 'sitepagenote')->getAdapter();
    $db->beginTransaction();
    try {
      //SAVE NOTE PHOTO DETAIL
      $album = Engine_Api::_()->getDbTable('albums', 'sitepagenote')->getSingletonAlbum($sitepagenote->getIdentity());
      $params = array(
          //WE CAN SET THEM NOW SINCE ONLY ONE ALBUM IS ALLOWED
          'collection_id' => $album->getIdentity(),
          'album_id' => $album->getIdentity(),
          'note_id' => $sitepagenote->getIdentity(),
          'user_id' => $viewer_id,
      );
      $sitepagenote->total_photos++;
      $sitepagenote->save();

      $photo_id = Engine_Api::_()->getDbTable('photos', 'sitepagenote')->createPhoto($params, $_FILES['Filedata'])->photo_id;
      if (!$sitepagenote->photo_id) {
        $sitepagenote->photo_id = $photo_id;
        $sitepagenote->save();
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
    $note_id = (int) $this->_getParam('note_id');

    //PHOTO REMOVE 
    $photoremove = (int) $this->_getParam('photoremove');

    //NOTE ITEM
    $this->view->sitepagenote = $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $note_id);

    //SEND TAB IT TO THE TPL
    $this->view->tab_selected_id = $this->_getParam('tab');

    //GET SITEPAGE ITEM
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagenote->page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    
    //SUPERADMIN, NOTE OWNER AND SITEPAGE OWNER CAN EDIT NOTE
    if (($viewer_id != $sitepagenote->owner_id) && $can_edit != 1) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //IF NOT POST OR FORM NOT VALID, RETURN
    if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true || $photoremove) {
      $photo = Engine_Api::_()->getItem('sitepagenote_photo', (int) $this->_getParam('photo_id'));
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

      if ($sitepagenote->photo_id == $file_id) {
        $sitepagenote->photo_id = 0;
        $sitepagenote->save();
      }
      $sitepagenote->total_photos--;
      $sitepagenote->save();

      if (isset($_POST['format'])) {
        $format = $_POST['format'];
      }

      //REDIRECTING TO THE NOTE VIEW PAGE
      if ($format != 'smoothbox') {
        return $this->_helper->redirector->gotoRoute(array('user_id' => $sitepagenote->owner_id, 'note_id' => $note_id, 'slug' => $sitepagenote->getSlug(), 'tab' => $this->view->tab_selected_id), 'sitepagenote_detail_view', true);
      } else {
        $this->_forwardCustom('success', 'utility', 'core', array(
            'smoothboxClose' => 2,
            'parentRedirect' => $this->_helper->url->url(array('user_id' => $sitepagenote->owner_id, 'note_id' => $note_id, 'slug' => $sitepagenote->getSlug(), 'tab' => $this->view->tab_selected_id), 'sitepagenote_detail_view', true),
            'parentRedirectTime' => '2',
            'messages' => array('Photo Deleted.')
        ));
      }
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
    $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', (int) $this->_getParam('note_id'));

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
    if ($viewer_id != $sitepagenote->owner_id && $can_edit != 1) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //PHOTO EDIT FORM
    $this->view->form = $form = new Sitepagenote_Form_Photo_Photoedit();

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
    $db = Engine_Api::_()->getDbtable('photos', 'sitepagenote')->getAdapter();
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
    $this->view->tab_selected_id = $this->_getParam('tab');

    //REDIRECTING TO THE NOTE IMAGE VIEW PAGE.    
    return $this->_forward('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved.')),
        'smoothboxClose' => 2,
        'parentRedirect' => $this->_helper->url->url(array('owner_id' => $photo->user_id, 'album_id' => $photo->album_id, 'photo_id' => $photo->photo_id, 'tab' => $this->view->tab_selected_id), 'sitepagenote_image_specific'),
        'parentRedirectTime' => '2',
    ));
  }

  //ACTION FOR EDIT DESCRIPTION FOR THE NOTE PHOTOS IN THE LIGHTBOX
  public function editDescriptionAction() {

    //HERE WE CAN GET THE TEXT BY THE GET PARAM
    $text = $this->_getParam('text_string');

    //GET PHOTO ITEM 
    $photo = Engine_Api::_()->getItem('sitepagenote_photo', $this->_getParam('photo_id'));

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
    if (!$this->_helper->requireSubject('sitepagenote_photo')->isValid())
      return;

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid method');
      return;
    }

    //GET PHOTO SUBECJT
    $photo = Engine_Api::_()->core()->getSubject('sitepagenote_photo');

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
    if (!$this->_helper->requireSubject('sitepagenote_photo')->isValid())
      return;

    //IF NOT POST OR FORM NOT VALID, RETURN      
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = $this->view->translate('Invalid method');
      return;
    }

    //GET PHOTO SUBECJT
    $photo = Engine_Api::_()->core()->getSubject('sitepagenote_photo');

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

  public function uploadSitemobilePhotoAction() {
    //GET SITEPAGENOTE SUBJECT
    $sitepagenote = Engine_Api::_()->core()->getSubject(); //---1
    //GET LOGGED IN USER INFORMATION    
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //SEND TAB ID TO TPL FILE  
    $this->view->tab_selected_id = $this->_getParam('tab');
    //NOTE OWNER ONLY CAN UPLOAD THE PHOTO
    if ($viewer_id != $sitepagenote->owner_id) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //GET THE ITEM OF SITEPAGE
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagenote->page_id);
    if (Engine_Api::_()->core()->hasSubject()) {
      Engine_Api::_()->core()->clearSubject();
    }
    Engine_Api::_()->core()->setSubject($sitepage);

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

    //GET SITEPAGENOTE ALBUM
    $album = Engine_Api::_()->getDbTable('albums', 'sitepagenote')->getSingletonAlbum($sitepagenote->note_id, $sitepagenote->title);

    //SEND NOTE ID TO TPL FILE  
    $this->view->note_id = $sitepagenote->note_id;
    $this->view->form = $form = new Sitepagenote_Form_SitemobilePhoto_Upload();
    $form->file->setAttrib('data', array('note_id' => $sitepagenote->getIdentity()));


    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }
    //GET FORM VALUES
    $values = $this->getRequest()->getPost();
    if (empty($values)) {
      return;
    }

    //CHECK MAX FILE SIZE
    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    //CHECK THE FILE IS PRESENT OR NOT
    if (empty($_FILES['Filedata'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
      return;
    }

    //COUNT NO. OF PHOTOS (CHECK ATLEAST SINGLE PHOTO UPLOAD).
    $count = 0;
    foreach ($_FILES['Filedata']['name'] as $data) {
      if (!empty($data)) {
        $count = 1;
        break;
      }
    }
    //PROCESS
    $tablePhoto = Engine_Api::_()->getDbtable('photos', 'sitepagenote');
    $db = $tablePhoto->getAdapter();
    $db->beginTransaction();

    try {
      //SAVE NOTE PHOTO DETAIL
      $album = Engine_Api::_()->getDbTable('albums', 'sitepagenote')->getSingletonAlbum($sitepagenote->getIdentity());
      if (!isset($_FILES['Filedata']) || !isset($_FILES['Filedata']['name']) || $count == 0) {
        $this->view->status = false;
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid Upload'));
        return;
      }
      foreach ($_FILES['Filedata']['name'] as $key => $uploadFile) {
        $params = array(
            //WE CAN SET THEM NOW SINCE ONLY ONE ALBUM IS ALLOWED
            'collection_id' => $album->getIdentity(),
            'album_id' => $album->getIdentity(),
            'note_id' => $sitepagenote->getIdentity(),
            'user_id' => $viewer_id,
        );
        
        $file = array('name' => $_FILES['Filedata']['name'][$key], 'tmp_name' => $_FILES['Filedata']['tmp_name'][$key], 'type' => $_FILES['Filedata']['type'][$key], 'size' => $_FILES['Filedata']['size'][$key], 'error' => $_FILES['Filedata']['error'][$key]);
        if (!is_uploaded_file($file['tmp_name'])) {
          continue;
        }
        $sitepagenote->total_photos++;
        $sitepagenote->save();

        $photo_id = Engine_Api::_()->getDbTable('photos', 'sitepagenote')->createPhoto($params, $file)->photo_id;
        if (!$sitepagenote->photo_id) {
          $sitepagenote->photo_id = $photo_id;
          $sitepagenote->save();
        }
        $this->view->status = true;
        $this->view->name = $_FILES['Filedata']['name'];
        $this->view->photo_id = $photo_id;
        $db->commit();
      }
    } catch (Exception $e) {

      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      return;
    }

    //REDIRECTING TO THE EDIT NOTE PHOTO PAGE
//    return $this->_helper->redirector->gotoRoute(array('note_id' => $sitepagenote->note_id, 'page_id' => $sitepagenote->page_id, 'tab' => $this->view->tab_selected_id), 'sitepagenote_editphoto', true);
//    
    //REDIRECTING TO THE EDIT NOTE VIEW PAGE
     return $this->_helper->redirector->gotoRoute(array('user_id' => $sitepagenote->owner_id, 'note_id' => $sitepagenote->note_id, 'slug' => $sitepagenote->getSlug(), 'tab' => $this->view->tab_selected_id), 'sitepagenote_detail_view', true);
  }

}

?>