<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagepoll_Installer extends Engine_Package_Installer_Module {

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
      $PRODUCT_TYPE = 'sitepagepoll';
      $PLUGIN_TITLE = 'Sitepagepoll';
      $PLUGIN_VERSION = '4.7.0';
      $PLUGIN_CATEGORY = 'plugin';
      $PRODUCT_DESCRIPTION = 'Sitepagepoll Plugin';
      $PRODUCT_TITLE = 'Directory / Pages - Polls Extension';
      $_PRODUCT_FINAL_FILE = 0;
      $sitepage_plugin_version = '4.7.0';
      $SocialEngineAddOns_version = '4.7.0';
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
			('sitepagepoll.basetime', $pageTime ),
			('sitepagepoll.isvar', 0 ),
			('sitepagepoll.filepath', 'Sitepagepoll/controllers/license/license2.php');");

      //PUT SITEPAGE POLL WIDGET IN ADMIN CONTENT TABLE
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_modules')
              ->where('name = ?', 'sitepagepoll')
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
                          ->where('name = ?', 'sitepagepoll.profile-sitepagepolls')
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
                    'name' => 'sitepagepoll.profile-sitepagepolls',
                    'parent_content_id' => $tab_id,
                    'order' => 118,
                    'params' => '{"title":"Polls","titleCount":"true"}',
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
      return $this->_error("<span style='color:red'>Note: You have installed the <a href='http://www.socialengineaddons.com/socialengine-directory-pages-plugin' target='_blank'>Directory / Pages Plugin</a> but not activated it on your site yet. Please activate it first before installing the Directory / Pages - Polls Extension.</span><br/> <a href='" . 'http://' . $core_final_url . "admin/sitepage/settings/readme'>Click here</a> to activate the Directory / Pages Plugin.");
    }
    else {
      $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
      return $this->_error("<span style='color:red'>Note: You have not installed the <a href='http://www.socialengineaddons.com/socialengine-directory-pages-plugin' target='_blank'>Directory / Pages Plugin</a> on your site yet. Please install it first before installing the <a href='http://www.socialengineaddons.com/pageextensions/socialengine-directory-pages-polls' target='_blank'>Directory / Pages - Polls Extension</a>.</span><br/> <a href='" . $base_url . "/manage'>Click here</a> to go Manage Packages.");
    }
  }

  function onInstall() {

    $db = $this->getDb();
    
    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepagepoll_polls'")->fetch();
    if (!empty($table_exist)) {
      //DROP THE COLUMN FROM THE "engine4_sitepagepoll_polls" TABLE
      $pageOwnerIdColumn = $db->query("SHOW COLUMNS FROM engine4_sitepagepoll_polls LIKE 'page_owner_id'")->fetch();
      if(!empty($pageOwnerIdColumn)) {
        $db->query("ALTER TABLE `engine4_sitepagepoll_polls` DROP `page_owner_id`");
      }

      //DROP THE COLUMN FROM THE "engine4_sitepagepoll_polls" TABLE
      $isClosedColumn = $db->query("SHOW COLUMNS FROM engine4_sitepagepoll_polls LIKE 'is_closed'")->fetch();
      if(!empty($isClosedColumn)) {
        $db->query("ALTER TABLE `engine4_sitepagepoll_polls` DROP `is_closed`");
      }

      //DROP THE INDEX FROM THE "engine4_sitepagepoll_polls" TABLE        
      $parentTypeColumn = $db->query("SHOW INDEX FROM `engine4_sitepagepoll_polls` WHERE Key_name = 'parent_type'")->fetch();
      if(!empty($parentTypeColumn)) {
        $db->query("ALTER TABLE `engine4_sitepagepoll_polls` DROP INDEX `parent_type`");
      }

      //DROP THE INDEX FROM THE "engine4_sitepagepoll_polls" TABLE
      $creationDateColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepagepoll_polls` WHERE Key_name = 'creation_date'")->fetch();

      if( !empty($creationDateColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepagepoll_polls` DROP INDEX `creation_date`");
      }

      //ADD THE INDEX FROM THE "engine4_sitepagepoll_polls" TABLE
      $pageIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepagepoll_polls` WHERE Key_name = 'page_id'")->fetch();

      if( empty($pageIdColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepagepoll_polls` ADD INDEX ( `page_id` );");
      }   

      //ADD THE COLUMN FROM THE "engine4_sitepagepoll_polls" TABLE
      $closedColumn = $db->query("SHOW COLUMNS FROM engine4_sitepagepoll_polls LIKE 'closed'")->fetch();

      if(empty($closedColumn)) {
        $db->query("ALTER TABLE `engine4_sitepagepoll_polls` ADD `closed` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `approved`;");
      }    
    }
    
    //REMOVED WIDGET SETTING TAB FROM ADMIN PANEL
    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_modules')
            ->where('name = ?', 'sitepagepoll')
            ->where('version <= ?', '4.1.7p2');
    $is_enabled = $select->query()->fetchObject();
    if ( !empty($is_enabled) ) {
      $widget_names = array('comment', 'view', 'recent', 'vote', 'like');
      foreach ( $widget_names as $widget_name ) {
        $widget_type = $widget_name;
        $widget_name = 'sitepagepoll.' . $widget_name . '-sitepagepolls';
        $setting_name = 'sitepagepoll.' . $widget_type . '.widgets';
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
    
    //START THE WORK FOR MAKE WIDGETIZE PAGE OF POLL LISTING AND POLL VIEW PAGE
    $select = new Zend_Db_Select($db);
		$select
						->from('engine4_core_modules')
						->where('name = ?', 'sitepagepoll')
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
							->where('name = ?', 'sitepagepoll_index_polllist')
							->limit(1);
			;
			$info = $select->query()->fetch();
      $select = new Zend_Db_Select($db);
			$select
							->from('engine4_core_pages')
							->where('name = ?', 'sitepagepoll_index_browse')
							->limit(1);
			;
			$info_browse = $select->query()->fetch();


			if ( empty($info) && empty($info_browse) ) {
				$db->insert('engine4_core_pages', array(
						'name' => 'sitepagepoll_index_browse',
						'displayname' => 'Page Polls',
						'title' => 'Page Polls',
						'description' => 'This is the page polls.',
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
						'name' => 'sitepagepoll.search-sitepagepoll',
						'parent_content_id' => $right_id,
						'order' => 3,
						'params' => '{"title":"","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagepoll.sitepage-poll',
						'parent_content_id' => $middle_id,
						'order' => 2,
						'params' => '{"title":"","titleCount":""}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagepoll.sitepage-sponsoredpoll',
						'parent_content_id' => $right_id,
						'order' => 4,
						'params' => '{"title":"Sponsored Polls","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagepoll.homecomment-sitepagepolls',
						'parent_content_id' => $right_id,
						'order' => 5,
						'params' => '{"title":"Most Commented Polls","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagepoll.homeview-sitepagepolls',
						'parent_content_id' => $right_id,
						'order' => 6,
						'params' => '{"title":"Most Viewed Polls","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagepoll.homelike-sitepagepolls',
						'parent_content_id' => $right_id,
						'order' => 7,
						'params' => '{"title":"Most Liked Polls","titleCount":"true"}',
				));

				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagepoll.homevote-sitepagepolls',
						'parent_content_id' => $right_id,
						'order' => 8,
						'params' => '{"title":"Most Voted Polls","titleCount":"true"}',
				));


				$db->insert('engine4_core_content', array(
						'page_id' => $page_id,
						'type' => 'widget',
						'name' => 'sitepagepoll.homerecent-sitepagepolls',
						'parent_content_id' => $right_id,
						'order' => 9,
						'params' => '{"title":"Recent Polls","titleCount":"true"}',
				));
				
				if ( $infomation && $rowinfo ) {
					$db->insert('engine4_core_content', array(
							'page_id' => $page_id,
							'type' => 'widget',
							'name' => 'sitepage.page-ads',
							'parent_content_id' => $right_id,
							'order' => 10,
							'params' => '{"title":"","titleCount":""}',
					));
				}
			}
      else {
        $db->update('engine4_core_pages', array('name' => 'sitepagepoll_index_browse'), array('name = ?' => 'sitepagepoll_index_polllist'));
      }

			$db = $this->getDb();
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

				if ( $infomation && $rowinfo ) {
					$db->insert('engine4_core_content', array(
							'page_id' => $page_id,
							'type' => 'widget',
							'name' => 'sitepage.page-ads',
							'parent_content_id' => $right_id,
							'order' => 1,
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
    //END THE WORK FOR MAKE WIDGETIZE PAGE OF POLL LISTING AND POLL VIEW PAGE
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'sitepage.feed.type');
    $info = $select->query()->fetch();
    $enable = 1;
    if (!empty($info))
      $enable = $info['value'];
    $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_object_thumb`) VALUE("sitepagepoll_admin_new", "sitepagepoll", "{item:$object} created a new poll:", ' . $enable . ', 6, 2, 1, 1, 1, 1)');

		$select = new Zend_Db_Select($db);
		$select
				->from('engine4_core_modules')
				->where('name = ?', 'sitemobile')
				->where('enabled = ?', 1);
		$is_sitemobile_object = $select->query()->fetchObject();
		if($is_sitemobile_object)  {
			include APPLICATION_PATH . "/application/modules/Sitepagepoll/controllers/license/mobileLayoutCreation.php";
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
('sitepagepoll','1')");
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_sitemobile_modules')
							->where('name = ?', 'sitepagepoll')
							->where('integrated = ?', 0);
			$is_sitemobile_object = $select->query()->fetchObject();
      if($is_sitemobile_object)  {
				$actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
				$controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
				if($controllerName == 'manage' && $actionName == 'install') {
          $view = new Zend_View();
					$baseUrl = ( !empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://':'http://') .  $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
					$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
					$redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/sitepagepoll/integrated/0/redirect/install');
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