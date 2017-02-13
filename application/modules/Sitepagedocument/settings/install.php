<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagedocument_Installer extends Engine_Package_Installer_Module {

  function onPreInstall() {
    $db = $this->getDb();

    //CHECK THAT SITEPAGE PLUGIN IS ACTIVATED OR NOT
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'sitepage.is.active')
            ->limit(1);
    $sitepage_settings = $select->query()->fetchAll();
    if ( !empty($sitepage_settings) ) {
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
    if ( !empty($check_sitepage) && !empty($sitepage_is_active) ) {
      $PRODUCT_TYPE = 'sitepagedocument';
      $PLUGIN_TITLE = 'Sitepagedocument';
      $PLUGIN_VERSION = '4.8.0';
      $PLUGIN_CATEGORY = 'plugin';
      $PRODUCT_DESCRIPTION = 'Sitepagedocument Plugin';
      $PRODUCT_TITLE = 'Directory / Pages - Documents Extension';
      $_PRODUCT_FINAL_FILE = 0;
      $sitepage_plugin_version = '4.8.0';
      $SocialEngineAddOns_version = '4.8.0p1';
      $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
      $is_file = file_exists($file_path);
      if ( empty($is_file) ) {
        include APPLICATION_PATH . "/application/modules/Sitepage/controllers/license/license4.php";
      }
      else {
        include $file_path;
      }

      $pageTime = time();
      $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
			('sitepagedocument.basetime', $pageTime ),
			('sitepagedocument.isvar', 0 ),
			('sitepagedocument.filepath', 'Sitepagedocument/controllers/license/license2.php');");

      //PUT SITEPAGE DOCUMENT WIDGET IN ADMIN CONTENT TABLE
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_modules')
              ->where('name = ?', 'sitepagedocument')
              ->where('version <= ?', '4.1.5p1');
      $is_enabled = $select->query()->fetchObject();
      if ( !empty($is_enabled) ) {
        $select = new Zend_Db_Select($db);
        $select_page = $select
                        ->from('engine4_core_pages', 'page_id')
                        ->where('name = ?', 'sitepage_index_view')
                        ->limit(1);
        $page = $select_page->query()->fetchAll();
        if ( !empty($page) ) {
          $page_id = $page[0]['page_id'];
          $select = new Zend_Db_Select($db);
          $select_content = $select
                          ->from('engine4_sitepage_admincontent')
                          ->where('page_id = ?', $page_id)
                          ->where('type = ?', 'widget')
                          ->where('name = ?', 'sitepagedocument.profile-sitepagedocuments')
                          ->limit(1);
          $content = $select_content->query()->fetchAll();
          if ( empty($content) ) {
            $select = new Zend_Db_Select($db);
            $select_container = $select
                            ->from('engine4_sitepage_admincontent', 'admincontent_id')
                            ->where('page_id = ?', $page_id)
                            ->where('type = ?', 'container')
                            ->limit(1);
            $container = $select_container->query()->fetchAll();
            if ( !empty($container) ) {
              $container_id = $container[0]['admincontent_id'];
              $select = new Zend_Db_Select($db);
              $select_middle = $select
                              ->from('engine4_sitepage_admincontent')
                              ->where('parent_content_id = ?', $container_id)
                              ->where('type = ?', 'container')
                              ->where('name = ?', 'middle')
                              ->limit(1);
              $middle = $select_middle->query()->fetchAll();
              if ( !empty($middle) ) {
                $middle_id = $middle[0]['admincontent_id'];
                $select = new Zend_Db_Select($db);
                $select_tab = $select
                                ->from('engine4_sitepage_admincontent')
                                ->where('type = ?', 'widget')
                                ->where('name = ?', 'core.container-tabs')
                                ->where('page_id = ?', $page_id)
                                ->limit(1);
                $tab = $select_tab->query()->fetchAll();
                $tab_id = 0;
                if ( !empty($tab) ) {
                  $tab_id = $tab[0]['admincontent_id'];
                }
                else {
                  $tab_id = $middle_id;
                }

                $db->insert('engine4_sitepage_admincontent', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitepagedocument.profile-sitepagedocuments',
                    'parent_content_id' => $tab_id,
                    'order' => 115,
                    'params' => '{"title":"Documents","titleCount":"true"}',
                ));
              }
            }
          }
        }
      }
      parent::onPreInstall();
    }
    elseif ( !empty($check_sitepage) && empty($sitepage_is_active) ) {
      $baseUrl = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      if ( strstr($url_string, "manage/install") ) {
        $calling_from = 'install';
      }
      else if ( strstr($url_string, "manage/query") ) {
        $calling_from = 'queary';
      }
      $explode_base_url = explode("/", $baseUrl);
      foreach ( $explode_base_url as $url_key ) {
        if ( $url_key != 'install' ) {
          $core_final_url .= $url_key . '/';
        }
      }

      return $this->_error("<span style='color:red'>Note: You have installed the <a href='http://www.socialengineaddons.com/socialengine-directory-pages-plugin' target='_blank'>Directory / Pages Plugin</a> but not activated it on your site yet. Please activate it first before installing the Directory / Pages - Documents Extension.</span><br/> <a href='" . 'http://' . $core_final_url . "admin/sitepage/settings/readme'>Click here</a> to activate the Directory / Pages Plugin.");
    }
    else {
      $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
      return $this->_error("<span style='color:red'>Note: You have not installed the <a href='http://www.socialengineaddons.com/socialengine-directory-pages-plugin' target='_blank'>Directory / Pages Plugin</a> on your site yet. Please install it first before installing the <a href='http://www.socialengineaddons.com/pageextensions/socialengine-directory-pages-documents' target='_blank'>Directory / Pages - Documents Extension</a>.</span><br/> <a href='" . $base_url . "/manage'>Click here</a> to go Manage Packages.");
    }
  }

  function onInstall() {

    $db = $this->getDb();
    
    $db->query('UPDATE  `engine4_activity_notificationtypes` SET  `body` =  \'{item:$subject} has created a page document {item:$object}.\' WHERE  `engine4_activity_notificationtypes`.`type` =  "sitepagedocument_create";');
    
    //WORK FOR CORE CONTENT PAGES
		$select = new Zend_Db_Select($db);

    $select->from('engine4_core_content',array('params'))
            ->where('name = ?', 'sitepagedocument.socialshare-sitepagedocuments');
		$result = $select->query()->fetchObject();
    if(!empty($result->params)) {
			$params = Zend_Json::decode($result->params);
			if(isset($params['code'])) {
				$code = $params['code'];
				$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
				('sitepagedocument.code.share','".$code. "');");
			}
    }
  
    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepagedocument_documents'")->fetch();
    if (!empty($table_exist)) {

      $columnExist = $db->query("SHOW COLUMNS FROM engine4_sitepagedocument_documents LIKE 'sitepagedocument_slug'")->fetch();
      if(!empty($columnExist)) {
        $db->query("ALTER TABLE `engine4_sitepagedocument_documents` DROP `sitepagedocument_slug`");
      }

      //DROP THE COLUMN FROM THE "engine4_sitepagedocument_documents" TABLE
      $pageOwnerIdColumn = $db->query("SHOW COLUMNS FROM engine4_sitepagedocument_documents LIKE 'page_owner_id'")->fetch();
      if(!empty($pageOwnerIdColumn)) {
        $db->query("ALTER TABLE `engine4_sitepagedocument_documents` DROP `page_owner_id`");
      }

      //DROP THE COLUMN FROM THE "engine4_sitepagedocument_documents" TABLE
      $ownerTypeColumn = $db->query("SHOW COLUMNS FROM engine4_sitepagedocument_documents LIKE 'owner_type'")->fetch();
      if(!empty($ownerTypeColumn)) {
        $db->query("ALTER TABLE `engine4_sitepagedocument_documents` DROP `owner_type`");
      }

      //DROP THE INDEX FROM THE "engine4_sitepagedocument_documents" TABLE
      $ownerTypeColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepagedocument_documents` WHERE Key_name = 'owner_type'")->fetch();

      if( !empty($ownerTypeColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepagedocument_documents` DROP INDEX `owner_type`");
      }

      //ADD THE INDEX FROM THE "engine4_sitepagedocument_documents" TABLE
      $pageIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepagedocument_documents` WHERE Key_name = 'page_id'")->fetch();

      if( empty($pageIdColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepagedocument_documents` ADD INDEX ( `page_id` );");
      }     

      //ADD THE INDEX FROM THE "engine4_sitepagedocument_documents" TABLE
      $ownerIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepagedocument_documents` WHERE Key_name = 'owner_id'")->fetch();

      if( empty($ownerIdColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepagedocument_documents` ADD INDEX ( `owner_id` );");
      } 

      //DROP THE INDEX FROM THE "engine4_sitepagedocument_documents" TABLE
      $indexColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepagedocument_documents` WHERE Key_name = 'INDEX'")->fetch();

      if( !empty($indexColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepagedocument_documents` DROP INDEX `INDEX`");
      }

      //DROP THE INDEX FROM THE "engine4_sitepagedocument_documents" TABLE
      $fullTextColumn = $db->query("SHOW COLUMNS FROM engine4_sitepagedocument_documents LIKE 'fulltext'")->fetch();

      if( !empty($fullTextColumn)) {
        $db->query("ALTER TABLE `engine4_sitepagedocument_documents` CHANGE `fulltext` `fulltext` LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;");
      }
    }

    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepagedocument_ratings'")->fetch();
    if (!empty($table_exist)) {

      //ADD THE INDEX FROM THE "engine4_sitepagedocument_ratings" TABLE
      $documentIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepagedocument_ratings` WHERE Key_name = 'document_id'")->fetch();

      if( empty($documentIdColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepagedocument_ratings` ADD INDEX ( `document_id` );");
      }  
    }
    
    //REMOVED WIDGET SETTING TAB FROM ADMIN PANEL
    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_modules')
            ->where('name = ?', 'sitepagedocument')
            ->where('version <= ?', '4.1.7p2');
    $is_enabled = $select->query()->fetchObject();
    if ( !empty($is_enabled) ) {
      $widget_names = array('comment', 'recent', 'like', 'featurelist', 'popular', 'rate');

      foreach ( $widget_names as $widget_name ) {

        $widget_type = $widget_name;

        $widget_name = 'sitepagedocument.' . $widget_name . '-sitepagedocuments';
        $setting_name = 'sitepagedocument.' . $widget_type . '.widgets';
        
        $total_items = $db->select()
                        ->from('engine4_core_settings', array('value'))
                        ->where('name = ?', $setting_name)
                        ->limit(1)
                        ->query()
                        ->fetchColumn();

        if ( empty($total_items) ) {
          $total_items = 3;
        }

        //WORK FOR CORE CONTENT PAGES
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_content', array('name', 'params', 'content_id'))->where('name = ?', $widget_name);
        $widgets = $select->query()->fetchAll();
        foreach ( $widgets as $widget ) {
          $explode_params = explode('}', $widget['params']);
          if ( !empty($explode_params[0]) && !strstr($explode_params[0], '"itemCount"') ) {
            $params = $explode_params[0] . ',"itemCount":"' . $total_items . '"}';

            $db->update('engine4_core_content', array('params' => $params), array('content_id = ?' => $widget['content_id'], 'name = ?' => $widget_name));
          }
        }

        //WORK FOR ADMIN USER CONTENT PAGE
        $select = new Zend_Db_Select($db);
        $select->from('engine4_sitepage_admincontent', array('name', 'params', 'admincontent_id'))->where('name = ?', $widget_name);
        $widgets = $select->query()->fetchAll();
        foreach ( $widgets as $widget ) {
          $explode_params = explode('}', $widget['params']);
          if ( !empty($explode_params[0]) && !strstr($explode_params[0], '"itemCount"') ) {
            $params = $explode_params[0] . ',"itemCount":"' . $total_items . '"}';

            $db->update('engine4_sitepage_admincontent', array('params' => $params), array('admincontent_id = ?' => $widget['admincontent_id'], 'name = ?' => $widget_name));
          }
        }

        //WORK FOR USER CONTENT PAGES
        $select = new Zend_Db_Select($db);
        $select->from('engine4_sitepage_content', array('name', 'params', 'content_id'))->where('name = ?', $widget_name);
        $widgets = $select->query()->fetchAll();
        foreach ( $widgets as $widget ) {
          $explode_params = explode('}', $widget['params']);
          if ( !empty($explode_params[0]) && !strstr($explode_params[0], '"itemCount"') ) {
            $params = $explode_params[0] . ',"itemCount":"' . $total_items . '"}';

            $db->update('engine4_sitepage_content', array('params' => $params), array('content_id = ?' => $widget['content_id'], 'name = ?' => $widget_name));
          }
        }
      }
    }

    //START THE WORK FOR MAKE WIDGETIZE PAGE OF DOCUMENTS LISTING AND DOCUMENT VIEW PAGE
    $select = new Zend_Db_Select($db);
		$select
						->from('engine4_core_modules')
						->where('name = ?', 'sitepagedocument')
						->where('version < ?', '4.2.0');
		$is_enabled = $select->query()->fetchObject();
    if(!empty($is_enabled)) {
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
			$rowinfo = $select->query()->fetch();

			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_core_pages')
							->where('name = ?', 'sitepagedocument_index_documentlist')
							->limit(1);
			;
			$info = $select->query()->fetch();

      $select = new Zend_Db_Select($db);
			$select
							->from('engine4_core_pages')
							->where('name = ?', 'sitepagedocument_index_browse')
							->limit(1);
			;
			$info_browse = $select->query()->fetch();

			if ( empty($info) && empty($info_browse) ) {
				$db->insert('engine4_core_pages', array(
						'name' => 'sitepagedocument_index_browse',
						'displayname' => 'Browse Page Documents',
						'title' => 'Page Documents',
						'description' => 'This is the page documents.',
						'custom' => 1,
				));
				$page_id = $db->lastInsertId('engine4_core_pages');

				//CONTAINERS
				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'container',
						'name' => 'main',
						'parent_content_id' => Null,
						'order' => 2,
						'params' => '',
				));
				$container_id = $db->lastInsertId('engine4_core_content');

				//INSERT MAIN - MIDDLE CONTAINER
				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'container',
						'name' => 'middle',
						'parent_content_id' => $container_id,
						'order' => 6,
						'params' => '',
				));
				$middle_id = $db->lastInsertId('engine4_core_content');


				//INSERT MAIN - RIGHT CONTAINER
				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'container',
						'name' => 'right',
						'parent_content_id' => $container_id,
						'order' => 5,
						'params' => '',
				));
				$right_id = $db->lastInsertId('engine4_core_content');


				//INSERT TOP CONTAINER
				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'container',
						'name' => 'top',
						'parent_content_id' => Null,
						'order' => 1,
						'params' => '',
				));
				$top_id = $db->lastInsertId('engine4_core_content');


				//INSERT TOP- MIDDLE CONTAINER
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
						'type' => 'widget',
						'name' => 'sitepage.browsenevigation-sitepage',
						'parent_content_id' => $top_middle_id,
						'order' => 1,
						'params' => '{"title":"","titleCount":""}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagedocument.search-sitepagedocument',
						'parent_content_id' => $right_id,
						'order' => 3,
						'params' => '{"title":"","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagedocument.sitepage-document',
						'parent_content_id' => $middle_id,
						'order' => 2,
						'params' => '{"title":"","titleCount":""}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagedocument.homefeaturelist-sitepagedocuments',
						'parent_content_id' => $right_id,
						'order' => 4,
						'params' => '{"title":"Featured Documents","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagedocument.sitepage-sponsoreddocument',
						'parent_content_id' => $right_id,
						'order' => 5,
						'params' => '{"title":"Sponsored Documents","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagedocument.homecomment-sitepagedocuments',
						'parent_content_id' => $right_id,
						'order' => 6,
						'params' => '{"title":"Most Commented Documents","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagedocument.homepopular-sitepagedocuments',
						'parent_content_id' => $right_id,
						'order' => 7,
						'params' => '{"title":"Most Popular Documents","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagedocument.homelike-sitepagedocuments',
						'parent_content_id' => $right_id,
						'order' => 8,
						'params' => '{"title":"Most Liked Documents","titleCount":"true"}',
				));


				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagedocument.homerate-sitepagedocuments',
						'parent_content_id' => $right_id,
						'order' => 9,
						'params' => '{"title":"Top Rated Documents","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
							'page_id' => $page_id,
							'type' => 'widget',
							'name' => 'sitepagedocument.homerecent-sitepagedocuments',
							'parent_content_id' => $right_id,
							'order' => 10,
							'params' => '{"title":"Recent Page Documents","titleCount":"true"}',
					));

				if ( $infomation && $rowinfo ) {
					$db->insert('engine4_core_content', array(
							'page_id' => $page_id,
							'type' => 'widget',
							'name' => 'sitepage.page-ads',
							'parent_content_id' => $right_id,
							'order' => 11,
							'params' => '{"title":"","titleCount":""}',
					));
				}
			}
      else {
        $db->update('engine4_core_pages', array('name' => 'sitepagedocument_index_browse'), array('name = ?' => 'sitepagedocument_index_documentlist'));
      }

			$db = $this->getDb();
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

			if ( $infomation && $rowinfo ) {
				$db->insert('engine4_core_content', array(
							'page_id' => $page_id,
							'type' => 'widget',
							'name' => 'sitepage.page-ads',
							'parent_content_id' => $right_id,
							'order' => 5,
							'params' => '{"title":"","titleCount":""}',
					));
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
    }

    $select = new Zend_Db_Select($db);
		$select
					->from('engine4_core_modules')
					->where('name = ?', 'sitepagedocument')
					->where('version < ?', '4.2.1');
		$is_enabled_document = $select->query()->fetchObject();
      
		if($is_enabled_document) {
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
				$db->update('engine4_sitepage_admincontent', array(
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
				$db->update('engine4_sitepage_content', array(
					'name' => 'sitepagedocument.highlightlist-sitepagedocuments',
					'params' => '{"title":"Highlighted Documents","titleCount":true}',
							), array(
					'name = ?' => 'sitepagedocument.featurelist-sitepagedocuments',
				));
			}

			$featuredColumn = $db->query("SHOW COLUMNS FROM engine4_sitepagedocument_documents LIKE 'featured'")->fetch();
      $highlightedColumn = $db->query("SHOW COLUMNS FROM engine4_sitepagedocument_documents LIKE 'highlighted'")->fetch();
			if (!empty($featuredColumn) && empty($highlightedColumn)) {
				$db->query("ALTER TABLE `engine4_sitepagedocument_documents` CHANGE `featured` `highlighted` TINYINT( 1 ) NOT NULL");
			}

			$featuredColumn = $db->query("SHOW COLUMNS FROM engine4_sitepagedocument_documents LIKE 'featured'")->fetch();
			if (empty($featuredColumn)) {
				$db->query("ALTER TABLE `engine4_sitepagedocument_documents` ADD `featured` TINYINT( 1 ) NOT NULL AFTER `highlighted`");
			}
      $db->update('engine4_core_pages', array('displayname' => 'Browse Page Documents'), array('displayname = ?' => 'Page Documents')); 
		}

		//START SOCIAL SHARE WIDGET WORK
		//CHECK PLUGIN VERSION
    $select = new Zend_Db_Select($db);
		$select
					->from('engine4_core_modules')
					->where('name = ?', 'sitepagedocument')
					->where('version < ?', '4.2.1');
		$is_enabled_module = $select->query()->fetchObject();

		if(!empty($is_enabled_module)) {

			$social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitepagedocument.socialshare-sitepagedocuments"}';

	  	$db->update('engine4_core_content', array('params' => $social_share_default_code,), array('name =?' => 'sitepagedocument.socialshare-sitepagedocuments'));
		}
		//END SOCIAL SHARE WIDGET WORK
$select = new Zend_Db_Select($db);
    $select
          ->from('engine4_core_settings')
          ->where('name = ?', 'sitepage.feed.type');
  $info = $select->query()->fetch();
    $enable = 1;
    if (!empty($info))
      $enable = $info['value'];
    $db->query('INSERT IGNORE INTO   `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_object_thumb`) VALUES("sitepagedocument_admin_new", "sitepagedocument", "{item:$object} created a new document:", '.$enable.', 6, 2, 1, 1, 0, 1)');
    
    $categoryIdColumn = $db->query("SHOW COLUMNS FROM engine4_sitepagedocument_documents LIKE 'category_id'")->fetch();
    if (empty($categoryIdColumn)) {
      $db->query("ALTER TABLE `engine4_sitepagedocument_documents` ADD `category_id` INT( 11 )  NOT NULL DEFAULT '0';");
    }
    
    $categoryTableExist = $db->query("SHOW TABLES LIKE 'engine4_sitepagedocument_categories'")->fetch();
    if(empty($categoryTableExist)) {
      $db->query("
        CREATE TABLE IF NOT EXISTS `engine4_sitepagedocument_categories` (
          `category_id` int(11) unsigned NOT NULL auto_increment,
          `title` varchar(64) NOT NULL,
          PRIMARY KEY  (`category_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
      ");
      
      $db->query("
        INSERT IGNORE INTO `engine4_sitepagedocument_categories` (`title`) VALUES
        ('Arts'),
        ('Business'),
        ('Conferences'),
        ('Festivals'),
        ('Food'),
        ('Fundraisers'),
        ('Galleries'),
        ('Health'),
        ('Just For Fun'),
        ('Kids'),
        ('Learning'),
        ('Literary'),
        ('Movies'),
        ('Museums'),
        ('Neighborhood'),
        ('Networking'),
        ('Nightlife'),
        ('On Campus'),
        ('Organizations'),
        ('Outdoors'),
        ('Pets'),
        ('Politics'),
        ('Sales'),
        ('Science'),
        ('Spirituality'),
        ('Sports'),
        ('Technology'),
        ('Theatre'),
        ('Other');
      ");
    }    
    
    $select = new Zend_Db_Select($db);
    $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'sitepagedocument')
                ->where('enabled = ?', 1);    
    $is_sitepagedocument_object = $select->query()->fetchObject();
    if($is_sitepagedocument_object) {
        $db->query('
          INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
          ("sitepagedocument_admin_main_categories", "sitepagedocument", "Categories", "", \'{"route":"admin_default","module":"sitepagedocument","controller":"settings","action":"categories"}\', "sitepagedocument_admin_main", "", 3);
        ');    
    }
    

		$select = new Zend_Db_Select($db);
		$select
					->from('engine4_core_modules')
					->where('name = ?', 'sitemobile')
					->where('enabled = ?', 1);
		$is_sitemobile_object = $select->query()->fetchObject();
		if($is_sitemobile_object)  {
				include APPLICATION_PATH . "/application/modules/Sitepagedocument/controllers/license/mobileLayoutCreation.php";
		}
    
    $documentTable = $db->query('SHOW TABLES LIKE \'engine4_sitepagedocument_documents\'')->fetch();
    if(!empty($documentTable)) {
        $featuredIndex = $db->query("SHOW INDEX FROM `engine4_sitepagedocument_documents` WHERE Key_name = 'featured'")->fetch();   
        if(empty($featuredIndex)) {
          $db->query("ALTER TABLE `engine4_sitepagedocument_documents` ADD INDEX ( `featured` )");
        }   

        $highlightedIndex = $db->query("SHOW INDEX FROM `engine4_sitepagedocument_documents` WHERE Key_name = 'highlighted'")->fetch();     
        if(empty($highlightedIndex)) {
          $db->query("ALTER TABLE `engine4_sitepagedocument_documents` ADD INDEX ( `highlighted` )");
        }         

        $categoryIdIndex = $db->query("SHOW INDEX FROM `engine4_sitepagedocument_documents` WHERE Key_name = 'category_id'")->fetch();     
        if(empty($categoryIdIndex)) {
          $db->query("ALTER TABLE `engine4_sitepagedocument_documents` ADD INDEX ( `category_id` )");
        }      

        $searchIndex = $db->query("SHOW INDEX FROM `engine4_sitepagedocument_documents` WHERE Key_name = 'search'")->fetch();
        if(!empty($searchIndex)) {
            $db->query("ALTER TABLE `engine4_sitepagedocument_documents` DROP INDEX search");
            $db->query("ALTER TABLE `engine4_sitepagedocument_documents` ADD INDEX (`draft`, `search`, `status`, `approved`)");
        }
    }

    parent::onInstall();
  }

  public function onPostInstall() {

    $db = $this->getDb();
		$select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitemobile')
            ->where('enabled = ?', 1);
    $is_sitemobile_object = $select->query()->fetchObject();
    if(!empty($is_sitemobile_object)) {
			$db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`) VALUES
('sitepagedocument','1')");
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_sitemobile_modules')
							->where('name = ?', 'sitepagedocument')
							->where('integrated = ?', 0);
			$is_sitemobile_object = $select->query()->fetchObject();
      if($is_sitemobile_object)  {
				$actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
				$controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
				if($controllerName == 'manage' && $actionName == 'install') {
          $view = new Zend_View();
					$baseUrl = ( !empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://':'http://') .  $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
					$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
					$redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/sitepagedocument/integrated/0/redirect/install');
				} 
      }
    } else {
			//Work for the word changes in the page plugin .csv file.
			$actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
			$controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
			if($controllerName == 'manage' && ($actionName == 'install' || $actionName == 'query')) {
				$view = new Zend_View();
				$baseUrl = ( !empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://':'http://') .  $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
				$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
				if ($actionName == 'install') {
					$redirector->gotoUrl($baseUrl . 'admin/sitepage/settings/language/redirect/install');
				} else {
					$redirector->gotoUrl($baseUrl . 'admin/sitepage/settings/language/redirect/query');
				}
			}
    }
  }

}

?>