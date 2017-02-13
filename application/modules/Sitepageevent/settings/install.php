<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 6590 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_Installer extends Engine_Package_Installer_Module {

  function onPreInstall() {
    $this->_runCustomQueries();
    
    $db = $this->getDb();
    
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
      $PRODUCT_TYPE = 'sitepageevent';
      $PLUGIN_TITLE = 'Sitepageevent';
      $PLUGIN_VERSION = '4.8.0';
      $PLUGIN_CATEGORY = 'plugin';
      $PRODUCT_DESCRIPTION = 'Sitepageevent Plugin';
      $PRODUCT_TITLE = 'Directory / Pages - Events Extension';
      $_PRODUCT_FINAL_FILE = 0;
      $sitepage_plugin_version = '4.8.0';
      $SocialEngineAddOns_version = '4.8.0';
      $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
      $is_file = file_exists($file_path);
      if (empty($is_file)) {
        include APPLICATION_PATH . "/application/modules/Sitepage/controllers/license/license4.php";
      } else {
        include $file_path;
      }

      $pageTime = time();
      $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
			('sitepageevent.basetime', $pageTime ),
			('sitepageevent.isvar', 0 ),
			('sitepageevent.filepath', 'Sitepageevent/controllers/license/license2.php');");

//PUT SITEPAGE EVNET WIDGET AT ADMIN CONTENT TABLE
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_modules')
              ->where('name = ?', 'sitepageevent')
              ->where('version <= ?', '4.1.5p1');
      $is_enabled = $select->query()->fetchObject();
      if (!empty($is_enabled)) {
        $select = new Zend_Db_Select($db);
        $select_page = $select
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'sitepage_index_view')
                ->limit(1);
        $page = $select_page->query()->fetchAll();
        if (!empty($page)) {
          $page_id = $page[0]['page_id'];
          $select = new Zend_Db_Select($db);
          $select_content = $select
                  ->from('engine4_sitepage_admincontent')
                  ->where('page_id = ?', $page_id)
                  ->where('type = ?', 'widget')
                  ->where('name = ?', 'sitepageevent.profile-sitepageevents')
                  ->limit(1);
          $content = $select_content->query()->fetchAll();
          if (empty($content)) {
            $select = new Zend_Db_Select($db);
            $select_container = $select
                    ->from('engine4_sitepage_admincontent', 'admincontent_id')
                    ->where('page_id = ?', $page_id)
                    ->where('type = ?', 'container')
                    ->limit(1);
            $container = $select_container->query()->fetchAll();
            if (!empty($container)) {
              $container_id = $container[0]['admincontent_id'];
              $select = new Zend_Db_Select($db);
              $select_middle = $select
                      ->from('engine4_sitepage_admincontent')
                      ->where('parent_content_id = ?', $container_id)
                      ->where('type = ?', 'container')
                      ->where('name = ?', 'middle')
                      ->limit(1);
              $middle = $select_middle->query()->fetchAll();
              if (!empty($middle)) {
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
                if (!empty($tab)) {
                  $tab_id = $tab[0]['admincontent_id'];
                } else {
                  $tab_id = $middle_id;
                }

                $db->insert('engine4_sitepage_admincontent', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitepageevent.profile-sitepageevents',
                    'parent_content_id' => $tab_id,
                    'order' => 117,
                    'params' => '{"title":"Events","titleCount":"true"}',
                ));
              }
            }
          }
        }
      }

