<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Content.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
	array(
		'title' => $view->translate('Advertise: Create an Ad'),
		'description' => $view->translate('This widget tempts users to advertise on your site. It contains a catchy phrase and a linked button to Create an Ad page.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.create-ad',
		'defaultParams' => array(
			'title' => $view->translate('Want more Customers?')
		),
		'adminForm' => array(
				'elements' => array(
				),
		),		
	),
   array(
		'title' => $view->translate('User Advertising Navigation'),
		'description' => $view->translate('Display a navigation bar to users to browse through adboard and different advertising options.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.user-navigation',
		'adminForm' => array(
				'elements' => array(
				),
		),			
	),
/*	array(
		'title' => $view->translate('Right Column Ads - Widget 1'),
		'description' => $view->translate('Display ads in the right most column of the page. We recommend you to not drag-and-drop this widget into the page. To include this widget in a page, do so from the ‘Manage Ad Blocks’ section by checking ‘Automatically Add on Page’ while creating an ad block and then adjust the vertical position of the widget on the page as desired from Layout Editor.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.right-ads',
		'adminForm' => array(
				'elements' => array(
				),
		),			
	),
	array(
		'title' => $view->translate('Right Column Ads - Widget 2'),
		'description' => $view->translate('Display ads in the right most column of the page. We recommend you to not drag-and-drop this widget into the page. To include this widget in a page, do so from the ‘Manage Ad Blocks’ section by checking ‘Automatically Add on Page’ while creating an ad block and then adjust the vertical position of the widget on the page as desired from Layout Editor.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.right-2-ads'
	),
	array(
		'title' => $view->translate('Middle Column Ads - Widget 1'),
		'description' => $view->translate('Display ads in the middle column of the page. We recommend you to not drag-and-drop this widget into the page. To include this widget in a page, do so from the ‘Manage Ad Blocks’ section by checking ‘Automatically Add on Page’ while creating an ad block and then adjust the vertical position of the widget on the page as desired from Layout Editor.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.middle-1-ads',
		'adminForm' => array(
				'elements' => array(
				),
		),			
	),
	array(
		'title' => $view->translate('Middle Column Ads - Widget 2'),
		'description' => $view->translate('Display ads in the middle column of the page. We recommend you to not drag-and-drop this widget into the page. To include this widget in a page, do so from the ‘Manage Ad Blocks’ section by checking ‘Automatically Add on Page’ while creating an ad block and then adjust the vertical position of the widget on the page as desired from Layout Editor.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.middle-2-ads'
	),
	array(
		'title' => $view->translate('Left Column Ads - Widget 1'),
		'description' => $view->translate('Display ads in the left most column of the page. We recommend you to not drag-and-drop this widget into the page. To include this widget in a page, do so from the ‘Manage Ad Blocks’ section by checking ‘Automatically Add on Page’ while creating an ad block and then adjust the vertical position of the widget on the page as desired from Layout Editor.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.left-1-ads',
		'adminForm' => array(
				'elements' => array(
				),
		),			
	),
	array(
		'title' => $view->translate('Left Column Ads - Widget 2'),
		'description' => $view->translate('Display ads in the left most column of the page. We recommend you to not drag-and-drop this widget into the page. To include this widget in a page, do so from the ‘Manage Ad Blocks’ section by checking ‘Automatically Add on Page’ while creating an ad block and then adjust the vertical position of the widget on the page as desired from Layout Editor.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.left-2-ads',
		'adminForm' => array(
				'elements' => array(
				),
		),			
	),
	array(
		'title' => $view->translate('Full Width Ads - Widget 1'),
		'description' => $view->translate('Display ads covering full width of the page. We recommend you to not drag-and-drop this widget into the page. To include this widget in a page, do so from the ‘Manage Ad Blocks’ section by checking ‘Automatically Add on Page’ while creating an ad block and then adjust the vertical position of the widget on the page as desired from Layout Editor.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.fullwidth-1-ads'
	),
	array(
		'title' => $view->translate('Full Width Ads - Widget 2'),
		'description' => $view->translate('Display ads covering full width of the page. We recommend you to not drag-and-drop this widget into the page. To include this widget in a page, do so from the ‘Manage Ad Blocks’ section by checking ‘Automatically Add on Page’ while creating an ad block and then adjust the vertical position of the widget on the page as desired from Layout Editor.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.fullwidth-2-ads',
		'adminForm' => array(
				'elements' => array(
				),
		),			
	),
	array(
		'title' => $view->translate('Extended Right / Left Ads - Widget 1'),
		'description' => $view->translate('Displays ads covering width equal to middle column and one of either left or right column of the page depending on the layout. We recommend you to not drag-and-drop this widget into the page. To include this widget in a page, do so from the ‘Manage Ad Blocks’ section by checking ‘Automatically Add on Page’ while creating an ad block and then adjust the vertical position of the widget on the page as desired.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.extended-1-ads',
		'adminForm' => array(
				'elements' => array(
				),
		),			
	),
	array(
		'title' => $view->translate('Extended Right / Left Ads - Widget 2'),
		'description' => $view->translate('Displays ads covering width equal to middle column and one of either left or right column of the page depending on the layout. We recommend you to not drag-and-drop this widget into the page. To include this widget in a page, do so from the ‘Manage Ad Blocks’ section by checking ‘Automatically Add on Page’ while creating an ad block and then adjust the vertical position of the widget on the page as desired.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.extended-2-ads'
	),
	array(
		'title' => $view->translate('Extended Right / Left Sponsored Ads Widget'),
		'description' => $view->translate('Displays Sponsored ads covering width equal to middle column and one of either left or right column of the page depending on the layout. We recommend you to not drag-and-drop this widget into the page. To include this widget in a page, do so from the ‘Manage Ad Blocks’ section by checking ‘Automatically Add on Page’ while creating an ad block and then adjust the vertical position of the widget on the page as desired.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.sponserd-extended-ad',
		'adminForm' => array(
				'elements' => array(
				),
		),			
	),
	array(
		'title' => $view->translate('Right Sponsored Ads Widget'),
		'description' => $view->translate('Display sponsored ads in the right most column of the page. We recommend you to not drag-and-drop this widget into the page. To include this widget in a page, do so from the ‘Manage Ad Blocks’ section by checking ‘Automatically Add on Page’ while creating an ad block and then adjust the vertical position of the widget on the page as desired from Layout Editor.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.sponserd-right-ad',
		'adminForm' => array(
				'elements' => array(
				),
		),			
	),
	array(
		'title' => $view->translate('Middle Sponsored Ads Widget'),
		'description' => $view->translate('Display sponsored ads in the middle column of the page. We recommend you to not drag-and-drop this widget into the page. To include this widget in a page, do so from the ‘Manage Ad Blocks’ section by checking ‘Automatically Add on Page’ while creating an ad block and then adjust the vertical position of the widget on the page as desired from Layout Editor.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.sponserd-middle-ad',
		'adminForm' => array(
				'elements' => array(
				),
		),			
	),
	array(
		'title' => $view->translate('Left Sponsored Ads Widget'),
		'description' => $view->translate('Display sponsored ads in the left most column of the page. We recommend you to not drag-and-drop this widget into the page. To include this widget in a page, do so from the ‘Manage Ad Blocks’ section by checking ‘Automatically Add on Page’ while creating an ad block and then adjust the vertical position of the widget on the page as desired from Layout Editor.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.sponsored-left-ad',
		'adminForm' => array(
				'elements' => array(
				),
		),			
	),
	array(
		'title' => $view->translate('Full Width Sponsored Ads Widget'),
		'description' => $view->translate('Display sponsored ads covering full width of the page. We recommend you to not drag-and-drop this widget into the page. To include this widget in a page, do so from the ‘Manage Ad Blocks’ section by checking ‘Automatically Add on Page’ while creating an ad block and then adjust the vertical position of the widget on the page as desired from Layout Editor.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.sponsored-fullwidth-ad',
		'adminForm' => array(
				'elements' => array(
				),
		),			
	),
	array(
		'title' => $view->translate('Extended Right / Left Featured Ads Widget'),
		'description' => $view->translate('Displays Featured ads covering width equal to middle column and one of either left or right column of the page depending on the layout. We recommend you to not drag-and-drop this widget into the page. To include this widget in a page, do so from the ‘Manage Ad Blocks’ section by checking ‘Automatically Add on Page’ while creating an ad block and then adjust the vertical position of the widget on the page as desired.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.featured-extended-ad',
		'adminForm' => array(
				'elements' => array(
				),
		),			
	),
	array(
		'title' => $view->translate('Right Featured Ads Widget'),
		'description' => $view->translate('Display featured ads in the right most column of the page. We recommend you to not drag-and-drop this widget into the page. To include this widget in a page, do so from the ‘Manage Ad Blocks’ section by checking ‘Automatically Add on Page’ while creating an ad block and then adjust the vertical position of the widget on the page as desired from Layout Editor.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.featured-right-ad',
		'adminForm' => array(
				'elements' => array(
				),
		),			
	),
	array(
		'title' => $view->translate('Middle Featured Ads Widget'),
		'description' => $view->translate('Display featured ads in the middle column of the page. We recommend you to not drag-and-drop this widget into the page. To include this widget in a page, do so from the ‘Manage Ad Blocks’ section by checking ‘Automatically Add on Page’ while creating an ad block and then adjust the vertical position of the widget on the page as desired from Layout Editor.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.featured-middle-ad',
		'adminForm' => array(
				'elements' => array(
				),
		),			
	),
	array(
		'title' => $view->translate('Left Featured Ads Widget'),
		'description' => $view->translate('Display featured ads in the left most column of the page. We recommend you to not drag-and-drop this widget into the page. To include this widget in a page, do so from the ‘Manage Ad Blocks’ section by checking ‘Automatically Add on Page’ while creating an ad block and then adjust the vertical position of the widget on the page as desired from Layout Editor.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.featured-left-ad',
		'adminForm' => array(
				'elements' => array(
				),
		),			
	),
	array(
		'title' => $view->translate('Full Width Featured Ads Widget'),
		'description' => $view->translate('Display featured ads covering full width of the page. We recommend you to not drag-and-drop this widget into the page. To include this widget in a page, do so from the ‘Manage Ad Blocks’ section by checking ‘Automatically Add on Page’ while creating an ad block and then adjust the vertical position of the widget on the page as desired from Layout Editor.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.featured-fullwidth-ad',
		'adminForm' => array(
				'elements' => array(
				),
		),			
	),
	array(
		'title' => $view->translate('Footer Ads Widget'),
		'description' => $view->translate('Displays Ads covering full width in the footer of the site. Note that this ads widget will come on all the pages because it gets placed in the footer. You may drag-and-drop this widget in the Site Footer from here.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.footer-ads',
		'adminForm' => array(
				'elements' => array(
				),
		),			
	),*/
	array(
		'title' => $view->translate('Sample Ad Widget'),
		'description' => $view->translate('Displays the sample ad of a content on its profile. This widget will be visible only to the owners of the content. Through this, owners will get an idea that how the ad corresponding to their content would look like. It would work as a motivation for content owners to create ads of their content.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.pagead-preview',
		'adminForm' => array(
				'elements' => array(
				),
		),			
	),
	array(
		'title' => $view->translate('Advertise a content Widget'),
		'description' => $view->translate('This widget tempts users to advertise their content on your site. It contains a catchy phrase and a link to Create an Ad page. This widget should be placed on the main page of a content.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.getconnection-link',
		'adminForm' => array(
				'elements' => array(
				),
		),			
	),
	array(
		'title' => $view->translate('Sponsored Stories Widget'),
		'description' => $view->translate('Displays Sponsored Stories to users. Sponsored Stories can be Liked, thus leading to their viral distribution. In the edit settings of this widget, you can enter the maximum number of stories to be shown in this widget, as well as choose if the content of this widget should be loaded via AJAX after page load.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.sponsored-stories',
		'defaultParams' => array(
			'title' => '',
			'itemCountPerPage' => 5
		),
		'adminForm' => array(
			'elements' => array(
				// Add Radio Button.
				array(
					'Radio',
					'isAjaxEnabled',
					array(
						'label' => $view->translate('AJAX Based Display'),
						'description' => $view->translate('Do you want the content of this widget to be loaded via AJAX after page load (this can be good for the overall webpage loading speed)?'),
						'multiOptions' => array(
							'1' => 'Yes',
							'0' => 'No',
						),
						'value' => 0,
					)
				),
				// Add Text Field.
				array(
					'Text',
					'itemCount',
					array(
						'label' => $view->translate('Maximum Sponsored Stories'),
						'description' => $view->translate('Enter the maximum number of Sponsored Stories to be displayed in this widget.'),
						'value' => 5,
					)
				),
			),
		),
	),

      array(
	      'title' => $view->translate('Mobile Community Ads'),
	      'description' => $view->translate('Mobile Community Ads'),
	      'category' => $view->translate('Community Ads'),
	      'type' => 'widget',
	      'name' => 'communityad.mobile-ads',

	    'defaultParams' => array(
		'title' => $view->translate('Mobile Community Ads')
	    ),
	    'adminForm' => array(
		'elements' => array(
		    array(
			'Radio',
			'imageDisplay',
			array(
			    'label' => 'Do you want the Ad Images to be displayed for Community Ads in this block?',
			    'multiOptions' => array(
				'1' => 'Yes',
				'0' => 'No',
			    ),
			    'value' => 1,
			)
		    ),

	  array(
	      'Text',
	      'WidLimit',
	      array(
		  'label' => 'Default Ads per block',
		  'description' => 'Enter the default value for maximum number of ads to be displayed in this block.',
		  'value' => 5
	      )
	  )

		),
	    ),
      ),
    array(
		'title' => $view->translate('Display Advertisements'),
		'description' => $view->translate('Display advertisements on your site. Multiple settings available in the Edit Settings of this widget.'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.ads',
        'autoEdit' => true,
        'defaultParams' => array(
            'loaded_by_ajax' => 1,
        ),
		'adminForm' => 'Communityad_Form_Admin_Widget_Ads'

    ),
)
?>
