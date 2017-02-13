<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'mgslapi',
    'version' => '4.0.2',
    'path' => 'application/modules/Mgslapi',
    'title' => 'mgslapi',
    'description' => 'My Global Sport Link Api',
    'author' => 'Tristan (Former Author: Technobd)',
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Module',
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Mgslapi',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/mgslapi.csv',
    ),
  ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onActivityNotificationCreateAfter',
            'resource' => 'Mgslapi_Plugin_Core',
        )
    ),
     // Routes --------------------------------------------------------------------
    'routes' => array(      
        'mgslapi_ios' => array(
            'route' => 'mgslapi/ios/:action/*',
            'defaults' => array(
                'module' => 'mgslapi',
                'controller' => 'Iosapi',
            ),
//            'reqs' => array(
//                'action' => '(TEMPLATEACTION|userlogin)',
//            )
        ), 
        'mgslapi_android' => array(
            'route' => 'mgslapi/android/:action/*',
            'defaults' => array(
                'module' => 'mgslapi',
                'controller' => 'androidapi',
            ),
        ),
        'mgslapi_android_testtool' => array(
            'type' => 'Zend_Controller_Router_Route_Static',
            'route' => 'mgslapi/android/testtool',
            'defaults' => array(
                'module' => 'mgslapi',
                'controller' => 'index',
                'action' => 'android',
            )
        ),
        'mgslapi_ios_testtool' => array(
            'type' => 'Zend_Controller_Router_Route_Static',
            'route' => 'mgslapi/ios/testtool',
            'defaults' => array(
                'module' => 'mgslapi',
                'controller' => 'index',
                'action' => 'ios',
            )
        ),
    ),
); ?>