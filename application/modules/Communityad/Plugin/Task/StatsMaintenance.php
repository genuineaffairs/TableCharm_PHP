<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: StatsMaintenance.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Plugin_Task_StatsMaintenance extends Core_Plugin_Task_Abstract {

  public function execute() {
    $timeObj = new Zend_Date(time());
    $current_date = gmdate('Y-m-d', $timeObj->getTimestamp());
    $adstatisticscache_table = Engine_Api::_()->getDbTable('adstatisticscache', 'communityad');
    $adstatisticscache_table->removeStatisticsCache(array('response_date < ?' => $current_date));

    $current_time = $timeObj->getTimestamp();

    // fetch that time stamp when the reminder mail was last sent
    $taskstable = Engine_Api::_()->getDbtable('tasks', 'core');
    $rtasksName = $taskstable->info('name');
    $taskstable_result = $taskstable->select()
            ->from($rtasksName, array('success_last'))
            ->where('title = ?', 'Ad Statistics Maintenance')
            ->where('plugin = ?', 'Communityad_Plugin_Task_StatsMaintenance')
            ->limit(1);

    $value = $taskstable->fetchRow($taskstable_result);
    $old_success_last = $value['success_last'];
    if (empty($old_success_last))
      $old_success_last = 0;
    // find the difference between current time and the time when the mail was last sent successfully
    $diff = (int) (($current_time - $old_success_last) / 86400);
    if ($diff > 100)
      $diff = 100;
    $lastExecutedTime = $timeObj->getTimestamp() - ($diff * 86400);

    $yesterday_time = $timeObj->getTimestamp() - 86400;
    $lastExecutedDate = gmdate('Y-m-d', $lastExecutedTime);
    $yesterday_date = gmdate('Y-m-d', $yesterday_time);

    if ($lastExecutedDate <= $yesterday_date) {
      $stat_table = Engine_Api::_()->getDbTable('adstatistics', 'communityad');
      $stat_name = $stat_table->info('name');
      $sub_status_select = $stat_table->select()
              ->from($stat_name, array('adstatistic_id', 'userad_id', 'adcampaign_id', 'response_date', 'SUM(value_click) as value_click', 'SUM(value_view) as value_view'));

      if ($lastExecutedDate == $yesterday_date) {
        $sub_status_select->where("DATE_FORMAT(response_date, '%Y-%m-%d') = ?", $yesterday_date);
      } else {
        $sub_status_select->where("DATE_FORMAT(response_date, '%Y-%m-%d') >= ?", $lastExecutedDate)
                ->where("DATE_FORMAT(response_date, '%Y-%m-%d') <= ?", $yesterday_date);
      }

      $sub_status_select->group("DATE_FORMAT(response_date, '%Y-%m-%d')")
              ->group('userad_id');

      $yesterday_stats = $stat_table->fetchAll($sub_status_select);

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {

        $stat_ids = array();
        foreach ($yesterday_stats as $values) {
          $row = $stat_table->createRow();
          $row->userad_id = $values['userad_id'];
          $row->adcampaign_id = $values['adcampaign_id'];
          $row->viewer_id = 0;
          $row->hostname = new Zend_Db_Expr('NULL');
          $row->user_agent = new Zend_Db_Expr('NULL');
          $row->url = new Zend_Db_Expr('NULL');
          $row->response_date = $values['response_date'];
          $row->value_click = $values['value_click'];
          $row->value_view = $values['value_view'];
          $row->value_like = 0;
          $row->save();
          $stat_ids[] = $row->adstatistic_id;
        }
        $sub_string = (string) ("'" . join("', '", $stat_ids) . "'");

        if ($lastExecutedDate == $yesterday_date) {
          $query = "DELETE FROM $stat_name WHERE (DATE_FORMAT($stat_name.response_date, " . "'%Y-%m-%d'" . ") = '$yesterday_date') AND $stat_name.adstatistic_id NOT IN ($sub_string)";
        } else {
          $query = "DELETE FROM $stat_name WHERE (DATE_FORMAT($stat_name.response_date, " . "'%Y-%m-%d'" . ") >= '$lastExecutedDate') AND (DATE_FORMAT($stat_name.response_date, " . "'%Y-%m-%d'" . ") <= '$yesterday_date') AND $stat_name.adstatistic_id NOT IN ($sub_string)";
        }

        $db->query($query);
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }

    
  }

}
