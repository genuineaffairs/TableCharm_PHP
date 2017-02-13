<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sitemobile_content.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    array(
        'title' => 'Profile Albums',
        'description' => 'Displays a member\'s albums on their profile.',
        'category' => 'Albums',
        'type' => 'widget',
        'name' => 'sitemobile.album-profile-albums',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Albums',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
);