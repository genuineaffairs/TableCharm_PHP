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
 
 
class Folder_IndexController extends Core_Controller_Action_Standard
{
  protected $_navigation;

  public function init()
  {
    if (!Engine_Api::_()->radcodes()->validateLicense('folder')) {
      return $this->_redirectCustom(array('route'=>'radcodes_general', 'action'=>'license', 'type'=>'folder'));
    }
    
    if( !$this->_helper->requireAuth()->setAuthParams('folder', null, 'view')->isValid() ) return;
    
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($folder_id = (int) $this->_getParam('folder_id')) &&
          null !== ($folder = Engine_Api::_()->getItem('folder', $folder_id)) )
      {
        Engine_Api::_()->core()->setSubject($folder);
      }
      else if( 0 !== ($user_id = (int) $this->_getParam('user_id')) &&
          null !== ($user = Engine_Api::_()->getItem('user', $user_id)) )
      {
        Engine_Api::_()->core()->setSubject($user);
      }
    }
    
    $this->_helper->requireUser->addActionRequires(array(
      'manage',
    ));
  }
  
  
  public function indexAction()
  {
    $this->_helper->content->setNoRender()->setEnabled();
  }
  
  // NONE USER SPECIFIC METHODS
  public function browseAction()
  {
    $this->_helper->content->setNoRender()->setEnabled();
  }

  
  public function manageAction()
  {
    $this->_loadNavigations();
    
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->form = $form = new Folder_Form_Filter_Manage();    

    $values = array();
    // Populate form data
    if( $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();
    }

    $values = Engine_Api::_()->getApi('filter','radcodes')->removeKeyEmptyValues($values);
    $this->view->formValues = $values;
            
    $this->view->assign($values);
          
    if (!empty($values['tag']))
    {
      $this->view->tagObject = Engine_Api::_()->getItem('core_tag', $values['tag']);
    }    
    
    if (!empty($values['category']))
    {
      $this->view->categoryObject = Engine_Api::_()->folder()->getCategory($values['category']);  
    }    
    
    $values['limit'] = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('folder.perpage', 10);
    $values['user'] = $viewer;
    
    $this->view->paginator = $paginator = Engine_Api::_()->folder()->getFoldersPaginator($values);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));    

    $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('folder', $viewer, 'create');
  }
  
  public function createAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('folder', null, 'create')->isValid()) return;
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->_loadNavigations();
    
    $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'folder', 'max');
    $this->view->current_count = Engine_Api::_()->folder()->countFolders(array('user'=>$viewer));

    $parent = null;
    $parent_guid = $this->_getParam('parent');
    if (!empty($parent_guid))
    {
      try
      {
        $parent = Engine_Api::_()->getItemByGuid($parent_guid);
		    if (!($parent instanceof Core_Model_Item_Abstract) || !$parent->getIdentity())
		    {
		    	return $this->_forward('requiresubject', 'error', 'core');
		    }
      }
      catch (Exception $e)
      {
      	return $this->_forward('requiresubject', 'error', 'core');
        // silence
      }
    }
    
    //echo 'class='.get_class($parent);
    //echo " ID=".$parent->getIdentity();
    
   
    
    if (!($parent instanceof Core_Model_Item_Abstract) || !$parent->getIdentity())
    {
    	$parent = $viewer;
    }
    
    $enabletypes = trim(Engine_Api::_()->getApi('settings', 'core')->getSetting('folder.enabletypes'));
    $enabletypes = str_replace(' ', '', $enabletypes);
    if (strlen($enabletypes)) {
      $enabletypes = explode(',', $enabletypes);
      if (!in_array($parent->getType(), $enabletypes)) {
        $parent = $viewer;
      }
    } 
    
    $owner = $parent->getOwner('user');
    if (!($owner instanceof User_Model_User) || !$owner->getIdentity()) {
    	$parent = $owner = $viewer;
    }
    
    if( !$owner->isSelf($viewer) )
    {
      return $this->_forward('requireauth', 'error', 'core');
    }    
    
    $this->view->form = $form = new Folder_Form_Folder_Create(array('parent'=>$parent));
    
    if (!$this->getRequest()->isPost())
    {
      return;
    }
    
    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }

    $table = Engine_Api::_()->getItemTable('folder');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {     
      $values = $form->getValues();
      $values['user_id'] = $viewer->getIdentity();

      $values['parent_type'] = $parent->getType();
      $values['parent_id'] = $parent->getIdentity();
      
      $folder = $table->createRow();
      $folder->setFromArray($values);
      $folder->save();
            
      // Add tags
      $tags = preg_split('/[,]+/', $values['keywords']);
      $tags = array_filter(array_map("trim", $tags));
      $folder->tags()->addTagMaps($viewer, $tags);

      $customfieldform = $form->getSubForm('fields');
      $customfieldform->setItem($folder);
      $customfieldform->saveValues();

      // Set photo
      if( !empty($values['photo']) ) 
      {
        $folder->setPhoto($form->photo);
      }      

      // CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;  
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

      $auth_keys = array(
       'view' => 'everyone',
       'comment' => 'registered',
      );
      
      foreach ($auth_keys as $auth_key => $auth_default)
      {
        $auth_value = isset($values['auth_'.$auth_key]) ? $values['auth_'.$auth_key] : $auth_default;
        $authMax = array_search($auth_value, $roles);
        
        foreach( $roles as $i => $role )
        {
          $auth->setAllowed($folder, $role, $auth_key, ($i <= $authMax));
        }
      }

      // Add activity
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $folder, 'folder_new');
      if ($action != null)
      {
        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $folder);
      }

      // Commit
      $db->commit();
      
      $this->_redirectCustom($folder->getActionHref('upload'));
      //$this->_redirectCustom(array('route' => 'folder_specific', 'action' => 'success', 'folder_id' => $folder->getIdentity()));
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

  }

  
  public function tagsAction()
  {
    $this->_helper->content->setNoRender()->setEnabled();
  }


  public function listAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $type = $this->_getParam('type');
    $identity = $this->_getParam('id');
    
    $item = null;
    if( $type && $identity ) {
      $item = Engine_Api::_()->getItem($type, $identity);
    }
    
    if (!($item instanceof Core_Model_Item_Abstract) || !$item->getIdentity()) {
      $this->view->error = 'no subject';
      return;
    }
    
    
    $this->view->item = $item;
    $this->view->is_owner = false;
    $owner = $item->getOwner('user');
    if (!($owner instanceof User_Model_User) || !$owner->getIdentity()) {
      $this->view->error = 'no owner';
      return;
    }
    else {
      $this->view->is_owner = $owner->isSelf($viewer);
    }
    
    $params = array(
      'parent' => $item,
      'search' => 1,
      'limit' => $this->_getParam('max', 5),
      'order' => $this->_getParam('order', 'recent'),
      'period' => $this->_getParam('period'),
      'keyword' => $this->_getParam('keyword'),
      'category' => $this->_getParam('category'),
    );
    
    if ($this->_getParam('featured', 0)) {
      $params['featured'] = 1;
    }
    
    if ($this->_getParam('sponsored', 0)) {
      $params['sponsored'] = 1;
    }
    
    $this->view->paginator = $paginator = Engine_Api::_()->folder()->getFoldersPaginator($params);

    $this->view->showphoto = $this->_getParam('showphoto', 1);
    $this->view->showdetails = $this->_getParam('showdetails', 1); 
    $this->view->showmeta = $this->_getParam('showmeta', 1); 
    $this->view->showdescription = $this->_getParam('showdescription', 1);     
    
  }
  
  protected function _loadNavigations()
  {
    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('folder_main');

    // Get quick navigation
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('folder_quick');    
  }
  
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
    if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) )
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

      $album = Engine_Api::_()->folder()->getSpecialAlbum($viewer, 'folder');
      
      $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
      $photo = $photoTable->createRow();
      $photo->setFromArray(array(
        'owner_type' => 'user',
        'owner_id' => $viewer->getIdentity(),
        'collection_id' => $album->album_id, // for SE <= 4.1.6 .. (this column was removed since v4.1.7
        'album_id' => $album->album_id, // for SE >= v4.1.7
      ));
      $photo->save();

      $photo->setPhoto($_FILES['Filedata']);

      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->photo_id = $photo->photo_id;
      $this->view->photo_url = $photo->getPhotoUrl();

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
      //throw $e;
      return;

    } catch( Exception $e ) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      //throw $e;
      return;
    }
  }
  
  
}

