<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content_user.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagenote.isActivate', 0);
if (empty($isActive)) {
  return;
}
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Page Profile Notes'),
        'description' => $view->translate('Forms the Notes tab of your Page and shows notes of your Page. It should be placed in the Tabbed Blocks area of the Page Profile.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagenote.profile-sitepagenotes',
        'defaultParams' => array(
            'title' => $view->translate('Notes'),
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Most Commented Notes'),
        'description' => $view->translate('Displays your Page\'s most commented notes.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagenote.comment-sitepagenotes',
        'defaultParams' => array(
            'title' => $view->translate('Most Commented Notes'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Most Recent Notes'),
        'description' => $view->translate('Displays your Page\'s most recent notes.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagenote.recent-sitepagenotes',
        'defaultParams' => array(
            'title' => $view->translate('Most Recent Notes'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Most Liked Notes'),
        'description' => $view->translate('Displays your Page\'s most liked notes.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagenote.like-sitepagenotes',
        'defaultParams' => array(
            'title' => $view->translate('Most Liked Notes'),
            'titleCount' => true,
        ),
    ),
)
?>