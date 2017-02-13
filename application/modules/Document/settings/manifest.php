<?php return array (
  'package' => array(
    'type' => 'module',
    'name' => 'document',
    'version' => '4.0.0',
    'path' => 'application/modules/Document',
    'title' => 'Document',
    'description' => 'Document module',
    'author' => 'My Global Sport Link',
    'callback' => array(
      'path' => 'application/modules/Document/settings/install.php',
      'class' => 'Document_Installer',
    ),
    'actions' => array(
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => array(
      0 => 'application/modules/Document',
    ),
    'files' => array(
      0 => 'application/languages/en/document.csv',
    ),
  ),
  'composer' => array(
    'document' => array( // the composer name here is important. it is used to call the "onAttach" function in Document_Plugin_Composer.
      'script' => array('_composeDocument.tpl', 'document'), // 1st param: view, 2nd param: module name.
      'plugin' => 'Document_Plugin_Composer',
      'auth' => array('document', 'create'), // user must have permission to create documents. permissions are defined in the "engine4_authorization_permissions" table and the module's install script.
    ),
  ),
  'items' => array(
    'document', // adds an item to the engine's global item table. allows you to do Engine_Api::_()->getItem('document', <document_id>).
  ),
  'routes' => array(
    'document_general' => array(
        'route' => 'documents/:action/*',
        'defaults' => array(
            'module' => 'document',
            'controller' => 'index',
            'action' => 'browse',
        ),
        'reqs' => array(
            'action' => '(browse|manage|edit|delete)',
        ),
    ),
    'document_view' => array(
      'route' => 'documents/:user_id/:document_id/:slug/*',
      'defaults' => array(
        'module' => 'document',
        'controller' => 'index',
        'action' => 'view',
        'slug' => '',
      ),
      'reqs' => array(
        'user_id' => '\d+',
      ),
    ),
  ),
); ?>