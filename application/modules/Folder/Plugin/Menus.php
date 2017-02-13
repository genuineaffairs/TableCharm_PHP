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

class Folder_Plugin_Menus
{

  public function canCreateFolders()
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }

    // Must be able to create folders
    if( !Engine_Api::_()->authorization()->isAllowed('folder', $viewer, 'create') ) {
      return false;
    }

    return true;
  }

  public function canViewFolders()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    // Must be able to view folders
    if( !Engine_Api::_()->authorization()->isAllowed('folder', $viewer, 'view') ) {
      return false;
    }

    return true;
  }  
  
  public function canSaveFolders()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }    
    
    // Must be able to view folders
    if( !Engine_Api::_()->authorization()->isAllowed('folder', $viewer, 'view') ) {
      return false;
    }

    return true;
  }  
  

  public function onMenuInitialize_FolderGutterCreate($row)
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }

    // Must be able to create folders
    if( !Engine_Api::_()->authorization()->isAllowed('folder', $viewer, 'create') ) {
      return false;
    }

    //return false;
    
    return true;
  }

  public function onMenuInitialize_FolderGutterEdit($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $folder = Engine_Api::_()->core()->getSubject('folder');

    if( !($folder instanceof Folder_Model_Folder) ) {
      return false;
    }    
    
    if( !$folder->authorization()->isAllowed($viewer, 'edit') ) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['folder_id'] = $folder->getIdentity();
    return $params;
  }

  public function onMenuInitialize_FolderGutterDelete($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $folder = Engine_Api::_()->core()->getSubject('folder');

    if( !($folder instanceof Folder_Model_Folder) ) {
      return false;
    }
    
    if( !$folder->authorization()->isAllowed($viewer, 'delete') ) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['folder_id'] = $folder->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_FolderDashboardView($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $folder = Engine_Api::_()->core()->getSubject('folder');

    if( !($folder instanceof Folder_Model_Folder) ) {
      return false;
    }
    
    if( !$folder->authorization()->isAllowed($viewer, 'view') ) {
      return false;
    }    
    
    // Modify params
    $params = $row->params;
    $params['params']['folder_id'] = $folder->getIdentity();
    $params['params']['slug'] = $folder->getSlug();
    return $params;
  }  
  
  public function onMenuInitialize_FolderDashboardEdit($row)
  {
    return $this->onMenuInitialize_FolderGutterEdit($row);
  }

  public function onMenuInitialize_FolderDashboardManage($row)
  {
    return $this->onMenuInitialize_FolderGutterEdit($row);
  }
  
  public function onMenuInitialize_FolderDashboardUpload($row)
  {
    return $this->onMenuInitialize_FolderGutterEdit($row);
  }
  
  public function onMenuInitialize_FolderDashboardDelete($row)
  {
    return $this->onMenuInitialize_FolderGutterDelete($row);
  }  
  

}