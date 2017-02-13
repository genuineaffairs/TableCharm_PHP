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
            'title' => 'Profile Document',
            'titleCount' => true,
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
        'title' => $view->translate('Message for No Documents'),
        'description' => $view->translate('Displays a message to users when there are no documents. This widget should be placed in the top of the middle column of Documents Home page.'),
        'category' => $view->translate('Documents'),
        'type' => 'widget',
        'name' => 'document.zero-documents',
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
                            "document_title" => "All documents in alphabetical order.",
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
        )
?>
