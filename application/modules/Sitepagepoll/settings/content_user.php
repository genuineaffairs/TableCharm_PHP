<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content_user.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Page Profile Polls'),
        'description' => $view->translate('Forms the Polls tab of your Page and shows polls of your Page. It should be placed in the Tabbed Blocks area of the Page Profile.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagepoll.profile-sitepagepolls',
        'defaultParams' => array(
            'title' => $view->translate('Polls'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Most Commented Polls'),
        'description' => $view->translate('Displays your Page\'s most commented polls.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagepoll.comment-sitepagepolls',
        'defaultParams' => array(
            'title' => $view->translate('Most Commented Polls'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Most Viewed Polls'),
        'description' => $view->translate('Displays your Page\'s most viewed polls.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagepoll.view-sitepagepolls',
        'defaultParams' => array(
            'title' => $view->translate('Most Viewed Polls'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Most Voted Polls'),
        'description' => $view->translate('Displays your Page\'s most voted polls.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagepoll.vote-sitepagepolls',
        'defaultParams' => array(
            'title' => $view->translate('Most Voted Polls'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Most Recent Polls'),
        'description' => $view->translate('Displays your Page\'s most recent polls.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagepoll.recent-sitepagepolls',
        'defaultParams' => array(
            'title' => $view->translate('Most Recent Polls'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Most Liked Polls'),
        'description' => $view->translate('Displays your Page\'s most liked polls.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagepoll.like-sitepagepolls',
        'defaultParams' => array(
            'title' => $view->translate('Most Liked Polls'),
            'titleCount' => true,
        ),
    ),
)
?>