<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: WidgetSettings.php 6590 2010-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

//START LANGUAGE WORK
Engine_Api::_()->getApi('language', 'sitepage')->languageChanges();
//END LANGUAGE WORK

//GET DB
$db = Zend_Db_Table_Abstract::getDefaultAdapter();

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`,
`submenu`, `enabled`, `custom`, `order`) VALUES
("sitepageevent_admin_main_locations", "sitepageevent", "Page Event Locations", "", \'{"route":"admin_default","module":"sitepageevent","controller":"settings","action":"locations"}\', "sitepageevent_admin_main", "", "1", "0", "9");');

$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
("sitepageevent_admin_main_categories", "sitepageevent", "Categories", "", \'{"route":"admin_default","module":"sitepageevent","controller":"settings","action":"categories"}\', "sitepageevent_admin_main", "", 4);');

//CORE MENU ITEMS TABLE
$tableMenuItems = Engine_Api::_()->getDbtable('menuItems', 'core');

//CORE MENU ITEMS TABLE NAME
$tableMenuItemsname = $tableMenuItems->info('name');

//SELECT
$select = $tableMenuItems->select()
        ->from($tableMenuItemsname, array('id'))
        ->where('name = ?', 'sitepageevent_admin_main_manage');
$queary_info = $select->query()->fetchAll();

//INSERT TAB FOR ADMIN MANAGE PAGE EVENTS
if (empty($queary_info)) {
  $menu_item = $tableMenuItems->createRow();
  $menu_item->name = 'sitepageevent_admin_main_manage';
  $menu_item->module = 'sitepageevent';
  $menu_item->label = 'Manage Page Events';
  $menu_item->plugin = '';
  $menu_item->params = '{"route":"admin_default","module":"sitepageevent","controller":"manage","action":"index"}';
  $menu_item->menu = 'sitepageevent_admin_main';
  $menu_item->submenu = '';
  $menu_item->order = 2;
  $menu_item->save();
}

//INSERTING THE ROWS FOR NOTIFICATION
$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `is_request`, `handler`) VALUES
("sitepageevent_accepted", "sitepageevent", "Your request to join the page event {item:$subject} has been approved.", 0, ""),
("sitepageevent_approve", "sitepageevent", "{item:$object} has requested to join the page event {item:$subject}.", 0, ""),
("sitepageevent_invite", "sitepageevent", "{item:$subject} has invited you to the page event {item:$object}.", 0, "sitepageevent.widget.request-sitepageevent");');

//CHECK THAT SITEPAGE PLUGIN IS ACTIVATED OR NOT
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_settings')
        ->where('name = ?', 'sitepage.is.active')
        ->limit(1);
$sitepage_settings = $select->query()->fetchAll();
if (!empty($sitepage_settings)) {
  $sitepage_is_active = $sitepage_settings[0]['value'];
} else {
  $sitepage_is_active = 0;
}

//CHECK THAT SITEPAGE PLUGIN IS INSTALLED OR NOT
$select = new Zend_Db_Select($db);
$select
        ->from('engine4_core_modules')
        ->where('name = ?', 'sitepage')
        ->where('enabled = ?', 1);
$check_sitepage = $select->query()->fetchObject();
if (!empty($check_sitepage) && !empty($sitepage_is_active)) {
  $select = new Zend_Db_Select($db);
  $select_page = $select
          ->from('engine4_core_pages', 'page_id')
          ->where('name = ?', 'sitepage_index_view')
          ->limit(1);
  $page = $select_page->query()->fetchAll();
  if (!empty($page)) {
    $page_id = $page[0]['page_id'];

    //INSERTING THE EVENT WIDGET IN SITEPAGE_ADMIN_CONTENT TABLE ALSO.
    Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitepageevent.profile-sitepageevents', $page_id, 'Events', 'true', '117');

    //INSERTING THE EVENT WIDGET IN CORE_CONTENT TABLE ALSO.
    Engine_Api::_()->getApi('layoutcore', 'sitepage')->setContentDefaultInfo('sitepageevent.profile-sitepageevents', $page_id, 'Events', 'true', '117');

    //INSERTING THE EVENT WIDGET IN SITEPAGE_CONTENT TABLE ALSO.
    $select = new Zend_Db_Select($db);
    $contentpage_ids = $select->from('engine4_sitepage_contentpages', 'contentpage_id')->query()->fetchAll();
    foreach ($contentpage_ids as $contentpage_id) {
      if (!empty($contentpage_id)) {
        Engine_Api::_()->getDbtable('content', 'sitepage')->setDefaultInfo('sitepageevent.profile-sitepageevents', $contentpage_id['contentpage_id'], 'Events', 'true', '117');
      }
    }
  }
   

  $select = new Zend_Db_Select($db);
	$select
					->from('engine4_core_modules')
					->where('name = ?', 'communityad')
					->where('enabled 	 = ?', 1)
					->limit(1);
    ;
	$infomation = $select->query()->fetch();
	$select = new Zend_Db_Select($db);
	$select
					->from('engine4_core_settings')
					->where('name = ?', 'sitepage.communityads')
					->where('value 	 = ?', 1)
					->limit(1);
	;
  $rowinfo = $select->query()->fetch();
  $contentTable = Engine_Api::_()->getDbtable('content', 'core');
  $contentTableName = $contentTable->info('name');
 
  $select = new Zend_Db_Select($db);
	$select
					->from('engine4_core_modules')
					->where('name = ?', 'communityad')
					->where('enabled 	 = ?', 1)
					->limit(1);
	;
	$infomation = $select->query()->fetch();
	$select = new Zend_Db_Select($db);
	$select
					->from('engine4_core_settings')
					->where('name = ?', 'sitepage.communityads')
					->where('value 	 = ?', 1)
					->limit(1);
    ;
  $rowinfo = $select->query()->fetch();

  $select = new Zend_Db_Select($db);
  $select
          ->from('engine4_core_pages')
          ->where('name = ?', 'sitepageevent_index_browse')
          ->limit(1);
  ;
  $info = $select->query()->fetch();
  if ( empty($info) ) {
    $db->insert('engine4_core_pages', array(
        'name' => 'sitepageevent_index_browse',
        'displayname' => 'Browse Page events',
        'title' => 'Page Events List',
        'description' => 'This is the page events.',
        'custom' => 1,
    ));
    $page_id = $db->lastInsertId('engine4_core_pages');
//INSERT MAIN CONTAINER
    $mainContainer = $contentTable->createRow();
    $mainContainer->page_id = $page_id;
    $mainContainer->type = 'container';
    $mainContainer->name = 'main';
    $mainContainer->order = 2;
    $mainContainer->save();
    $container_id = $mainContainer->content_id;

//INSERT MAIN - MIDDLE CONTAINER
    $mainMiddleContainer = $contentTable->createRow();
    $mainMiddleContainer->page_id = $page_id;
    $mainMiddleContainer->type = 'container';
    $mainMiddleContainer->name = 'middle';
    $mainMiddleContainer->parent_content_id = $container_id;
    $mainMiddleContainer->order = 6;
    $mainMiddleContainer->save();
    $middle_id = $mainMiddleContainer->content_id;

//INSERT MAIN - RIGHT CONTAINER
    $mainRightContainer = $contentTable->createRow();
    $mainRightContainer->page_id = $page_id;
    $mainRightContainer->type = 'container';
    $mainRightContainer->name = 'right';
    $mainRightContainer->parent_content_id = $container_id;
    $mainRightContainer->order = 5;
    $mainRightContainer->save();
    $right_id = $mainRightContainer->content_id;

//INSERT TOP CONTAINER
    $topContainer = $contentTable->createRow();
    $topContainer->page_id = $page_id;
    $topContainer->type = 'container';
    $topContainer->name = 'top';
    $topContainer->order = 1;
    $topContainer->save();
    $top_id = $topContainer->content_id;

//INSERT TOP- MIDDLE CONTAINER
    $topMiddleContainer = $contentTable->createRow();
    $topMiddleContainer->page_id = $page_id;
    $topMiddleContainer->type = 'container';
    $topMiddleContainer->name = 'middle';
    $topMiddleContainer->parent_content_id = $top_id;
    $topMiddleContainer->order = 6;
    $topMiddleContainer->save();
    $top_middle_id = $topMiddleContainer->content_id;

    //INSERT NAVIGATION WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.browsenevigation-sitepage', $top_middle_id, 1);

//INSERT EVENT WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepageevent.sitepage-event', $middle_id, 2);

    //INSERT SEARCH PAGE EVENT WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepageevent.search-sitepageevent', $right_id, 3, "", "true");

    //INSERT UPCOMING PAGE EVENT WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepageevent.upcoming-sitepageevent', $right_id, 4, "Upcoming Events", "true");

    //INSERT SPONSORED PAGE EVENT WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepageevent.sitepage-sponsoredevent', $right_id, 5, "Sponsored Events", "true");

    if ( $infomation && $rowinfo ) {
      Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-ads', $right_id, 6, "", "true");
    }
  }

  $select = new Zend_Db_Select($db);
  $select
          ->from('engine4_core_pages')
          ->where('name = ?', 'sitepageevent_index_view')
          ->limit(1);
  ;
  $info = $select->query()->fetch();
  if (empty($info)) {
    $db->insert('engine4_core_pages', array(
        'name' => 'sitepageevent_index_view',
        'displayname' => 'Page Event Profile',
        'title' => 'Page Event Profile',
        'description' => 'This is the profile for an page event.',
        'custom' => 1,
    ));
    $page_id = $db->lastInsertId('engine4_core_pages');

    //CONTAINERS
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'main',
        'parent_content_id' => null,
        'order' => 2,
        'params' => '',
    ));
    $container_id = $db->lastInsertId('engine4_core_content');

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_id,
        'order' => 6,
        'params' => '',
    ));
    $middle_id = $db->lastInsertId('engine4_core_content');

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'top',
        'parent_content_id' => null,
        'order' => 1,
        'params' => '',
    ));
    $topcontainer_id = $db->lastInsertId('engine4_core_content');

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'left',
        'parent_content_id' => $container_id,
        'order' => 4,
        'params' => '',
    ));
    $left_id = $db->lastInsertId('engine4_core_content');

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $topcontainer_id,
        'order' => 6,
        'params' => '',
    ));
    $topmiddle_id = $db->lastInsertId('engine4_core_content');

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitepageevent.profile-breadcrumbevent',
        'parent_content_id' => $topmiddle_id,
        'order' => 3,
        'params' => '',
    ));

    if ($infomation && $rowinfo) {
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'right',
          'parent_content_id' => $container_id,
          'order' => 5,
          'params' => '',
      ));
      $right_id = $db->lastInsertId('engine4_core_content');

      //RIGHT COLUMN
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.page-ads',
          'parent_content_id' => $right_id,
          'order' => 1,
          'params' => '',
      ));
    }

    //MIDDLE COLUMN
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'core.container-tabs',
        'parent_content_id' => $middle_id,
        'order' => 3,
        'params' => '{"max":"6"}',
    ));
    $tab_id = $db->lastInsertId('engine4_core_content');
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitepageevent.profile-status',
        'parent_content_id' => $middle_id,
        'order' => 1,
        'params' => '',
    ));

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'facebookse')
            ->where('enabled 	 = ?', 1)
            ->limit(1);
    ;
    $infomation = $select->query()->fetch();
    if ($infomation) {
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'Facebookse.facebookse-commonlike',
          'parent_content_id' => $middle_id,
          'order' => 2,
          'params' => '',
      ));
    }

    //LEFT COLUMN
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitepageevent.profile-photo',
        'parent_content_id' => $left_id,
        'order' => 1,
        'params' => '',
    ));
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitepageevent.profile-options',
        'parent_content_id' => $left_id,
        'order' => 2,
        'params' => '',
    ));
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitepageevent.profile-info',
        'parent_content_id' => $left_id,
        'order' => 3,
        'params' => '{"title":"Page Event Details"}',
    ));
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitepageevent.profile-rsvp',
        'parent_content_id' => $left_id,
        'order' => 4,
        'params' => '',
    ));

    //TABS
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'seaocore.feed',
        'parent_content_id' => $tab_id,
        'order' => 1,
        'params' => '{"title":"Updates"}',
    ));
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitepageevent.profile-members',
        'parent_content_id' => $tab_id,
        'order' => 2,
        'params' => '{"title":"Guests","titleCount":true}',
    ));
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitepageevent.profile-photos',
        'parent_content_id' => $tab_id,
        'order' => 3,
        'params' => '{"title":"Photos","titleCount":true}',
    ));
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitepage.profile-discussions',
        'parent_content_id' => $tab_id,
        'order' => 4,
        'params' => '{"title":"Discussions","titleCount":true}',
    ));
  }

  $select = new Zend_Db_Select($db);
  $select
          ->from('engine4_core_modules')
          ->where('name = ?', 'mobi')
          ->where('enabled 	 = ?', 1)
          ->limit(1);
  ;
  $infomation = $select->query()->fetch();
  if(!empty($infomation)) {
		$select = new Zend_Db_Select($db);
	  $select
	          ->from('engine4_core_pages')
	          ->where('name = ?', 'sitepageevent_mobi_view')
	          ->limit(1);
	  ;
	  $info = $select->query()->fetch();
	  if (empty($info)) {
	    $db->insert('engine4_core_pages', array(
	        'name' => 'sitepageevent_mobi_view',
	        'displayname' => 'Mobile Page Event Profile',
	        'title' => 'Mobile Page Event Profile',
	        'description' => 'This is the mobile verison of a Page event profile page.',
	        'custom' => 0,
	    ));
	    $page_id = $db->lastInsertId('engine4_core_pages');
	
	    //CONTAINERS
	    $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'main',
	        'parent_content_id' => null,
	        'order' => 2,
	        'params' => '',
	    ));
	    $container_id = $db->lastInsertId('engine4_core_content');
	
	    //CONTAINERS
	    $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'container',
	        'name' => 'middle',
	        'parent_content_id' => $container_id,
	        'order' => 6,
	        'params' => '',
	    ));
	    $middle_id = $db->lastInsertId('engine4_core_content');
	    	
	    //CONTAINERS
	    $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'core.container-tabs',
	        'parent_content_id' => $middle_id,
	        'order' => 7,
	        'params' => '{"max":6}',
	    ));
    
	    $tab_id = $db->lastInsertId('engine4_core_content');
	    
	    //CONTAINERS
	    $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'sitepageevent.profile-breadcrumbevent',
	        'parent_content_id' => $middle_id,
	        'order' => 3,
	        'params' => '',
	    ));
	    
	    //CONTAINERS
	    $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'sitepageevent.profile-status',
	        'parent_content_id' => $middle_id,
	        'order' => 4,
	        'params' => '',
	    ));
 
	    //CONTAINERS
	    $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'sitepageevent.profile-photo',
	        'parent_content_id' => $middle_id,
	        'order' => 5,
	        'params' => '',
	    ));
	    
	    //CONTAINERS
	    $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'sitepageevent.profile-info',
	        'parent_content_id' => $middle_id,
	        'order' => 6,
	        'params' => '',
	    ));	
	    
	    //CONTAINERS
	    $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'seaocore.feed',
	        'parent_content_id' => $tab_id,
	        'order' => 8,
	        'params' => '{"title":"Updates"}',
	    ));	
	    	    
	    //CONTAINERS
	    $db->insert('engine4_core_content', array(
	        'page_id' => $page_id,
	        'type' => 'widget',
	        'name' => 'sitepageevent.profile-members',
	        'parent_content_id' => $tab_id,
	        'order' => 9,
	        'params' => '{"title":}',
	    ));	     
	  }
  }  

  $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepageevent_admin_main_event_tab", "sitepageevent", "Tabbed Events Widget", "", \'{"route":"admin_default","module":"sitepageevent","controller":"settings", "action": "widget"}\', "sitepageevent_admin_main", "", 1, 0, 3)');

	$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepageevent_admin_main_dayitems", "sitepageevent", "Event of the Day", "", \'{"route":"admin_default","module":"sitepageevent","controller":"settings", "action": "manage-day-items"}\', "sitepageevent_admin_main", "", 1, 0, 4)');


	$select = new Zend_Db_Select($db);
	$select
					->from('engine4_core_pages')
					->where('name = ?', 'sitepageevent_index_home')
					->limit(1);
	$info = $select->query()->fetch();
	if (empty($info)) {
		$db->insert('engine4_core_pages', array(
				'name' => 'sitepageevent_index_home',
				'displayname' => 'Page Events Home',
				'title' => 'Page Events Home',
				'description' => 'This is page event home page.',
				'custom' => 1
		));
		$page_id = $db->lastInsertId('engine4_core_pages');

		// containers
		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 2,
				'params' => '',
		));
		$container_id = $db->lastInsertId('engine4_core_content');

		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'right',
				'parent_content_id' => $container_id,
				'order' => 5,
				'params' => '',
		));
		$right_id = $db->lastInsertId('engine4_core_content');

		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'left',
				'parent_content_id' => $container_id,
				'order' => 4,
				'params' => '',
		));
		$left_id = $db->lastInsertId('engine4_core_content');

		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'top',
				'parent_content_id' => null,
				'order' => 1,
				'params' => '',
		));
		$top_id = $db->lastInsertId('engine4_core_content');

		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'middle',
				'parent_content_id' => $top_id,
				'order' => 6,
				'params' => '',
		));
		$top_middle_id = $db->lastInsertId('engine4_core_content');

		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'middle',
				'parent_content_id' => $container_id,
				'order' => 6,
				'params' => '',
		));
		$middle_id = $db->lastInsertId('engine4_core_content');

	// Top Middle
		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepage.browsenevigation-sitepage',
				'parent_content_id' => $top_middle_id,
				'order' => 3,
				'params' => '',
		));

		// Left
	
		//INSERT MOST VIEWED PAGE EVENT WIDGET
			Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepageevent.homeview-sitepageevents', $right_id, 21, "Most Viewed Events", "true");

		// Middle
		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepageevent.featured-events-slideshow',
				'parent_content_id' => $middle_id,
				'order' => 16,
				'params' => '{"title":"Featured Upcoming Events","titleCount":"true"}',
		));

		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepageevent.list-events-tabs-view',
				'parent_content_id' => $middle_id,
				'order' => 18,
				'params' => '{"title":"Events","margin_photo":"12"}',
		));
		// Right Side
		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepageevent.sitepageeventlist-link',
				'parent_content_id' => $right_id,
				'order' => 20,
				'params' => '',
		));

		// Right Side
		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepageevent.search-sitepageevent',
				'parent_content_id' => $right_id,
				'order' => 19,
				'params' => '',
		));

		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepageevent.event-of-the-day',
				'parent_content_id' => $left_id,
				'order' => 14,
				'params' => '{"title":"Event of the Day"}',
		));

    $db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepageevent.upcoming-sitepageevent',
				'parent_content_id' => $left_id,
				'order' => 15,
				'params' => '{"title":"Upcoming Events","titleCount":"true"}',
		));

		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepageevent.homefeaturelist-sitepageevents',
				'parent_content_id' => $right_id,
				'order' => 21,
				'params' => '{"title":"Featured Events","itemCountPerPage":3}',
		));

	}

}
?>