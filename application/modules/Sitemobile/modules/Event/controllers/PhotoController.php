<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: PhotoController.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Event_PhotoController extends Core_Controller_Action_Standard {

  public function init() {
    if (!Engine_Api::_()->core()->hasSubject()) {
      if (0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
              null !== ($photo = Engine_Api::_()->getItem('event_photo', $photo_id))) {
        Engine_Api::_()->core()->setSubject($photo);
      } else if (0 !== ($event_id = (int) $this->_getParam('event_id')) &&
              null !== ($event = Engine_Api::_()->getItem('event', $event_id))) {
        Engine_Api::_()->core()->setSubject($event);
      }
    }

    $this->_helper->requireUser->addActionRequires(array(
        'upload',
        // 'upload-photo', // Not sure if this is the right
        'edit',
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
        'list' => 'event',
        'upload' => 'event',
        'view' => 'event_photo',
        'edit' => 'event_photo',
    ));
  }

  public function listAction() {
    $this->view->event = $event = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $event->getSingletonAlbum();

    if (!$this->_helper->requireAuth()->setAuthParams($event, null, 'view')->isValid()) {
      return;
    }

    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $this->view->canUpload = $event->authorization()->isAllowed(null, 'photo');
  }

  public function viewAction() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $photo->getCollection();
    $this->view->event = $event = $photo->getEvent();
    $this->view->canEdit = $photo->authorization()->isAllowed(null, 'edit');

    if (!$this->_helper->requireAuth()->setAuthParams($event, null, 'view')->isValid()) {
      return;
    }

    if (!$viewer || !$viewer->getIdentity() || $photo->user_id != $viewer->getIdentity()) {
      $photo->view_count = new Zend_Db_Expr('view_count + 1');
      $photo->save();
    }

    // Render
    $this->_helper->content
            //->setNoRender()
            ->setEnabled()
    ;
  }

  public function uploadAction() {
//     if( isset($_GET['ul']) || isset($_FILES['Filedata']) ) {
//       return $this->_forward('upload-photo', null, null, array('format' => 'json'));
//     }

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->event = $event = Engine_Api::_()->core()->getSubject();
    $album = $event->getSingletonAlbum();

    if (!$this->_helper->requireAuth()->setAuthParams($event, null, 'photo')->isValid()) {
      return;
    }

    $this->view->form = $form = new Event_Form_Photo_Upload();
    $this->view->clear_cache = true;
    $form->file->setAttrib('data', array('event_id' => $event->getIdentity()));

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

//     // Process
//     $table = Engine_Api::_()->getItemTable('event_photo');
//     $db = $table->getAdapter();
//     $db->beginTransaction();
// 
//     try
//     {
//       $values = $form->getValues();
//       $params = array(
//         'event_id' => $event->getIdentity(),
//         'user_id' => $viewer->getIdentity(),
//       );
//       
//       // Add action and attachments
//       $api = Engine_Api::_()->getDbtable('actions', 'activity');
//       $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $event, 'event_photo_upload', null, array('count' => count($values['file'])));
// 
//       // Do other stuff
//       $count = 0;
//       foreach( $values['file'] as $photo_id )
//       {
//         $photo = Engine_Api::_()->getItem("event_photo", $photo_id);
//         if( !($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity() ) continue;
// 
//         /*
//         if( $set_cover )
//         {
//           $album->photo_id = $photo_id;
//           $album->save();
//           $set_cover = false;
//         }
//         */
// 
//         $photo->collection_id = $album->album_id;
//         $photo->album_id = $album->album_id;
//         $photo->save();
// 
//         if( $action instanceof Activity_Model_Action && $count < 8 )
//         {
//           $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
//         }
//         $count++;
//       }
//       
//       $db->commit();
//     }
// 
//     catch( Exception $e )
//     {
//       $db->rollBack();
//       throw $e;
//     }

    $db = Engine_Api::_()->getDbtable('photos', 'event')->getAdapter();
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
      $values = $form->getValues();

      $viewer = Engine_Api::_()->user()->getViewer();
      $values['file'] = array();
      $photoTable = Engine_Api::_()->getDbtable('photos', 'event');
      foreach ($_FILES['Filedata']['name'] as $key => $uploadFile) {
        $file = array('name' => $_FILES['Filedata']['name'][$key], 'tmp_name' => $_FILES['Filedata']['tmp_name'][$key], 'type' => $_FILES['Filedata']['type'][$key], 'size' => $_FILES['Filedata']['size'][$key], 'error' => $_FILES['Filedata']['error'][$key]);

        if (!is_uploaded_file($file['tmp_name'])) {
          continue;
        }

        $params = array(
            // We can set them now since only one album is allowed
            'collection_id' => $album->getIdentity(),
            'album_id' => $album->getIdentity(),
            'event_id' => $event->getIdentity(),
            'user_id' => $viewer->getIdentity(),
        );


        $photo = $photoTable->createRow();
        $photo->setFromArray($params);
        $photo->save();
        $photo->setPhoto($file);
        $photo->save();
        $values['file'][] = $photo->photo_id;
      }

      if (count($values['file']) < 1) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Invalid Upload'));
        return;
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
      return;
    }

    // Add action and attachments
    $api = Engine_Api::_()->getDbtable('actions', 'activity');
    $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $event, 'event_photo_upload', null, array('count' => count($values['file'])));

    try {
      // Do other stuff
      $count = 0;
      foreach ($values['file'] as $photo_id) {
        $photo = Engine_Api::_()->getItem("event_photo", $photo_id);
        if (!($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity())
          continue;

        $photo->event_id = $event->event_id;
        $photo->save();

        if ($action instanceof Activity_Model_Action && $count < 8) {
          $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
        }
        $count++;
        $db->commit();
      }
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
      return;
    }


    // Redirect to the post
    // Try to get topic
    return $this->_forward('success', 'utility', 'core', array(
                'redirect' => $event->getHref(),
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Album Uploded.')),
            ));
  }

