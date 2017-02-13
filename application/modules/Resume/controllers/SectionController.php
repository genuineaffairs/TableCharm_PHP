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

class Resume_SectionController extends Core_Controller_Action_Standard
{

  public function init()
  {
    if( 0 !== ($section_id = (int) $this->_getParam('section_id')) &&
        null !== ($section = Engine_Api::_()->getItem('resume_section', $section_id)) ) {
      Engine_Api::_()->core()->setSubject($section);
    }

    $this->_helper->requireUser();
    $this->_helper->requireSubject('resume_section');
    
  }

  public function editAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->section = $section = Engine_Api::_()->core()->getSubject();
    
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() ) {
      return;
    }  
    
    $this->view->form = $form = new Resume_Form_Section_Edit();
    
    $form->populate($section->toArray());
    
    // Temporary hack to hide description in text field for Currently Seeking section
    if(strpos($form->getValue('description'), 'Enter details of what you are seeking') !== false) {
      $form->description->setValue('');
    }
    
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
      $section->setFromArray($values);

      $section->save();
            
      $db->commit();

      return $this->_forward('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes were saved.')),
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
  
  
  public function deleteAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->section = $section = Engine_Api::_()->core()->getSubject();
    
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() ) {
      return;
    }  
    
    $this->view->form = $form = new Resume_Form_Section_Delete();
    
    $form->populate($section->toArray());
    
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
      $section->delete();
            
      $db->commit();

      return $this->_forward('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Section deleted successfully.')),
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
  
  public function addChildAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->section = $section = Engine_Api::_()->core()->getSubject();
    
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() ) {
      return;
    }  
    
    if ($section->isChildTypeText()) {
      return $this->_forward('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Invalid section type.')),
        'layout' => 'default-simple',
        'parentRefresh' => true,
        'closeSmoothbox' => true,
      ));
    }
    
    $class = "Resume_Form_{$section->child_type}_Create";
    
    $this->view->form = $form = new $class();
        
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
      $table = $section->getChildTable();
      
      $values['section_id'] = $section->getIdentity();
      
      $child = $table->createRow();
      $child->setFromArray($values);
      $child->save();
            
      $db->commit();

      return $this->_forward('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Added to section.')),
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
  
  
  public function editChildAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->section = $section = Engine_Api::_()->core()->getSubject();
    
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() ) {
      return;
    }  
    
    $child = $section->getChildItem((int) $this->_getParam('child_id'));
    if (!$child || !$child->getParent()->isSelf($section))
    {
      return $this->_forward('requireauth', 'error', 'core');
    }    
    
    $class = "Resume_Form_{$section->child_type}_Edit";
    
    $this->view->form = $form = new $class();
        
    $form->populate($child->toArray());
    
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
      $child->setFromArray($values);
      $child->save();
            
      $db->commit();

      return $this->_forward('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes were saved.')),
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
  
  
  public function deleteChildAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->section = $section = Engine_Api::_()->core()->getSubject();
    
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() ) {
      return;
    }  
    
    $child = $section->getChildItem((int) $this->_getParam('child_id'));
    if (!$child || !$child->getParent()->isSelf($section))
    {
      return $this->_forward('requireauth', 'error', 'core');
    }    
    
    $class = "Resume_Form_{$section->child_type}_Delete";
    
    $this->view->form = $form = new $class();
        
    $form->populate($child->toArray());
    
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
      $child->delete();
            
      $db->commit();

      return $this->_forward('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Deleted successfully.')),
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
  
  public function orderChildrenAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->section = $section = Engine_Api::_()->core()->getSubject();
    
    if (!$this->getRequest()->isPost()) {
      return;
    }
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      $children = $section->getChildItems();
      foreach ($children as $child)
      {
        $child->order = (int) $this->getRequest()->getParam('resume_section_child_'.$child->getIdentity());
        $child->save();
      }
      
      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

  }
  
}
