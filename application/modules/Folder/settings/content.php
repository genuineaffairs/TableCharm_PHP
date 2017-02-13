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

  // ------- on user profile tab

  array(
    'title' => 'Profile (Integration) Folders',
    'description' => 'Displays an item\'s folders on its profile. An item can be a member, event, group, listing, or job etc.. You can use this widget on Member (or Event, Group, Listing, Job etc..) Profile pages.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.profile-folders',
    'defaultParams' => array(
      'title' => 'Folders',
      'titleCount' => true,
      'max' => 5,
      'order' => 'recent',
    ),
    'adminForm' => array(
      'description' => 'Displays an item\'s folders on its profile. An item can be a member, event, group, listing, or job etc.. You can use this widget on any Member (or Event, Group, Listing, Job etc..) Profile pages.',
      'attribs' => array(
        'class' => 'folder_widget_form'
      ),
      'elements' => array(
      
        Folder_Form_Helper::getContentField('title', array('value' => 'Folders')),
        Folder_Form_Helper::getContentField('max', array('value' => 5)),
        Folder_Form_Helper::getContentField('order'),
        Folder_Form_Helper::getContentField('period'),
        Folder_Form_Helper::getContentField('keyword'),
        Folder_Form_Helper::getContentField('category'),

        Folder_Form_Helper::getContentField('featured'),
        Folder_Form_Helper::getContentField('sponsored'),
        
        Folder_Form_Helper::getContentField('showphoto'),
        Folder_Form_Helper::getContentField('showdetails'),
        Folder_Form_Helper::getContentField('showdescription'),
        Folder_Form_Helper::getContentField('showmeta'),
               
      ),
    ),     
  ),
  
  // --------- browse folders
  array(
    'title' => 'Browse Folders - Listing Folders',
    'description' => 'Displays listing folders on Browse Folders page',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.browse-folders',
    'defaultParams' => array(
      'title' => 'Browse Folders',
      'titleCount' => true,
      'max' => 10,
    ),
    'adminForm' => array(
      'attribs' => array(
        'class' => 'folder_widget_form'
      ),
      'elements' => array(
      
        Folder_Form_Helper::getContentField('title', array('value' => 'Browse Folders')),
        Folder_Form_Helper::getContentField('max', array('value' => 10)),
               
      ),
    ),     
  ),  
  // --------- browse parent item
  array(
    'title' => 'Browse Folders - Parent Item',
    'description' => 'Displays Parent Item info on Browse Folders page. The Parent Item is the item that folders associated with, which can be a member, event, group, article, or job etc..',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.browse-parent-item',    
  ), 
  
  // ------- categories
  
  array(
    'title' => 'Folder Categories',
    'description' => 'Displays a list of folder categories (support narrow / wide mode).',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.categories',
    'defaultParams' => array(
      'title' => 'Categories',
      'display_style' => 'narrow',
      'showphoto' => 1,
    ),   
    'adminForm' => array(
      'elements' => array(
        Folder_Form_Helper::getContentField('title', array('value' => 'Categories')),
        Folder_Form_Helper::getContentField('display_style', array('value' => 'narrow')),
        Folder_Form_Helper::getContentField('showphoto'),
        Folder_Form_Helper::getContentField('showdescription', array('value' => 0)),
      ),
    ),    
  ), 
  
  // ------- create new folder
  array(
    'title' => 'Share New Files',
    'description' => 'Displays a quick navigation folder to create new folder (Share New Files) button link.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.create-new',
  ), 
  
  // ------- featured folders
  
  array(
    'title' => 'Featured Folders',
    'description' => 'Displays slideshow of featured folders with different filtering options (wide mode / main column)',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.featured-folders',
    'defaultParams' => array(
      'title' => 'Featured Folders',
    ),  
    'adminForm' => array(
      'attribs' => array(
        'class' => 'folder_widget_form'
      ),    
      'elements' => array(
      
        Folder_Form_Helper::getContentField('title', array('value' => 'Featured Folders')),
        Folder_Form_Helper::getContentField('max', array('value' => 5)),
        Folder_Form_Helper::getContentField('order', array('value' => 'random')),
        Folder_Form_Helper::getContentField('period'),
        
        Folder_Form_Helper::getContentField('parent_type'),
        Folder_Form_Helper::getContentField('parent_id'),
        Folder_Form_Helper::getContentField('user'),
        Folder_Form_Helper::getContentField('keyword'),
        Folder_Form_Helper::getContentField('category'),

        Folder_Form_Helper::getContentField('showphoto'),
        Folder_Form_Helper::getContentField('showdetails'),
        Folder_Form_Helper::getContentField('showdescription'),
        Folder_Form_Helper::getContentField('showmeta'),
                             
      ),
    ),    
  ),  
    
  // ------- sponsored folders
  
  array(
    'title' => 'Sponsored Folders',
    'description' => 'Displays slideshow of sponsored folders with different filtering options (side bar / narrow column)',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.sponsored-folders',
    'defaultParams' => array(
      'title' => 'Sponsored Folders',
    ),  
    'adminForm' => array(
      'attribs' => array(
        'class' => 'folder_widget_form'
      ),    
      'elements' => array(
      
        Folder_Form_Helper::getContentField('title', array('value' => 'Sponsored Folders')),
        Folder_Form_Helper::getContentField('max', array('value' => 5)),
        Folder_Form_Helper::getContentField('order', array('value' => 'random')),
        Folder_Form_Helper::getContentField('period'),
        
        Folder_Form_Helper::getContentField('parent_type'),
        Folder_Form_Helper::getContentField('parent_id'),
        
        Folder_Form_Helper::getContentField('user'),
        Folder_Form_Helper::getContentField('keyword'),

        Folder_Form_Helper::getContentField('category'),

        Folder_Form_Helper::getContentField('showphoto'),
        Folder_Form_Helper::getContentField('showdetails'),
        Folder_Form_Helper::getContentField('showdescription'),
        Folder_Form_Helper::getContentField('showmeta'),
                             
      ),
    ),    
  ),    
  
  // ------- list folders
  
  array(
    'title' => 'List Folders',
    'description' => 'Displays a list of posted folders with different filtering options (can be used to build variety of folder listings such as Recent Folders, Most Commented by XYZ user with specified category etc..)',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.list-folders',
    'defaultParams' => array(
      'title' => 'Listing Folders',
      'display_style' => 'wide',
    ),  
    'adminForm' => array(
      'attribs' => array(
        'class' => 'folder_widget_form'
      ),
      'elements' => array(
      
        Folder_Form_Helper::getContentField('title', array('value' => 'Listing Folders')),
        Folder_Form_Helper::getContentField('max'),
        Folder_Form_Helper::getContentField('order'),
        Folder_Form_Helper::getContentField('period'),
        
        Folder_Form_Helper::getContentField('parent_type'),
        Folder_Form_Helper::getContentField('parent_id'),
        Folder_Form_Helper::getContentField('user'),
        Folder_Form_Helper::getContentField('keyword'),
        Folder_Form_Helper::getContentField('category'),

        Folder_Form_Helper::getContentField('featured'),
        Folder_Form_Helper::getContentField('sponsored'),
        Folder_Form_Helper::getContentField('display_style'),
        
        Folder_Form_Helper::getContentField('showphoto'),
        Folder_Form_Helper::getContentField('showdetails'),
        Folder_Form_Helper::getContentField('showdescription'),
        Folder_Form_Helper::getContentField('showmeta'),
      
        Folder_Form_Helper::getContentField('showemptyresult'),
      ),
    ),    
  ),  
  
  // ------- top menu nav
  array(
    'title' => 'Menu Folders',
    'description' => 'Displays a menu navigation (Browse Folders, My Folders, Share New Files) on folder home page.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.main-menu',
  ), 
  // ------- popular tags
  
  array(
    'title' => 'Folder Popular Tags',
    'description' => 'Displays folder popular tags.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.popular-tags',
    'defaultParams' => array(
      'title' => 'Popular Tags',
      'max' => 100,
      'order' => 'text',
    ),
    'adminForm' => array(
      'elements' => array(
        Folder_Form_Helper::getContentField('title', array('value' => 'Popular Tags')),
        Folder_Form_Helper::getContentField('max', array('label' => 'Max Tags', 'value' => 100)),
        Folder_Form_Helper::getContentField('order', array('value' => 'text', 'multiOptions' => array('text' => 'Tag Name','total' => 'Total Count'))), 
        Folder_Form_Helper::getContentField('showlinkall'),             
      ),
    ),     
  ),
  // ------- search form
  array(
    'title' => 'Search Folders',
    'description' => 'Displays search form on folder home page.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.search-form',
  ),   
  // ------- top submitters
  array(
    'title' => 'Top Folder Creators',
    'description' => 'Displays list of top folder\'s creators',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.top-submitters',
    'defaultParams' => array(
      'title' => 'Top Uploaders',
      'max' => 5,
    ),  
    'adminForm' => array(
      'elements' => array(
        Folder_Form_Helper::getContentField('title', array('value' => 'Top Uploaders')),
        Folder_Form_Helper::getContentField('max', array('label' => 'Max Items')),
        Folder_Form_Helper::getContentField('period'),
      ),
    ),    
  ),   
  
  
  // ========================= FOLDER PROFILE WIDGETS (folder view page) ===========================
  // ------- folder profile breadcrumb
  array(
    'title' => 'Folder - Profile Breadcrumb',
    'description' => 'Displays a folder\'s breadcrumb on its profile.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.profile-breadcrumb',      
  ), 
  
  // ------- folder profile comments
  array(
    'title' => 'Folder - Profile Comments',
    'description' => 'Displays a folder\'s comments on its profile.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.profile-comments',    
    'defaultParams' => array(
      'title' => 'Comments',
    ),  
    'adminForm' => array(
      'elements' => array(
        Folder_Form_Helper::getContentField('title', array('value' => 'Comments')),
      ),
    ),    
  ),
  
  // ------- folder profile description
  array(
    'title' => 'Folder - Profile Description',
    'description' => 'Displays a folder\'s description on its profile.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.profile-description',    
    'defaultParams' => array(
      'title' => 'Folder Description',
    ),  
    'adminForm' => array(
      'elements' => array(
        Folder_Form_Helper::getContentField('title', array('value' => '')),
      ),
    ),    
  ), 
  
  // ------- folder profile details
  array(
    'title' => 'Folder - Profile Details',
    'description' => 'Displays a folder\'s details (customized question/field data) on its profile.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.profile-details',    
    'defaultParams' => array(
      'title' => 'Details',
    ),  
    'adminForm' => array(
      'elements' => array(
        Folder_Form_Helper::getContentField('title', array('value' => 'Details')),
      ),
    ),    
  ), 
  
  // ------- folder profile apply
  array(
    'title' => 'Folder - Profile Files',
    'description' => 'Displays a folder\'s uploaded files on its profile.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.profile-files',      
  ),  
  
  
  // ------- folder profile icon featured
  array(
    'title' => 'Folder - Profile Icon Featured',
    'description' => 'Displays a icon for featured folder on its profile.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.profile-icon-featured',    
    'defaultParams' => array(
      'title' => '',
      'text' => 'FEATURED FOLDER',
    ),  
    'adminForm' => array(
      'elements' => array(
        Folder_Form_Helper::getContentField('title', array('value' => '')),
        Folder_Form_Helper::getContentField('text', array('label'=>'Icon Text', 'value' => 'FEATURED FOLDER')),
        Folder_Form_Helper::getContentField('image', array('label' => 'Icon Image URL', 'value' => '')),
      ),
    ),    
  ),
  
  // ------- folder profile icon sponsored
  array(
    'title' => 'Folder - Profile Icon Sponsored',
    'description' => 'Displays a icon for sponsored folder on its profile.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.profile-icon-sponsored',    
    'defaultParams' => array(
      'title' => '',
      'text' => 'SPONSORED FOLDER',
    ),  
    'adminForm' => array(
      'elements' => array(
        Folder_Form_Helper::getContentField('title', array('value' => '')),
        Folder_Form_Helper::getContentField('text', array('label'=>'Icon Text', 'value' => 'SPONSORED FOLDER')),
        Folder_Form_Helper::getContentField('image', array('label' => 'Icon Image URL', 'value' => '')),
      ),
    ),    
  ),  
  
  // ------- folder profile info
  array(
    'title' => 'Folder - Profile Info',
    'description' => 'Displays a folder\'s info (type, parent, category, tags, description etc..) on its profile.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.profile-info',    
    'defaultParams' => array(
      'title' => 'Folder Info',
    ),  
    'adminForm' => array(
      'elements' => array(
        Folder_Form_Helper::getContentField('title', array('value' => 'Folder Info')),
      ),
    ),    
  ),  
  
  // ------- folder profile notice
  array(
    'title' => 'Folder - Profile Notice',
    'description' => 'Displays a folder\'s system notice such as approval status, expiration, preview message etc..',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.profile-notice', 
  ),
 
  
  // ------- folder profile options
  array(
    'title' => 'Folder - Profile Options',
    'description' => 'Displays a folder\'s options (sidebar navigation: Share New Files | Edit This Folder | Delete This Folder) on its profile.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.profile-options',    
    'defaultParams' => array(
      'title' => '',
    ),  
    'adminForm' => array(
      'elements' => array(
        Folder_Form_Helper::getContentField('title', array('value' => '')),
      ),
    ),    
  ),
  
  // ------- folder profile photo
  array(
    'title' => 'Folder - Profile Photo',
    'description' => 'Displays a folder\'s main photo.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.profile-photo',    
    'defaultParams' => array(
      'title' => '',
    ),  
    'adminForm' => array(
      'elements' => array(
        Folder_Form_Helper::getContentField('title', array('value' => '')),
      ),
    ),    
  ), 
  
  // ------- folder profile related folders
  array(
    'title' => 'Folder - Profile Related Folders',
    'description' => 'Displays a folder\'s related folders (which are belong to the same parent) on its profile.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.profile-related-folders',    
    'defaultParams' => array(
  	  'titleCount' => true,
      'title' => 'Related Folders',
      'max' => 5,
      'order' => 'random',
    ),  
    'adminForm' => array(
      'attribs' => array(
        'class' => 'folder_widget_form'
      ),
      'elements' => array(
      
        Folder_Form_Helper::getContentField('title', array('value' => 'Related Folders')),
        Folder_Form_Helper::getContentField('max'),
        Folder_Form_Helper::getContentField('order', array('value' => 'recent')),
        Folder_Form_Helper::getContentField('period'),
        Folder_Form_Helper::getContentField('keyword'),
        Folder_Form_Helper::getContentField('category'),
        Folder_Form_Helper::getContentField('featured'),
        Folder_Form_Helper::getContentField('sponsored'),        
        Folder_Form_Helper::getContentField('showphoto'),
        Folder_Form_Helper::getContentField('showdescription'),
        Folder_Form_Helper::getContentField('showmeta'),     

      ),
    ),    
  ),  

  // ------- folder profile social shares
  array(
    'title' => 'Folder - Profile Social Shares',
    'description' => 'Displays a folder\'s social shares such as Facebook, Twitter, Digg using AddThis service on its profile.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.profile-social-shares',       
  ),
  
  // ------- folder profile stats
  array(
    'title' => 'Folder - Profile Stats',
    'description' => 'Displays a folder\'s stats (owner, date posted, date updated; various of download, view, comment, like counts) on its profile.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.profile-stats',    
    'defaultParams' => array(
      'title' => 'Folder Statistics',
    ),  
    'adminForm' => array(
      'elements' => array(
        Folder_Form_Helper::getContentField('title', array('value' => 'Folder Statistics')),
      ),
    ),    
  ),

  // ------- folder profile submitter
  array(
    'title' => 'Folder - Profile Submitter',
    'description' => 'Displays a folder\'s submitter on its profile.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.profile-submitter',    
    'defaultParams' => array(
      'title' => 'Submitter',
    ),  
    'adminForm' => array(
      'elements' => array(
        Folder_Form_Helper::getContentField('title', array('value' => 'Submitter')),
      ),
    ),    
  ),
    
  // ------- folder profile title
  array(
    'title' => 'Folder - Profile Title',
    'description' => 'Displays a folder\'s title (folder name; folder parent\'s icon, type, and links) on its profile.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.profile-title',       
  ),
  
  // ------- folder profile tools
  array(
    'title' => 'Folder - Profile Tools',
    'description' => 'Displays a folder\'s tools (Share | Report) on its profile.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.profile-tools',       
  ),
  
  ////////////// FILE PROFILE WIDGETS ///////////////////
  // ------- file profile breadcrumb
  array(
    'title' => 'File - Profile Breadcrumb',
    'description' => 'Displays a file\'s breadcrumb on its profile.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.file-breadcrumb',      
  ),  
  // ------- file profile details
  array(
    'title' => 'File - Profile Details',
    'description' => 'Displays a file\'s details on its profile.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.file-details',    
    'defaultParams' => array(
      'title' => 'File Details',
    ),  
    'adminForm' => array(
      'elements' => array(
        Folder_Form_Helper::getContentField('title', array('value' => 'File Details')),
      ),
    ),
  ),    
  // ------- file profile download
  array(
    'title' => 'File - Profile Download',
    'description' => 'Displays a file\'s download section (File Name and Download This File button) on its profile.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.file-download',       
  ),    
  // ------- file profile folder link
  array(
    'title' => 'File - Profile Folder Link',
    'description' => 'Displays a file\'s folder link (Back to Folder) on its profile.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.file-folder-link',       
  ),   
  // ------- file profile share link
  array(
    'title' => 'File - Profile Share Link',
    'description' => 'Displays a file\'s share link box (Share This File) on its profile.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.file-share-link',       
  ), 
  // ------- file profile social shares
  array(
    'title' => 'File - Profile Social Shares',
    'description' => 'Displays a file\'s social shares such as Facebook, Twitter, Digg using AddThis service on its profile.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.file-social-shares',       
  ),    
  // ------- file profile title
  array(
    'title' => 'File - Profile Title',
    'description' => 'Displays a file\'s title (folder name; folder parent\'s icon, type, and links) on its profile.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.file-title',       
  ),
  // ------- file profile tools
  array(
    'title' => 'File - Profile Tools',
    'description' => 'Displays a file\'s tools (Share | Report) on its profile.',
    'category' => 'Folders',
    'type' => 'widget',
    'name' => 'folder.file-tools',       
  ),
 
);

