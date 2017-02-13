<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: PhotoController.php 9465 2011-11-02 02:04:43Z shaun $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Resume_PhotoController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
          null !== ($photo = Engine_Api::_()->getItem('resume_photo', $photo_id)) )
      {
        Engine_Api::_()->core()->setSubject($photo);
      }

      else if( 0 !== ($resume_id = (int) $this->_getParam('resume_id')) &&
          null !== ($resume = Engine_Api::_()->getItem('resume', $resume_id)) )
      {
        Engine_Api::_()->core()->setSubject($resume);
      }
    }
    
    $this->view->headLink()->appendStylesheet(
            $this->view->layout()->staticBaseUrl . 'application/modules/Resume/externals/styles/resume.css');
    
    $this->_helper->requireUser->addActionRequires(array(
      'upload',
      'upload-photo', // Not sure if this is the right
      'edit',
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'list' => 'resume',
      'upload' => 'resume',
      'view' => 'resume_photo',
      'edit' => 'resume_photo',
    ));
  }

  public function listAction()
  {
    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $resume->getSingletonAlbum();

    if( !$this->_helper->requireAuth()->setAuthParams($resume, null, 'view')->isValid() ) {
      return;
    }

    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $this->view->canUpload = $resume->authorization()->isAllowed(null, 'photo');
    
    //GET TAB ID
    $this->view->tab_selected_id = $this->_getParam('tab');
    
    // Render
    $this->_helper->content
        //->setNoRender(false)
        ->setEnabled()
        ;      
  }
  
  public function viewAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $photo->getCollection();
    $this->view->resume = $resume = $photo->getResume();
    $this->view->canEdit = $photo->canEdit(Engine_Api::_()->user()->getViewer());

    if( !$this->_helper->requireAuth()->setAuthParams($resume, null, 'view')->isValid() ) {
      return;
    }

    if( !$viewer || !$viewer->getIdentity() || $photo->user_id != $viewer->getIdentity() ) {
      $photo->view_count = new Zend_Db_Expr('view_count + 1');
      $photo->save();
    }

    // Get tags
    $tags = array();
    foreach( $photo->tags()->getTagMaps() as $tagmap ) {
      $tags[] = array_merge($tagmap->toArray(), array(
        'id' => $tagmap->getIdentity(),
        'text' => $tagmap->getTitle(),
        'href' => $tagmap->getHref(),
        'guid' => $tagmap->tag_type . '_' . $tagmap->tag_id
      ));
    }
    $this->view->tags = $tags;
    
    //GET TAB ID
    $this->view->tab_selected_id = $tab_selected_id = $this->_getParam('tab');
    
    // Render
    $this->_helper->content
        //->setNoRender(false)
        ->setEnabled()
        ;    

  }

  public function uploadAction()
  {
    if( isset($_GET['ul']) || isset($_FILES['Filedata']) ) {
      return $this->_forward('upload-photo', null, null, array('format' => 'json'));
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $resume = Engine_Api::_()->core()->getSubject();

    if( !$this->_helper->requireAuth()->setAuthParams($resume, null, 'photo')->isValid() ) {
      return;
    }

    $album = $resume->getSingletonAlbum();

    $this->view->resume = $resume;
    $this->view->form = $form = new Resume_Form_Photo_Upload();
    $form->file->setAttrib('data', array('resume_id' => $resume->getIdentity()));

    if( !$this->getRequest()->isPost() )
    {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $table = Engine_Api::_()->getItemTable('resume_photo');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $values = $form->getValues();
      $params = array(
        'resume_id' => $resume->getIdentity(),
        'user_id' => $viewer->getIdentity(),
      );
      
      // Add action and attachments
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $resume, 'resume_photo_upload', null, array(
        'count' => count($values['file'])
      ));

      // Do other stuff
      $count = 0;
      foreach( $values['file'] as $photo_id )
      {
        $photo = Engine_Api::_()->getItem("resume_photo", $photo_id);
        if( !($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity() ) continue;

        /*
        if( $set_cover )
        {
          $album->photo_id = $photo_id;
          $album->save();
          $set_cover = false;
        }
        */

        $photo->collection_id = $album->album_id;
        $photo->album_id = $album->album_id;
        $photo->save();

        if( $action instanceof Activity_Model_Action && $count < 8 )
        {
          $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
        }
        $count++;
      }
      
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }


    $this->_redirectCustom($resume);
  }

  public function uploadPhotoAction()
  {
    $resume = Engine_Api::_()->getItem('resume', $this->_getParam('resume_id'));
    
    if( !$this->_helper->requireAuth()->setAuthParams($resume, null, 'photo')->isValid() ) {
      return;
    }

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

    // @todo check auth
    //$resume

    $values = $this->getRequest()->getPost();
    if( empty($values['Filename']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
      return;
    }

    if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    $db = Engine_Api::_()->getDbtable('photos', 'resume')->getAdapter();
    $db->beginTransaction();

    try {
      $viewer = Engine_Api::_()->user()->getViewer();
      $album = $resume->getSingletonAlbum();
      
      $params = array(
        // We can set them now since only one album is allowed
        'collection_id' => $album->getIdentity(),
        'album_id' => $album->getIdentity(),

        'resume_id' => $resume->getIdentity(),
        'user_id' => $viewer->getIdentity(),
      );
      
      $photoTable = Engine_Api::_()->getItemTable('resume_photo');
      $photo = $photoTable->createRow();
      $photo->setFromArray($params);
      $photo->save();
      
      $photo->setPhoto($_FILES['Filedata']);
      
      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->photo_id = $photo->photo_id;

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      // throw $e;
      return;
    }
  }

  public function editAction()
  {
    $photo = Engine_Api::_()->core()->getSubject();
    $resume = $photo->getParent('resume');
    if( !$this->_helper->requireAuth()->setAuthParams($resume, null, 'photo')->isValid() ) {
      return;
    }
    $this->view->form = $form = new Resume_Form_Photo_Edit();

    if( !$this->getRequest()->isPost() )
    {
      $form->populate($photo->toArray());
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('photos', 'resume')->getAdapter();
    $db->beginTransaction();

    try
    {
      $photo->setFromArray($form->getValues())->save();

      $db->commit();
    }

    catch( Exception $e )
    {
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

  public function deleteAction()
  {
    $photo = Engine_Api::_()->core()->getSubject();
    $resume = $photo->getParent('resume');
    if( !$this->_helper->requireAuth()->setAuthParams($resume, null, 'photo')->isValid() ) {
      return;
    }

    $this->view->form = $form = new Resume_Form_Photo_Delete();

    if( !$this->getRequest()->isPost() )
    {
      $form->populate($photo->toArray());
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('photos', 'resume')->getAdapter();
    $db->beginTransaction();

    try
    {
      $photo->delete();

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Photo deleted')),
      'layout' => 'default-simple',
      'parentRedirect' => $resume->getHref(),
      'closeSmoothbox' => true,
    ));
  }
}