<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.isActivate', 0);
if (empty($isActive)) {
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
        'title' => 'Page Profile Polls',
        'description' => 'This widget forms the Polls tab on the Page Profile and displays the polls of the Page. It should be placed in the Tabbed Blocks area of the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagepoll.profile-sitepagepolls',
        'defaultParams' => array(
            'title' => 'Polls',
            'titleCount' => true,
        ),
    ),
    array(
        'title' => 'Page Profile Most Commented Polls',
        'description' => 'Displays list of a Page\'s most commented polls. This widget should be placed on the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagepoll.comment-sitepagepolls',
        'defaultParams' => array(
            'title' => 'Most Commented Polls',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of polls to show)',
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
        'title' => 'Page Profile Most Viewed Polls',
        'description' => 'Displays list of a Page\'s most viewed polls. This widget should be placed on the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagepoll.view-sitepagepolls',
        'defaultParams' => array(
            'title' => 'Most Viewed Polls',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of polls to show)',
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
        'title' => 'Page Profile Most Voted Polls',
        'description' => 'Displays list of a Page\'s most voted polls. This widget should be placed on the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagepoll.vote-sitepagepolls',
        'defaultParams' => array(
            'title' => 'Most Voted Polls',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of polls to show)',
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
        'title' => 'Page Profile Most Recent Polls',
        'description' => 'Displays list of a page\'s most recent polls. This widget should be placed on the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagepoll.recent-sitepagepolls',
        'defaultParams' => array(
            'title' => 'Most Recent Polls',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of polls to show)',
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
        'title' => 'Page Profile Most Liked Polls',
        'description' => 'Displays list of a page\'s most liked polls. This widget should be placed on the Page Profile.',
        'category' => 'Page Profile',
        'type' => 'widget',
        'name' => 'sitepagepoll.like-sitepagepolls',
        'defaultParams' => array(
            'title' => 'Most Liked Polls',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of polls to show)',
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
        'title' => 'Most Commented Polls',
        'description' => "Displays the Most Commented Page Polls. You can choose the number of entries to be shown.",
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagepoll.homecomment-sitepagepolls',
        'defaultParams' => array(
            'title' => 'Most Commented Polls',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of polls to show)',
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
        'title' => 'Most Voted Polls',
        'description' => "Displays list of Page's most voted polls.",
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagepoll.homevote-sitepagepolls',
        'defaultParams' => array(
            'title' => 'Most Voted Polls',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of polls to show)',
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
        'title' => 'Most Viewed Polls',
        'description' => "Displays the Most Viewed Page Polls. You can choose the number of entries to be shown.",
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagepoll.homeview-sitepagepolls',
        'defaultParams' => array(
            'title' => 'Most Viewed Polls',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of polls to show)',
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
        'title' => 'Most Liked Polls',
        'description' => "Displays the Most Liked Page Polls. You can choose the number of entries to be shown.",
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagepoll.homelike-sitepagepolls',
        'defaultParams' => array(
            'title' => 'Most Liked Polls',
            'titleCount' => true,
        ),
        'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of polls to show)',
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
        'title' => 'Recent Polls',
        'description' => 'Displays the recent polls of the site.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagepoll.homerecent-sitepagepolls',
        'defaultParams' => array(
            'title' => 'Recent Polls'
        ),
         'adminForm' => array(
					'elements' => array(
						array(
								'Text',
								'itemCount',
								array(
										'label' => 'Count',
										'description' => '(number of polls to show)',
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
			'title' => 'Search Page Polls form',
			'description' => 'Displays the form for searching Page Polls on the basis of various filters. You can edit the fields to be available in this form.',
			'category' => 'Pages',
			'type' => 'widget',
			'name' => 'sitepagepoll.search-sitepagepoll',
			'defaultParams' => array(
					'title' => '',
          'search_column' => array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => '5'),
					'titleCount' => true,

			),
			'adminForm' => array(
              'elements' => array(
							array(
									'MultiCheckbox',
									'search_column',
									array(
											'label' => 'Choose the fields that you want to be available in the Search Page Polls form widget.',
											'multiOptions' => array("1" => "Show","2" => "Browse By", "3" => "Page Title", "4" => "Poll Keywords", "5" => "Page Category"),
									),
							),
					),
			)
    ),

    array(
        'title' => 'Page Polls',
        'description' => 'Displays a list of all the pages poll on site. This widget should be placed on the  Pages poll page.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagepoll.sitepage-poll',
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
                        'description' => '(number of polls to show)',
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
        'title' => 'Sponsored Polls',
        'description' => 'Displays the Polls from Paid Pages. You can choose the number of entries to be shown.',
        'category' => 'Pages',
        'type' => 'widget',
        'name' => 'sitepagepoll.sitepage-sponsoredpoll',
        'defaultParams' => array(
            'title' => 'Sponsored Polls',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Count',
                        'description' => '(number of polls to show)',
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
			'title' => 'Page Poll View',
			'description' => "This widget should be placed on the Page Poll View Page.",
      'category' => 'Pages',
			'type' => 'widget',
			'name' => 'sitepagepoll.sitepagepoll-content',
			'defaultParams' => array(
					'title' => '',
					'titleCount' => true,
			),
	),

  array(
    'title' => 'Top Creators : Page Polls',
    'description' => 'Displays the Pages which have the most number of Page Polls added in them. Motivates Page Admins to add more content on your website.',
    'category' => 'Pages',
    'type' => 'widget',
    'name' => 'sitepagepoll.topcreators-sitepagepoll',
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
      'subject' => 'sitepagepoll',
    ),
  ),
)
?>