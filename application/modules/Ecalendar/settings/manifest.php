<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ecalendar',
    'version' => '4.6.0',
    'path' => 'application/modules/Ecalendar',
    'title' => 'Event Calendar',
    'description' => 'This module allows to display events in the monthly,weekly calendar format',
    'author' => '<a href="ipragmatech.com"> iPragmatech</a>',
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
    'callback' =>
    array(
        'path' => 'application/modules/Ecalendar/settings/install.php',
        'class' => 'Ecalendar_Installer',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Ecalendar',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ecalendar.csv',
    ),
  ),
		'routes' => array(
				'ecalendar_general' => array(
						'route' => 'ecalendar/:controller/:action',
						'defaults' => array(
								'module' => 'ecalendar',
								'controller' => 'index',
								'action' => 'index',
						),
						'reqs' => array(
								'action' => '(index|allevents)',
						)
				),
				
				
				
		)
		); ?>
