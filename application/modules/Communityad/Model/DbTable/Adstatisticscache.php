<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Adstatistics.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Model_DbTable_Adstatisticscache extends Engine_Db_Table {

  protected $_name = 'communityad_adstatisticscache';

  /* Returns all the fields set to the corresponding package_id
   */

  public function getStatisticsCache($params=array()) {
    $sub_status_table = $this;
    $sub_status_name = $sub_status_table->info('name');
    $sub_status_select = $sub_status_table->select()
            ->from($sub_status_name, array('adstatistic_id', 'value_view','value_click'))
            ->where('userad_id = ?', $params['userad_id'])
            ->where('viewer_id = ?', $params['viewer_id'])
            ->where("DATE_FORMAT(" . $sub_status_name . " .response_date, '%Y-%m-%d') = ?", $params['response_date'])
            ->limit(1);
    return $sub_status_select->query()->fetchAll();
  }

  public function setStatisticsCache($statistics) {
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $db->insert('engine4_communityad_adstatisticscache', $statistics);
  }

  public function updateStatisticsCache($setArray=array(), $whereArray=array()) {
    $this->update($setArray, $whereArray);
  }

  public function removeStatisticsCache($whereArray) {
    $this->delete($whereArray);
  }

}