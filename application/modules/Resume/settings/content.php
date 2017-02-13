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



return array(

  
  // ------- categories
  
  array(
    'title' => 'Resume Categories',
    'description' => 'Displays a list of resume categories (support narrow / wide mode).',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.categories',
    'defaultParams' => array(
      'title' => 'Categories',
      'display_style' => 'narrow',
      'showphoto' => 1,
      'descriptionlength' => 255,
    ),   
    'adminForm' => array(
      'elements' => array(
        Resume_Form_Helper::getContentField('title', array('value' => 'Categories')),
        Resume_Form_Helper::getContentField('display_style', array('value' => 'narrow')),
        Resume_Form_Helper::getContentField('showphoto'),
        Resume_Form_Helper::getContentField('descriptionlength', array('value' => 255)),
      ),
    ),    
  ), 

  
  // ------- create new resume
  array(
    'title' => 'Post New Resume',
    'description' => 'Displays a quick navigation link to post new resume',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.create-new',
  ),   
  
  // ------- edit info
  array(
    'title' => 'Resume Edit - Info',
    'description' => 'Displays resume info block on editing pages',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.edit-info',
  ), 

  // ------- edit menu
  array(
    'title' => 'Resume Edit - Menu',
    'description' => 'Displays resume edit menu navigation links on editing pages',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.edit-menu',
  ), 
  
  
  // ------- featured resumes
  
  array(
    'title' => 'Featured Resumes',
    'description' => 'Displays slideshow of featured resumes with different filtering options (wide mode)',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.featured-resumes',
    'defaultParams' => array(
      'title' => 'Featured Resumes',
      'max' => 5,
    ),  
    'adminForm' => array(
      'attribs' => array(
        'class' => 'resume_widget_form'
      ),    
      'elements' => array(
      
        Resume_Form_Helper::getContentField('title', array('value' => 'Featured Resumes')),
        Resume_Form_Helper::getContentField('max', array('value' => 5)),
        Resume_Form_Helper::getContentField('order', array('value' => 'random')),
        Resume_Form_Helper::getContentField('period'),
        Resume_Form_Helper::getContentField('user'),
        Resume_Form_Helper::getContentField('keyword'),
        Resume_Form_Helper::getContentField('location'),
        Resume_Form_Helper::getContentField('distance'),
        Resume_Form_Helper::getContentField('category'),
        Resume_Form_Helper::getContentField('showphoto'),
        Resume_Form_Helper::getContentField('showdetails'),
        Resume_Form_Helper::getContentField('showdescription'),
        Resume_Form_Helper::getContentField('showmeta'),                     
      ),
    ),    
  ),   
  

  
  // ------- list resumes
  
  array(
    'title' => 'List Resumes',
    'description' => 'Displays a list of posted resumes with different filtering options (can be used to build variety of resume listings such as Recent Resumes, Most Commented by XYZ user with specified category etc..)',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.list-resumes',
    'defaultParams' => array(
      'title' => 'Listing Resumes',
      'max' => 5,
      'display_style' => 'wide',
      'order' => 'recent'
    ),  
    'adminForm' => array(
      'attribs' => array(
        'class' => 'resume_widget_form'
      ),
      'elements' => array(
        Resume_Form_Helper::getContentField('title', array('value' => 'Listing Resumes')),
        Resume_Form_Helper::getContentField('max'),
        Resume_Form_Helper::getContentField('order'),
        Resume_Form_Helper::getContentField('period'),
        Resume_Form_Helper::getContentField('user'),
        Resume_Form_Helper::getContentField('keyword'),
        Resume_Form_Helper::getContentField('location'),
        Resume_Form_Helper::getContentField('distance'),
        Resume_Form_Helper::getContentField('category'),
        Resume_Form_Helper::getContentField('featured'),
        Resume_Form_Helper::getContentField('sponsored'),
        Resume_Form_Helper::getContentField('display_style'),
        Resume_Form_Helper::getContentField('showphoto'),
        Resume_Form_Helper::getContentField('showdetails'),
        Resume_Form_Helper::getContentField('showdescription'),
        Resume_Form_Helper::getContentField('showmeta'),
      ),
    ),    
  ),  
    
  
  
  // ------- top menu nav
  array(
    'title' => 'Menu Resumes',
    'description' => 'Displays a resume main menu navigation (Browse Resumes, My Resumes, Resume Posting Package, Post New Resume).',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.main-menu',
  ),  
  
  
  
  // ------- map resumes
  
  array(
    'title' => 'Map Resumes',
    'description' => 'Displays a map of posted resumes with different filtering options (can be used to build variety of resume listings such as Recent Resumes, Most Commented by XYZ user with specified category etc..)',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.map-resumes',
    'defaultParams' => array(
      'title' => 'Map Resumes',
      'max' => 10,
      'order' => 'recent'
    ),  
    'adminForm' => array(
      'attribs' => array(
        'class' => 'resume_widget_form'
      ),
      'elements' => array(
        Resume_Form_Helper::getContentField('title', array('value' => 'Map Resumes')),
        Resume_Form_Helper::getContentField('max'),
        Resume_Form_Helper::getContentField('order'),
        Resume_Form_Helper::getContentField('period'),
        Resume_Form_Helper::getContentField('user'),
        Resume_Form_Helper::getContentField('keyword'),
        Resume_Form_Helper::getContentField('location'),
        Resume_Form_Helper::getContentField('distance'),
        Resume_Form_Helper::getContentField('category'),
        Resume_Form_Helper::getContentField('featured'),
        Resume_Form_Helper::getContentField('sponsored'),
      ),
    ),    
  ),  
    
  
  // ------- packages
  
  array(
    'title' => 'Resume Packages',
    'description' => 'Displays available resume packages with full details such as title, terms, descriptions etc..',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.packages',
    'defaultParams' => array(
      'title' => 'Resume Packages',
      'showdetails' => 1,
      'showdescription' => 1,
      'create_link' => 1,
    ),   
    'adminForm' => array(
      'elements' => array(
        Resume_Form_Helper::getContentField('title', array('value' => 'Packages')),
        Resume_Form_Helper::getContentField('showdetails'),
        Resume_Form_Helper::getContentField('showdescription'),
        array(
            'Select', 
            'create_link',
            array(
              'label' => 'Post New Resume Link',
              'multiOptions' => array(
                1 => 'Only show if member has permission',
                2 => 'Always show',
                0 => 'Always hide'
              ),
              'value' => 1,
            )
          ),
      ),
    ),    
  ), 
    

  
  // ------- create packages resume
  array(
    'title' => 'Resume Posting Packages',
    'description' => 'Displays a quick navigation to "Resume Posting Packages" landing page',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.packages-menu',
  ),   
  
  

  // ------- popular tags
  
  array(
    'title' => 'Resume Popular Tags',
    'description' => 'Displays resume popular tags.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.popular-tags',
    'defaultParams' => array(
      'title' => 'Popular Tags',
      'max' => 100,
      'order' => 'text',
    ),
    'adminForm' => array(
      'elements' => array(
        Resume_Form_Helper::getContentField('title', array('value' => 'Popular Tags')),
        Resume_Form_Helper::getContentField('max', array('label' => 'Max Tags', 'value' => 100)),
        Resume_Form_Helper::getContentField('order', array('value' => 'text', 'multiOptions' => array('text' => 'Tag Name','total' => 'Total Count'))),              
      ),
    ),     
  ),
  
  

  


  
  // ========================= RESUME PROFILE WIDGETS (resume view page) ===========================
  

  
  // ------- resume profile breadcrumb
  array(
    'title' => 'Resume Profile - Breadcrumb',
    'description' => 'Displays a resume\'s breadcrumb on its profile.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-breadcrumb',      
  ),  
  
  
  // ------- resume profile comments
  array(
    'title' => 'Resume Profile - Comments',
    'description' => 'Displays a resume\'s comments on its profile.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-comments',    
    'defaultParams' => array(
      'title' => 'Comments',
    ),  
    'adminForm' => array(
      'elements' => array(
        Resume_Form_Helper::getContentField('title', array('value' => 'Comments')),
      ),
    ),    
  ),  
  
  // ------- resume profile description
  array(
    'title' => 'Resume Profile - Body',
    'description' => 'Displays a resume\'s full body content.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-body',    
    'defaultParams' => array(
      'title' => 'Resume',
    ),  
    'adminForm' => array(
      'elements' => array(
        Resume_Form_Helper::getContentField('title', array('value' => 'Resume')),
      ),
    ),    
  ), 
  
  // ------- resume profile details
  array(
    'title' => 'Resume Profile - Details',
    'description' => 'Displays a resume\'s details (customized question/field data) on its profile.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-details',    
    'defaultParams' => array(
      'title' => 'Resume Details',
    ),  
    'adminForm' => array(
      'elements' => array(
        Resume_Form_Helper::getContentField('title', array('value' => 'Resume Details')),
      ),
    ),    
  ),  
  
  // ------- resume profile icon featured
  array(
    'title' => 'Resume Profile - Icon Featured',
    'description' => 'Displays a icon for featured resume on its profile.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-icon-featured',    
    'defaultParams' => array(
      'title' => '',
      'text' => 'FEATURED RESUME',
    ),  
    'adminForm' => array(
      'elements' => array(
        Resume_Form_Helper::getContentField('title', array('value' => '')),
        Resume_Form_Helper::getContentField('text', array('label'=>'Icon Text', 'value' => 'FEATURED RESUME')),
        Resume_Form_Helper::getContentField('image', array('label' => 'Icon Image URL', 'value' => '')),
      ),
    ),    
  ),
  
  // ------- resume profile icon package
  array(
    'title' => 'Resume Profile - Icon Package',
    'description' => 'Displays resume package\'s icon on its profile.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-icon-package',    
    'defaultParams' => array(
      'title' => '',
    ),  
    'adminForm' => array(
      'elements' => array(
        Resume_Form_Helper::getContentField('title', array('value' => '')),
      ),
    ),    
  ), 
  
  // ------- resume profile icon sponsored
  array(
    'title' => 'Resume Profile - Icon Sponsored',
    'description' => 'Displays a icon for sponsored resume on its profile.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-icon-sponsored',    
    'defaultParams' => array(
      'title' => '',
      'text' => 'SPONSORED RESUME',
    ),  
    'adminForm' => array(
      'elements' => array(
        Resume_Form_Helper::getContentField('title', array('value' => '')),
        Resume_Form_Helper::getContentField('text', array('label'=>'Icon Text', 'value' => 'SPONSORED RESUME')),
        Resume_Form_Helper::getContentField('image', array('label' => 'Icon Image URL', 'value' => '')),
      ),
    ),    
  ),  

  /*
  // ------- resume profile info
  array(
    'title' => 'Resume Profile - Info',
    'description' => 'Displays a resume\'s info (address, price, category etc..) on its profile.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-info',    
    'defaultParams' => array(
      'title' => 'Resume Info',
    ),  
    'adminForm' => array(
      'elements' => array(
        Resume_Form_Helper::getContentField('title', array('value' => 'Resume Info')),
      ),
    ),    
  ),
  */
  
  // ------- resume profile intro
  array(
    'title' => 'Resume Profile - Intro',
    'description' => 'Displays a resume\'s short description',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-intro',    
    'defaultParams' => array(
      'title' => '',
    ),  
    'adminForm' => array(
      'elements' => array(
        Resume_Form_Helper::getContentField('title', array('value' => '')),
      ),
    ),    
  ),  
 
  // ------- resume profile location
  array(
    'title' => 'Resume Profile - Location',
    'description' => 'Displays a resume\'s location and map.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-location',    
    'defaultParams' => array(
      'title' => 'Map',
    ),  
    'adminForm' => array(
      'elements' => array(
        Resume_Form_Helper::getContentField('title', array('value' => 'Location')),
      ),
    ),    
  ),   
  
  // ------- resume profile notice
  array(
    'title' => 'Resume Profile - Notice',
    'description' => 'Displays a resume\'s system notice such as approval status, expiration, preview message etc..',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-notice', 
  ),
  
  // ------- resume profile options
  array(
    'title' => 'Resume Profile - Options',
    'description' => 'Displays a resume\'s options (View Submitter Resumes | Create | Edit | Delete) on its profile.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-options',    
    'defaultParams' => array(
      'title' => '',
    ),  
    'adminForm' => array(
      'elements' => array(
        Resume_Form_Helper::getContentField('title', array('value' => '')),
      ),
    ),    
  ),
  
  // ------- resume profile photo
  array(
    'title' => 'Resume Profile - Photo',
    'description' => 'Displays a resume\'s main photo.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-photo',    
    'defaultParams' => array(
      'title' => '',
    ),  
    'adminForm' => array(
      'elements' => array(
        Resume_Form_Helper::getContentField('title', array('value' => '')),
      ),
    ),    
  ),  
  
  // ------- resume profile photos
  array(
    'title' => 'Resume Profile - Photos',
    'description' => 'Displays a resume\'s photos.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-photos',    
    'defaultParams' => array(
      'title' => 'Photos',
    ),  
    'adminForm' => array(
      'elements' => array(
        Resume_Form_Helper::getContentField('title', array('value' => 'Photos')),
      ),
    ),    
  ),
  
  // ------- resume profile videos
  array(
    'title' => 'Resume Profile - Videos',
    'description' => 'Displays a resume\'s videos.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-videos',    
    'defaultParams' => array(
      'title' => 'Videos',
    ),  
    'adminForm' => array(
      'elements' => array(
        Resume_Form_Helper::getContentField('title', array('value' => 'Videos')),
      ),
    ),    
  ),
    
  // ------- resume profile messages
  array(
    'title' => 'Resume Profile - Messages',
    'description' => 'Displays sending messages form.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-messages',    
    'defaultParams' => array(
      'title' => 'Message',
    ),  
  ),
    
  // ------- resume profile bottom
  array(
    'title' => 'Resume Profile - Bottom',
    'description' => 'Displays bottom content of profile page.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-bottom',    
    'defaultParams' => array(
      'title' => '',
    ),  
  ),
  
  // ------- on user profile tab

  array(
    'title' => 'Member Profile Resumes',
    'description' => 'Displays a member\'s resumes on their profile. It also supports displaying resumes that are created by specific page/subject owner, example: when use this widget on Group Profile page, and config User=OWNER mode, it would shows resumes created by the group owner. If you set User=VIEWER mode, then the widget will displays resumes created by current logged in member.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-resumes',
    'defaultParams' => array(
      'title' => 'Resumes',
      'titleCount' => true,
      'max' => 5,
      'user_type' => 'owner',
      'order' => 'recent',
      'display_style' => 'wide',
    ),
    'adminForm' => array(
      'description' => 'Displays a member\'s resumes on their profile. It also supports displaying resumes that are created by specific page/subject owner, example: when use this widget on Group Profile page, and config User=OWNER mode, it would shows resumes created by the group owner. If you set User=VIEWER mode, then the widget will displays resumes created by current logged in member.',
      'attribs' => array(
        'class' => 'resume_widget_form'
      ),
      'elements' => array(
      
        Resume_Form_Helper::getContentField('title', array('value' => 'Resumes')),
        Resume_Form_Helper::getContentField('max', array('value' => 5)),
        Resume_Form_Helper::getContentField('user_type', array('value' => 'owner')),
        Resume_Form_Helper::getContentField('order'),
        Resume_Form_Helper::getContentField('period'),
        
        Resume_Form_Helper::getContentField('keyword'),
        
        Resume_Form_Helper::getContentField('location'),
        Resume_Form_Helper::getContentField('distance'),
        Resume_Form_Helper::getContentField('category'),
        
        
        Resume_Form_Helper::getContentField('featured'),
        Resume_Form_Helper::getContentField('sponsored'),
        Resume_Form_Helper::getContentField('display_style'),
        Resume_Form_Helper::getContentField('showphoto'),
        Resume_Form_Helper::getContentField('showdetails'),
        Resume_Form_Helper::getContentField('showdescription'),
        Resume_Form_Helper::getContentField('showmeta'),
        Resume_Form_Helper::getContentField('showmemberitemlist'),        
               
      ),
    ),     
  ),  
  
  // ------- resume profile related resumes
  array(
    'title' => 'Resume Profile - Related Resumes',
    'description' => 'Displays a resume\'s related resumes (by tags) on its profile.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-related-resumes',    
    'defaultParams' => array(
  	  'titleCount' => true,
      'title' => 'Related Resumes',
      'max' => 5,
      'order' => 'random',
    ),  
    'adminForm' => array(
      'attribs' => array(
        'class' => 'resume_widget_form'
      ),
      'elements' => array(
      
        Resume_Form_Helper::getContentField('title', array('value' => 'Related Resumes')),
        Resume_Form_Helper::getContentField('max'),
        Resume_Form_Helper::getContentField('order', array('value' => 'recent')),
        Resume_Form_Helper::getContentField('period'),
        Resume_Form_Helper::getContentField('user'),
        Resume_Form_Helper::getContentField('keyword'),
        
        Resume_Form_Helper::getContentField('location'),
        Resume_Form_Helper::getContentField('distance'),
        Resume_Form_Helper::getContentField('category'),
        
        
        Resume_Form_Helper::getContentField('featured'),
        Resume_Form_Helper::getContentField('sponsored'),
        Resume_Form_Helper::getContentField('display_style'),
        Resume_Form_Helper::getContentField('showphoto'),
        Resume_Form_Helper::getContentField('showdetails'),
        Resume_Form_Helper::getContentField('showdescription'),
        Resume_Form_Helper::getContentField('showmeta'),      

      ),
    ),    
  ),  

  // ------- resume profile social shares
  array(
    'title' => 'Resume Profile - Social Shares',
    'description' => 'Displays a resume\'s social shares such as Facebook, Twitter, Digg using AddThis service on its profile.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-social-shares',       
  ),
  
  // ------- resume profile stats
  array(
    'title' => 'Resume Profile - Stats',
    'description' => 'Displays a resume\'s stats (owner, date posted, date updated, various of view, comment, like counts) on its profile.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-stats',    
    'defaultParams' => array(
      'title' => 'Resume Statistics',
    ),  
    'adminForm' => array(
      'elements' => array(
        Resume_Form_Helper::getContentField('title', array('value' => 'Resume Statistics')),
      ),
    ),    
  ),

  
  // ------- resume profile submitter
  array(
    'title' => 'Resume Profile - Submitter',
    'description' => 'Displays a resume\'s submitter on its profile.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-submitter',    
    'defaultParams' => array(
      'title' => 'Submitter',
    ),  
    'adminForm' => array(
      'elements' => array(
        Resume_Form_Helper::getContentField('title', array('value' => 'Submitter')),
      ),
    ),    
  ),
  
  // ------- resume profile title
  array(
    'title' => 'Resume Profile - Title',
    'description' => 'Displays a resume\'s title on its profile.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-title',       
  ),
  
  // ------- resume profile tools
  array(
    'title' => 'Resume Profile - Tools',
    'description' => 'Displays a resume\'s tools (Share | Report) on its profile.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.profile-tools',       
  ),
  
  // ========================= END RESUME PROFILE WIDGETS (resume view page) ===========================
  

  // ------- search form
  
  array(
    'title' => 'Search Resumes',
    'description' => 'Displays search form on resume home page.',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.search-form',
  ), 
  
    
  // ------- sponsored resumes
  
  array(
    'title' => 'Sponsored Resumes',
    'description' => 'Displays ticker-news of sponsored resumes with different filtering options (side bar)',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.sponsored-resumes',
    'defaultParams' => array(
      'title' => 'Sponsored Resumes',
      'max' => 5,
    ),  
    'adminForm' => array(
      'attribs' => array(
        'class' => 'resume_widget_form'
      ),    
      'elements' => array(
      
        Resume_Form_Helper::getContentField('title', array('value' => 'Sponsored Resumes')),
        Resume_Form_Helper::getContentField('max', array('value' => 5)),
        Resume_Form_Helper::getContentField('order', array('value' => 'random')),
        Resume_Form_Helper::getContentField('period'),
        Resume_Form_Helper::getContentField('user'),
        Resume_Form_Helper::getContentField('keyword'),
        
        Resume_Form_Helper::getContentField('location'),
        Resume_Form_Helper::getContentField('distance'),
        Resume_Form_Helper::getContentField('category'),
        
        

        Resume_Form_Helper::getContentField('showphoto'),
        Resume_Form_Helper::getContentField('showdetails'),
        Resume_Form_Helper::getContentField('showdescription'),
        Resume_Form_Helper::getContentField('showmeta'),
                             
      ),
    ),    
  ),    


  // ------- top submitters
  
  array(
    'title' => 'Top Resume Submitters',
    'description' => 'Displays list of top resume\'s submitters',
    'category' => 'Resumes',
    'type' => 'widget',
    'name' => 'resume.top-submitters',
    'defaultParams' => array(
      'title' => 'Top Submitters',
      'max' => 5,
    ),  
    'adminForm' => array(
      'elements' => array(
        Resume_Form_Helper::getContentField('title', array('value' => 'Top Submitters')),
        Resume_Form_Helper::getContentField('max', array('label' => 'Max Items')),
        Resume_Form_Helper::getContentField('period'),
      ),
    ),    
  ),   
 
  
);

