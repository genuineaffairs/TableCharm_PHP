<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AccessLevel
 *
 * @author abakivn
 */
class Zulu_Model_DbTable_AccessLevel extends SharedResources_Model_DbTable_Abstract implements Authorization_Model_AdapterInterface {

  const FULL = 1;
  const READ_ONLY = 2;
  const LIMITED = 3;

  public static $accessTypeMap = array(
      'full' => self::FULL,
      'read_only' => self::READ_ONLY,
      'limited' => self::LIMITED,
  );
  
  public static $accessTypeString = array(
      self::FULL => 'Full Access',
      self::READ_ONLY => 'Read Only Access',
      self::LIMITED => 'Emergency Summary'
  );

  public function getAdapterName() {
    return 'access_levels';
  }

  public function getAdapterPriority() {
    return 100;
  }

  public function getAllowed($resource, $role, $action) {
    return $this->isAllowed($resource, $role, $action);
  }

  public function isAllowed($resource, $role, $action) {

    if ($resource instanceof Core_Model_Item_Abstract && $resource->getOwner()->isSelf($role)) {
      return Authorization_Api_Core::LEVEL_MODERATE;
    }

    $access_level = Engine_Api::_()->getDbTable('profileshare', 'zulu')->getAccessLevel($resource, $role);

    switch ($access_level) {
      case self::FULL:
        if (in_array($action, array('view', 'edit', 'print'))) {
          return Authorization_Api_Core::LEVEL_MODERATE;
        }
      case self::READ_ONLY:
      case self::LIMITED:
        if (in_array($action, array('view', 'print'))) {
          return Authorization_Api_Core::LEVEL_MODERATE;
        }
      case self::FULL:
      case self::READ_ONLY:
      case self::LIMITED:
        if (in_array($action, array('view_clinical'))) {
          return Authorization_Api_Core::LEVEL_MODERATE;
        }
      default:
        if (in_array($action, array('view_clinical', 'print'))) {
          return Authorization_Api_Core::LEVEL_DISALLOW;
        } else {
          return Authorization_Api_Core::LEVEL_IGNORE;
        }
    }
  }

  public function setAllowed($resource, $role, $action, $value = null) {
    return false;
  }

}