//PUT SITEPAGE EVNET WIDGET AT ADMIN CONTENT TABLE
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_modules')
              ->where('name = ?', 'sitepageevent')
              ->where('version <= ?', '4.1.6');
      $is_enabled = $select->query()->fetchObject();
      if (!empty($is_enabled)) {
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'mobi')
                ->where('enabled 	 = ?', 1)
                ->limit(1);
        $infomation = $select->query()->fetch();
        if (!empty($infomation)) {
          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_pages')
                  ->where('name = ?', 'sitepageevent_mobi_view')
                  ->limit(1);
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
                'name' => 'activity.feed',
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
      }
      parent::onPreInstall();
    } elseif (!empty($check_sitepage) && empty($sitepage_is_active)) {
      $baseUrl = $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl();
      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      if (strstr($url_string, "manage/install")) {
        $calling_from = 'install';
      } else if (strstr($url_string, "manage/query")) {
        $calling_from = 'queary';
      }
      $explode_base_url = explode("/", $baseUrl);
      foreach ($explode_base_url as $url_key) {
        if ($url_key != 'install') {
          $core_final_url .= $url_key . '/';
        }
      }

      return $this->_error("<span style='color:red'>Note: You have installed the <a href='http://www.socialengineaddons.com/socialengine-directory-pages-plugin' target='_blank'>Directory / Pages Plugin</a> but not activated it on your site yet. Please activate it first before installing the Directory / Pages - Events Extension.</span><br/> <a href='" . 'http://' . $core_final_url . "admin/sitepage/settings/readme'>Click here</a> to activate the Directory / Pages Plugin.");
    }
    else {
      $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
      return $this->_error("<span style='color:red'>Note: You have not installed the <a href='http://www.socialengineaddons.com/socialengine-directory-pages-plugin' target='_blank'>Directory / Pages Plugin</a> on your site yet. Please install it first before installing the <a href='http://www.socialengineaddons.com/pageextensions/socialengine-directory-pages-events' target='_blank'>Directory / Pages - Events Extension</a>.</span><br/> <a href='" . $base_url . "/manage'>Click here</a> to go Manage Packages.");
    }
  }

  function onInstall() {

    $db = $this->getDb();
    
    $db->query('UPDATE  `engine4_activity_notificationtypes` SET  `body` =  \'{item:$subject} has created a page event {item:$object}.\' WHERE  `engine4_activity_notificationtypes`.`type` =  "sitepageevent_create";');

    $seao_locationid = $db->query("SHOW COLUMNS FROM engine4_sitepageevent_events LIKE 'seao_locationid'")->fetch();
    if (empty($seao_locationid)) {
      $db->query("ALTER TABLE `engine4_sitepageevent_events` ADD `seao_locationid` INT( 11 ) NOT NULL ");
    }

//START THE WORK FOR MAKE WIDGETIZE PAGE OF Locatio or map.
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_pages')
            ->where('name = ?', 'sitepageevent_index_by-locations')
            ->limit(1);
    $info = $select->query()->fetch();

    if (empty($info)) {
      $db->insert('engine4_core_pages', array(
          'name' => 'sitepageevent_index_by-locations',
          'displayname' => 'Browse Page Events’ Locations',
          'title' => 'Browse Page Events’ Locations',
          'description' => 'Browse Page Events’ Locations',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'top',
          'parent_content_id' => null,
          'order' => 1,
          'params' => '',
      ));
      $top_id = $db->lastInsertId('engine4_core_content');

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

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'middle',
          'parent_content_id' => $top_id,
          'params' => '',
      ));
      $top_middle_id = $db->lastInsertId('engine4_core_content');

//INSERT MAIN - MIDDLE CONTAINER
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'middle',
          'parent_content_id' => $container_id,
          'order' => 2,
          'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');

// Top Middle
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.browsenevigation-sitepage',
          'parent_content_id' => $top_middle_id,
          'order' => 1,
          'params' => '',
      ));

//INSERT WIDGET OF LOCATION SEARCH AND CORE CONTENT
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepageevent.location-search',
          'parent_content_id' => $middle_id,
          'order' => 2,
          'params' => '',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepageevent.bylocation-event',
          'parent_content_id' => $middle_id,
          'order' => 3,
          'params' => '{"title":"","titleCount":true,"order_by":"2","nomobile":"0"}',
      ));
    }
