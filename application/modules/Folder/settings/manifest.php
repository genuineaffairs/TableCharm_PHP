<?php 

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Folder
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */

return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'folder',
    'version' => '4.1.8',
    'path' => 'application/modules/Folder',
    'repository' => 'radcodes.com',

    'title' => 'Folder / File Sharing Plugin',
    'description' => 'This plugin your member create folders and share files.',
    'author' => 'Radcodes LLC',  

    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Folder/settings/install.php',
      'class' => 'Folder_Installer',
    ),
    'dependencies' => array(
      'radcodes' => array(
        'type' => 'module',
        'name' => 'radcodes',
        'minVersion' => '4.1.1'
      ),     
    ),
    'directories' => array(
      'application/modules/Folder',
    ),
    'files' => array(
      'application/languages/en/folder.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onStatistics',
      'resource' => 'Folder_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Folder_Plugin_Core',
    ),
    array(
      'event' => 'onItemDeleteBefore',
      'resource' => 'Folder_Plugin_Core',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'folder',
    'folder_category',
    'folder_attachment',
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'folder_extended' => array(
      'route' => 'folders/:controller/:action/*',
      'defaults' => array(
        'module' => 'folder',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'controller' => '\D+',
        'action' => '\D+',
      )
    ),
    'folder_general' => array(
      'route' => 'folders/:action/*',
      'defaults' => array(
        'module' => 'folder',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(index|browse|create|manage|tags|upload-photo)',
      )
    ),   
    'folder_specific' => array(
      'route' => 'folders/item/:folder_id/:action/*',
      'defaults' => array(
        'module' => 'folder',
        'controller' => 'folder',
      ),
      'reqs' => array(
        'action' => '(edit|delete|success|manage|upload|upload-attachment|remove-attachment)',
        'folder_id' => '\d+',
      )
    ),
    'folder_profile' => array(
      'route' => 'folder/:folder_id/:slug/*',
      'defaults' => array(
        'module' => 'folder',
        'controller' => 'profile',
        'action' => 'index',
        'slug' => ''
      ),
      'reqs' => array(
        'folder_id' => '\d+',
      )
    ),
    'folder_attachment_specific' => array(
      'route' => 'folders/attachment/:attachment_id/:action/*',
      'defaults' => array(
        'module' => 'folder',
        'controller' => 'attachment',
      ),
      'reqs' => array(
        'action' => '(download|edit|delete)',
        'attachment_id' => '\d+',
      )
    ),
    'folder_attachment_profile' => array(
      'route' => 'folders/file/:attachment_id/*',
      'defaults' => array(
        'module' => 'folder',
        'controller' => 'attachment',
        'action' => 'view',
      ),
      'reqs' => array(
        'attachment_id' => '\d+',
      )
    ),    
  ),
);
