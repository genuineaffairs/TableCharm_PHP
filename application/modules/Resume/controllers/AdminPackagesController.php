<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @package   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 

class Resume_AdminPackagesController extends Core_Controller_Action_Admin
{
  public function init()
  {
    if (!Engine_Api::_()->resume()->checkLicense()) {
      return $this->_redirectCustom(array('route'=>'admin_default', 'module'=>'resume', 'controller'=>'settings', 'notice' => 'license'));
    }     
    
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($package_id = (int) $this->_getParam('package_id')) &&
          null !== ($package = Engine_Api::_()->getItem('resume_package', $package_id)) )
      {
        Engine_Api::_()->core()->setSubject($package);
      }
    }

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'delete' => 'resume_package',
      'edit' => 'resume_package',
      'icon' => 'resume_package',
      'delete-photo' => 'resume_package',
    ));
    
    $this->_loadNavigation();
  }
  
  
  public function indexAction()
  {
    $this->view->packages = Engine_Api::_()->resume()->getPackages();
  }

  
  public function createAction()
  {
    $this->view->form = $form = new Resume_Form_Admin_Package_Create();
    
    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    
    // Process
    $values = $form->getValues();

    $tmp = $values['duration'];
    unset($values['duration']);
    if( empty($tmp) || !is_array($tmp) ) {
      $tmp = array(null, null);
    }
    $values['duration'] = (int) $tmp[0];
    $values['duration_type'] = $tmp[1];    
    

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {      
      $table = Engine_Api::_()->getDbtable('packages', 'resume');
      $package = $table->createRow();
      $package->setFromArray($values);
      $package->save();

      // Set photo
      if( !empty($values['photo']) ) {
        $package->setPhoto($form->photo);
      }
      
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    
    $this->_redirectCustom(array('route' => 'admin_default', 'module'=>'resume', 'controller' => 'packages'));

  }
  

  public function editAction()
  {
    $this->view->package = $package = Engine_Api::_()->core()->getSubject('resume_package');
    
    $this->view->form = $form = new Resume_Form_Admin_Package_Edit(array(
      'item' => $package
    ));
    
    
    // Populate form
    $values = $package->toArray();
   
    $values['duration'] = array($values['duration'], $values['duration_type']);
    unset($values['duration_type']);

    $otherValues = array(
      'duration' => $values['duration'],
    );
    
    $form->populate($values);
    
    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    
    // Hack em up
    //$form->populate($otherValues);

    // Process
    $values = $form->getValues();
    $tmp = $values['duration'];
    unset($values['duration']);
    if( empty($tmp) || !is_array($tmp) ) {
      $tmp = array(null, null);
    }
    $values['duration'] = (int) $tmp[0];
    $values['duration_type'] = $tmp[1];    
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      $package->setFromArray($values);
      $package->save();

      // Set photo
      if( !empty($values['photo']) ) {
        $package->removePhoto();
        $package->setPhoto($form->photo);
      }
      
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    
    $this->_redirectCustom(array('route' => 'admin_default', 'module'=>'resume', 'controller' => 'packages'));
  }

  
  public function deleteAction()
  {
    $this->view->package = $package = Engine_Api::_()->core()->getSubject('resume_package');
    
    $this->view->form = $form = new Resume_Form_Admin_Package_Delete();
    
    $form->populate($package->toArray());
    
    $this->view->can_delete = $package->getResumeCount() == 0 && $package->getEpaymentCount() == 0;
    
    
    // Check method/data
    if( !$this->getRequest()->isPost() || !$this->view->can_delete) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }    

    
    $values = $form->getValues();
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      // delete the resume package in the database
      $package->delete();        

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
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Resume package deleted.'))
    ));

    
  }
/*
  public function moveAction()
  {

    $form = $this->view->form = new Resume_Form_Admin_Package_Move();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
        
    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()))
    {
      $values = $form->getValues();
      
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $from_package_id = $values['from_package_id'];
        $to_package_id = $values['to_package_id'];
        
        // go through logs and see which resume used this package and set it to NEW CATEGORY
        $resumeTable = $this->_helper->api()->getDbtable('resumes', 'resume');
        $where = $resumeTable->getAdapter()->quoteInto('package_id = ?', $from_package_id);
        $data = array('package_id' => $to_package_id);
        $resumeTable->update($data, $where);
        
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
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Resume package moved.'))
      ));
    }

  }
  */
  
  public function orderAction()
  {
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      $packages = Engine_Api::_()->resume()->getPackages();
      foreach ($packages as $package)
      {
        $package->order = $this->getRequest()->getParam('admin_package_item_'.$package->getIdentity());
        $package->save();
        //echo "\n".$package->package_id.'='.$package->order;
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
  
  public function iconAction()
  {
    $this->view->package = $package = Engine_Api::_()->core()->getSubject('resume_package');
  }
  
  public function deletePhotoAction()
  {
    $this->view->package = $package = Engine_Api::_()->core()->getSubject('resume_package');
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      $package->removePhoto();        
      $package->save();
      
      $db->commit();
      
      Engine_Api::_()->core()->clearSubject();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    
    $this->_redirectCustom(array('route' => 'admin_default', 'module'=>'resume', 'controller' => 'packages'));
    
    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh'=> 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Resume package photo delete.'))
    ));    
    
    
    
  }
  
  protected function _loadNavigation()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('resume_admin_main', array(), 'resume_admin_main_packages');     
  }
}