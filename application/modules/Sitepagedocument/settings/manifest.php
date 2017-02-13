<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$routeStart = "pagedocuments";
$module=null;$controller=null;$action=null;
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
  $module = $request->getModuleName(); // Return the current module name.
  $action = $request->getActionName();
  $controller = $request->getControllerName();
}
if (empty($request) || !($module == "default" && $controller == "sdk" && $action == "build")) {
  $routeStart = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.manifestUrl', "page-documents");
}
return array(
    // Package -------------------------------------------------------------------
    'package' => array(
        'type' => 'module',
        'name' => 'sitepagedocument',
        'version' => '4.8.2p1',
        'path' => 'application/modules/Sitepagedocument',
        'repository' => 'null',
        'title' => 'Directory / Pages - Documents Extension',
        'description' => 'Directory / Pages - Documents Extension',
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
            'path' => 'application/modules/Sitepagedocument/settings/install.php',
            'class' => 'Sitepagedocument_Installer',
        ),
        'directories' => array(
            'application/modules/Sitepagedocument',
        ),
        'files' => array(
            'application/languages/en/sitepagedocument.csv',
        ),
    ),
    'sitemobile_compatible' =>true,
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Sitepagedocument_Plugin_Core',
        ),
       array(
       'event' => 'onRenderLayoutMobileSMDefault',
       'resource' => 'Sitepagedocument_Plugin_Sitemobile',
     ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'sitepagedocument_document',
        'sitepagedocument_search',
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'sitepagedocument_general' => array(
            'route' => $routeStart.'/upload/:action/*',
            'defaults' => array(
                'module' => 'sitepagedocument',
                'controller' => 'index',
                'action' => 'upload-photo',
            ),
            'reqs' => array(
                'action' => '(upload-photo)',
            ),
        ),        
        'sitepagedocument_create' => array(
            'route' => $routeStart.'/create/:page_id/*',
            'defaults' => array(
                'module' => 'sitepagedocument',
                'controller' => 'index',
                'action' => 'create'
            ),
            'reqs' => array(
                'page_id' => '\d+'
            )
        ),
        'sitepagedocument_delete' => array(
            'route' => $routeStart.'/delete/:document_id/:page_id/*',
            'defaults' => array(
                'module' => 'sitepagedocument',
                'controller' => 'index',
                'action' => 'delete'
            ),
            'reqs' => array(
                'document_id' => '\d+',
                'page_id' => '\d+'
            )
        ),
        'sitepagedocument_publish' => array(
            'route' => $routeStart.'/publish/:document_id/*',
            'defaults' => array(
                'module' => 'sitepagedocument',
                'controller' => 'index',
                'action' => 'publish'
            ),
            'reqs' => array(
                'document_id' => '\d+'
            )
        ),
        'sitepagedocument_featured' => array(
            'route' => $routeStart.'/featured/:document_id/*',
            'defaults' => array(
                'module' => 'sitepagedocument',
                'controller' => 'index',
                'action' => 'featured'
            ),
            'reqs' => array(
                'document_id' => '\d+'
            )
        ),
         'sitepagedocument_highlighted' => array(
            'route' => $routeStart.'/highlighted/:document_id/*',
            'defaults' => array(
                'module' => 'sitepagedocument',
                'controller' => 'index',
                'action' => 'highlighted'
            ),
            'reqs' => array(
                'document_id' => '\d+'
            )
        ),
        'sitepagedocument_approved' => array(
            'route' => $routeStart.'/approved/:document_id',
            'defaults' => array(
                'module' => 'sitepagedocument',
                'controller' => 'index',
                'action' => 'approved'
            ),
            'reqs' => array(
                'document_id' => '\d+'
            )
        ),
        'sitepagedocument_edit' => array(
            'route' => $routeStart.'/edit/:document_id/:page_id/*',
            'defaults' => array(
                'module' => 'sitepagedocument',
                'controller' => 'index',
                'action' => 'edit'
            )
        ),
        'sitepagedocument_ssl' => array(
            'route' => $routeStart.'/ssl/*',
            'defaults' => array(
                'module' => 'sitepagedocument',
                'controller' => 'index',
                'action' => 'ssl'
            )
        ),
        'sitepagedocument_detail_view' => array(
            'route' => $routeStart.'/:user_id/:document_id/:slug/*',
            'defaults' => array(
                'module' => 'sitepagedocument',
                'controller' => 'index',
                'action' => 'view',
                'slug' => '',
            ),
            'reqs' => array(
                'user_id' => '\d+',
                'document_id' => '\d+'
            )
        ),

        'sitepagedocument_browse' => array(
            'route' => $routeStart.'/browse/*',
            'defaults' => array(
                'module' => 'sitepagedocument',
                'controller' => 'index',
                'action' => 'browse',
            ),
        ),

        'sitepagedocument_home' => array(
            'route' => $routeStart.'/home/*',
            'defaults' => array(
                'module' => 'sitepagedocument',
                'controller' => 'index',
                'action' => 'home',
            ),
        ), 
    )
)
?>