//END THE WORK FOR MAKE WIDGETIZE PAGE OF LOCATIO OR MAP.
//START THE WORK FOR MAKE WIDGETIZE PAGE OF Locatio or map.
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_pages')
            ->where('name = ?', 'sitepageevent_index_mobileby-locations')
            ->limit(1);
    $info = $select->query()->fetch();

    if (empty($info)) {
      $db->insert('engine4_core_pages', array(
          'name' => 'sitepageevent_index_mobileby-locations',
          'displayname' => 'Mobile Page Browse Events’ Locations',
          'title' => 'Mobile Page Browse Events’ Locations',
          'description' => 'Mobile Page Browse Events’ Locations',
          'custom' => 0,
      ));
      $page_id = $db->lastInsertId('engine4_core_pages');

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'top',
          'parent_content_id' => null,
          'order' => 1,
          'params' => '',
      ));
      $top_id = $db->lastInsertId('engine4_core_content');

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

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'middle',
          'parent_content_id' => $top_id,
          'params' => '',
      ));
      $top_middle_id = $db->lastInsertId('engine4_core_content');

//INSERT MAIN - MIDDLE CONTAINER
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'container',
          'name' => 'middle',
          'parent_content_id' => $container_id,
          'order' => 2,
          'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');

// Top Middle
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.browsenevigation-sitepage',
          'parent_content_id' => $top_middle_id,
          'order' => 1,
          'params' => '',
      ));

//INSERT WIDGET OF LOCATION SEARCH AND CORE CONTENT
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepageevent.location-search',
          'parent_content_id' => $middle_id,
          'order' => 2,
          'params' => '',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepageevent.bylocation-event',
          'parent_content_id' => $middle_id,
          'order' => 3,
          'params' => '{"title":"","titleCount":true,"order_by":"2","nomobile":"0"}',
      ));
    }
//END THE WORK FOR MAKE WIDGETIZE PAGE OF LOCATIO OR MAP.
// START WORK FOR SINK LOCATION WITH NEW LOCATION TABLE.
    $table_exist = $db->query('SHOW TABLES LIKE \'engine4_sitepageevent_events\'')->fetch();
    if (!empty($table_exist)) {
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_sitepageevent_events', array('event_id', 'seao_locationid', 'location'))
              ->where('location <> ?', '')
              ->where('seao_locationid <> ?', 0);
      $result = $select->query()->fetchAll();

      foreach ($result as $results) {

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_seaocore_locations')
                ->where('location_id = ?', $results['seao_locationid']);
        $resultlocations = $select->query()->fetchAll();

        foreach ($resultlocations as $resultlocation) {
					$resultlocation['location'] = str_replace("'", "\'", $resultlocation['location']);
					$resultlocation['formatted_address'] = str_replace("'", "\'", $resultlocation['formatted_address']);
					$resultlocation['country'] = str_replace("'", "\'", $resultlocation['country']);
					$resultlocation['state'] = str_replace("'", "\'", $resultlocation['state']);
					$resultlocation['city'] = str_replace("'", "\'", $resultlocation['city']);
					$resultlocation['address'] = str_replace("'", "\'", $resultlocation['address']);
					
					$db->query("INSERT IGNORE INTO `engine4_seaocore_locationitems` (`resource_type`, `resource_id`, `location`, `latitude`, `longitude`, `formatted_address`, `country`, `state`, `zipcode`, `city`, `address`, `zoom`) VALUES ('sitepageevent_event', '" . $results['event_id']. "', '" . $resultlocation['location']. "', '" . $resultlocation['latitude']. "', '" . $resultlocation['longitude']. "', '" . $resultlocation['formatted_address']. "', '" . $resultlocation['country']. "', '" . $resultlocation['state']. "', '" . $resultlocation['zipcode']. "', '" . $resultlocation['city']. "', '" . $resultlocation['address']. "', '" . $resultlocation['zoom']. "')");
					
          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_seaocore_locationitems', array('locationitem_id'))
                  ->where('resource_type = ?', 'sitepageevent_event')
                  ->where('resource_id = ?', $results['event_id']);
          $info = $select->query()->fetch();

          $db->query("UPDATE `engine4_sitepageevent_events` SET `seao_locationid` = '" . $info['locationitem_id'] . "' WHERE `engine4_sitepageevent_events`.`event_id` = '" . $results['event_id'] . "';");
        }
      }
    }
