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
abstract class SharedResources_Model_DbTable_Abstract extends Engine_Db_Table
{

  /**
   * Central database configuration
   * 
   * @var array
   */
  protected static $_centralDbConfig = array(
      'adapter' => 'mysqli',
      'params' =>
      array(
//          'host' => 'localhost',
//          'username' => 'root',
//          'password' => 'password',
//          'dbname' => 'mgsl_live',
          'host' => 'aasg8mno13c6ti.cijeopa46pte.ap-southeast-2.rds.amazonaws.com',
          'username' => 'ebroot',
          'password' => 'Password1!',
          'dbname' => 'ebdb',
          'charset' => 'UTF8',
          'adapterNamespace' => 'Zend_Db_Adapter',
      )
  );

  /**
   * Singleton db adapter
   * 
   * @var Zend_Db_Adapter_Abstract
   */
  protected static $_sharedAdapter;

  /**
   * Used to turn sharing mode on or off
   * 
   * @var boolean
   */
  protected static $_sharingMode = false;

  public static function getSharedAdapter()
  {
    if (!self::$_sharedAdapter) {
      if (self::$_sharingMode) {
        self::$_sharedAdapter = Zend_Db::factory(self::$_centralDbConfig['adapter'], self::$_centralDbConfig['params']);
      } else {
        self::$_sharedAdapter = Engine_Db_Table::getDefaultAdapter();
      }
    }
    return self::$_sharedAdapter;
  }

  public function __construct($config = array())
  {
    parent::__construct($config);

    $this->_setAdapter(self::getSharedAdapter());
  }

}
