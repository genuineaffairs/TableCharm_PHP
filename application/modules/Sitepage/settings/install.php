<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: install.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Installer extends Engine_Package_Installer_Module {

  function onPreInstall() {

    $getErrorMsg = $this->getVersion(); 
    if (!empty($getErrorMsg)) {
      return $this->_error($getErrorMsg);
    }

    $PRODUCT_TYPE = 'sitepage';
    $PLUGIN_TITLE = 'Sitepage';
    $PLUGIN_VERSION = '4.8.0p1';
    $PLUGIN_CATEGORY = 'plugin';
    $PRODUCT_DESCRIPTION = 'Sitepage Plugin';
    $PRODUCT_TITLE = 'Directory / Pages';
    $_PRODUCT_FINAL_FILE = 0;
    $SocialEngineAddOns_version = '4.8.0';
    $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
    $is_file = file_exists($file_path);
    if (empty($is_file)) {
      include APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license4.php";
    } else {
      include APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license3.php";
      $db = $this->getDb();
      $select = new Zend_Db_Select($db);
      $select->from('engine4_core_modules')->where('name = ?', $PRODUCT_TYPE);
      $is_Mod = $select->query()->fetchObject();
      if (empty($is_Mod)) {
        include_once $file_path;
      }
    }

    parent::onPreInstall();
  }

  private function getVersion() {
  
    $db = $this->getDb();

    $errorMsg = '';
    $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

    $modArray = array(
      'sitemobile' => '4.6.0p2',
      'advancedactivity' => '4.8.0',
      'communityad'  =>'4.8.0'
    );
    
    $finalModules = array();
    foreach ($modArray as $key => $value) {
    		$select = new Zend_Db_Select($db);
		$select->from('engine4_core_modules')
					->where('name = ?', "$key")
					->where('enabled = ?', 1);
		$isModEnabled = $select->query()->fetchObject();
			if (!empty($isModEnabled)) {
				$select = new Zend_Db_Select($db);
				$select->from('engine4_core_modules',array('title', 'version'))
					->where('name = ?', "$key")
					->where('enabled = ?', 1);
				$getModVersion = $select->query()->fetchObject();

				$isModSupport = strcasecmp($getModVersion->version, $value);
				if ($isModSupport < 0) {
					$finalModules[$key] = $getModVersion->title;
				}
			}
    }

    foreach ($finalModules as $modArray) {
      $errorMsg .= '<div class="tip"><span style="background-color: #da5252;color:#FFFFFF;">Note: You do not have the latest version of the "' . $modArray . '". Please upgrade "' . $modArray . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "'.$modArray.'".<br/> Please <a class="" href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';

    }

    return $errorMsg;
  }

  function onInstall() {

    $db = $this->getDb();

    $db->query("INSERT IGNORE INTO `engine4_core_mailtemplates` (`type`, `module`, `vars`) VALUES ('SITEPAGE_PAGE_CREATION', 'sitepage', '[host],[object_title],[sender],[object_link],[object_description]');");
    
    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_membership'")->fetch();
    if(!empty($table_exist)) {
      $email_field = $db->query("SHOW COLUMNS FROM engine4_sitepage_membership LIKE 'email'")->fetch();
      if (empty($email_field)) {
        $db->query("ALTER TABLE `engine4_sitepage_membership` ADD `email` TINYINT( 1 ) NOT NULL DEFAULT '1'");
      }
    }

    // ADD COLUMN BROWSE IN PAGE TABLE META TABLE
    $meta_table_exist = $db->query('SHOW TABLES LIKE \'engine4_sitepage_page_fields_meta\'')->fetch();
    if (!empty($meta_table_exist)) {
      $column_exist = $db->query('SHOW COLUMNS FROM engine4_sitepage_page_fields_meta LIKE \'browse\'')->fetch();
      if (empty($column_exist)) {
        $db->query("ALTER TABLE `engine4_sitepage_page_fields_meta`  ADD `browse` TINYINT UNSIGNED NOT NULL DEFAULT '0';");
      }
    }
    
    $column_exist_action_email = $db->query('SHOW COLUMNS FROM engine4_sitepage_manageadmins LIKE \'action_email\'')->fetch();
    if (empty($column_exist_action_email)) {
      $db->query("ALTER TABLE `engine4_sitepage_manageadmins` ADD `action_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL");
    }
    
    $db->query("DELETE FROM `engine4_seaocore_searchformsetting` WHERE `engine4_seaocore_searchformsetting`.`module` = 'sitepage' AND `engine4_seaocore_searchformsetting`.`name` = 'profile_type' LIMIT 1");

    $sitepagePagesTable = $db->query('SHOW TABLES LIKE \'engine4_sitepage_pages\'')->fetch();
    if (!empty($sitepagePagesTable)) {
			$subpage = $db->query("SHOW COLUMNS FROM engine4_sitepage_pages LIKE 'subpage'")->fetch();
			if (empty($subpage)) {
				$db->query("ALTER TABLE `engine4_sitepage_pages` ADD `subpage` TINYINT( 1 ) NOT NULL");
			}
			
			$parent_id = $db->query("SHOW COLUMNS FROM engine4_sitepage_pages LIKE 'parent_id'")->fetch();
			if (empty($parent_id)) {
				$db->query("ALTER TABLE `engine4_sitepage_pages` ADD `parent_id` INT( 11 ) NOT NULL DEFAULT '0'");
			}
		}

    
    //DROP THE INDEX FROM THE `engine4_sitepage_lists` TABLE
    $sitepageListsTable = $db->query('SHOW TABLES LIKE \'engine4_sitepage_lists\'')->fetch();
    if (!empty($sitepageListsTable)) {
			$sitepagelistsResults = $db->query("SHOW INDEX FROM `engine4_sitepage_lists` WHERE Key_name = 'page_id'")->fetch();
			if (!empty($sitepagelistsResults)) {
				$db->query("ALTER TABLE engine4_sitepage_lists DROP INDEX page_id");
				$db->query("ALTER TABLE `engine4_sitepage_lists` ADD UNIQUE (`owner_id`, `page_id`);");
			}
    }

    //START FOLLOW WORK
    //IF 'engine4_seaocore_follows' TABLE IS NOT EXIST THAN CREATE'
    $seocoreFollowTable = $db->query('SHOW TABLES LIKE \'engine4_seaocore_follows\'')->fetch();
    if (empty($seocoreFollowTable)) {
      $db->query("CREATE TABLE IF NOT EXISTS `engine4_seaocore_follows` (
        `follow_id` int(11) unsigned NOT NULL auto_increment,
        `resource_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
        `resource_id` int(11) unsigned NOT NULL,
        `poster_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
        `poster_id` int(11) unsigned NOT NULL,
        `creation_date` datetime NOT NULL,
        PRIMARY KEY  (`follow_id`),
        KEY `resource_type` (`resource_type`, `resource_id`),
        KEY `poster_type` (`poster_type`, `poster_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;");
    }

    $select = new Zend_Db_Select($db);
    $advancedactivity = $select->from('engine4_core_modules', 'name')
            ->where('name = ?', 'advancedactivity')
            ->query()
            ->fetchcolumn();

    $is_enabled = $select->query()->fetchObject();
    if (!empty($advancedactivity)) {
      $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`, `is_grouped`) VALUES ("follow_sitepage_page", "sitepage", \'{item:$subject} is following {item:$owner}\'\'s {item:$object:page}: {body:$body}\', 1, 5, 1, 1, 1, 1, 1)');
    } else {
      $db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES ("follow_sitepage_page", "sitepage", \'{item:$subject} is following {item:$owner}\'\'s {item:$object:page}: {body:$body}\', 1, 1, 1, 1, 1, 1)');
    }
    //END FOLLOW WORK
    //START LIKE PRIVACY WORKENTRY FOR LIST TABLE. 
    $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitepage_lists` (
			`list_id` int(11) NOT NULL AUTO_INCREMENT,
			`title` varchar(64) NOT NULL,
			`owner_id` int(11) NOT NULL,
			`page_id` int(11) NOT NULL,
			PRIMARY KEY (`list_id`),
			UNIQUE KEY `owner_id` (`owner_id`,`page_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;");

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepage')
            ->where('version <= ?', '4.2.9');
    $is_enabled = $select->query()->fetchObject();
    if (!empty($is_enabled)) {
      $select = new Zend_Db_Select($db);
      $select->from('engine4_sitepage_pages', array('page_id', 'owner_id'));
      $sitepage_results = $select->query()->fetchAll();
      if (!empty($sitepage_results)) {
        foreach ($sitepage_results as $result) {
          $db->query("INSERT IGNORE INTO `engine4_sitepage_lists` (`title`, `owner_id`, `page_id`) VALUES ('SITEPAGE_LIKE', " . $result['owner_id'] . " , " . $result['page_id'] . ");");
        }
      }

      //START UPDATE ALL MEMBER LEVEL SETTINGS WITH NEW SETTING LIKE PRIVACY.
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_authorization_levels', array('level_id'))
              ->where('title != ?', 'public');
      $check_sitepage = $select->query()->fetchAll();
      foreach ($check_sitepage as $modArray) {

        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_authorization_permissions', array('params', 'name', 'level_id'))
                ->where('type LIKE "%sitepage_page%"')
                ->where('level_id = ?', $modArray['level_id'])
                ->where('name LIKE "%auth_s%"');
        $result = $select->query()->fetchAll();

        foreach ($result as $results) {
          $params = Zend_Json::decode($results['params']);
          $params[] = 'like_member';
          $paramss = Zend_Json::encode($params);
          $db->query("UPDATE `engine4_authorization_permissions` SET `params` = '$paramss' WHERE `engine4_authorization_permissions`.`type` = 'sitepage_page' AND `engine4_authorization_permissions`.`name` = '" . $results['name'] . "' AND `engine4_authorization_permissions`.`level_id` = '" . $results['level_id'] . "';");
        }
      }
      //START UPDATE ALL MEMBER LEVEL SETTINGS WITH NEW SETTING LIKE PRIVACY.
    }
    //END LIKE PRIVACY WORKENTRY FOR LIST TABLE. 
    	$member_titleCover = $db->query("SHOW COLUMNS FROM engine4_sitepage_pages LIKE 'member_title'")->fetch();
		if (empty($member_titleCover)) {
			$db->query("ALTER TABLE `engine4_sitepage_pages` ADD `member_title` VARCHAR( 64 ) NOT NULL");
		}
		 
	  $pageCover = $db->query("SHOW COLUMNS FROM engine4_sitepage_pages LIKE 'page_cover'")->fetch();
		if (empty($pageCover)) {
			$db->query("ALTER TABLE `engine4_sitepage_pages` ADD `page_cover` INT( 11 ) NOT NULL DEFAULT '0'");
		}
		
	  $pageCoverParams = $db->query("SHOW COLUMNS FROM engine4_sitepage_albums LIKE 'cover_params'")->fetch();
		if (empty($pageCoverParams)) {
			$db->query("ALTER TABLE `engine4_sitepage_albums` ADD `cover_params` VARCHAR( 265 ) NULL");
		}

    $column_exist_action_notification = $db->query('SHOW COLUMNS FROM engine4_sitepage_manageadmins LIKE \'action_notification\'')->fetch();
    if (empty($column_exist_action_notification)) {
      $db->query("ALTER TABLE `engine4_sitepage_manageadmins` ADD `action_notification` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL");
    }

    $column_exist_body = $db->query('SHOW COLUMNS FROM engine4_sitepage_pages LIKE \'body\'')->fetch();
    if (!empty($column_exist_body)) {
      $db->query("ALTER TABLE  `engine4_sitepage_pages` CHANGE  `body`  `body` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL");
    }

    $column_exist_description = $db->query('SHOW COLUMNS FROM engine4_sitepage_contentpages LIKE \'description\'')->fetch();
    if (!empty($column_exist_description)) {
      $db->query("ALTER TABLE  `engine4_sitepage_contentpages` CHANGE  `description`  `description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL");
    }

    $column_exist_locationname = $db->query('SHOW COLUMNS FROM engine4_sitepage_locations LIKE \'locationname\'')->fetch();
    if (empty($column_exist_locationname)) {
      $db->query("ALTER TABLE `engine4_sitepage_locations` ADD `locationname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL");
    }

    $column_exist_follow_count = $db->query('SHOW COLUMNS FROM engine4_sitepage_pages LIKE \'follow_count\'')->fetch();
    if (empty($column_exist_follow_count)) {
      $db->query("ALTER TABLE `engine4_sitepage_pages` ADD `follow_count` int(11) NOT NULL");
    }

    //Notification seetings work
    $column_exist_email = $db->query('SHOW COLUMNS FROM engine4_sitepage_manageadmins LIKE \'email\'')->fetch();
    $column_exist_notification = $db->query('SHOW COLUMNS FROM engine4_sitepage_manageadmins LIKE \'notification\'')->fetch();
    if (empty($column_exist) && empty($column_exist_notification)) {
      $db->query("ALTER TABLE `engine4_sitepage_manageadmins` ADD `email` TINYINT( 1 ) NOT NULL DEFAULT '1'");
      $db->query("ALTER TABLE `engine4_sitepage_manageadmins` ADD `notification` TINYINT( 1 ) NOT NULL");
    }

    //START THE WORK FOR MAKE WIDGETIZE PAGE OF Locatio or map.
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepage')
            ->where('version < ?', '4.2.3');
    $is_enabled = $select->query()->fetchObject();
    if (empty($is_enabled)) {
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_pages')
              ->where('name = ?', 'sitepage_index_map')
              ->limit(1);
      $info = $select->query()->fetch();

      if (empty($info)) {
        $db->insert('engine4_core_pages', array(
            'name' => 'sitepage_index_map',
            'displayname' => 'Browse Pages’ Locations',
            'title' => 'Browse Pages’ Locations',
            'description' => 'Browse Pages’ Locations',
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
            'name' => 'sitepage.location-search',
            'parent_content_id' => $middle_id,
            'order' => 2,
            'params' => '{"title":"","titleCount":"true","street":"1","city":"1","state":"1","country":"1"}',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitepage.browselocation-sitepage',
            'parent_content_id' => $middle_id,
            'order' => 3,
            'params' => '{"title":"","titleCount":"true"}',
        ));
      }
    }
    //END THE WORK FOR MAKE WIDGETIZE PAGE OF LOCATIO OR MAP.
    //START THE WORK FOR MAKE WIDGETIZE PAGE OF Locatio or map.MOBILE PAGE.
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepage')
            ->where('version < ?', '4.2.3');
    $is_enabled = $select->query()->fetchObject();
    if (empty($is_enabled)) {
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_pages')
              ->where('name = ?', 'sitepage_index_mobilemap')
              ->limit(1);
      $info = $select->query()->fetch();

      if (empty($info)) {
        $db->insert('engine4_core_pages', array(
            'name' => 'sitepage_index_mobilemap',
            'displayname' => 'Mobile Browse Pages’ Locations',
            'title' => 'Mobile Browse Pages’ Locations',
            'description' => 'Mobile Browse Pages’ Locations',
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
            'name' => 'sitepage.location-search',
            'parent_content_id' => $middle_id,
            'order' => 2,
            'params' => '{"title":"","titleCount":"true","street":"1","city":"1","state":"1","country":"1"}',
        ));

        $db->insert('engine4_core_content', array(
            'page_id' => $page_id,
            'type' => 'widget',
            'name' => 'sitepage.browselocation-sitepage',
            'parent_content_id' => $middle_id,
            'order' => 3,
            'params' => '{"title":"","titleCount":"true"}',
        ));
      }
    }
    //END THE WORK FOR MAKE WIDGETIZE PAGE OF LOCATIO OR MAP.MOBILE PAGE.
    //WORK FOR CORE CONTENT PAGES
    $select = new Zend_Db_Select($db);

//     $select->from('engine4_core_content',array('params'))
//             ->where('name = ?', 'sitepage.socialshare-sitepage');
// 		$result = $select->query()->fetchObject();
//     if(!empty($result->params)) {
// 			$params = Zend_Json::decode($result->params);
// 			if(isset($params['code'])) {
// 				$code = $params['code'];
// 				$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
// 				('sitepage.code.share','".$code. "');");
// 			}
//     }
    //MIGRATE DATA TO 'engine4_seaocore_searchformsetting' FROM 'engine4_sitepage_searchform'
    $seocoreSearchformTable = $db->query('SHOW TABLES LIKE \'engine4_seaocore_searchformsetting\'')->fetch();
    $sitepageSearchformTable = $db->query('SHOW TABLES LIKE \'engine4_sitepage_searchform\'')->fetch();
    if (!empty($seocoreSearchformTable) && !empty($sitepageSearchformTable)) {
      $datas = $db->query('SELECT * FROM `engine4_sitepage_searchform`')->fetchAll();
      foreach ($datas as $data) {
        $data_module = 'sitepage';
        $data_name = $data['name'];
        $data_display = $data['display'];
        $data_order = $data['order'];
        $data_label = $data['label'];

        $db->query("INSERT IGNORE INTO `engine4_seaocore_searchformsetting` (`module`, `name`, `display`, `order`, `label`) VALUES ('$data_module', '$data_name', $data_display, $data_order, '$data_label')");
      }

      $db->query('DROP TABLE IF EXISTS `engine4_sitepage_searchform`');
    }

    $table_exist = $db->query('SHOW TABLES LIKE \'engine4_sitepage_photos\'')->fetch();
    if (!empty($table_exist)) {
      $column_exist = $db->query('SHOW COLUMNS FROM engine4_sitepage_photos LIKE \'description\'')->fetch();
      if (empty($column_exist)) {
        $db->query('ALTER TABLE `engine4_sitepage_photos` CHANGE `description` `description` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL');
      }
    }

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepage')
            ->where('version <= ?', '4.1.5p1');
    $is_enabled = $select->query()->fetchObject();
    if (!empty($is_enabled)) {
      $db->query("DROP TABLE IF EXISTS `engine4_sitepage_admincontent`;");
      $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitepage_admincontent` (
								  `admincontent_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
								  `page_id` int(11) unsigned NOT NULL,
								  `type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'widget',
								  `name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
								  `parent_content_id` int(11) unsigned DEFAULT NULL,
								  `order` int(11) NOT NULL DEFAULT '1',
								  `params` text COLLATE utf8_unicode_ci,
								  `attribs` text COLLATE utf8_unicode_ci,
								  PRIMARY KEY (`admincontent_id`),
								  KEY `page_id` (`page_id`,`order`)
								) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;");

      $db->query("DROP TABLE IF EXISTS `engine4_sitepage_hideprofilewidgets`;");
      $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitepage_hideprofilewidgets` (
								  `hideprofilewidgets_id` int(11) NOT NULL AUTO_INCREMENT,
								  `widgetname` varchar(64) NOT NULL,
								  PRIMARY KEY (`hideprofilewidgets_id`)
								) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");

      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_pages', array('page_id'))
              ->where('name = ?', 'sitepage_index_view');
      $corepageObject = $select->query()->fetchAll();

      if (!empty($corepageObject)) {
        $page_id = $corepageObject[0]['page_id'];
      }
      if (!empty($page_id)) {
        $select = new Zend_Db_Select($db);
        $db->query("DELETE FROM engine4_sitepage_hideprofilewidgets");

        if (!empty($page_id)) {
          $select = new Zend_Db_Select($db);
          $db->query("DELETE FROM engine4_sitepage_admincontent WHERE page_id = $page_id");
        }
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_settings', array('value'))
                ->where('name = ?', 'sitepage.layout.setting');
        $layoutsetting = $select->query()->fetchAll();
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_settings', array('value'))
                ->where('name = ?', 'sitepage.showmore');
        $showmore = $select->query()->fetchAll();

        if (!empty($showmore)) {
          $showmaxtab = $showmore[0]['value'];
          $maxtab = "{\"max\":\"$showmaxtab\"}";
        } else {
          $maxtab = "{\"max\":\"8\"}";
        }

        if ($layoutsetting[0]['value'] == 1) {
          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`) VALUES
					($page_id, 'container', 'main', '2')");
          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_sitepage_admincontent', array('admincontent_id'))
                  ->where('name = ?', 'main')
                  ->where('type = ?', 'container');
          $containerObject = $select->query()->fetchAll();
          $container_id = $containerObject[0]['admincontent_id'];
          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`,`parent_content_id`) VALUES
		($page_id, 'container', 'middle', '6', $container_id)");
          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_sitepage_admincontent', array('admincontent_id'))
                  ->where('name = ?', 'middle')
                  ->where('type = ?', 'container');
          $containerObject = $select->query()->fetchAll();
          $middle_id = $containerObject[0]['admincontent_id'];
          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`,`parent_content_id`) VALUES
		($page_id, 'container', 'left', '4', $container_id)");
          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_sitepage_admincontent', array('admincontent_id'))
                  ->where('name = ?', 'left')
                  ->where('type = ?', 'container');
          $containerObject = $select->query()->fetchAll();
          $left_id = $containerObject[0]['admincontent_id'];

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
			($page_id, 'widget', 'core.container-tabs', '7', $middle_id, '$maxtab')");
          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_sitepage_admincontent', array('admincontent_id'))
                  ->where('name = ?', 'core.container-tabs')
                  ->where('type = ?', 'widget');
          $containerObject = $select->query()->fetchAll();
          $middle_tab = $containerObject[0]['admincontent_id'];
          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
		($page_id, 'widget', 'sitepage.thumbphoto-sitepage', '1', $middle_id,'{\"title\":\"\"}')");
          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepage.title-sitepage', '2', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'seaocore.like-button', '3', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'facebookse');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'Facebookse.facebookse-sitepageprofilelike', '4', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
          }

          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepagealbum');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				  ($page_id, 'widget', 'sitepage.photorecent-sitepage', '5', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				  ($page_id, 'widget', 'sitepage.albums-sitepage', '23', $left_id,'{\"title\":\"Albums\",\"titleCount\":\"true\"}')");
          }

          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepagemusic');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				  ($page_id, 'widget', 'sitepagemusic.profile-player', '24', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
          }

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
			($page_id, 'widget', 'sitepage.favourite-page', '25', $left_id,'{\"title\":\"Linked Pages\",\"titleCount\":\"true\"}')");

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepage.mainphoto-sitepage', '10', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");


          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepage.options-sitepage', '11', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepage.write-page', '12', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepage.information-sitepage', '13', $left_id,'{\"title\":\"Information\",\"titleCount\":\"true\"}')");

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'seaocore.people-like', '14', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepagereview');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepagereview.ratings-sitepagereviews', '15', $left_id,'{\"title\":\"Ratings\",\"titleCount\":\"true\"}')");
          }

          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepagebadge');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepagebadge.badge-sitepagebadge', '16', $left_id,'{\"title\":\"Badge\",\"titleCount\":\"true\"}')");
          }

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepage.suggestedpage-sitepage', '17', $left_id,'{\"title\":\"You May Also Like\",\"titleCount\":\"true\"}')");

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepage.socialshare-sitepage', '18', $left_id,'{\"title\":\"Social Share\",\"titleCount\":\"true\"}')");
          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepage.foursquare-sitepage', '19', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepage.insights-sitepage', '21', $left_id,'{\"title\":\"Insights\",\"titleCount\":\"true\"}')");

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepage.featuredowner-sitepage', '22', $left_id,'{\"title\":\"Owners\",\"titleCount\":\"true\"}')");

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'activity.feed', '1', $middle_tab,'{\"title\":\"Updates\",\"titleCount\":\"true\"}')");

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepage.info-sitepage', '2', $middle_tab,'{\"title\":\"Info\",\"titleCount\":\"true\"}')");

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepage.overview-sitepage', '3', $middle_tab,'{\"title\":\"Overview\",\"titleCount\":\"true\"}')");

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
			($page_id, 'widget', 'sitepage.location-sitepage', '4', $middle_tab,'{\"title\":\"Map\",\"titleCount\":\"true\"}')");

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
			($page_id, 'widget', 'core.profile-links', '125', $middle_tab,'{\"title\":\"Links\",\"titleCount\":\"true\"}')");
          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepagealbum');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepage.photos-sitepage', '110', $middle_tab,'{\"title\":\"Photos\",\"titleCount\":\"true\"}')");
          }

          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepagevideo');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepagevideo.profile-sitepagevideos', '111', $middle_tab,'{\"title\":\"Videos\",\"titleCount\":\"true\"}')");
          }

          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepagenote');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepagenote.profile-sitepagenotes', '112', $middle_tab,'{\"title\":\"Notes\",\"titleCount\":\"true\"}')");
          }

          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepagereview');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepagereview.profile-sitepagereviews', '113', $middle_tab,'{\"title\":\"Reviews\",\"titleCount\":\"true\"}')");
          }
          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepageform');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepageform.sitepage-viewform', '114', $middle_tab,'{\"title\":\"Form\",\"titleCount\":\"false\"}')");
          }

          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepagedocument');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepagedocument.profile-sitepagedocuments', '115', $middle_tab,'{\"title\":\"Documents\",\"titleCount\":\"false\"}')");
          }


          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepageoffer');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepageoffer.profile-sitepageoffers', '116', $middle_tab,'{\"title\":\"Offers\",\"titleCount\":\"false\"}')");
          }


          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepageevent');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepageevent.profile-sitepageevents', '117', $middle_tab,'{\"title\":\"Events\",\"titleCount\":\"false\"}')");
          }


          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepagepoll');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepagepoll.profile-sitepagepolls', '118', $middle_tab,'{\"title\":\"Polls\",\"titleCount\":\"false\"}')");
          }


          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepagediscussion');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
				($page_id, 'widget', 'sitepage.discussion-sitepage', '119', $middle_tab,'{\"title\":\"Discussions\",\"titleCount\":\"false\"}')");
          }


          $select = new Zend_Db_Select($db);

          $select
                  ->from('engine4_sitepage_contentpages', array('contentpage_id'))
                  ->where('name =?', 'sitepage_index_view');


          $contentpages_id = $select->query()->fetchAll();

          foreach ($contentpages_id as $key => $value) {
            $page_id = $value['contentpage_id'];

            $db->query("DELETE FROM engine4_sitepage_content WHERE contentpage_id = $page_id");



            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`) VALUES
					($page_id, 'container', 'main', '2')");

            $select = new Zend_Db_Select($db);

            $select
                    ->from('engine4_sitepage_content', array('content_id'))
                    ->where('name = ?', 'main')
                    ->where('type = ?', 'container')
                    ->where('contentpage_id = ?', $page_id)
            ;

            $containerObject = $select->query()->fetchAll();
            $container_id = $containerObject[0]['content_id'];

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`,`parent_content_id`) VALUES
		($page_id, 'container', 'middle', '6', $container_id)");
            $select = new Zend_Db_Select($db);

            $select
                    ->from('engine4_sitepage_content', array('content_id'))
                    ->where('name = ?', 'middle')
                    ->where('type = ?', 'container')
                    ->where('contentpage_id = ?', $page_id);
            $containerObject = $select->query()->fetchAll();
            $middle_id = $containerObject[0]['content_id'];


            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`,`parent_content_id`) VALUES
		($page_id, 'container', 'left', '4', $container_id)");
            $select = new Zend_Db_Select($db);

            $select
                    ->from('engine4_sitepage_content', array('content_id'))
                    ->where('name = ?', 'left')
                    ->where('type = ?', 'container')
                    ->where('contentpage_id = ?', $page_id);
            $containerObject = $select->query()->fetchAll();
            $left_id = $containerObject[0]['content_id'];




            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
	($page_id, 'widget', 'core.container-tabs', '7', $middle_id, '$maxtab')");
            $select = new Zend_Db_Select($db);

            $select
                    ->from('engine4_sitepage_content', array('content_id'))
                    ->where('name = ?', 'core.container-tabs')
                    ->where('type = ?', 'widget')
                    ->where('contentpage_id = ?', $page_id);
            ;
            $containerObject = $select->query()->fetchAll();
            $middle_tab = $containerObject[0]['content_id'];


            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
	($page_id, 'widget', 'sitepage.thumbphoto-sitepage', '1', $middle_id,'{\"title\":\"\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
						($page_id, 'widget', 'sitepage.title-sitepage', '2', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");



            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
						($page_id, 'widget', 'seaocore.like-button', '3', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");



            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'facebookse');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'Facebookse.facebookse-sitepageprofilelike', '4', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
            }

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepagealbum');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.photorecent-sitepage', '5', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.albums-sitepage', '23', $left_id,'{\"title\":\"Albums\",\"titleCount\":\"true\"}')");
            }

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepagemusic');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							  ($page_id, 'widget', 'sitepagemusic.profile-player', '24', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
            }
            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
			($page_id, 'widget', 'sitepage.favourite-page', '25', $left_id,'{\"title\":\"Linked Pages\",\"titleCount\":\"true\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.mainphoto-sitepage', '10', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");


            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.options-sitepage', '11', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.write-page', '12', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.information-sitepage', '13', $left_id,'{\"title\":\"Information\",\"titleCount\":\"true\"}')");


            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'seaocore.people-like', '14', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");


            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepagereview');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepagereview.ratings-sitepagereviews', '15', $left_id,'{\"title\":\"Ratings\",\"titleCount\":\"true\"}')");
            }


            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepagebadge');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepagebadge.badge-sitepagebadge', '16', $left_id,'{\"title\":\"Badge\",\"titleCount\":\"true\"}')");
            }


            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.suggestedpage-sitepage', '17', $left_id,'{\"title\":\"You May Also Like\",\"titleCount\":\"true\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.socialshare-sitepage', '18', $left_id,'{\"title\":\"Social Share\",\"titleCount\":\"true\"}')");
            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.foursquare-sitepage', '19', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.insights-sitepage', '21', $left_id,'{\"title\":\"Insights\",\"titleCount\":\"true\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.featuredowner-sitepage', '22', $left_id,'{\"title\":\"Owners\",\"titleCount\":\"true\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'activity.feed', '1', $middle_tab,'{\"title\":\"Updates\",\"titleCount\":\"true\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.info-sitepage', '2', $middle_tab,'{\"title\":\"Info\",\"titleCount\":\"true\"}')");



            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.overview-sitepage', '3', $middle_tab,'{\"title\":\"Overview\",\"titleCount\":\"true\"}')");




            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.location-sitepage', '4', $middle_tab,'{\"title\":\"Map\",\"titleCount\":\"true\"}')");



            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'core.profile-links', '125', $middle_tab,'{\"title\":\"Links\",\"titleCount\":\"true\"}')");
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepagealbum');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.photos-sitepage', '110', $middle_tab,'{\"title\":\"Photos\",\"titleCount\":\"true\"}')");
            }


            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepagevideo');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepagevideo.profile-sitepagevideos', '111', $middle_tab,'{\"title\":\"Videos\",\"titleCount\":\"true\"}')");
            }

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepagenote');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepagenote.profile-sitepagenotes', '112', $middle_tab,'{\"title\":\"Notes\",\"titleCount\":\"true\"}')");
            }

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepagereview');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepagereview.profile-sitepagereviews', '113', $middle_tab,'{\"title\":\"Reviews\",\"titleCount\":\"true\"}')");
            }
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepageform');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepageform.sitepage-viewform', '114', $middle_tab,'{\"title\":\"Form\",\"titleCount\":\"false\"}')");
            }

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepagedocument');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepagedocument.profile-sitepagedocuments', '115', $middle_tab,'{\"title\":\"Documents\",\"titleCount\":\"false\"}')");
            }


            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepageoffer');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepageoffer.profile-sitepageoffers', '116', $middle_tab,'{\"title\":\"Offers\",\"titleCount\":\"false\"}')");
            }


            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepageevent');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepageevent.profile-sitepageevents', '117', $middle_tab,'{\"title\":\"Events\",\"titleCount\":\"false\"}')");
            }


            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepagepoll');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepagepoll.profile-sitepagepolls', '118', $middle_tab,'{\"title\":\"Polls\",\"titleCount\":\"false\"}')");
            }


            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepagediscussion');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.discussion-sitepage', '119', $middle_tab,'{\"title\":\"Discussions\",\"titleCount\":\"false\"}')");
            }
          }
        } else {

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`) VALUES
	($page_id, 'container', 'main', '2')");

          $select = new Zend_Db_Select($db);

          $select
                  ->from('engine4_sitepage_admincontent', array('admincontent_id'))
                  ->where('name = ?', 'main')
                  ->where('type = ?', 'container');

          $containerObject = $select->query()->fetchAll();
          $container_id = $containerObject[0]['admincontent_id'];

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`,`parent_content_id`) VALUES
	($page_id, 'container', 'middle', '6', $container_id)");
          $select = new Zend_Db_Select($db);

          $select
                  ->from('engine4_sitepage_admincontent', array('admincontent_id'))
                  ->where('name = ?', 'middle')
                  ->where('type = ?', 'container');
          $containerObject = $select->query()->fetchAll();
          $middle_id = $containerObject[0]['admincontent_id'];


          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`,`parent_content_id`) VALUES
	($page_id, 'container', 'left', '4', $container_id)");
          $select = new Zend_Db_Select($db);

          $select
                  ->from('engine4_sitepage_admincontent', array('admincontent_id'))
                  ->where('name = ?', 'left')
                  ->where('type = ?', 'container');
          $containerObject = $select->query()->fetchAll();
          $left_id = $containerObject[0]['admincontent_id'];

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
						($page_id, 'widget', 'sitepage.title-sitepage', '2', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
						($page_id, 'widget', 'seaocore.like-button', '3', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'facebookse');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'Facebookse.facebookse-sitepageprofilelike', '4', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
          }

          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepagemusic');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							  ($page_id, 'widget', 'sitepagemusic.profile-player', '24', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
          }
          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
			($page_id, 'widget', 'sitepage.favourite-page', '25', $left_id,'{\"title\":\"Linked Pages\",\"titleCount\":\"true\"}')");
          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepagealbum');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.photorecent-sitepage', '5', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.albums-sitepage', '23', $left_id,'{\"title\":\"Albums\",\"titleCount\":\"true\"}')");
          }



          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.mainphoto-sitepage', '10', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");


          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.widgetlinks-sitepage', '11', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");


          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.options-sitepage', '12', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.write-page', '13', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.information-sitepage', '14', $left_id,'{\"title\":\"Information\",\"titleCount\":\"true\"}')");


          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'seaocore.people-like', '15', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");


          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepagereview');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepagereview.ratings-sitepagereviews', '16', $left_id,'{\"title\":\"Ratings\",\"titleCount\":\"true\"}')");
          }


          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepagebadge');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepagebadge.badge-sitepagebadge', '17', $left_id,'{\"title\":\"Badge\",\"titleCount\":\"true\"}')");
          }

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.socialshare-sitepage', '18', $left_id,'{\"title\":\"Social Share\",\"titleCount\":\"true\"}')");
          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.foursquare-sitepage', '19', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.insights-sitepage', '21', $left_id,'{\"title\":\"Insights\",\"titleCount\":\"true\"}')");

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.featuredowner-sitepage', '22', $left_id,'{\"title\":\"Owners\",\"titleCount\":\"true\"}')");

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'activity.feed', '6', $middle_id,'{\"title\":\"Updates\",\"titleCount\":\"true\"}')");

          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.info-sitepage', '7', $middle_id,'{\"title\":\"Info\",\"titleCount\":\"true\"}')");



          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.overview-sitepage', '8', $middle_id,'{\"title\":\"Overview\",\"titleCount\":\"true\"}')");




          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.location-sitepage', '9', $middle_id,'{\"title\":\"Map\",\"titleCount\":\"true\"}')");



          $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'core.profile-links', '125', $middle_id,'{\"title\":\"Links\",\"titleCount\":\"true\"}')");
          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepagealbum');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.photos-sitepage', '110', $middle_id,'{\"title\":\"Photos\",\"titleCount\":\"true\"}')");
          }


          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepagevideo');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepagevideo.profile-sitepagevideos', '111', $middle_id,'{\"title\":\"Videos\",\"titleCount\":\"true\"}')");
          }

          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepagenote');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepagenote.profile-sitepagenotes', '112', $middle_id,'{\"title\":\"Notes\",\"titleCount\":\"true\"}')");
          }

          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepagereview');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepagereview.profile-sitepagereviews', '113', $middle_id,'{\"title\":\"Reviews\",\"titleCount\":\"true\"}')");
          }
          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepageform');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepageform.sitepage-viewform', '114', $middle_id,'{\"title\":\"Form\",\"titleCount\":\"false\"}')");
          }

          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepagedocument');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepagedocument.profile-sitepagedocuments', '115', $middle_id,'{\"title\":\"Documents\",\"titleCount\":\"false\"}')");
          }


          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepageoffer');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepageoffer.profile-sitepageoffers', '116', $middle_id,'{\"title\":\"Offers\",\"titleCount\":\"false\"}')");
          }


          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepageevent');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepageevent.profile-sitepageevents', '117', $middle_id,'{\"title\":\"Events\",\"titleCount\":\"false\"}')");
          }


          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepagepoll');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepagepoll.profile-sitepagepolls', '118', $middle_id,'{\"title\":\"Polls\",\"titleCount\":\"false\"}')");
          }


          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_modules')
                  ->where('name = ?', 'sitepagediscussion');
          $is_enabled = $select->query()->fetchObject();
          if (!empty($is_enabled)) {
            $db->query("INSERT IGNORE INTO `engine4_sitepage_admincontent` (`page_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.discussion-sitepage', '119', $middle_id,'{\"title\":\"Discussions\",\"titleCount\":\"false\"}')");
          }

          $select = new Zend_Db_Select($db);

          $select
                  ->from('engine4_sitepage_contentpages', array('contentpage_id'))
                  ->where('name =?', 'sitepage_index_view');

          $contentpages_id = $select->query()->fetchAll();

          foreach ($contentpages_id as $key => $value) {
            $page_id = $value['contentpage_id'];

            $db->query("DELETE FROM engine4_sitepage_content WHERE contentpage_id = $page_id");
            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`) VALUES
							($page_id, 'container', 'main', '2')");

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_sitepage_content', array('content_id'))
                    ->where('name = ?', 'main')
                    ->where('type = ?', 'container')
                    ->where('contentpage_id = ?', $page_id)
            ;

            $containerObject = $select->query()->fetchAll();
            $container_id = $containerObject[0]['content_id'];

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`,`parent_content_id`) VALUES
	($page_id, 'container', 'middle', '6', $container_id)");
            $select = new Zend_Db_Select($db);

            $select
                    ->from('engine4_sitepage_content', array('content_id'))
                    ->where('name = ?', 'middle')
                    ->where('type = ?', 'container')
                    ->where('contentpage_id = ?', $page_id);
            $containerObject = $select->query()->fetchAll();
            $middle_id = $containerObject[0]['content_id'];


            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`,`parent_content_id`) VALUES
	($page_id, 'container', 'left', '4', $container_id)");
            $select = new Zend_Db_Select($db);

            $select
                    ->from('engine4_sitepage_content', array('content_id'))
                    ->where('name = ?', 'left')
                    ->where('type = ?', 'container')
                    ->where('contentpage_id = ?', $page_id);
            $containerObject = $select->query()->fetchAll();
            $left_id = $containerObject[0]['content_id'];

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
						($page_id, 'widget', 'sitepage.title-sitepage', '2', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
						($page_id, 'widget', 'sitepage.favourite-page', '25', $left_id,'{\"title\":\"Linked Pages\",\"titleCount\":\"true\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
						($page_id, 'widget', 'seaocore.like-button', '3', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'facebookse');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'Facebookse.facebookse-sitepageprofilelike', '4', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
            }

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepagealbum');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.photorecent-sitepage', '5', $middle_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.albums-sitepage', '23', $left_id,'{\"title\":\"Albums\",\"titleCount\":\"true\"}')");
            }

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepagemusic');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							  ($page_id, 'widget', 'sitepagemusic.profile-player', '24', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
            }

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.mainphoto-sitepage', '10', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");
            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.widgetlinks-sitepage', '11', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.options-sitepage', '12', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.write-page', '13', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.information-sitepage', '14', $left_id,'{\"title\":\"Information\",\"titleCount\":\"true\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'seaocore.people-like', '15', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepagereview');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepagereview.ratings-sitepagereviews', '16', $left_id,'{\"title\":\"Ratings\",\"titleCount\":\"true\"}')");
            }

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepagebadge');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepagebadge.badge-sitepagebadge', '17', $left_id,'{\"title\":\"Badge\",\"titleCount\":\"true\"}')");
            }

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.socialshare-sitepage', '18', $left_id,'{\"title\":\"Social Share\",\"titleCount\":\"true\"}')");
            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.foursquare-sitepage', '19', $left_id,'{\"title\":\"\",\"titleCount\":\"true\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.insights-sitepage', '21', $left_id,'{\"title\":\"Insights\",\"titleCount\":\"true\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.featuredowner-sitepage', '22', $left_id,'{\"title\":\"Owners\",\"titleCount\":\"true\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'activity.feed', '6', $middle_id,'{\"title\":\"Updates\",\"titleCount\":\"true\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.info-sitepage', '7', $middle_id,'{\"title\":\"Info\",\"titleCount\":\"true\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.overview-sitepage', '8', $middle_id,'{\"title\":\"Overview\",\"titleCount\":\"true\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.location-sitepage', '9', $middle_id,'{\"title\":\"Map\",\"titleCount\":\"true\"}')");

            $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'core.profile-links', '125', $middle_id,'{\"title\":\"Links\",\"titleCount\":\"true\"}')");
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepagealbum');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.photos-sitepage', '110', $middle_id,'{\"title\":\"Photos\",\"titleCount\":\"true\"}')");
            }

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepagevideo');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepagevideo.profile-sitepagevideos', '111', $middle_id,'{\"title\":\"Videos\",\"titleCount\":\"true\"}')");
            }

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepagenote');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepagenote.profile-sitepagenotes', '112', $middle_id,'{\"title\":\"Notes\",\"titleCount\":\"true\"}')");
            }

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepagereview');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepagereview.profile-sitepagereviews', '113', $middle_id,'{\"title\":\"Reviews\",\"titleCount\":\"true\"}')");
            }
            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepageform');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepageform.sitepage-viewform', '114', $middle_id,'{\"title\":\"Form\",\"titleCount\":\"false\"}')");
            }

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepagedocument');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepagedocument.profile-sitepagedocuments', '115', $middle_id,'{\"title\":\"Documents\",\"titleCount\":\"false\"}')");
            }

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepageoffer');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepageoffer.profile-sitepageoffers', '116', $middle_id,'{\"title\":\"Offers\",\"titleCount\":\"false\"}')");
            }

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepageevent');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepageevent.profile-sitepageevents', '117', $middle_id,'{\"title\":\"Events\",\"titleCount\":\"false\"}')");
            }

            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepagepoll');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepagepoll.profile-sitepagepolls', '118', $middle_id,'{\"title\":\"Polls\",\"titleCount\":\"false\"}')");
            }


            $select = new Zend_Db_Select($db);
            $select
                    ->from('engine4_core_modules')
                    ->where('name = ?', 'sitepagediscussion');
            $is_enabled = $select->query()->fetchObject();
            if (!empty($is_enabled)) {
              $db->query("INSERT IGNORE INTO `engine4_sitepage_content` (`contentpage_id`, `type`, `name`, `order`, `parent_content_id`, `params`) VALUES
							($page_id, 'widget', 'sitepage.discussion-sitepage', '119', $middle_id,'{\"title\":\"Discussions\",\"titleCount\":\"false\"}')");
            }
          }
        }
      }
    }

    $type_array = $db->query("SHOW COLUMNS FROM engine4_core_likes LIKE 'creation_date'")->fetch();
    if (empty($type_array)) {
      $run_query = $db->query("ALTER TABLE `engine4_core_likes` ADD `creation_date` DATETIME NOT NULL");
    }
    //CODE FOR INCREASE THE SIZE OF engine4_authorization_permissions's FIELD type
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

    //CODE FOR INCREASE THE SIZE OF engine4_authorization_allow's FIELD type
    $type_array = $db->query("SHOW COLUMNS FROM engine4_authorization_allow LIKE 'resource_type'")->fetch();
    if (!empty($type_array)) {
      $varchar = $type_array['Type'];
      $length_varchar = explode("(", $varchar);
      $length = explode(")", $length_varchar[1]);
      $length_type = $length[0];
      if ($length_type < 32) {
        $run_query = $db->query("ALTER TABLE `engine4_authorization_allow` CHANGE `resource_type` `resource_type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
      }
    }

    //CODE FOR INCREASE THE SIZE OF engine4_activity_attachments's FIELD type
    $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_attachments LIKE 'type'")->fetch();
    if (!empty($type_array)) {
      $varchar = $type_array['Type'];
      $length_varchar = explode("(", $varchar);
      $length = explode(")", $length_varchar[1]);
      $length_type = $length[0];
      if ($length_type < 32) {
        $run_query = $db->query("ALTER TABLE `engine4_activity_attachments` CHANGE `type` `type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
      }
    }

    //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
    $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notifications LIKE 'subject_type'")->fetch();
    if (!empty($type_array)) {
      $varchar = $type_array['Type'];
      $length_varchar = explode("(", $varchar);
      $length = explode(")", $length_varchar[1]);
      $length_type = $length[0];
      if ($length_type < 32) {
        $run_query = $db->query("ALTER TABLE `engine4_activity_notifications` CHANGE `subject_type` `subject_type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
      }
    }

    $pageTime = time();
    $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
		('sitepage.basetime', $pageTime ),
		('sitepage.isvar', 0 ),
		('sitepage.filepath', 'Sitepage/controllers/license/license2.php');");

    //CODE FOR INCREASE THE SIZE OF engine4_activity_notifications's FIELD type
    $type_array = $db->query("SHOW COLUMNS FROM engine4_activity_notifications LIKE 'object_type'")->fetch();
    if (!empty($type_array)) {
      $varchar = $type_array['Type'];
      $length_varchar = explode("(", $varchar);
      $length = explode(")", $length_varchar[1]);
      $length_type = $length[0];
      if ($length_type < 32) {
        $run_query = $db->query("ALTER TABLE `engine4_activity_notifications` CHANGE `object_type` `object_type` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
      }
    }


    //
    // Mobile Pages Home
    // page
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_pages')
            ->where('name = ?', 'sitepage_mobi_home')
            ->limit(1);
    ;
    $info = $select->query()->fetch();

    if (empty($info)) {
      $db->insert('engine4_core_pages', array(
          'name' => 'sitepage_mobi_home',
          'displayname' => 'Mobile Pages Home',
          'title' => 'Mobile Pages Home',
          'description' => 'This is the mobile verison of a Pages home page.',
          'custom' => 0
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
          'name' => 'middle',
          'parent_content_id' => $container_id,
          'order' => 2,
          'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');

      // widgets entry
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.browsenevigation-sitepage',
          'parent_content_id' => $middle_id,
          'order' => 1,
          'params' => '{"title":"","titleCount":"true"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.zeropage-sitepage',
          'parent_content_id' => $middle_id,
          'order' => 3,
          'params' => '{"title":"","titleCount":"true"}',
      ));
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.search-sitepage',
          'parent_content_id' => $middle_id,
          'order' => 2,
          'params' => '{"title":"","titleCount":"true"}',
      ));
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.recently-popular-random-sitepage',
          'parent_content_id' => $middle_id,
          'order' => 4,
          'params' => '{"title":"","titleCount":"true"}',
      ));
    }

    // Mobile Browse Pages
    // page
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_pages')
            ->where('name = ?', 'sitepage_mobi_index')
            ->limit(1);
    ;
    $info = $select->query()->fetch();

    if (empty($info)) {
      $db->insert('engine4_core_pages', array(
          'name' => 'sitepage_mobi_index',
          'displayname' => 'Mobile Browse Pages',
          'title' => 'Mobile Browse Pages',
          'description' => 'This is the mobile verison of a pages browse page.',
          'custom' => 0
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
          'name' => 'middle',
          'parent_content_id' => $container_id,
          'order' => 2,
          'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');


      // widgets entry
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.browsenevigation-sitepage',
          'parent_content_id' => $middle_id,
          'order' => 1,
          'params' => '{"title":"","titleCount":"true"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.search-sitepage',
          'parent_content_id' => $middle_id,
          'order' => 2,
          'params' => '{"title":"","titleCount":"true"}',
      ));
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.pages-sitepage',
          'parent_content_id' => $middle_id,
          'order' => 3,
          'params' => '{"title":"","titleCount":"true"}',
      ));
    }

    //
    // Mobile Pages Profile
    // page
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_pages')
            ->where('name = ?', 'sitepage_mobi_view')
            ->limit(1);
    ;
    $info = $select->query()->fetch();

    if (empty($info)) {
      $db->insert('engine4_core_pages', array(
          'name' => 'sitepage_mobi_view',
          'displayname' => 'Mobile Page Profile',
          'title' => 'Mobile Page Profile',
          'description' => 'This is the mobile verison of a listing profile.',
          'custom' => 0
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
          'name' => 'middle',
          'parent_content_id' => $container_id,
          'order' => 2,
          'params' => '',
      ));
      $middle_id = $db->lastInsertId('engine4_core_content');

      // widgets entry

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.title-sitepage',
          'parent_content_id' => $middle_id,
          'order' => 1,
          'params' => '{"title":"","titleCount":"true"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.mainphoto-sitepage',
          'parent_content_id' => $middle_id,
          'order' => 2,
          'params' => '{"title":"","titleCount":"true"}',
      ));


      // middle tabs
      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'core.container-tabs',
          'parent_content_id' => $middle_id,
          'order' => 4,
          'params' => '{"max":"6"}',
      ));
      $tab_middle_id = $db->lastInsertId('engine4_core_content');


      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'seaocore.feed',
          'parent_content_id' => $tab_middle_id,
          'order' => 1,
          'params' => '{"title":"What\'s New","titleCount":"true"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.info-sitepage',
          'parent_content_id' => $tab_middle_id,
          'order' => 2,
          'params' => '{"title":"Info","titleCount":"true"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.overview-sitepage',
          'parent_content_id' => $tab_middle_id,
          'order' => 3,
          'params' => '{"title":"Overview","titleCount":"true"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.location-sitepage',
          'parent_content_id' => $tab_middle_id,
          'order' => 4,
          'params' => '{"title":"Map","titleCount":"true"}',
      ));
    }

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepage')
            ->where('version >= ?', '4.1.6')
            ->where('version < ?', '4.1.7');
    $oldVersion = $select->query()->fetchObject();
    if (!empty($oldVersion)) {
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_core_settings')
              ->where('name = ?', 'sitepage.profile.search')
              ->limit(1);
      $info = $select->query()->fetch();
      if (!empty($info)) {
        if ($info['value'] == 1) {
          $db->update('engine4_seaocore_searchformsetting', array('display' => $info['value']), array('module' => 'sitepage', 'name = ?' => 'profile_type'));
        } else {
          $db->update('engine4_seaocore_searchformsetting', array('display' => $info['value']), array('module' => 'sitepage', 'name = ?' => 'profile_type'));
        }
        $db->delete('engine4_core_settings', array('name = ?' => 'sitepage.profile.search'));
      }
    }

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepage')
            ->where('version < ?', '4.1.7p1');
    $is_latestversion = $select->query()->fetchObject();
    if (!empty($is_latestversion)) {
      $select = new Zend_Db_Select($db);
      $select->from('engine4_activity_actiontypes')->where('type =?', 'sitepage_profile_photo_update')->where('module =?', 'sitepage')->limit(1);
      $fetchInfo = $select->query()->fetch();
      if (empty($fetchInfo)) {
        $db->insert('engine4_activity_actiontypes', array(
            'type' => 'sitepage_profile_photo_update',
            'module' => 'sitepage',
            'body' => '{item:$subject} changed their Page profile photo.',
            'enabled' => 1,
            'displayable' => 3,
            'attachable' => 2,
            'commentable' => 1,
            'shareable' => 1,
            'is_generated' => 1,
        ));
      }

      $select = new Zend_Db_Select($db);
      $select->from('engine4_core_pages')->where('name =?', 'sitepage_index_index')->limit(1);
      $fetchPageId = $select->query()->fetch();
      if (!empty($fetchPageId)) {
        $select = new Zend_Db_Select($db);
        $select = $select->from('engine4_core_content')
                ->where('page_id =?', $fetchPageId['page_id'])
                ->where('type = ?', 'container')
                ->where('name = ?', 'top')
                ->limit(1);
        $container_id = $select->query()->fetch();
        if (!empty($container_id)) {
          $select = new Zend_Db_Select($db);
          $select = $select->from('engine4_core_content')
                  ->where('page_id =?', $fetchPageId['page_id'])
                  ->where('type = ?', 'container')
                  ->where('name = ?', 'middle')
                  ->where('parent_content_id = ?', $container_id['content_id'])
                  ->limit(1);
          $middle_id = $select->query()->fetch();
          if (!empty($middle_id)) {
            $select = new Zend_Db_Select($db);
            $select = $select->from('engine4_core_content')
                    ->where('page_id =?', $fetchPageId['page_id'])
                    ->where('name = ?', 'sitepage.alphabeticsearch-sitepage')
                    ->where('parent_content_id = ?', $middle_id['content_id'])
                    ->limit(1);
            $fetchWidgetContentId = $select->query()->fetchAll();
            if (empty($fetchWidgetContentId)) {
              $db->insert('engine4_core_content', array(
                  'page_id' => $fetchPageId['page_id'],
                  'type' => 'widget',
                  'name' => 'sitepage.alphabeticsearch-sitepage',
                  'parent_content_id' => $middle_id['content_id'],
                  'order' => 4,
                  'params' => '{"title":"","titleCount":"true"}',
              ));
            }
          }
        }
      }

      $adColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_packages LIKE 'ads'")->fetch();
      if (empty($adColumn)) {
        $run_query = $db->query("ALTER TABLE `engine4_sitepage_packages` ADD `ads` BOOL NOT NULL DEFAULT '1'");
        $select = new Zend_Db_Select($db);
        $select
                ->from('engine4_core_modules')
                ->where('name = ?', 'communityad');
        $is_enabled = $select->query()->fetchObject();
        if ($is_enabled) {
          $select = new Zend_Db_Select($db);
          $select
                  ->from('engine4_core_settings')
                  ->where('name = ?', 'sitepage.communityads')
                  ->limit(1);
          $info = $select->query()->fetch();
          if (!empty($info)) {
            $communitadSetting = $info['value'];
            if (!empty($communitadSetting)) {
              $select = new Zend_Db_Select($db);
              $select
                      ->from('engine4_core_settings')
                      ->where('name = ?', 'sitepage.adwithpackage')
                      ->limit(1);
              $info = $select->query()->fetch();
              if (!empty($info)) {
                $showAdWithPackage = $info['value'];
                if (!empty($showAdWithPackage)) {
                  $select = new Zend_Db_Select($db);
                  $select->from('engine4_sitepage_packages')->where('price > ?', 0);
                  $info = $select->query()->fetchAll();
                  foreach ($info as $data) {
                    $db->update('engine4_sitepage_packages', array('ads' => $showAdWithPackage), array('package_id = ?' => $data['package_id']));
                  }
                } else {
                  $select = new Zend_Db_Select($db);
                  $select->from('engine4_sitepage_packages')->where('price > ?', 0);
                  $info = $select->query()->fetchAll();
                  foreach ($info as $data) {
                    $db->update('engine4_sitepage_packages', array('ads' => $showAdWithPackage), array('package_id = ?' => $data['package_id']));
                  }
                  $select = new Zend_Db_Select($db);
                  $select->from('engine4_sitepage_packages')->where('price = ?', 0);
                  $info = $select->query()->fetchAll();
                  foreach ($info as $data) {
                    $db->update('engine4_sitepage_packages', array('ads' => 1), array('package_id = ?' => $data['package_id']));
                  }
                }
                $db->delete('engine4_core_settings', array('name = ?' => 'sitepage.adwithpackage'));
              }
            }
          }
        }
      }
    }

    //REMOVED WIDGET SETTING TAB FROM ADMIN PANEL
    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_modules')
            ->where('name = ?', 'sitepage')
            ->where('version <= ?', '4.1.7p2');
    $is_enabled = $select->query()->fetchObject();
    if (!empty($is_enabled)) {
      $widget_names = array('comment', 'recent', 'likes', 'popular', 'random', 'mostdiscussed', 'usersitepage', 'suggest', 'locations', 'recently', 'recentlyfriend', 'pagelike', 'favourite', 'feature', 'sponserdsitepage');

      foreach ($widget_names as $widget_name) {

        $widget_type = $widget_name;

        $widget_name = 'sitepage.' . $widget_name . '-sitepage';
        $setting_name = 'sitepage.' . $widget_type . '.widgets';

        if ($widget_type == 'locations') {
          $setting_name = 'sitepage.' . 'popular' . '.locations';
          $widget_name = 'sitepage.' . 'popularlocations' . '-sitepage';
        } elseif ($widget_type == 'recently') {
          $setting_name = 'sitepage.' . 'recently' . '.view';
          $widget_name = 'sitepage.' . 'recentview' . '-sitepage';
        } elseif ($widget_type == 'recentlyfriend') {
          $setting_name = 'sitepage.' . 'recentlyfriend' . '_view';
          $widget_name = 'sitepage.' . 'recentfriend' . '-sitepage';
        } elseif ($widget_type == 'pagelike') {
          $setting_name = 'sitepage.' . 'pagelike' . '.view';
          $widget_name = 'sitepage.' . 'page' . '-like';
        } elseif ($widget_type == 'favourite') {
          $setting_name = 'sitepage.' . 'favourite' . '.pages';
          $widget_name = 'sitepage.' . 'favourite' . '-page';
        } elseif ($widget_type == 'suggest') {
          $setting_name = 'sitepage.' . 'suggest' . '.sitepages';
          $widget_name = 'sitepage.' . 'suggestedpage' . '-sitepage';
        } elseif ($widget_type == 'comment') {
          $widget_name = 'sitepage.' . 'mostcommented' . '-sitepage';
        } elseif ($widget_type == 'recent') {
          $widget_name = 'sitepage.' . 'recentlyposted' . '-sitepage';
        } elseif ($widget_type == 'likes') {
          $widget_name = 'sitepage.' . 'mostlikes' . '-sitepage';
        } elseif ($widget_type == 'popular') {
          $widget_name = 'sitepage.' . 'mostviewed' . '-sitepage';
        } elseif ($widget_type == 'mostdiscussed') {
          $widget_name = 'sitepage.' . 'mostdiscussion' . '-sitepage';
        } elseif ($widget_type == 'usersitepage') {
          $widget_name = 'sitepage.' . 'userpage' . '-sitepage';
        } elseif ($widget_type == 'feature') {
          $widget_name = 'sitepage.' . 'slideshow' . '-sitepage';
        } elseif ($widget_type == 'sponserdsitepage') {
          $widget_name = 'sitepage.' . 'sponsored' . '-sitepage';
        }
        $total_items = $db->select()
                ->from('engine4_core_settings', array('value'))
                ->where('name = ?', $setting_name)
                ->limit(1)
                ->query()
                ->fetchColumn();

        if (empty($total_items)) {
          $total_items = '';
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

      // SITEPAGE AJAX BASED TAB HOME PAGE WIDGETS START
      $viewsOfPageDb = $db->select()
              ->from('engine4_core_settings', array('value'))
              ->where('name like ?', 'sitepage.ajax.widgets.layout%')
              ->query()
              ->fetchAll();

      if (count($viewsOfPageDb) > 0) {
        $viewsOfPage = array();
        foreach ($viewsOfPageDb as $value)
          $viewsOfPage[] = $value['value'];
      } else {
        $viewsOfPage = array("0" => "1", "1" => "2", "2" => "3");
      }

      $diffaultView = $db->select()
              ->from('engine4_core_settings', array('value'))
              ->where('name = ?', 'sitepage.ajax.layouts.oder')
              ->limit(1)
              ->query()
              ->fetchColumn();

      if (empty($diffaultView)) {
        $diffaultView = 1;
      }

      $select = new Zend_Db_Select($db);
      $widget_name = 'sitepage.pages-sitepage';
      $select->from('engine4_core_content', array('name', 'params', 'content_id'))->where('name = ?', $widget_name);
      $widgets = $select->query()->fetchAll();
      foreach ($widgets as $widget) {
        $explode_params = explode('}', $widget['params']);
        if (!empty($explode_params[0]) && !strstr($explode_params[0], '"layouts_views"')) {
          $params = $explode_params[0] . ',"layouts_views":' . '["' . join('","', $viewsOfPage) . '"]' . ',"layouts_oder":' . $diffaultView . '}';

          $db->update('engine4_core_content', array('params' => $params), array('content_id = ?' => $widget['content_id'], 'name = ?' => $widget_name));
        }
      }

      $enableTabsDb = $db->select()
              ->from('engine4_core_settings', array('value'))
              ->where('name like ?', 'sitepage.ajax.widgets.list%')
              ->query()
              ->fetchAll();

      if (count($enableTabsDb) > 0) {
        $enableTabs = array();
        foreach ($enableTabsDb as $value)
          $enableTabs[] = $value['value'];
      } else {

        $enableTabs = array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => '5');
      }

      $select = new Zend_Db_Select($db);
      $widget_name = 'sitepage.recently-popular-random-sitepage';
      $select->from('engine4_core_content', array('name', 'params', 'content_id'))->where('name = ?', $widget_name);
      $widgets = $select->query()->fetchAll();
      foreach ($widgets as $widget) {
        $explode_params = explode('}', $widget['params']);
        if (!empty($explode_params[0]) && !strstr($explode_params[0], '"layouts_views"')) {
          $params = $explode_params[0] . ',"layouts_views":' . '["' . join('","', $viewsOfPage) . '"]' . ',"layouts_oder":' . $diffaultView . ',"layouts_tabs":' . '["' . join('","', $enableTabs) . '"]' . ',"recent_order":1,"popular_order":2,"random_order":3,"featured_order":4,"sponosred_order":5,"list_limit":10,"grid_limit":15}';

          $db->update('engine4_core_content', array('params' => $params), array('content_id = ?' => $widget['content_id'], 'name = ?' => $widget_name));
        }
      }
      $db->delete('engine4_core_settings', array('name like ?' => 'sitepage.ajax.widgets.layout%'));
      $db->delete('engine4_core_settings', array('name = ?' => 'sitepage.ajax.layouts.oder'));
      $db->delete('engine4_core_settings', array('name like ?' => 'sitepage.ajax.widgets.list%'));

      // SITEPAGE AJAX BASED TAB HOME PAGE WIDGETS END
    }

    //DROP THE INDEX FROM THE "engine4_sitepage_itemofthedays" TABLE
    $itemofthedayResults = $db->query("SHOW INDEX FROM `engine4_sitepage_itemofthedays` WHERE Key_name = 'itemoftheday_id'")->fetch();
    if (!empty($itemofthedayResults)) {
      $db->query("ALTER TABLE engine4_sitepage_itemofthedays DROP INDEX itemoftheday_id");
    }

    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_albums'")->fetch();
    if (!empty($table_exist)) {
      //ADD THE INDEX FROM THE "engine4_sitepageevent_membership" TABLE
      $ownerIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_albums` WHERE Key_name = 'owner_id'")->fetch();

      if (empty($ownerIdColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepage_albums` ADD INDEX ( `owner_id` );");
      }

      //DROP THE COLUMN FROM THE "engine4_sitepage_albums" TABLE
      $ownerTypeColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_albums LIKE 'owner_type'")->fetch();
      if (!empty($ownerTypeColumn)) {
        $db->query("ALTER TABLE `engine4_sitepage_albums` DROP `owner_type`");
      }

      //DROP THE COLUMN FROM THE "engine4_sitepage_albums" TABLE
      $typeTypeColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_albums LIKE 'type'")->fetch();
      if (!empty($typeTypeColumn)) {
        $db->query("ALTER TABLE `engine4_sitepage_albums` CHANGE `type` `type` ENUM( 'note', 'overview','wall', 'announcements', 'discussions', 'cover' ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;");
      }
    }

    //QUERIES TRANFER FROM UPGRADE FILE OF 4.1.7P2
    $itemTable = $db->query("SHOW TABLES LIKE 'engine4_sitepage_itemofthedays'")->fetch();
    if (!empty($itemTable)) {

      $titleColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_itemofthedays LIKE 'title'")->fetch();
      if (!empty($titleColumn)) {
        $db->query("ALTER TABLE `engine4_sitepage_itemofthedays` DROP `title`");
      }

      $pageIdColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_itemofthedays LIKE 'page_id'")->fetch();
      $endTimeColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_itemofthedays LIKE 'endtime'")->fetch();
      $dateColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_itemofthedays LIKE 'date'")->fetch();

      $endDateColoum = $db->query("SHOW COLUMNS FROM engine4_sitepage_itemofthedays LIKE 'end_date'")->fetch();
      $startDateColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_itemofthedays LIKE 'start_date'")->fetch();
      //$dateColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_itemofthedays` WHERE Key_name = 'date'")->fetch();
      //$endTimeColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_itemofthedays` WHERE Key_name = 'endtime'")->fetch();
      $endDateColoumIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_itemofthedays` WHERE Key_name = 'end_date'")->fetch();
      $startDateColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_itemofthedays` WHERE Key_name = 'start_date'")->fetch();


//      if (!empty($dateColumn) && empty($dateColumnIndex)) {
//        $db->query("ALTER TABLE `engine4_sitepage_itemofthedays` ADD INDEX ( `date` );");
//      }
//
//      if (!empty($endTimeColumn) && empty($endTimeColumnIndex)) {
//        $db->query("ALTER TABLE `engine4_sitepage_itemofthedays` ADD INDEX ( `endtime` );");
//      }

      if (!empty($endDateColoum) && empty($endDateColoumIndex)) {
        $db->query("ALTER TABLE `engine4_sitepage_itemofthedays` ADD INDEX ( `end_date` );");
      }

      if (!empty($startDateColumn) && empty($startDateColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepage_itemofthedays` ADD INDEX ( `start_date` );");
      }

      if (!empty($pageIdColumn) && !empty($endTimeColumn) && !empty($dateColumn)) {
        $db->query("ALTER TABLE `engine4_sitepage_itemofthedays` CHANGE `page_id` `resource_id` INT( 11 ) NOT NULL ,
					CHANGE `endtime` `end_date` DATE NOT NULL, CHANGE `date` `start_date` DATE NOT NULL");
      }

      $resourceTypeColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_itemofthedays LIKE 'resource_type'")->fetch();

      $resourceTypeColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_itemofthedays` WHERE Key_name = 'resource_type'")->fetch();

      if (empty($resourceTypeColumn)) {
        $db->query("ALTER TABLE `engine4_sitepage_itemofthedays` ADD `resource_type` VARCHAR( 64 ) NOT NULL");
      }

      if (!empty($resourceTypeColumn) && empty($resourceTypeColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepage_itemofthedays` ADD INDEX (`resource_type`)");
        $db->query("UPDATE `engine4_sitepage_itemofthedays` SET `resource_type` = 'sitepage_page' WHERE `engine4_sitepage_itemofthedays` .`resource_type` = ''");
      }
    }

    $pageTable = $db->query("SHOW TABLES LIKE 'engine4_sitepage_pages'")->fetch();
    $networkPrivacyColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_pages LIKE 'networks_privacy'")->fetch();
    if (!empty($pageTable)) {
      if (empty($networkPrivacyColumn)) {
        $db->query("ALTER TABLE `engine4_sitepage_pages` ADD `networks_privacy` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL");
      }

      $subsubCategoryIdColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_pages LIKE 'subsubcategory_id'")->fetch();
      if (empty($subsubCategoryIdColumn)) {
        $db->query("ALTER TABLE `engine4_sitepage_pages` ADD `subsubcategory_id` INT( 11 ) NOT NULL");
      }
    }

    $categoryTable = $db->query("SHOW TABLES LIKE 'engine4_sitepage_categories'")->fetch();
    if (!empty($categoryTable)) {

      $userIdColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_categories LIKE 'user_id'")->fetch();
      if (!empty($userIdColumn)) {
        $db->query("ALTER TABLE `engine4_sitepage_categories` DROP `user_id`");
      }

      $subcatDependencyColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_categories LIKE 'subcat_dependency'")->fetch();
      if (empty($subcatDependencyColumn)) {
        $db->query("ALTER TABLE `engine4_sitepage_categories` ADD `subcat_dependency` INT( 11 ) NOT NULL");
      }
    }

    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_claims'")->fetch();
    if (!empty($table_exist)) {
      //ADD THE INDEX FROM THE "engine4_sitepage_claims" TABLE
      $pageIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_claims` WHERE Key_name = 'page_id'")->fetch();

      if (empty($pageIdColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepage_claims` ADD INDEX ( `page_id` );");
      }

      //ADD THE INDEX FROM THE "engine4_sitepage_claims" TABLE
      $userIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_claims` WHERE Key_name = 'user_id'")->fetch();

      if (empty($userIdColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepage_claims` ADD INDEX ( `user_id` );");
      }
    }

    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_contentpages`'")->fetch();
    if (!empty($table_exist)) {
      //ADD THE INDEX FROM THE "engine4_sitepage_contentpages" TABLE
      $pageIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_contentpages` WHERE Key_name = 'page_id'")->fetch();

      if (empty($pageIdColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepage_contentpages` ADD INDEX ( `page_id` );");
      }

      //ADD THE INDEX FROM THE "engine4_sitepage_contentpages" TABLE
      $userIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_contentpages` WHERE Key_name = 'user_id'")->fetch();

      if (empty($userIdColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepage_contentpages` ADD INDEX ( `user_id` );");
      }

      //ADD THE INDEX FROM THE "engine4_sitepage_contentpages" TABLE
      $nameColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_contentpages` WHERE Key_name = 'name'")->fetch();

      if (empty($nameColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepage_contentpages` ADD INDEX ( `name` );");
      }
    }

    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_favourites'")->fetch();
    if (!empty($table_exist)) {
      //ADD THE INDEX FROM THE "engine4_sitepage_favourites" TABLE
      $pageIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_favourites` WHERE Key_name = 'page_id'")->fetch();

      if (empty($pageIdColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepage_favourites` ADD INDEX ( `page_id` );");
      }

      //ADD THE INDEX FROM THE "engine4_sitepage_favourites" TABLE
      $ownerIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_favourites` WHERE Key_name = 'owner_id'")->fetch();

      if (empty($ownerIdColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepage_favourites` ADD INDEX ( `owner_id` );");
      }
    }

    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_manageadmins'")->fetch();
    if (!empty($table_exist)) {
      //ADD THE INDEX FROM THE "engine4_sitepage_manageadmins" TABLE
      $pageIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_manageadmins` WHERE Key_name = 'page_id'")->fetch();

      if (empty($pageIdColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepage_manageadmins` ADD INDEX ( `page_id` );");
      }
    }

    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_pagestatistics'")->fetch();
    if (!empty($table_exist)) {
      //ADD THE INDEX FROM THE "engine4_sitepage_pagestatistics" TABLE
      $pageIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_pagestatistics` WHERE Key_name = 'page_id'")->fetch();

      if (empty($pageIdColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepage_pagestatistics` ADD INDEX ( `page_id` );");
      }

      //ADD THE INDEX FROM THE "engine4_sitepage_pagestatistics" TABLE
      $viewerIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_pagestatistics` WHERE Key_name = 'viewer_id'")->fetch();

      if (empty($viewerIdColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepage_pagestatistics` ADD INDEX ( `viewer_id` );");
      }
    }

    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_listmemberclaims'")->fetch();
    if (!empty($table_exist)) {
      //ADD THE INDEX FROM THE "engine4_sitepage_listmemberclaims" TABLE
      $userIdColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_listmemberclaims` WHERE Key_name = 'user_id'")->fetch();

      if (empty($userIdColumnIndex)) {
        $db->query("ALTER TABLE `engine4_sitepage_listmemberclaims` ADD INDEX ( `user_id` );");
      }
    }

    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_content'")->fetch();
    if (!empty($table_exist)) {
      $widgetAdminColumn = $db->query("SHOW COLUMNS FROM `engine4_sitepage_content` LIKE 'widget_admin'")->fetch();
      if (empty($widgetAdminColumn)) {
        $db->query("ALTER TABLE `engine4_sitepage_content` ADD `widget_admin` BOOL NOT NULL DEFAULT '1'");
      }

      if (!empty($widgetAdminColumn)) {
        $db->query("ALTER TABLE `engine4_sitepage_content` CHANGE `widget_admin` `widget_admin` TINYINT( 1 ) NOT NULL DEFAULT '1'");
      }
    }

    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_modules')
            ->where('name = ?', 'sitepage')
            ->where('version <= ?', '4.2.3');
    $is_old_version = $select->query()->fetchObject();
    if ($is_old_version) {
      $select = new Zend_Db_Select($db);
      $select->from('engine4_sitepage_admincontent');
      $adminContentResults = $select->query()->fetchAll();
      if (!empty($adminContentResults)) {
        $contentArray = array();
        foreach ($adminContentResults as $value) {
          if (!in_array($value['name'], array('core.html-block', 'core.ad-campaign'))) {
            $db->update('engine4_sitepage_content', array('widget_admin' => 1), array('name = ?' => $value['name']));
          } else {
            $contentArray[] = $value;
          }
        }
        foreach ($contentArray as $value) {
          $db->update('engine4_sitepage_content', array('widget_admin' => 1), array('name = ?' => $value['name'], 'params = ?' => $value['params']));
        }
      }
    }

    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_admincontent'")->fetch();
    if (!empty($table_exist)) {
      $defaultColumn = $db->query("SHOW COLUMNS FROM engine4_sitepage_admincontent LIKE 'default_admin_layout'")->fetch();
      if (empty($defaultColumn)) {
        $db->query("ALTER TABLE `engine4_sitepage_admincontent` ADD `default_admin_layout` BOOL NOT NULL DEFAULT '0'");
      }
    }

    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_activity_actiontypes'")->fetch();
    if (!empty($table_exist)) {
      $widgetAdminColumn = $db->query("SHOW COLUMNS FROM `engine4_activity_actiontypes` LIKE 'is_object_thumb'")->fetch();
      if (empty($widgetAdminColumn)) {
        $db->query("ALTER TABLE `engine4_activity_actiontypes` ADD `is_object_thumb` BOOL NOT NULL DEFAULT '0'");
      }
    }

    $select = new Zend_Db_Select($db);
    $select->from('engine4_core_settings', array('value'))
            ->where('name = ?', 'sitepage.feed.type');
    $feedType = $select->query()->fetchAll();
    if (!empty($feedType) && $feedType[0]['value'] == 1) {

      $select = new Zend_Db_Select($db);
      $select->from('engine4_core_modules')->where('name = ?', 'sitepage')->where('version <= ?', '4.2.0p1');
      $is_enabled = $select->query()->fetchObject();
      if (!empty($is_enabled)) {
        $select = new Zend_Db_Select($db);
        $select->from('engine4_activity_actions')->where('subject_type = ?', 'sitepage_page');

        $resultAction = $select->query()->fetchAll();
        if (!empty($resultAction)) {
          foreach ($resultAction as $result) {

            $db->query("UPDATE `engine4_activity_actions` SET `subject_type` = '" . $result['object_type'] . "',
        `subject_id` = " . $result['object_id'] . ", `object_type` = '" . $result['subject_type'] . "',
        `object_id` = " . $result['subject_id'] . " WHERE `engine4_activity_actions`.`action_id` =
        " . $result['action_id'] . " ;");

            $db->query("UPDATE `engine4_activity_stream` SET `subject_type` = '" . $result['object_type'] . "',
        `subject_id` = " . $result['object_id'] . ", `object_type` = '" . $result['subject_type'] . "',
        `object_id` = " . $result['subject_id'] . " WHERE `engine4_activity_stream`.`action_id` =
        " . $result['action_id'] . " ;");
          }
        }

        $select = new Zend_Db_Select($db);
        $select->from('engine4_activity_stream')->where('object_type = ?', 'sitepage_page')->group('action_id');
        $resultStreams = $select->query()->fetchAll();
        if (!empty($resultStreams)) {
          foreach ($resultStreams as $result) {

            $db->query("INSERT IGNORE INTO `engine4_activity_stream` (`target_type`, `target_id`,
        `subject_type`, `subject_id`, `object_type`, `object_id`, `type`, `action_id`) VALUES ('sitepage_page',
        " . $result['object_id'] . " , '" . $result['subject_type'] . "', " . $result['subject_id'] . ",
        '" . $result['object_type'] . "', " . $result['object_id'] . ", '" . $result['type'] . "', " .
                    $result['action_id'] . ");");
          }
        }
      }
    }

    //ADD NEW COLUMN IN engine4_sitepage_imports TABLE
    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_imports'")->fetch();
    if (!empty($table_exist)) {

      $column_exist = $db->query("SHOW COLUMNS FROM engine4_sitepage_imports LIKE 'userclaim'")->fetch();
      if (empty($column_exist)) {
        $db->query("ALTER TABLE `engine4_sitepage_imports` ADD `userclaim` TINYINT( 1 ) NOT NULL DEFAULT '0'");
      }
    }

    //CHECK THAT foursquare_text COLUMN EXIST OR NOT IN PAGE TABLE
    $column_exist = $db->query("SHOW COLUMNS FROM engine4_sitepage_pages LIKE 'foursquare_text'")->fetch();
    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_sitepage_pages'")->fetch();
    if (!empty($column_exist) && !empty($table_exist)) {

      $column_type = $db->query("SELECT data_type FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'engine4_sitepage_pages' AND COLUMN_NAME = 'foursquare_text'")->fetch();

      if ($column_type != 'tinyint') {

        //FETCH PAGES
        $pages = $db->select()->from('engine4_sitepage_pages', array('foursquare_text', 'page_id'))->query()->fetchAll();

        if (!empty($pages)) {
          foreach ($pages as $page) {
            $page_id = $page['page_id'];
            $foursquare_text = $page['foursquare_text'];

            if (!empty($page_id)) {

              //UPDATE FOURSQUARE TEXT VALUE
              if (!empty($foursquare_text)) {
                $db->update('engine4_sitepage_pages', array('foursquare_text' => 1), array('page_id = ?' => $page_id));
              } else {
                $db->update('engine4_sitepage_pages', array('foursquare_text' => 0), array('page_id = ?' => $page_id));
              }
            }
          }
        }
      }

      $db->query("ALTER TABLE `engine4_sitepage_pages` CHANGE `foursquare_text` `foursquare_text` TINYINT(1) NULL DEFAULT '0'");
    }

    //START SOCIAL SHARE WIDGET WORK 
    //CHECK PLUGIN VERSION
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitepage')
            ->where('version < ?', '4.2.1');
    $is_enabled_module = $select->query()->fetchObject();

    if (!empty($is_enabled_module)) {

      $social_share_default_code = '{"title":"Social Share","titleCount":true,"code":"<div class=\"addthis_toolbox addthis_default_style \">\r\n<a class=\"addthis_button_preferred_1\"><\/a>\r\n<a class=\"addthis_button_preferred_2\"><\/a>\r\n<a class=\"addthis_button_preferred_3\"><\/a>\r\n<a class=\"addthis_button_preferred_4\"><\/a>\r\n<a class=\"addthis_button_preferred_5\"><\/a>\r\n<a class=\"addthis_button_compact\"><\/a>\r\n<a class=\"addthis_counter addthis_bubble_style\"><\/a>\r\n<\/div>\r\n<script type=\"text\/javascript\">\r\nvar addthis_config = {\r\n          services_compact: \"facebook, twitter, linkedin, google, digg, more\",\r\n          services_exclude: \"print, email\"\r\n}\r\n<\/script>\r\n<script type=\"text\/javascript\" src=\"http:\/\/s7.addthis.com\/js\/250\/addthis_widget.js\"><\/script>","nomobile":"","name":"sitepage.socialshare-sitepage"}';

      $db->update('engine4_core_content', array('params' => $social_share_default_code,), array('name =?' => 'sitepage.socialshare-sitepage'));
      $db->update('engine4_sitepage_content', array('params' => $social_share_default_code,), array('name =?' => 'sitepage.socialshare-sitepage'));
    }

    //MAKING A COLOMN IN THE SITEPAGE_PAGE TABLE

    $type_array = $db->query("SHOW COLUMNS FROM engine4_sitepage_pages LIKE 'fbpage_url' ")->fetch();
    if (empty($type_array)) {
      $run_query = $db->query("ALTER TABLE  `engine4_sitepage_pages` ADD  `fbpage_url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL");
    }
    //END SOCIAL SHARE WIDGET WORK

    $db->query('UPDATE  `engine4_core_content` SET  `name` =  "seaocore.like-button" WHERE  `engine4_core_content`.`name` ="sitepage.page-like-button";');
    $db->query('UPDATE  `engine4_sitepage_content` SET  `name` =  "seaocore.like-button" WHERE  `engine4_sitepage_content`.`name` ="sitepage.page-like-button";');

    $db->query('UPDATE  `engine4_core_content` SET  `name` =  "seaocore.people-like" WHERE  `engine4_core_content`.`name` ="sitepage.page-like";');
    $db->query('UPDATE  `engine4_sitepage_content` SET  `name` =  "seaocore.people-like" WHERE  `engine4_sitepage_content`.`name` ="sitepage.page-like";');


    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_pages')
            ->where('name = ?', 'sitepage_index_pinboard_browse')
            ->limit(1);
    $info = $select->query()->fetch();

    if (empty($info)) {
      $db->insert('engine4_core_pages', array(
          'name' => 'sitepage_index_pinboard_browse',
          'displayname' => 'Browse Pages’ Pinboard View',
          'title' => 'Browse Pages’ Pinboard View',
          'description' => 'Browse Pages’ Pinboard View',
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
          'name' => 'sitepage.horizontal-search',
          'parent_content_id' => $middle_id,
          'order' => 2,
          'params' => '{"title":"","titleCount":"true","street":"1","city":"1","state":"1","country":"1","browseredirect":"pinboard"}',
      ));

      $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type' => 'widget',
          'name' => 'sitepage.pinboard-browse',
          'parent_content_id' => $middle_id,
          'order' => 3,
          'params' => '{"title":"","titleCount":true,"postedby":"1","showoptions":["likeCount","followCount","memberCount"],"detactLocation":"0","defaultlocationmiles":"1000","itemWidth":"274","withoutStretch":"0","itemCount":"12","show_buttons":["comment","like","share","facebook","twitter","pinit","tellAFriend"],"truncationDescription":"100","nomobile":"0"}',
      ));
    }

