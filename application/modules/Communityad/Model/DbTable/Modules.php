<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Modules.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Model_DbTable_Modules extends Engine_Db_Table {

  protected $_name = 'communityad_modules';
  protected $_rowClass = 'Communityad_Model_Module';

  // Function: Return the 'Module Name'array, which are available in the table.
  public function getModuleName() {
    // Queary which return the modules name which are already set by admin.
    $tableName = $this->info('name');
    $selectModule = $this->select()->from($tableName, array('module_name'));
    $fetchModule = $selectModule->query()->fetchAll();
    if (!empty($fetchModule)) {
      foreach ($fetchModule as $moduleName) {
        $moduleArray[] = $moduleName['module_name'];
      }
    }
    // Array: Which modules are not allow for advertisment.
    $not_alow_modules = array('facebookse', 'facebooksefeed', 'facebooksepage', 'grouppoll', 'birthday', 'poke', 'sitelike', 'dbbackup', 'suggestion', 'mcard', 'groupdocument', 'siteslideshow', 'mapprofiletypelevel', 'peopleyoumayknow', 'userconnection', 'communityad', 'seaocore', 'feedback', 'sitepagealbum', 'sitepageinvite', 'sitepagepoll', 'sitepagediscussion', 'sitepagedocument', 'sitepagenote', 'sitepageevent', 'sitepageoffer', 'sitepagevideo', 'sitepageform', 'sitepagebadge', 'sitepagereview');
    $moduleArray = array_merge($moduleArray, $not_alow_modules);
    return $moduleArray;
  }

  public function ismoduleads_enabled($module_name) {
    $tableName = $this->info('name');
    $selectModule = $this->select()->from($tableName, array('module_name'))->where('module_name = ?', $module_name);
    $fetchModule = $this->fetchRow($selectModule);
    if (!empty($fetchModule->module_name)) {
      return true;
    } else {
      return false;
    }
  }

  // Function: Return the 'Table Name' of any modules.
  public function getModuleInfo($contentType) {
    if( empty($contentType) ){ return; }
    $tableName = $this->info('name');
    if( strstr($contentType, "sitereview_") ) {
      $isModuleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereview');
      if( empty($isModuleEnabled) )
          return;
      
      $explodeReview = explode("_", $contentType);
      $selectModule = $this->select()->from($tableName)->where('module_id =?', $explodeReview[1]);
    }else {
        $selectModule = $this->select()->from($tableName)->where('module_name =?', $contentType)->orwhere('table_name =?', $contentType);
    }

    $fetchModule = $selectModule->query()->fetchAll();
    
    if( !empty($fetchModule) && !empty($fetchModule[0]) && !empty($fetchModule[0]['module_name']) ){
      $isModuleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($fetchModule[0]['module_name']);
      if( empty($isModuleEnabled) )
          return;
    }
    
    if (!empty($fetchModule)) {
      if( strstr($contentType, "sitereview_") ) {
        $fetchModule[0]['table_name'] = "sitereview_listing";
        return $fetchModule[0];
      }else if( strstr($contentType, "sitereview") ) {
        return $fetchModule;
      }else {
        return $fetchModule[0];
      }
//      if( strstr($contentType, "sitereview") ) { return $fetchModule; }
//        else { return $fetchModule[0]; }
    } else {
      return;
    }
  }
	
	// Return the row acording to the "Table name". Need to use this function for "Sponcerd Stories".
  public function getModuleType($contentType) {
    if(empty($contentType))
      return;
    
    $tableName = $this->info('name');
    
    
    if( strstr($contentType, "sitereview_") ) {
      $explodeReview = explode("_", $contentType);
      $getTemModuleId = end($explodeReview);
      if(is_numeric($getTemModuleId))
        $selectModule = $this->select()->from($tableName)->where('module_id =?', $explodeReview[1]);
      
      $fetchModule = $selectModule->query()->fetchAll();
    }else {
      $selectModule = $this->select()->from($tableName)->where('table_name =?', $contentType);
      $fetchModule = $selectModule->query()->fetchAll();
    }
        
    if (!empty($fetchModule)) {
      if( strstr($contentType, "sitereview_") ) {
        $fetchModule[0]['table_name'] = "sitereview_listing";
        return $fetchModule[0];
      }else {
        return $fetchModule[0];
      }
    } else {
      return;
    }
  }

  public function freePackageModule() {
    return array('album', 'classified', 'blog', 'event', 'forum', 'group', 'music', 'poll', 'video', 'list', 'document', 'sitepage', 'recipe');
  }

}
