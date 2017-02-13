<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Abstract.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
abstract class SharedResources_Model_Item_Abstract extends Core_Model_Item_Abstract
{

  public function getSiteId()
  {
    return $this->site_id;
  }

//  public function getOwner($recurseType = null)
//  {
//    // Get owner type
//    $type = null;
//    if (!empty($this->_owner_type)) { // Local definition
//      $type = $this->_owner_type;
//    } else if (!empty($this->owner_type)) { // Db definition
//      $type = $this->owner_type;
//    } else {
//      $type = 'user';
//    }
//    // Currently modification only applied to user owner type
//    if ($type === 'user') {
//      // Get parent id
//      $id = null;
//      if (!empty($this->owner_id)) {
//        $id = $this->owner_id;
//      } else {
//        $short_type = Engine_Api::typeToShort($type, Engine_Api::_()->getItemModule($type));
//        $prop = $short_type . '_id';
//        if (!empty($this->$prop)) {
//          $id = $this->$prop;
//        }
//      }
//      return $this->_customFindItem($type, $id);
//    } else {
//      return parent::getOwner($recurseType);
//    }
//  }
//
//  /**
//   * Find item and generate row class
//   * @param scalar $id
//   */
//  protected function _customFindItem($type, $id)
//  {
//    /* @var $ownerTable Engine_Db_Table */
//    $ownerTable = Engine_Api::_()->getItemTable($type);
//
//    // Reset the site separation condition
//    $primary_key = array_shift($ownerTable->info('primary'));
//    $select = $ownerTable->select()->reset('where')->where("$primary_key = ?", $id);
//    $rows = $select->query()->fetchAll(Zend_Db::FETCH_ASSOC);
//
//    if (count($rows) == 0) {
//      return Engine_Api::_()->getItem($type, $id);
//    }
//
//    $data = array(
//        'table' => $ownerTable,
//        'data' => $rows[0],
//        'readOnly' => $select->isReadOnly(),
//        'stored' => true
//    );
//
//    $rowClass = $ownerTable->getRowClass();
//    if (!class_exists($rowClass)) {
//      Zend_Loader::loadClass($rowClass);
//    }
//    return new $rowClass($data);
//  }
}
