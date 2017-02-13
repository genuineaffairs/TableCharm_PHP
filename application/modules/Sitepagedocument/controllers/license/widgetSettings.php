<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepagedocument
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

$db->query('
  INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
  ("sitepagedocument_admin_main_categories", "sitepagedocument", "Categories", "", \'{"route":"admin_default","module":"sitepagedocument","controller":"settings","action":"categories"}\', "sitepagedocument_admin_main", "", 3);
');

//CHECK THAT SITEPAGE PLUGIN IS ACTIVATED OR NOT
$select = new Zend_Db_Select($db);
	$select
  ->from('engine4_core_settings')
  ->where('name = ?', 'sitepage.is.active')
	->limit(1);
$sitepage_settings = $select->query()->fetchAll();
if(!empty($sitepage_settings)) {
	$sitepage_is_active = $sitepage_settings[0]['value'];
}
else {
	$sitepage_is_active = 0;
}

//CHECK THAT SITEPAGE PLUGIN IS INSTALLED OR NOT
$select = new Zend_Db_Select($db);
	$select
	  ->from('engine4_core_modules')
  ->where('name = ?', 'sitepage')
	->where('enabled = ?', 1);
$check_sitepage = $select->query()->fetchObject();
if(!empty($check_sitepage)  && !empty($sitepage_is_active)) {
	$select = new Zend_Db_Select($db);
	$select_page = $select
										 ->from('engine4_core_pages', 'page_id')
										 ->where('name = ?', 'sitepage_index_view')
										 ->limit(1);
  $page = $select_page->query()->fetchAll();
	if(!empty($page)) {
		$page_id = $page[0]['page_id'];
		
		//INSERTING THE DOCUMENT WIDGET IN SITEPAGE_ADMIN_CONTENT TABLE ALSO.
		Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitepagedocument.profile-sitepagedocuments', $page_id, 'Documents', 'true', '115');			
	 
		//INSERTING THE DOCUMENT WIDGET IN CORE_CONTENT TABLE ALSO.
		Engine_Api::_()->getApi('layoutcore', 'sitepage')->setContentDefaultInfo('sitepagedocument.profile-sitepagedocuments', $page_id, 'Documents', 'true', '115');
		
    //INSERTING THE DOCUMENT WIDGET IN SITEPAGE_CONTENT TABLE ALSO.
    $select = new Zend_Db_Select($db);								
		$contentpage_ids = $select->from('engine4_sitepage_contentpages', 'contentpage_id')->query()->fetchAll();
    foreach ($contentpage_ids as $contentpage_id) {
			if(!empty($contentpage_id)) {
        Engine_Api::_()->getDbtable('content', 'sitepage')->setDefaultInfo('sitepagedocument.profile-sitepagedocuments', $contentpage_id['contentpage_id'], 'Documents', 'true', '115');
			}
		}
	}
	
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
          ->where('name = ?', 'sitepagedocument_index_browse')
          ->limit(1);
  ;
  $info = $select->query()->fetch();
  if ( empty($info) ) {
    $db->insert('engine4_core_pages', array(
        'name' => 'sitepagedocument_index_browse',
        'displayname' => 'Browse Page documents',
        'title' => 'Page Documents List',
        'description' => 'This is the page documents.',
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

//INSERT DOCUMENT WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagedocument.sitepage-document', $middle_id, 2);

    //INSERT SEARCH PAGE DOCUMENT WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagedocument.search-sitepagedocument', $right_id, 3, "", "true");

    //INSERT FEATURED PAGE DOCUMENT WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagedocument.homefeaturelist-sitepagedocuments', $right_id, 4, "Featured Documents", "true");

    //INSERT SPONSORED PAGE DOCUMENT WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagedocument.sitepage-sponsoreddocument', $right_id, 5, "Sponsored Documents", "true");

    //INSERT MOST COMMENTED PAGE DOCUMENT WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagedocument.homecomment-sitepagedocuments', $right_id, 6, "Most Commented Documents", "true");

    //INSERT MOST VIEWED PAGE DOCUMENT WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagedocument.homepopular-sitepagedocuments', $right_id, 7, "Most Viewed Documents", "true");

    //INSERT MOST LIKED PAGE DOCUMENT WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagedocument.homelike-sitepagedocuments', $right_id, 8, "Most Liked Documents", "true");

    //INSERT TOP RATED PAGE DOCUMENT WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagedocument.homerate-sitepagedocuments', $right_id, 9, "Top Rated Documents", "true");

    //INSERT RECENT PAGE DOCUMENT WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagedocument.homerecent-sitepagedocuments', $right_id, 10, "Recent Page Documents", "true");

    if ( $infomation && $rowinfo ) {
      Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-ads', $right_id, 11, "", "true");
    }
  }

  $select = new Zend_Db_Select($db);

  // Check if it's already been placed
  $select = new Zend_Db_Select($db);
  $select
          ->from('engine4_core_pages')
          ->where('name = ?', 'sitepagedocument_index_view')
          ->limit(1);
  ;
  $info = $select->query()->fetch();

  if ( empty($info) ) {
    $db->insert('engine4_core_pages', array(
        'name' => 'sitepagedocument_index_view',
        'displayname' => 'Page Document View Page',
        'title' => 'View Page Document',
        'description' => 'This is the view page for a page document.',
        'custom' => 1,
        'provides' => 'subject=sitepagedocument',
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
        'name' => 'right',
        'parent_content_id' => $container_id,
        'order' => 1,
        'params' => '',
    ));
    $right_id = $db->lastInsertId('engine4_core_content');

    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'container',
        'name' => 'middle',
        'parent_content_id' => $container_id,
        'order' => 3,
        'params' => '',
    ));
    $middle_id = $db->lastInsertId('engine4_core_content');

    // middle column content
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitepagedocument.document-content',
        'parent_content_id' => $middle_id,
        'order' => 1,
        'params' => '',
    ));

    //PAGE DOCUMENT OWNER PHOTO WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitepagedocument.document-owner-photo-documents',
        'parent_content_id' => $right_id,
        'order' => 2,
        'params' => '',
      ));

			//PAGE DOCUMENT OPTIONS WIDGET
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitepagedocument.options-documents',
        'parent_content_id' => $right_id,
        'order' => 3,
        'params' => '',
      ));

      //RECENT PAGE DOCUMENT
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitepagedocument.viewrecent-sitepagedocuments',
        'parent_content_id' => $right_id,
        'order' => 4,
        'params' => '',
      ));

			$social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitepagedocument.socialshare-sitepagedocuments"}';

			//SOCIAL SHARE BUTTONS WIDGET
			$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepagedocument.socialshare-sitepagedocuments',
				'parent_content_id' => $right_id,
				'order' => 4,
				'params' => $social_share_default_code,
			));

    if ( $infomation && $rowinfo ) {
      Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-ads', $right_id, 5, "", "true");
    }

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
						->where('name = ?', 'sitepagedocument_mobi_view')
						->limit(1);
		;
		$info = $select->query()->fetch();
		if (empty($info)) {
			$db->insert('engine4_core_pages', array(
					'name' => 'sitepagedocument_mobi_view',
					'displayname' => 'Mobile Page Document Profile',
					'title' => 'Mobile Page Document Profile',
					'description' => 'This is the mobile verison of a Page document profile page.',
					'custom' => 0,
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
					'name' => 'right',
					'parent_content_id' => $container_id,
					'order' => 1,
					'params' => '',
			));
			$right_id = $db->lastInsertId('engine4_core_content');

			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $container_id,
					'order' => 3,
					'params' => '',
			));
			$middle_id = $db->lastInsertId('engine4_core_content');

			// middle column content
			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagedocument.document-content',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => '',
			));
		}
	}

		$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepagedocument_admin_submain_general_tab", "sitepagedocument", "General Settings", "", \'{"route":"admin_default","module":"sitepagedocument","controller":"widgets", "action": "index"}\', "sitepagedocument_admin_submain", "", 1, 0, 1)');

		$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepagedocument_admin_submain_document_tab", "sitepagedocument", "Tabbed Documents Widget", "", \'{"route":"admin_default","module":"sitepagedocument","controller":"settings", "action": "widget"}\', "sitepagedocument_admin_submain", "", 1, 0, 2)');

		$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepagedocument_admin_submain_dayitems", "sitepagedocument", "Document of the Day", "", \'{"route":"admin_default","module":"sitepagedocument","controller":"settings", "action": "manage-day-items"}\', "sitepagedocument_admin_submain", "", 1, 0, 4)');


		$select = new Zend_Db_Select($db);
		$select
						->from('engine4_core_pages')
						->where('name = ?', 'sitepagedocument_index_home')
						->limit(1);
		$info = $select->query()->fetch();
		if (empty($info)) {
			$db->insert('engine4_core_pages', array(
					'name' => 'sitepagedocument_index_home',
					'displayname' => 'Page Documents Home',
					'title' => 'Page Documents Home',
					'description' => 'This is page document home page.',
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
		
			//INSERT TOP RATED PAGE DOCUMENT WIDGET
			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagedocument.homerate-sitepagedocuments',
					'parent_content_id' => $left_id,
					'order' => 14,
					'params' => '{"title":"Top Rated Documents","titleCount":"true"}',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagedocument.homerecent-sitepagedocuments',
					'parent_content_id' => $left_id,
					'order' => 13,
					'params' => '{"title":"Recent Page Documents","titleCount":"true"}',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagedocument.homepopular-sitepagedocuments',
					'parent_content_id' => $right_id,
					'order' => 20,
					'params' => '{"title":"Most Popular Documents","titleCount":"true"}',
			));

			// Middle
			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagedocument.featured-documents-slideshow',
					'parent_content_id' => $middle_id,
					'order' => 15,
					'params' => '{"title":"Featured Documents","titleCount":"true"}',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagedocument.list-documents-tabs-view',
					'parent_content_id' => $middle_id,
					'order' => 17,
					'params' => '{"title":"Documents","margin_photo":"12"}',
			));
			// Right Side
			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagedocument.sitepagedocumentlist-link',
					'parent_content_id' => $right_id,
					'order' => 19,
					'params' => '',
			));

			// Right Side
			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagedocument.search-sitepagedocument',
					'parent_content_id' => $right_id,
					'order' => 18,
					'params' => '',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagedocument.document-of-the-day',
					'parent_content_id' => $left_id,
					'order' => 12,
					'params' => '{"title":"Document of the Day"}',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagedocument.homefeaturelist-sitepagedocuments',
					'parent_content_id' => $right_id,
					'order' => 21,
					'params' => '{"title":"Featured Documents","itemCountPerPage":3}',
			));


			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagedocument.homecomment-sitepagedocuments',
					'parent_content_id' => $right_id,
					'order' => 22,
					'params' => '{"title":"Most Commented Documents","titleCount":"true"}',
			));

			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagedocument.homelike-sitepagedocuments',
					'parent_content_id' => $right_id,
					'order' => 23,
					'params' => '{"title":"Most Liked Documents","titleCount":"true"}',
			));
		}

		$select = new Zend_Db_Select($db);
		$select
					->from('engine4_core_content')
					->where('name = ?', 'sitepagedocument.featurelist-sitepagedocuments');
					
		$is_exit = $select->query()->fetchObject();
		if(!empty($is_exit)) {
			$db->update('engine4_core_content', array(
				'name' => 'sitepagedocument.highlightlist-sitepagedocuments',
				'params' => '{"title":"Highlighted Documents","titleCount":true}',
						), array(
				'name = ?' => 'sitepagedocument.featurelist-sitepagedocuments',
			));
		}
		
		$select = new Zend_Db_Select($db);
		$select
					->from('engine4_sitepage_admincontent')
					->where('name = ?', 'sitepagedocument.featurelist-sitepagedocuments');
					
		$is_exit_content = $select->query()->fetchObject();
		if(!empty($is_exit_content)) {
			$db->update('engine4_core_content', array(
				'name' => 'sitepagedocument.highlightlist-sitepagedocuments',
				'params' => '{"title":"Highlighted Documents","titleCount":true}',
						), array(
				'name = ?' => 'sitepagedocument.featurelist-sitepagedocuments',
			));
		}

		$select = new Zend_Db_Select($db);
		$select
					->from('engine4_sitepage_content')
					->where('name = ?', 'sitepagedocument.featurelist-sitepagedocuments');
					
		$is_exit_sitepagecontent = $select->query()->fetchObject();
		if(!empty($is_exit_sitepagecontent)) {
			$db->update('engine4_core_content', array(
				'name' => 'sitepagedocument.highlightlist-sitepagedocuments',
				'params' => '{"title":"Highlighted Documents","titleCount":true}',
						), array(
				'name = ?' => 'sitepagedocument.featurelist-sitepagedocuments',
			));
		}
}

?>