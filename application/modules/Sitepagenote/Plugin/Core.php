<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_Plugin_Core {

  //DELETE USERS BELONGINGS BEFORE THAT USER DELETION
  public function onUserDeleteBefore($event) {
    
    $payload = $event->getPayload();
    if ($payload instanceof User_Model_User) {
      // DELETE NOTES
      $sitepagenoteTable = Engine_Api::_()->getDbtable('notes', 'sitepagenote');
      $sitepagenoteSelect = $sitepagenoteTable->select()->where('owner_id = ?', $payload->getIdentity());
      foreach ($sitepagenoteTable->fetchAll($sitepagenoteSelect) as $sitepagenote) {
        //DELETE NOTE, ALBUM AND NOTE IMAGES
        Engine_Api::_()->sitepagenote()->deleteContent($sitepagenote->note_id);
      }
    }
  }

  public function onRenderLayoutDefault($event) {
    $viewTemp = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $view = $event->getPayload();
    $view->headScript()
            ->appendFile($viewTemp->layout()->staticBaseUrl . 'application/modules/Sitepagenote/externals/scripts/core.js');
  }

}

?>