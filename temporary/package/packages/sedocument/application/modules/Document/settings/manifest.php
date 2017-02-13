<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

//START WORK RELATED TO CUSTOMIZE URLs
$routeStartP = "documents";
$routeStartS = "document";
$module=null;$controller=null;$action=null;
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
  $module = $request->getModuleName();
  $action = $request->getActionName();
  $controller = $request->getControllerName();
}
if (empty($request) || !($module == "default" && $controller == "sdk" && $action == "build")) {
  $routeStartP = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.manifestUrlP', "documents");
  $routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.manifestUrlS', "document");
}
//END WORK RELATED TO CUSTOMIZE URLs

return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'document',
    'version' => '4.6.0',
    'path' => 'application/modules/Document',
		'repository' => 'null',
		'title' => 'Documents',
		'description' => 'Documents / Scribd iPaper plugin allows your users to upload and display documents; share, print and download them; add tags to documents, categorize them and give comments.',
'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
		'date' => 'Thursday, 11 Aug 2010 18:33:08 +0000',
		'copyright' => 'Copyright 2009-2010 BigStep Technologies Pvt. Ltd.',
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Document/settings/install.php',
      'class' => 'Document_Installer',
    ),
    'directories' => array(
      'application/modules/Document',
    ),
    'files' => array(
      'application/languages/en/document.csv',
    ),
  ),
     'sitemobile_compatible' =>true,
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Document_Plugin_Core',
    ),
    
    array(
      'event' => 'onStatistics',
      'resource' => 'Document_Plugin_Core'
    ),
    array(
       'event' => 'onRenderLayoutMobileSMDefault',
       'resource' => 'Document_Plugin_Sitemobile',
     ),
    
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'document',
		'document_search',
		'document_itemofthedays',
		'document_profilemap'
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
  
		'document_general' => array(
				'route' => $routeStartS.'/category/:action/*',
				'defaults' => array(
						'module' => 'document',
						'controller' => 'index',
						'action' => 'sub-category',
				),
				'reqs' => array(
						'action' => '(sub-category|subsub-category)',
				),
		),

    'document_create' => array(
      'route' => $routeStartS.'/create/*',
      'defaults' => array(
        'module' => 'document',
        'controller' => 'index',
        'action' => 'create'
      )
    ),
    
    'document_delete' => array(
      'route' => $routeStartS.'/delete/:document_id',
      'defaults' => array(
        'module' => 'document',
        'controller' => 'index',
        'action' => 'delete'
      ),
      'reqs' => array(
        'document_id' => '\d+'
      )
    ),
    
    'document_publish' => array(
      'route' => $routeStartS.'/publish/:document_id',
      'defaults' => array(
        'module' => 'document',
        'controller' => 'index',
        'action' => 'publish'
      ),
      'reqs' => array(
        'document_id' => '\d+'
      )
    ),

		'document_profile_doc' => array(
				'route' => $routeStartS.'/profileDoc/:document_id',
				'defaults' => array(
						'module' => 'document',
						'controller' => 'index',
						'action' => 'profile-doc'
				),
				'reqs' => array(
						'document_id' => '\d+',
				)
		),
    
    'document_browse' => array(
      'route' => $routeStartP.'/:action/*',
      'defaults' => array(
        'module' => 'document',
        'controller' => 'index',
        'action' => 'browse',
        //'sort' => 'recent',
      ),
    ),

    'document_home' => array(
      'route' => $routeStartP.'/home/*',
      'defaults' => array(
        'module' => 'document',
        'controller' => 'index',
        'action' => 'home',
        //'sort' => 'recent',
      ),
    ),

		'document_ajaxhome' => array(
				'route' => $routeStartP.'/ajaxhomedocuments/',
				'defaults' => array(
						'module' => 'document',
						'controller' => 'index',
						'action' => 'ajax-home-documents'
				)
		),

		'document_homesponsored' => array(
				'route' => $routeStartP.'/homesponsored',
				'defaults' => array(
						'module' => 'document',
						'controller' => 'index',
						'action' => 'home-sponsored'
				)
		),
    
    'document_manage' => array(
      'route' => $routeStartP.'/manage',
      'defaults' => array(
        'module' => 'document',
        'controller' => 'index',
        'action' => 'manage'
      )
    ),
    
    'document_edit' => array(
      'route' => $routeStartP.'/edit/:document_id',
      'defaults' => array(
        'module' => 'document',
        'controller' => 'index',
        'action' => 'edit'
      )
    ),
    
    'document_list' => array(
      'route' => $routeStartP.'/list/:user_id',
      'defaults' => array(
        'module' => 'document',
        'controller' => 'index',
        'action' => 'list',
      ),
      'reqs' => array(
        'user_id' => '\d+'
      )
    ),
    
    'document_detail_view' => array(
      'route' => $routeStartP.'/:user_id/:document_id/:slug',
      'defaults' => array(
        'module' => 'document',
        'controller' => 'index',
        'action' => 'view',
        'slug' => '',
      ),
      'reqs' => array(
        'user_id' => '\d+',
        'document_id' => '\d+'
      )
    ),

    'document_tagscloud' => array(
      'route' => $routeStartP.'/tagscloud',
      'defaults' => array(
        'module' => 'document',
        'controller' => 'index',
        'action' => 'tagscloud'
      )
    ),
  ) 
)
?>