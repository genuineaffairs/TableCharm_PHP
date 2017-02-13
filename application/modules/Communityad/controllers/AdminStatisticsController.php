<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminStatisticsController.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_AdminStatisticsController extends Core_Controller_Action_Admin {

  public function exportReportAction() {
    Engine_Api::_()->getDbTable('adstatistics', 'communityad')->removeOldStatistics();
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('communityad_admin_main', array(), 'communityad_admin_main_statistics');

    // Get viewer's Ads and campaigns
    $useradsTable = Engine_Api::_()->getDbtable('userads', 'communityad');
    $useradsName = $useradsTable->info('name');


    // to calculate the oldest ad's creation year
    $select = $useradsTable->select();
    $select
        ->from($useradsName, array('userad_id', 'MIN(cads_start_date) as min_year'))
        ->group('userad_id')
        ->limit(1);
    $min_year = $useradsTable->fetchRow($select);
    $date = explode(' ', $min_year['min_year']);
    $yr = explode('-', $date[0]);
    $current_yr = date('Y', time());
    $year_array = array();
    $this->view->no_ads = 0;
    if (empty($min_year)) {
      $this->view->no_ads = 1;
    }
    $year_array[$current_yr] = $current_yr;
    while ($current_yr != $yr[0]) {
      $current_yr--;
      $year_array[$current_yr] = $current_yr;
    }

    $adcampaignsTable = Engine_Api::_()->getDbtable('adcampaigns', 'communityad');
    $adcampaignsName = $adcampaignsTable->info('name');

    $this->view->reportform = $reportform = new Communityad_Form_Admin_Report();
    $reportform->year_start->setMultiOptions($year_array);
    $reportform->year_end->setMultiOptions($year_array);

		// POPULATE FORM
    if (!empty($_GET['type'])) {
      $reportform->populate($_GET);

			// Get Form Values
			$values = $reportform->getValues();
			$start_cal_date = $values['start_cal'];
			$end_cal_date = $values['end_cal'];
			$start_tm = strtotime($start_cal_date);
			$end_tm = strtotime($end_cal_date);
			$url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      $url_values = explode('?', $url_string);

			if(empty($values['format_report'])) {
				$url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('module' => 'communityad', 'controller' => 'statistics', 'action' => 'export-webpage', 'start_daily_time' => $start_tm, 'end_daily_time' => $end_tm), 'admin_default', true) . '?' . $url_values[1];
			}
			else {
				$url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('module' => 'communityad', 'controller' => 'statistics', 'action' => 'export-excel', 'start_daily_time' => $start_tm, 'end_daily_time' => $end_tm), 'admin_default', true) . '?' . $url_values[1];
			}
			// Session Object
			$session = new Zend_Session_Namespace('empty_adminredirect');
			if(isset($session->empty_session) && !empty($session->empty_session)) {
				unset($session->empty_session);
       } else {
				header("Location: $url");
			}
    }
    $this->view->empty = $this->_getParam('empty', 0);
  }

  public function exportExcelAction() {

    // in case of admin's report format is excel file, the form action is redirected to this action
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->post = $post = 0;
		$start_daily_time = $this->_getParam('start_daily_time', time());
		$end_daily_time = $this->_getParam('end_daily_time', time());

    if (!empty($_GET)) {
      $this->_helper->layout->setLayout('default-simple');
      $this->view->post = $post = 1;
      $values = $_GET;
      $values = array_merge(array(
									'start_daily_time' => $start_daily_time,
									'end_daily_time' => $end_daily_time,
                  'admin_report' => '1',
                  'type' => 'Advertising Performance',
              ), $values);
      $this->view->values = $values;

      // REPORT TYPE
      if ($values['type'] == 'Advertising Performance') {
        $stat_object = Engine_Api::_()->getDbTable('adstatistics', 'communityad')->getStats($values);
        $rawdata = Engine_Api::_()->getDbTable('adstatistics', 'communityad')->fetchAll($stat_object);
      }
      $this->view->rawdata = $rawdata;
      $rawdata_array = $rawdata->toarray();
      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      $url_values = explode('?', $url_string);
      $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('module' => 'communityad', 'controller' => 'statistics', 'action' => 'export-report', 'empty' => '1'), 'admin_default', true) . '?' . $url_values[1];
      if (empty($rawdata_array)) {
				// Session Object
				$session = new Zend_Session_Namespace('empty_adminredirect');
				$session->empty_session = 1;
        header("Location: $url");
      }
    }
  }

  public function exportWebpageAction() {

    // in case of admin's report format is webpage, the form action is redirected to this action
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('communityad_admin_main', array(), 'communityad_admin_main_statistics');

    $this->view->post = $post = 0;
		$start_daily_time = $this->_getParam('start_daily_time', time());
		$end_daily_time = $this->_getParam('end_daily_time', time());

    if (!empty($_GET)) {
      $this->view->post = $post = 1;
      $values = $_GET;
      $values = array_merge(array(
									'start_daily_time' => $start_daily_time,
									'end_daily_time' => $end_daily_time,
                  'admin_report' => '1',
                  'type' => 'Advertising Performance',
              ), $values);
      $this->view->values = $values;

      // REPORT TYPE
      if ($values['type'] == 'Advertising Performance') {
        $adstatisticsTable = Engine_Api::_()->getDbTable('adstatistics', 'communityad');
        $totalStatsSql = $adstatisticsTable->getTotalStats($values);
        $totalStats = $adstatisticsTable->fetchRow($totalStatsSql);

        if (!empty($totalStats)) {
          $this->view->totalViews = $totalViews = $totalStats->views;
          $this->view->totalClicks = $totalClicks = $totalStats->clicks;
          $this->view->totalCtr = $totalCtr = round(($totalClicks / $totalViews) * 100, 4);
        }
        $stat_object = $adstatisticsTable->getStats($values);
        $rawdata = $adstatisticsTable->fetchAll($stat_object);
      }
      $this->view->rawdata = $rawdata;
      $rawdata_array = $rawdata->toarray();
      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      $url_values = explode('?', $url_string);
      $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('module' => 'communityad', 'controller' => 'statistics', 'action' => 'export-report', 'empty' => '1'), 'admin_default', true) . '?' . $url_values[1];
      if (empty($rawdata_array)) {
				// Session Object
				$session = new Zend_Session_Namespace('empty_adminredirect');
				$session->empty_session = 1;
        header("Location: $url");
      }
    }
  }

	// To display users in the auto suggest at report form
  public function suggestusersAction() {
    $text = $this->_getParam('search', $this->_getParam('value'));
    $limit = $this->_getParam('limit', 40);
    $userTable = Engine_Api::_()->getItemTable('user');
    $select = $userTable->select()
            ->where('displayname LIKE ?', '%' . $text . '%')
            ->order('displayname ASC')
            ->limit($limit);
    $users = $userTable->fetchAll($select);

    $data = array();
    $mode = $this->_getParam('struct');
    if ($mode == 'text') {
      foreach ($users as $user) {
        $data[] = $user->displayname;
      }
    } else {
      foreach ($users as $user) {
        $data[] = array(
                'id' => $user->user_id,
                'label' => $user->displayname,
                'photo' => $this->view->itemPhoto($user, 'thumb.icon'),
        );
      }
    }

    if ($this->_getParam('sendNow', true)) {
      return $this->_helper->json($data);
    } else {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }
}