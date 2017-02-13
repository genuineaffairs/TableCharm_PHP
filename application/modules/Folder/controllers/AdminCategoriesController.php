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
 

class Folder_AdminCategoriesController extends Core_Controller_Action_Admin
{
  public function init()
  {
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($category_id = (int) $this->_getParam('category_id')) &&
          null !== ($category = Engine_Api::_()->getItem('folder_category', $category_id)) )
      {
        Engine_Api::_()->core()->setSubject($category);
      }
    }

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'delete' => 'folder_category',
      'edit' => 'folder_category',
      'icon' => 'folder_category',
      'delete-photo' => 'folder_category',
    ));
  }
  
  
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('folder_admin_main', array(), 'folder_admin_main_categories');

    $this->view->categories = Engine_Api::_()->folder()->getCategories();
  }

  
  public function addAction()
  {
    $this->view->form = $form = new Folder_Form_Admin_Category_Create();
    
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      // we will add the category
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $values['user_id'] = 0;
        
        $table = Engine_Api::_()->getDbtable('categories', 'folder');
        $category = $table->createRow();
        $category->setFromArray($values);
        $category->save();

        // Set photo
        if( !empty($values['photo']) ) {
          $category->setPhoto($form->photo);
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
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Folder category added'))
      ));
    }
  }
  

  public function editAction()
  {
    $this->view->category = $category = Engine_Api::_()->core()->getSubject('folder_category');
    
    $this->view->form = $form = new Folder_Form_Admin_Category_Edit(array(
      'item' => $category
    ));
    
    $form->populate($category->toArray());
    
    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $category->setFromArray($values);
        $category->save();

        // Set photo
        if( !empty($values['photo']) ) {
          $category->removePhoto();
          $category->setPhoto($form->photo);
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
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Folder category updated.'))
      ));
    }
  }

  
  public function deleteAction()
  {
    $this->view->category = $category = Engine_Api::_()->core()->getSubject('folder_category');
    
    $this->view->form = $form = new Folder_Form_Admin_Category_Delete(array(
      'item' => $category
    ));
    
    $form->populate($category->toArray());
    
      // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()))
    {
      $values = $form->getValues();
      
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $new_category_id = $values['new_category_id'] ? $values['new_category_id'] : 0;

        $folderTable = $this->_helper->api()->getDbtable('folders', 'folder');
        $where = $folderTable->getAdapter()->quoteInto('category_id = ?', $category->category_id);
        $data = array('category_id' => $new_category_id);
        $folderTable->update($data, $where);

        // delete the folder category in the database
        $category->delete();        

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
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Folder category deleted.'))
      ));
    }
    
  }

  public function moveAction()
  {

    $form = $this->view->form = new Folder_Form_Admin_Category_Move();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
        
    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()))
    {
      $values = $form->getValues();
      
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $from_category_id = $values['from_category_id'];
        $to_category_id = $values['to_category_id'];
        
        // go through logs and see which folder used this category and set it to NEW CATEGORY
        $folderTable = $this->_helper->api()->getDbtable('folders', 'folder');
        $where = $folderTable->getAdapter()->quoteInto('category_id = ?', $from_category_id);
        $data = array('category_id' => $to_category_id);
        $folderTable->update($data, $where);
        
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
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Folder category moved.'))
      ));
    }

  }
  
  public function orderAction()
  {
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      $categories = Engine_Api::_()->folder()->getCategories();
      foreach ($categories as $category)
      {
        $category->order = $this->getRequest()->getParam('admin_category_item_'.$category->category_id);
        $category->save();
        //echo "\n".$category->category_id.'='.$category->order;
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
    $this->view->category = $category = Engine_Api::_()->core()->getSubject('folder_category');
  }
  
  public function deletePhotoAction()
  {
    $this->view->category = $category = Engine_Api::_()->core()->getSubject('folder_category');
    
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try
    {
      $category->removePhoto();        
      $category->save();
      
      $db->commit();
      
      Engine_Api::_()->core()->clearSubject();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    
    $this->_redirectCustom(array('route' => 'admin_default', 'module'=>'folder', 'controller' => 'categories'));
    
    $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh'=> 10,
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Folder category photo delete.'))
    ));    
    
  }  
  
}