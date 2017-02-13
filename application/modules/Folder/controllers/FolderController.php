<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Folder
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */

class Folder_FolderController extends Core_Controller_Action_Standard
{

  public function init()
  {
    if( 0 !== ($folder_id = (int) $this->_getParam('folder_id')) &&
        null !== ($folder = Engine_Api::_()->getItem('folder', $folder_id)) &&
        !Engine_Api::_()->core()->hasSubject() ) {
      Engine_Api::_()->core()->setSubject($folder);
    }

    $this->_helper->requireUser();
    $this->_helper->requireSubject('folder');
    
    $this->_loadNavigations();
  }

  public function successAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->folder = $folder = Engine_Api::_()->core()->getSubject();
    
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() ) {
      return;
    }    
  }  
  
  public function editAction()
  {
  	$viewer = Engine_Api::_()->user()->getViewer();
  	$this->view->folder = $folder = Engine_Api::_()->core()->getSubject('folder');
  	
    if( !$this->_helper->requireAuth()->setAuthParams($folder, $viewer, 'edit')->isValid() ) {
      return;
    }
    
    $this->view->form = $form = new Folder_Form_Folder_Edit(array(
      'item' => $folder,
      'parent' => $folder->getParent(),
    ));

    $form->populate($folder->toArray());

    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
    $auth_keys = array(
     'view' => 'everyone',
     'comment' => 'registered',
    );
    
    // Save folder entry
    if( !$this->getRequest()->isPost() )
    {     
      foreach ($auth_keys as $auth_key => $auth_default)
      {
        $auth_field = 'auth_'.$auth_key;
        
        foreach( $roles as $i => $role )
        {
          if (isset($form->$auth_field->options[$role]) && 1 === $auth->isAllowed($folder, $role, $auth_key))
          {
            $form->$auth_field->setValue($role);
          }
        }
      }
      
      return;
    }
        
    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }


    // Process

    // handle save for tags
    $values = $form->getValues();
    $tags = preg_split('/[,]+/', $values['keywords']);
    $tags = array_filter(array_map("trim", $tags));

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try
    {

      $folder->setFromArray($values);
      $folder->modified_date = date('Y-m-d H:i:s');

      $folder->tags()->setTagMaps($viewer, $tags);
      $folder->save();

      // Set photo
      if( !empty($values['photo']) ) {
        $folder->setPhoto($form->photo);
      }      

      // Save custom fields
      $customfieldform = $form->getSubForm('fields');
      $customfieldform->setItem($folder);
      $customfieldform->saveValues();

      // CREATE AUTH STUFF HERE
      $values = $form->getValues();
      
      // CREATE AUTH STUFF HERE
      foreach ($auth_keys as $auth_key => $auth_default)
      {
        $auth_value = isset($values['auth_'.$auth_key]) ? $values['auth_'.$auth_key] : $auth_default;
        $authMax = array_search($auth_value, $roles);
          
        foreach( $roles as $i => $role )
        {
          $auth->setAllowed($folder, $role, $auth_key, ($i <= $authMax));
        }
      }
      
      $db->commit();


      $savedChangesNotice = Zend_Registry::get('Zend_Translate')->_("Your changes were saved.");
      $form->addNotice($savedChangesNotice);
      $customfieldform->removeElement('submit');
      
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }


    $db->beginTransaction();
    try {
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach( $actionTable->getActionsByObject($folder) as $action ) {
        $actionTable->resetActivityBindings($action);
      }

      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    // Redirect
    if( $this->_getParam('ref') === 'profile' ) {
      $this->_redirectCustom($folder);
    } else {
      //$this->_redirectCustom(array('route' => 'folder_general', 'action' => 'manage'));
    }
  }

  public function uploadAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
  	$this->view->folder = $folder = Engine_Api::_()->core()->getSubject('folder');
  	
    if( !$this->_helper->requireAuth()->setAuthParams($folder, $viewer, 'edit')->isValid() ) {
      return;
    }
    
    /*
    Engine_Api::_()->getApi('debug', 'radcodes')->log("folder :: uploadAction");
    Engine_Api::_()->getApi('debug', 'radcodes')->log($_REQUEST);
    Engine_Api::_()->getApi('debug', 'radcodes')->log($_FILES);
    */
    
    if( isset($_GET['ul']) || isset($_FILES['Filedata']) ) {
      return $this->_forward('upload-attachment', null, null, array('format' => 'json', 'folder_id'=>(int) $folder->getIdentity()));
    }

    /*
    if( !$this->_helper->requireAuth()->setAuthParams($folder, null, 'attachment')->isValid())
    {
      return $this->_forward('requireauth', 'error', 'core');
    }
    */

    $this->view->folder_id = $folder->folder_id;
    $this->view->form = $form = new Folder_Form_Folder_Upload();
    $form->file->setAttrib('data', array('folder_id' => $folder->getIdentity()));

    //$form->file->setAttrib('allowedExtensions', 'doc, txt, stupid');
    
    if( !$this->getRequest()->isPost() )
    {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $table = Engine_Api::_()->getItemTable('folder_attachment');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $values = $form->getValues();
      $params = array(
        'folder_id' => $folder->getIdentity(),
        'user_id' => $viewer->getIdentity(),
      );

      // Add action and attachments
      /*
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $folder, 'folder_attachment_upload', null, array('count' => count($values['file'])));
			*/
      /*
      // Do other stuff
      $count = 0;
      foreach( $values['file'] as $attachment_id )
      {
        $attachment = Engine_Api::_()->getItem("folder_attachment", $attachment_id);
        if( !($attachment instanceof Core_Model_Item_Abstract) || !$attachment->getIdentity() ) continue;

        $attachment->folder_id = $folder->folder_id;
        $attachment->save();

      }
			*/
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }


    $this->_redirectCustom($folder->getActionHref('manage'));
  }

  public function uploadAttachmentAction()
  {
    Engine_Api::_()->getApi('debug', 'radcodes')->log("folder :: uploadAttachmentAction");
    Engine_Api::_()->getApi('debug', 'radcodes')->log($_REQUEST);
    Engine_Api::_()->getApi('debug', 'radcodes')->log($_FILES);
    
    $folder = Engine_Api::_()->getItem('folder', (int) $this->_getParam('folder_id'));

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
    //$folder

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

    $db = Engine_Api::_()->getDbtable('attachments', 'folder')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = Engine_Api::_()->user()->getViewer();

      /////
      $table = Engine_Api::_()->getItemTable('folder_attachment');
      $attachment = $table->createRow();
      $attachment->setFromArray(array(
        'owner_type' => 'user',
        'owner_id' => $viewer->getIdentity(),
      	'folder_id' => $folder->getIdentity(),
      ));
      $attachment->save();

      $attachment->setAttachment($_FILES['Filedata']);
      ////
      /*
      $params = array(
        // We can set them now since only one folder is allowed
        'collection_id' => $folder->getIdentity(),
        'folder_id' => $folder->getIdentity(),

        'folder_id' => $folder->getIdentity(),
        'user_id' => $viewer->getIdentity(),
      );
      
      $attachment = Engine_Api::_()->folder()->createAttachment($params, $_FILES['Filedata']);
			*/
      
      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->attachment_id = $attachment->attachment_id;

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred. '.$e->getMessage());
      // throw $e;
      return;
    }
  }
  
  
  public function deleteAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->folder = $folder = Engine_Api::_()->core()->getSubject('folder');

    if( !$this->_helper->requireAuth()->setAuthParams($folder, $viewer, 'delete')->isValid()) {
      return;
    }

    $this->view->form = $form = new Folder_Form_Folder_Delete();
    
    if( !$this->getRequest()->isPost() )
    {
      return;
    }
          
    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }    
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try
    {
      $folder->delete();
      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    } 
    
    return $this->_redirectCustom(array('route' => 'folder_general', 'action'=>'manage'));
    
  }

  public function manageAction()
  {
  	$viewer = Engine_Api::_()->user()->getViewer();
    $this->view->folder = $folder = Engine_Api::_()->core()->getSubject();
    
    if( !$this->_helper->requireAuth()->setAuthParams($folder, $viewer, 'edit')->isValid()) {
      return;
    }

    $params = array('limit' => 9999);
    // Prepare data
    $this->view->paginator = $paginator = $folder->getAttachmentPaginator($params);
  }  
  
  protected function _loadNavigations()
  {
    // Get navigation
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('folder_main');

    // Get quick navigation
    $this->view->quickNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('folder_quick');  

    // Get dashboard navigation
    $this->view->dashboardNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('folder_dashboard');   
  }  
  
}