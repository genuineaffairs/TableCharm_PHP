<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 6590 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$routeStart = "pageevents";
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
  $routeStart = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepageevent.manifestUrl', "page-events");
}
return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'sitepageevent',
        'version' => '4.8.2',
        'path' => 'application/modules/Sitepageevent',
        'title' => 'Directory / Pages - Events Extension',
        'description' => 'Directory / Pages - Events Extension',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'date' => 'Thrusday, 05 May 2011 18:33:08 +0000',
        'copyright' => 'Copyright 2010-2011 BigStep Technologies Pvt. Ltd.',
        'actions' =>
        array(
            0 => 'install',
            1 => 'upgrade',
            2 => 'refresh',
            3 => 'enable',
            4 => 'disable',
        ),
        'callback' =>
        array(
            'path' => 'application/modules/Sitepageevent/settings/install.php',
            'class' => 'Sitepageevent_Installer',
        ),
        'directories' =>
        array(
            0 => 'application/modules/Sitepageevent',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/sitepageevent.csv',
        ),
    ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Sitepageevent_Plugin_Core',
        ),
        array(
            'event' => 'addActivity',
            'resource' => 'Sitepageevent_Plugin_Core',
        ),
        array(
            'event' => 'onSitepageeventEventCreateAfter',
            'resource' => 'Sitepageevent_Plugin_Core',
        ),
        array(
            'event' => 'onSitepageeventEventUpdateAfter',
            'resource' => 'Sitepageevent_Plugin_Core',
        )
    ),
    // ITEMS --------------------------------------------------------------------
    'items' => array(
        'sitepageevent_event',
        'sitepageevent_photo',
        'sitepageevent_album'
    ),
    // COMPATIBLE WITH MOBILE / TABLET PLUGIN --------------------------------------------------------------------
    'sitemobile_compatible' =>true,
    // ROUTES --------------------------------------------------------------------
    'routes' => array(
        'sitepageevent_detail_view' => array(
            'route' => $routeStart . '/:user_id/:event_id/:slug/*',
            'defaults' => array(
                'module' => 'sitepageevent',
                'controller' => 'index',
                'action' => 'view',
                'slug' => '',
            ),
            'reqs' => array(
                'user_id' => '\d+',
                'event_id' => '\d+'
            )
        ),
        'sitepageevent_create' => array(
            'route' => $routeStart . '/create/:page_id/*',
            'defaults' => array(
                'module' => 'sitepageevent',
                'controller' => 'index',
                'action' => 'create'
            ),
            'reqs' => array(
                'page_id' => '\d+'
            )
        ),
        'sitepageevent_delete' => array(
            'route' => $routeStart . '/admin-manage/delete/:event_id/*',
            'defaults' => array(
                'module' => 'sitepageevent',
                'controller' => 'admin-manage',
                'action' => 'delete'
            ),
            'reqs' => array(
                'event_id' => '\d+'
            )
        ),
        'sitepageevent_specific' => array(
            'route' => $routeStart . '/:action/:event_id/:page_id/*',
            'defaults' => array(
                'module' => 'sitepageevent',
                'controller' => 'index',
                'action' => 'edit'
            ),
            'reqs' => array(
                'event_id' => '\d+',
                'action' => '(edit|invite|delete|join|leave|request|edit-location|edit-address|invite-members)',
            ),
        ),
        'sitepageevent_extended' => array(
            'route' => $routeStart . '/:action/:event_id/:page_id/:user_id/*',
            'defaults' => array(
                'module' => 'sitepageevent',
                'controller' => 'index',
                'action' => 'edit'
            ),
            'reqs' => array(
                'event_id' => '\d+',
                'action' => '(reject|remove|approve|cancel|accept)',
            ),
        ),
        'sitepageevent_photo_extended' => array(
            'route' => $routeStart . '/photos/:action/*',
            'defaults' => array(
                'module' => 'sitepageevent',
                'controller' => 'photo',
                'action' => 'list'
            ),
            'reqs' => array(
                'action' => '(upload|list|view|edit-photo|photo-edit|remove|upload-sitemobile-photo)',
            ),
        ),
        'sitepageevent_browse' => array(
            'route' => $routeStart . '/browse/*',
            'defaults' => array(
                'module' => 'sitepageevent',
                'controller' => 'index',
                'action' => 'browse',
            ),
        ),
        'sitepageevent_featured' => array(
            'route' => $routeStart . '/featured/:event_id/*',
            'defaults' => array(
                'module' => 'sitepageevent',
                'controller' => 'index',
                'action' => 'featured'
            ),
            'reqs' => array(
                'event_id' => '\d+'
            )
        ),
        'sitepageevent_featuredevent' => array(
            'route' => $routeStart . '/admin/featuredevent/:id/*',
            'defaults' => array(
                'module' => 'sitepageevent',
                'controller' => 'admin-manage',
                'action' => 'featuredevent',
            ),
            'reqs' => array(
                'id' => '\d+'
            )
        ),
        'sitepageevent_home' => array(
            'route' => $routeStart . '/home/*',
            'defaults' => array(
                'module' => 'sitepageevent',
                'controller' => 'index',
                'action' => 'home',
            ),
        ),
        'sitepageevent_bylocation' => array(
            'route' => $routeStart . '/by-locations',
            'defaults' => array(
                'module' => 'sitepageevent',
                'controller' => 'index',
                'action' => 'by-locations',
            ),
        ),
    ),
);
?>