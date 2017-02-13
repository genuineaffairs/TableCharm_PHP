<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$routeStart = "pagenotes";
$module = null;
$controller = null;
$action = null;
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
  $module = $request->getModuleName(); // Return the current module name.
  $action = $request->getActionName();
  $controller = $request->getControllerName();
}
if (empty($request) || !($module == "default" && $controller == "sdk" && $action == "build")) {
  $routeStart = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagenote.manifestUrl', "page-notes");
}
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitepagenote',
        'version' => '4.7.1',
        'path' => 'application/modules/Sitepagenote',
        'title' => 'Directory / Pages - Notes Extension',
        'description' => 'Directory / Pages - Notes Extension',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'date' => 'Thrusday, 05 May 2011 18:33:08 +0000',
        'copyright' => 'Copyright 2010-2011 BigStep Technologies Pvt. Ltd.',
        'callback' => array(
            'path' => 'application/modules/Sitepagenote/settings/install.php',
            'class' => 'Sitepagenote_Installer',
        ),
        'actions' =>
        array(
            0 => 'install',
            1 => 'upgrade',
            2 => 'refresh',
            3 => 'enable',
            4 => 'disable',
        ),
        'directories' =>
        array(
            0 => 'application/modules/Sitepagenote',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/sitepagenote.csv',
        ),
    ),
		// COMPATIBLE WITH MOBILE / TABLET PLUGIN --------------------------------------------------------------------
	 'sitemobile_compatible' =>true,
    'hooks' => array(
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Sitepagenote_Plugin_Core',
        ),
        array(
            // 'event' => 'addActivity',
            'event' => 'onRenderLayoutDefault',
            'resource' => 'Sitepagenote_Plugin_Core'
        ),
    ),
    'items' => array(
        'sitepagenote_note',
        'sitepagenote_photo',
        'sitepagenote_album',
    ),
    // ROUTES --------------------------------------------------------------------
    'routes' => array(
        'sitepagenote_specific' => array(
            'route' => $routeStart . '/:action/:note_id/*',
            'defaults' => array(
                'module' => 'sitepagenote',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'note_id' => '\d+',
                'action' => '(delete|edit|close|success)',
            ),
        ),
        'sitepagenote_detail_view' => array(
            'route' => $routeStart . '/:user_id/:note_id/:slug/*',
            'defaults' => array(
                'module' => 'sitepagenote',
                'controller' => 'index',
                'action' => 'view',
                'slug' => '',
            ),
            'reqs' => array(
                'user_id' => '\d+',
                'note_id' => '\d+'
            )
        ),
        'sitepagenote_uploadphoto' => array(
            'route' => $routeStart . '/uploadphoto/*',
            'defaults' => array(
                'module' => 'sitepagenote',
                'controller' => 'index',
                'action' => 'upload-photo',
            ),
        ),
        'sitepagenote_create' => array(
            'route' => $routeStart . '/create/:page_id/*',
            'defaults' => array(
                'module' => 'sitepagenote',
                'controller' => 'index',
                'action' => 'create'
            ),
            'reqs' => array(
                'page_id' => '\d+'
            )
        ),
        'sitepagenoteadmin_delete' => array(
            'route' => $routeStart . '/admin-manage/delete/:id/*',
            'defaults' => array(
                'module' => 'sitepagenote',
                'controller' => 'admin-manage',
                'action' => 'delete'
            ),
            'reqs' => array(
                'id' => '\d+'
            )
        ),
        'sitepagenote_edit' => array(
            'route' => $routeStart . '/edit/:note_id/:page_id/*',
            'defaults' => array(
                'module' => 'sitepagenote',
                'controller' => 'index',
                'action' => 'edit'
            ),
        ),
        'sitepagenote_editphoto' => array(
            'route' => $routeStart . '/edit-photo/:note_id/:page_id/*',
            'defaults' => array(
                'module' => 'sitepagenote',
                'controller' => 'photo',
                'action' => 'edit-photo'
            ),
        ),
        'sitepagenote_publish' => array(
            'route' => $routeStart . '/publish/:note_id/*',
            'defaults' => array(
                'module' => 'sitepagenote',
                'controller' => 'index',
                'action' => 'publish'
            ),
            'reqs' => array(
                'page_id' => '\d+'
            )
        ),
        'sitepagenote_delete' => array(
            'route' => $routeStart . '/delete/:note_id/:page_id/*',
            'defaults' => array(
                'module' => 'sitepagenote',
                'controller' => 'index',
                'action' => 'delete'
            ),
            'reqs' => array(
                'note_id' => '\d+',
                'page_id' => '\d+'
            )
        ),
        'sitepagenote_image_specific' => array(
            'route' => $routeStart . '/photo/view/:owner_id/:album_id/:photo_id/*',
            'defaults' => array(
                'module' => 'sitepagenote',
                'controller' => 'photo',
                'action' => 'view'
            ),
            'reqs' => array(
                'action' => '(view|upload)',
            ),
        ),
        'sitepagenote_removeimage' => array(
            'route' => $routeStart . '/photo/remove/:note_id/:photo_id/*',
            'defaults' => array(
                'module' => 'sitepagenote',
                'controller' => 'photo',
                'action' => 'remove',
            ),
        ),
        'sitepagenote_photoedit' => array(
            'route' => $routeStart . '/photo-edit/:photo_id/:page_id/*',
            'defaults' => array(
                'module' => 'sitepagenote',
                'controller' => 'photo',
                'action' => 'photo-edit'
            ),
        ),
        'sitepagenote_photoupload' => array(
            'route' => $routeStart . '/photo/upload/:note_id/*',
            'defaults' => array(
                'module' => 'sitepagenote',
                'controller' => 'photo',
                'action' => 'upload'
            ),
        ),
        'sitepagenote_sitemobilephotoupload' => array(
            'route' => $routeStart . '/photo/upload-sitemobile-photo/:note_id/*',
            'defaults' => array(
                'module' => 'sitepagenote',
                'controller' => 'photo',
                'action' => 'upload-sitemobile-photo'
            ),
        ),
        'sitepagenote_browse' => array(
            'route' => $routeStart . '/browse/*',
            'defaults' => array(
                'module' => 'sitepagenote',
                'controller' => 'index',
                'action' => 'browse',
            ),
        ),
        'sitepagenote_tags' => array(
            'route' => $routeStart . '/tagscloud/:page/',
            'defaults' => array(
                'module' => 'sitepagenote',
                'controller' => 'index',
                'action' => 'tags-cloud',
                'page' => 1
            )
        ),
        'sitepagenote_home' => array(
            'route' => $routeStart.'/home/*',
            'defaults' => array(
                'module' => 'sitepagenote',
                'controller' => 'index',
                'action' => 'home',
            ),
        ), 

         'sitepagenote_featured' => array(
            'route' => $routeStart . '/featured/:note_id/*',
            'defaults' => array(
                'module' => 'sitepagenote',
                'controller' => 'index',
                'action' => 'featured'
            ),
            'reqs' => array(
                'note_id' => '\d+'
            )
        ),

         'sitepagenote_featurednote' => array(
            'route' => $routeStart .'/admin/featurednote/:id/*',
            'defaults' => array(
                'module' => 'sitepagenote',
                'controller' => 'admin-manage',
                'action' => 'featurednote',
            ),
            'reqs' => array(
                'id' => '\d+'
            )
        ),

    ),
);
?>