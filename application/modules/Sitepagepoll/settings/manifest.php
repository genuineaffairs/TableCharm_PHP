<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$routeStart = "pagepolls";
$module=null;$controller=null;$action=null;
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
  $module = $request->getModuleName(); // Return the current module name.
  $action = $request->getActionName();
  $controller = $request->getControllerName();
}
if (empty($request) || !($module == "default" && $controller == "sdk" && $action == "build")) { 
  $routeStart = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.manifestUrl', "page-polls");
}
return array(
    // Package -------------------------------------------------------------------
    'package' => array(
        'type' => 'module',
        'name' => 'sitepagepoll',
        'version' => '4.7.0',
        'path' => 'application/modules/Sitepagepoll',
        'repository' => 'null',
        'title' => 'Directory / Pages - Polls Extension',
        'description' => 'Directory / Pages - Polls Extension',
      'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'date' => 'Thrusday, 05 May 2011 18:33:08 +0000',
        'copyright' => 'Copyright 2010-2011 BigStep Technologies Pvt. Ltd.',
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Sitepagepoll/settings/install.php',
            'class' => 'Sitepagepoll_Installer',
        ),
        'directories' => array(
            'application/modules/Sitepagepoll',
        ),
        'files' => array(
            'application/languages/en/sitepagepoll.csv',
        ),
    ),
    'sitemobile_compatible' =>true,
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Sitepagepoll_Plugin_Core',
        ),
        array(
            'event' => 'onRenderLayoutMobileSMDefault',
            'resource' => 'Sitepagepoll_Plugin_Sitemobile',
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'sitepagepoll_poll',
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'sitepagepoll_create' => array(
            'route' => $routeStart.'/create/:page_id/*',
            'defaults' => array(
                'module' => 'sitepagepoll',
                'controller' => 'index',
                'action' => 'create'
            ),
            'reqs' => array(
                'page_id' => '\d+'
            )
        ),
        'sitepagepoll_approved' => array(
            'route' => $routeStart.'/approved/:poll_id',
            'defaults' => array(
                'module' => 'sitepagepoll',
                'controller' => 'admin',
                'action' => 'approved'
            ),
            'reqs' => array(
                'poll_id' => '\d+'
            )
        ),
        'sitepagepoll_delete' => array(
            'route' => $routeStart.'/delete/:poll_id/:page_id/*',
            'defaults' => array(
                'module' => 'sitepagepoll',
                'controller' => 'index',
                'action' => 'delete'
            ),
            'reqs' => array(
                'poll_id' => '\d+',
                'page_id' => '\d+'
            )
        ),
        'sitepagepolladmin_delete' => array(
            'route' => $routeStart.'/admin/delete/:poll_id/*',
            'defaults' => array(
                'module' => 'sitepagepoll',
                'controller' => 'admin',
                'action' => 'delete'
            ),
            'reqs' => array(
                'poll_id' => '\d+'
            )
        ),
        'sitepagepoll_detail_view' => array(
            'route' => $routeStart.'/:user_id/:poll_id/:slug/*',
            'defaults' => array(
                'module' => 'sitepagepoll',
                'controller' => 'index',
                'action' => 'view',
                'slug' => '',
            ),
            'reqs' => array(
                'user_id' => '\d+',
                'poll_id' => '\d+'
            )
        ),
         'sitepagepoll_specific' => array(
            'route' => $routeStart.'/close/:poll_id/*',
            'defaults' => array(
                'module' => 'sitepagepoll',
                'controller' => 'index',
                'action' => 'close',
            ),
            'reqs' => array(
                'poll_id' => '\d+'
            )
        ),

        'sitepagepoll_browse' => array(
            'route' => $routeStart.'/browse/*',
            'defaults' => array(
                'module' => 'sitepagepoll',
                'controller' => 'index',
                'action' => 'browse',
            ),
        ),
    )
)
?>