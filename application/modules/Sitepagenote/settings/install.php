<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_Installer extends Engine_Package_Installer_Module {

  function onPreInstall() {
    //GET DB
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
      $PRODUCT_TYPE = 'sitepagenote';
      $PLUGIN_TITLE = 'Sitepagenote';
      $PLUGIN_VERSION = '4.7.1';
      $PLUGIN_CATEGORY = 'plugin';
      $PRODUCT_DESCRIPTION = 'Sitepagenote Plugin';
      $_PRODUCT_FINAL_FILE = 0;
      $sitepage_plugin_version = '4.7.1';
      $SocialEngineAddOns_version = '4.7.1';
      $PRODUCT_TITLE = 'Directory / Pages - Notes Extension';
      $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
      $is_file = file_exists($file_path);
      if (empty($is_file)) {
        include APPLICATION_PATH . "/application/modules/Sitepage/controllers/license/license4.php";
      } else {
        include $file_path;
      }

      //CODE FOR INCREASE THE SIZE OF engine4_authorization_permissions'S FIELD TYPE
      $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_permissions LIKE 'type'")->fetch();
      if (!empty($type_array)) {
        $varchar = $type_array['Type'];
        $length_varchar = explode("(", $varchar);
        $length = explode(")", $length_varchar[1]);
        $length_type = $length[0];
        if ($length_type < 32) {
          $run_query = $db->query("ALTER TABLE `engine4_authorization_permissions` CHANGE `type` `type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
        }
      }


      $pageTime = time();
      $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
			('sitepagenote.basetime', $pageTime ),
			('sitepagenote.isvar', 0 ),
			('sitepagenote.filepath', 'Sitepagenote/controllers/license/license2.php');");

      //PUT SITEPAGE NOTE WIDGET IN ADMIN CONTENT TABLE
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_modules')
              ->where('name = ?', 'sitepagenote')
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
                  ->where('name = ?', 'sitepagenote.profile-sitepagenotes')
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
                    'name' => 'sitepagenote.profile-sitepagenotes',
                    'parent_content_id' => $tab_id,
                    'order' => 112,
                    'params' => '{"title":"Notes","titleCount":"true"}',
                ));
              }
            }
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
      return $this->_error("<span style='color:red'>Note: You have installed the <a href='http://www.socialengineaddons.com/socialengine-directory-pages-plugin' target='_blank'>Directory / Pages Plugin</a> but not activated it on your site yet. Please activate it first before installing the Directory / Pages - Notes Extension.</span><br/> <a href='" . 'http://' . $core_final_url . "admin/sitepage/settings/readme'>Click here</a> to activate the Directory / Pages Plugin.");
    } else {
      $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
      return $this->_error("<span style='color:red'>Note: You have not installed the <a href='http://www.socialengineaddons.com/socialengine-directory-pages-plugin' target='_blank'>Directory / Pages Plugin</a> on your site yet. Please install it first before installing the <a href='http://www.socialengineaddons.com/pageextensions/socialengine-directory-pages-notes' target='_blank'>Directory / Pages - Notes Extension</a>.</span><br/> <a href='" . $base_url . "/manage'>Click here</a> to go Manage Packages.");
    }
  }

  function onInstall() {

    $db = $this->getDb();

    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepagenote_notes'")->fetch();
    if (!empty($table_exist)) {
      //DROP THE COLUMN FROM THE "engine4_sitepagenote_notes" TABLE
      $pageOwnerIdColumn = $db->query("SHOW COLUMNS FROM engine4_sitepagenote_notes LIKE 'page_owner_id'")->fetch();
      if (!empty($pageOwnerIdColumn)) {
        $db->query("ALTER TABLE `engine4_sitepagenote_notes` DROP `page_owner_id`");
      }

      //DROP THE COLUMN FROM THE "engine4_sitepagenote_notes" TABLE
      $ownerTypeColumn = $db->query("SHOW COLUMNS FROM engine4_sitepagenote_notes LIKE 'owner_type'")->fetch();
      if (!empty($ownerTypeColumn)) {
        $db->query("ALTER TABLE `engine4_sitepagenote_notes` DROP `owner_type`");
      }

      //DROP THE INDEX FROM THE "engine4_sitepagenote_notes" TABLE
      $ownerTypeColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepagenote_notes` WHERE Key_name = 'owner_type'")->fetch();

      if (!empty($ownerTypeColumnIndex)) {
        $db->query("ALTER TABLE engine4_sitepagenote_notes DROP INDEX owner_type");
      }

      //ADD THE INDEX FROM THE "engine4_sitepagenote_notes" TABLE
      $pageIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepagenote_notes` WHERE Key_name = 'page_id'")->fetch();

      if (empty($pageIdColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepagenote_notes` ADD INDEX ( `page_id` );");
      }

      //ADD THE INDEX FROM THE "engine4_sitepagenote_notes" TABLE
      $ownerIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepagenote_notes` WHERE Key_name = 'owner_id'")->fetch();

      if (empty($ownerIdColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepagenote_notes` ADD INDEX ( `owner_id` );");
      }
    }

    //REMOVED WIDGET SETTING TAB FROM ADMIN PANEL
    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_modules')
            ->where('name = ?', 'sitepagenote')
            ->where('version <= ?', '4.1.7p2');
    $is_enabled = $select->query()->fetchObject();
    if (!empty($is_enabled)) {
      $widget_names = array('comment', 'recent', 'like', 'homerecent');

      foreach ($widget_names as $widget_name) {
        $widget_type = $widget_name;
        $widget_name = 'sitepagenote.' . $widget_name . '-sitepagenotes';

        if ($widget_name == 'sitepagenote.homerecent-sitepagerenotes') {
          $setting_name = 'sitepagenote.homerecentnotes.widgets';
        } else {
          $setting_name = 'sitepagenote.' . $widget_type . '.widgets';
        }
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

//    //START CODE FOR LIGHTBOX
//    //HERE WE CHECKING THAT SITEPAGEALBUM ENTRY EXIST IN THE CORE MODULE TABLE OR NOT
//    $select = new Zend_Db_Select($db);
//    $select
//            ->from('engine4_core_modules', array('version'))
//            ->where("name =?", "sitepagenote");
//    $sitepagenoteVersion = $select->query()->fetchAll();
//
//    //IF NOT EXIST THEN WE INSERTING THE LIGHTBOX SHOULD BE DISPLAY OR NOT
//    if (empty($sitepagenoteVersion)) {
//      $value = '';
//      $select = new Zend_Db_Select($db);
//      $value = $select
//              ->from('engine4_core_settings', array('value'))
//              ->where("name =?", "socialengineaddon.display.lightbox")
//              ->query()
//              ->fetchColumn();
//
//      //IF LIGHTBOX IS NOT DISPLAY THEN WE WILL INSERTING THE ACTIVITY FEED VALUE
//      if (empty($value)) {        
//        $select = new Zend_Db_Select($db);
//        $select
//                ->from('engine4_core_settings', array('name'))
//                ->where("name Like ?", "%socialengineaddon.lightbox.option.display%");
//        $name = $select->query()->fetchAll();
//
//        $count = count($name);
//
//        $select = new Zend_Db_Select($db);
//        $select
//                ->from('engine4_core_settings', array('name'))
//                ->where("value =?", "sitepagenote");
//        $name = $select->query()->fetchColumn();
//        if (empty($name)) {
//          $name = 'socialengineaddon.lightbox.option.display.' . ++$count;
//          $db->insert('engine4_core_settings', array(
//              'name' => $name,
//              'value' => 'sitepagenote'
//          ));
//        }      
//      }          
//    }
//    //END CODE FOR LIGHTBOX    
    //START THE WORK FOR MAKE WIDGETIZE PAGE OF NOTES LISTING AND NOTE VIEW PAGE
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepagenote')
            ->where('version < ?', '4.2.0');
    $is_enabled = $select->query()->fetchObject();
    if (!empty($is_enabled)) {
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_pages')
              ->where('name = ?', 'sitepagenote_index_notelist')
              ->limit(1);
      ;
      $info = $select->query()->fetch();

      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_pages')
              ->where('name = ?', 'sitepagenote_index_browse')
              ->limit(1);
      ;
      $info_browse = $select->query()->fetch();

      if (empty($info) && empty($info_browse)) {
        $db->insert('engine4_core_pages', array(
            'name' => 'sitepagenote_index_browse',
            'displayname' => 'Browse Page Notes',
            'title' => 'Page Notes List',
            'description' => 'This is the page notes.',
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
            'name' => 'sitepagenote.search-sitepagenote',
            'parent_content_id' => $right_id,
            'order' => 3,
            'params' => '{"title":"","titleCount":"true"}',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitepagenote.sitepage-note',
            'parent_content_id' => $middle_id,
            'order' => 2,
            'params' => '{"title":"","titleCount":""}',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitepagenote.homecomment-sitepagenotes',
            'parent_content_id' => $right_id,
            'order' => 4,
            'params' => '{"title":"Most Commented Notes","titleCount":"true"}',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitepagenote.sitepage-sponsorednote',
            'parent_content_id' => $right_id,
            'order' => 5,
            'params' => '{"title":"Sponsored Notes","titleCount":"true"}',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitepagenote.homelike-sitepagenotes',
            'parent_content_id' => $right_id,
            'order' => 6,
            'params' => '{"title":"Most Liked Notes","titleCount":"true"}',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitepagenote.homerecent-sitepagenotes',
            'parent_content_id' => $right_id,
            'order' => 7,
            'params' => '{"title":"Recent Notes","titleCount":"true"}',
        ));

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
        if ($infomation && $rowinfo) {
          $db->insert('engine4_core_content', array(
              'page_id' => $page_id,
              'type' => 'widget',
              'name' => 'sitepage.page-ads',
              'parent_content_id' => $right_id,
              'order' => 10,
              'params' => '{"title":"","titleCount":""}',
          ));
        }
      } else {
        $db->update('engine4_core_pages', array('name' => 'sitepagenote_index_browse'), array('name = ?' => 'sitepagenote_index_notelist'));
      }

      $db = $this->getDb();
      $select = new Zend_Db_Select($db);

      // Check if it's already been placed
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_pages')
              ->where('name = ?', 'sitepagenote_index_view')
              ->limit(1);
      ;
      $info = $select->query()->fetch();

      if (empty($info)) {
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
            'params' => '{"title":"Related Notes","titleCount":""}',
        ));

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
        if ($infomation && $rowinfo) {
          $db->insert('engine4_core_content', array(
              'page_id' => $page_id,
              'type' => 'widget',
              'name' => 'sitepage.page-ads',
              'parent_content_id' => $right_id,
              'order' => 2,
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
      if (!empty($infomation)) {
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
    }
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepagenote')
            ->where('version < ?', '4.2.1');
    $is_enabled_note = $select->query()->fetchObject();
    if ($is_enabled_note) {
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
      $featuredColumn = $db->query("SHOW COLUMNS FROM engine4_sitepagenote_notes LIKE 'featured'")->fetch();
      if (empty($featuredColumn)) {
        $db->query("ALTER TABLE `engine4_sitepagenote_notes` ADD `featured` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `photo_id`");
      }
      $db->update('engine4_core_pages', array('displayname' => 'Browse Page Notes'), array('displayname = ?' => 'Page Notes'));
    }
    //END THE WORK FOR MAKE WIDGETIZE PAGE OF NOTES LISTING AND NOTE VIEW PAGE
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'sitepage.feed.type');
    $info = $select->query()->fetch();
    $enable = 1;
    if (!empty($info))
      $enable = $info['value'];
    $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_object_thumb`) VALUES("sitepagenote_admin_new", "sitepagenote", "{item:$object} created a new note:", ' . $enable . ' , 6, 2, 1, 1, 1, 1)');

    $categoryIdColumn = $db->query("SHOW COLUMNS FROM engine4_sitepagenote_notes LIKE 'category_id'")->fetch();
    if (empty($categoryIdColumn)) {
      $db->query("ALTER TABLE `engine4_sitepagenote_notes` ADD `category_id` INT( 11 )  NOT NULL DEFAULT '0';");
    }
    
    $categoryTableExist = $db->query("SHOW TABLES LIKE 'engine4_sitepagenote_categories'")->fetch();
    if(empty($categoryTableExist)) {
      $db->query("
        CREATE TABLE IF NOT EXISTS `engine4_sitepagenote_categories` (
          `category_id` int(11) unsigned NOT NULL auto_increment,
          `title` varchar(64) NOT NULL,
          PRIMARY KEY  (`category_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
      ");
      
      $db->query("
        INSERT IGNORE INTO `engine4_sitepagenote_categories` (`title`) VALUES
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
                ->where('name = ?', 'sitepagenote')
                ->where('enabled = ?', 1);    
    $is_sitepagenote_object = $select->query()->fetchObject();
    if($is_sitepagenote_object) {    
        $db->query('
          INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
          ("sitepagenote_admin_main_categories", "sitepagenote", "Categories", "", \'{"route":"admin_default","module":"sitepagenote","controller":"settings","action":"categories"}\', "sitepagenote_admin_main", "", 3);
        ');
    }

		$select = new Zend_Db_Select($db);
		$select
					->from('engine4_core_modules')
					->where('name = ?', 'sitemobile')
					->where('enabled = ?', 1);
		$is_sitemobile_object = $select->query()->fetchObject();
		if($is_sitemobile_object)  {
			include APPLICATION_PATH . "/application/modules/Sitepagenote/controllers/license/mobileLayoutCreation.php";
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
('sitepagenote','1')");
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_sitemobile_modules')
							->where('name = ?', 'sitepagenote')
							->where('integrated = ?', 0);
			$is_sitemobile_object = $select->query()->fetchObject();
      if($is_sitemobile_object)  {
				$actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
				$controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
				if($controllerName == 'manage' && $actionName == 'install') {
          $view = new Zend_View();
					$baseUrl = ( !empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://':'http://') .  $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
					$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
					$redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/sitepagenote/integrated/0/redirect/install');
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