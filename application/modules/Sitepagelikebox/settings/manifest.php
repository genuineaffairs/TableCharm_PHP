<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagelikebox
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2011-10-10 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$routeStartP = "pageitems";
$routeStartS = "pageitem";
$module=null;$controller=null;$action=null;
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
  $module = $request->getModuleName(); // Return the current module name.
  $action = $request->getActionName();
  $controller = $request->getControllerName();
}
if (empty($request) || !($module == "default" && $controller == "sdk" && $action == "build")) {
  $routeStartP = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manifestUrlP', "pageitems");
  $routeStartS = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manifestUrlS', "pageitem");
}
return array (
  'package' =>
  array (
    'type' => 'module' ,
    'name' => 'sitepagelikebox' ,
    'version' => '4.7.0' ,
    'path' => 'application/modules/Sitepagelikebox' ,
    'title' => 'Directory / Pages - Embeddable Badges, Like Box Extension' ,
    'description' => 'Directory / Pages - Embeddable Badges, Like Box Extension' ,
    'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
		'callback' => array(
				'path' => 'application/modules/Sitepagelikebox/settings/install.php',
				'class' => 'Sitepagelikebox_Installer',
		),
    'actions' =>
    array (
      0 => 'install' ,
      1 => 'upgrade' ,
      2 => 'refresh' ,
      3 => 'enable' ,
      4 => 'disable' ,
    ) ,
    'directories' =>
    array (
      0 => 'application/modules/Sitepagelikebox' ,
    ) ,
    'files' =>
    array (
      0 => 'application/languages/en/sitepagelikebox.csv' ,
    ) ,
  ) ,
  //Route--------------------------------------------------------------------
  'routes' => array (
    'sitepagelikebox_general' => array (
      'route' => $routeStartP.'/likebox/:action/*' ,
      'defaults' => array (
        'module' => 'sitepagelikebox' ,
        'controller' => 'index' ,
      ) ,
      'reqs' => array (
        'action' => '(index|like-box|get-like-code|has-login|login|like|unlike)' ,
      ) ,
    ) ,
  ) ,
) ;
?>