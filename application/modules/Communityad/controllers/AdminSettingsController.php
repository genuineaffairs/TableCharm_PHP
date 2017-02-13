<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_AdminSettingsController extends Core_Controller_Action_Admin {

  public function indexAction() {

    //include APPLICATION_PATH .'/application/modules/Communityad/settings/upgrade_install_4.7.1p2.php';
    
    if (!$this->getRequest()->isPost()) {
    $timeObj = new Zend_Date(time());
    $current_time = $timeObj->getTimestamp();
    $current_date = gmdate('Y-m-d', $current_time);
    $adstatisticscache_table = Engine_Api::_()->getDbTable('adstatisticscache', 'communityad');
    $adstatisticscache_table->removeStatisticsCache(array('response_date < ?' => $current_date));
    }
    if (!empty($_POST['communityad_lsettings'])) {
      $_POST['communityad_lsettings'] = trim($_POST['communityad_lsettings']);
    }
    $oldStyleWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.block.width', 150);
    $communityad_form_content = array('ad_block_width', 'ad_image_width', 'ad_image_hight', 'ad_char_title', 'ad_char_body', 'ad_show_menu', 'ad_saleteam_con', 'ad_saleteam_email',  'ad_board_limit', 'currency', 'advertise_benefit', 'submit', 'adboard_footer', 'adblock_create_link', 'adcancel_enable', 'show.adboard', 'show_adboard', 'communityad_title', 'communityad_view_limit', 'communityad_ad_type', 'dummy_communityad_title', 'dummy_story_title', 'dummy_general_title', 'story_char_title', 'custom_ad_url', 'ad_statistics_limit');

    // Save the Advertisment Type value in data base
    $getPostValue = $this->getRequest()->getPost();
    if (!empty($getPostValue) && array_key_exists('communityad_ad_type', $getPostValue)) {
      $adTypeArray = $getPostValue['communityad_ad_type'];
      Engine_Api::_()->getItemTable('communityad_adtype')->setSettings($adTypeArray);
      unset($getPostValue['communityad_ad_type']);
    }

    $showAdBoard = Engine_Api::_()->getApi('settings', 'core')->getSetting('show.adboard', 1);


    include APPLICATION_PATH . '/application/modules/Communityad/controllers/license/license1.php';

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $showChangeAdBoard = Engine_Api::_()->getApi('settings', 'core')->getSetting('show.adboard', 1);
      if ($showAdBoard != $showChangeAdBoard) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->query("UPDATE `engine4_core_menuitems` SET `enabled` = '" . $showChangeAdBoard . "' WHERE `engine4_core_menuitems`.`name` ='communityad_main_adboard' AND `module` = 'communityad';
");
        if (!empty($showChangeAdBoard)) {
          $db->query("UPDATE `engine4_core_menuitems` SET `plugin`='Communityad_Plugin_Menus::canViewAdvertiesment', `params` = '{\"route\":\"communityad_display\",\"action\":\"adboard\",\"controller\":\"display\"}' WHERE `engine4_core_menuitems`.`name` ='core_main_communityad' AND `module` = 'communityad';
");
        } else {
          $db->query("UPDATE `engine4_core_menuitems` SET `plugin`='',`params` = '{\"route\":\"communityad_help_and_learnmore\",\"action\":\"help-and-learnmore\",\"controller\":\"display\"}' WHERE `engine4_core_menuitems`.`name` ='core_main_communityad' AND `module` = 'communityad';
");
        }
      }
    }
    $newStyleWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.block.width', 150);
   // echo $newStyleWidth; die;
    $newStyleWidthUpdate = Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.block.widthupdatefile', 1);
    if ($oldStyleWidth !== $newStyleWidth || empty($newStyleWidthUpdate)) {
      $this->upgradeStyleCssFile($newStyleWidth);
    }

    if (!$this->getRequest()->isPost()) {
     Engine_Api::_()->getDbTable('adstatistics', 'communityad')->removeOldStatistics();
    }
  }

  public function faqAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('communityad_admin_main', array(), 'communityad_admin_faq');
    $this->view->faq = 1;
    $this->view->faq_type = $this->_getParam('faq_type', 'general');
  }

  public function guidelinesAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('communityad_admin_main', array(), 'communityad_admin_widget_setting');
  }

  // This is the 'readme Action' which will call first time only when plugin will install.
  public function readmeAction() {
    $this->view->faq = 0;
    $this->view->faq_type = $this->_getParam('faq_type', 'general');
  }

  public function graphAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('communityad_admin_main', array(), 'communityad_admin_graph');

    $this->view->form = $form = new Communityad_Form_Admin_Settings_Graph();

    if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
      $values = $form->getValues();

      include_once(APPLICATION_PATH . "/application/modules/Communityad/controllers/license/license2.php");
    }
  }

  public function targetAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('communityad_admin_main', array(), 'communityad_admin_target_settings');
    $targetTable = Engine_Api::_()->getItemTable('target');
    $tagetFields = $formElementsContent = Engine_Api::_()->communityad()->preFieldPkgTargetData();

    // Make form
    $this->view->form = $form = new Communityad_Form_Admin_Target();
    if (!$this->getRequest()->isPost())
      $form->populate($tagetFields);

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      if (!isset($values['target_birthday']))
        $values['target_birthday'] = 0;

      if (!isset($values['community_target_network']))
        $values['community_target_network'] = 0;

      Engine_Api::_()->getApi('settings', 'core')->setSetting('target.birthday', $values['target_birthday']);
      Engine_Api::_()->getApi('settings', 'core')->setSetting('community.target.network', $values['community_target_network']);


      $targetTable->delete(null);
      $checks = array();

      $field_ids = array(); //contain all the field_id to all the fields present
      $option_id = array(); //contains checked element option_id with normal key
      //Check and select elements are to be eleminated from data
      foreach ($values as $key => $value) {
        if (strstr($key, 'check') != null && $value) {
          $tc = explode("check", $key);
          $option_id[] = (int) $tc[0];
          $checks[] = (int) $tc[1];
        }
      }

      include_once(APPLICATION_PATH . "/application/modules/Communityad/controllers/license/license2.php");

      // ADD COLUMN IN USERADS TABLE
      $structure = Engine_Api::_()->getApi('core', 'communityad')->getFieldsStructureSearch('user');
      $key = array();

      foreach ($structure as $map) {
        $field = $map->getChild();

        if (!in_array($field->field_id, $checks)) {
          continue;
        }
        if (!empty($field->alias)) {
          $key[] = $field->alias;
        } else {
          $key[] = sprintf('field_%d', $field->field_id);
        }
      }

      $data = array();
      $data = Engine_Api::_()->getApi('core', 'communityad')->getTargetColumns();

      $NULLBIRTHDAY = 0;
      $key = array_unique($key);
      if (!in_array('birthdate', $key)) {
        $NULLBIRTHDAY = 1;
      }
      $key = array_diff($key, array('birthdate'));
      $remove = $key;
      $remove[] = 'age_min';
      $remove[] = 'age_max';
      $remove[] = 'adtarget_id';
      $remove[] = 'userad_id';
      $remove[] = 'birthday_enable';
      $remove[] = 'networks';

      $removeKey = array_diff($data, $remove);


      $adtargetTable = Engine_Api::_()->getDbtable('adtargets', 'communityad');
      $targetName = $adtargetTable->info('name');

      $db = Zend_Db_Table_Abstract::getDefaultAdapter();


      foreach ($removeKey as $field_name) {

        $alter_sql = "ALTER TABLE `" . $targetName . "` DROP `$field_name`";

        if (!($db->query($alter_sql))) {
          echo "Error in running sql query.";
        }
      }

      if (!empty($NULLBIRTHDAY)) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $age_aql = "UPDATE " . $adtargetTable->info("name") . " SET `age_min` = NULL, `age_max` = NULL ";
        $db->query($age_aql);
      }
      $birthday_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('target.birthday', 0);
      if (empty($birthday_enable)) {
        $adtargetTable->update(array("birthday_enable" => 0), null);
      }

      $network_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('community.target.network', 0);
      if (empty($network_enable)) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $network_sql = "UPDATE " . $adtargetTable->info("name") . " SET `networks` =NULL";
        $db->query($network_sql);
      }
      $addKey = array_diff($key, $data);

      foreach ($addKey as $field_name) {
        $alter_sql = "ALTER TABLE `" . $targetName . "` ADD `$field_name` VARCHAR( 255 ) NULL";
        if (!($db->query($alter_sql))) {
          echo "Error in running sql query.";
        }
      }
    }
  }

  private function createDir($path) {
    if (!empty($path)) {
      if (!@is_dir($path) && !@mkdir($path, 0777, true)) {
        @mkdir(dirname($path));
        @chmod(dirname($path), 0777);
        @touch($path);
        @chmod($path, 0777);
      }
    }
  }

  private function upgradeStyleCssFile($width) {
    return;
    $path = APPLICATION_PATH . '/application/modules/Communityad/externals/styles/style.css';
    @chmod($path, 0777);
    if (!@is_writeable($path)) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting('ad.block.widthupdatefile', 0);
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
    Engine_Api::_()->getApi('settings', 'core')->setSetting('ad.block.widthupdatefile', 1);
  }

}