//   public function uploadPhotoAction()
//   {
//     $event = Engine_Api::_()->getItem('event', $this->_getParam('event_id'));
// 
//     if( !$this->_helper->requireAuth()->setAuthParams($event, null, 'photo')->isValid() ) {
//       return;
//     }
// 
//     if( !$this->_helper->requireUser()->checkRequire() )
//     {
//       $this->view->status = false;
//       $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
//       return;
//     }
// 
//     if( !$this->getRequest()->isPost() )
//     {
//       $this->view->status = false;
//       $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
//       return;
//     }
// 
//     // @todo check auth
//     //$event
// 
//     $values = $this->getRequest()->getPost();
//     if( empty($values['Filename']) ) {
//       $this->view->status = false;
//       $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
//       return;
//     }
// 
//     if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) ) {
//       $this->view->status = false;
//       $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
//       return;
//     }
// 
//     $db = Engine_Api::_()->getDbtable('photos', 'event')->getAdapter();
//     $db->beginTransaction();
// 
//     try {
//       $viewer = Engine_Api::_()->user()->getViewer();
//       $album = $event->getSingletonAlbum();
//       
//       $params = array(
//         // We can set them now since only one album is allowed
//         'collection_id' => $album->getIdentity(),
//         'album_id' => $album->getIdentity(),
//         'event_id' => $event->getIdentity(),
//         'user_id' => $viewer->getIdentity(),
//       );
//       
//       $photoTable = Engine_Api::_()->getItemTable('event_photo');
//       $photo = $photoTable->createRow();
//       $photo->setFromArray($params);
//       $photo->save();
//       
//       $photo->setPhoto($_FILES['Filedata']);
// 
//       $this->view->status = true;
//       $this->view->name = $_FILES['Filedata']['name'];
//       $this->view->photo_id = $photo->getIdentity();
// 
//       $db->commit();
//     } catch( Exception $e ) {
//       $db->rollBack();
//       $this->view->status = false;
//       $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
//       // throw $e;
//       return;
//     }
//   }

  public function editAction() {
    $photo = Engine_Api::_()->core()->getSubject();

    if (!$this->_helper->requireAuth()->setAuthParams($photo, null, 'edit')->isValid()) {
      return;
    }

    $this->view->form = $form = new Event_Form_Photo_Edit();
    $this->view->clear_cache = true;
    if (!$this->getRequest()->isPost()) {
      $form->populate($photo->toArray());
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('photos', 'event')->getAdapter();
    $db->beginTransaction();

    try {
      $photo->setFromArray($form->getValues())->save();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved')),
                'layout' => 'default-simple',
                'parentRefresh' => true,
                'closeSmoothbox' => true,
            ));
  }

  public function deleteAction() {
    $photo = Engine_Api::_()->core()->getSubject();
    $event = $photo->getParent('event');

    if (!$this->_helper->requireAuth()->setAuthParams($photo, null, 'edit')->isValid()) {
      return;
    }

    $this->view->form = $form = new Event_Form_Photo_Delete();
    $this->view->clear_cache = true;
    if (!$this->getRequest()->isPost()) {
      $form->populate($photo->toArray());
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('photos', 'event')->getAdapter();
    $db->beginTransaction();

    try {
      $photo->delete();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Photo deleted')),
                'layout' => 'default-simple',
                'parentRedirect' => $event->getHref(),
                'closeSmoothbox' => true,
            ));
  }

}