//     $db->delete('engine4_core_content', array('name =?' => 'sitepage.thumbphoto-sitepage'));
//     $db->delete('engine4_sitepage_admincontent', array('name =?' => 'sitepage.thumbphoto-sitepage'));
//     $db->delete('engine4_sitepage_content', array('name =?' => 'sitepage.thumbphoto-sitepage'));
// 		$select = new Zend_Db_Select($db);
// 		$select
// 						->from('engine4_core_pages', array('page_id'))
// 						->where('name = ?', 'sitepage_index_view');
// 		$corepageObject = $select->query()->fetchObject();
// 		$select = new Zend_Db_Select($db);
// 		$select
// 						->from('engine4_core_settings', array('value'))
// 						->where('name = ?', 'sitepage.core.cover.layout');
// 		$coreSettingsObject = $select->query()->fetchObject();
// 
// 		if (!empty($corepageObject) && empty($coreSettingsObject)) {
// 			$page_id = $corepageObject->page_id;
// 			$select = new Zend_Db_Select($db);
// 
// 			$select
// 							->from('engine4_core_content', array('name'))
// 							->where('name=?', 'left')->where('name=?', 'right')->where('page_id=?', $page_id);
// 			$corepageObject = $select->query()->fetchObject();
// 
// 			if(empty($corepageObject)) {
// 				$select = new Zend_Db_Select($db);
// 				$select
// 								->from('engine4_core_content', array('name'))
// 								->where('name = ?', 'left')
// 								->where('page_id = ?', $page_id);
// 				$corepageObject = $select->query()->fetchObject();
// 				if($corepageObject) {
// 					$db->update('engine4_core_content', array('name' => 'right'), array('name = ?' => 'left', 'page_id =?' => $page_id));
// 				}
// 				$db->delete('engine4_core_content', array('name =?' => 'sitepage.mainphoto-sitepage'));
// 				$db->delete('engine4_core_content', array('name =?' => 'sitepagemember.profile-sitepagemembers-announcements'));
// 				$db->delete('engine4_core_content', array('name =?' => 'seaocore.like-buttons'));
// 				$db->delete('engine4_core_content', array('name =?' => 'seaocore.seaocore-follow'));
// 				$db->delete('engine4_core_content', array('name =?' => 'facebookse.facebookse-commonlike'));
// 				$db->delete('engine4_core_content', array('name =?' => 'sitepage.title-sitepage'));
// 				$db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('sitepage.core.cover.layout', 1);");
// 			}
// 		}
		$select = new Zend_Db_Select($db);
		$select
           ->from('engine4_core_modules')
           ->where('name = ?', 'sitetagcheckin')
           ->where('enabled = ?', 1);
		$is_sitetagcheckin_object = $select->query()->fetchObject();
		if(!empty($is_sitetagcheckin_object)) {
			$db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
("sitetagcheckin_spal_photo_new", "sitetagcheckin", "{item:$object} added {var:$count} photo(s) to the album {var:$linked_album_title} - {var:$prefixadd} {var:$location}.", 1, 5, 1, 3, 1, 1)');
    }



    