//END WORK FOR SINK LOCATION WITH NEW LOCATION TABLE.

    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepageevent_events'")->fetch();
    if (!empty($table_exist)) {
//DROP THE COLUMN FROM THE "engine4_sitepageevent_events" TABLE
      $pageOwnerIdColumn = $db->query("SHOW COLUMNS FROM engine4_sitepageevent_events LIKE 'page_owner_id'")->fetch();
      if (!empty($pageOwnerIdColumn)) {
        $db->query("ALTER TABLE `engine4_sitepageevent_events` DROP `page_owner_id`");
      }

//DROP THE INDEX FROM THE "engine4_sitepageevent_events" TABLE
      $parentTypeColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepageevent_events` WHERE Key_name = 'parent_type'")->fetch();

      if (!empty($creationDateColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepageevent_events` DROP INDEX `parent_type`");
      }

//ADD THE INDEX FROM THE "engine4_sitepageevent_events" TABLE
      $pageIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepageevent_events` WHERE Key_name = 'page_id'")->fetch();

      if (empty($pageIdColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepageevent_events` ADD INDEX ( `page_id` );");
      }
    }

    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepageevent_photos'")->fetch();
    if (!empty($table_exist)) {
//DROP THE INDEX FROM THE "engine4_sitepageevent_photos" TABLE
      $noteIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepageevent_photos` WHERE Key_name = 'sitepagenote_id'")->fetch();

      if (!empty($noteIdColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepageevent_photos` DROP INDEX `sitepagenote_id`");
      }

//ADD THE INDEX FROM THE "engine4_sitepageevent_photos" TABLE
      $eventIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepageevent_photos` WHERE Key_name = 'event_id'")->fetch();

      if (empty($eventIdColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepageevent_photos` ADD INDEX ( `event_id` );");
      }
    }

    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepageevent_albums'")->fetch();
    if (!empty($table_exist)) {
//DROP THE INDEX FROM THE "engine4_sitepageevent_albums" TABLE
      $noteIdAlbumsColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepageevent_albums` WHERE Key_name = 'sitepagenote_id'")->fetch();

      if (!empty($noteIdAlbumsColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepageevent_albums` DROP INDEX `sitepagenote_id`");
      }

//ADD THE INDEX FROM THE "engine4_sitepageevent_albums" TABLE
      $eventIdAlbumsColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepageevent_albums` WHERE Key_name = 'event_id'")->fetch();

      if (empty($eventIdAlbumsColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepageevent_albums` ADD INDEX ( `event_id` );");
      }
    }

    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepageevent_membership'")->fetch();
    if (!empty($table_exist)) {
//ADD THE INDEX FROM THE "engine4_sitepageevent_membership" TABLE
      $membershipColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepageevent_membership` WHERE Key_name = 'REVERSE'")->fetch();

      if (!empty($membershipColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepageevent_membership` DROP INDEX `REVERSE`");
      }

//ADD THE INDEX FROM THE "engine4_sitepageevent_membership" TABLE
      $userIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepageevent_membership` WHERE Key_name = 'user_id'")->fetch();

      if (empty($userIdColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepageevent_membership` ADD INDEX ( `user_id` );");
      }
    }

