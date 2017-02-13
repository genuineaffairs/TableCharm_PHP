<?php return array (
  // Packages---------------------------------------------------------------------
  'package' =>
  array (
    'type' => 'module',
    'name' => 'advgroup',
    'version' => '4.05p3',
    'path' => 'application/modules/Advgroup',
    'title' => 'Advanced Groups',
    'description' => 'Advanced Groups allow member to create groups, post photos,albums, polls or discussion, etc .. on their groups.',
    'author' => 'YouNet Company',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.2',
      ),
      array(
        'type' => 'module',
        'name' => 'group',
        'minVersion' => '4.1.5',
      ),
      array(
         'type' => 'module',
         'name' => 'younet-core',
         'minVersion' => '4.02',
      ),
    ),
    'callback' => array (
      'path' => 'application/modules/Advgroup/settings/install.php',
      'class' => 'Advgroup_Installer',
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
      0 => 'application/modules/Advgroup',
    ),
    'files' =>
    array (
      0 => 'application/languages/en/yngroup.csv',
      1 => 'application/modules/Messages/controllers/MessagesController.php'
    ),
  ),
    // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onStatistics',
      'resource' => 'Advgroup_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Advgroup_Plugin_Core',
    ),
    array(
      'event' => 'getActivity',
      'resource' => 'Advgroup_Plugin_Core',
    ),
    array(
      'event' => 'addActivity',
      'resource' => 'Advgroup_Plugin_Core',
    ),
    array(
      'event' => 'onUserCreateAfter',
      'resource' => 'Advgroup_Plugin_Signup',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'advgroup_announcement',
    'group',
    'advgroup_album',
    'advgroup_category',
    'advgroup_list',
    'advgroup_list_item',
    'advgroup_photo',
    'advgroup_post',
    'advgroup_topic',
    'advgroup_link',
    'advgroup_poll',
    'advgroup_video',
  	'advgroup_report',
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'group_extended' => array(
      'route' => 'groups/:controller/:action/*',
      'defaults' => array(
        'module' => 'advgroup',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'controller' => '\D+',
        'action' => '\D+',
      )
    ),
    'group_general' => array(
      'route' => 'groups/:action/*',
      'defaults' => array(
        'module' => 'advgroup',
        'controller' => 'index',
        'action' => 'browse',
      ),
      'reqs' => array(
        'action' => '(browse|create|listing|manage|post)',
      )
    ),
    'group_specific' => array(
      'route' => 'groups/:action/:group_id/*',
      'defaults' => array(
        'module' => 'advgroup',
        'controller' => 'group',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(edit|delete|join|leave|cancel|accept|invite|style|transfer)',
        'group_id' => '\d+',
      )
    ),
    'group_profile' => array(
      'route' => 'group/:id/:slug/*',
      'defaults' => array(
        'module' => 'advgroup',
        'controller' => 'profile',
        'action' => 'index',
        'slug' => '',
      ),
      'reqs' => array(
        'id' => '\d+',
      )
    ),
    'group_browse' => array(
      'route' => 'group/browse',
      'defaults' => array(
        'module' => 'advgroup',
        'controller' => 'index',
        'action' => 'browse'
      )
    ),
    'group_link' =>array(
      'route' => 'groups/link/:action/*',
      'defaults' =>array(
        'module' => 'advgroup',
        'controller' => 'link',
        'action' => 'create',
       ),
      'reqs' => array(
        'action' => '(create|manage|edit|delete)',
      )
    ),
  	'group_report' => array(
  		'route' => 'group/report/:group_id/*',
  		'defaults' => array(
  			'module' => 'advgroup',
  			'controller' => 'report',
  			'action' => 'add')
  	),
  	'reqs' => array(
  		'group_id' => '\d+',
  	),
  	'group_activity' => array(
        'route' => 'group/:action/*',
        'defaults' => array(
          'module' => 'advgroup',
          'controller' => 'activity',
          'action' => 'activity',
        ),
        'reqs' => array(
          'action' => '(activity|viewmore)',
        )
      ),
  )
); ?>