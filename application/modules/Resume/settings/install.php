<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Resume_Installer extends Engine_Package_Installer_Module
{

  public function addProfilePage()
  {
    $db = $this->getDb();
    $select = new Zend_Db_Select($db);

    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $hasWidget = $select
      ->from('engine4_core_pages', new Zend_Db_Expr('TRUE'))
      ->where('name = ?', 'resume_profile_index')
      ->limit(1)
      ->query()
      ->fetchColumn()
      ;

    // Add it
    if( empty($hasWidget) ) {

      $db->insert('engine4_core_pages', array(
        'name' => 'resume_profile_index',
        'displayname' => 'Resume - Profile Page',
        'title' => 'Resume Profile',
        'description' => 'This is the profile for a resume.',
        'custom' => 0,
        //'provides' => 'subject=resume', // requires SE v4.1.2
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
        'name' => 'resume.profile-notice',
        'parent_content_id' => $middle_id,
        'order' => ++$m,
        'params' => '',
      ));      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.profile-breadcrumb',
        'parent_content_id' => $middle_id,
        'order' => ++$m,
        'params' => '',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.profile-title',
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
        'name' => 'resume.profile-photo',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":""}',
      ));
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.profile-submitter',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":""}',
      ));
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.profile-icon-sponsored',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":"","text":"SPONSORED RESUME"}',
      ));      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.profile-icon-featured',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":"","text":"FEATURED RESUME"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.profile-icon-package',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":""}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.profile-options',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":""}',
      ));
      /*
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.profile-intro',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":""}',
      ));
            
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.profile-info',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":"Resume Info"}',
      ));
      */
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.profile-stats',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":"Resume Statistics"}',
      ));
   
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.profile-social-shares',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":""}',
      ));
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.profile-tools',
        'parent_content_id' => $right_id,
        'order' => ++$r,
        'params' => '{"title":""}',
      ));
      
      
      // tabs
      $t = 0;
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.profile-body',
        'parent_content_id' => $tab_id,
        'order' => ++$t,
        'params' => '{"title":"Resume"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.profile-details',
        'parent_content_id' => $tab_id,
        'order' => ++$t,
        'params' => '{"title":"Details"}',
      ));      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.profile-location',
        'parent_content_id' => $tab_id,
        'order' => ++$t,
        'params' => '{"title":"Map"}',
      )); 
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.profile-photos',
        'parent_content_id' => $tab_id,
        'order' => ++$t,
        'params' => '{"title":"Portfolio"}',
      ));                  
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
        'name' => 'resume.profile-comments',
        'parent_content_id' => $tab_id,
        'order' => ++$t,
        'params' => '{"title":"Comments"}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.profile-related-resumes',
        'parent_content_id' => $tab_id,
        'order' => ++$t,
        'params' => '{"titleCount":true,"title":"Related Resumes","max":5,"order":"random"}',
      ));
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


    // resume.profile-resumes
    
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_content')
      ->where('page_id = ?', $page_id)
      ->where('type = ?', 'widget')
      ->where('name = ?', 'resume.profile-resumes')
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
        'name'    => 'resume.profile-resumes',
        'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
        'order'   => 6,
        'params'  => '{"title":"Resumes","titleCount":true,"max":5,"order":"recent","showvote":1,"showphoto":1,"showdetails":1,"showmeta":1,"showdescription":1}',
      ));

    }
  }
  // addUserProfileTab
  
  public function addHomePage()
  {
    // resume Home page
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);
    
      // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'resume_index_index')
      ->limit(1);
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {

      $db->insert('engine4_core_pages', array(
        'name' => 'resume_index_index',
        'displayname' => 'Resume - Home Page',
        'title' => 'Resume Home Page',
        'description' => 'This is the home page for resumes.',
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
        'name' => 'resume.main-menu',
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
        'name' => 'resume.categories',
        'parent_content_id' => $main_left_id,
        'order' => $i++,
        'params' => '{"title":"","photo":1,"details":0,"descriptionlength":0}',
      )); 
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.list-resumes',
        'parent_content_id' => $main_left_id,
        'order' => $i++,
        'params' => '{"title":"Most Viewed","max":3,"order":"mostviewed","display_style":"narrow","showphoto":1,"showdetails":1,"showmeta":1,"showdescription":0,"period":"month"}',
      ));      
      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.top-submitters',
        'parent_content_id' => $main_left_id,
        'order' => $i++,
        'params' => '{"title":"Top Posters","max":5}',
      ));
      
      // ------ MAIN :: RIGHT WIDGETS
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.search-form',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.packages-menu',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.create-new',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.sponsored-resumes',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":"Sponsored Resumes"}',
      ));
      
      // ------ MAIN :: MIDDLE WIDGETS   
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.featured-resumes',
        'parent_content_id' => $main_middle_id,
        'order' => $i++,
        'params' => '{"title":"Featured Resumes","max":5}',
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
        'name' => 'resume.list-resumes',
        'parent_content_id' => $tab_id,
        'order' => $i++,
        'params' => '{"title":"Recent Resumes","max":10,"order":"recent","display_style":"wide","showphoto":1,"showdetails":1,"showmeta":1,"showdescription":1,"showemptyresult":1}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.map-resumes',
        'parent_content_id' => $tab_id,
        'order' => $i++,
        'params' => '{"title":"Map","max":10,"order":"recent"}',
      ));      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.popular-tags',
        'parent_content_id' => $tab_id,
        'order' => $i++,
        'params' => '{"title":"Popular Tags"}',
      )); 

      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.packages',
        'parent_content_id' => $tab_id,
        'order' => $i++,
        'params' => '{"title":"Packages","create_link":1,"showdetails":1,"showdescription":1}',
      ));
    }
  } 
  // addHomePage
  
  public function addManagePage()
  {
    $page_name = 'resume_index_manage';
    if (!$this->getPageByName($page_name))
    {
      $page_data = array(
        'name' => $page_name,
        'displayname' => 'Resume - Manage Page',
        'title' => 'Manage Resumes',
        'description' => 'This is the manage page for resumes.',
        'custom' => 0,
      );
      $this->createStandardContentPage($page_data);
    }
  }
  // addManagePage
  
  public function addBrowsePage()
  {
    $page_name = 'resume_index_browse';
    if (!$this->getPageByName($page_name))
    {
      $page_data = array(
        'name' => $page_name,
        'displayname' => 'Resume - Browse Page',
        'title' => 'Browse Resumes',
        'description' => 'This is the browse page for resumes.',
        'custom' => 0,
      );
      $this->createStandardContentPage($page_data);
    }
  }
  // addBrowsePage
  
  public function addTagsPage()
  {
    $page_name = 'resume_index_tags';
    if (!$this->getPageByName($page_name))
    {
      $page_data = array(
        'name' => $page_name,
        'displayname' => 'Resume - Tags Page',
        'title' => 'Resume Tags',
        'description' => 'This is page for resume tags.',
        'custom' => 0,
      );
      $this->createStandardContentPage($page_data);
    }
  }
  // addTagsPage
  
  public function addCreatePage()
  {
    $page_name = 'resume_index_create';
    if (!$this->getPageByName($page_name))
    {
      $page_data = array(
        'name' => $page_name,
        'displayname' => 'Resume - Post New Page',
        'title' => 'Post New Resume',
        'description' => 'This is the post new resume page.',
        'custom' => 0,
      );
      $this->createStandardContentPage($page_data, array('right_column'=>false));
    }
  }
  // addCreatePage
  
  public function addEditPage()
  {
    $page_name = 'resume_resume_edit';
    if (!$this->getPageByName($page_name))
    {
      $page_data = array(
        'name' => $page_name,
        'displayname' => 'Resume - Edit Page',
        'title' => 'Manage/Edit Resume',
        'description' => 'This is the manage/edit page for resume.',
        'custom' => 0,
      );

      $content_data = $this->createStandardContentPage($page_data, array('main_right_content'=>false));
      @extract($content_data);
      
      $db     = $this->getDb();
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.edit-info',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.edit-menu',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));  
    }
  }
  // addEditPage
  
  public function addPackageBrowsePage()
  {
    $page_name = 'resume_package_browse';
    if (!$this->getPageByName($page_name))
    {
      $page_data = array(
        'name' => $page_name,
        'displayname' => 'Resume - Packages - Browse Page',
        'title' => 'Resume Posting Packages',
        'description' => 'This is the browse packages page for resumes.',
        'custom' => 0,
      );
      $this->createStandardContentPage($page_data);
    }
  }
  // addPackageBrowsePage
  
  
  public function addPackageProfilePage()
  {
    $page_name = 'resume_package_profile';
    if (!$this->getPageByName($page_name))
    {
      $page_data = array(
        'name' => $page_name,
        'displayname' => 'Resume - Packages - View Page',
        'title' => 'Resume Posting Package',
        'description' => 'This is the package profile view page for resumes.',
        'custom' => 0,
      );
      $this->createStandardContentPage($page_data);
    }
  }
  // addPackageProfilePage  
  
  public function addPhotoListPage()
  {
    $page_name = 'resume_photo_list';
    if (!$this->getPageByName($page_name))
    {
      $page_data = array(
        'name' => $page_name,
        'displayname' => 'Resume - Photos - Browse Page',
        'title' => 'Browse Resume Photos',
        'description' => 'This is the browse resume photos page.',
        'custom' => 0,
      );
      $this->createSimpleContentPage($page_data);
    }
  }
  // addPhotoListPage
  
  public function addPhotoViewPage()
  {
    $page_name = 'resume_photo_view';
    if (!$this->getPageByName($page_name))
    {
      $page_data = array(
        'name' => $page_name,
        'displayname' => 'Resume - Photos - View Page',
        'title' => 'View Resume Photo',
        'description' => 'This is the view resume photo page.',
        'custom' => 0,
      );
      $content_data = $this->createSimpleContentPage($page_data);
      
      @extract($content_data);
      
      $db     = $this->getDb();
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.comments',
        'page_id' => $page_id,
        'parent_content_id' => $main_middle_id,
        'order' => $order++,
      ));
      
    }
  }
  // addPhotoViewPage
    
  
  public function createStandardContentPage($page_data, $options = array())
  {
    $db     = $this->getDb();
    
    $db->insert('engine4_core_pages', $page_data);
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
      'name' => 'resume.main-menu',
      'parent_content_id' => $top_middle_id,
      'order' => $i++,
      'params' => '{"title":""}',
    ));
    $top_middle_menu_id = $db->lastInsertId('engine4_core_content');
    
    // ---------- CONTAINER MAIN -----------      
    if (!isset($options['right_column']) || $options['right_column'] !== false)
    {
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'right',
        'parent_content_id' => $main_id,
        'order' => $i++,
        'params' => '',
      ));
      $main_right_id = $db->lastInsertId('engine4_core_content');
    }
    
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
    if ($main_right_id && (!isset($options['main_right_content']) || $options['main_right_content'] !== false))
    {
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.search-form',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.packages-menu',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));      
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'resume.create-new',
        'parent_content_id' => $main_right_id,
        'order' => $i++,
        'params' => '{"title":""}',
      ));
    }
    
    // ------ MAIN :: MIDDLE WIDGETS   
    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'core.content',
      'parent_content_id' => $main_middle_id,
      'order' => $i++,
      'params' => '',
    ));
    
    $content_data = array(
      'page_id' => $page_id,
      'top_id' => $top_id,
      'top_middle_id' => $top_middle_id,  
      'main_id' => $main_id,
      'main_right_id' => $main_right_id,  
      'main_middle_id' => $main_middle_id,  
      'order' => $i,
      'i' => $i,
    );
    
    return $content_data;    
  }
  // createStandardContentPage
  
  
  public function createSimpleContentPage($page_data, $options = array())
  {
    $db     = $this->getDb();
    
    $db->insert('engine4_core_pages', $page_data);
    $page_id = $db->lastInsertId('engine4_core_pages');

    $i = 1;
    
    // CONTAINERS (MAIN)
    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'main',
      'parent_content_id' => null,
      'order' => $i++,
      'params' => '',
    ));
    $main_id = $db->lastInsertId('engine4_core_content');

    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'container',
      'name' => 'middle',
      'parent_content_id' => $main_id,
      'order' => $i++,
      'params' => '',
    ));
    $main_middle_id = $db->lastInsertId('engine4_core_content');

    
    // ------ MAIN :: MIDDLE WIDGETS   
    $db->insert('engine4_core_content', array(
      'page_id' => $page_id,
      'type' => 'widget',
      'name' => 'core.content',
      'parent_content_id' => $main_middle_id,
      'order' => $i++,
      'params' => '',
    ));
    $db->lastInsertId('engine4_core_content');
    
    $content_data = array(
      'page_id' => $page_id,
      'main_id' => $main_id,
      'main_middle_id' => $main_middle_id,  
      'order' => $i,
      'i' => $i,  
    );
    
    return $content_data;
  }
  // createSimpleContentPage
  
  private function getPageByName($page_name)
  {
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);
    
      // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', $page_name)
      ->limit(1);
      ;
    $info = $select->query()->fetch();
    
    return $info;
  }
  // getPageByName
  
  private function addVideoWidget() {
    $db = $this->getDb();
    
    $resumeProfilePageId = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('`name` = ?', 'resume_profile_index')
            ->limit(1)
            ->query()
            ->fetchColumn()
            ;
    
    if($resumeProfilePageId) {
      $hasVideoWidget = $db->select()
              ->from('engine4_core_content')
              ->where('`name` = ?', 'resume.profile-videos')
              ->where('page_id = ?', $resumeProfilePageId)
              ->limit(1)
              ->query()
              ->fetchColumn()
              ;
      
      if(empty($hasVideoWidget)) {
        
        // container_id (will always be there)
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_content')
                ->where('page_id = ?', $resumeProfilePageId)
                ->where('type = ?', 'container')
                ->limit(1);
        $container_id = $select->query()->fetchObject()->content_id;

        // middle_id (will always be there)
        $select->reset();
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
                ->where('page_id = ?', $resumeProfilePageId)
                ->limit(1);
        $tab_id = $select->query()->fetchObject();
        if ($tab_id && @$tab_id->content_id) {
          $tab_id = $tab_id->content_id;
        } else {
          $tab_id = null;
        }
        
        $db->insert('engine4_core_content', array(
            'page_id' => $resumeProfilePageId,
            'type' => 'widget',
            'name' => 'resume.profile-videos',
            'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
            'order' => 8,
            'params' => '{"title":"Videos"}'
        ));
      }
    }
  }
  
  private function addMessageWidget() {
    $db = $this->getDb();
    
    $resumeProfilePageId = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('`name` = ?', 'resume_profile_index')
            ->limit(1)
            ->query()
            ->fetchColumn()
            ;
    
    if($resumeProfilePageId) {
      $hasMessageWidget = $db->select()
              ->from('engine4_core_content')
              ->where('`name` = ?', 'resume.profile-messages')
              ->where('page_id = ?', $resumeProfilePageId)
              ->limit(1)
              ->query()
              ->fetchColumn()
              ;
      
      if(empty($hasMessageWidget)) {
        
        // container_id (will always be there)
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_content')
                ->where('page_id = ?', $resumeProfilePageId)
                ->where('type = ?', 'container')
                ->limit(1);
        $container_id = $select->query()->fetchObject()->content_id;

        // middle_id (will always be there)
        $select->reset();
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
                ->where('page_id = ?', $resumeProfilePageId)
                ->limit(1);
        $tab_id = $select->query()->fetchObject();
        if ($tab_id && @$tab_id->content_id) {
          $tab_id = $tab_id->content_id;
        } else {
          $tab_id = null;
        }
        
        $db->insert('engine4_core_content', array(
            'page_id' => $resumeProfilePageId,
            'type' => 'widget',
            'name' => 'resume.profile-messages',
            'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
            'order' => 10,
            'params' => '{"title":"Message"}'
        ));
      }
    }
  }
  
  /**
   * Try to generalize the add widget functions
   * 
   * @param string $widget_name
   * @param string $title
   * @param boolean $is_tab
   */
  private function addProfilePageWidget($widget_name, $title = '', $is_tab = false) {
    $db = $this->getDb();
    
    $resumeProfilePageId = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('`name` = ?', 'resume_profile_index')
            ->limit(1)
            ->query()
            ->fetchColumn()
            ;
    
    if($resumeProfilePageId) {
      $hasWidget = $db->select()
              ->from('engine4_core_content')
              ->where('`name` = ?', $widget_name)
              ->where('page_id = ?', $resumeProfilePageId)
              ->limit(1)
              ->query()
              ->fetchColumn()
              ;
      
      if(empty($hasWidget)) {
        
        // container_id (will always be there)
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_content')
                ->where('page_id = ?', $resumeProfilePageId)
                ->where('type = ?', 'container')
                ->limit(1);
        $container_id = $select->query()->fetchObject()->content_id;

        // middle_id (will always be there)
        $select->reset();
        $select
                ->from('engine4_core_content')
                ->where('parent_content_id = ?', $container_id)
                ->where('type = ?', 'container')
                ->where('name = ?', 'middle')
                ->limit(1);
        $middle_id = $select->query()->fetchObject()->content_id;
        
        if($is_tab) {
          // tab_id (tab container) may not always be there
          $select
                  ->reset('where')
                  ->where('type = ?', 'widget')
                  ->where('name = ?', 'core.container-tabs')
                  ->where('page_id = ?', $resumeProfilePageId)
                  ->limit(1);
          $tab_id = $select->query()->fetchObject();
        }
        if ($tab_id && @$tab_id->content_id) {
          $tab_id = $tab_id->content_id;
        } else {
          $tab_id = null;
        }
        
        $db->insert('engine4_core_content', array(
            'page_id' => $resumeProfilePageId,
            'type' => 'widget',
            'name' => $widget_name,
            'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
            'order' => 10,
            'params' => '{"title":"' . $title . '"}'
        ));
      }
    }
  }
  
  private function updateProfileToolbar() {
    $db = $this->getDb();
    
    $resumeProfilePageId = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('`name` = ?', 'resume_profile_index')
            ->limit(1)
            ->query()
            ->fetchColumn()
            ;
    
    if($resumeProfilePageId) {
      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_content')
              ->where('page_id = ?', $resumeProfilePageId)
              ->where('type = ?', 'container')
              ->limit(1);
      $container_id = $select->query()->fetchObject()->content_id;

      // middle_id (will always be there)
      $select->reset();
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
              ->where('page_id = ?', $resumeProfilePageId)
              ->limit(1);
      $tab_id = $select->query()->fetchObject();
      if ($tab_id && @$tab_id->content_id) {
        $tab_id = $tab_id->content_id;
      } else {
        $tab_id = null;
      }
      
      $db->update(
        // table
        'engine4_core_content',
        // values
        array(
          'order' => 6,
          'params' => '{"title":"Message Me"}'
        ),
        // where
        array(
          'name = ?' => 'resume.profile-messages',
          'type = ?' => 'widget',
          'page_id = ?' => $resumeProfilePageId,
          'parent_content_id = ?' => ($tab_id ? $tab_id : $middle_id)
        )
      );

      $db->update(
        // table
        'engine4_core_content',
        // values
        array(
          'order' => 7,
          'params' => '{"title":"Summary"}'
        ),
        // where
        array(
          'name = ?' => 'resume.profile-details',
          'type = ?' => 'widget',
          'page_id = ?' => $resumeProfilePageId,
          'parent_content_id = ?' => ($tab_id ? $tab_id : $middle_id)
        )
      );

      $db->update(
        // table
        'engine4_core_content',
        // values
        array(
          'order' => 8,
          'params' => '{"title":"CV"}'
        ),
        // where
        array(
          'name = ?' => 'resume.profile-body',
          'type = ?' => 'widget',
          'page_id = ?' => $resumeProfilePageId,
          'parent_content_id = ?' => ($tab_id ? $tab_id : $middle_id)
        )
      );

      $db->update(
        // table
        'engine4_core_content',
        // values
        array(
          'order' => 9,
        ),
        // where
        array(
          'name = ?' => 'resume.profile-videos',
          'type = ?' => 'widget',
          'page_id = ?' => $resumeProfilePageId,
          'parent_content_id = ?' => ($tab_id ? $tab_id : $middle_id)
        )
      );

      $db->update(
        // table
        'engine4_core_content',
        // values
        array(
          'order' => 10,
        ),
        // where
        array(
          'name = ?' => 'resume.profile-photos',
          'type = ?' => 'widget',
          'page_id = ?' => $resumeProfilePageId,
          'parent_content_id = ?' => ($tab_id ? $tab_id : $middle_id)
        )
      );

      $db->update(
        // table
        'engine4_core_content',
        // values
        array(
          'order' => 11,
        ),
        // where
        array(
          'name = ?' => 'resume.profile-location',
          'type = ?' => 'widget',
          'page_id = ?' => $resumeProfilePageId,
          'parent_content_id = ?' => ($tab_id ? $tab_id : $middle_id)
        )
      );
    }
  }
  
  private function updateGeneralParts() {
    $db = $this->getDb();
    
    $db->update(
      // table
      'engine4_core_menuitems',
      // values
      array(
        'params' => '{"route":"resume_general","action":"browse"}',
      ),
      // where
      array(
        'name = ?' => 'core_main_resume',
      )
    );
  }
  
  private function addAdvertSpaceToBrowsePage() {
    $db = $this->getDb();

    // Get Resume Browse Page Id
    $resumeBrowsePageId = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('`name` = ?', 'resume_index_browse')
            ->limit(1)
            ->query()
            ->fetchColumn()
    ;

    // Get Resume Home Page Id
    $resumeHomePageId = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('`name` = ?', 'resume_index_index')
            ->limit(1)
            ->query()
            ->fetchColumn()
    ;

    // Check if we have already added the advert space widget
    $hasAdvertSpace = $db->select()
            ->from('engine4_core_content')
            ->where('`name` = ?', 'core.ad-campaign')
            ->where('`page_id` = ?', $resumeBrowsePageId)
            ->limit(1)
            ->query()
            ->fetchColumn()
    ;

    if (!$hasAdvertSpace) {
      // Get Advert space widget from Home Page
      $advertSpace = $db->select()
              ->from('engine4_core_content')
              ->where('`name` = ?', 'core.ad-campaign')
              ->where('`page_id` = ?', $resumeHomePageId)
              ->limit(1)
              ->query()
              ->fetch(Zend_Db::FETCH_ASSOC)
      ;

      // Get main container id on Browse Page
      $browsePageMainContainer = $db->select()
              ->from('engine4_core_content')
              ->where('`name` = ?', 'resume.main-menu')
              ->where('`page_id` = ?', $resumeBrowsePageId)
              ->limit(1)
              ->query()
              ->fetchObject()
      ;

      if ($browsePageMainContainer) {
        if(is_array($advertSpace)) {
          // Change columns of new row
          unset($advertSpace['content_id']);
          $advertSpace['page_id'] = $resumeBrowsePageId;
          $advertSpace['parent_content_id'] = $browsePageMainContainer->parent_content_id;
          $advertSpace['order'] = $browsePageMainContainer->order - 1;
          // Copy advert widget row to Browse Page
          $db->insert('engine4_core_content', $advertSpace);
        } else {
          throw new Engine_Package_Installer_Exception('Advert Space is invalid for insertion.');
        }
      }
    }
  }
  
  private function updateSearchFields() {
    $db = $this->getDb();

    $db->update('engine4_resume_fields_meta',
            // SET
            array(
              'label' => 'Current Competition Level'
            ),
            // WHERE
            array(
              'label = ?' => 'Current Participation Level'
            )
    );
  }
  
  private function runCustomQueries() {
    $db = $this->getDb();

    $path = $this->_operation->getPrimaryPackage()->getBasePath() . '/'
            . $this->_operation->getPrimaryPackage()->getPath() . '/'
            . 'settings/custom-queries';

    $files = array(
        'custom.sql' => function() {
          return true;
        }
    );
    
    $db->beginTransaction();

    foreach ($files as $file => $callback) {
      if (call_user_func($callback) && file_exists($path . '/' . $file)) {
        $contents = file_get_contents($path . '/' . $file);
        foreach (Engine_Package_Utilities::sqlSplit($contents) as $sqlFragment) {
          try {
            $db->getConnection()->query($sqlFragment);
          } catch (Exception $e) {
            return $this->_error('Query failed with error: ' . $e->getMessage());
          }
        }
      }
    }
    $db->commit();
  }

  public function onInstall()
  {
    parent::onInstall();
    
    $this->addBrowsePage();
    $this->addCreatePage();
    $this->addEditPage();
    $this->addHomePage();
    $this->addManagePage();
    
    $this->addPackageBrowsePage();
    $this->addPackageProfilePage();
    
    $this->addPhotoListPage();
    $this->addPhotoViewPage();    
    
    $this->addProfilePage();
    $this->addTagsPage();
    $this->addUserProfileTab();
    
    // New upgrade
    $this->addVideoWidget();
    $this->addMessageWidget();
    $this->updateProfileToolbar();
    $this->updateGeneralParts();
    $this->addAdvertSpaceToBrowsePage();
    $this->updateSearchFields();
    $this->runCustomQueries();
    // Add profile bottom widget
    $this->addProfilePageWidget('resume.profile-bottom');
  }
}