//REMOVED WIDGET SETTING TAB FROM ADMIN PANEL
    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_modules')
            ->where('name = ?', 'sitepageevent')
            ->where('version <= ?', '4.1.7p2');
    $is_enabled = $select->query()->fetchObject();
    if (!empty($is_enabled)) {
      $widget_names = array('event', 'upcomingevents');

      foreach ($widget_names as $widget_name) {

        $widget_type = $widget_name;
        if ($widget_type == 'event') {
          $widget_name = 'sitepageevent.' . profile . '-events';
        }
        if ($widget_type == 'upcomingevents') {
          $widget_name = 'sitepageevent.' . upcoming . '-sitepageevent';
        }

        $setting_name = 'sitepageevent.' . $widget_type . '.widgets';
        $total_items = $db->select()
                ->from('engine4_core_settings', array('value'))
                ->where('name = ?', $setting_name)
                ->limit(1)
                ->query()
                ->fetchColumn();

        if (empty($total_items)) {
          $total_items = 3;
        }

//WORK FOR CORE CONTENT PAGES
        $select = new Zend_Db_Select($db);
        $select->from('engine4_core_content', array('name', 'params', 'content_id'))->where('name = ?', $widget_name);
        $widgets = $select->query()->fetchAll();
        foreach ($widgets as $widget) {
          $explode_params = explode('}', $widget['params']);
          if (!empty($explode_params[0]) && !strstr($explode_params[0], '"itemCount"')) {
            $params = $explode_params[0] . ',"itemCount":"' . $total_items . '"}';

            $db->update('engine4_core_content', array('params' => $params), array('content_id = ?' => $widget['content_id'], 'name = ?' => $widget_name));
          }
        }

//WORK FOR ADMIN USER CONTENT PAGE
        $select = new Zend_Db_Select($db);
        $select->from('engine4_sitepage_admincontent', array('name', 'params', 'admincontent_id'))->where('name = ?', $widget_name);
        $widgets = $select->query()->fetchAll();
        foreach ($widgets as $widget) {
          $explode_params = explode('}', $widget['params']);
          if (!empty($explode_params[0]) && !strstr($explode_params[0], '"itemCount"')) {
            $params = $explode_params[0] . ',"itemCount":"' . $total_items . '"}';

            $db->update('engine4_sitepage_admincontent', array('params' => $params), array('admincontent_id = ?' => $widget['admincontent_id'], 'name = ?' => $widget_name));
          }
        }

//WORK FOR USER CONTENT PAGES
        $select = new Zend_Db_Select($db);
        $select->from('engine4_sitepage_content', array('name', 'params', 'content_id'))->where('name = ?', $widget_name);
        $widgets = $select->query()->fetchAll();
        foreach ($widgets as $widget) {
          $explode_params = explode('}', $widget['params']);
          if (!empty($explode_params[0]) && !strstr($explode_params[0], '"itemCount"')) {
            $params = $explode_params[0] . ',"itemCount":"' . $total_items . '"}';

            $db->update('engine4_sitepage_content', array('params' => $params), array('content_id = ?' => $widget['content_id'], 'name = ?' => $widget_name));
          }
        }
      }
    }

