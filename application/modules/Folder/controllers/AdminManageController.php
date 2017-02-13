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
 
 
class Folder_AdminManageController extends Core_Controller_Action_Admin
{
  
  public function init()
  {
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($folder_id = (int) $this->_getParam('folder_id')) &&
          null !== ($folder = Engine_Api::_()->getItem('folder', $folder_id)) )
      {
        Engine_Api::_()->core()->setSubject($folder);
      }
    }

    $this->_helper->requireSubject->setActionRequireTypes(array(
      'featured' => 'folder',
      'sponsored' => 'folder',
      'delete' => 'folder',
    ));
  }
  
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('folder_admin_main', array(), 'folder_admin_main_manage');

      
    $this->view->formFilter = $formFilter = new Folder_Form_Admin_Manage_Filter();

    // Process form
    $values = array();
    if($formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    }
    $values = Engine_Api::_()->folder()->filterEmptyParams($values);
    
    $this->view->formValues = $values;

    $this->view->assign($values);
   
    $this->view->paginator = Engine_Api::_()->folder()->getFoldersPaginator($values);
    $this->view->paginator->setItemCountPerPage(20);
    $this->view->paginator->setCurrentPageNumber($this->_getParam('page',1));
    $this->view->params = $values;
  }
  
  
  public function featuredAction()
  {
    // In smoothbox
    $this->view->folder = $folder = Engine_Api::_()->core()->getSubject('folder');
    
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {       
        $folder->featured = $this->_getParam('featured') == 'yes' ? 1 : 0;
        $folder->save();
        
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
    $this->view->folder = $folder = Engine_Api::_()->core()->getSubject('folder');
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $folder->sponsored = $this->_getParam('sponsored') == 'yes' ? 1 : 0;
        $folder->save();
        
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
    $this->view->folder = $folder = Engine_Api::_()->core()->getSubject('folder');
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();


      try
      {
        $folder->delete();
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
      ->getNavigation('folder_admin_main', array(), 'folder_admin_main_manage');
          
    $this->view->ids = $ids = $this->_getParam('ids', null);
    $confirm = $this->_getParam('confirm', false);
    $this->view->count = count(explode(",", $ids));

    // Save values
    if( $this->getRequest()->isPost() && $confirm == true )
    {
      $ids_array = explode(",", $ids);
      foreach( $ids_array as $id ){
        $folder = Engine_Api::_()->getItem('folder', $id);
        if( $folder ) $folder->delete();
      }

      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }  
  
}