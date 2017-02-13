<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Plugin_Menus {

  public function canViewAdvertiesment() {
    $temp_file = Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.temp.file');
    if (empty($temp_file)) {
      return;
    }
    $viewer = Engine_Api::_()->user()->getViewer();

    // Must be able to view advertising
    if (!Engine_Api::_()->authorization()->isAllowed('communityad', $viewer, 'view')) {
      return false;
    }

    return true;
  }

  public function canManageAdvertiesment() {
    $communityad_navi = Zend_Registry::get('communityad_navigation_show');
    if (empty($communityad_navi))
      return false;
    $viewer = Engine_Api::_()->user()->getViewer();

    // Must be able to view advertising
    if (!(Engine_Api::_()->authorization()->isAllowed('communityad', $viewer, 'create'))) {
      return false;
    }

    return true;
  }

  public function canCreateAdvertiesment() {
    $communityad_navi = Zend_Registry::get('communityad_navigation_show');
    if (empty($communityad_navi))
      return false;
    $viewer = Engine_Api::_()->user()->getViewer();
    // Must be able to view advertising
    if (!Engine_Api::_()->authorization()->isAllowed('communityad', $viewer, 'create')) {
      return false;
    }

    return true;
  }

  // SHOWING LINK ON "GROUP PROFILE PAGE".
  public function onMenuInitialize_CoreMainCommunityad($row) {
    $temp_file = Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.temp.file');
    if (empty($temp_file)) {
      return;
    }
    $viewer = Engine_Api::_()->user()->getViewer()->getIdentity();
    if (!empty($viewer)) {
      return array(
          'label' => $row->label,
          'icon' => 'application/modules/Communityad/externals/images/ad-icon16.png',
          'route' => 'communityad_listpackage',
      );
    }
    return false;
  }

}