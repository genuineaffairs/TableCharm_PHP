<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Cleanup.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Plugin_Task_Cleanup extends Core_Plugin_Task_Abstract {

  public function execute() {

    if (date('Y-m-d', strtotime(Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.update.approved'))) < date('Y-m-d')) {
      Engine_Api::_()->getDbtable('userads', 'communityad')->updateApproved();
    }
  }

}