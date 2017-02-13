<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepagenote
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
  ("sitepagenote_admin_main_categories", "sitepagenote", "Categories", "", \'{"route":"admin_default","module":"sitepagenote","controller":"settings","action":"categories"}\', "sitepagenote_admin_main", "", 3);
');

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

    //INSERTING THE NOTE WIDGET IN SITEPAGE_ADMIN_CONTENT TABLE ALSO
    Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitepagenote.profile-sitepagenotes', $page_id, 'Notes', 'true', '112');

    //INSERTING THE NOTE WIDGET IN CORE_CONTENT TABLE ALSO
    Engine_Api::_()->getApi('layoutcore', 'sitepage')->setContentDefaultInfo('sitepagenote.profile-sitepagenotes', $page_id, 'Notes', 'true', '112');

    //INSERTING THE NOTE WIDGET IN SITEPAGE_CONTENT TABLE ALSO
    $select = new Zend_Db_Select($db);
    $contentpage_ids = $select->from('engine4_sitepage_contentpages', 'contentpage_id')->query()->fetchAll();
    foreach ($contentpage_ids as $contentpage_id) {
      if (!empty($contentpage_id)) {
        Engine_Api::_()->getDbtable('content', 'sitepage')->setDefaultInfo('sitepagenote.profile-sitepagenotes', $contentpage_id['contentpage_id'], 'Notes', 'true', '112');
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
          ->from('engine4_core_pages')
          ->where('name = ?', 'sitepagenote_index_browse')
          ->limit(1);
  ;
  $info = $select->query()->fetch();
  if ( empty($info) ) {
    $db->insert('engine4_core_pages', array(
        'name' => 'sitepagenote_index_browse',
        'displayname' => 'Browse Page Notes',
        'title' => 'Page Notes List',
        'description' => 'This is the page notes.',
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

//INSERT NOTE WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagenote.sitepage-note', $middle_id, 2);

    //INSERT SEARCH PAGE NOTE WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagenote.search-sitepagenote', $right_id, 3, "", "true");

    //INSERT MOST COMMENTED PAGE NOTE WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagenote.homecomment-sitepagenotes', $right_id, 4, "Most Commented Notes", "true");

    //INSERT SPONSORED PAGE NOTE WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagenote.sitepage-sponsorednote', $right_id, 5, "Sponsored Notes", "true");

    //INSERT MOST LIKED PAGE NOTE WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagenote.homelike-sitepagenotes', $right_id, 6, "Most Liked Notes", "true");

    //INSERT RECENT PAGE NOTE WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagenote.homerecent-sitepagenotes', $right_id, 7, "Recent Notes", "true");

    if ( $infomation && $rowinfo ) {
      Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-ads', $right_id, 10, "", "true");
    }
  }
$select = new Zend_Db_Select($db);

  // Check if it's already been placed
  $select = new Zend_Db_Select($db);
  $select
          ->from('engine4_core_pages')
          ->where('name = ?', 'sitepagenote_index_view')
          ->limit(1);
  ;
  $info = $select->query()->fetch();

  if ( empty($info) ) {
    $db->insert('engine4_core_pages', array(
        'name' => 'sitepagenote_index_view',
        'displayname' => 'Page Note View Page',
        'title' => 'View Page Note',
        'description' => 'This is the view page for a page note.',
        'custom' => 1,
        'provides' => 'subject=sitepagenote',
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
        'name' => 'sitepagenote.note-content',
        'parent_content_id' => $middle_id,
        'order' => 1,
        'params' => '',
    ));

    // right column
    $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type' => 'widget',
        'name' => 'sitepagenote.show-same-tags',
        'parent_content_id' => $right_id,
        'order' => 1,
        'params' => '{"title":"Related Notes"}',
    ));

		if ( $infomation && $rowinfo ) {
			Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-ads', $right_id, 10, "", "true");
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
		->where('name = ?', 'sitepagenote_mobi_view')
		->limit(1);
		;
		$info = $select->query()->fetch();
		if (empty($info)) {
			$db->insert('engine4_core_pages', array(
						'name' => 'sitepagenote_mobi_view',
						'displayname' => 'Mobile Page Note Profile',
						'title' => 'Mobile Page Note Profile',
						'description' => 'This is the mobile verison of a Page note profile page.',
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
					'name' => 'sitepagenote.note-content',
					'parent_content_id' => $middle_id,
					'order' => 1,
					'params' => '',
			));

			// right column
			$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'sitepagenote.show-same-tags',
					'parent_content_id' => $right_id,
					'order' => 1,
					'params' => '{"title":"Related Notes"}',
			));
		}
	}

  $db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepagenote_admin_submain_general_tab", "sitepagenote", "General Settings", "", \'{"route":"admin_default","module":"sitepagenote","controller":"widgets", "action": "index"}\', "sitepagenote_admin_submain", "", 1, 0, 1)');

	$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepagenote_admin_submain_note_tab", "sitepagenote", "Tabbed Notes Widget", "", \'{"route":"admin_default","module":"sitepagenote","controller":"settings", "action": "widget"}\', "sitepagenote_admin_submain", "", 1, 0, 2)');

	$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("sitepagenote_admin_submain_dayitems", "sitepagenote", "Note of the Day", "", \'{"route":"admin_default","module":"sitepagenote","controller":"settings", "action": "manage-day-items"}\', "sitepagenote_admin_submain", "", 1, 0, 4)');


	$select = new Zend_Db_Select($db);
	$select
					->from('engine4_core_pages')
					->where('name = ?', 'sitepagenote_index_home')
					->limit(1);
	$info = $select->query()->fetch();
	if (empty($info)) {
		$db->insert('engine4_core_pages', array(
				'name' => 'sitepagenote_index_home',
				'displayname' => 'Page Notes Home',
				'title' => 'Page Notes Home',
				'description' => 'This is page note home page.',
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
		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepagenote.homerecent-sitepagenotes',
				'parent_content_id' => $left_id,
				'order' => 14,
				'params' => '{"title":"Recent Page Notes","titleCount":"true"}',
		));

		// Middle
		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepagenote.featured-notes-slideshow',
				'parent_content_id' => $middle_id,
				'order' => 15,
				'params' => '{"title":"Featured Notes","titleCount":"true"}',
		));

		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepagenote.list-notes-tabs-view',
				'parent_content_id' => $middle_id,
				'order' => 17,
				'params' => '{"title":"Notes","margin_photo":"12"}',
		));
		// Right Side
		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepagenote.sitepagenotelist-link',
				'parent_content_id' => $right_id,
				'order' => 19,
				'params' => '',
		));

		// Right Side
		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepagenote.search-sitepagenote',
				'parent_content_id' => $right_id,
				'order' => 18,
				'params' => '',
		));

		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepagenote.note-of-the-day',
				'parent_content_id' => $left_id,
				'order' => 13,
				'params' => '{"title":"Note of the Day"}',
		));

		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepagenote.homefeaturelist-sitepagenotes',
				'parent_content_id' => $right_id,
				'order' => 21,
				'params' => '{"title":"Featured Notes","itemCountPerPage":3}',
		));


		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepagenote.homecomment-sitepagenotes',
				'parent_content_id' => $right_id,
				'order' => 22,
				'params' => '{"title":"Most Commented Notes","titleCount":"true"}',
		));

		$db->insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'sitepagenote.homelike-sitepagenotes',
				'parent_content_id' => $right_id,
				'order' => 23,
				'params' => '{"title":"Most Liked Notes","titleCount":"true"}',
		));
	}
}
?>