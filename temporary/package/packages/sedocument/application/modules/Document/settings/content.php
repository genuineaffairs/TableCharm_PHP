<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

$categories = Engine_Api::_()->getDbTable('categories', 'document')->getCategories(0, 0);
if (count($categories) != 0) {
  $categories_prepared[0] = "";
  foreach ($categories as $category) {
    $categories_prepared[$category->category_id] = $category->category_name;
  }
}

$category_documents_multioptions = array(
		'views' => $view->translate('Views'),
		'like_count' => $view->translate('Likes'),
		'comment_count' => $view->translate('Comments'),
);

return array(
  array(
    'title' => $view->translate('Profile Documents'),
    'description' => $view->translate('Displays a member\'s documents on their profile.'),
    'category' => $view->translate('Documents'),
    'type' => 'widget',
    'name' => 'document.profile-documents',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => $view->translate('Documents'),
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title' => $view->translate('Member’s Profile Document'),
    'description' => $view->translate("Displays the Profile Document on member profile as chosen by the member. This widget should be placed in the tabbed blocks area of Member Profile Page."),
    'category' => $view->translate('Documents'),
    'type' => 'widget',
    'name' => 'document.profile-doc-documents',
    'defaultParams' => array(
      'title' => $view->translate('Profile Document'),
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
		'adminForm' => array(
				'elements' => array(
					array(
							'Text',
							'documentViewerHeight',
							array(
									'description' => $view->translate('What should be the height in pixels of the document viewer?'),
									'value' => 600,
							)
					),
					array(
							'Text',
							'documentViewerWidth',
							array(
									'description' => $view->translate('What should be the width in pixels of the document viewer?'),
									'value' => 730,
							)
					),
					array(
							'Radio',
							'download',
							array(
									'label' => $view->translate("Do you want to show the 'Download' link to users in this widget? [Note: This link is also dependent on Member Level Settings.]"),
									'multiOptions' => array(
											1 => 'Yes',
											0 => 'No'
									),
									'value' => 1,
							)
					),
					array(
							'Radio',
							'email',
							array(
									'label' => $view->translate("Do you want to show the 'Email Document' link to users in this widget? [Note: This link is also dependent on Member Level Settings.]"),
									'multiOptions' => array(
											1 => 'Yes',
											0 => 'No'
									),
									'value' => 1,
							)
					),
					array(
							'Radio',
							'share',
							array(
									'label' => $view->translate("Do you want to show the 'Share' link to users in this widget? [Note: This link is also dependent on Global Settings.]"),
									'multiOptions' => array(
											1 => 'Yes',
											0 => 'No'
									),
									'value' => 1,
							)
					),
					array(
							'Radio',
							'report',
							array(
									'label' => $view->translate("Do you want to show the 'Report' link to users in this widget? [Note: This link is also dependent on Global Settings.]"),
									'multiOptions' => array(
											1 => 'Yes',
											0 => 'No'
									),
									'value' => 1,
							)
					),
					array(
							'Radio',
							'comment_like',
							array(
									'label' => $view->translate("Do you want to show the Comments box to users in this widget (This box also has the link for “Like”)? [Note: These are also dependent on Member Level Settings.]"),
									'multiOptions' => array(
											1 => 'Yes',
											0 => 'No'
									),
									'value' => 1,
							)
					),
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
    'title' => $view->translate('Document Viewer'),
    'description' => $view->translate('Displays the document viewer for viewing document. You can select the viewer type from ‘Global Settings’ of this plugin. This widget should be placed on Document View Page'),
    'category' => $view->translate('Documents'),
    'type' => 'widget',
    'name' => 'document.document-view-documents',
    'defaultParams' => array(
      'title' => '',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'document',
    ),
		'adminForm' => array(
				'elements' => array(
					array(
							'Text',
							'documentViewerHeight',
							array(
									'description' => $view->translate('What should be the height in pixels of the document viewer?'),
									'value' => 600,
							)
					),
					array(
							'Text',
							'documentViewerWidth',
							array(
									'description' => $view->translate('What should be the width in pixels of the document viewer?'),
									'value' => 730,
							)
					),
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
			'title' => $view->translate('Document View Page Options'),
			'description' => $view->translate('Displays the various action links that can be performed on the document on document’s view page (edit, download, report, share, etc ). This widget should be placed on the Document View Page in the right column, below the Document Owner’s photo. You can manage the action links available here from the Menu Editor section by choosing Document View Page Options Menu.'),
			'category' => $view->translate('Documents'),
			'type' => 'widget',
			'name' => 'document.options-documents',
			'requirements' => array(
				'subject' => 'document',
			),
	),
  array(
    'title' => $view->translate('Recent Documents'),
    'description' => $view->translate('Displays a list of recently created documents.'),
    'category' => $view->translate('Documents'),
    'type' => 'widget',
    'name' => 'document.recent-documents',
    'defaultParams' => array(
      'title' => $view->translate('Recent Documents'),
    ),
		'adminForm' => array(
				'elements' => array(
					array(
							'Text',
							'itemCount',
							array(
									'label' => $view->translate('Count'),
									'description' => $view->translate('(number of documents to show)'),
									'value' => 3,
							)
					),
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
		'title' => $view->translate('Document Social Share Buttons'),
		'description' => $view->translate("Contains Social Sharing buttons and enables users to easily share Documents on their favorite Social Networks. This widget should be placed on the Document View Page. You can customize the code for social sharing buttons from Global Settings of this plugin by adding your own code generated from: <a href='http://www.addthis.com' target='_blank'>http://www.addthis.com</a>"),
		'category' => $view->translate('Documents'),
		'type' => 'widget',
		'name' => 'document.socialshare-documents',
		'defaultParams' => array(
				'title' => $view->translate('Social Share'),
				'titleCount' => true,
		),
    'requirements' => array(
      'subject' => 'document',
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
			'title' => $view->translate("Owner’s Document Archives"),
			'description' => $view->translate('Displays the month-wise archives for the documents posted by the document owner. This widget should be placed on Document View Page.'),
			'category' => $view->translate('Documents'),
			'type' => 'widget',
			'name' => 'document.archives-documents',
			'defaultParams' => array(
					'title' => $view->translate('Archives'),
					'titleCount' => true,
			),
	),
	array(
			'title' => $view->translate('Owner’s Photo'),
			'description' => $view->translate('Displays the document owner’s photo with owner’s name. This widget should be placed in the right column of Document View Page.'),
			'category' => $view->translate('Documents'),
			'type' => 'widget',
			'name' => 'document.document-owner-photo-documents',
			'requirements' => array(
				'subject' => 'document',
			),
	),
  array(
    'title' => $view->translate("Owner’s Documents"),
    'description' => $view->translate('Displays a list of owner’s other documents. This widget should be placed on Document View Page.'),
    'category' => $view->translate('Documents'),
    'type' => 'widget',
    'name' => 'document.document-owner-documents',
    'defaultParams' => array(
      'title' => '',
    ),
    'requirements' => array(
      'subject' => 'document',
    ),
		'adminForm' => array(
				'elements' => array(
					array(
							'Text',
							'itemCount',
							array(
									'label' => $view->translate('Count'),
									'description' => $view->translate('(number of documents to show)'),
									'value' => 3,
							)
					),
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
    'title' => $view->translate('Popular Documents'),
    'description' => $view->translate('Displays a list of most viewed documents.'),
    'category' => $view->translate('Documents'),
    'type' => 'widget',
    'name' => 'document.popular-documents',
    'defaultParams' => array(
      'title' => $view->translate('Popular Documents'),
      'titleCount' => true,
    ),
		'adminForm' => array(
				'elements' => array(
					array(
							'Text',
							'itemCount',
							array(
									'label' => $view->translate('Count'),
									'description' => $view->translate('(number of documents to show)'),
									'value' => 3,
							)
					),
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
    'title' => $view->translate('Most Commented Documents'),
    'description' => $view->translate('Displays a list of most commented documents.'),
    'category' => $view->translate('Documents'),
    'type' => 'widget',
    'name' => 'document.comment-documents',
    'defaultParams' => array(
      'title' => $view->translate('Most Commented Documents'),
      'titleCount' => true,
    ),
		'adminForm' => array(
				'elements' => array(
					array(
							'Text',
							'itemCount',
							array(
									'label' => $view->translate('Count'),
									'description' => $view->translate('(number of documents to show)'),
									'value' => 3,
							)
					),
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
    'title' => $view->translate('Most Liked Documents'),
    'description' => $view->translate('Displays a list of most liked documents.'),
    'category' => $view->translate('Documents'),
    'type' => 'widget',
    'name' => 'document.like-documents',
    'defaultParams' => array(
      'title' => $view->translate('Most Liked Documents'),
      'titleCount' => true,
    ),
		'adminForm' => array(
				'elements' => array(
					array(
							'Text',
							'itemCount',
							array(
									'label' => $view->translate('Count'),
									'description' => $view->translate('(number of documents to show)'),
									'value' => 3,
							)
					),
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
    'title' => $view->translate('Top Rated Documents'),
    'description' => $view->translate('Displays a list of top rated documents.'),
    'category' => $view->translate('Documents'),
    'type' => 'widget',
    'name' => 'document.rate-documents',
    'defaultParams' => array(
      'title' => $view->translate('Top Rated Documents'),
      'titleCount' => true,
    ),
		'adminForm' => array(
				'elements' => array(
					array(
							'Text',
							'itemCount',
							array(
									'label' => $view->translate('Count'),
									'description' => $view->translate('(number of documents to show)'),
									'value' => 3,
							)
					),
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
			'title' => $view->translate('Ajax Based Recently Posted, Popular, Random, Featured and Sponsored Documents'),
			'description' => $view->translate("Displays the recently posted, popular, random, featured and sponsored documents in a block in separate ajax based tabs respectively."),
			'category' => $view->translate('Documents'),
			'type' => 'widget',
			'name' => 'document.ajax-home-documents',
			'defaultParams' => array(
					'title' => "",
					'titleCount' => "",
					'layouts_views' => array("0" => "1", "1" => "2", "2" => "3"),
					'layouts_oder' => 1,
					'layouts_tabs' => array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => '5'),
					'recent_order' => 1,
					'popular_order' => 2,
					'random_order' => 3,
					'featured_order' => 4,
					'sponosred_order' => 5,
					'list_limit' => 10,
					'grid_limit' => 15,
			),
			'adminForm' => array(
					'elements' => array(
							array(
									'MultiCheckbox',
									'layouts_views',
									array(
											'label' => $view->translate('Choose the view types that you want to be available for documents on the documents home and browse documents.'),
											'multiOptions' => array("1" => "List View", "2" => "Grid View"),
									),
							),
							array(
									'Radio',
									'layouts_oder',
									array(
											'label' => $view->translate('Select a default view type for Documents.'),
											'multiOptions' => array("1" => "List View", "2" => "Grid View"),
									)
							),
							array(
									'Text',
									'list_limit',
									array(
											'label' => $view->translate('List View (Limit)'),
											'validators' => array(
													array('Int', true),
													array('GreaterThan', true, array(0)),
											),
									),
							),
							array(
									'Text',
									'grid_limit',
									array(
											'label' => $view->translate('Grid View (Limit)'),
											'validators' => array(
													array('Int', true),
													array('GreaterThan', true, array(0)),
											),
									),
							),
							array(
									'MultiCheckbox',
									'layouts_tabs',
									array(
											'label' => $view->translate('Choose the ajax tabs that you want to be there in the Main Documents Home Widget.'),
											'multiOptions' => array("1" => "Recent", "2" => "Most Popular", "3" => "Random", "4" => "Featured", "5" => "Sponsored"),
									),
							),
							array(
									'Text',
									'recent_order',
									array(
											'label' => $view->translate('Recent Tab (order)'),
									),
							),
							array(
									'Text',
									'popular_order',
									array(
											'label' => $view->translate('Most Popular Tab (order)'),
									),
							),
							array(
									'Text',
									'random_order',
									array(
											'label' => $view->translate('Random Tab (order)'),
									),
							),
							array(
									'Text',
									'featured_order',
									array(
											'label' => $view->translate('Featured Tab (order)'),
									),
							),
							array(
									'Text',
									'sponosred_order',
									array(
											'label' => $view->translate('Sponosred Tab (order)'),
									),
							),
							array(
									'Hidden',
									'nomobile',
									array(
											'label' => '',
									)
							),
					)
			),
	),
	array(
			'title' => $view->translate('Sponsored Documents Carousel'),
			'description' => $view->translate('This widget contains an attractive AJAX based carousel, showcasing the sponsored Documents on the site.'),
			'category' => $view->translate('Documents'),
			'type' => 'widget',
			'name' => 'document.sponsored-documents',
			'defaultParams' => array(
					'title' => $view->translate('Sponsored Documents'),
					'titleCount' => true,
			),
			'adminForm' => array(
					'elements' => array(
							array(
									'Text',
									'itemCount',
									array(
											'label' => $view->translate('Count'),
											'description' => $view->translate('(number of documents to show)'),
											'value' => 4,
											'validators' => array(
												array('Int', true),
												array('GreaterThan', true, array(0)),
											),
									)
							),
							array(
									'Text',
									'interval',
									array(
											'label' => $view->translate('Sponsored Carousel Speed'),
											'description' => $view->translate('(What maximum Carousel Speed should be applied to the sponsored widget?)'),
											'value' => 300,
											'validators' => array(
												array('Int', true),
												array('GreaterThan', true, array(0)),
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
							array(
									'Text',
									'truncation',
									array(
											'label' => $view->translate('Title Truncation Limit'),
											'description' => $view->translate('(What maximum limit should be applied to the number of characters in the titles of items in the Sponsored widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.))'),
											'value' => 18,
											'validators' => array(
												array('Int', true),
												array('GreaterThan', true, array(0)),
											),
									)
							),
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
			'title' => $view->translate('Message for No Documents'),
			'description' => $view->translate('Displays a message to users when there are no documents. This widget should be placed in the top of the middle column of Documents Home page.'),
			'category' => $view->translate('Documents'),
			'type' => 'widget',
			'name' => 'document.zero-documents',
	),
	array(
    'title' => $view->translate('Featured Documents Carousel'),
    'description' => $view->translate('Displays a list of featured documents in carousel.'),
    'category' => $view->translate('Documents'),
    'type' => 'widget',
    'name' => 'document.featured-documents',
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
									'description' => $view->translate('(number of documents to show)'),
									'value' => 15,
							)
					),
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
    'title' => $view->translate('Featured Documents'),
    'description' => $view->translate('Displays a list of featured documents.'),
    'category' => $view->translate('Documents'),
    'type' => 'widget',
    'name' => 'document.featurelist-documents',
    'defaultParams' => array(
      'title' => $view->translate('Featured Documents'),
      'titleCount' => true,
    ),
		'adminForm' => array(
				'elements' => array(
					array(
							'Text',
							'itemCount',
							array(
									'label' => $view->translate('Count'),
									'description' => $view->translate('(number of documents to show)'),
									'value' => 3,
							)
					),
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
    'title' => $view->translate('Browse Documents'),
    'description' => $view->translate('Displays a list of all documents.'),
    'category' => $view->translate('Documents'),
    'type' => 'widget',
    'name' => 'document.browse-documents',
    'defaultParams' => array(
      'title' => '',
      'titleCount' => true,
			'orderby' => 'document_id'
    ),
		'adminForm' => array(
				'elements' => array(
					array(
							'Text',
							'itemCount',
							array(
									'label' => $view->translate('Count'),
									'description' => $view->translate('(number of documents to show)'),
									'value' => 10,
							)
					),
					array(
							'Radio',
							'orderby',
							array(
									'label' => $view->translate('Default Ordering of Documents'),
									'multiOptions' => array("document_id" => "All documents in descending order of creation.", 
																					"views" => "All documents in descending order of views.",
																					"document_title"	=> "All documents in alphabetical order.",
                                          "sponsored" => "Sponsored documents followed by others in descending order of creation.",
                                          "featured" => "Featured documents followed by others in descending order of creation.",
                                          "fespfe" => "Sponsored & Featured documents followed by Sponsored documents followed by Featured documents followed by others in descending order of creation.",
                                          "spfesp" => "Featured & Sponsored documents followed by Featured documents followed by Sponsored documents followed by others in descending order of creation."
																				),
									'value' => 'document_id',
							)
					),
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
    'title' => $view->translate('Search Documents form'),
    'description' => $view->translate('Displays search form.'),
    'category' => $view->translate('Documents'),
    'type' => 'widget',
    'name' => 'document.search-documents',
  ),
  array(
    'title' => $view->translate('Search Text Box'),
    'description' => $view->translate('Displays search box at document view page. This widget should be placed on Document View Page in right/left column'),
    'category' => $view->translate('Documents'),
    'type' => 'widget',
    'name' => 'document.search-box-documents',
    'defaultParams' => array(
      'title' => $view->translate('Search Documents'),
      'titleCount' => true,
    ),
  ),
  array(
    'title' => $view->translate('Popular Tags'),
    'description' => $view->translate('Shows popular tags with frequency.'),
    'category' => $view->translate('Documents'),
    'type' => 'widget',
    'name' => 'document.tagcloud-documents',
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
									'description' => $view->translate('(number of tags to show)'),
									'value' => 100,
							)
					),
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
    'title' => $view->translate('Create New Document'),
    'description' => $view->translate('Displays a create document link.( Create link will be visible if user level is allowed to create documents )'),
    'category' => $view->translate('Documents'),
    'type' => 'widget',
    'name' => 'document.create-documents',
  ),
  array(
    'title' => $view->translate('Navigation Tabs'),
    'description' => $view->translate('Display navigation tabs'),
    'category' => $view->translate('Documents'),
    'type' => 'widget',
    'name' => 'document.navigation-documents',
  ),
	array(
			'title' => $view->translate('Document of the Day'),
			'description' => $view->translate("Displays the Document of the Day as selected by the Admin from the 'Document of the day' section of this plugin."),
			'category' => $view->translate('Documents'),
			'type' => 'widget',
			'name' => 'document.day-item-document',
			'defaultParams' => array(
					'title' => $view->translate('Document of the Day'),
					'titleCount' => true,
			),
	),
	array(
			'title' => $view->translate('Featured Documents Slideshow'),
			'description' => $view->translate('Displays the Featured Documents in the form of an attractive Slideshow with interactive controls.'),
			'category' => $view->translate('Documents'),
			'type' => 'widget',
			'name' => 'document.slideshow-featured-documents',
			'defaultParams' => array(
					'title' => $view->translate('Featured Documents'),
					'titleCount' => true,
			),
			'adminForm' => array(
					'elements' => array(
							array(
									'Text',
									'itemCount',
									array(
										'label' => $view->translate('Count'),
										'description' => $view->translate('(number of documents to show)'),
										'value' => 10,
										'validators' => array(
											array('Int', true),
											array('GreaterThan', true, array(0)),
										),
									)
							),
							array(
									'Select',
									'category_id',
									array(
											'label' => $view->translate('Category'),
											'multiOptions' => $categories_prepared,
									)
							),
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
			'title' => $view->translate('Categories, Sub-categories and 3<sup>rd</sup> Level-categories (sidebar)'),
			'description' => $view->translate('Displays the Categories, Sub-categories and 3<sup>rd</sup> Level-categories of documents in an expandable form. Clicking on them will redirect the viewer to the list of documents created in that category.'),
			'category' => $view->translate('Documents'),
			'type' => 'widget',
			'name' => 'document.sidebar-categories-documents',
			'defaultParams' => array(
					'title' => $view->translate('Categories'),
					'titleCount' => true,
			),
	),
	array(
			'title' => $view->translate('Categories, Sub-categories and 3<sup>rd</sup> Level-categories'),
			'description' => $view->translate('Displays the Categories, Sub-categories and 3<sup>rd</sup> Level-categories of documents in an expandable form. Clicking on them will redirect the viewer to the list of documents created in that category.'),
			'category' => $view->translate('Documents'),
			'type' => 'widget',
			'name' => 'document.middle-column-categories-documents',
			'defaultParams' => array(
					'title' => $view->translate('Categories'),
					'titleCount' => true,
			),
			'adminForm' => array(
					'elements' => array(
							array(
									'Radio',
									'showAllCategories',
									array(
											'label' => $view->translate('Do you want all the categories, sub-categories and 3rd level categories to be shown to the users even if they have 0 documents in them?'),
											'multiOptions' => array(
													1 => 'Yes',
													0 => 'No'
											),
											'value' => 0,
									)
							),					
							array(
									'Radio',
									'show2ndlevelCategory',
									array(
											'label' => $view->translate('Do you want to show 2nd level category to the viewer?'),
											'multiOptions' => array(
													1 => 'Yes',
													0 => 'No'
											),
											'value' => 1,
									)
							),					
							array(
									'Radio',
									'show3rdlevelCategory',
									array(
											'label' => $view->translate('Do you want to show 3rd level category to the viewer?'),
											'multiOptions' => array(
													1 => 'Yes',
													0 => 'No'
											),
											'value' => 0,
									)
							),
							array(
									'Hidden',
									'nomobile',
									array(
											'label' => '',
									)
							),
					)
			),
	),
	array(
			'title' => $view->translate('Categorically Popular Documents'),
			'description' => $view->translate('This attractive widget categorically displays the most popular documents on your site. It displays 5 Documents for each category. From the edit popup of this widget, you can choose the number of categories to show, criteria for popularity and the duration for consideration of popularity.'),
			'category' => $view->translate('Documents'),
			'type' => 'widget',
			'name' => 'document.categorized-documents',
			'defaultParams' => array(
					'title' => $view->translate('Categorically Popular Documents'),
					'titleCount' => true,
			),
			'adminForm' => array(
					'elements' => array(
							array(
									'Text',
									'itemCount',
									array(
											'label' => $view->translate('Category Count'),
											'description' => $view->translate('No. of Categories to show. Enter 0 for showing all categories.'),
											'value' => 0,
									)
							),
							array(
									'Text',
									'documentCount',
									array(
											'label' => $view->translate('Documents Count per Category'),
											'description' => $view->translate('No. of Documents to be shown in each Category.'),
											'value' => 5,
											'validators' => array(
												array('Int', true),
												array('GreaterThan', true, array(0)),
											),
									)
							),
							array(
									'Select',
									'popularity',
									array(
											'label' => $view->translate('Popularity Criteria'),
											'multiOptions' => $category_documents_multioptions,
											'value' => 'views',
									)
							),
							array(
									'Select',
									'interval',
									array(
											'label' => $view->translate('Popularity Duration (This duration will be applicable to all Popularity Criteria except Views.)'),
											'multiOptions' => array('week' => '1 Week', 'month' => '1 Month', 'overall' => 'Overall'),
											'value' => 'overall',
									)
							),
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
)
?>
