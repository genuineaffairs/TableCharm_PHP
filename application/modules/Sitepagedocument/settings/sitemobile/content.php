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

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('Page Profile Documents'),
        'description' => $view->translate('This widget forms the Documents tab on the Page Profile and displays the documents of the Page. It should be placed in the Tabbed Blocks area of the Page Profile.'),
        'category' => $view->translate('Page Profile'),
        'type' => 'widget',
        'name' => 'sitepagedocument.sitemobile-profile-sitepagedocuments',
        'defaultParams' => array(
            'title' => $view->translate('Documents'),
        ),
    ),

     array(
        'title' => $view->translate('Page Documents'),
        'description' => $view->translate('Displays the list of Documents from Pages created on your community. This widget should be placed in the widgetized Page Documents page. Results from the Search Page Documents form are also shown here.'),
        'category' => $view->translate('Pages'),
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
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of documents to show)'),
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
			'title' => $view->translate('Page Document View'),
			'description' => $view->translate("This widget should be placed on the Page Document View Page."),
      'category' => $view->translate('Pages'),
			'type' => 'widget',
			'name' => 'sitepagedocument.document-content',
			'defaultParams' => array(
					'title' => '',
					'titleCount' => true,
			),
	),
)
?>