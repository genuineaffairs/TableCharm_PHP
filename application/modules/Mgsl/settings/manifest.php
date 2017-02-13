<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'mgsl',
    'version' => '4.0.1',
    'path' => 'application/modules/Mgsl',
    'title' => 'MGSL',
    'description' => 'MGSL module',
    'author' => 'My Global Sport Link',
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
      0 => 'application/modules/Mgsl',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/mgsl.csv',
    ),
  ),
  // Routes ------------------------------------------------------------------
  'routes' => array(
    'mgsl_general' => array(
        'route' => 'mgsl/:action/*',
        'defaults' => array(
            'module' => 'mgsl',
            'controller' => 'index',
        )
    )
  )
); ?>