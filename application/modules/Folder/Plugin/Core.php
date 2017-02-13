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
 
 
 
class Folder_Plugin_Core
{
  public function onStatistics($event)
  {
    $table   = Engine_Api::_()->getDbTable('folders', 'folder');
    
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count');
    $event->addResponse($select->query()->fetchColumn(0), 'folder');
  }


  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User ) {
      // Delete folders
      $folderTable = Engine_Api::_()->getDbtable('folders', 'folder');

      $folderSelect = $folderTable->select()->where('user_id = ?', $payload->getIdentity());
      foreach( $folderTable->fetchAll($folderSelect) as $folder ) {
        $folder->delete();
      }

    }
  }
  
  public function onItemDeleteBefore($event)
  {
    $item = $event->getPayload();

      // Delete folders
    $folderTable = Engine_Api::_()->getDbtable('folders', 'folder');

    $folderSelect = $folderTable->select()
      ->where('parent_type = ?', $item->getType())
      ->where('parent_id = ?', $item->getIdentity());
      
    foreach( $folderTable->fetchAll($folderSelect) as $folder ) {
      $folder->delete();
    }    
    
  }
  
}