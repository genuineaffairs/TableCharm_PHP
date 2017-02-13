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
        'title' => 'Advanced Activity Feed',
        'description' => 'Displays the activity feed.',
        'category' => 'Advanced Activity Feed',
        'type' => 'widget',
        'name' => 'sitemobile.sitemobile-advfeed',
        'defaultParams' => array(
            'title' => 'What\'s New',
            'sitemobileadvfeed_scroll_autoload' => 1
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                        'value' => 'What\'s New'
                    )
                ),
                array(
                    'Radio',
                    'sitemobileadvfeed_scroll_autoload',
                    array(
                        'label' => 'Auto-Loading Activity Feeds On-scroll',
                        'description' => "Do you want to enable auto-loading of old activity feeds when users scroll down to the bottom of Advanced Activity Feeds?",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                    )
                ),
                array(
                    'Text',
                    'sitemobileadvfeed_length',
                    array(
                        'label' => 'Overall Feed Length',
                        'description' => "ACTIVITY_FORM_ADMIN_SETTINGS_GENERAL_LENGTH_DESCRIPTION",
                        'value' => 15,
                        'required' => true,
                        'allowEmpty' => false,
                        'validators' => array(
                            array('Int', true),
                            array('Between', true, array(1, 50, true))
                        ),
                    )
                ),
            )
        ),
    ),
    
    array(
        'title' => 'Advanced Activity Facebook Feed',
        'description' => 'Displays the activity feed.',
        'category' => 'Advanced Activity Feed',
        'type' => 'widget',
        'name' => 'advancedactivity.advancedactivityfacebook-userfeed',
        'defaultParams' => array(
            'title' => 'Facebook Feeds',
            'sitemobilefacebookfeed_scroll_autoload' => 1
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                        'value' => 'Facebook Feeds'
                    )
                ),
                array(
                    'Radio',
                    'sitemobilefacebookfeed_scroll_autoload',
                    array(
                        'label' => 'Auto-Loading Activity Feeds On-scroll',
                        'description' => "Do you want to enable auto-loading of old activity feeds when users scroll down to the bottom of Advanced Activity Feeds?",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                    )
                ),
                array(
                    'Text',
                    'sitemobilefacebookfeed_length',
                    array(
                        'label' => 'Overall Feed Length',
                        'description' => "ACTIVITY_FORM_ADMIN_SETTINGS_GENERAL_LENGTH_DESCRIPTION",
                        'value' => 15,
                        'required' => true,
                        'allowEmpty' => false,
                        'validators' => array(
                            array('Int', true),
                            array('Between', true, array(1, 50, true))
                        ),
                    )
                ),
            )
        ),
    ),
    
    array(
        'title' => 'Advanced Activity Linkedin Feed',
        'description' => 'Displays the activity feed.',
        'category' => 'Advanced Activity Feed',
        'type' => 'widget',
        'name' => 'advancedactivity.advancedactivitylinkedin-userfeed',
        'defaultParams' => array(
            'title' => 'Linkedin Feeds',
            'sitemobilelinkedinfeed_scroll_autoload' => 1
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                        'value' => 'Linkedin Feeds'
                    )
                ),
                array(
                    'Radio',
                    'sitemobilelinkedinfeed_scroll_autoload',
                    array(
                        'label' => 'Auto-Loading Activity Feeds On-scroll',
                        'description' => "Do you want to enable auto-loading of old activity feeds when users scroll down to the bottom of Advanced Activity Feeds?",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                    )
                ),
                array(
                    'Text',
                    'sitemobilelinkedinfeed_length',
                    array(
                        'label' => 'Overall Feed Length',
                        'description' => "ACTIVITY_FORM_ADMIN_SETTINGS_GENERAL_LENGTH_DESCRIPTION",
                        'value' => 15,
                        'required' => true,
                        'allowEmpty' => false,
                        'validators' => array(
                            array('Int', true),
                            array('Between', true, array(1, 50, true))
                        ),
                    )
                ),
            )
        ),
    ),
    
    array(
        'title' => 'Advanced Activity Twitter Feed',
        'description' => 'Displays the activity feed.',
        'category' => 'Advanced Activity Feed',
        'type' => 'widget',
        'name' => 'advancedactivity.advancedactivitytwitter-userfeed',
        'defaultParams' => array(
            'title' => 'Twitter Feeds',
            'sitemobiletwitterfeed_scroll_autoload' => 1
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                        'value' => 'Twitter Feeds'
                    )
                ),
                array(
                    'Radio',
                    'sitemobiletwitterfeed_scroll_autoload',
                    array(
                        'label' => 'Auto-Loading Activity Feeds On-scroll',
                        'description' => "Do you want to enable auto-loading of old activity feeds when users scroll down to the bottom of Advanced Activity Feeds?",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                    )
                ),
                array(
                    'Text',
                    'sitemobiletwitterfeed_length',
                    array(
                        'label' => 'Overall Feed Length',
                        'description' => "ACTIVITY_FORM_ADMIN_SETTINGS_GENERAL_LENGTH_DESCRIPTION",
                        'value' => 15,
                        'required' => true,
                        'allowEmpty' => false,
                        'validators' => array(
                            array('Int', true),
                            array('Between', true, array(1, 50, true))
                        ),
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Requests',
        'description' => 'Displays the current logged-in member\'s requests (i.e. friend requests, group invites, etc).',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'activity.list-requests',
        'defaultParams' => array(
            'title' => 'Requests',
        ),
        'requirements' => array(
            'viewer',
        ),
    ),
);