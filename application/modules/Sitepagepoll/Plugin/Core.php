<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagepoll_Plugin_Core {

  //DELETE USERS BELONGINGS BEFORE THAT USER DELETION
  public function onUserDeleteBefore($event) {
    $payload = $event->getPayload();

    if ($payload instanceof User_Model_User) {

      //DELETE POLLS
      $sitepagepollTable = Engine_Api::_()->getDbtable('polls', 'sitepagepoll');
      $sitepagepollSelect = $sitepagepollTable->select()->where('owner_id = ?', $payload->getIdentity());

      foreach ($sitepagepollTable->fetchAll($sitepagepollSelect) as $sitepagepoll) {
        $sitepagepoll->delete();
      }
    }
  }

}
?>