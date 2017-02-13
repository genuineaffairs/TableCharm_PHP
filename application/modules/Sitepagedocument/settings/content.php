<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.isActivate', 0);
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
        'title' => 'Page Profile Documents',
        'description' => 'This widget forms the Documents tab on the Page Profile and displays the documents of the Page. It should be placed in the Tabbed Blocks area of the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagedocument.profile-sitepagedocuments',
        'defaultParams' => array(
            'title' => 'Documents',
        ),
    ),
    array(
        'title' => 'Page Profile Recent Documents',
        'description' => 'Displays list of Page’s recent documents. This widget should be placed on Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagedocument.recent-sitepagedocuments',
        'defaultParams' => array(
            'title' => 'Most Recent Documents',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of documents to show)',
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
        'title' => 'Page Profile Popular Documents',
        'description' => 'Displays list of page’s most viewed documents. This widget should be placed on Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagedocument.popular-sitepagedocuments',
        'defaultParams' => array(
            'title' => 'Most Popular Documents',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of documents to show)',
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
        'title' => 'Page Profile Most Commented Documents',
        'description' => 'Displays list of page’s most commented documents. This widget should be placed on the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagedocument.comment-sitepagedocuments',
        'defaultParams' => array(
            'title' => 'Most Commented Documents',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of documents to show)',
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
        'title' => 'Page Profile Most Liked Documents',
        'description' => 'Displays list of page’s most liked documents. This widget should be placed on the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagedocument.like-sitepagedocuments',
        'defaultParams' => array(
            'title' => 'Most Liked Documents',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of documents to show)',
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
        'title' => 'Page Profile Top Rated Documents',
        'description' => 'Displays list of page’s top rated documents. This widget should be placed on Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagedocument.rate-sitepagedocuments',
        'defaultParams' => array(
            'title' => 'Top Rated Documents',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of documents to show)',
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
        'title' => 'Page Profile Featured Documents',
        'description' => 'Displays list of page’s featured documents. This widget should be placed on Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagedocument.featurelist-sitepagedocuments',
        'defaultParams' => array(
            'title' => 'Featured Documents',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of documents to show)',
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
        'title' => 'Recent Documents',
        'description' => 'Displays the recent documents of the site.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagedocument.homerecent-sitepagedocuments',
        'defaultParams' => array(
            'title' => 'Recent Documents'
        ),
         'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of documents to show)',
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
		'title' => 'Page Document Social Share Buttons Buttons',
		'description' => "Contains Social Sharing buttons and enables users to easily share Page Documents on their favorite Social Networks. This widget should be placed on the Page Document View Page. You can customize the code for social sharing buttons from Global Settings of this plugin by adding your own code generated from: <a href='http://www.addthis.com' target='_blank'>http://www.addthis.com</a>",
		'category' => 'Pages',
		'type' => 'widget',
		'name' => 'sitepagedocument.socialshare-sitepagedocuments',
		'defaultParams' => array(
				'title' => 'Social Share',
				'titleCount' => true,
		),
    'requirements' => array(
      'subject' => 'sitepagedocument',
    ),
		'adminForm' => array(
			'elements' => array(
				array(
						'Hidden',
						'nomobile',
						array(
								'label' => '',
						)
				),
			),
		),
	),
    array(
        'title' => 'Recent Documents from Page',
        'description' => 'Displays recent Page Documents corresponding to the Page of which the document is being viewed. You can choose the number of entries to be shown. This widget should be placed on Page Document View Page.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagedocument.viewrecent-sitepagedocuments',
        'defaultParams' => array(
            'title' => ''
        ),
         'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of documents to show)',
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
			'title' => 'Search Page Documents form',
			'description' => 'Displays the form for searching Page Documents on the basis of various filters. You can edit the fields to be available in this form.',
			'category' => 'Pages',
			'type' => 'widget',
			'name' => 'sitepagedocument.search-sitepagedocument',
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
											'label' => 'Choose the fields that you want to be available in the Search Page Documents form widget.',
											'multiOptions' => array("1" => "Show","2" => "Browse By", "3" => "Page Title", "4" => "Document Title", "5" => "Page Category", "6" => "Document Category"),
									),
							),
					),
			)
    ),

     array(
        'title' => 'Page Documents',
        'description' => 'Displays the list of Documents from Pages created on your community. This widget should be placed in the widgetized Page Documents page. Results from the Search Page Documents form are also shown here.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagedocument.sitepage-document',
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
                        'description' => '(number of documents to show)',
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
        'title' => 'Sponsored Documents',
        'description' => 'Displays the Documents from Paid Pages. You can choose the number of entries to be shown.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagedocument.sitepage-sponsoreddocument',
        'defaultParams' => array(
            'title' => 'Sponsored Documents',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of documents to show)',
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
        'title' => 'Featured Documents',
        'description' => "Displays Featured Page Documents. You can mark Page Documents as Featured from the “Manage Page Documents” section in the Admin Panel of this extension. You can choose the number of entries to be shown.",
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagedocument.homefeaturelist-sitepagedocuments',
        'defaultParams' => array(
            'title' => 'Featured Documents',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of documents to show)',
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
   
//     array(
//         'title' => 'Highlighted Documents',
//         'description' => "Displays Highlighted Page Documents. You can mark Page Documents as Highlighted from the “Manage Page Documents” section in the Admin Panel of this extension. You can choose the number of entries to be shown.",
//         'category' => 'Pages',
//         'type' => 'widget',
//         'name' => 'sitepagedocument.homehighlightlist-sitepagedocuments',
//         'defaultParams' => array(
//             'title' => 'Highlighted Documents',
//             'titleCount' => true,
//         ),
//         'adminForm' => array(
// 					'elements' => array(
// 						array(
// 								'Text',
// 								'itemCount',
// 								array(
// 										'label' => 'Count',
// 										'description' => '(number of documents to show)',
// 										'value' => 3,
// 										'validators' => array(
// 											array('Int', true),
// 											array('GreaterThan', true, array(0)),
// 										),
// 								),
// 						),
// 					),
//         ),
//     ),

    array(
        'title' => 'Most Commented Documents',
        'description' => "Displays the Most Commented Page Documents. You can choose the number of entries to be shown.",
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagedocument.homecomment-sitepagedocuments',
        'defaultParams' => array(
            'title' => 'Most Commented Documents',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of documents to show)',
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
        'title' => 'Most Popular Documents',
        'description' => "Displays the Most Viewed Page Documents. You can choose the number of entries to be shown.",
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagedocument.homepopular-sitepagedocuments',
        'defaultParams' => array(
            'title' => 'Most Popular Documents',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of documents to show)',
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
        'title' => 'Most Liked Documents',
        'description' => "Displays the Most Liked Page Documents. You can choose the number of entries to be shown.",
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagedocument.homelike-sitepagedocuments',
        'defaultParams' => array(
            'title' => 'Most Liked Documents',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of documents to show)',
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
        'title' => 'Top Rated Documents',
        'description' => "Displays the Top Rated Page Documents. You can choose the number of entries to be shown.",
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagedocument.homerate-sitepagedocuments',
        'defaultParams' => array(
            'title' => 'Top Rated Documents',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of documents to show)',
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
			'title' => 'Page Document View',
			'description' => "This widget should be placed on the Page Document View Page.",
      'category' => 'Pages',
			'type' => 'widget',
			'name' => 'sitepagedocument.document-content',
			'defaultParams' => array(
					'title' => '',
					'titleCount' => true,
			),
	),

  array(
			'title' => 'Page Document View Options',
			'description' => 'Displays various action link options to users viewing a Page Document. This Widget should be placed on the Page Document View Page in the right column, below the Page Document\'s Owner Photo.',
			'category' => 'Pages',
			'type' => 'widget',
			'name' => 'sitepagedocument.options-documents',
			'requirements' => array(
				'subject' => 'sitepagedocument_document',
			),
	),

  array(
			'title' => 'Page Document\'s Owner Photo',
			'description' => 'Displays page owner\'s photo with page owner\'s name to users viewing Page Document. This widget should be placed on Page Document View Page.',
			'category' => 'Pages',
			'type' => 'widget',
			'name' => 'sitepagedocument.document-owner-photo-documents',
			'requirements' => array(
				'subject' => 'sitepagedocument_document',
			),
	),

  array(
        'title' => 'Document of the Day',
        'description' => 'Displays the Document of the Day as selected by the Admin from the widget settings section of Directory / Pages - Documents Extension.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagedocument.document-of-the-day',
        'defaultParams' => array(
            'title' => 'Document of the Day'
        ),
    ),

     array(
        'title' => 'Page’s Featured Documents Slideshow',
        'description' => 'Displays featured documents in an attractive slideshow. You can set the count of the number of documents to show in this widget. If the total number of documents featured are more than that count, then the documents to be displayed will be sequentially picked up.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagedocument.featured-documents-slideshow',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Featured Documents',
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
        'title' => 'Page’s Featured Documents Carousel',
        'description' => 'This widget contains an attractive AJAX based carousel, showcasing the featured documents on the site. Multiple settings of this widget makes it highly configurable.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagedocument.featured-documents-carousel',
        'defaultParams' => array(
            'title' => 'Featured Documents',
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
                        'label' => 'Documents in a Row',
                        'description' => '(number of documents to show in one row. Note: This field is applicable only when you have selected ‘Horizontal’ in ‘Carousel Type’ field.)',
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
        'title' => 'Page’s Ajax based Tabbed widget for Documents',
        'description' => 'Displays the Recent, Most Liked, Most Viewed, Most Commented and Featured Documents in separate AJAX based tabs. Settings for this widget are available in the Widget Settings section of Directory / Pages - Documents Extension.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagedocument.list-documents-tabs-view',
        'defaultParams' => array(
            'title' => 'Documents',
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
									'Select',
									'category_id',
									array(
											'label' => 'Category',
											'multiOptions' => $categories_prepared,
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
              )
            ),
        ),
    ),
 
    array(
        'title' => 'Browse Documents',
        'description' => 'Displays the link to view Page’s Documents Browse page.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagedocument.sitepagedocumentlist-link',
        'defaultParams' => array(
            'title' => '',
            'titleCount' => true,
        ),
    ),

    array(
        'title' => 'Page Profile Highlighted Documents',
        'description' => "Displays list of page's highlighted documents. This widget should be placed on the Page Profile.",
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagedocument.highlightlist-sitepagedocuments',
        'defaultParams' => array(
            'title' => 'Highlighted Page Documents',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of documents to show)',
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
    'title' => 'Top Creators : Page Documents',
    'description' => 'Displays the Pages which have the most number of Page Documents added in them. Motivates Page Admins to add more content on your website.',
    'category' => 'Pages',
    'type' => 'widget',
    'name' => 'sitepagedocument.topcreators-sitepagedocument',
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
      'subject' => 'sitepagedocument',
    ),
  ),

)
?>