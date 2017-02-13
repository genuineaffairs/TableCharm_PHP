<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagedocument_Plugin_Core {

  //DELETE USERS BELONGINGS BEFORE THAT USER DELETION
  public function onUserDeleteBefore($event) {
    $payload = $event->getPayload();

    if ($payload instanceof User_Model_User) {

      //DELETE DOCUMENTS
      $sitepagedocumentTable = Engine_Api::_()->getDbtable('documents', 'sitepagedocument');
      $sitepagedocumentSelect = $sitepagedocumentTable->select()->where('owner_id = ?', $payload->getIdentity());

			Engine_Api::_()->getDbtable('ratings', 'sitepagedocument')->delete(array('user_id = ?' => $payload->getIdentity()));

      foreach ($sitepagedocumentTable->fetchAll($sitepagedocumentSelect) as $sitepagedocument) {
				Engine_Api::_()->sitepagedocument()->deleteContent($sitepagedocument->document_id);
      }
    }
  }

}
?>