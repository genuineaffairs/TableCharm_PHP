<?php

/**
 * Description of Core
 *
 * @author Chips Invincible <gachip11589@gmail.com> :))
 */
class SharedResources_Api_Core extends Core_Api_Abstract
{

  protected $_site_id;

  public function getSiteId($host = null)
  {
    if (!$host) {
      $host = $_SERVER['HTTP_HOST'];

      if ($this->_site_id) {
        return $this->_site_id;
      }
    }
    /* @var $siteTable Engine_Db_Table */
    $siteTable = Engine_Api::_()->getDbTable('sites', 'sharedResources');
    // Get current site id
    $site = $siteTable->fetchRow(array('host = ?' => filter_var($host)));
    // Set static value
    $this->_site_id = $site->site_id ? $site->site_id : SharedResources_Model_DbTable_Sites::DEFAULT_SITE_ID;

    return $this->_site_id;
  }

  public function getSiteMaster($site_id = null)
  {
    if (!$site_id) {
      $site_id = $this->getSiteId();
    }

    $site_master_id = Engine_Api::_()->getDbTable('usersSites', 'sharedResources')
                    ->fetchRow(array(
                        'is_site_master = ?' => 1,
                        'site_id = ?' => $site_id
                    ))->user_id;

    // Get site master
    $site_master = Engine_Api::_()->user()->getUser($site_master_id);

    return $site_master;
  }

  /**
   * Add site separation condition to select query
   * 
   * @param Zend_Db_Select $select
   */
  public function addSiteSeprationCondition(Zend_Db_Select $select)
  {
    // Get current site id
    $current_site_id = Engine_Api::_()->getApi('core', 'sharedResources')->getSiteId();
    // Users and sites relationship table
    $usersSitesTable = Engine_Api::_()->getDbTable('usersSites', 'sharedResources');
    $usersSitesTableName = $usersSitesTable->info('name');
    // Users table
    $userTable = Engine_Api::_()->getDbTable('users', 'user');
    $userTableName = $userTable->info('name');

    $from = $select->getPart('from');
    if (empty($from)) {
      $select->from($userTableName);
    }
    $from2 = $select->getPart('from');
    if (array_key_exists($userTableName, $from2)) {
      $tableName = $userTableName;
    } else {
      $tableName = array_shift(array_keys($from2));
    }
    // Add the condition
    $select->join($usersSitesTableName, "{$usersSitesTableName}.user_id = {$tableName}.user_id", null)
            ->where($usersSitesTableName . '.site_id = ?', $current_site_id);
  }

}
