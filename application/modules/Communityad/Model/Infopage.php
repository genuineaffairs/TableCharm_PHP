<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Infopage.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Model_Infopage extends Core_Model_Item_Abstract {

  protected $_parent_type = 'infopage';
  protected $_parent_is_owner = true;
  protected $_package;
  protected $_statusChanged;
  protected $_searchTriggers = false;
  protected $_modifiedTriggers = false;

}