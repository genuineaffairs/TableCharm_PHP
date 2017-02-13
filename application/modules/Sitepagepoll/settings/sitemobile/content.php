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

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Page Profile Polls'),
        'description' => $view->translate('This widget forms the Polls tab on the Page Profile and displays the polls of the Page. It should be placed in the Tabbed Blocks area of the Page Profile.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagepoll.sitemobile-profile-sitepagepolls',
        'defaultParams' => array(
            'title' => $view->translate('Polls'),
            'titleCount' => true,
        ),
    ),
    array(
        'title' => $view->translate('Page Polls'),
        'description' => $view->translate('Displays a list of all the pages poll on site. This widget should be placed on the  Pages poll page.'),
        'category' => $view->translate('Pages'),
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
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of polls to show)'),
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
    array(
			'title' => $view->translate('Page Poll View'),
			'description' => $view->translate("This widget should be placed on the Page Poll View Page."),
      'category' => $view->translate('Pages'),
			'type' => 'widget',
			'name' => 'sitepagepoll.sitepagepoll-content',
			'defaultParams' => array(
					'title' => '',
					'titleCount' => true,
			),
	),
)
?>