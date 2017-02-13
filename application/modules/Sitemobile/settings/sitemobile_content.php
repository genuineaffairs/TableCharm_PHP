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
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
// ALBUM, BLOG, CLASSIFIED, EVENT, FORUM, GROUP, MUSIC,  POLL, VIDEO, 
$multiOptions = array("" => "", 'album' => 'Albums', 'blog' => 'Blogs', 'classified' => 'Classifieds', 'event' => 'Events', 'forum' => 'Forum', 'group' => 'Groups', 'music' => 'Music', 'poll' => 'Poll', 'video' => 'Videos');

$searchOptionsInt=  array('sitepage','sitestore','sitegroup','sitebusiness','document','siteevent');

$modules=Engine_Api::_()->getDbtable('modules', 'sitemobile')->getManageModulesList(array('integrated'=>1));
$searchOptions=  array();
$searchOptions[]='';
foreach ($modules as $module){
  if(in_array($module->name,$searchOptionsInt)){
    $searchOptions[$module->name]= " ".$module->title. " Browse Search Form";
  }
}
if(count($searchOptions)>1){
$searchModuleList =array(
                    'Select',
                    'module_search',
                    array(
                        'label' => $view->translate('Select the form type which you want to show on this page. If you want to show the form based on the module corresponding to this page, then leave it blank.'),
                        'multiOptions' => $searchOptions,
                        'value' => '',
                    )
                );
}else{
  $searchModuleList = array();
}

return array(
    array(
        'title' => 'HTML Block',
        'description' => 'Inserts any HTML of your choice.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'core.html-block',
        'special' => 1,
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title'
                    )
                ),
                array(
                    'Textarea',
                    'data',
                    array(
                        'label' => 'HTML'
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Ad Campaign',
        'description' => 'Shows one of your ad banners. Requires that you have at least one active ad campaign.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.ad-campaign',
        // 'special' => 1,
        'autoEdit' => true,
        'adminForm' => 'Core_Form_Admin_Widget_Ads',
    ),
    array(
        'title' => 'Background / Watermark Image',
        'description' => 'Shows the background/watermark image.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.background-image',
        // 'special' => 1,
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Hidden',
                    'title',
                    array()
                ),
                array(
                    'Text',
                    'backgroundImage',
                    array(
                        'label' => 'Enter the Background / Watermark Image Path.',
                    )
                ),
                )
            )
    ),
    array(/* change */
        'title' => 'Tab Container',
        'description' => 'Adds a container with a tab menu. Any other blocks you drop inside it will become tabs.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.container-tabs-columns',
        'special' => 1,
        'defaultParams' => array(
            'layoutContainer' => 'horizontal',
           // 'max' => 4,
        ),
        'canHaveChildren' => true,
        'childAreaDescription' => 'Adds a container with a tab menu. Any other blocks you drop inside it will become tabs.',
        //'special' => 1,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                    )
                ),
                array(
                    'Select',
                    'layoutContainer',
                    array(
                        'label' => 'Choose the view that you want to be available for the tabs in this tab container.',

                        'default' => 'tab',
                        'multiOptions' => array(
                            'tab' => 'Tab Collapsible View',
                            // 'vertical' => 'Vertical View',
                            'horizontal' => 'Horizontal Tab View',
                            'horizontal_icon' => 'Horizontal Tab with Icon View',
                            'panel' => 'Tab Panel View',
                        ),
                        'value'=>'horizontal_icon'
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Content',
        'description' => 'Shows the page\'s primary content area. (Not all pages have primary content)',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'core.content',
        'requirements' => array(
            'page-content',
        ),
    ),
    array(
        'title' => 'Site Logo',
        'description' => 'Shows your site-wide main logo. Images are uploaded via the "File Media Manager".',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.sitemobile-menu-logo',
        'adminForm' => 'Sitemobile_Form_Admin_Widget_Logo',
        'requirements' => array(
            'header-footer',
        ),
    ),
    array(
        'title' => 'Dashboard Menu',
        'description' => 'Shows the dashboard menu. You can edit its contents in your dashboard menu editor.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.dashboard',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'showSearch',
                    array(
                        'label' => 'Do you want to be show search form',
                        'default' => '1',
                        'multiOptions' => array(
                            '1' => 'Yes',
                            '0' => 'No',
                        )
                    )
                ),
            ),
        )
    ),
    array(
        'title' => 'Back Button',
        'description' => 'Shows back button.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.back-button',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Select',
                    'buttonType',
                    array(
                        'label' => 'Select the display type of back button.',
                        'default' => 'text',
                        'multiOptions' => array(
                            'notext' => 'Only Icon',
                            'text' => 'Only Text',
                            'both' => 'Both Icon and Text',
                        )
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'Profile Links',
        'description' => 'Displays a member\'s, group\'s, or event\'s links on their profile.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.profile-links',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Links',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject',
        ),
    ),
    array(
        'title' => 'Footer',
        'description' => 'Shows the footer menu.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.sitemobile-footer',
        'requirements' => array(
            'header-footer',
        ),
        'defaultParams' => array(
            'shows' => array("copyright","menusFooter","languageChooser","affiliateCode")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'shows',
                    array(
                        'label' => $view->translate('Select the options below that you want to be displayed in this block.'),
                        'multiOptions' => array("copyright" => "Copyright", "menusFooter" => "Footer Menus", "languageChooser" => "Language Chooser", 'affiliateCode' => 'Affiliate Code'),
                   
                    ),
                )
            )
        )
    ),
    array(
        'title' => 'Comments',
        'description' => 'Shows the comments about an item.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.comments',
        'defaultParams' => array(
            'title' => 'Comments'
        ),
        'requirements' => array(
            'subject',
        ),
    ),
    array(
        'title' => 'Startup Image',
        'description' => 'Shows the  startup image that will appears during start-up, before loading of site. You can add a Startup Image from the "Layout" >> "File & Media Manager". This widget should be placed on the "Startup Page"',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.startup',
        'adminForm' => 'Sitemobile_Form_Admin_Widget_Startup',
        'requirements' => array(
            'header-footer',
        ),
    ),
