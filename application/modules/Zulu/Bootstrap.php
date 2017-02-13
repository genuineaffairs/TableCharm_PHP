<?php

class Zulu_Bootstrap extends Engine_Application_Bootstrap_Abstract {

  public function __construct($application)
  {
    parent::__construct($application);

    // Temporary disable Zulu Module for mobile for this version
//        if (!Engine_Api::_()->zulu()->isMobileMode()) {
    $routes = array(
        'zulu_extended' => array(
            'route' => 'members/:controller/:action/*',
            'defaults' => array(
                'module' => 'zulu',
                'controller' => 'index',
                'action' => 'index'
            ),
            'reqs' => array(
                'controller' => 'edit',
                'action' => '\D+',
            )
        ),
        'zulu_general' => array(
            'route' => 'members/:action/*',
            'defaults' => array(
                'module' => 'user',
                'controller' => 'index',
                'action' => 'browse'
            ),
            'reqs' => array(
                'action' => '(home|browse)',
            )
        ),
//                'zulu_profile' => array(
//                    'route' => 'profile/:id/*',
//                    'defaults' => array(
//                        'module' => 'zulu',
//                        'controller' => 'profile',
//                        'action' => 'index'
//                    )
//                ),
        'zulu_print' => array(
            'route' => 'profile/:id/print',
            'defaults' => array(
                'module' => 'zulu',
                'controller' => 'profile',
                'action' => 'print'
            ),
        ),
        'zulu_login' => array(
            //'type' => 'Zend_Controller_Router_Route_Static',
            'route' => '/login/*',
            'defaults' => array(
                'module' => 'zulu',
                'controller' => 'auth',
                'action' => 'login'
            )
        ),
//        'user_logout' => array(
//            'type' => 'Zend_Controller_Router_Route_Static',
//            'route' => '/logout',
//            'defaults' => array(
//                'module' => 'user',
//                'controller' => 'auth',
//                'action' => 'logout'
//            )
//        ),
        'zulu_signup' => array(
            'route' => '/signup/:action/*',
            'defaults' => array(
                'module' => 'zulu',
                'controller' => 'signup',
                'action' => 'index'
            )
        ),
    );
    Zend_Registry::get('Zend_Controller_Front')->getRouter()->addConfig(new Zend_Config($routes));
//        }
    // Add view helper and action helper paths
    $this->initViewHelperPath();
    $this->initActionHelperPath();

    // Add main user javascript
    //$headScript = new Zend_View_Helper_HeadScript();
    //$headScript->appendFile('application/modules/User/externals/scripts/core.js');
    // Get viewer
    $viewer = Engine_Api::_()->user()->getViewer();

    // Check if they were disabled
    if ($viewer->getIdentity() && !$viewer->enabled) {
      Engine_Api::_()->user()->getAuth()->clearIdentity();
      Engine_Api::_()->user()->setViewer(null);
    }

    // Check user online state
    $table = Engine_Api::_()->getDbtable('online', 'user');
    $table->check($viewer);
  }

  protected function _initRequest()
  {
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    // Append necessary scripts for this module
    $jsFiles = array('jquery.js', 'fields.js');
    foreach ($jsFiles as $file) {
      $view->headScript()->appendFile($view->layout()->staticBaseUrl . 'application/modules/Zulu/externals/js/' . $file);
    }
    $view->headScript()->appendScript('jQuery.noConflict()');

    // Append necessary styles for this module
    $cssFiles = array('global.css', 'fields.css');
    foreach ($cssFiles as $file) {
      $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl . 'application/modules/Zulu/externals/css/' . $file);
    }

    // Register module plugin core (for hooking purpose)
    Zend_Registry::get('Zend_Controller_Front')->registerPlugin(new Zulu_Plugin_Core());
    Zend_Controller_Action_HelperBroker::addHelper(new Zulu_Controller_Action_Helper_Hooks());
  }

  public function _bootstrap($resource = null)
  {
    parent::_bootstrap($resource);

    $fields = include APPLICATION_PATH . '/application/modules/Zulu/settings/fields.php';
    Engine_Api::_()->zulu()->addFields($fields);
  }

}