//START THE WORK FOR MAKE WIDGETIZE PAGE OF EVENTS LISTING
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepageevent')
            ->where('version < ?', '4.2.0');
    $is_enabled = $select->query()->fetchObject();
    if (!empty($is_enabled)) {
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
              ->where('name = ?', 'sitepageevent_index_eventlist')
              ->limit(1);
      ;
      $info = $select->query()->fetch();

      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_pages')
              ->where('name = ?', 'sitepageevent_index_browse')
              ->limit(1);
      ;
      $info_browse = $select->query()->fetch();

      if (empty($info) && empty($info_browse)) {
        $db->insert('engine4_core_pages', array(
            'name' => 'sitepageevent_index_browse',
            'displayname' => 'Browse Page Events',
            'title' => 'Page Events',
            'description' => 'This is the page events.',
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
            'name' => 'sitepageevent.search-sitepageevent',
            'parent_content_id' => $right_id,
            'order' => 3,
            'params' => '{"title":"","titleCount":"true"}',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitepageevent.sitepage-event',
            'parent_content_id' => $middle_id,
            'order' => 2,
            'params' => '{"title":"","titleCount":""}',
        ));


        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitepageevent.sitepage-sponsoredevent',
            'parent_content_id' => $right_id,
            'order' => 4,
            'params' => '{"title":"Sponsored Events","titleCount":"true"}',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitepageevent.upcoming-sitepageevent',
            'parent_content_id' => $right_id,
            'order' => 5,
            'params' => '{"title":"Upcoming Events","titleCount":"true"}',
        ));

        if ($infomation && $rowinfo) {
          $db->insert('engine4_core_content', array(
              'page_id' => $page_id,
              'type' => 'widget',
              'name' => 'sitepage.page-ads',
              'parent_content_id' => $right_id,
              'order' => 6,
              'params' => '{"title":"","titleCount":""}',
          ));
        }
      } else {
        $db->update('engine4_core_pages', array('name' => 'sitepageevent_index_browse'), array('name = ?' => 'sitepageevent_index_eventlist'));
      }
    }

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepageevent')
            ->where('version < ?', '4.2.1');
    $is_enabled = $select->query()->fetchObject();
    if ($is_enabled) {
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
// Middle
        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitepageevent.homeview-sitepageevents',
            'parent_content_id' => $right_id,
            'order' => 21,
            'params' => '{"title":"Most Viewed Events","titleCount":"true"}',
        ));

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
      $featuredColumn = $db->query("SHOW COLUMNS FROM engine4_sitepageevent_events LIKE 'featured'")->fetch();
      if (empty($featuredColumn)) {
        $db->query("ALTER TABLE `engine4_sitepageevent_events` ADD `featured` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `photo_id`");
      }
      $showColumn = $db->query("SHOW COLUMNS FROM engine4_seaocore_tabs LIKE 'show'")->fetch();
      if (empty($showColumn)) {
        $db->query("ALTER TABLE `engine4_seaocore_tabs` ADD `show` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `limit`");
      }
      $db->update('engine4_core_pages', array('displayname' => 'Browse Page Events'), array('displayname = ?' => 'Page Events'));
    }

