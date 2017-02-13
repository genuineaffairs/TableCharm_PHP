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
abstract class SharedResources_Model_DbTable_RadcodesAbstract extends Radcodes_Model_DbTable_Categories
{

  public function __construct($config = array())
  {
    parent::__construct($config);

    $this->_setAdapter(SharedResources_Model_DbTable_Abstract::getSharedAdapter());
  }

}
