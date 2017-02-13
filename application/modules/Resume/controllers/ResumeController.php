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

class Resume_ResumeController extends Core_Controller_Action_Standard
{

  public function init()
  {
    if( 0 !== ($resume_id = (int) $this->_getParam('resume_id')) &&
        null !== ($resume = Engine_Api::_()->getItem('resume', $resume_id)) ) {
      Engine_Api::_()->core()->setSubject($resume);
    }

    $this->_helper->requireUser();
    $this->_helper->requireSubject('resume');
    
    $this->_loadNavigations();
  }

  // tested
  public function successAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject();
    
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() ) {
      return;
    }    
       
    $this->_initContentPage();
    /*
    if (!$resume->getOwner()->isSelf($viewer))
    {
      return $this->_forward('requireauth', 'error', 'core');
    }
    */
  }  
  
  public function _initContentPage()
  {
    $request = $this->getRequest();
    if (Engine_Api::_()->hasModuleBootstrap('zulu') && (Engine_Api::_()->zulu()->isMobileMode() || $request->getParam('from_app'))) {
      return;
    }

    // Render
    $this->_helper->content
        ->setContentName('resume_resume_edit')
        ->setEnabled()
        ; 
  }
  
  // tested
  public function editAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() ) {
      return;
    }
    
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

    $this->_initContentPage();
     
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject();
    
      // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('resume_main');

    $this->view->form = $form = new Resume_Form_Resume_Edit(array(
      'item' => $resume
    ));

    $form->populate($resume->toArray());

    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
    $auth_keys = array(
      'view' => 'everyone',
      'comment' => 'registered',
      'photo' => 'owner',
    );
    
    // Save resume entry
    if( !$this->getRequest()->isPost() )
    {     
      foreach ($auth_keys as $auth_key => $auth_default)
      {
        $auth_field = 'auth_'.$auth_key;
        
        foreach( $roles as $i => $role )
        {
          if (isset($form->$auth_field->options[$role]) && 1 === $auth->isAllowed($resume, $role, $auth_key))
          {
            $form->$auth_field->setValue($role);
          }
        }
      }
      
      return;
    }
        
    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      //$form->setHeaderFields();
      return;
    }
    //$form->setHeaderFields();


    // Process

    // handle save for tags
    $values = $form->getValues();
    $tags = preg_split('/[,]+/', $values['keywords']);
    $tags = array_filter(array_map("trim", $tags));

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try
    {

      $resume->setFromArray($values);
      $resume->modified_date = date('Y-m-d H:i:s');

      $resume->tags()->setTagMaps($viewer, $tags);
      $resume->save();

      // Set photo
      if( !empty($values['photo']) ) {
        $resume->setPhoto($form->photo);
      }      

      // Save custom fields
      $customfieldform = $form->getSubForm('fields');
      $customfieldform->setItem($resume);
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
          $auth->setAllowed($resume, $role, $auth_key, ($i <= $authMax));
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
      foreach( $actionTable->getActionsByObject($resume) as $action ) {
        $actionTable->resetActivityBindings($action);
      }

      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    
    if ($from_app == 1) {
      $params = array(
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('CV Profiler updated.')),
      );
      return $this->_forwardCustom('success', 'utility', 'core', $params);
    }

    // Redirect
    if( $this->_getParam('ref') === 'profile' ) {
      $this->_redirectCustom($resume);
    } else {
      //$this->_redirectCustom(array('route' => 'resume_general', 'action' => 'manage'));
    }
  }

  public function publishAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject();
    
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() ) {
      return;
    }
    
    if ($resume->published) {
      if( $this->_helper->contextSwitch->getCurrentContext() == "smoothbox" ) {
        return $this->_forward('success', 'utility', 'core', array(
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Resume had been published.')),
          'layout' => 'default-simple',
          'parentRefresh' => true,
          'closeSmoothbox' => true,
        ));
      }
      else {
        $this->_redirectCustom($resume);
      }     
    }
    
    $this->view->form = $form = new Resume_Form_Resume_Publish();
    
    if( !$this->getRequest()->isPost() )
    {
      return;
    }
          
    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }    
    
    
    $values = $form->getValues();

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try
    {
      $resume->published = 1;
      $resume->save();

      Engine_Api::_()->resume()->pushNewPostActivity($resume);
      
      $db->commit();
    
      return $this->_forward('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Resume published successfully.')),
        'layout' => 'default-simple',
        'parentRefresh' => true,
        'closeSmoothbox' => true,
      ));
      
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }     
    
  }
  
  public function sectionsAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject();
    
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() ) {
      return;
    }
    
    $this->_initContentPage();
  }
  
  
  public function addSectionAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject();
    
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() ) {
      return;
    }
    
    $this->view->form = $form = new Resume_Form_Section_Create();

    if( !$this->getRequest()->isPost() )
    {
      return;
    }
          
    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }
    
    $values = $form->getValues();

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try
    {
      $sectionTable = Engine_Api::_()->getItemTable('resume_section');
      
      $section = $sectionTable->getSection($values['section_id']);

      $data = $section->toArray();
      unset($data['section_id']);
      $new_section = $sectionTable->createRow();
      $new_section->setFromArray($data);
      $new_section->resume_id = $resume->getIdentity();
      $new_section->photo_id = 0;
      $new_section->save();

      $db->commit();
    
      return $this->_forward('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Section added to your resume.')),
        'layout' => 'default-simple',
        'parentRefresh' => true,
        'closeSmoothbox' => true,
      ));
      
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    } 

  }
  
  
  // tested
  public function locationAction()
  {
    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject('resume');
    
    if( !$this->_helper->requireAuth()->setAuthParams($resume, null, 'edit')->isValid() ) return;
    
    $this->_initContentPage();
    
    //$this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    
    $this->view->location = $location = $resume->getLocation();
    
    $this->view->form = $form = new Resume_Form_Resume_Location(array(
      'item' => $resume
    ));
    
    $form->populate($location->toArray());
    
    if( !$this->getRequest()->isPost() )
    {
      return;
    }
          
    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }
    
    // handle save for tags
    $values = $form->getValues();

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try
    {
      $location->setFromArray($values);

      $location->save();
            
      $db->commit();

      $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    } 
  }
  
  // tested
  public function deleteAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject('resume');

    if( !$this->_helper->requireAuth()->setAuthParams($resume, null, 'delete')->isValid()) {
      return;
    }

    $this->_initContentPage();
    
    $this->view->form = $form = new Resume_Form_Resume_Delete();
    
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
      $resume->delete();
      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    } 
    
    return $this->_redirectCustom(array('route' => 'resume_general', 'action'=>'manage'));
    
  }

  // tested
  public function styleAction()
  {
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() )
        return;
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'style')->isValid() )
        return;

    $this->_initContentPage();    
        
    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject('resume');

    // Make form
    $this->view->form = $form = new Resume_Form_Resume_Style();

    // Get current row
    $table = Engine_Api::_()->getDbtable('styles', 'core');
    $select = $table->select()
            ->where('type = ?', $resume->getType())
            ->where('id = ?', $resume->getIdentity())
            ->limit(1);

    $row = $table->fetchRow($select);

    // Check post
    if( !$this->getRequest()->isPost() ) {
      $form->populate(array(
        'style' => ( null === $row ? '' : $row->style )
      ));
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $db = $resume->getTable()->getAdapter();
    $db->beginTransaction();

    try {
	    // Cool! Process
	    $style = $form->getValue('style');
	
	    // Save
	    if( null == $row ) {
	      $row = $table->createRow();
	      $row->type = $resume->getType();
	      $row->id = $resume->getIdentity();
	    }
	
	    $row->style = $style;
	    $row->save();

      $db->commit();
      
      $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }    

  }

  // tested
  public function paymentsAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject();
    
    if (!$resume->getOwner()->isSelf($viewer))
    {
      return $this->_forward('requireauth', 'error', 'core');
    } 
    
    $this->_initContentPage();
  	$this->view->paginator = $resume->epayments()->getEpaymentPaginator();
  }
  
  // tested
  public function packagesAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject();
    
    if (!$resume->getOwner()->isSelf($viewer))
    {
      return $this->_forward('requireauth', 'error', 'core');
    }   
    
    $this->_initContentPage();
    $this->view->packages = $packages = Engine_Api::_()->resume()->getPackages(array('enabled'=>1));
  }
  
  // tested
  public function checkoutAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject();
    
    if (!$resume->getOwner()->isSelf($viewer))
    {
      return $this->_forward('requireauth', 'error', 'core');
    }  
    
    $package = null;
    if ($this->_hasParam('package_id'))
    {
      $package = Engine_Api::_()->resume()->getPackage($this->_getParam('package_id'));
    }
    
    if (!($package instanceof Resume_Model_Package))
    {
      $package = $resume->getPackage();
    }
    
    // same package | renew
    if ($package->isSelf($resume->getPackage()))
    {
      // free - can't checkout | upgrade
      if ($package->isFree())
      {
        return $this->_redirectCustom($resume->getActionHref('packages'));
      }
      // paid
      else 
      {
        $recentEpayment = $resume->getRecentEpayment();
        if ($recentEpayment instanceof Epayment_Model_Epayment)
        {
          if ($package->allow_renew)
          {
            $form = new Resume_Form_Resume_Renew(array('item'=>$resume));
          }
          else 
          {
            return $this->_redirectCustom($resume->getActionHref('packages'));
          }
        }
        else 
        {
          $form = new Resume_Form_Resume_Checkout(array('item'=>$resume));
        }
      }
      
    }
    // upgrade | change package
    else 
    {
      // free - can't checkout | upgrade
      if ($package->isFree())
      {
        return $this->_redirectCustom($resume->getActionHref('packages'));
      }
      // paid
      else 
      {
        if ($package->allow_upgrade)
        {
          $form = new Resume_Form_Resume_Upgrade(array('item'=>$resume, 'package'=>$package));
        }
        else 
        {
          return $this->_redirectCustom($resume->getActionHref('packages'));
        }
      }
    }
    
    $this->_initContentPage();
    
    $this->view->form = $form; 
   
  }
  
  
  public function renewAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject();
    
    if (!$resume->getOwner()->isSelf($viewer))
    {
      return $this->_forward('requireauth', 'error', 'core');
    }      
    
    $this->view->package = $package = $resume->getPackage();
    
    if ($package->isFree() || !$package->allow_renew)
    {
      return $this->_redirectCustom($resume->getActionHref('packages'));
    }
    
    $this->_initContentPage();
    
    $this->view->form = $form = new Resume_Form_Resume_Renew(array('item'=>$resume)); 
  }
  
  
  public function upgradeAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject();
    
    if (!$resume->getOwner()->isSelf($viewer))
    {
      return $this->_forward('requireauth', 'error', 'core');
    }    
    
    $this->view->package = $package = Engine_Api::_()->resume()->getPackage($this->_getParam('package_id'));
    
    if (!($package instanceof Resume_Model_Package) 
      || $package->isFree() 
      || !$package->allow_upgrade
    )
    {
      return $this->_redirectCustom($resume->getActionHref('packages'));
    }
    
    if ($package->isSelf($resume->getPackage()))
    {
      return $this->_redirectCustom($resume->getActionHref('renew'));
    }
    
    $this->_initContentPage();
    
    $this->view->form = $form = new Resume_Form_Resume_Upgrade(array('item'=>$resume, 'package'=>$package));
  }
  
  
  public function paymentCancelAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject();
    
    if (!$resume->getOwner()->isSelf($viewer))
    {
      return $this->_forward('requireauth', 'error', 'core');
    }
  }
  
  
  public function paymentSuccessAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject();
    
    if (!$resume->getOwner()->isSelf($viewer))
    {
      return $this->_forward('requireauth', 'error', 'core');
    }     
  }
  
  
  public function orderSectionsAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject();    
    
    if (!$this->getRequest()->isPost()) {
      return;
    }
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      $sections = $resume->getSections();
      foreach ($sections as $section)
      {
        $section->order = (int) $this->getRequest()->getParam('resume_section_'.$section->section_id);
        $section->save();
      }
      
      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    
    return;
  }   
  
  
  protected function _loadNavigations()
  {
    // Get navigation
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('resume_main');

    // Get quick navigation
    $this->view->quickNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('resume_quick');  

    // Get dashboard navigation
    $this->view->dashboardNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('resume_dashboard');   
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