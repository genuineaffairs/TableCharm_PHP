<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 6590 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepageevent.isActivate', 0);
if (empty($isActive)) {
  return;
}
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Page Profile Events'),
        'description' => $view->translate('Forms the Events tab of your Page and shows events of your Page. It should be placed in the Tabbed Blocks area of the Page Profile.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepageevent.profile-sitepageevents',
        'defaultParams' => array(
            'title' => $view->translate('Events'),
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Upcoming Events'),
        'description' => $view->translate('Displays upcoming events of your Page.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepageevent.profile-events',
        'defaultParams' => array(
            'title' => $view->translate('Upcoming Events'),
        ),
    )
)
?>