//    array(
//        'title' => 'Statistics',
//        'description' => 'Shows some basic usage statistics about your community.',
//        'category' => 'Core',
//        'type' => 'widget',
//        'name' => 'core.statistics',
//        'defaultParams' => array(
//            'title' => 'Statistics'
//        ),
//        'requirements' => array(
//            'no-subject',
//        ),
//    ),
    array(
        'title' => 'Contact Form',
        'description' => 'Displays the contact form.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'core.contact',
        'requirements' => array(
            'no-subject',
        ),
        'defaultParams' => array(
            'title' => 'Contact',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Notifications, Requests and Messages',
        'description' => 'Shows the notification, request and messages received by a member.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.sitemobile-notification-request-messages',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'loadingViaAjax',
                    array(
                        'label' => $view->translate('Widget Content Loading'),
                        'description' => $view->translate('Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content. (Note: Select ‘No’, if you are placing this widget in the Footer of your site.)'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                )
            )
        )
    ),
    array(
        'title' => 'Advanced Search',
        'description' => 'Add the ability to search your site’s content on any page..',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.sitemobile-advancedsearch',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                $searchModuleList,                
                array(
                    'Radio',
                    'search',
                    array(
                        'label' => $view->translate('Select the display type for Search.'),
                        'multiOptions' => array(
                            1 => $view->translate('Only Search Text field'),
                            3 => $view->translate('Expanded Advanced Search'),
                            2 => $view->translate('Search Text field with expandable Advanced Search options'),
                        ),
                        'value' => 2,
                    )
                ),
                 array(
                    'Select',
                    'location',
                    array(
                        'label' => $view->translate('Do you want  show the Location Text field with Search Text field.'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No'),
                           
                        ),
                        'value' => 0,
                    )
                )
            )
        )
    ),
    array(
        'title' => 'Options',
        'description' => 'Displays a list of actions that can be performed on the page which is being viewed currently (edit, report, join, invite, etc).',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.sitemobile-options'
    ),
    array(
        'title' => 'Profile Photo and Status',
        'description' => 'Displays a profiles photo and status on it\'s profile.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.profile-photo-status'
// 			'requirements' => array(
// 				'subject' => 'event',
// 			),
    ),
    array(
        'title' => $view->translate('Scroll To Top'),
        'description' => $view->translate('This widget displays a "Scroll To Top" button when users scroll down to the bottom of the page. This widget should be placed at the height of your page where you want the user to be scrolled-to upon clicking.'),
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.scroll-to-top',
        'adminForm' => array(
            'elements' => array(
                array(
                    'hidden',
                    'title',
                    array(
                        'label' => ''
                    )
                ),
//                array(
//                    'Text',
//                    'mouseOverText',
//                    array(
//                        'label' => $view->translate('Enter the HTML title that you want to display when users mouse-over on "Scroll to Top" button.'),
//                        'value' => $view->translate('Scroll to Top'),
//                    )
//                ),
            )
        )
    ),
    array(
        'title' => 'Announcements',
        'description' => 'Displays recent announcements.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.list-announcements',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Announcements',
        ),
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Navigation',
        'description' => 'Displays Navigation.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.sitemobile-navigation',
//        'isPaginated' => true,
        'defaultParams' => array(
            'title' => '',
        ),
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Page Title',
        'description' => 'Displays the title of the page.',
        'category' => 'Core',
        'type' => 'widget',
        'name' => 'sitemobile.sitemobile-headingtitle',
        'isPaginated' => false,
        'defaultParams' => array(
            'title' => '',
        ),
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'nonloggedin',
                    array(
                        'label' => $view->translate('Show Page Title to users (non-logged in users) of your site.'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'loggedin',
                    array(
                        'label' => $view->translate('Show Page Title to members of your site.'),
                        'multiOptions' => array(
                            1 => $view->translate('Yes'),
                            0 => $view->translate('No')
                        ),
                        'value' => 0,
                    )
                )
            )
        )
    ),
        )
?>