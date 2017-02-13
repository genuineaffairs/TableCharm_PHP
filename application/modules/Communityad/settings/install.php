<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Install.php  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Installer extends Engine_Package_Installer_Module {

  function onPreInstall() {

    $getErrorMsg = $this->getVersion();
    if (!empty($getErrorMsg)) {
      return $this->_error($getErrorMsg);
    }

    $PRODUCT_TYPE = 'communityad';
    $PLUGIN_TITLE = 'Communityad';
    $PLUGIN_VERSION = '4.8.0';
    $PLUGIN_CATEGORY = 'plugin';
    $PRODUCT_DESCRIPTION = 'Communityad Plugin';
    $_PRODUCT_FINAL_FILE = 0;
    $_BASE_FILE_NAME = 0;
    $PRODUCT_TITLE = 'Advertisements / Community Ads Plugin';
    $SocialEngineAddOns_version = '4.8.0';
    $file_path = APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/ilicense.php";
    $is_file = file_exists($file_path);
    if (empty($is_file)) {
      include_once APPLICATION_PATH . "/application/modules/$PLUGIN_TITLE/controllers/license/license3.php";
    } else {
      if (!empty($_PRODUCT_FINAL_FILE)) {
        include_once APPLICATION_PATH . '/application/modules/' . $PLUGIN_TITLE . '/controllers/license/' . $_PRODUCT_FINAL_FILE;
      }
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

  function onInstall() {

    $db = $this->getDb();

    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'siteevent')
            ->where('enabled = ?', 1);
    $is_siteevent_object = $select->query()->fetchObject();
    if (!empty($is_siteevent_object)) {
      $db->query("DELETE FROM `engine4_communityad_modules` WHERE `engine4_communityad_modules`.`module_name` = 'event' LIMIT 1");
    }
    $db->query("INSERT IGNORE INTO `engine4_communityad_modules` (`module_name`, `module_title`, `table_name`, `title_field`, `body_field`, `owner_field`, `is_delete`, `displayable`) VALUES
('siteevent', 'Event', 'siteevent_event', 'title', 'body', 'owner_id', '1', '7');");


    //CODE FOR INCREASE THE SIZE OF engine4_communityad_package's FIELD urloption
    $is_table_exist = $db->query("SHOW TABLES LIKE 'engine4_communityad_package'")->fetch();
    if (!empty($is_table_exist)) {
      $type_array = $db->query("SHOW COLUMNS FROM engine4_communityad_package LIKE 'urloption'")->fetch();
      if (!empty($type_array)) {
        $varchar = $type_array['Type'];
        $length_varchar = explode("(", $varchar);
        $length = explode(")", $length_varchar[1]);
        $length_type = $length[0];
        if ($length_type < 255) {
          $run_query = $db->query("ALTER TABLE `engine4_communityad_package` CHANGE `urloption` `urloption` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL");
        }
      }
    }

    //ADD COLUMN IF FAQ PLUGIN IS INSTALLED
    $check_sitefaq = $db->select()
            ->from('engine4_core_modules', array('enabled'))
            ->where('name = ?', 'sitefaq')
            ->limit(1)
            ->query()
            ->fetchColumn();

    $table_exist = $db->query("SHOW TABLES LIKE 'engine4_communityad_faqs'")->fetch();
    if (!empty($table_exist) && !empty($check_sitefaq)) {
      $column_exist = $db->query("SHOW COLUMNS FROM engine4_communityad_faqs LIKE 'import'")->fetch();
      if (empty($column_exist)) {
        $db->query("ALTER TABLE `engine4_communityad_faqs` ADD `import` TINYINT( 1 ) NOT NULL DEFAULT '0'");
      }
    }

    $communityad_time_set = time();
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_modules')
            ->where('name = ?', 'communityad');
    $modules_version = $select->query()->fetchObject();
    if (!empty($modules_version)) {
      $product_version = $modules_version->version;

      if ($product_version < '4.1.8p1') {
        $this->addWidgets();
      }

      // It is the script which delete all the unwanted row from the "like_table" which user has been deleted or not have existence on site.
      if ($product_version < '4.1.9') {
        $this->deleteLikeTableRow();
      }

      if ($product_version == '4.1.2') {
        $table_exist = $db->query("SHOW TABLES LIKE 'engine4_communityad_package'")->fetch();
        if (!empty($table_exist)) {
          $column_exist = $db->query("SHOW COLUMNS FROM engine4_communityad_package LIKE 'order'")->fetch();
          if (empty($column_exist)) {
            $db->query("ALTER TABLE `engine4_communityad_package`  ADD `order` INT(11) NOT NULL DEFAULT '0'");
          }

          $column_level_exist = $db->query("SHOW COLUMNS FROM engine4_communityad_package LIKE 'level_id'")->fetch();
          if (empty($column_level_exist)) {
            $db->query("ALTER TABLE `engine4_communityad_package`  ADD `level_id` VARCHAR(265) NOT NULL DEFAULT '0'");
          }
        }
      }
    }


    $db->query("UPDATE  `engine4_seaocores` SET  `is_activate` =  '1' WHERE  `engine4_seaocores`.`module_name` ='communityad';");

    $db->query("INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
		('communityad.base.time', $communityad_time_set ),
		('communityad.check.var', 0 ), 
		('communityad.time.var', 3456000 ),
		('communityad.get.path', 'Communityad/controllers/license/license2.php');");

    $db = $this->getDb();
    $select = new Zend_Db_Select($db);
    $select
            ->from('engine4_core_settings')
            ->where('name = ?', 'ad.block.width');
    $adWidthBlock = $select->query()->fetchObject();

    if ($adWidthBlock && $adWidthBlock->value != '150') {
      $this->upgradeStyleCssFile($adWidthBlock->value);
    }

    // check for stats maintenance work
    if (!empty($modules_version) && $modules_version->version <= '4.1.6p1') {
      $table_stats_exist = $db->query("SHOW TABLES LIKE 'engine4_communityad_adstatistics'")->fetch();
      if (!empty($table_stats_exist)) {
        $db->query("ALTER TABLE `engine4_communityad_adstatistics` CHANGE `hostname` `hostname` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL , CHANGE `user_agent` `user_agent` VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL , CHANGE `url` `url` VARCHAR( 1000 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL");

        $timeObj = new Zend_Date(time());
        $time_select = new Zend_Db_Select($db);
        $time_select
                ->from('engine4_core_settings')
                ->where('name = ?', 'core.locale.timezone');
        $time_setting = $time_select->query()->fetchObject();
        $timeObj->setTimezone($time_setting->value);

        $yesterday_time = $timeObj->getTimestamp() - 86400;
        $lastExecutedDate = gmdate('Y-m-d', $lastExecutedTime);
        $yesterday_date = gmdate('Y-m-d', $yesterday_time);

        $sub_status_select = new Zend_Db_Select($db);
        $sub_status_select
                ->from('engine4_communityad_adstatistics', array('adstatistic_id', 'userad_id', 'adcampaign_id', 'response_date', 'SUM(value_click) as value_click', 'SUM(value_view) as value_view'))
                ->where("DATE_FORMAT(response_date, '%Y-%m-%d') <= ?", $yesterday_date)
                ->group("DATE_FORMAT(response_date, '%Y-%m-%d')")
                ->group('userad_id');

        $yesterday_stats = $sub_status_select->query()->fetchAll();

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {

          $stat_ids = array();
          foreach ($yesterday_stats as $values) {
            $db->insert('engine4_communityad_adstatistics', array(
                'userad_id' => $values['userad_id'],
                'adcampaign_id' => $values['adcampaign_id'],
                'viewer_id' => 0,
                'hostname' => NULL,
                'user_agent' => NULL,
                'url' => NULL,
                'response_date' => $values['response_date'],
                'value_click' => $values['value_click'],
                'value_view' => $values['value_view'],
                'value_like' => 0,
            ));
            $stat_ids[] = $db->lastInsertId('engine4_communityad_adstatistics');
          }
          $sub_string = (string) ("'" . join("', '", $stat_ids) . "'");

          $query = "DELETE FROM `engine4_communityad_adstatistics` WHERE (DATE_FORMAT(`engine4_communityad_adstatistics`.`response_date`, " . "'%Y-%m-%d'" . ") <= '$yesterday_date') AND `engine4_communityad_adstatistics`.`adstatistic_id` NOT IN ($sub_string)";

          $db->query($query);
          $db->commit();
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }
      }
    }

    $is_adstatisticscache_table_exist = $db->query("SHOW TABLES LIKE 'engine4_communityad_adstatisticscache'")->fetch();
    if (!$is_adstatisticscache_table_exist) {
      $db->query("CREATE TABLE IF NOT EXISTS `engine4_communityad_adstatisticscache` (
  `adstatisticcache_id` int(11) NOT NULL AUTO_INCREMENT,
  `userad_id` int(11) NOT NULL,
  `adcampaign_id` int(11) NOT NULL,
  `viewer_id` int(11) NOT NULL,
  `hostname` varchar(60) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `url` varchar(1000) DEFAULT NULL,
  `response_date` datetime NOT NULL,
  `value_click` int(11) DEFAULT NULL,
  `value_view` int(11) DEFAULT NULL,
  `value_like` varchar(35) DEFAULT NULL,
  `adstatistic_id` int(11) NOT NULL,
  PRIMARY KEY (`adstatisticcache_id`),
  KEY `viewer_id` (`viewer_id`),
  KEY `userad_id` (`userad_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
    }
    if (!empty($modules_version) && $modules_version->version <= '4.2.9') {
      $db = Zend_Db_Table_Abstract::getDefaultAdapter();
      $date = new Zend_Date(time());
      $timezone = $db->select()
              ->from('engine4_core_settings', 'value')
              ->where('name = ?', "core_locale_timezone")
              ->limit(1)
              ->query()
              ->fetchColumn();
      if (empty($timezone))
        $timezone = 'GMT';
      $date->setTimezone($timezone);
      $current_date = gmdate('Y-m-d', $date->getTimestamp());
      $statistics = $db->select()
              ->from('engine4_communityad_adstatistics')
              ->where('response_date > ?', $current_date)
              ->query()
              ->fetchAll();
      foreach ($statistics as $stats) {
        $db->insert('engine4_communityad_adstatisticscache', $stats);
      }
    }
    $db->query("INSERT IGNORE INTO `engine4_core_tasks` (`title`, `module`, `plugin`, `timeout`, `processes`, `semaphore`, `started_last`, `started_count`, `completed_last`, `completed_count`, `failure_last`, `failure_count`, `success_last`, `success_count`) VALUES ('Ad Statistics Maintenance', 'communityad', 'Communityad_Plugin_Task_StatsMaintenance', '86400', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0')");
    if (!empty($modules_version) && $modules_version->version <= '4.7.1p1') {
      include APPLICATION_PATH . '/application/modules/Communityad/settings/upgrade_install_4.7.1p2.php';
    }
    $this->_addAdBoardPage();
    parent::onInstall();
  }

  // It is the script which delete all the unwanted row from the "like_table" which user has been deleted or not have existence on site.
  private function deleteLikeTableRow() {
    $db = $this->getDb();
    $getLike = $db->query('SELECT `like_id`, `poster_id` FROM  `engine4_communityad_likes`;')->fetchAll();
    foreach ($getLike as $getLikeValue) {
      $getName = $db->query('SELECT `user_id` FROM  `engine4_users` WHERE  `user_id` = ' . $getLikeValue['poster_id'] . ' LIMIT 1')->fetchAll();
      if (empty($getName)) {
        $db->query('DELETE FROM `engine4_communityad_likes` WHERE `engine4_communityad_likes`.`like_id` = ' . $getLikeValue['like_id'] . ' LIMIT 1');
      }
    }
  }

  private function upgradeStyleCssFile($width) {
    $db = $this->getDb();
    $path = APPLICATION_PATH . '/application/modules/Communityad/externals/styles/style.css';
    @chmod($path, 0777);
    if (!@is_writeable($path)) {
      //Engine_Api::_()->getApi('settings', 'core')->setSetting('ad.block.widthupdatefile', 0);
      $db->query("UPDATE  `engine4_core_settings` SET  `engine4_core_settings`.`value` =  '0' WHERE  `engine4_core_settings`.`name` ='ad.block.widthupdatefile';");
      return;
    }
    if (empty($width))
      $width = 150;
    // Read the file in as an array of lines
    $fileData = file($path);
    $i = 0;
    $orignalWidth = $width;
    $newArray = null;

    foreach ($fileData as $key => $line) {


      // find the line that starts with width: and change it to custome width
      if (preg_match('/width:/', $line)) {

        if ($i == 1)
          $width = ($orignalWidth + 10) * 3;
        if ($i == 4)
          $width = ($orignalWidth + 20) * 5;
        if ($i == 7)
          $width = ($orignalWidth + 20) * 4;

        $explode = explode(":", $line);
        $explode[1] = $width . 'px;' . "\n";
        $line = implode(":", $explode);

        $i++;
      }

      $newArray .= $line;
    }

    // Overwrite test.txt

    $fp = fopen($path, 'w');
    fwrite($fp, $newArray);
    @chmod($path, 0755);
    fclose($fp);
    // Engine_Api::_()->getApi('settings', 'core')->setSetting('ad.block.widthupdatefile', 1);
    $db->query("UPDATE  `engine4_core_settings` SET  `engine4_core_settings`.`value` =  '1' WHERE  `engine4_core_settings`.`name` ='ad.block.widthupdatefile';");
  }

  private function addWidgets() {

    $db = $this->getDb();

    // Add default widgets for "Sponsored Story" only for upgradation.
    $select = new Zend_Db_Select($db);
    $page_id = $select
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'user_index_home')
            ->limit(1)
            ->query()
            ->fetchColumn(0)
    ;
    if (!empty($page_id)) {

      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $container_id = $select
              ->from('engine4_core_content', 'content_id')
              ->where('page_id = ?', $page_id)
              ->where('type = ?', 'container')
              ->where('name =?', 'main')
              ->limit(1)
              ->query()
              ->fetchColumn()
      ;

      if (!empty($container_id)) {
        // middle_id (will always be there)
        $select = new Zend_Db_Select($db);
        $right_id = $select
                ->from('engine4_core_content', 'content_id')
                ->where('parent_content_id = ?', $container_id)
                ->where('type = ?', 'container')
                ->where('name = ?', 'right')
                ->limit(1)
                ->query()
                ->fetchColumn()
        ;

        // insert
        if ($right_id) {
          $db->insert('engine4_core_content', array(
              'page_id' => $page_id,
              'type' => 'widget',
              'name' => 'communityad.sponsored-stories',
              'parent_content_id' => $right_id,
              'order' => 999,
          ));
        }
      }
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
    if (!empty($is_sitemobile_object)) {
      $db->query("INSERT IGNORE INTO `engine4_sitemobile_modules` (`name`, `visibility`) VALUES ('communityad','1')");
      $select = new Zend_Db_Select($db);
      $select
              ->from('engine4_sitemobile_modules')
              ->where('name = ?', 'communityad')
              ->where('integrated = ?', 0);
      $is_sitemobile_object = $select->query()->fetchObject();
      if ($is_sitemobile_object) {
        $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
        $controllerName = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
        if ($controllerName == 'manage' && $actionName == 'install') {
          $view = new Zend_View();
          $baseUrl = (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"]) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace('install/', '', $view->url(array(), 'default', true));
          $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
          $redirector->gotoUrl($baseUrl . 'admin/sitemobile/module/enable-mobile/enable_mobile/1/name/communityad/integrated/0/redirect/install');
        }
      }
    }
  }

  private function getVersion() {

    $db = $this->getDb();

    $errorMsg = '';
    $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

    $modArray = array(
        'sitemobile' => '4.6.0p4',
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
        $select->from('engine4_core_modules', array('title', 'version'))
                ->where('name = ?', "$key")
                ->where('enabled = ?', 1);
        $getModVersion = $select->query()->fetchObject();

        $isModSupport = strcasecmp($getModVersion->version, $value);
        if ($isModSupport < 0) {
          $finalModules[] = $getModVersion->title;
        }
      }
    }

    foreach ($finalModules as $modArray) {
      $errorMsg .= '<div class="tip"><span style="background-color: #da5252;color:#FFFFFF;">Note: You do not have the latest version of the "' . $modArray . '". Please upgrade "' . $modArray . '" on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "Mobile / Tablet Plugin".<br/> Please <a class="" href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
    }

    return $errorMsg;
  }

  protected function _addAdBoardPage() {
    $db = $this->getDb();

    // profile page
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'communityad_display_adboard')
      ->limit(1)
      ->query()
      ->fetchColumn();
    
    // insert if it doesn't exist yet
    if( !$page_id ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'communityad_display_adboard',
        'displayname' => 'Community Ads - Ad Board',
        'title' => 'Ad Board',
        'description' => 'This page displays advertisements.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId();
      
      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => 1,
      ));
      $top_id = $db->lastInsertId();
      
      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 2,
      ));
      $main_id = $db->lastInsertId();
      
      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_id,
      ));
      $top_middle_id = $db->lastInsertId();
      
      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 2,
      ));
      $main_middle_id = $db->lastInsertId();
      
//      // Insert main-right
//      $db->insert('engine4_core_content', array(
//        'type' => 'container',
//        'name' => 'right',
//        'page_id' => $page_id,
//        'parent_content_id' => $main_id,
//        'order' => 1,
//      ));
//      $main_right_id = $db->lastInsertId();
      
      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'communityad.user-navigation',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'order' => 1,
      ));
      
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

}
