<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Menus.php 9770 2012-08-30 02:36:05Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_Plugin_UserMenus {

  // core_main

  public function onMenuInitialize_CoreMainHome($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $route = array(
        'route' => 'default',
    );

    if ($viewer->getIdentity()) {
      $route['route'] = 'user_general';
      $route['params'] = array(
          'action' => 'home',
      );
      if ('user' == $request->getModuleName() &&
              'index' == $request->getControllerName() &&
              'home' == $request->getActionName()) {
        $route['active'] = true;
      }
    }
    if ($row->params)
      $route = array_merge($row->params, $route);
    return $route;
  }

}
