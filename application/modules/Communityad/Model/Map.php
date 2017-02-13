<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Map.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Model_Map extends Core_Model_Item_Abstract {

  // Properties
  protected $_parent_type = 'map';
  protected $_searchColumns = array('option_id', 'child_id',);
  protected $_parent_is_owner = true;

}