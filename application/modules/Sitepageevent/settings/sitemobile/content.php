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
if ( empty($isActive) ) {
  return;
}

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Page Profile Events'),
        'description' => $view->translate('This widget forms the Events tab on the Page Profile and displays the events of the Page. It should be placed in the Tabbed Blocks area of the Page Profile.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepageevent.sitemobile-profile-sitepageevents',
        'defaultParams' => array(
            'title' => $view->translate('Events'),
        ),
    ),
    array(
			'title' => $view->translate('Page Event Profile Details'),
			'description' => $view->translate("Displays a Page event's details on it's profile."),
			'category' => $view->translate('Page Event Profile'),
			'type' => 'widget',
			'name' => 'sitepageevent.sitemobile-profile-info',
			'defaultParams' => array(
					'title' => $view->translate(''),
			),
			'adminForm' => array(
				'elements' => array(
					array(
						'Radio',
						'sitepageeventInfoCollapsible',
						array(
								'label' => $view->translate('Do you want users to be able to expand and collapse page event information shown in this block?'),
								//'description' => $view->translate('Do you want to show the event detail collapsible?'),
								'multiOptions' => array(
										1 => $view->translate('Yes'),
										0 => $view->translate('No')
								),
								'value' => 0,
						)
					),
					array(
						'Radio',
						'sitepageeventInfoCollapsibleDefault',
						array(
								'label' => $view->translate('Do you want page event details to be expanded or collapsed by default? (This setting will only work, if you have selected ‘No’ in the above setting)'),
								//'description' => $view->translate('Do you want to show the event detail collapsible?'),
								'multiOptions' => array(
										1 => $view->translate('Yes'),
										0 => $view->translate('No')
								),
								'value' => 1,
						)
					),
				),
			),
    ),
    array(
        'title' => $view->translate('Page Event Profile Rsvp'),
        'description' => $view->translate("Displays a Page event's rsvp on it's profile."),
        'category' => $view->translate('Page Event Profile'),
        'type' => 'widget',
        'name' => 'sitepageevent.profile-rsvp',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
    'title' => $view->translate('Page Event Profile Photos'),
    'description' => $view->translate('Displays a Page event\'s photos on it\'s profile.'),
    'category' => $view->translate('Page Event Profile'),
    'type' => 'widget',
    'name' => 'sitepageevent.profile-photos',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Photos',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'sitepageevent_event',
    ),
  ),
	array(
		'title' => $view->translate('Page Event Profile Guest Member'),
		'description' => $view->translate("Displays a Page event's guest members on it's profile."),
		'category' => $view->translate('Page Event Profile'),
		'type' => 'widget',
		'name' => 'sitepageevent.profile-members',
		'defaultParams' => array(
				'title' => 'Guests',
				'titleCount' => true
		),
	),
	array(
		'title' => $view->translate('Page Event Breadcrumb'),
		'description' => $view->translate("Displays breadcrumb on Page event's various pages."),
		'category' => $view->translate('Page Event Breadcrumb'),
		'type' => 'widget',
		'name' => 'sitepageevent.sitemobile-breadcrumb',
		'defaultParams' => array(
				'title' => '',
		),
	),
  array(
		'title' => $view->translate('Page Events'),
		'description' => $view->translate('Displays the list of Events from Pages created on your community. This widget should be placed in the widgetized Page Events page. Results from the Search Page Events form are also shown here.'),
		'category' => $view->translate('Pages'),
		'type' => 'widget',
		'name' => 'sitepageevent.sitepage-event',
		'defaultParams' => array(
				'title' => '',
				'titleCount' => true,
		),
		'adminForm' => array(
				'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => $view->translate('Count'),
										'description' => $view->translate('(number of events to show)'),
										'value' => 10,
										'validators' => array(
											array('Int', true),
											array('GreaterThan', true, array(0)),
										),
								),
						),
				),
		),
  ),
)
?>