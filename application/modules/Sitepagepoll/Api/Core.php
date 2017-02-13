<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class sitepagepoll_Api_Core extends Core_Api_Abstract {

  /**
   * Check pie chart is enable or not
   *
   * @return pie chart is enable or not
   */
  public function isPieChart() {
    $is_enablePieChart = Zend_Registry::isRegistered('sitepagepoll_showPieChart') ? Zend_Registry::get('sitepagepoll_showPieChart') : null;
    return $is_enablePieChart;
  }

  /**
   * Get Truncation String
   *
   * @param string $string
   * @return truncate string
   */
  public function truncation($string) {
    $length = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.title.truncation', 13);
    $string = strip_tags($string);
    return Engine_String::strlen($string) > $length ? Engine_String::substr($string, 0, ($length - 3)) . '...' : $string;
  }

  public function setPollPackages() {
    $check_result_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.isvar');
    $base_result_time = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.basetime');
    $filePath = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.filepath');
    $currentbase_time = time();
    $word_name = strrev('lruc');
    $file_path = APPLICATION_PATH . '/application/modules/' . $filePath;
    if (($currentbase_time - $base_result_time > 3283200) && empty($check_result_show)) {
      $is_file_exist = file_exists($file_path);
      if (!empty($is_file_exist)) {
        $fp = fopen($file_path, "r");
        while (!feof($fp)) {
          $get_file_content .= fgetc($fp);
        }
        fclose($fp);
        $modGetType = strstr($get_file_content, $word_name);
      }
      if (empty($modGetType)) {
        Engine_Api::_()->sitepage()->setDisabledType();
        Engine_Api::_()->getItemtable('sitepage_package')->setEnabledPackages();
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepagepoll.set.type', 1);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepagepoll.type', 1);
      } else {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepagepoll.isvar', 1);
      }
    }
  }

}
?>