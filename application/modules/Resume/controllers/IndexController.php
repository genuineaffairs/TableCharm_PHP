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
 
 
class Resume_IndexController extends Core_Controller_Action_Standard
{
  protected $_navigation;

  public function init()
  {
    if (!Engine_Api::_()->radcodes()->validateLicense('resume')) {
      return $this->_redirectCustom(array('route'=>'radcodes_general', 'action'=>'license', 'type'=>'resume'));
    }
    
    if( !$this->_helper->requireAuth()->setAuthParams('resume', null, 'view')->isValid() ) return;
    
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($resume_id = (int) $this->_getParam('resume_id')) &&
          null !== ($resume = Engine_Api::_()->getItem('resume', $resume_id)) )
      {
        Engine_Api::_()->core()->setSubject($resume);
      }
      else if( 0 !== ($user_id = (int) $this->_getParam('user_id')) &&
          null !== ($user = Engine_Api::_()->getItem('user', $user_id)) )
      {
        Engine_Api::_()->core()->setSubject($user);
      }
    }
    
    $this->_helper->requireUser->addActionRequires(array(
      'create',
      'delete',
      'edit',
      'manage',
      'success',
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'delete' => 'resume',
      'edit' => 'resume',
      'view' => 'resume',
      'sucess' => 'resume',
      'list' => 'user',
      
    ));
  }
  
  
  public function indexAction()
  {
    $this->_helper->content->setNoRender()->setEnabled();
  }
  
  // NONE USER SPECIFIC METHODS
  public function testingAction()
  {
    $this->_helper->content->setNoRender()->setEnabled();
  }

  
  public function browseAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->form = $form = new Resume_Form_Filter_Browse();    

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
      $this->view->categoryObject = Engine_Api::_()->resume()->getCategory($values['category']);  
    }    
    
    if (!empty($values['user']))
    {
      $this->view->userObject = Engine_Api::_()->user()->getUser($values['user']);
    }    
    
    $values['limit'] = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('resume.perpage', 10);
    
    $values = array_merge($values, array(
  	  'live' => true,
  	  'search' => 1,
  	  'limit' => $this->_getParam('max', 10),
  	  'preorder' => (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('resume.preorder', 1),    
    ));
    
    $this->view->paginator = $paginator = Engine_Api::_()->resume()->getResumesPaginator($values);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));    

    $this->_helper->content->setEnabled();    
  }
  
  public function manageAction()
  {   
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->form = $form = new Resume_Form_Filter_Manage();    

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
      $this->view->categoryObject = Engine_Api::_()->resume()->getCategory($values['category']);  
    }    
    
    $values['limit'] = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('resume.perpage', 10);
    $values['user'] = $viewer;
    
    $this->view->paginator = $paginator = Engine_Api::_()->resume()->getResumesPaginator($values);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));    

    $this->_helper->content->setEnabled();
    
  }
  
  public function createAction()
  {
    $this->view->from_app = $from_app = $this->getRequest()->getParam('from_app');
    
    // Hack to eliminate silly errors when posting file from webview of phone app
    if ($from_app == 1 || !Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      if (!array_key_exists('photo', $_FILES)) {
        $_FILES['photo'] = array(
            'name' => '',
            'type' => '',
            'tmp_name' => '',
            'error' => 4,
            'size' => 0
        );
      }
    }
    
    if( !$this->_helper->requireAuth()->setAuthParams('resume', null, 'create')->isValid()) return;
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->_loadNavigations();
    
    $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'resume', 'max');
    $this->view->current_count = Engine_Api::_()->resume()->countResumes(array('user'=>$viewer));

    $package = null;
    if ($package_id = $this->getRequest()->getParam('package'))
    {
      $package = Engine_Api::_()->resume()->getPackage($package_id);
      if ($package instanceof Resume_Model_Package && !$package->enabled) {
        $this->_redirectCustom(array('route' => 'resume_general', 'action' => 'create'));
      }
    }
    
    if (!(Engine_Api::_()->hasModuleBootstrap('zulu') && (Engine_Api::_()->zulu()->isMobileMode() || $this->getRequest()->getParam('from_app')))) {
      $this->_helper->content->setEnabled();
    }
    
    $this->view->form = $form = new Resume_Form_Resume_Create(array('package' => $package));
    
    if (!$this->getRequest()->isPost())
    {
      return;
    }
    
    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }
    
    
    $table = Engine_Api::_()->getItemTable('resume');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {     
      $values = $form->getValues();
      $values['user_id'] = $viewer->getIdentity();
      $values['published'] = 0;
                         
      $resume = $table->createRow();
      $resume->setFromArray($values);
      
      $status = Resume_Model_Resume::STATUS_QUEUED;
      
      $package = $resume->getPackage();
      $resume->featured = $package->featured;
      $resume->sponsored = $package->sponsored;
      
      $resume->updateExpirationDate();
      
      if (!$resume->requiresEpayment())
      {
        if ($package->auto_process)
        {
          $status = Resume_Model_Resume::STATUS_APPROVED;
        }
      }
      else
      {
        // cost $$
      }
      $resume->updateStatus($status);
      
      $resume->save();
      
      $resume->updateLocation();
      
      // Add tags
      $tags = preg_split('/[,]+/', $values['keywords']);
      $tags = array_filter(array_map("trim", $tags));
      $resume->tags()->addTagMaps($viewer, $tags);

      $customfieldform = $form->getSubForm('fields');
      $customfieldform->setItem($resume);
      $customfieldform->saveValues();

      // Set photo
      if( !empty($values['photo']) ) 
      {
        $resume->setPhoto($form->photo);
      }      

      // CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;  
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

      $auth_keys = array(
        'view' => 'everyone',
        'comment' => 'registered',
        'photo' => 'owner',
      );
      
      foreach ($auth_keys as $auth_key => $auth_default)
      {
        $auth_value = isset($values['auth_'.$auth_key]) ? $values['auth_'.$auth_key] : $auth_default;
        $authMax = array_search($auth_value, $roles);
        
        foreach( $roles as $i => $role )
        {
          $auth->setAllowed($resume, $role, $auth_key, ($i <= $authMax));
        }
      }

      $sectionTable = Engine_Api::_()->getItemTable('resume_section');
      $sections = $sectionTable->getCoreSections(array('enabled'=>1));

      foreach ($sections as $section) {
        $default_in_categories = json_decode($section->default_in_categories, true);
        // Only insert sections related to participation level
        if (is_array($default_in_categories) && in_array($resume->category_id, $default_in_categories)) {
          $data = $section->toArray();
          unset($data['section_id']);
          $new_section = $sectionTable->createRow();
          $new_section->setFromArray($data);
          $new_section->resume_id = $resume->getIdentity();
          $new_section->photo_id = 0;
          $new_section->save();
        }
      }

      // If no section was inserted, then insert player's sections by default
      if (count($resume->getSections()) === 0) {
        $player_category_id = Resume_Model_DbTable_Categories::PLAYER_CATEGORY_ID;
        foreach ($sections as $section) {
          $default_in_categories = json_decode($section->default_in_categories, true);
          if (is_array($default_in_categories) && in_array($player_category_id, $default_in_categories)) {
            $data = $section->toArray();
            unset($data['section_id']);
            $new_section = $sectionTable->createRow();
            $new_section->setFromArray($data);
            $new_section->resume_id = $resume->getIdentity();
            $new_section->photo_id = 0;
            $new_section->save();
          }
        }
      }

      if ($resume->isApprovedStatus())
      {
        // Add activity
        /*
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $resume, 'resume_new');
        if ($action != null)
        {
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $resume);
        }
        */
      }
      // Commit
      $db->commit();
      
      if ($from_app == 1) {
        $params = array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('CV Profiler created.')),
        );
        return $this->_forwardCustom('success', 'utility', 'core', $params);
      }
      
      $this->_redirectCustom(array('route' => 'resume_specific', 'action' => 'sections', 'resume_id' => $resume->getIdentity()));
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    
  }

  
  public function tagsAction()
  {
    $this->_helper->content->setEnabled(); 
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->form = $form = new Resume_Form_Filter_Browse();
    
    $this->view->tags = $tags = Engine_Api::_()->resume()->getPopularTags(array('limit' => 999, 'order' => 'text'));
  }


  protected function _loadNavigations()
  {
    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('resume_main');

    // Get quick navigation
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('resume_quick');    
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

      $album = Engine_Api::_()->resume()->getSpecialAlbum($viewer, 'resume');
      
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
  
  protected function _forwardCustom($action, $controller = null, $module = null, array $params = null) {
    // Parent
    $request = $this->getRequest();

    if (null !== $params) {
      $request->setParams($params);
    }

    if (null !== $controller) {
      $request->setControllerName($controller);

      // Module should only be reset if controller has been specified
      if (null !== $module) {
        $request->setModuleName($module);
      }
    }

    $request->setActionName($action);
    if (Engine_API::_()->seaocore()->isSiteMobileModeEnabled()) {
      $sr_response = Engine_Api::_()->sitemobile()->setupRequest($request);
    }
    $request->setDispatched(false);
  }
  
  
}

