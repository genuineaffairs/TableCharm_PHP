<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content_user.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Page Profile Documents'),
        'description' => $view->translate('Forms the Documents tab of your Page and shows the documents of your Page. It should be placed in the Tabbed Blocks area of the Page Profile.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagedocument.profile-sitepagedocuments',
        'defaultParams' => array(
            'title' => $view->translate('Documents'),
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Recent Documents'),
        'description' => $view->translate('Displays recent documents of your Page.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagedocument.recent-sitepagedocuments',
        'defaultParams' => array(
            'title' => $view->translate('Most Recent Documents'),
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Popular Documents'),
        'description' => $view->translate('Displays popular documents of your Page.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagedocument.popular-sitepagedocuments',
        'defaultParams' => array(
            'title' => $view->translate('Most Popular Documents'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Most Commented Documents'),
        'description' => $view->translate('Displays most commented documents of your Page.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagedocument.comment-sitepagedocuments',
        'defaultParams' => array(
            'title' => $view->translate('Most Commented Documents'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Most Liked Documents'),
        'description' => $view->translate('Displays most liked documents of your Page.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagedocument.like-sitepagedocuments',
        'defaultParams' => array(
            'title' => $view->translate('Most Liked Documents'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Top Rated Documents'),
        'description' => $view->translate('Displays top rated documents of your Page.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagedocument.rate-sitepagedocuments',
        'defaultParams' => array(
            'title' => $view->translate('Top Rated Documents'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Profile Featured Documents'),
        'description' => $view->translate('Displays featured documents of your Page.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagedocument.featurelist-sitepagedocuments',
        'defaultParams' => array(
            'title' => $view->translate('Featured Documents'),
            'titleCount' => true,
        ),
    ),

    array(
        'title' => $view->translate('Page Profile Highlighted Documents'),
        'description' => $view->translate("Displays list of page's highlighted documents. This widget should be placed on the Page Profile."),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagedocument.highlightlist-sitepagedocuments',
        'defaultParams' => array(
            'title' => $view->translate('Highlighted Page Documents'),
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