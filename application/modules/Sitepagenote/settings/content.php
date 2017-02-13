<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Content.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagenote.isActivate', 0);
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
        'title' => 'Page Profile Notes',
        'description' => 'This widget forms the Notes tab on the Page Profile and displays the notes of the Page. It should be placed in the Tabbed Blocks area of the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagenote.profile-sitepagenotes',
        'defaultParams' => array(
            'title' => 'Notes',
        ),
    ),
    array(
        'title' => 'Page Profile Most Commented Notes',
        'description' => 'Displays list of a Page\'s most commented notes. This widget should be placed on the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagenote.comment-sitepagenotes',
        'defaultParams' => array(
            'title' => 'Most Commented Notes',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of notes to show)',
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
        'title' => 'Page Profile Most Recent Notes',
        'description' => 'Displays list of a Page\'s most recent notes. This widget should be placed on the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagenote.recent-sitepagenotes',
        'defaultParams' => array(
            'title' => 'Most Recent Notes',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of notes to show)',
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
        'title' => 'Page Profile Most Liked Notes',
        'description' => 'Displays list of a Page\'s most liked notes. This widget should be placed on the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagenote.like-sitepagenotes',
        'defaultParams' => array(
            'title' => 'Most Liked Notes',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of notes to show)',
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
        'title' => 'Recent Notes',
        'description' => 'Displays list of recent notes on the site.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagenote.homerecent-sitepagenotes',
        'defaultParams' => array(
            'title' => 'Recent Notes'
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of notes to show)',
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
        'title' => 'Page Notes',
        'description' => 'Displays a list of all the page notes on site.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagenote.sitepage-note',
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
                        'description' => '(number of notes to show)',
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
			'title' => 'Page Note View',
			'description' => "Displays the list of Notes from Pages created on your community. This widget should be placed in the widgetized Page Notes page. Results from the Search Page Notes form are also shown here.",
      'category' => 'Pages',
			'type' => 'widget',
			'name' => 'sitepagenote.note-content',
			'defaultParams' => array(
					'title' => '',
					'titleCount' => true,
			),
	),

    array(
        'title' => 'Most Commented Notes',
        'description' => 'Displays the Most Commented Page Notes. You can choose the number of entries to be shown',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagenote.homecomment-sitepagenotes',
        'defaultParams' => array(
            'title' => 'Most Commented Notes'
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of notes to show)',
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
        'title' => 'Most Liked Notes',
        'description' => 'Displays the Most Liked Page Notes. You can choose the number of entries to be shown.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagenote.homelike-sitepagenotes',
        'defaultParams' => array(
            'title' => 'Most Liked Notes'
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of notes to show)',
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
        'title' => 'Sponsored Notes',
        'description' => "Displays the Notes from Paid Pages. You can choose the number of entries to be shown",
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagenote.sitepage-sponsorednote',
        'defaultParams' => array(
            'title' => 'Sponsored Notes',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of notes to show)',
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
			'title' => 'Search Page Notes form',
			'description' => 'Displays the form for searching Page Notes on the basis of various filters. You can edit the fields to be available in this form.',
			'category' => 'Pages',
			'type' => 'widget',
			'name' => 'sitepagenote.search-sitepagenote',
			'defaultParams' => array(
					'title' => '',
          'search_column' => array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => '5', "5" => "6"),
					'titleCount' => true,

			),
			'adminForm' => array(
              'elements' => array(
							array(
									'MultiCheckbox',
									'search_column',
									array(
											'label' => 'Choose the fields that you want to be available in the Search Page Notes form widget.',
											'multiOptions' => array("1" => "Show","2" => "Browse By", "3" => "Page Title", "4" => "Note Keywords", "5" => "Page Category", "6" => "Note Category"),
									),
							),
					),
			)
    ),

    array(
    'title' => 'Related Notes',
    'description' => 'Displays a list of other page notes that are similar to the current page notes, based on tags.',
    'category' => 'Pages',
    'type' => 'widget',
    'name' => 'sitepagenote.show-same-tags',
   // 'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Related Notes',
    ),
     'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of notes to show)',
										'value' => 3,
										'validators' => array(
											array('Int', true),
											array('GreaterThan', true, array(0)),
										),
								),
						),
					),
        ),
    'requirements' => array(
      'subject' => 'sitepagenote',
    ),
  ),
  array(
  'title' => 'Popular Note Tags',
  'description' => 'Shows popular tags with frequency.',
  'category' => 'Pages',
  'type' => 'widget',
  'name' => 'sitepagenote.tagcloud-sitepagenote',
  'adminForm' => array(
       'elements' => array(
         array(
           'hidden',
           'title',
           array(
             'label' => ''
           )
         ),
         array(
           'hidden',
           'nomobile',
           array(
             'label' => ''
           )
         ),
         array(
           'hidden',
           'execute',
           array(
             'label' => ''
           )
         ),
         array(
           'hidden',
           'cancel',
           array(
             'label' => ''
           )
         ),
       )
     ),
    ),
   
    array(
        'title' => 'Page’s Featured Notes Slideshow',
        'description' => 'Displays featured notes in an attractive slideshow. You can set the count of the number of notes to show in this widget. If the total number of notes featured are more than that count, then the notes to be displayed will be sequentially picked up.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagenote.featured-notes-slideshow',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Featured Notes',
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
        'title' => 'Page’s Featured Notes Carousel',
        'description' => 'This widget contains an attractive AJAX based carousel, showcasing the featured notes on the site. Multiple settings of this widget makes it highly configurable.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagenote.featured-notes-carousel',
        'defaultParams' => array(
            'title' => 'Featured Notes',
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
                    'Text',
                    'inOneRow',
                    array(
                        'label' => 'Notes in a Row',
                        'description' => '(number of notes to show in one row. Note: This field is applicable only when you have selected ‘Horizontal’ in ‘Carousel Type’ field.)',
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
        'title' => 'Page’s Ajax based Tabbed widget for Notes',
        'description' => 'Displays the Recent, Most Liked, Most Viewed, Most Commented and Featured Notes in separate AJAX based tabs. Settings for this widget are available in the Widget Settings section of Directory / Pages - Notes Extension.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagenote.list-notes-tabs-view',
        'defaultParams' => array(
            'title' => 'Notes',
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
							)
            ),
        ),
    ),

  array(
        'title' => 'Browse Notes',
        'description' => 'Displays the link to view Page’s Notes Browse page.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagenote.sitepagenotelist-link',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),

  array(
        'title' => 'Page’s Note of the Day',
        'description' => 'Displays the Note of the Day as selected by the Admin from the widget settings section of Directory / Pages - Notes Extension.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagenote.note-of-the-day',
        'defaultParams' => array(
            'title' => 'Note of the Day'
        ),
    ),
  
    array(
        'title' => 'Featured Notes',
        'description' => "Displays Featured Page Notes. You can mark Page Notes as Featured from the “Manage Page Notes” section in the Admin Panel of this extension. You can choose the number of entries to be shown.",
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagenote.homefeaturelist-sitepagenotes',
        'defaultParams' => array(
            'title' => 'Featured Notes',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of notes to show)',
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
    'title' => 'Top Creators : Page Notes',
    'description' => 'Displays the Pages which have the most number of Page Notes added in them. Motivates Page Admins to add more content on your website.',
    'category' => 'Pages',
    'type' => 'widget',
    'name' => 'sitepagenote.topcreators-sitepagenote',
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
    'requirements' => array(
      'subject' => 'sitepagenote',
    ),
  ),
)
?>