//     $select = new Zend_Db_Select($db);
//     $select
//             ->from('engine4_core_modules')
//             ->where('name = ?', 'sitepage')
//             ->where('version <= ?', '4.6.0p1');
//     $is_enabled = $select->query()->fetchObject();
//     if (!empty($is_enabled)) {

    // $db->query("DROP TABLE IF EXISTS `engine4_sitepage_mobileadmincontent`;");
     $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitepage_mobileadmincontent` (
  `mobileadmincontent_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(11) unsigned NOT NULL,
  `type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'widget',
  `name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `parent_content_id` int(11) unsigned DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT '1',
  `params` text COLLATE utf8_unicode_ci,
  `attribs` text COLLATE utf8_unicode_ci,
  `module` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `default_admin_layout` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mobileadmincontent_id`),
  KEY `page_id` (`page_id`,`order`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;");

    // $db->query("DROP TABLE IF EXISTS `engine4_sitepage_mobilecontent`;");
     $db->query("CREATE TABLE IF NOT EXISTS `engine4_sitepage_mobilecontent` (
  `mobilecontent_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mobilecontentpage_id` int(11) unsigned NOT NULL,
  `type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'widget',
  `name` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `parent_content_id` int(11) unsigned DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT '1',
  `params` text COLLATE utf8_unicode_ci,
  `attribs` text COLLATE utf8_unicode_ci,
  `module` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `widget_admin` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`mobilecontent_id`),
  KEY `page_id` (`mobilecontentpage_id`,`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");

  //$db->query("DROP TABLE IF EXISTS `engine4_sitepage_mobilecontentpages`;");
	$db->query("CREATE TABLE IF NOT EXISTS `engine4_sitepage_mobilecontentpages` (
  `mobilecontentpage_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `page_id` int(11) unsigned NOT NULL,
  `name` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `displayname` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `keywords` text COLLATE utf8_unicode_ci NOT NULL,
  `custom` tinyint(1) NOT NULL DEFAULT '1',
  `fragment` tinyint(1) NOT NULL DEFAULT '0',
  `layout` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `view_count` int(11) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`mobilecontentpage_id`),
  KEY `page_id` (`page_id`),
  KEY `user_id` (`user_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");

			$select = new Zend_Db_Select($db);
			$select
            ->from('engine4_core_modules')
            ->where('name = ?', 'sitemobile')
            ->where('enabled = ?', 1);
      $is_sitemobile_object = $select->query()->fetchObject();
      if($is_sitemobile_object)  {
				include APPLICATION_PATH . "/application/modules/Sitepage/controllers/license/mobileLayoutCreation.php";
      }
   // }

			$db->query('INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`,`is_object_thumb`) VALUES ("sitepage_admin_profile_photo", "sitepage", "{item:$object} updated a new profile photo.", 1, 3, 2, 1, 1, 1, 1);');

			//MAKE WIDGITIZE PAGE FOR THE PAGES I LIKE AND PAGES I JOINED AND CREATE PAGE AND EDIT PAGE AND EDT.
			$this->makeWidgitizePage('sitepage_index_manage', 'Directory / Pages - Manage Pages', 'My Pages', 'This page lists a user\'s Pages\'s.');
			
			$this->makeWidgitizePage('sitepage_index_edit', 'Directory / Pages - Edit Page', 'Edit Page', 'This is page edit page.');
			
			$this->makeWidgitizePage('sitepage_index_create', 'Directory / Pages - Create Page', 'Create new Page', 'This is page create page.');
			
			$this->makeWidgitizePage('sitepage_like_my-joined', 'Directory / Pages - Manage Page (Pages I\'ve Joined)', 'Pages I\'ve Joined', 'This page lists a user\'s Pages\'s which user\'s have joined.');
			
      	$this->makeWidgitizePage('sitepage_like_mylikes', 'Directory / Pages - Manage Page (Pages I Like)', 'Pages I Like', 'This page lists a user\'s Pages\'s which user\'s likes.');
      	
			$this->makeWidgitizePage('sitepage_manageadmin_my-pages', 'Directory / Pages - Manage Page (Pages I Admin)', 'Pages I Admin', 'This page lists a user\'s Pages\'s of which user\'s is admin.');


			$select = new Zend_Db_Select($db);
			$select
						->from('engine4_core_modules')
						->where('name = ?', 'siteevent')
						->where('enabled = ?', 1);
			$is_siteevent_object = $select->query()->fetchObject();
			if($is_siteevent_object)  {
				$select = new Zend_Db_Select($db);
				$select
							->from('engine4_core_settings')
							->where('name = ?', 'sitepage.isActivate')
							->where('value = ?', 1); 
				$sitepage_isActivate_object = $select->query()->fetchObject();
				if($sitepage_isActivate_object) {
					$db->query('INSERT IGNORE INTO `engine4_activity_notificationtypes` (`type`, `module`, `body`, `handler`) VALUES("siteevent_page_host", "siteevent", \'{item:$subject} has made your page {var:$page} host of the event {itemSeaoChild:$object:siteevent_occurrence:$occurrence_id}.\', "");');
					$db->query('INSERT IGNORE INTO `engine4_core_mailtemplates` ( `type`, `module`, `vars`) VALUES("SITEEVENT_PAGE_HOST", "siteevent", "[host],[email],[sender],[event_title_with_link],[event_url],[page_title_with_link]");');
					$itemMemberTypeColumn = $db->query("SHOW COLUMNS FROM `engine4_siteevent_modules` LIKE 'item_membertype'")->fetch();
					if (empty($itemMemberTypeColumn)) {
						$db->query("ALTER TABLE `engine4_siteevent_modules` ADD `item_membertype` VARCHAR( 255 ) NOT NULL AFTER `item_title`");
					}
					$db->query("INSERT IGNORE INTO `engine4_siteevent_modules` (`item_type`, `item_id`, `item_module`, `enabled`, `integrated`, `item_title`, `item_membertype`) VALUES ('sitepage_page', 'page_id', 'sitepage', '0', '0', 'Page Events', 'a:3:{i:0;s:14:\"contentmembers\";i:1;s:18:\"contentlikemembers\";i:2;s:20:\"contentfollowmembers\";}')");
					$db->query('INSERT IGNORE INTO `engine4_core_menuitems` ( `name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES("sitepage_admin_main_manage", "siteevent", "Manage Events", "", \'{"uri":"admin/siteevent/manage/index/contentType/sitepage_page/contentModule/sitepage"}\', "sitepage_admin_main", "", 1, 0, 24);');
					$db->query('INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( "siteevent.event.leader.owner.sitepage.page", "1");');
				}
			}
		
    $db->query('UPDATE `engine4_activity_notificationtypes` SET `body` = \'{item:$subject} has liked {item:$object}.\' WHERE `engine4_activity_notificationtypes`.`type` = "sitepage_contentlike" LIMIT 1 ;');
    
    $db->query('UPDATE `engine4_activity_notificationtypes` SET `body` = \'{item:$subject} has commented on {item:$object}.\' WHERE `engine4_activity_notificationtypes`.`type` = "sitepage_contentcomment" LIMIT 1 ;');
    
    $categoriesTable = $db->query('SHOW TABLES LIKE \'engine4_sitepage_categories\'')->fetch();
		if (!empty($categoriesTable)) {
 
			$catDependencyIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_categories` WHERE Key_name = 'cat_dependency'")->fetch();     
      if(empty($catDependencyIndex)) {
        $db->query("ALTER TABLE `engine4_sitepage_categories` ADD INDEX ( `cat_dependency` )");
      }    
      
			$subcatDependencyIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_categories` WHERE Key_name = 'subcat_dependency'")->fetch();     
      if(empty($subcatDependencyIndex)) {
        $db->query("ALTER TABLE `engine4_sitepage_categories` ADD INDEX ( `subcat_dependency` )");
      }       
    }   
    
    $favouritesTable = $db->query('SHOW TABLES LIKE \'engine4_sitepage_favourites\'')->fetch();
		if (!empty($favouritesTable)) {
			$pageIdForIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_favourites` WHERE Key_name = 'page_id_for'")->fetch();     
      if(empty($pageIdForIndex)) {
        $db->query("ALTER TABLE `engine4_sitepage_favourites` ADD INDEX ( `page_id_for` )");
      }    
    }  
    
    $pagesTable = $db->query('SHOW TABLES LIKE \'engine4_sitepage_pages\'')->fetch();
		if (!empty($pagesTable)) {
			$categoryIdIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_pages` WHERE Key_name = 'category_id'")->fetch();     
      if(empty($categoryIdIndex)) {
        $db->query("ALTER TABLE `engine4_sitepage_pages` ADD INDEX ( `category_id` )");
      }

			$parentIdIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_pages` WHERE Key_name = 'parent_id'")->fetch();     
      if(empty($parentIdIndex)) {
        $db->query("ALTER TABLE `engine4_sitepage_pages` ADD INDEX ( `parent_id` )");
      }

			

			$profileTypeIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_pages` WHERE Key_name = 'profile_type'")->fetch();     
      if(empty($profileTypeIndex)) {
        $db->query("ALTER TABLE `engine4_sitepage_pages` ADD INDEX ( `profile_type` )");
      }

			$featuredIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_pages` WHERE Key_name = 'featured'")->fetch();
			$sponsoredIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_pages` WHERE Key_name = 'sponsored'")->fetch();     
      if(empty($featuredIndex) && empty($sponsoredIndex)) {
				$db->query("ALTER TABLE `engine4_sitepage_pages` ADD INDEX ( `featured` )");
				$db->query("ALTER TABLE `engine4_sitepage_pages` ADD INDEX ( `sponsored` )");
        $db->query("ALTER TABLE `engine4_sitepage_pages` ADD INDEX featured_sponsored ( `featured`, `sponsored` )");
      }

      $searchIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_pages` WHERE Key_name = 'closed'")->fetch();
			if(empty($searchIndex)) {
				$db->query("ALTER TABLE `engine4_sitepage_pages` ADD INDEX closed ( `search`,`closed`,`approved`,`declined`,`draft` )");
			}           
    }  
    
    $profilemapsTable = $db->query('SHOW TABLES LIKE \'engine4_sitepage_profilemaps\'')->fetch();
		if (!empty($profilemapsTable)) {
			$categoryIdIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_profilemaps` WHERE Key_name = 'category_id'")->fetch();     
      if(empty($categoryIdIndex)) {
        $db->query("ALTER TABLE `engine4_sitepage_profilemaps` ADD INDEX ( `category_id` )");
      }    
    }    
    
    $itemTable = $db->query('SHOW TABLES LIKE \'engine4_sitepage_itemofthedays\'')->fetch();
		if (!empty($itemTable)) {    
        $dateColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_itemofthedays` WHERE Key_name = 'date'")->fetch();
        $endTimeColumnIndex = $db->query("SHOW INDEX FROM `engine4_sitepage_itemofthedays` WHERE Key_name = 'endtime'")->fetch();
        if (!empty($dateColumnIndex)) {
          $db->query("ALTER TABLE `engine4_sitepage_itemofthedays` DROP INDEX `date`;");
        }

        if (!empty($endTimeColumnIndex)) {
          $db->query("ALTER TABLE `engine4_sitepage_itemofthedays` DROP INDEX `endtime`;");
        }     
    }    
    
    parent::onInstall();
    
    $this->runCustomQueries();
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
			$db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`) VALUES ('sitepage','1')");
			$select = new Zend_Db_Select($db);
			$select
							->from('engine4_sitemobile_modules')
							->where('name = ?', 'sitepage')
							->where('integrated = ?', 0);
			$is_sitemobile_object = $select->query()->fetchObject();
      if($is_sitemobile_object)  {
				$actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
				$controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
				if($controllerName == 'manage' && $actionName == 'install') {
          $view = new Zend_View();
					$baseUrl = ( !empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://':'http://') .  $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
					$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
					$redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/sitepage/integrated/0/redirect/install');
				} 
      }
    }
    else {
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

  function onDisable() {
    $db = $this->getDb();

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules', array('name'))
            ->where('enabled = ?', 1);
    $moduleData = $select->query()->fetchAll();

    $subModuleArray = array("sitepagealbum", "sitepagebadge", "sitepagediscussion", "sitepagedocument", "sitepageevent", "sitepageform", "sitepageinvite", "sitepagenote", "sitepageoffer", "sitepagepoll", "sitepagereview", "sitepagevideo", "sitepagemusic", "sitepagewishlist", "sitepageadmincontact", "sitepageurl");

    foreach ($moduleData as $key => $moduleName) {
      if (in_array($moduleName['name'], $subModuleArray)) {
        $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
        $error_msg1 = Zend_Registry::get('Zend_Translate')->_('Note: Please disable all the integrated sub-modules of Pages Plugin before disabling the Pages Plugin itself.');
        echo "<div style='background-color: #E9F4FA;border-radius:7px 7px 7px 7px;float:left;overflow: hidden;padding:10px;'><div style='background:#FFFFFF;border:1px solid #D7E8F1;overflow:hidden;padding:20px;'><span style='color:red'>$error_msg1</span><br/> <a href='" . $base_url . "/manage'>Click here</a> to go Manage Packages.</div></div>";
        die;
      }
    }

    parent::onDisable();
  }
  
  public function makeWidgitizePage($pagename, $displayname, $title, $description) {
  
    $db = $this->getDb();
		//Create a page for the edit page.
		$select = new Zend_Db_Select($db);
		$select
						->from('engine4_core_pages')
						->where('name = ?', "$pagename")
						->limit(1);
		$info = $select->query()->fetch();

		// insert if it doesn't exist yet
		if (empty($info)) {
			// Insert page
			$db->insert('engine4_core_pages', array(
					'name' => $pagename,
					'displayname' => $displayname,
					'title' => $title,
					'description' => $description,
					'custom' => 0,
			));
			$page_id = $db->lastInsertId();

			// Insert main
			$db->insert('engine4_core_content', array(
					'type' => 'container',
					'name' => 'main',
					'page_id' => $page_id,
					'order' => 1,
			));
			$main_id = $db->lastInsertId();

			// Insert main-middle
			$db->insert('engine4_core_content', array(
					'type' => 'container',
					'name' => 'middle',
					'page_id' => $page_id,
					'parent_content_id' => $main_id,
			));
			$main_middle_id = $db->lastInsertId();

			// Insert content
			$db->insert('engine4_core_content', array(
					'type' => 'widget',
					'name' => 'core.content',
					'page_id' => $page_id,
					'parent_content_id' => $main_middle_id,
					'order' => 1,
			));
		}
  }
  
  private function runCustomQueries() {
    $db = $this->getDb();

    $path = $this->_operation->getPrimaryPackage()->getBasePath() . '/'
            . $this->_operation->getPrimaryPackage()->getPath() . '/'
            . 'settings/custom-queries';

    $files = array(
        'custom.sql' => function() {
          return true;
        }
    );
    
    $db->beginTransaction();

    foreach ($files as $file => $callback) {
      if (call_user_func($callback) && file_exists($path . '/' . $file)) {
        $contents = file_get_contents($path . '/' . $file);
        foreach (Engine_Package_Utilities::sqlSplit($contents) as $sqlFragment) {
          try {
            $db->getConnection()->query($sqlFragment);
          } catch (Exception $e) {
            return $this->_error('Query failed with error: ' . $e->getMessage());
          }
        }
      }
    }
    $db->commit();
  }
}

?>