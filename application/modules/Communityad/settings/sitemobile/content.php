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
$featuredSponsoredElement = array(
    'Select',
    'show_ads',
    array(
        'label' => 'Show Ads',
        'multiOptions' => array(
            '0' => 'All',
            '1' => 'Featured Only',
            '2' => 'Sponsored Only'
        ),
        'value' => 'all',
    )
);
return 
array(
	array(
		'title' => $view->translate('Community Ads'),
		'description' => $view->translate('Community Ads'),
		'category' => $view->translate('Community Ads'),
		'type' => 'widget',
		'name' => 'communityad.sitemobile-ads',
		'defaultParams' => array(
			'title' => $view->translate('Community Ads')
		),
		'adminForm' => array(
			'elements' => array(
        $featuredSponsoredElement,
				array(
						'Radio',
						'ajaxView',
						array(
								'label' => $view->translate('Widget Content Loading'),
								'description' => $view->translate('Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)'),
								'multiOptions' => array(
										1 => $view->translate('Yes'),
										0 => $view->translate('No')
								),
								'value' => 0,
						)
				 ),
				array(
						'Text',
						'columnHeight',
						array(
								'label' => 'Column Height For Grid View.',
								'value' => '235',
						)
				),
				 array(
						'Text',
						'limit',
						array(
								'label' => 'Count',
								'description' => '(number of ads to show)',
								'value' => 3,
								'validators' => array(
										array('Int', true),
										array('GreaterThan', true, array(0)),
								),
						)
				),
				array(
						'Radio',
						'carouselView',
						array(
								'label' => $view->translate('Do you want Carousel View (Sliding effect) for Ads ?'),
								'multiOptions' => array(
										'1' => $view->translate('Yes'),
										'0' => $view->translate('No'),
								),
								'default' => '0',
								'value' => '0',
						)
				),
			),
		),
	),
);