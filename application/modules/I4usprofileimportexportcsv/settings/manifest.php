<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'i4usprofileimportexportcsv',
    'version' => '4.0.0',
    'path' => 'application/modules/I4usprofileimportexportcsv',
    'meta' => 
    array (
      'title' => 'I4US Profile Import/Export using CSV File',
      'description' => 'I4US Profile Import/Export CSV File ',
      'author' => 'Integration4us.com',
    ),
    'callback' => array(
      'path' => 'application/modules/I4usprofileimportexportcsv/settings/install.php',
      'class' => 'I4usprofileimportexportcsv_Installer',
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
      0 => 'application/modules/I4usprofileimportexportcsv',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/i4usprofileimportexportcsv.csv',
    ),
  ),
); ?>