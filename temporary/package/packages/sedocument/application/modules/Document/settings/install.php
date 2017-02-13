<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Document_Installer extends Engine_Package_Installer_Module
{
  function onPreInstall() {
    $PRODUCT_TYPE = 'sedocument';
    $PLUGIN_TITLE = 'seDocument';
    $PLUGIN_VERSION = '4.6.0';
    $PLUGIN_CATEGORY = 'plugin';
    $PRODUCT_DESCRIPTION = 'Documents / Scribd iPaper plugin allows your users to upload and display documents; share, print and download them; add tags to documents, categorize them and give comments.';
    $_PRODUCT_FINAL_FILE = 'license3.php';
    $_BASE_FILE_NAME = 'documentnew';
    $PRODUCT_TITLE = 'Documents / Scribd iPaper plugin';
    $SocialEngineAddOns_version = '4.6.0p8';

    $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
    $is_file = file_exists($file_path);
    if (empty($is_file)) {
      include_once APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license4.php";
    } else {
			if( !empty($_PRODUCT_FINAL_FILE) ) {
				include_once APPLICATION_PATH . '/application/modules/' . $PLUGIN_TITLE . '/controllers/license/' . $_PRODUCT_FINAL_FILE;
			}
      $db = $this->getDb();
      $select = new Zend_Db_Select($db);
      $select->from('engine4_core_modules')->where('name = ?', $PRODUCT_TYPE);
      $is_Mod = $select->query()->fetchObject();
      if( empty($is_Mod) ) {
				include_once $file_path;
      }
    }
    parent::onPreInstall();
  }

  function onInstall()
  {
		$db = $this->getDb();
    
    //NETWORK PRIVACY WORK
    $column_exist = $db->query("SHOW COLUMNS FROM engine4_documents LIKE 'networks_privacy'")->fetch();
    if(empty($column_exist)) {
      $db->query("ALTER TABLE `engine4_documents` ADD `networks_privacy` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL");
    }

    //WORK FOR CORE CONTENT PAGES
		$select = new Zend_Db_Select($db);

//     $select->from('engine4_core_content',array('params'))
//             ->where('name = ?', 'document.socialshare-documents');
// 		$result = $select->query()->fetchObject();
//     if(!empty($result->params)) {
// 			$params = Zend_Json::decode($result->params);
// 			if(isset($params['code'])) {
// 				$code = $params['code'];
// 				$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
// 				('document.code.share','".$code. "');");
// 			}
//     }

		//MIGRATE DATA TO 'engine4_seaocore_searchformsetting' FROM 'engine4_document_searchform'
		$seocoreSearchformTable = $db->query('SHOW TABLES LIKE \'engine4_seaocore_searchformsetting\'')->fetch();
		$documentSearchformTable = $db->query('SHOW TABLES LIKE \'engine4_document_searchform\'')->fetch();
		if(!empty($seocoreSearchformTable) && !empty($documentSearchformTable)) {
			$datas = $db->query('SELECT * FROM `engine4_document_searchform`')->fetchAll();
			foreach($datas as $data) {
				$data_module = 'document';
				$data_name = $data['name'];
				$data_display = $data['display'];
				$data_order = $data['order'];
				$data_label = $data['label'];

				$db->query("INSERT IGNORE INTO `engine4_seaocore_searchformsetting` (`module`, `name`, `display`, `order`, `label`) VALUES ('$data_module', '$data_name', $data_display, $data_order, '$data_label')");
			}
			$db->query('DROP TABLE IF EXISTS `engine4_document_searchform`');
		}

		$select = new Zend_Db_Select($db);
		$select
			->from('engine4_core_modules')
			->where('name = ?', 'suggestion');
		$is_installed = $select->query()->fetchObject();
		if(!empty($is_installed)) {
			$db->query('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ("document_gutter_suggest", "document", "Suggest to Friends", "Document_Plugin_Menus", "", "document_gutter", "", 1, 0, 6)');
		}

    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_modules')
           ->where('name = ?', 'document')
           ->where('version < ?', '4.2.0');
    $is_enabled = $select->query()->fetchObject();
    if (!empty($is_enabled)) {

			//START MOBILE DOCUMENT BROWSE PAGE WORK
			$select = new Zend_Db_Select($db);
			$select
				->from('engine4_core_pages')
				->where('name = ?', 'document_index_mobi-browse')
				->limit(1);
			$info = $select->query()->fetch();
	
			if( empty($info) ) {
				//CREATE PAGE IF NOT EXIST
				$db->insert('engine4_core_pages', array(
					'name' => 'document_index_mobi-browse',
					'displayname' => 'Mobile Documents Browse Page',
					'title' => 'Mobile Documents Browse Page',
					'description' => 'This is the mobile browse document page.',
					'custom' => 0,
					'layout' => 'default',
				));
				$page_id = $db->lastInsertId('engine4_core_pages');

				//MAIN CONTAINER
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'main',
					'parent_content_id' => null,
					'order' => 1,
					'params' => '',
				));
				$main_container_id = $db->lastInsertId('engine4_core_content');

				//MIDDLE CONTAINER
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $main_container_id,
					'order' => 2,
					'params' => '',
				));
				$middle_container_id = $db->lastInsertId('engine4_core_content');

				//MAIN NAVIGATION WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.navigation-documents',
					'parent_content_id' => $middle_container_id,
					'order' => 3,
					'params' => '',
				));

				//SEARCH FORM WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.search-documents',
					'parent_content_id' => $middle_container_id,
					'order' => 4,
					'params' => '',
				));

				$total_items = $db->select()
												->from('engine4_core_settings', array('value'))
												->where('name = ?', 'document.browse.widgets')
												->limit(1)
												->query()
												->fetchColumn();
				if(empty($total_items)) {
					$total_items = 10;
				}

				$params = '{"itemCount":"'.$total_items.'"}';

				//BROWSE DOCUMENTS WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.browse-documents',
					'parent_content_id' => $middle_container_id,
					'order' => 5,
					'params' => $params,
				));
			}
			//END MOBILE DOCUMENT BROWSE PAGE WORK

			//START MOBILE DOCUMENT HOME PAGE WORK
			$select = new Zend_Db_Select($db);
			$select
				->from('engine4_core_pages')
				->where('name = ?', 'document_index_mobi-home')
				->limit(1);
				;
			$info = $select->query()->fetch();

			if( empty($info) ) {
				//CREATE PAGE IF NOT EXIST
				$db->insert('engine4_core_pages', array(
					'name' => 'document_index_mobi-home',
					'displayname' => 'Mobile Documents Home Page',
					'title' => 'Mobile Documents Home Page',
					'description' => 'This is the mobile document home page.',
					'custom' => 0,
					'layout' => 'default',
				));
				$page_id = $db->lastInsertId('engine4_core_pages');

				//MAIN CONTAINER
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'main',
					'parent_content_id' => null,
					'order' => 1,
					'params' => '',
				));
				$main_container_id = $db->lastInsertId('engine4_core_content');

				//MIDDLE CONTAINER
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $main_container_id,
					'order' => 2,
					'params' => '',
				));
				$middle_container_id = $db->lastInsertId('engine4_core_content');

				//MAIN NAVIGATION WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.navigation-documents',
					'parent_content_id' => $middle_container_id,
					'order' => 3,
					'params' => '',
				));

				//SEARCH FORM WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.search-documents',
					'parent_content_id' => $middle_container_id,
					'order' => 4,
					'params' => '',
				));

				//HOME DOCUMENTS WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.ajax-home-documents',
					'parent_content_id' => $middle_container_id,
					'order' => 5,
					'params' => '',
				));
			}
			//END MOBILE DOCUMENT HOME PAGE WORK

			//START MOBILE DOCUMENT VIEW PAGE WORK
			$select = new Zend_Db_Select($db);
			$select
				->from('engine4_core_pages')
				->where('name = ?', 'document_index_mobi-view')
				->limit(1);
				;
			$info = $select->query()->fetch();

			if( empty($info) ) {
				//CREATE PAGE IF NOT EXIST
				$db->insert('engine4_core_pages', array(
					'name' => 'document_index_mobi-view',
					'displayname' => 'Mobile Document View Page',
					'title' => 'Mobile Document View Page',
					'description' => 'This is the mobile view document page.',
					'custom' => 0,
					'layout' => 'default',
				));
				$page_id = $db->lastInsertId('engine4_core_pages');

				//MAIN CONTAINER
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'main',
					'parent_content_id' => null,
					'order' => 1,
					'params' => '',
				));
				$main_container_id = $db->lastInsertId('engine4_core_content');

				//MIDDLE CONTAINER
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $main_container_id,
					'order' => 2,
					'params' => '',
				));
				$middle_container_id = $db->lastInsertId('engine4_core_content');

				//VIEW DOCUMENTS WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.document-view-documents',
					'parent_content_id' => $middle_container_id,
					'order' => 5,
					'params' => '',
				));
			}
			//END MOBILE DOCUMENT VIEW PAGE WORK

			//START DOCUMENT VIEW PAGE WORK
			$select = new Zend_Db_Select($db);
			$select
				->from('engine4_core_pages')
				->where('name = ?', 'document_index_view')
				->limit(1);

			$info = $select->query()->fetch();

			if( empty($info) ) {
				//CREATE PAGE IF NOT EXIST
				$db->insert('engine4_core_pages', array(
					'name' => 'document_index_view',
					'displayname' => 'Document View Page',
					'title' => 'Document View Page',
				'description' => 'This is the document view page.',
					'custom' => 0,
					'layout' => 'default',
				));
				$page_id = $db->lastInsertId('engine4_core_pages');

				//MAIN CONTAINER
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'main',
					'parent_content_id' => null,
					'order' => 2,
					'params' => '',
				));
				$main_container_id = $db->lastInsertId('engine4_core_content');

				//RIGHT CONTAINER
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'right',
					'parent_content_id' => $main_container_id,
					'order' => 5,
					'params' => '',
				));
				$right_container_id = $db->lastInsertId('engine4_core_content');

				//MAIN MIDDLE CONTAINER
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $main_container_id,
					'order' => 6,
					'params' => '',
				));
				$main_middle_container_id = $db->lastInsertId('engine4_core_content');

				//DOCUMENT VIEWER WINDOW WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.document-view-documents',
					'parent_content_id' => $main_middle_container_id,
					'order' => 3,
					'params' => '',
				));

				//DOCUMENT OWNER PHOTO WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.document-owner-photo-documents',
					'parent_content_id' => $right_container_id,
					'order' => 5,
					'params' => '',
				));

				//DOCUMENT OPTIONS WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.options-documents',
					'parent_content_id' => $right_container_id,
					'order' => 6,
					'params' => '',
				));

				//SEARCH BOX WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.search-box-documents',
					'parent_content_id' => $right_container_id,
					'order' => 7,
					'params' => '{"title":"Search Documents","titleCount":"true"}',
				));

				//SAME USER DOCUMENTS WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.document-owner-documents',
					'parent_content_id' => $right_container_id,
					'order' => 8,
					'params' => '',
				));

				$social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"document.socialshare-documents"}';

				//SOCIAL SHARE BUTTONS WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.socialshare-documents',
					'parent_content_id' => $right_container_id,
					'order' => 9,
					'params' => $social_share_default_code,
				));

				//TAG CLOUD WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.tagcloud-documents',
					'parent_content_id' => $right_container_id,
					'order' => 11,
					'params' => '',
				));

				//ARCHIVES WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.archives-documents',
					'parent_content_id' => $right_container_id,
					'order' => 12,
					'params' => '{"title":"Archives","titleCount":"true"}',
				));

			}
			//END DOCUMENT VIEW PAGE WORK

			//START DOCUMENT HOME PAGE WORK
			$select = new Zend_Db_Select($db);
			$select
				->from('engine4_core_pages')
				->where('name = ?', 'document_index_home')
				->limit(1);

			$info = $select->query()->fetch();

			if( empty($info) ) {
				//CREATE PAGE IF NOT EXIST
				$db->insert('engine4_core_pages', array(
					'name' => 'document_index_home',
					'displayname' => 'Documents Home Page',
					'title' => 'Documents Home Page',
					'description' => 'This is the document home page.',
					'custom' => 0,
					'layout' => 'default',
				));
				$page_id = $db->lastInsertId('engine4_core_pages');

				//TOP CONTAINER
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'top',
					'parent_content_id' => null,
					'order' => 1,
					'params' => '',
				));
				$top_container_id = $db->lastInsertId('engine4_core_content');

				//MAIN CONTAINER
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'main',
					'parent_content_id' => null,
					'order' => 2,
					'params' => '',
				));
				$main_container_id = $db->lastInsertId('engine4_core_content');

				//LEFT CONTAINER
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'left',
					'parent_content_id' => $main_container_id,
					'order' => 4,
					'params' => '',
				));
				$left_container_id = $db->lastInsertId('engine4_core_content');

				//RIGHT CONTAINER
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'right',
					'parent_content_id' => $main_container_id,
					'order' => 5,
					'params' => '',
				));
				$right_container_id = $db->lastInsertId('engine4_core_content');

				//TOP MIDDLE CONTAINER
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $top_container_id,
					'order' => 6,
					'params' => '',
				));
				$top_middle_container_id = $db->lastInsertId('engine4_core_content');

				//MAIN MIDDLE CONTAINER
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'container',
					'name' => 'middle',
					'parent_content_id' => $main_container_id,
					'order' => 6,
					'params' => '',
				));
				$main_middle_container_id = $db->lastInsertId('engine4_core_content');

				//MAIN NAVIGATION WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.navigation-documents',
					'parent_content_id' => $top_middle_container_id,
					'order' => 3,
					'params' => '',
				));

				//DOCUMENT OF THE DAY WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.day-item-document',
					'parent_content_id' => $left_container_id,
					'order' => 6,
					'params' => '{"title":"Document of the Day","titleCount":"true"}',
				));

				//TOP RATED DOCUMENTS WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.rate-documents',
					'parent_content_id' => $left_container_id,
					'order' => 7,
					'params' => '{"title":"Top Rated Documents","titleCount":"true"}',
				));

				//MOST LIKED DOCUMENTS WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.like-documents',
					'parent_content_id' => $left_container_id,
					'order' => 8,
					'params' => '{"title":"Most Liked Documents","titleCount":"true"}',
				));

				//MOST LIKED DOCUMENTS WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.popular-documents',
					'parent_content_id' => $left_container_id,
					'order' => 8,
					'params' => '{"title":"Popular Documents","titleCount":"true"}',
				));
			

				//RECOMMENDED DOCUMENTS WIDGET IF SUGGESTION IS INSTALLED
				$select = new Zend_Db_Select($db);
				$select
					->from('engine4_core_modules')
					->where('name = ?', 'suggestion');
				$is_installed = $select->query()->fetchObject();
				if(!empty($is_installed)) {
					$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'Suggestion.suggestion-document',
						'parent_content_id' => $left_container_id,
						'order' => 9,
						'params' => '{"title":"Recommended Documents","titleCount":"true"}',
					));
				}

				//ZERO DOCUMENTS MESSAGE WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.zero-documents',
					'parent_content_id' => $main_middle_container_id,
					'order' => 10,
					'params' => '',
				));

				//FEATURED SLIDESHOW DOCUMENTS WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.slideshow-featured-documents',
					'parent_content_id' => $main_middle_container_id,
					'order' => 11,
					'params' => '{"title":"Featured Documents","titleCount":"true"}',
				));

				//CATEGORIES, 2nd LEVEL CATEGORIES AND 3rd CATEGORIES LIST WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.middle-column-categories-documents',
					'parent_content_id' => $main_middle_container_id,
					'order' => 12,
					'params' => '{"title":"Categories","titleCount":"true"}',
				));

				//CATEGORIZED DOCUMENTS WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.categorized-documents',
					'parent_content_id' => $main_middle_container_id,
					'order' => 13,
					'params' => '{"title":"Categorically Popular Documents","titleCount":"true"}',
				));

				//AJAX BASED DOCUMENTS WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.ajax-home-documents',
					'parent_content_id' => $main_middle_container_id,
					'order' => 14,
					'params' => '',
				));

				//SEARCH FORM WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.search-documents',
					'parent_content_id' => $right_container_id,
					'order' => 15,
					'params' => '',
				));

				//SPONSORED CAROUSEL WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.sponsored-documents',
					'parent_content_id' => $right_container_id,
					'order' => 16,
					'params' => '{"title":"Sponsored Documents","titleCount":"true"}',
				));

				//TAG CLOUD WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.tagcloud-documents',
					'parent_content_id' => $right_container_id,
					'order' => 17,
					'params' => '',
				));

				//MOST COMMENTED DOCUMENTS WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.comment-documents',
					'parent_content_id' => $right_container_id,
					'order' => 18,
					'params' => '{"title":"Most Commented Documents","titleCount":"true"}',
				));

				//MOST RECENT DOCUMENTS WIDGET
				$db->insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'document.recent-documents',
					'parent_content_id' => $right_container_id,
					'order' => 19,
					'params' => '{"title":"Recent Documents","titleCount":"true"}',
				));
			}
			//END DOCUMENT HOME PAGE WORK

			//START CATEGORIES WIDGET WORK
			$select = new Zend_Db_Select($db);
			$select
				->from('engine4_core_pages', array('page_id'))
				->where('name = ?', 'document_index_browse')
				->limit(1);
			$page = $select->query()->fetch();

			//IF DOCUMENT BROWSE PAGE IS EXIST
			if(!empty($page)) {
				$page_id = $page['page_id'];
				if(!empty($page_id)) {

					$rightContainerId = $db->select()
																	->from('engine4_core_content', array('content_id'))
																	->where('page_id = ?', $page_id)
																	->where('type = ?', 'container')
																	->where('name = ?', 'right')
																	->limit(1)
																	->query()
																	->fetchColumn();

					$leftContainerId = $db->select()
																	->from('engine4_core_content', array('content_id'))
																	->where('page_id = ?', $page_id)
																	->where('type = ?', 'container')
																	->where('name = ?', 'left')
																	->limit(1)
																	->query()
																	->fetchColumn();

					if(!empty($rightContainerId)) { $container_id = $rightContainerId; } else { $container_id = $leftContainerId;}

					if(!empty($container_id)) {

						$select = new Zend_Db_Select($db);
						$select
								->from('engine4_core_content', array('content_id'))					
								->where('page_id =?', $page_id)
								->where('type = ?', 'widget')
								->where('name = ?', 'document.sidebar-categories-documents')
								->limit(1);
						$categoryWidget = $select->query()->fetch();

						if(empty($categoryWidget)) {

							$db->insert('engine4_core_content', array(
								'page_id' => $page_id,
								'type' => 'widget',
								'name' => 'document.sidebar-categories-documents',
								'parent_content_id' => $container_id,
								'order' => 1,
								'params' => '{"title":"Categories","titleCount":"true"}',
							));
						}
					}
				}
			}
			//END CATEGORIES WIDGET WORK

			//START PROFILE DOC WIDGET WORK
			$select = new Zend_Db_Select($db);
			$select
				->from('engine4_core_pages', array('page_id'))
				->where('name = ?', 'user_profile_index')
				->limit(1);
			$page = $select->query()->fetch();

			//IF USER PROFILE PAGE IS EXIST
			if(!empty($page)) {
				$page_id = $page['page_id'];
				if(!empty($page_id)) {

					$select = new Zend_Db_Select($db);
					$select
						->from('engine4_core_content', array('content_id'))
						->where('page_id = ?', $page_id)
						->where('type = ?', 'widget')
						->where('name = ?', 'core.container-tabs')
						->limit(1);
					$container = $select->query()->fetch();

					//IF DESIRABLE CONTAINER IS EXIST
					if(!empty($container)) {
						$container_id = $container['content_id'];
						if(!empty($container_id)) {
							$select = new Zend_Db_Select($db);
							$select
									->from('engine4_core_content', array('content_id'))					
									->where('page_id =?', $page_id)
									->where('type = ?', 'widget')
									->where('name = ?', 'document.profile-doc-documents')
									->where('parent_content_id = ?', $container_id)
									->limit(1);
							$profileDocWidget = $select->query()->fetch();

							//IF PROFILE DOC IS WIDGET IS NOT THERE
							if(empty($profileDocWidget)) {

								$social_share_default_code = '{"title":"Profile Document","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"document.profile-doc-documents"}';

								$db->insert('engine4_core_content', array(
									'page_id' => $page_id,
									'type' => 'widget',
									'name' => 'document.profile-doc-documents',
									'parent_content_id' => $container_id,
									'order' => 999,
									'params' => $social_share_default_code,
								));
							}
						}
					}
				}
			}
			//END PROFILE DOC WIDGET WORK
		}

		//START SOCIAL ENGINE ADDONS WORK
		$db->query("UPDATE  `engine4_seaocores` SET  `is_activate` = '1' WHERE  `engine4_seaocores`.`module_name` ='document';");

		$install_time = time();
		$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('document.upgrade.time', $install_time), 
		('document.myvars', 0),('document.lrucvar.word', 'lruc'), ('document.mypath','Document/controllers/license/license2.php');");
		//END SOCIAL ENGINE ADDONS WORK
		
		//START DOCUMENT DESCRIPTION EDITOR WORK
		$select = new Zend_Db_Select($db);
		$select
			->from('engine4_core_settings')
			->where('name = ?', 'document.show.editor');
		$document_show_editor = $select->query()->fetchObject();
		if(empty($document_show_editor)) {
	
			$select = new Zend_Db_Select($db);
			$select
				->from('engine4_core_settings')
				->where('name = ?', 'document.bbcode');
			$document_bbcode = $select->query()->fetchObject();
			$document_bbcode_value = 0;
			if(empty($document_bbcode))
			{
				$document_bbcode_value = $document_bbcode['value'];
			}

			$select = new Zend_Db_Select($db);
			$select
				->from('engine4_core_settings')
				->where('name = ?', 'document.html');
			$document_html = $select->query()->fetchObject();
			$document_html_value = 0;
			if(empty($document_html))
			{
				$document_html_value = $document_html['value'];
			}

			if($document_bbcode_value == 1 && $document_html_value == 1) {
				$db->insert('engine4_core_settings', array(
					'name' => 'document.show.editor',
					'value' => 0
				));
			}
			else {
				$db->insert('engine4_core_settings', array(
					'name' => 'document.show.editor',
					'value' => 1
				));
			}
		}
		//END DOCUMENT DESCRIPTION EDITOR WORK

		//START REMOVE WIDGET SETTING TAB FROM ADMIN PANEL
    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_menuitems')
           ->where('name = ?', 'document_admin_main_widgets')
           ->where('module = ?', 'document');
    $is_exists = $select->query()->fetchObject();
		if(!empty($is_exists)) {
			$db->query("DELETE FROM `engine4_core_menuitems` WHERE `name` = 'document_admin_main_widgets' AND `module` = 'document' LIMIT 1");
		}
		
    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_modules')
           ->where('name = ?', 'document')
           ->where('version < ?', '4.2.0');
    $is_enabled = $select->query()->fetchObject();
    if (!empty($is_enabled)) {
			$widget_names = array('browse', 'comment', 'featured', 'featurelist', 'popular', 'rate', 'recent');

			foreach($widget_names as $widget_name) {

				$widget_type = $widget_name;
				$setting_name = 'document.'.$widget_name.'.widgets';
				$widget_name = 'document.'.$widget_name.'-documents';
				
				$total_items = $db->select()
												->from('engine4_core_settings', array('value'))
												->where('name = ?', $setting_name)
												->limit(1)
												->query()
												->fetchColumn();

				$setting_value = $total_items;

				if(empty($total_items) && $widget_type == 'browse') {
					$total_items = 10;
				}
				elseif(empty($total_items) && $widget_type == 'featured') {
					$total_items = 15;
				}
				elseif(empty($total_items)) {
					$total_items = 3;
				}

				//WORK FOR CORE CONTENT PAGES
				$select = new Zend_Db_Select($db);
				$select->from('engine4_core_content', array('name', 'params', 'content_id'))->where('name = ?', $widget_name);
				$widgets = $select->query()->fetchAll();
				foreach($widgets as $widget) { 
					$explode_params = explode('}',$widget['params']);
					if(!empty($explode_params[0]) && !strstr($explode_params[0], '"itemCount"')) {
						$params = $explode_params[0].',"itemCount":"'.$total_items.'"}';

						$db->update('engine4_core_content', array('params' => $params), array('content_id = ?' => $widget['content_id'], 'name = ?' => $widget_name));
					}
				}

				//DELETE COUNT ENTRY FROM engine_core_settings TABLE
				if(!empty($setting_value)) {
					$db->query("DELETE FROM `engine4_core_settings` WHERE `engine4_core_settings`.`name` = '$setting_name' LIMIT 1");
				}
			}
		}
		//END REMOVE WIDGET SETTING TAB FROM ADMIN PANEL

		//START MAKE CHANGES IN engine4_documents TABLE
		$table_exist = $db->query("SHOW TABLES LIKE 'engine4_documents'")->fetch();
		if (!empty($table_exist)) {

			$column_exist = $db->query("SHOW COLUMNS FROM engine4_documents LIKE 'photo_id'")->fetch();
			if (empty($column_exist)) {
				$db->query("ALTER TABLE `engine4_documents` ADD `photo_id` INT( 11 ) NOT NULL DEFAULT '0'");
			}

			$column_exist = $db->query("SHOW COLUMNS FROM engine4_documents LIKE 'document_slug'")->fetch();
			if (!empty($column_exist)) {
				$db->query("ALTER TABLE `engine4_documents` DROP `document_slug`");
			}

			$column_exist = $db->query("SHOW COLUMNS FROM engine4_documents LIKE 'profile_doc'")->fetch();
			if (empty($column_exist)) {
				$db->query("ALTER TABLE `engine4_documents` ADD `profile_doc` TINYINT( 2 ) NOT NULL DEFAULT '0'");
			}

			$column_exist = $db->query("SHOW COLUMNS FROM engine4_documents LIKE 'subcategory_id'")->fetch();
			if (empty($column_exist)) {
				$db->query("ALTER TABLE `engine4_documents` ADD  `subcategory_id` int( 11 ) NOT NULL DEFAULT '0'");
			}

			$column_exist = $db->query("SHOW COLUMNS FROM engine4_documents LIKE 'subsubcategory_id'")->fetch();
			if (empty($column_exist)) {
				$db->query("ALTER TABLE `engine4_documents` ADD  `subsubcategory_id` int( 11 ) NOT NULL DEFAULT '0'");
			}

			$column_exist = $db->query("SHOW COLUMNS FROM engine4_documents LIKE 'sponsored'")->fetch();
			if (empty($column_exist)) {
				$db->query("ALTER TABLE `engine4_documents` ADD `sponsored` TINYINT( 1 ) NOT NULL DEFAULT '0'");
			}

			$column_exist = $db->query("SHOW COLUMNS FROM engine4_documents LIKE 'email_allow'")->fetch();
			if (!empty($column_exist)) {
				$db->query("ALTER TABLE `engine4_documents` CHANGE `email_allow` `email_allow` TINYINT( 1 ) NOT NULL DEFAULT '0'");
			}

			$column_exist = $db->query("SHOW COLUMNS FROM engine4_documents LIKE 'secure_allow'")->fetch();
			if (!empty($column_exist)) {
				$db->query("ALTER TABLE `engine4_documents` CHANGE `secure_allow` `secure_allow` TINYINT( 1 ) NOT NULL DEFAULT '0'");
			}

			$column_exist = $db->query("SHOW COLUMNS FROM engine4_documents LIKE 'download_allow'")->fetch();
			if (!empty($column_exist)) {
				$db->query("ALTER TABLE `engine4_documents` CHANGE `download_allow` `download_allow` TINYINT( 1 ) NOT NULL DEFAULT '0'");
			}

			$column_exist = $db->query("SHOW COLUMNS FROM engine4_documents LIKE 'activity_feed'")->fetch();
			if(empty($column_exist)) {
				$db->query("ALTER TABLE `engine4_documents` ADD `activity_feed` TINYINT( 2 ) NOT NULL DEFAULT '0' AFTER `status`");

				//PUT activity_feed = 1 if status = 1;
				$db->query("UPDATE `engine4_documents` SET activity_feed = 1 WHERE status = 1");
			}

			$column_exist = $db->query("SHOW COLUMNS FROM engine4_documents LIKE 'like_count'")->fetch();
			if (empty($column_exist)) {
				
				//ADD like_count COLUMN TO DOCUMENT TABLE
				$db->query("ALTER TABLE `engine4_documents` ADD `like_count` INT( 11 ) NOT NULL DEFAULT '0' AFTER `comment_count`");

				//FETCH DOCUMENTS
				$documents = $db->select()->from('engine4_documents', 'document_id')->query()->fetchAll();

				if (!empty($documents)) {
					foreach($documents as $document)
					{
						$document_id = $document['document_id'];

						if(!empty($document_id)) {

							//GET TOTAL LIKES CORROSPONDING TO DOCUMENT ID
							$total_likes = $db->select()
															->from('engine4_core_likes', array('COUNT(*) AS count'))
															->where('resource_id = ?', $document_id)
															->where('resource_type = ?', 'document')
															->limit(1)
															->query()
															->fetchColumn();

							if(!empty($total_likes)) {
								//UPDATE TOTAL LIKES IN DOCUMENT TABLE
								$db->update('engine4_documents', array('like_count' => $total_likes), array('document_id = ?' => $document_id));
							}
						}
					}
				}
			}
		}
		//END MAKE CHANGES IN engine4_documents TABLE

		//START MAKE CHANGES IN engine4_document_categories TABLE
		$table_exist = $db->query("SHOW TABLES LIKE 'engine4_document_categories'")->fetch();
		if (!empty($table_exist)) {

			$column_exist = $db->query("SHOW COLUMNS FROM engine4_document_categories LIKE 'user_id'")->fetch();
			if (!empty($column_exist)) {
				$db->query("ALTER TABLE `engine4_document_categories` DROP `user_id`");
			}

			$column_exist = $db->query("SHOW COLUMNS FROM engine4_document_categories LIKE 'cat_order'")->fetch();
			if (empty($column_exist)) {
				$db->query("ALTER TABLE `engine4_document_categories` ADD `cat_order` INT( 5 ) NOT NULL DEFAULT '0'");
			}

			$column_exist = $db->query("SHOW COLUMNS FROM engine4_document_categories LIKE 'cat_dependency'")->fetch();
			if (empty($column_exist)) {
				$db->query("ALTER TABLE `engine4_document_categories` ADD `cat_dependency` INT( 5 ) NOT NULL DEFAULT '0'");
			}

			$column_exist = $db->query("SHOW COLUMNS FROM engine4_document_categories LIKE 'subcat_dependency'")->fetch();
			if (empty($column_exist)) {
				$db->query("ALTER TABLE `engine4_document_categories` ADD `subcat_dependency` INT( 5 ) NOT NULL DEFAULT '0'");
			}
		}
		//END MAKE CHANGES IN engine4_document_categories TABLE

		//START PROFILE MAPPING WORK
		$select = new Zend_Db_Select($db);
		$select
			->from('engine4_core_modules')
			->where('name = ?', 'document')
			->where('version < ?', '4.2.0');
		$is_enabled = $select->query()->fetchObject();

		$table_exist = $db->query("SHOW TABLES LIKE 'engine4_document_fields_meta'")->fetch();

		if (!empty($table_exist) && !empty($is_enabled)) {
			$field_id = $db->select()
											->from('engine4_document_fields_meta', array('field_id'))
											->where('type = ?', 'profile_type')
											->where('alias = ?', 'profile_type')
											->limit(1)
											->query()
											->fetchColumn();

			if(empty($field_id)) {
				$db->query("INSERT IGNORE INTO `engine4_document_fields_meta` (`type`, `label`, `description`, `alias`, `required`, `config`, `validators`, `filters`, `display`, `search`) VALUES ('profile_type', 'Default Type', '', 'profile_type', 1, '', NULL, NULL, 0, 2)");

				$field_id = $db->select()
								->from('engine4_document_fields_meta', array('field_id'))
								->where('type = ?', 'profile_type')
								->where('alias = ?', 'profile_type')
								->limit(1)
								->query()
								->fetchColumn();

				if(!empty($field_id)) {
					$db->query("INSERT IGNORE INTO `engine4_document_fields_options` (`field_id`, `label`, `order`) VALUES ($field_id, 'Default Type', 0)");

					$option_id = $db->select()
									->from('engine4_document_fields_options', array('option_id'))
									->where('field_id = ?', $field_id)
									->where('label = ?', 'Default Type')
									->limit(1)
									->query()
									->fetchColumn();
					
					if(!empty($option_id))
					{
						$db->query("UPDATE `engine4_document_fields_maps` SET `field_id` = $field_id, `option_id` = $option_id WHERE `field_id` = 0 AND `option_id` = 0");
						$db->query("INSERT IGNORE INTO `engine4_document_fields_maps` (`field_id`, `option_id`, `child_id`, `order`) VALUES (0, 0, $field_id, 1)");
						$db->query("ALTER TABLE `engine4_document_fields_search` ADD `profile_type` SMALLINT( 11 ) UNSIGNED DEFAULT NULL , ADD INDEX ( `profile_type` )");
						$db->query("UPDATE `engine4_document_fields_search` SET `profile_type` = $option_id");

						$documents = $db->select()
														->from('engine4_documents', 'document_id')
														->query()
														->fetchAll();

						if (!empty($documents)) {
							foreach($documents as $document)
							{
								$document_id = $document['document_id'];
								$db->query("INSERT IGNORE INTO `engine4_document_fields_values` (`item_id`, `field_id`, `index`, `value`) VALUES ($document_id, $field_id, 0, $option_id)");
							}
						}

						$table_exist = $db->query("SHOW TABLES LIKE 'engine4_documents'")->fetch();
						$column_exist = $db->query("SHOW COLUMNS FROM engine4_documents LIKE 'profile_type'")->fetch();
						if (!empty($table_exist) && empty($column_exist)) {
							$db->query("ALTER TABLE `engine4_documents` ADD `profile_type` INT( 11 ) NOT NULL DEFAULT '0'");
							$db->query("UPDATE `engine4_documents` SET `profile_type` = $option_id");
						}

						$db->query("CREATE TABLE IF NOT EXISTS `engine4_document_profilemaps` (`profilemap_id` int(11) unsigned NOT NULL AUTO_INCREMENT, `category_id` int(11) NOT NULL, `profile_type` int(11) NOT NULL, PRIMARY KEY (`profilemap_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1");

						$categories = $db->select()
														->from('engine4_document_categories', 'category_id')
														->where('cat_dependency = ?', 0)
														->where('subcat_dependency = ?', 0)
														->query()
														->fetchAll();

						if (!empty($categories)) {
							foreach($categories as $category)
							{
								$category_id = $category['category_id'];
								$db->query("INSERT IGNORE INTO `engine4_document_profilemaps` (`category_id`, `profile_type`) VALUES ($category_id, $option_id)");
							}
						}
					}
				}
			}
		}
   //END PROFILE MAPPING WORK

    parent::onInstall(); 
  }
  
  //SITEMOBILE CODE TO CALL MY.SQL ON POST INSTALL
    public function onPostInstall() {
        $moduleName = 'document';
        $db = $this->getDb();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitemobile')
                ->where('enabled = ?', 1);
        $is_sitemobile_object = $select->query()->fetchObject();
        if (!empty($is_sitemobile_object)) {
            $db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`) VALUES
('$moduleName','1')");
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_sitemobile_modules')
                    ->where('name = ?', $moduleName)
                    ->where('integrated = ?', 0);
            $is_sitemobile_object = $select->query()->fetchObject();
            if ($is_sitemobile_object) {
                $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
                $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
                if ($controllerName == 'manage' && $actionName == 'install') {
                    $view = new Zend_View();
                    $baseUrl = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
                    $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                    $redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/' . $moduleName . '/integrated/0/redirect/install');
                }
            }
        }
    }
}
?>