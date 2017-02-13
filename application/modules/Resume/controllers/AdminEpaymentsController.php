<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @epayment   Application_Extensions
 * @epayment    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 

class Resume_AdminEpaymentsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    if (!Engine_Api::_()->resume()->checkLicense()) {
      return $this->_redirectCustom(array('route'=>'admin_default', 'module'=>'resume', 'controller'=>'settings', 'notice' => 'license'));
    }    
    
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($epayment_id = (int) $this->_getParam('epayment_id')) &&
          null !== ($epayment = Engine_Api::_()->getItem('epayment', $epayment_id)) )
      {
        Engine_Api::_()->core()->setSubject($epayment);
      }
    }

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'view' => 'epayment',
      'edit' => 'epayment',
      'process' => 'epayment',
      'delete' => 'epayment',
    ));
    
    $this->_loadNavigation();
  }
  
  
  public function indexAction()
  { 
    $this->view->form = $form = new Resume_Form_Admin_Epayment_Filter();
    $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module'=>'resume', 'controller'=>'epayments'), 'admin_default', true));
    
    // Process form
    $values = array();
    if ($form->isValid($this->_getAllParams()) ) {
      $values = $form->getValues();
    }
    $values = Engine_Api::_()->resume()->filterEmptyParams($values);
    
    $this->view->formValues = $values;

    $this->view->assign($values);
   
    $values['resource_type'] = 'resume';
    
    $this->view->paginator = Engine_Api::_()->epayment()->getEpaymentsPaginator($values);
    $this->view->paginator->setItemCountPerPage(20);
    $this->view->paginator->setCurrentPageNumber($this->_getParam('page',1));
    $this->view->params = $values;      
  }

  
  public function createAction()
  {
    $this->view->form = $form = new Resume_Form_Admin_Epayment_Create();
    
    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    
    // Process
    $values = $form->getValues();
    
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {      
      $resume = Engine_Api::_()->resume()->getResume($values['resource_id']);
      $epayment = $resume->epayments()->addEpayment($values);
      
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    
    $this->_redirectCustom(array('route' => 'admin_default', 'module'=>'resume', 'controller' => 'epayments', 'action' => 'process', 'epayment_id' => $epayment->getIdentity()));

  }
  

  public function editAction()
  {
    $this->view->epayment = $epayment = Engine_Api::_()->core()->getSubject('epayment');
    
    $this->view->form = $form = new Resume_Form_Admin_Epayment_Edit(array(
      'item' => $epayment
    ));
    
    $form->populate($epayment->toArray());
    
    if( !$this->getRequest()->isPost() ) {
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
      $epayment->setFromArray($values);
      $epayment->save();
      
      $savedChangesNotice = Zend_Registry::get('Zend_Translate')->_("Your changes were saved.");
      $form->addNotice($savedChangesNotice);
      
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

  }

  public function viewAction()
  {
    $this->view->epayment = $epayment = Engine_Api::_()->core()->getSubject('epayment');
  }
  
  public function processAction()
  {
    $this->view->epayment = $epayment = Engine_Api::_()->core()->getSubject('epayment');
    
    $this->view->form = $form = new Resume_Form_Admin_Epayment_Process(array(
      'item' => $epayment
    ));
    
    $form->populate($epayment->toArray());
    
    if( !$this->getRequest()->isPost() ) {
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
      $values['auto_process'] = true;
      Engine_Api::_()->resume()->processEpayment($epayment, $values);
      
      $this->view->form = $form = new Resume_Form_Admin_Epayment_Process(array(
        'item' => $epayment
      ));
      
      $savedChangesNotice = Zend_Registry::get('Zend_Translate')->_("Payment has been processed.");
      $form->addNotice($savedChangesNotice);
      
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
  }
  
  
  public function deleteAction()
  {
    $this->view->epayment = $epayment = Engine_Api::_()->core()->getSubject('epayment');
    
    $this->view->form = $form = new Resume_Form_Admin_Epayment_Delete();
        
    $form->populate($epayment->toArray());
    
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
      // delete the resume epayment in the database
      $epayment->delete();        

      $db->commit();
      
      Engine_Api::_()->core()->clearSubject();
      
      $this->_redirectCustom(array('route' => 'admin_default', 'module'=>'resume', 'controller' => 'epayments', 'action' => 'index'));

    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    
  }

  protected function _loadNavigation()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('resume_admin_main', array(), 'resume_admin_main_epayments');   
      
    if (Engine_Api::_()->core()->hasSubject('epayment'))
    {
      $this->view->gutterNavigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('resume_admin_epayment', array());  
    }  
      
  }
  

}