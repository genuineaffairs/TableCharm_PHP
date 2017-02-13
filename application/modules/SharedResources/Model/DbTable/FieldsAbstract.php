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
abstract class SharedResources_Model_DbTable_FieldsAbstract extends Engine_Db_Table
{

  public function __construct($config = array())
  {
    parent::__construct($config);

    $itemTable = Engine_Api::_()->getItemTable($this->_fieldType);
    // Check if the parent item is a shared resource
    if ($itemTable instanceof SharedResources_Model_DbTable_Abstract &&
            !($itemTable instanceof User_Model_DbTable_Users)) {
      // Switch adapter if resource is shared across all sites
      $this->_setAdapter(SharedResources_Model_DbTable_Abstract::getSharedAdapter());
    }
  }

}
