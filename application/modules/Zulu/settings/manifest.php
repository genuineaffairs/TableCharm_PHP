<?php

return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'zulu',
        'version' => '4.0.3',
        'path' => 'application/modules/Zulu',
        'title' => 'Zulu',
        'description' => 'Electronic Medical Record',
        'author' => 'Tristan',
        'callback' =>
        array(
            'path' => 'application/modules/Zulu/settings/install.php',
            'class' => 'Zulu_Installer',
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
            0 => 'application/modules/Zulu',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/zulu.csv',
        ),
    ),
    // Hooks -------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onUserDeleteAfter',
            'resource' => 'Zulu_Plugin_Core'
        ),
        array(
            'event' => 'onFieldsValuesSave',
            'resource' => 'Zulu_Plugin_Core'
        ),
    ),
    // Items -------------------------------------------------------------------
    'items' => array(
        'zulu'
    ),
    // Routes ------------------------------------------------------------------
    'routes' => array(
//        // User - General
//        'zulu_extended' => array(
//            'route' => 'members/:controller/:action/*',
//            'defaults' => array(
//                'module' => 'zulu',
//                'controller' => 'index',
//                'action' => 'index'
//            ),
//            'reqs' => array(
//                'controller' => 'edit',
//                'action' => '\D+',
//            )
//        ),
//        'zulu_general' => array(
//            'route' => 'members/:action/*',
//            'defaults' => array(
//                'module' => 'user',
//                'controller' => 'index',
//                'action' => 'browse'
//            ),
//            'reqs' => array(
//                'action' => '(home|browse)',
//            )
//        ),
//        // User - Specific
//        'user_profile' => array(
//            'route' => 'profile/:id/*',
//            'defaults' => array(
//                'module' => 'user',
//                'controller' => 'profile',
//                'action' => 'index'
//            )
//        ),
//        'zulu_login' => array(
//            //'type' => 'Zend_Controller_Router_Route_Static',
//            'route' => '/login/*',
//            'defaults' => array(
//                'module' => 'zulu',
//                'controller' => 'auth',
//                'action' => 'login'
//            )
//        ),
//        'user_logout' => array(
//            'type' => 'Zend_Controller_Router_Route_Static',
//            'route' => '/logout',
//            'defaults' => array(
//                'module' => 'user',
//                'controller' => 'auth',
//                'action' => 'logout'
//            )
//        ),
//        'zulu_signup' => array(
//            'route' => '/signup/:action/*',
//            'defaults' => array(
//                'module' => 'zulu',
//                'controller' => 'signup',
//                'action' => 'index'
//            )
//        ),
    )
);
?>