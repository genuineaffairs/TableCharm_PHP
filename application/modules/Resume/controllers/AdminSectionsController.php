<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @section   Application_Extensions
 * @package    Radcodes
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
class Resume_AdminSectionsController extends Core_Controller_Action_Admin
{

  /**
   * Type of section, ex: module_section, article_section
   * @var $_itemType string
   */
  protected $_itemType = 'resume_section';
  
  /**
   * Module name in proper case, ex: Module, Article
   * @var $_moduleName string
   */
  protected $_moduleName = 'Resume';
  
  /**
   * Module name in lower case, ex: module, article
   * @var $_moduleSpec string
   */
  protected $_moduleSpec = 'resume';
  
  
  protected $_formSpec = array(
    'create' => 'Resume_Form_Admin_Section_Create',
    'edit' => 'Resume_Form_Admin_Section_Edit',
    'delete' => 'Resume_Form_Admin_Section_Delete',
    'photo' => 'Resume_Form_Admin_Section_Photo',
    'move' => 'Resume_Form_Admin_Section_Move',  
  );
  
  public function init()
  {
    if (!Engine_Api::_()->resume()->checkLicense()) {
      return $this->_redirectCustom(array('route'=>'admin_default', 'module'=>'resume', 'controller'=>'settings', 'notice' => 'license'));
    } 
    
    $this->setup();

    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($section_id = (int) $this->_getParam('section_id')) &&
          null !== ($section = $this->getSectionTable()->getSection($section_id)) )
      {
        Engine_Api::_()->core()->setSubject($section);
      }
    }

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'delete' => $this->_itemType,
      'edit' => $this->_itemType,
      'icon' => $this->_itemType,
    ));    
    
    //die(dirname(dirname(__FILE__)) . '/views/scripts');
    //die(get_class($this->view));
    // Hack up the view paths
    /*
    $this->view->addHelperPath(dirname(dirname(__FILE__)) . '/views/helpers', 'Radcodes_View_Helper');
    $this->view->addScriptPath(dirname(dirname(__FILE__)) . '/views/scripts');

    $this->view->addHelperPath(dirname(dirname(dirname(__FILE__))) . DS . $this->_moduleName . '/views/helpers', $this->_moduleName . '_View_Helper');
    $this->view->addScriptPath(dirname(dirname(dirname(__FILE__))) . DS . $this->_moduleName . '/views/scripts');
		*/
          
    $this->view->moduleName = $this->_moduleName;
    $this->view->moduleSpec = $this->_moduleSpec;
    $this->view->itemType = $this->_itemType;        
    
    $this->_loadNavigation();
  }
  
  protected function setup()
  {

    if( !$this->_itemType || !$this->_moduleName || !Engine_APi::_()->hasItemType($this->_itemType) ) {
      throw new Core_Model_Exception('Invalid _itemType or _moduleName');
    }

  }
  
  /**
   * @return Resume_Model_DbTable_Sections
   */
  public function getSectionTable()
  {
    return Engine_Api::_()->getItemTable($this->_itemType);
  }
  
  
  public function indexAction()
  {
    $this->view->sections = $this->getSectionTable()->getCoreSections();
  }
  
  public function iconAction()
  {
    $this->view->section = $section = Engine_Api::_()->core()->getSubject($this->_itemType);
    $this->view->form = $form = $this->getForm('photo');
  }
  
  public function deletePhotoAction()
  {
    $this->view->section = $section = Engine_Api::_()->core()->getSubject($this->_itemType);
    $this->view->form = $form = $this->getForm('photo');
    
    if (!$this->getRequest()->isPost())
    {
      return;
    }
    
    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    
    try
    {
      $section->removePhoto();
      $section->save();
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
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Section photo deleted.'))
    )); 
  }
  
  public function addAction()
  {
    $this->view->form = $form = $this->getForm('create');
    

    if (!$this->getRequest()->isPost())
    {
      return;
    }
    
    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }
    
    // we will add the section
    $values = $form->getValues();
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    
    try
    {
      $values['resume_id'] = 0;
      
      $table = $this->getSectionTable();
      $section = $table->createRow();
      $section->setFromArray($values);
      $section->save();
      
      // Set photo
      if( !empty($values['photo']) ) {
        $section->setPhoto($form->photo);
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
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Section added.'))
    )); 
  }
  
  
  public function editAction()
  {
    $this->view->section = $section = Engine_Api::_()->core()->getSubject($this->_itemType);
    $this->view->form = $form = $this->getForm('edit');

    $form->removeElement('child_type');
    
    $form->populate($section->toArray());
    
    
    if (!$this->getRequest()->isPost())
    {
      return;
    }
    
    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }
    
    $values = $form->getValues();
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    
    try
    {
      if (!empty($values['remove_photo'])) {
        $section->removePhoto();
      }
      
      $section->setFromArray($values);
      $section->save();
      
      // Set photo
      if( !empty($values['photo']) ) {
        $section->removePhoto();
        $section->setPhoto($form->photo);
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
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Section updated.'))
    ));    
    
  }
  
  
  public function deleteAction()
  {
    $this->view->section = $section = Engine_Api::_()->core()->getSubject($this->_itemType);
    $this->view->form = $form = $this->getForm('delete');

    $form->populate($section->toArray());
    
    if (!$this->getRequest()->isPost())
    {
      return;
    }
    
    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }    
    
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      $section->delete();        
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
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Section deleted.'))
    ));
    
  }  
  
  public function moveAction()
  {

    $this->view->form = $form = $this->getForm('move');
        
    if (!$this->getRequest()->isPost())
    {
      return;
    }
    
    if (!$form->isValid($this->getRequest()->getPost()))
    {
      return;
    }     
    
    $values = $form->getValues();
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      $from_section_id = $values['from_section_id'];
      $to_section_id = $values['to_section_id'];
      
      if ($from_section_id != $to_section_id)
      {
        $entriesTable = Engine_Api::_()->getItemTable($this->_moduleSpec);
        $where = $entriesTable->getAdapter()->quoteInto('section_id = ?', $from_section_id);
        $data = array('section_id' => $to_section_id);
        $entriesTable->update($data, $where);
        
        $db->commit();
      }

    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh'=> 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Section entries moved.'))
    ));

  }
  
  public function orderAction()
  {
    if (!$this->getRequest()->isPost()) {
      return;
    }
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      //$parent_id = $this->getRequest()->getParam('parent_id', 0);
      //$sections = $this->getSectionTable()->getChildrenOfParent($parent_id);
      $sections = $this->getSectionTable()->getCoreSections();
      foreach ($sections as $section)
      {
        $section->order = $this->getRequest()->getParam('admin_section_item_'.$section->section_id);
        $section->save();
        //echo "\n".$section->section_id.'='.$section->order;
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
  
  protected function _loadNavigation()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation($this->_moduleSpec . '_admin_main', array(), $this->_moduleSpec . '_admin_main_sections');    
  }
  
  /**
   * @return Engine_Form
   */
  protected function getForm($spec)
  {
    $class = $this->_formSpec[$spec];
    $form = new $class(array('sectionTable'=>$this->getSectionTable()));
    return $form;
  }
}

