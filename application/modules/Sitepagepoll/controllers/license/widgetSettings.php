<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepagepoll
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
		
		//INSERTING THE POLL WIDGET IN SITEPAGE_ADMIN_CONTENT TABLE ALSO.
  	Engine_Api::_()->getDbtable('admincontent', 'sitepage')->setAdminDefaultInfo('sitepagepoll.profile-sitepagepolls', $page_id, 'Polls', 'true', '116');
  	
		//INSERTING THE POLL WIDGET IN CORE_CONTENT TABLE ALSO.
		Engine_Api::_()->getApi('layoutcore', 'sitepage')->setContentDefaultInfo('sitepagepoll.profile-sitepagepolls', $page_id, 'Polls', 'true', '118');
		
    //INSERTING THE POLL WIDGET IN SITEPAGE_CONTENT TABLE ALSO.
    $select = new Zend_Db_Select($db);								
		$contentpage_ids = $select->from('engine4_sitepage_contentpages', 'contentpage_id')->query()->fetchAll();
    foreach ($contentpage_ids as $contentpage_id) {
			if(!empty($contentpage_id)) {
        Engine_Api::_()->getDbtable('content', 'sitepage')->setDefaultInfo('sitepagepoll.profile-sitepagepolls', $contentpage_id['contentpage_id'], 'Polls', 'true', '118');
			}
		}
	}

  $contentTable = Engine_Api::_()->getDbtable('content', 'core');
  $contentTableName = $contentTable->info('name');

  $select = new Zend_Db_Select($db);
  $select
          ->from('engine4_core_pages')
          ->where('name = ?', 'sitepagepoll_index_browse')
          ->limit(1);
  ;
  $info = $select->query()->fetch();
  if ( empty($info) ) {
    $db->insert('engine4_core_pages', array(
        'name' => 'sitepagepoll_index_browse',
        'displayname' => 'Page polls',
        'title' => 'Page Polls List',
        'description' => 'This is the page polls.',
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

//INSERT POLL WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagepoll.sitepage-poll', $middle_id, 2);

    //INSERT SEARCH PAGE POLL WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagepoll.search-sitepagepoll', $right_id, 3, "", "true");

    //INSERT SPONSORED PAGE POLL WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagepoll.sitepage-sponsoredpoll', $right_id, 4, "Sponsored Polls", "true");

    //INSERT MOST COMMENTED PAGE POLL WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagepoll.homecomment-sitepagepolls', $right_id, 5, "Most Commented Polls", "true");

    //INSERT MOST VIEWED PAGE POLL WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagepoll.homeview-sitepagepolls', $right_id, 6, "Most Viewed Polls", "true");

    //INSERT MOST LIKED PAGE POLL WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagepoll.homelike-sitepagepolls', $right_id, 7, "Most Liked Polls", "true");

    //INSERT MOST VOTED PAGE POLL WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagepoll.homevote-sitepagepolls', $right_id, 8, "Most Voted Polls", "true");

     //INSERT MOST RECENT PAGE POLL WIDGET
    Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepagepoll.homerecent-sitepagepolls', $right_id, 9, "Recent Polls", "true");


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
    if ( $infomation && $rowinfo ) {
      Engine_Api::_()->sitepage()->setDefaultDataWidget($contentTable, $contentTableName, $page_id, 'widget', 'sitepage.page-ads', $right_id, 10, "", "true");
    }
  }

  $select = new Zend_Db_Select($db);

  // Check if it's already been placed
  $select = new Zend_Db_Select($db);
  $select
          ->from('engine4_core_pages')
          ->where('name = ?', 'sitepagepoll_index_view')
          ->limit(1);
  ;
  $info = $select->query()->fetch();

  if ( empty($info) ) {
    $db->insert('engine4_core_pages', array(
        'name' => 'sitepagepoll_index_view',
        'displayname' => 'Page Poll View Page',
        'title' => 'View Page Poll',
        'description' => 'This is the view page for a page poll.',
        'custom' => 1,
        'provides' => 'subject=sitepagepoll',
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
        'name' => 'sitepagepoll.sitepagepoll-content',
        'parent_content_id' => $middle_id,
        'order' => 1,
        'params' => '',
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
		->where('name = ?', 'sitepagepoll_mobi_view')
		->limit(1);
		;
		$info = $select->query()->fetch();
		if (empty($info)) {
			$db->insert('engine4_core_pages', array(
						'name' => 'sitepagepoll_mobi_view',
						'displayname' => 'Mobile Page Poll Profile',
						'title' => 'Mobile Page Poll Profile',
						'description' => 'This is the mobile verison of a Page poll profile page.',
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
						'name' => 'sitepagepoll.sitepagepoll-content',
						'parent_content_id' => $middle_id,
						'order' => 1,
						'params' => '',
			));
		}
	}

}

?>