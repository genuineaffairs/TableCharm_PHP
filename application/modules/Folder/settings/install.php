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
 
class Folder_Installer extends Engine_Package_Installer_Module
{
  
  public function addPopularTagsPage()
  {
    // folder Home page
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);
    
      // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'folder_index_tags')
      ->limit(1);
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {

      $db->insert('engine4_core_pages', array(
        'name' => 'folder_index_tags',
        'displayname' => 'Folder Popular Tags',
        'title' => 'Folder Popular Tags',
        'description' => 'This is the popular tags page for folders.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      $i = 1;
      
      // CONTAINERS (TOP / MAIN)
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $top_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $main_id = $db->lastInsertId('engine4_core_content');

      // ---------- CONTAINER TOP & WIDGET MENU -----------
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_id,
        'order' => $i++,
        'params' => '',
      ));
      $top_middle_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.main-menu',
        'parent_content_id' => $top_middle_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $top_middle_menu_id = $db->lastInsertId('engine4_core_content');
      
      // ---------- CONTAINER MAIN -----------      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_right_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_middle_id = $db->lastInsertId('engine4_core_content');
      
    
      // ------ MAIN :: RIGHT WIDGETS     
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.search-form',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
    
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.create-new',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));

      // ------ MAIN :: MIDDLE WIDGETS   

      // tab
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.container-tabs',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '{"max":"6"}',
      ));
      // tab items
      $tab_id = $db->lastInsertId('engine4_core_content');
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.popular-tags',
        'parent_content_id' => $tab_id,
        'order' => $i++,
        'params' => '{"title":"Popular Tags","max":"999","showlinkall":"0"}',
      ));    

    }
  }
  
  public function addBrowsePage()
  {
    // folder Home page
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);
    
      // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'folder_index_browse')
      ->limit(1);
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {

      $db->insert('engine4_core_pages', array(
        'name' => 'folder_index_browse',
        'displayname' => 'Folder Browse Page',
        'title' => 'Folder Browse Page',
        'description' => 'This is the browse page for folders.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      $i = 1;
      
      // CONTAINERS (TOP / MAIN)
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $top_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $main_id = $db->lastInsertId('engine4_core_content');

      // ---------- CONTAINER TOP & WIDGET MENU -----------
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_id,
        'order' => $i++,
        'params' => '',
      ));
      $top_middle_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.main-menu',
        'parent_content_id' => $top_middle_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $top_middle_menu_id = $db->lastInsertId('engine4_core_content');
      
      // ---------- CONTAINER MAIN -----------      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_right_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_middle_id = $db->lastInsertId('engine4_core_content');
      
    
      // ------ MAIN :: RIGHT WIDGETS
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.browse-parent-item',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.search-form',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
    
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.create-new',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));

      // ------ MAIN :: MIDDLE WIDGETS   

      // tab
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.container-tabs',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '{"max":"6"}',
      ));
      // tab items
      $tab_id = $db->lastInsertId('engine4_core_content');
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.browse-folders',
        'parent_content_id' => $tab_id,
        'order' => $i++,
        'params' => '{"title":"Browse Folders"}',
      ));    
      
      /*
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.browse-folders',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '{"title":"Browse Folders"}',
      ));
	  */
      
    }
  } 
  
  
  public function addProfilePage()
  {
    $db = $this->getDb();
    $select = new Zend_Db_Select($db);

    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $hasWidget = $select
      ->from('engine4_core_pages', new Zend_Db_Expr('TRUE'))
      ->where('name = ?', 'folder_profile_index')
      ->limit(1)
      ->query()
      ->fetchColumn()
      ;

    // Add it
    if( empty($hasWidget) ) {

      $db->insert('engine4_core_pages', array(
        'name' => 'folder_profile_index',
        'displayname' => 'Folder Profile',
        'title' => 'Folder Profile',
        'description' => 'This is the profile for a folder.',
        'custom' => 0,
        //'provides' => 'subject=folder', // requires SE v4.1.2
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      // containers
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
      ));
      $container_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_id,
        'order' => 3,
        'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $container_id,
        'order' => 1,
        'params' => '',
      ));
      $right_id = $db->lastInsertId('engine4_core_content');

      // middle column
      $m = 0;
      /*
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.profile-notice',
        'parent_content_id' => $middle_id,
        'order' => ++$m,
        'params' => '',
      ));
      */      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.profile-breadcrumb',
        'parent_content_id' => $middle_id,
        'order' => ++$m,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.profile-title',
        'parent_content_id' => $middle_id,
        'order' => ++$m,
        'params' => '',
      ));  
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.profile-files',
        'parent_content_id' => $middle_id,
        'order' => ++$m,
        'params' => '',
      ));      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.container-tabs',
        'parent_content_id' => $middle_id,
        'order' => ++$m,
        'params' => '{"max":"6"}',
      ));
      $tab_id = $db->lastInsertId('engine4_core_content');



      // right column
      $r = 0;
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.profile-photo',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":""}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.profile-icon-sponsored',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":"","text":"SPONSORED FOLDER"}',
      ));      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.profile-icon-featured',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":"","text":"FEATURED FOLDER"}',
      ));   
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.profile-submitter',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":""}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.profile-info',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":"Folder Info"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.profile-stats',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":"Folder Statistics"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.profile-options',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":""}',
      ));    
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.profile-social-shares',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":""}',
      ));
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.profile-tools',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":""}',
      ));
      
      
      // tabs
      $t = 0;  
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'activity.feed',
        'parent_content_id' => $tab_id,
        'order' => ++$t,
        'params' => '{"title":"Updates"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.profile-details',
        'parent_content_id' => $tab_id,
        'order' => ++$t,
        'params' => '{"title":"Details"}',
      ));      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.profile-comments',
        'parent_content_id' => $tab_id,
        'order' => ++$t,
        'params' => '{"title":"Comments"}',
      ));
      /*
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.profile-related-folders',
        'parent_content_id' => $tab_id,
        'order' => ++$t,
        'params' => '{"titleCount":true,"title":"Related Folders","max":5,"order":"random"}',
      ));
      */
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.profile-links',
        'parent_content_id' => $tab_id,
        'order' => ++$t,
        'params' => '{"title":"Links","titleCount":true}',
      ));
    }
  }
  // addProfilePage
  
  
  
  
  public function addFileProfilePage()
  {
    $db = $this->getDb();
    $select = new Zend_Db_Select($db);

    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $hasWidget = $select
      ->from('engine4_core_pages', new Zend_Db_Expr('TRUE'))
      ->where('name = ?', 'folder_attachment_view')
      ->limit(1)
      ->query()
      ->fetchColumn()
      ;

    // Add it
    if( empty($hasWidget) ) {

      $db->insert('engine4_core_pages', array(
        'name' => 'folder_attachment_view',
        'displayname' => 'Folder File Profile',
        'title' => 'Folder File Profile',
        'description' => 'This is the profile for a file.',
        'custom' => 0,
        //'provides' => 'subject=folder', // requires SE v4.1.2
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      // containers
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
      ));
      $container_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_id,
        'order' => 3,
        'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $container_id,
        'order' => 1,
        'params' => '',
      ));
      $right_id = $db->lastInsertId('engine4_core_content');

      // middle column
      $m = 0;    
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.file-breadcrumb',
        'parent_content_id' => $middle_id,
        'order' => ++$m,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.file-title',
        'parent_content_id' => $middle_id,
        'order' => ++$m,
        'params' => '',
      ));  
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.file-download',
        'parent_content_id' => $middle_id,
        'order' => ++$m,
        'params' => '',
      ));      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.file-folder-link',
        'parent_content_id' => $middle_id,
        'order' => ++$m,
        'params' => '',
      ));       
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.file-share-link',
        'parent_content_id' => $middle_id,
        'order' => ++$m,
        'params' => '',
      ));  
      // right column
      $r = 0;   
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.file-details',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":"File Details"}',
      ));
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.file-social-shares',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '',
      ));
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.file-tools',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '',
      ));

    }
  }
  // addFileProfilePage
 
  
  
  public function addUserProfileTab()
  {
    //
    // install content areas
    //
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);

    // profile page
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'user_profile_index')
      ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;


    // folder.profile-folders
    
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_content')
      ->where('page_id = ?', $page_id)
      ->where('type = ?', 'widget')
      ->where('name = ?', 'folder.profile-folders')
      ;
    $info = $select->query()->fetch();
    if( empty($info) ) {
    
      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('page_id = ?', $page_id)
        ->where('type = ?', 'container')
        ->limit(1);
      $container_id = $select->query()->fetchObject()->content_id;

      // middle_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('parent_content_id = ?', $container_id)
        ->where('type = ?', 'container')
        ->where('name = ?', 'middle')
        ->limit(1);
      $middle_id = $select->query()->fetchObject()->content_id;

      // tab_id (tab container) may not always be there
      $select
        ->reset('where')
        ->where('type = ?', 'widget')
        ->where('name = ?', 'core.container-tabs')
        ->where('page_id = ?', $page_id)
        ->limit(1);
      $tab_id = $select->query()->fetchObject();
      if( $tab_id && @$tab_id->content_id ) {
          $tab_id = $tab_id->content_id;
      } else {
        $tab_id = null;
      }

      // tab on profile
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type'    => 'widget',
        'name'    => 'folder.profile-folders',
        'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
        'order'   => 6,
        'params'  => '{"title":"Folders","titleCount":true,"max":5,"order":"recent"}',
      ));

    }
  }
  // addUserProfileTab
  
  public function addHomePage()
  {
    // folder Home page
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);
    
      // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'folder_index_index')
      ->limit(1);
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {

      $db->insert('engine4_core_pages', array(
        'name' => 'folder_index_index',
        'displayname' => 'Folder Home Page',
        'title' => 'Folder Home Page',
        'description' => 'This is the home / landing page for folders.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      $i = 1;
      
      // CONTAINERS (TOP / MAIN)
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $top_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => $i++,
        'params' => '',
      ));
      $main_id = $db->lastInsertId('engine4_core_content');

      // ---------- CONTAINER TOP & WIDGET MENU -----------
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $top_id,
        'order' => $i++,
        'params' => '',
      ));
      $top_middle_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.main-menu',
        'parent_content_id' => $top_middle_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $top_middle_menu_id = $db->lastInsertId('engine4_core_content');
      
      // ---------- CONTAINER MAIN -----------
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'left',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_left_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_right_id = $db->lastInsertId('engine4_core_content');
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_middle_id = $db->lastInsertId('engine4_core_content');
      
      // ------ MAIN :: LEFT WIDGETS  
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.categories',
        'parent_content_id' => $main_left_id,
        'order' => $i++,
        'params' => '{"title":"","photo":1,"details":0,"descriptionlength":0}',
      )); 
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.top-submitters',
        'parent_content_id' => $main_left_id,
        'order' => $i++,
        'params' => '{"title":"Top Posters"}',
      ));
      
      // ------ MAIN :: RIGHT WIDGETS
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.search-form',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));     
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.create-new',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.sponsored-folders',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":"Sponsored Folders"}',
      ));
      
      // ------ MAIN :: MIDDLE WIDGETS   
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.featured-folders',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '{"title":"Featured Folders"}',
      ));
      
      // tab
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.container-tabs',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '{"max":"6"}',
      ));
      // tab items
      $tab_id = $db->lastInsertId('engine4_core_content');
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.list-folders',
        'parent_content_id' => $tab_id,
        'order' => $i++,
        'params' => '{"title":"Recent Folders","max":10,"order":"recent","display_style":"wide","showemptyresult":1}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'folder.popular-tags',
        'parent_content_id' => $tab_id,
        'order' => $i++,
        'params' => '{"title":"Popular Tags","max":"50","showlinkall":"1"}',
      )); 
    }
  } 
  // addHomePage
  
  public function onInstall()
  {
    $this->addHomePage();
    $this->addBrowsePage();
    $this->addPopularTagsPage();
    $this->addProfilePage();
    $this->addFileProfilePage();
    $this->addUserProfileTab();
    
    parent::onInstall();
  }
}
