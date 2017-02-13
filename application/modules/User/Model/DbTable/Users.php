<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Users.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_Model_DbTable_Users extends SharedResources_Model_DbTable_Abstract
{

  protected $_name = 'users';
  protected $_rowClass = 'User_Model_User';

  /**
   * Define a list of pages in which we need to separate users for each site
   * 
   * @var array
   */
//  protected $_separateSections = array(
//      array('user', 'index', 'browse'),
//      array('sitepagemember', 'index', 'getmembers'),
//      array('zulu', 'index', 'get-members')
//  );
//  
//  public function fetchAll($where = null, $order = null, $count = null, $offset = null)
//  {
//    if (!($where instanceof Zend_Db_Table_Select)) {
//      $select = $this->select();
//
//      if ($where !== null) {
//        $this->_where($select, $where);
//      }
//
//      if ($order !== null) {
//        $this->_order($select, $order);
//      }
//
//      if ($count !== null || $offset !== null) {
//        $select->limit($count, $offset);
//      }
//    } else {
//      $select = $where;
//    }
//
//    if ($this->_matchURL()) {
//      $current_site_id = Engine_Api::_()->getApi('core', 'sharedResources')->getSiteId();
//      $usersSitesTable = Engine_Api::_()->getDbTable('usersSites', 'sharedResources');
//      $usersSitesTableName = $usersSitesTable->info('name');
//      $userTableName = $this->info('name');
//
//      $from = $select->getPart('from');
//      
//      if (empty($from)) {
//        $select->from($userTableName)->join($usersSitesTableName, "{$usersSitesTableName}.user_id = {$userTableName}.user_id", null)
//                ->where($usersSitesTableName . '.site_id = ?', $current_site_id);
//      }
//    }
//
//    return parent::fetchAll($select, $order, $count, $offset);
//  }
//
//  /**
//   * Check if the current URL is matched with pre-defined URLs
//   * 
//   * @return boolean
//   */
//  protected function _matchURL()
//  {
//    $request = Zend_Controller_Front::getInstance()->getRequest();
//
//    if ($request) {
//      foreach ($this->_separateSections as $url) {
//        if ($request->getModuleName() === $url[0] && $request->getControllerName() === $url[1]) {
//          if ($url[2] === '*') {
//            // Applied for all actions
//            return true;
//          } else if ($request->getActionName() === $url[2]) {
//            // Only applied for the pre-defined action
//            return true;
//          }
//        }
//      }
//    }
//    return false;
//  }

}
