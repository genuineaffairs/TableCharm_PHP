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

$categories = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategories();
if (count($categories) != 0) {
  $categories_prepared[0] = "";
  foreach ($categories as $category) {
    $categories_prepared[$category->category_id] = $category->category_name;
  }
}

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => 'Page Profile Events',
        'description' => 'This widget forms the Events tab on the Page Profile and displays the events of the Page. It should be placed in the Tabbed Blocks area of the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepageevent.profile-sitepageevents',
        'defaultParams' => array(
            'title' => 'Events',
        ),
    ),
    array(
        'title' => 'Page Event Profile Photo',
        'description' => "Displays a Page event's photo on it's profile.",
        'category' => 'Page Event Profile',
        'type' => 'widget',
        'name' => 'sitepageevent.profile-photo',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => 'Page Event Profile Options',
        'description' => "Displays a Page event's options on it's profile.",
        'category' => 'Page Event Profile',
        'type' => 'widget',
        'name' => 'sitepageevent.profile-options',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => 'Page Event Profile Details',
        'description' => "Displays a Page event's details on it's profile.",
        'category' => 'Page Event Profile',
        'type' => 'widget',
        'name' => 'sitepageevent.profile-info',
        'defaultParams' => array(
            'title' => 'Page Events Details',
        ),
    ),
    array(
        'title' => 'Page Event Profile Rsvp',
        'description' => "Displays a Page event's rsvp on it's profile.",
        'category' => 'Page Event Profile',
        'type' => 'widget',
        'name' => 'sitepageevent.profile-rsvp',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
    'title' => 'Page Event Profile Photos',
    'description' => 'Displays a Page event\'s photos on it\'s profile.',
    'category' => 'Page Event Profile',
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
        'title' => 'Page Event Profile Guest Member',
        'description' => "Displays a Page event's guest members on it's profile.",
        'category' => 'Page Event Profile',
        'type' => 'widget',
        'name' => 'sitepageevent.profile-members',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => 'Page Event Profile Title',
        'description' => "Displays a Page event's title on it's profile.",
        'category' => 'Page Event Profile',
        'type' => 'widget',
        'name' => 'sitepageevent.profile-status',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => 'Page Profile Upcoming Events',
        'description' => "Displays list of page’s upcoming events. This widget should be placed on Page Profile.",
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepageevent.profile-events',
        'defaultParams' => array(
            'title' => 'Upcoming Events',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of events to show)',
                        'value' => 3,
												'validators' => array(
													array('Int', true),
													array('GreaterThan', true, array(0)),
												),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Page Event Profile Breadcrumb',
        'description' => "Displays a 'Breadcrumb' on Page event's profile. This breadcrumb contains links to the Page this event belongs to and all the events of that Page.",
        'category' => 'Page Event Profile',
        'type' => 'widget',
        'name' => 'sitepageevent.profile-breadcrumbevent',
        'defaultParams' => array(
            'title' => '',
        ),
    ),
    array(
        'title' => "Upcoming Events",        
        'description' => "Displays list of upcoming events of pages on the site.",
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepageevent.upcoming-sitepageevent',
        'defaultParams' => array(
            'title' => 'Upcoming Events',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of events to show)',
                        'value' => 3,
												'validators' => array(
													array('Int', true),
													array('GreaterThan', true, array(0)),
												),
                    ),
                ),
                array(
									'Select',
									'category_id',
									array(
											'label' => 'Category',
											'multiOptions' => $categories_prepared,
									)
							  ),
            ),
        ),
    ),

    array(
			'title' => 'Search Page Events form',
			'description' => 'Displays the form for searching Page Events on the basis of various filters. You can edit the fields to be available in this form.',
			'category' => 'Pages',
			'type' => 'widget',
			'name' => 'sitepageevent.search-sitepageevent',
			'defaultParams' => array(
					'title' => '',
          'search_column' => array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => '5', "5" => '6'),
					'titleCount' => true,

			),
			'adminForm' => array(
              'elements' => array(
							array(
									'MultiCheckbox',
									'search_column',
									array(
											'label' => 'Choose the fields that you want to be available in the Search Page Events form widget.',
											'multiOptions' => array("1" => "Show","2" => "Browse By", "3" => "Page Title", "4" => "Event Title", "5" => "Page Category", "6" => "Event Category"),
									),
							),
					),
			)
    ),

    array(
        'title' => 'Page Events',
        'description' => 'Displays the list of Events from Pages created on your community. This widget should be placed in the widgetized Page Events page. Results from the Search Page Events form are also shown here.',
        'category' => 'Pages',
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
                        'label' => 'Count',
                        'description' => '(number of events to show)',
                        'value' => 20,
												'validators' => array(
													array('Int', true),
													array('GreaterThan', true, array(0)),
												),
                    ),
                ),
            ),
        ),
    ),

    array(
        'title' => 'Sponsored Events',
        'description' => 'Displays the Events from Paid Pages. You can choose the number of entries to be shown.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepageevent.sitepage-sponsoredevent',
        'defaultParams' => array(
            'title' => 'Sponsored Events',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of events to show)',
                        'value' => 3,
												'validators' => array(
													array('Int', true),
													array('GreaterThan', true, array(0)),
												),
                    ),
                ),
                array(
									'Select',
									'category_id',
									array(
											'label' => 'Category',
											'multiOptions' => $categories_prepared,
									)
						    ),
                array(
									'Radio',
									'showevent',
									array(
											'label' => 'Show Events',
											'multiOptions' => array('upcoming' => 'Upcoming', 'overall' => 'Overall'),
											'value' => 'overall',
									)
							),
            ),
        ),
    ),

    array(
        'title' => 'Page’s Featured Events',
        'description' => "Displays Featured Page Events. You can mark Page Events as Featured from the “Manage Page Events” section in the Admin Panel of this extension. You can choose the number of entries to be shown.",
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepageevent.homefeaturelist-sitepageevents',
        'defaultParams' => array(
            'title' => 'Featured Events',
            'titleCount' => true,
            'showevent' => array('upcoming' => 'Upcoming', 'overall' => 'Overall'),
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of events to show)',
										'value' => 3,
										'validators' => array(
											array('Int', true),
											array('GreaterThan', true, array(0)),
										),
								),
						),
            array(
									'Select',
									'category_id',
									array(
											'label' => 'Category',
											'multiOptions' => $categories_prepared,
									)
					  ),
            array(
									'Radio',
									'showevent',
									array(
											'label' => 'Show Events',
											'multiOptions' => array('upcoming' => 'Upcoming', 'overall' => 'Overall'),
											'value' => 'overall',
									)
							),
					),
        ),
    ),

    array(
        'title' => 'Page’s Featured Events Slideshow',
        'description' => 'Displays featured events in an attractive slideshow. You can set the count of the number of events to show in this widget. If the total number of events featured are more than that count, then the events to be displayed will be sequentially picked up.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepageevent.featured-events-slideshow',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Featured Upcoming Events',
            'itemCountPerPage' => 10,
        ),
        'adminForm' => array(
					'elements' => array(
							array(
									'Select',
									'category_id',
									array(
											'label' => 'Category',
											'multiOptions' => $categories_prepared,
									)
							),
					),
			),
    ),

    array(
        'title' => 'Page’s Featured Events Carousel',
        'description' => 'This widget contains an attractive AJAX based carousel, showcasing the featured events on the site. Multiple settings of this widget makes it highly configurable.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepageevent.featured-events-carousel',
        'defaultParams' => array(
            'title' => 'Featured Upcoming Events',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'vertical',
                    array(
                        'label' => 'Carousel Type',
                        'multiOptions' => array(
                            '0' => 'Horizontal',
                            '1' => 'Vertical',
                        ),
                        'value' => 0,
                    )
                ),
                array(
									'Select',
									'category_id',
									array(
											'label' => 'Category',
											'multiOptions' => $categories_prepared,
									)
					      ),
                array(
                    'Text',
                    'inOneRow',
                    array(
                        'label' => 'Events in a Row',
                        'description' => '(number of events to show in one row. Note: This field is applicable only when you have selected ‘Horizontal’ in ‘Carousel Type’ field.)',
                        'value' => 3,
                    )
                ),
                array(
                    'Text',
                    'noOfRow',
                    array(
                        'label' => 'Rows',
                        'description' => '(number of rows in one view)',
                        'value' => 2,
                    )
                ),
                array(
                    'Text',
                    'interval',
                    array(
                        'label' => 'Speed',
                        'description' => '(transition interval between two slides in millisecs)',
                        'value' => 250,
                    )
                ),
            ),
        ),
    ),

    array(
        'title' => 'Page’s Event of the Day',
        'description' => 'Displays the Event of the Day as selected by the Admin from the widget settings section of Directory / Pages - Events Extension.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepageevent.event-of-the-day',
        'defaultParams' => array(
            'title' => 'Event of the Day'
        ),
    ),

    array(
        'title' => 'Browse Events',
        'description' => 'Displays the link to view Page’s Events Browse page.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepageevent.sitepageeventlist-link',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),

    array(
        'title' => 'Most Viewed Events',
        'description' => "Displays the Most Viewed Page Events. You can choose the number of entries to be shown.",
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepageevent.homeview-sitepageevents',
        'defaultParams' => array(
            'title' => 'Most Viewed Events',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of events to show)',
										'value' => 3,
										'validators' => array(
											array('Int', true),
											array('GreaterThan', true, array(0)),
										),
								),
						),
            array(
									'Select',
									'category_id',
									array(
											'label' => 'Category',
											'multiOptions' => $categories_prepared,
									)
					  ),
					),
        ),
    ),

    array(
        'title' => 'Page’s Ajax based Tabbed widget for Events',
        'description' => 'Displays the Recent, Most Liked, Most Viewed, Most Commented and Featured Events in separate AJAX based tabs. Settings for this widget are available in the Widget Settings section of Directory / Pages - Events Extension.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepageevent.list-events-tabs-view',
        'defaultParams' => array(
            'title' => 'Events',
            'margin_photo'=>12,
            'showViewMore'=>1
        ),
         'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'margin_photo',
                    array(
                        'label' => 'Horizontal Margin between Elements',
                        'description' => '(Horizontal margin in px between consecutive elements in this widget. You might want to change this value if the content of this widget is not coming properly on your site because of the column width in your theme.)',
                        'value' => 12,
                    )
                ),
                 array(
                  'Radio',
                  'showViewMore',
                  array(
                      'label' => 'Show "View More" link',
                      'multiOptions' => array(
                          '1' => 'Yes',
                          '0' => 'No',
                      ),
                  )
              ),
              array(
									'Select',
									'category_id',
									array(
											'label' => 'Category',
											'multiOptions' => $categories_prepared,
									)
					    ),
            ),
        ),
    ),

    array(
    'title' => 'Top Creators : Page Events',
    'description' => 'Displays the Pages which have the most number of Page Events added in them. Motivates Page Admins to add more content on your website.',
    'category' => 'Pages',
    'type' => 'widget',
    'name' => 'sitepageevent.topcreators-sitepageevent',
   // 'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Top Creators',
    ),
     'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of elements to show)',
										'value' => 5,
										'validators' => array(
											array('Int', true),
											array('GreaterThan', true, array(0)),
										),
								),
                array(
									'Select',
									'category_id',
									array(
											'label' => 'Category',
											'multiOptions' => $categories_prepared,
									)
					      ),
						),
					),
        ),
    'requirements' => array(
      'subject' => 'sitepageevent',
    ),
  ),
  
  
  
      array(
        'title' => 'By Locations',
        'description' => 'Displays a list of all the page events having location entered corresponding to them on the site. This widget should be placed on Browse Page Events’ Locations page.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepageevent.bylocation-event',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
//         'adminForm' => array(
// 					'elements' => array(
// 						array(
// 								'Radio',
// 								'order_by',
// 								array(
// 										'description' => 'Which all events do you want to display in this widget?',
// 										'multiOptions' => array(
// 												'2' => 'Only Upcoming Events',
// 												'1' => 'Only Past Events',
// 												'0' => 'Both Upcoming and Past Events',
// 										),
// 										'value' => 2,
// 								)
// 						),
// 					),
//         )
    ),
    array(
			'title' => 'Search Page Events Location Form',
			'description' => 'Displays the form for searching Page Events corresponding to location on the basis of various filters.',
			'category' => 'Pages',
			'type' => 'widget',
			'name' => 'sitepageevent.location-search',
			'defaultParams' => array(
				'title' => '',
				'titleCount' => true,
			),
    ),
)
?>