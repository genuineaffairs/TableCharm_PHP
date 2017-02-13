<?php return array (
  'package' => 
  array (
    'type' => 'theme',
    'name' => 'mgsl',
    'version' => NULL,
    'revision' => '$Revision: 9747 $',
    'path' => 'application/themes/mgsl',
    'repository' => 'socialengine.com',
    'title' => 'MGSL',
    'thumb' => 'snowbot_theme.jpg',
    'author' => 'My Global Sport Link',
    'changeLog' => 
    array (
      '4.2.0' => 
      array (
        'manifest.php' => 'Incremented version',
        'theme.css' => 'Fixed issue with feed comment option list',
      ),
      '4.1.8p1' => 
      array (
        'manifest.php' => 'Incremented version',
        'theme.css' => 'Fixed issue with new pages in the layout editor',
      ),
      '4.1.8' => 
      array (
        'manifest.php' => 'Incremented version',
        'mobile.css' => 'Added styles for HTML5 input elements',
        'theme.css' => 'Added styles for HTML5 input elements',
      ),
      '4.1.4' => 
      array (
        'mainfest.php' => 'Incremented version',
        'mobile.css' => 'Added new type of stylesheet',
      ),
      '4.0.1' => 
      array (
        'manifest.php' => 'Incremented version; removed deprecated meta key',
      ),
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'remove',
    ),
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' => 
    array (
      0 => 'application/themes/snowbot',
    ),
    'description' => 'MGSL Theme',
  ),
  'files' => 
  array (
    0 => 'theme.css',
    1 => 'constants.css',
  ),
); ?>