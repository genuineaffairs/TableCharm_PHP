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
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Page Profile Notes'),
        'description' => $view->translate('This widget forms the Notes tab on the Page Profile and displays the notes of the Page. It should be placed in the Tabbed Blocks area of the Page Profile.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagenote.sitemobile-profile-sitepagenotes',
        'defaultParams' => array(
            'title' => $view->translate('Notes'),
        ),
    ),
     array(
        'title' => $view->translate('Page Notes'),
        'description' => $view->translate('Displays a list of all the page notes on site.'),
        'category' => $view->translate('Pages'),
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
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of notes to show)'),
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
			'title' => $view->translate('Page Note View'),
			'description' => $view->translate("Displays the list of Notes from Pages created on your community. This widget should be placed in the widgetized Page Notes page. Results from the Search Page Notes form are also shown here."),
      'category' => $view->translate('Pages'),
			'type' => 'widget',
			'name' => 'sitepagenote.note-content',
			'defaultParams' => array(
					'title' => '',
					'titleCount' => true,
			),
	),
)
?>