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
 
 
class Resume_AdminManageController extends Core_Controller_Action_Admin
{
  
  public function init()
  {
    if (!Engine_Api::_()->resume()->checkLicense()) {
      return $this->_redirectCustom(array('route'=>'admin_default', 'module'=>'resume', 'controller'=>'settings', 'notice' => 'license'));
    }     
    
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($resume_id = (int) $this->_getParam('resume_id')) &&
          null !== ($resume = Engine_Api::_()->getItem('resume', $resume_id)) )
      {
        Engine_Api::_()->core()->setSubject($resume);
      }
    }

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'featured' => 'resume',
      'sponsored' => 'resume',
      'delete' => 'resume',
      'update-status' => 'resume',
      'update-expiration' => 'resume',
      'update-package' => 'resume',
    ));
  }
  
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('resume_admin_main', array(), 'resume_admin_main_manage');

      
    $this->view->formFilter = $formFilter = new Resume_Form_Admin_Manage_Filter();

    // Process form
    $values = array();
    if($formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    }
    $values = Engine_Api::_()->resume()->filterEmptyParams($values);
    
    $this->view->formValues = $values;

    $this->view->assign($values);
   
    $this->view->paginator = Engine_Api::_()->resume()->getResumesPaginator($values);
    $this->view->paginator->setItemCountPerPage(20);
    $this->view->paginator->setCurrentPageNumber($this->_getParam('page',1));
    $this->view->params = $values;
  }
  
  
  public function updateStatusAction()
  {
    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject('resume');
    
    $this->view->form = $form = new Resume_Form_Admin_Resume_Status();
    
    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      
      $values = array(
        'status' => $resume->status,
        'status_date' => Engine_Api::_()->resume()->serverToLocalTime($resume->status_date)
      );
      
      $form->populate($values);
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }    
    

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    { 
      $values = $form->getValues();
      
      if ($values['status_settings'])
      {
        $values['status_date'] = Engine_Api::_()->resume()->localToServerTime($values['status_date']);
      }
      else 
      {
        $values['status_date'] = date('Y-m-d H:i:s');
      }
      
      $current_resume_status = $resume->status;
      
      $resume->setFromArray($values);
      $resume->save();

      if ($resume->isApprovedStatus())
      {
        Engine_Api::_()->resume()->pushNewPostActivity($resume);
      }
      
      if ($current_resume_status != $resume->status)
      {
        Engine_Api::_()->resume()->pushStatusUpdateNotification($resume);
      }
      
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh'=> 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved'))
    ));

  }
  
  
  public function updateExpirationAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject('resume');
    
    $this->view->form = $form = new Resume_Form_Admin_Resume_Expiration();
    
    
    
    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      
      $values = array(
        'expiration_settings' => $resume->expiration_settings,
        'expiration_date' => $resume->expiration_date,
      );
      
      if ($values['expiration_settings']) {
        $values['expiration_date'] = Engine_Api::_()->resume()->serverToLocalTime($values['expiration_date']);
      }
      
      $form->populate($values);
      
      return;
    }

    $values = $this->getRequest()->getPost();
    
    //$values = $form->getValues();
    
    if ($values['expiration_settings']) {
      $form->expiration_date->setRequired(true);
      $form->expiration_date->setAllowEmpty(false);
    }
    else {
      $form->expiration_date->setIgnore(true);
      $form->expiration_date->setRequired(false);
      $form->expiration_date->setAllowEmpty(true);
      
      unset($values['expiration_date']);
    }    
    
    if( !$form->isValid($values) ) {
      return;
    }    
    

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    { 
      //print_r($values);
      
      $values = $form->getValues();
      
      if ($values['expiration_settings']) {
        $values['expiration_date'] = Engine_Api::_()->resume()->localToServerTime($values['expiration_date']);
      }
      else {
        $values['expiration_date'] = '0000-00-00 00:00:00';
      }

      
      //print_r($values);
      $resume->setFromArray($values);
      $resume->save();

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh'=> 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved'))
    ));
    
  }
  
  
  public function updatePackageAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject('resume');
    
    $this->view->form = $form = new Resume_Form_Admin_Resume_Package();
    
    $form->populate($resume->toArray());
    
    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }    
    

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    { 
      //print_r($values);
      
      $values = $form->getValues();
      
      $package = Engine_Api::_()->resume()->getPackage($values['package_id']);
      
      if ($values['featured_update'])
      {
        $values['featured'] = $package->featured ? 1 : 0;
      }
      
      if ($values['sponsored_update'])
      {
        $values['sponsored'] = $package->sponsored ? 1 : 0;
      }
      
      if ($values['expiration_update'])
      {
        $start_date = $values['expiration_update'] == 2 ? strtotime($resume->status_date) : null;
        $resume->updateExpirationDate($package, $start_date);
      }
      
      //print_r($values);
      $resume->setFromArray($values);
      $resume->save();

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh'=> 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved'))
    ));
    
  }
  
  
  public function featuredAction()
  {
    // In smoothbox
    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject('resume');
    
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {       
        $resume->featured = $this->_getParam('featured') == 'yes' ? 1 : 0;
        $resume->save();
        
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved'))
      ));
    }
  }
  
  public function sponsoredAction()
  {
    // In smoothbox
    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject('resume');
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $resume->sponsored = $this->_getParam('sponsored') == 'yes' ? 1 : 0;
        $resume->save();
        
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved'))
      ));
    }
  }
  
  
  public function deleteAction()
  {
    // In smoothbox
    $this->view->resume = $resume = Engine_Api::_()->core()->getSubject('resume');
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();


      try
      {
        $resume->delete();
        $db->commit();
        
        Engine_Api::_()->core()->clearSubject();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }
  }

  public function deleteselectedAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('resume_admin_main', array(), 'resume_admin_main_manage');
          
    $this->view->ids = $ids = $this->_getParam('ids', null);
    $confirm = $this->_getParam('confirm', false);
    $this->view->count = count(explode(",", $ids));

    // Save values
    if( $this->getRequest()->isPost() && $confirm == true )
    {
      $ids_array = explode(",", $ids);
      foreach( $ids_array as $id ){
        $resume = Engine_Api::_()->getItem('resume', $id);
        if( $resume ) $resume->delete();
      }

      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }  
  
}