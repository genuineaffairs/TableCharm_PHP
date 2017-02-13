<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Plugin_Core {

  public function onRenderLayoutDefault() {
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
//    $newStyleWidthUpdate = Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.block.widthupdatefile', 1);
//    if (empty($newStyleWidthUpdate))
//      $view->headLink()
//              ->appendStylesheet($view->url(array("module" => "communityad", "controller" => "index", "action" => "communityads-style"), "default", true));
//    else
//      $view->headLink()
//              ->appendStylesheet($view->layout()->staticBaseUrl . 'application/modules/Communityad/externals/styles/style.css');
  }

  public function onUserDeleteBefore($event) {
    $payload = $event->getPayload();
    if ($payload instanceof User_Model_User) {
      // Delete
      $owner_id = $payload->getIdentity();
      $adcampaignTable = Engine_Api::_()->getDbtable('adcampaigns', 'communityad');
      $adcampaignSelect = $adcampaignTable->select()->where('owner_id = ?', $owner_id);
      foreach ($adcampaignTable->fetchAll($adcampaignSelect) as $adcampaign) {
        $adcampaign->delete();
      }
      $adTable = Engine_Api::_()->getDbtable('userads', 'communityad');
      $adSelect = $adTable->select()->where('owner_id = ?', $owner_id);
      foreach ($adTable->fetchAll($adSelect) as $userads) {
        $userads->delete();
      }

      // Delete all entry from the "communityad_like" table.
      $likeTable = Engine_Api::_()->getDbtable('likes', 'communityad');
      $select = $likeTable->select()->where('poster_id = ?', $owner_id);
      foreach ($likeTable->fetchAll($select) as $like) {
        $like->delete();
      }
    }
  }

  public function onCommunityadAdcampaignDeleteBefore($event) {
    $payload = $event->getPayload();

    if ($payload instanceof Communityad_Model_Adcampaign) {
      $adTable = Engine_Api::_()->getDbtable('userads', 'communityad');
      $adSelect = $adTable->select()->where('campaign_id = ?', $payload->getIdentity());
      foreach ($adTable->fetchAll($adSelect) as $userads) {
        $userads->delete();
      }
    }
  }

  public function onCommunityadUseradDeleteBefore($event) {
    $userads = $event->getPayload();
    if ($userads instanceof Communityad_Model_Userad) {
      $communityadAdcancelTable = Engine_Api::_()->getItemTable('communityad_adcancel');
      $targetTable = Engine_Api::_()->getDbtable('adtargets', 'communityad');
      $likeTable = Engine_Api::_()->getDbtable('likes', 'communityad');
      $adstatisticsTable = Engine_Api::_()->getDbtable('adstatistics', 'communityad');
      $target = $targetTable->getUserAdTargets($userads->userad_id);
      if (!empty($target))
        $target->delete();

      $communityadAdcancelSelect = $communityadAdcancelTable->select()
              ->where('ad_id = ?', $userads->userad_id);

      foreach ($communityadAdcancelTable->fetchAll($communityadAdcancelSelect) as $adcancel) {
        $adcancel->delete();
      }

      $likeSelect = $likeTable->select()
              ->where('ad_id = ?', $userads->userad_id);

      foreach ($likeTable->fetchAll($likeSelect) as $like) {
        $like->delete();
      }

      $adstatisticsSelect = $adstatisticsTable->select()
              ->where('userad_id = ?', $userads->userad_id);

      foreach ($adstatisticsTable->fetchAll($adstatisticsSelect) as $adstatistic) {
        $adstatistic->delete();
      }
    }
  }

}