//END THE WORK FOR MAKE WIDGETIZE PAGE OF EVNETS LISTING
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'sitepage.feed.type');
    $info = $select->query()->fetch();
    $enable = 1;
    if (!empty($info))
      $enable = $info['value'];
    $db->query('INSERT IGNORE INTO   `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_object_thumb`) VALUES("sitepageevent_admin_new", "sitepageevent", "{item:$object} created a new event:", ' . $enable . ', 6, 2, 1, 1, 1, 1)');

    $this->_addEventProfileContentPhotoAndDiscussions();

		$categoryIdColumn = $db->query("SHOW COLUMNS FROM engine4_sitepageevent_events LIKE 'category_id'")->fetch();
		if (empty($categoryIdColumn)) {
			$db->query("ALTER TABLE `engine4_sitepageevent_events` ADD `category_id` INT( 11 )  NOT NULL DEFAULT '0';");
		}

		$select = new Zend_Db_Select($db);
		$select
					->from('engine4_core_modules')
					->where('name = ?', 'sitemobile')
					->where('enabled = ?', 1);
		$is_sitemobile_object = $select->query()->fetchObject();
		if($is_sitemobile_object)  {
				include APPLICATION_PATH . "/application/modules/Sitepageevent/controllers/license/mobileLayoutCreation.php";
		}
    
    $eventTable = $db->query('SHOW TABLES LIKE \'engine4_sitepageevent_events\'')->fetch();
    if(!empty($eventTable)) {
        
        $featuredColumn = $db->query("SHOW COLUMNS FROM engine4_sitepageevent_events LIKE 'featured'")->fetch();
        if(!empty($featuredColumn)) {
            $featuredIndex = $db->query("SHOW INDEX FROM `engine4_sitepageevent_events` WHERE Key_name = 'featured'")->fetch();   
            if(empty($featuredIndex)) {
              $db->query("ALTER TABLE `engine4_sitepageevent_events` ADD INDEX ( `featured` )");
            }
        }

        $categoryColumn = $db->query("SHOW COLUMNS FROM engine4_sitepageevent_events LIKE 'category_id'")->fetch();
        if(!empty($categoryColumn)) {
            $categoryIdIndex = $db->query("SHOW INDEX FROM `engine4_sitepageevent_events` WHERE Key_name = 'category_id'")->fetch();     
            if(empty($categoryIdIndex)) {
              $db->query("ALTER TABLE `engine4_sitepageevent_events` ADD INDEX ( `category_id` )");
            }      
        }
    }

    parent::onInstall();
  }
  
  protected function _runCustomQueries() {
    $db = $this->getDb();
    
    try {
      $params_json = $db->select()
              ->from('engine4_core_content', 'params')->where('name = ?', 'sitepageevent.profile-sitepageevents')->query()->fetchColumn();
      $params = json_decode($params_json, true);
      $params['title'] = 'Calendar';
      $updated_params_json = json_encode($params);
      $db->query("UPDATE engine4_core_content SET params = '{$updated_params_json}' WHERE name = 'sitepageevent.profile-sitepageevents'");
      
      $this->_insertSiteSpecificData();
    } catch(Exception $e) {
      echo $e->getMessage();
      exit;
    }
  }
  
  protected function _insertSiteSpecificData() {
    $db = $this->getDb();

    $path = $this->_operation->getPrimaryPackage()->getBasePath() . '/'
            . $this->_operation->getPrimaryPackage()->getPath() . '/'
            . 'settings' . '/'
            . 'mgsl';

    $files = array(
        'event_categories.sql' => function() {
          $db = $this->getDb();
      
          $sportCategories = $db->select()
                  ->from('engine4_sitepageevent_categories')
                  ->where("title LIKE ?", '%Rugby%')
                  ->limit(1)
                  ->query()
                  ->fetchObject();
          
          return empty($sportCategories);
        },
    );
    
    $db->beginTransaction();

    foreach ($files as $file => $callback) {
      if (call_user_func($callback)) {
        $contents = file_get_contents($path . '/' . $file);
        foreach (Engine_Package_Utilities::sqlSplit($contents) as $sqlFragment) {
          try {
            $db->query($sqlFragment);
          } catch (Exception $e) {
            return $this->_error('Query failed with error: ' . $e->getMessage());
          }
        }
      }
    }
    
    $db->commit();
  }

  protected function _addEventProfileContentPhotoAndDiscussions() {
//
// install content areas
//
    $db = $this->getDb();
    $select = new Zend_Db_Select($db);

// profile page
    $select
            ->from('engine4_core_pages')
            ->where('name = ?', 'sitepageevent_index_view')
            ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;

    if (empty($page_id))
      return;
// classified.profile-classifieds
// Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_content')
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'sitepageevent.profile-photos')
    ;
    $info = $select->query()->fetch();
    if (empty($info)) {

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
      if ($tab_id && @$tab_id->content_id) {
        $tab_id = $tab_id->content_id;
      } else {
        $tab_id = null;
      }

// tab on profile
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepageevent.profile-photos',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 5,
          'params' => '{"title":"Photos","titleCount":true}',
      ));
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.profile-discussions',
          'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
          'order' => 9,
          'params' => '{"title":"Discussions","titleCount":true}',
      ));
    }
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
('sitepageevent','1')");
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_sitemobile_modules')
							->where('name = ?', 'sitepageevent')
							->where('integrated = ?', 0);
			$is_sitemobile_object = $select->query()->fetchObject();
      if($is_sitemobile_object)  {
				$actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
				$controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
				if($controllerName == 'manage' && $actionName == 'install') {
          $view = new Zend_View();
					$baseUrl = ( !empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://':'http://') .  $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
					$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
					$redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/sitepageevent/integrated/0/redirect/install');
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