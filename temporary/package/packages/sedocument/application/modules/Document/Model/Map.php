<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Map.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Document_Model_Map extends Core_Model_Item_Abstract {

	//PROPERTIES
  protected $_parent_type = 'map';
  protected $_searchColumns = array('option_id', 'child_id',);
  protected $_parent_is_owner = true;

}
