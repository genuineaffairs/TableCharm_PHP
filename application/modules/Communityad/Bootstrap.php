<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Communityad_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
	public function __construct($application)
  {
    parent::__construct($application);
		include APPLICATION_PATH . '/application/modules/Communityad/controllers/license/license.php';
		
  }
}