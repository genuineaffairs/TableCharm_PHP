<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: StatisticsController.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_StatisticsController extends Core_Controller_Action_Standard {

  protected $_navigation;
  protected $_viewer;
  protected $_viewer_id;
  protected $_periods = array(
      Zend_Date::DAY, //dd
      Zend_Date::WEEK, //ww
      Zend_Date::MONTH, //MM
      Zend_Date::YEAR, //y
  );
  protected $_allPeriods = array(
      Zend_Date::SECOND,
      Zend_Date::MINUTE,
      Zend_Date::HOUR,
      Zend_Date::DAY,
      Zend_Date::WEEK,
      Zend_Date::MONTH,
      Zend_Date::YEAR,
  );
  protected $_periodMap = array(
      Zend_Date::DAY => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
      ),
      Zend_Date::WEEK => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
          Zend_Date::WEEKDAY_8601 => 1,
      ),
      Zend_Date::MONTH => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
          Zend_Date::DAY => 1,
      ),
      Zend_Date::YEAR => array(
          Zend_Date::SECOND => 0,
          Zend_Date::MINUTE => 0,
          Zend_Date::HOUR => 0,
          Zend_Date::DAY => 1,
          Zend_Date::MONTH => 1,
      ),
  );

  public function init() {
    // It will show the navigation bar.
    $this->view->viewer = $this->_viewer = Engine_Api::_()->user()->getViewer();

    if (!$this->_helper->requireAuth()->setAuthParams('communityad', $this->_viewer, 'view')->isValid()) {
      return;
    }
    $this->view->viewer_id = $this->_viewer_id = $this->_viewer->getIdentity();

    if (!empty($this->_viewer_id))
      $user_level = $this->_viewer->level_id;
    else
      $user_level = Engine_Api::_()->communityad()->getPublicUserLevel();

    $this->view->can_create = Engine_Api::_()->authorization()->getPermission($user_level, 'communityad', 'create');

    $can_delete = Engine_Api::_()->authorization()->getPermission($user_level, 'communityad', 'delete');
    $this->view->can_delete = 1;
    if (empty($can_delete)) {
      $this->view->can_delete = 0;
    }

    $this->view->navigation = $this->_navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('communityad_main');
    if (date('Y-m-d', strtotime(Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.update.approved'))) < date('Y-m-d')) {
      Engine_Api::_()->getDbtable('userads', 'communityad')->updateApproved();
    }
  }

// Display Compagins list
  public function indexAction() {

    if (!$this->_helper->requireUser()->isValid())
      return;

    $chunk = Zend_Date::DAY;
    $period = Zend_Date::WEEK;
    $start = time();
    $this->removeOldStatistics();
    // Make start fit to period?
    $startObject = new Zend_Date($start);

    $partMaps = $this->_periodMap[$period];
    foreach ($partMaps as $partType => $partValue) {
      $startObject->set($partValue, $partType);
    }
    $startObject->add(1, $chunk);
    $this->view->is_ajax = $this->_getParam('is_ajax', 0);
    $this->view->formFilter = $formFilter = new Communityad_Form_Admin_Filter();
    if (!$this->_helper->requireAuth()->setAuthParams('communityad', null, 'create')->isValid())
      return;

//    if (!$this->_helper->requireAuth()->setAuthParams('communityad', null, 'edit')->isValid())
//      return;
    $this->view->can_edit = Engine_Api::_()->authorization()->getPermission($this->_viewer->level_id, 'communityad', 'edit');
    $graph_type = Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.graph.type', 1);
    if (empty($graph_type)) {
      return;
    }

    //Get data
    $graph_type = Zend_Registry::get('communityad_graph_type');
    $statsTable = Engine_Api::_()->getDbtable('adstatistics', 'communityad');

    $useradsTable = Engine_Api::_()->getDbtable('userads', 'communityad');
    $useradsName = $useradsTable->info('name');

    $adcampaignsTable = Engine_Api::_()->getDbtable('adcampaigns', 'communityad');
    $adcampaignsName = $adcampaignsTable->info('name');

    $statsSelect = $adcampaignsTable->select();
    $statsSelect
            ->from($adcampaignsName, array('owner_id', 'name', 'adcampaign_id', 'status'))
            ->setIntegrityCheck(false)
            ->joinleft($useradsName, $useradsName . '.campaign_id = ' . $adcampaignsName . '.adcampaign_id', array('userad_id', 'resource_type', 'SUM(count_view) as views', 'SUM(count_click) as clicks', 'COUNT(userad_id) as ads', "(case when SUM(count_view) <> 0 and  SUM(count_click) <>0  then  ROUND((SUM(count_click) / SUM(count_view)), 7)  else 0 end)   AS CTR", 'SUM(count_like) as likes'))
            ->where($adcampaignsName . '.owner_id = ?', $this->_viewer_id)
            ->group($adcampaignsName . '.adcampaign_id')
            ->distinct(true);

    $values = array();

    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
      if (empty($values['order']) && empty($values['order_direction'])) {
        $values['order'] = 'adcampaign_id';
        $values['order_direction'] = 'DESC';
      }
    }

    $this->view->assign($values);

    $statsSelect->order((!empty($values['order']) ? $values['order'] : 'adcampaign_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    $date_select = $useradsTable->select()->from($useradsName, array('MIN(create_date) as earliest_ad_date'))
            ->where('owner_id = ?', $this->_viewer_id);

    $earliest_ad_date = $useradsTable->fetchRow($date_select)->earliest_ad_date;
    $adStaticsLimitDate = Engine_Api::_()->communityad()->getAdStaticsLimitDate();
    if (strtotime($adStaticsLimitDate) > strtotime($earliest_ad_date))
      $earliest_ad_date = $adStaticsLimitDate;
    $this->view->prev_link = 1;
    $this->view->startObject = $startObject = strtotime($startObject);
    $this->view->earliest_ad_date = $earliest_ad_date = strtotime($earliest_ad_date);
    if ($earliest_ad_date > $startObject) {
      $this->view->prev_link = 0;
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($statsSelect);
    // No of item show per page
    $items_per_page = '5';

    $this->view->total_count = $total_count = $paginator->getTotalItemCount();
    $this->view->total_pages = $total_pages = ceil($total_count / $items_per_page);

    $paginator->setItemCountPerPage($items_per_page);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $this->view->filterForm = $filterForm = new Communityad_Form_Statistics_Filter();
    if (empty($graph_type)) {
      return;
    }
  }

  public function chartDataAction() {
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
    $chart_data_type = Zend_Registry::get('communityad_chart_data');
    if (empty($chart_data_type)) {
      return;
    }
    // Get params
    $type = $this->_getParam('type');
    $start = $this->_getParam('start');
    $offset = $this->_getParam('offset', 0);
    $mode = $this->_getParam('mode');
    $chunk = $this->_getParam('chunk');
    $period = $this->_getParam('period');
    $periodCount = $this->_getParam('periodCount', 1);
    $subject = $this->_getParam('ad_subject');
    $ad_id = $this->_getParam('ad_id');
    $adcampaign_id = $this->_getParam('adcampaign_id');
    if (empty($subject)) {
      if (!empty($ad_id)) {
        $subject = 'ad';
      } else {
        $subject = 'campaign';
      }
    }

    // Validate chunk/period
    if (!$chunk || !in_array($chunk, $this->_periods)) {
      $chunk = Zend_Date::DAY;
    }
    if (!$period || !in_array($period, $this->_periods)) {
      $period = Zend_Date::MONTH;
    }

    if (array_search($chunk, $this->_periods) >= array_search($period, $this->_periods)) {
      die('whoops.');
      return;
    }

    // Validate start
    if ($start && !is_numeric($start)) {
      $start = strtotime($start);
    }
    if (!$start) {
      $start = time();
    }

    // Fixes issues with month view
    Zend_Date::setOptions(array(
        'extend_month' => true,
    ));

    // Make start fit to period?
    $startObject = new Zend_Date($start);
    $startObject->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));

    $partMaps = $this->_periodMap[$period];
    foreach ($partMaps as $partType => $partValue) {
      $startObject->set($partValue, $partType);
    }

    // Do offset
    if ($offset != 0) {
      $startObject->add($offset, $period);
    }

    // Get end time
    $endObject = new Zend_Date($startObject->getTimestamp());
    $endObject->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));
    $endObject->add($periodCount, $period);

    $end_tmstmp_obj = new Zend_Date(time());
    $end_tmstmp_obj->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));
    $end_tmstmp = $end_tmstmp_obj->getTimestamp();
    if ($endObject->getTimestamp() < $end_tmstmp) {
      $end_tmstmp = $endObject->getTimestamp();
    }
    $end_tmstmp_object = new Zend_Date($end_tmstmp);
    $end_tmstmp_object->setTimezone(Engine_Api::_()->getApi('settings', 'core')->getSetting('core_locale_timezone', 'GMT'));

    // Get data
    $statsTable = Engine_Api::_()->getDbtable('adstatistics', 'communityad');
    $statsName = $statsTable->info('name');

    $useradsTable = Engine_Api::_()->getDbtable('userads', 'communityad');
    $useradsName = $useradsTable->info('name');

    $adcampaignsTable = Engine_Api::_()->getDbtable('adcampaigns', 'communityad');
    $adcampaignsName = $adcampaignsTable->info('name');

    if ($subject == 'ad') {
      $subjectid = 'userad_id';
      $subjecTableName = $useradsName;
    } elseif ($subject == 'campaign') {
      $subjectid = 'adcampaign_id';
      $subjecTableName = $adcampaignsName;
    }

    $statsSelect = $statsTable->select();

    $statsSelect
            ->from($statsName, array('adstatistic_id', $subjectid, 'response_date', 'SUM(value_view) as summation_view', 'SUM(value_click) as summation_click'))
            ->setIntegrityCheck(false)
            ->join($subjecTableName, $subjecTableName . '.' . $subjectid . ' = ' . $statsName . '.' . $subjectid, array('owner_id'))
            ->where($statsName . '.response_date >= ?', gmdate('Y-m-d H:i:s', $startObject->getTimestamp()))
            ->where($statsName . '.response_date < ?', gmdate('Y-m-d H:i:s', $endObject->getTimestamp()))
            ->group("DATE_FORMAT(" . $statsName . " .response_date, '%Y-%m-%d')")
            ->order($statsName . '.response_date ASC')
            ->distinct(true)
    ;
    if (empty($ad_id)) {
      $statsSelect->where($subjecTableName . '.owner_id = ?', $this->_viewer_id);
    }

    if (!empty($adcampaign_id)) {
      $statsSelect->where($statsName . '.' . $subjectid . ' = ?', $adcampaign_id);
    } elseif (!empty($ad_id)) {
      $statsSelect->where($statsName . '.' . $subjectid . ' = ?', $ad_id);
    }
    $rawData = $statsTable->fetchAll($statsSelect);

    // Now create data structure
    $currentObject = clone $startObject;
    $nextObject = clone $startObject;

    $data_views = array();
    $data_clicks = array();
    $data_ctr = array();
    $dataLabels = array();
    $cumulative_views = 0;
    $cumulative_clicks = 0;
    $cumulative_ctr = 0;
    $previous_views = 0;
    $previous_clicks = 0;
    $previous_ctr = 0;

    do {
      $nextObject->add(1, $chunk);

      $currentObjectTimestamp = $currentObject->getTimestamp();
      $nextObjectTimestamp = $nextObject->getTimestamp();
      $data_views[$currentObjectTimestamp] = $cumulative_views;
      $data_clicks[$currentObjectTimestamp] = $cumulative_clicks;
      $data_ctr[$currentObjectTimestamp] = $cumulative_ctr;

      // Get everything that matches
      $currentPeriodCount_views = 0;
      $currentPeriodCount_clicks = 0;
      $currentPeriodCount_ctr = 0;
      foreach ($rawData as $rawDatum) {
        $rawDatumDate = strtotime($rawDatum->response_date);
        if ($rawDatumDate >= $currentObjectTimestamp && $rawDatumDate < $nextObjectTimestamp) {

          $currentPeriodCount_views += $rawDatum->summation_view;
          $currentPeriodCount_clicks += $rawDatum->summation_click;
          if (!empty($rawDatum->summation_view) && !empty($rawDatum->summation_click)) {
            $currentPeriodCount_ctr += round((($rawDatum->summation_click) / ($rawDatum->summation_view) * 100), 4);
          }
        }
      }

      // Now do stuff with it
      switch ($mode) {
        default:
        case 'normal':
          $data_views[$currentObjectTimestamp] = $currentPeriodCount_views;
          $data_clicks[$currentObjectTimestamp] = $currentPeriodCount_clicks;
          $data_ctr[$currentObjectTimestamp] = $currentPeriodCount_ctr;
          break;
        case 'cumulative':
          $cumulative_views += $currentPeriodCount_views;
          $cumulative_clicks += $currentPeriodCount_clicks;
          $cumulative_ctr += $currentPeriodCount_ctr;
          $data_views[$currentObjectTimestamp] = $cumulative_views;
          $data_clicks[$currentObjectTimestamp] = $cumulative_clicks;
          $data_ctr[$currentObjectTimestamp] = $cumulative_ctr;
          break;
        case 'delta':
          $data_views[$currentObjectTimestamp] = $currentPeriodCount_views - $previous_views;
          $data_clicks[$currentObjectTimestamp] = $currentPeriodCount_clicks - $previous_clicks;
          $data_ctr[$currentObjectTimestamp] = $currentPeriodCount_ctr - $previous_ctr;
          $previous_views = $currentPeriodCount_views;
          $previous_clicks = $currentPeriodCount_clicks;
          $previous_ctr = $currentPeriodCount_ctr;
          break;
      }
      $currentObject->add(1, $chunk);
    } while ($nextObject->getTimestamp() < $end_tmstmp);

    $data_views_count = count($data_views);
    $data_clicks_count = count($data_clicks);
    $data_ctr_count = count($data_ctr);
    $data = array();
    switch ($type) {

      case 'all':
        $merged_data_array = array_merge($data_views, $data_clicks, $data_ctr);
        $data_count_max = max($data_views_count, $data_clicks_count, $data_ctr_count);
        $data = $data_views;
        break;

      case 'view':
        $merged_data_array = $data_views;
        $data_count_max = $data_views_count;
        $data = $data_views;
        break;

      case 'click':
        $data = $merged_data_array = $data_clicks;
        $data_count_max = $data_clicks_count;
        break;

      case 'CTR':
        $data = $merged_data_array = $data_ctr;
        $data_count_max = $data_ctr_count;
        break;
    }

    // Reprocess label
    $labelStrings = array();
    $labelDate = new Zend_Date();
    foreach ($data as $key => $value) {
      if ($key <= $end_tmstmp) {
        $labelDate->set($key);
        $labelStrings[] = $this->view->locale()->toDate($labelDate, array('size' => 'short'));
      } else {
        $labelDate->set($end_tmstmp);
        $labelStrings[] = date('n/j/y', $end_tmstmp);
      }
    }

    // Let's expand them by 1.1 just for some nice spacing
    $maxVal = max($merged_data_array);

    $minVal = min($merged_data_array);

    $minVal = floor($minVal * ($minVal < 0 ? 1.1 : (1 / 1.1)) / 10) * 10;
    $maxVal = ceil($maxVal * ($maxVal > 0 ? 1.1 : (1 / 1.1)) / 10) * 10;

    // Remove some labels if there are too many
    $xlabelsteps = 1;

    if ($data_count_max > 10) {
      $xlabelsteps = ceil($data_count_max / 10);
    }

    // Remove some grid lines if there are too many
    $xsteps = 1;
    if ($data_count_max > 100) {
      $xsteps = ceil($data_count_max / 100);
    }
    $steps = null;
    if (empty($maxVal)) {
      $steps = 1;
    }

    // Create base chart
    require_once 'OFC/OFC_Chart.php';


    // Make x axis labels
    $x_axis_labels = new OFC_Elements_Axis_X_Label_Set();
    $x_axis_labels->set_steps($xlabelsteps);
    $x_axis_labels->set_labels($labelStrings);

    // Make x axis
    $labels = new OFC_Elements_Axis_X();
    $labels->set_labels($x_axis_labels);
    $labels->set_colour("#416b86");
    $labels->set_grid_colour("#dddddd");
    $labels->set_steps($xsteps);

    // Make y axis
    $yaxis = new OFC_Elements_Axis_Y();
    $yaxis->set_range($minVal, $maxVal, $steps);
    $yaxis->set_colour("#416b86");
    $yaxis->set_grid_colour("#dddddd");

    // Make title
    $translate = Zend_Registry::get('Zend_Translate');
    $titleStr = $translate->_('_COMMUNITYAD_STATISTICS_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $type), '_')));
    $title = new OFC_Elements_Title($titleStr . ' - ' . $this->view->locale()->toDateTime($startObject) . ' to ' . $this->view->locale()->toDateTime($end_tmstmp_object));
    $title->set_style("{font-size: 14px;font-weight: bold;margin-bottom: 10px; color: #777777;}");

    // Make full chart
    $chart = new OFC_Chart();
    $chart->set_bg_colour(Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.graph.bgcolor', '#ffffff'));

    $chart->set_x_axis($labels);
    $chart->add_y_axis($yaxis);

    $view_width = Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.graphview.width', '3');
    $click_width = Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.graphclick.width', '3');
    $ctr_width = Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.graphctr.width', '3');
    $view_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.graphview.color', '#3299CC');
    $click_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.graphclick.color', '#458B00');
    $ctr_color = Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.graphctr.color', '#CD6839');
    $community_temp_file = Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.temp.file', 1);
    if (empty($community_temp_file)) {
      return;
    }

    $view_str = $translate->_('Views');
    $click_str = $translate->_('Clicks');
    $ctr_str = $translate->_('CTR');
    // Make data
    switch ($type) {

      case 'all':
        $graph1 = new OFC_Charts_Line();
        $graph1->set_values(array_values($data_views));
        $graph1->set_key($view_str, '12');
        $graph1->set_width($view_width);
        $graph1->set_dot_size('20');
        $graph1->set_colour($view_color);
        $chart->add_element($graph1);

        $graph2 = new OFC_Charts_Line();
        $graph2->set_values(array_values($data_clicks));
        $graph2->set_key($click_str, '12');
        $graph2->set_width($click_width);
        $graph2->set_colour($click_color);
        $chart->add_element($graph2);

        $graph3 = new OFC_Charts_Line();
        $graph3->set_values(array_values($data_ctr));
        $graph3->set_key($ctr_str, '12');
        $graph3->set_width($ctr_width);
        $graph3->set_colour($ctr_color);
        $chart->add_element($graph3);
        break;

      case 'view':
        $graph1 = new OFC_Charts_Line();
        $graph1->set_values(array_values($data_views));
        $graph1->set_key($view_str, '12');
        $graph1->set_width($view_width);
        $graph1->set_colour($view_color);
        $chart->add_element($graph1);
        break;

      case 'click':
        $graph2 = new OFC_Charts_Line();
        $graph2->set_values(array_values($data_clicks));
        $graph2->set_key($click_str, '12');
        $graph2->set_width($click_width);
        $graph2->set_colour($click_color);
        $chart->add_element($graph2);
        break;

      case 'CTR':
        $graph3 = new OFC_Charts_Line();
        $graph3->set_values(array_values($data_ctr));
        $graph3->set_key($ctr_str, '12');
        $graph3->set_width($ctr_width);
        $graph3->set_colour($ctr_color);
        $chart->add_element($graph3);
        break;
    }

    $chart->set_title($title);

    // Send
    $this->getResponse()->setBody($chart->toPrettyString());
  }

// Browse the Ads of one campaign
  public function browseAdAction() {

    if (!$this->_helper->requireUser()->isValid())
      return;
    if (!$this->_helper->requireAuth()->setAuthParams('communityad', null, 'create')->isValid())
      return;
//    if (!$this->_helper->requireAuth()->setAuthParams('communityad', null, 'edit')->isValid())
//      return;
    $this->removeOldStatistics();


    // Hack navigation
    foreach ($this->_navigation->getPages() as $page) {
      if ($page->route != 'communityad_campaigns')
        continue;
      $page->active = true;
      break;
    }

    $this->view->can_edit = Engine_Api::_()->authorization()->getPermission($this->_viewer->level_id, 'communityad', 'edit');

    $chunk = Zend_Date::DAY;
    $period = Zend_Date::WEEK;
    $start = time();

    // Make start fit to period?
    $startObject = new Zend_Date($start);

    $partMaps = $this->_periodMap[$period];
    foreach ($partMaps as $partType => $partValue) {
      $startObject->set($partValue, $partType);
    }
    $startObject->add(1, $chunk);

    $this->view->is_ajax = $this->_getParam('is_ajax', 0);
    $communityad_browse_data = Zend_Registry::get('communityad_browse_data');
    $this->view->adcampaign_id = $adcampaign_id = $this->_getParam('adcampaign_id');
    $camp = Engine_Api::_()->getItem('adcampaign', $adcampaign_id);
    if ($camp->owner_id != $this->_viewer_id) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    $this->view->camp_title = $camp->name;
    $this->view->formFilter = $formFilter = new Communityad_Form_Admin_Filter();

    $useradsTable = Engine_Api::_()->getDbtable('userads', 'communityad');
    $useradsName = $useradsTable->info('name');

    $packageTable = Engine_Api::_()->getDbtable('packages', 'communityad');
    $packageName = $packageTable->info('name');

    $statsSelect = $useradsTable->select();

    $statsSelect
            ->setIntegrityCheck(false)
            ->from($useradsName, array('userad_id', 'owner_id', 'cads_title', 'ad_type', 'cads_start_date', 'cads_end_date', 'resource_type', 'count_view as views', 'declined', 'count_click as clicks', "(case when count_view <> 0 and  count_click <>0  then  ROUND((count_click / count_view), 7)  else 0 end)   AS CTR", 'count_like as likes', 'payment_status as payment', 'approve_date', 'status', 'enable', 'approved', 'price_model', 'limit_click', 'limit_view', 'expiry_date', 'story_type', 'resource_id'))
            ->join($packageName, $packageName . '.package_id = ' . $useradsName . '.package_id', array($packageName . '.price', $packageName . '.renew', $packageName . '.renew_before'))
            ->where('owner_id = ?', $this->_viewer_id)
            ->where('campaign_id = ?', $adcampaign_id)
            ->distinct(true);

    $values = array();

    if ($formFilter->isValid($this->_getAllParams())) {

      $values = $formFilter->getValues();
      if (empty($values['order']) && empty($values['order_direction'])) {
        $values['order'] = 'userad_id';
        $values['order_direction'] = 'DESC';
      }
    }
    if (empty($communityad_browse_data)) {
      return;
    }
    $this->view->assign($values);

    $statsSelect->order((!empty($values['order']) ? $values['order'] : 'userad_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    $date_select = $useradsTable->select()->from($useradsName, array('MIN(create_date) as earliest_ad_date'))
            ->where('owner_id = ?', $this->_viewer_id)
            ->where('campaign_id = ?', $adcampaign_id);

    $earliest_ad_date = $useradsTable->fetchRow($date_select)->earliest_ad_date;
    $adStaticsLimitDate = Engine_Api::_()->communityad()->getAdStaticsLimitDate();
    if (strtotime($adStaticsLimitDate) > strtotime($earliest_ad_date))
      $earliest_ad_date = $adStaticsLimitDate;
    $this->view->prev_link = 1;
    $this->view->startObject = $startObject = strtotime($startObject);
    $this->view->earliest_ad_date = $earliest_ad_date = strtotime($earliest_ad_date);
    if ($earliest_ad_date > $startObject) {
      $this->view->prev_link = 0;
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($statsSelect);
    // No of item show per page
    $items_per_page = '5';

    $this->view->total_count = $total_count = $paginator->getTotalItemCount();
    $this->view->total_pages = $total_pages = ceil($total_count / $items_per_page);

    $paginator->setItemCountPerPage($items_per_page);
    $paginator->setCurrentPageNumber($this->_getParam('page'));

    $this->view->filterForm = $filterForm = new Communityad_Form_Statistics_Filter();
  }

// Details of Ad
  public function viewAdAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;
    $this->removeOldStatistics();
    if (null !== ($saved = $this->_getParam('state'))) {
      $this->view->saved = $saved;
    }
    // Hack navigation
    foreach ($this->_navigation->getPages() as $page) {
      if ($page->route != 'communityad_campaigns')
        continue;
      $page->active = true;
      break;
    }
    $chunk = Zend_Date::DAY;
    $period = Zend_Date::WEEK;
    $start = time();

    // Make start fit to period?
    $startObject = new Zend_Date($start);
    $community_ad_view = Zend_Registry::get('communityad_view_ad', 0);
    if (empty($community_ad_view)) {
      return;
    }
    $partMaps = $this->_periodMap[$period];
    foreach ($partMaps as $partType => $partValue) {
      $startObject->set($partValue, $partType);
    }
    $startObject->add(1, $chunk);
    $this->view->is_ajax = $this->_getParam('is_ajax', 0);
    $this->view->ad_id = $ad_id = $this->_getParam('ad_id');
    $this->view->list = $userAd = Engine_Api::_()->getItem('userads', $ad_id);
    if (empty($userAd)) {
      return $this->_forward('notfound', 'error', 'core');
    }
    // Check Targeting link show or not
    $optionsProfile = Engine_Api::_()->getDBTable('options', 'communityad')->getAllProfileTypes();
    $count_profile = @count($optionsProfile);
    if (!empty($userAd->profile) && $count_profile > 1) {
      $this->view->linkTarget = $this->view->enableTarget = 1;
    }

    if (empty($userAd->profile) || $count_profile <= 1) {
      $targetFields = Engine_Api::_()->getItemTable('target')->getFields();
      $this->view->enableTarget = count($targetFields);


      $birthday_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('target.birthday', 0);
      $network_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('community.target.network', 0);


      if (!empty($this->view->enableTarget) || !empty($birthday_enable) || (!empty($network_enable) && Engine_Api::_()->communityad()->hasNetworkOnSite() )) {
        $this->view->linkTarget = 0;
        $this->view->target = $userAdTargets = Engine_Api::_()->getDbtable('adtargets', 'communityad')->getUserAdTargets($ad_id);

        if (!empty($userAdTargets)) {
          unset($userAdTargets->userad_id);
          foreach ($userAdTargets as $key => $valueTarget) {
            if (!empty($valueTarget) && $key !== "adtarget_id") {
              $this->view->linkTarget = $this->view->enableTarget = 1;
              break;
            }
          }
        }
      }
    }


    if (!empty($this->_viewer_id))
      $user_level = $this->_viewer->level_id;
    else
      $user_level = Engine_Api::_()->communityad()->getPublicUserLevel();

    $can_showdetail = Engine_Api::_()->authorization()->getPermission($user_level, 'communityad', 'showdetail');

    if (empty($can_showdetail) || ($can_showdetail == 1 && $userAd->owner_id != $this->_viewer_id)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    $can_edit = Engine_Api::_()->authorization()->getPermission($user_level, 'communityad', 'edit');
    $this->view->can_edit = 1;
    if (empty($can_edit) || ($can_edit == 1 && $userAd->owner_id != $this->_viewer_id)) {
      $this->view->can_edit = 0;
    }

    if (!Engine_Api::_()->core()->hasSubject('userad')) {
      Engine_Api::_()->core()->setSubject($userAd);
    }

    if (!$this->_helper->requireAuth()->setAuthParams('communityad', $this->_viewer, 'view')->isValid()) {
      return;
    }

    $start_date = $userAd->cads_start_date;

    if (empty($this->view->is_ajax)) {
      $this->view->filter_form = $filter_form = new Communityad_Form_Statistics_Viewfilter();
      $adStaticsLimitDate = Engine_Api::_()->communityad()->getAdStaticsLimitDate();
      if (strtotime($adStaticsLimitDate) > strtotime($start_date))
        $start_date = $adStaticsLimitDate;
      $filter_form->start_cal->setValue(date('Y-m-d H:i:s', strToTime($start_date)));
    }
    $this->view->showMarkerInDate = $this->showMarkerInDate();
    $this->view->post = $post = 0;
    if ($this->getRequest()->isPost() || !empty($this->view->is_ajax)) {

      $this->view->post = $post = 1;
      if (empty($this->view->is_ajax)) {
        $values = $this->getRequest()->getPost();
      } else {
        $values['start_cal']['date'] = $this->_getParam('start_cal');
        $values['end_cal']['date'] = $this->_getParam('end_cal');
      }
      $this->view->values = $values;
      $filter_form_temp = new Communityad_Form_Statistics_Viewfilter();
      $filter_form_temp->populate($values);
      $values = $filter_form_temp->getValues();
      $this->view->ajax_filter = $values['ajax_filter'];
      $start = strtotime($values['start_cal']);
      $end = strtotime($values['end_cal']);
      $startTime = date('Y-m-d', $start);
      $endTime = date('Y-m-d', $end);
    }

    $statsTable = Engine_Api::_()->getDbtable('adstatistics', 'communityad');
    $statsName = $statsTable->info('name');

    $adcampaignsTable = Engine_Api::_()->getDbtable('adcampaigns', 'communityad');
    $adcampaignsName = $adcampaignsTable->info('name');

    $packageTable = Engine_Api::_()->getDbtable('packages', 'communityad');
    $packageName = $packageTable->info('name');

    $communityad_table = Engine_Api::_()->getItemTable('userads');
    $communityad_table_name = $communityad_table->info('name');

    $statsSelect = $statsTable->select();
    $statsSelect
            ->from($statsName, array('adstatistic_id', 'response_date', 'SUM(value_view) as views', 'SUM(value_click) as clicks'))
            ->where($statsName . '.userad_id = ?', $ad_id)
            ->group("DATE_FORMAT(" . $statsName . " .response_date, '%Y-%m-%d')")
            ->order($statsName . '.response_date DESC')
            ->distinct(true);

    if (!empty($post)) {
      $statsSelect->where("DATE_FORMAT(" . $statsName . " .response_date, '%Y-%m-%d') >= ?", $startTime)
              ->where("DATE_FORMAT(" . $statsName . " .response_date, '%Y-%m-%d') <= ?", $endTime);
    }

    $date_select = $communityad_table->select()->from($communityad_table_name, array('create_date as earliest_ad_date'))
            ->where("userad_id =?", $ad_id);

    $earliest_ad_date = $communityad_table->fetchRow($date_select)->earliest_ad_date;
    $adStaticsLimitDate = Engine_Api::_()->communityad()->getAdStaticsLimitDate();
    if (strtotime($adStaticsLimitDate) > strtotime($earliest_ad_date))
      $earliest_ad_date = $adStaticsLimitDate;
    $this->view->prev_link = 1;
    $this->view->startObject = $startObject = strtotime($startObject);
    $this->view->earliest_ad_date = $earliest_ad_date = strtotime($earliest_ad_date);
    if ($earliest_ad_date > $startObject) {
      $this->view->prev_link = 0;
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($statsSelect);
    // show no. of item per page
    $items_per_page = '5';

    $this->view->total_count = $total_count = $paginator->getTotalItemCount();
    $this->view->total_pages = $total_pages = ceil($total_count / $items_per_page);

    $paginator->setItemCountPerPage($items_per_page);
    $paginator->setCurrentPageNumber($this->_getParam('page'));

// Code Start for Preview.
    $advertismantId = $ad_id;
    if (!empty($advertismantId)) {
      $communityad_select = $communityad_table->select()
              ->setIntegrityCheck(false)
              ->from($communityad_table_name, array($communityad_table_name . '.*'))
              ->joinright($adcampaignsName, $adcampaignsName . '.adcampaign_id = ' . $communityad_table_name . '.campaign_id', array('adcampaign_id', 'name'))
              ->joinright($packageName, $packageName . '.package_id = ' . $communityad_table_name . '.package_id', array('title as package_name', 'price', 'renew', 'renew_before'))
              ->where("userad_id =?", $advertismantId)
              ->limit(1);
      $fetch_community_ads = $communityad_select->query()->fetchAll();
      if (!empty($fetch_community_ads)) {
        $this->view->communityads_array = $fetch_community_ads[0];
        $this->view->hideCustomUrl = Engine_Api::_()->communityad()->hideCustomUrl();
      } else {
        return;
      }
    }
// Code End for Preview.

    $this->view->filterForm = $filterForm = new Communityad_Form_Statistics_Filter();
  }

  public function exportReportAction() {

    if (!$this->_helper->requireUser()->isValid())
      return;
    $this->removeOldStatistics();
    // Get viewer's Ads and campaigns
    $useradsTable = Engine_Api::_()->getDbtable('userads', 'communityad');
    $useradsName = $useradsTable->info('name');

    // to calculate the oldest ad's creation year
    $select = $useradsTable->select();
    $select
            ->from($useradsName, array('userad_id', 'MIN(cads_start_date) as min_year'))
            ->where('owner_id = ?', $this->_viewer_id)
            ->group('userad_id')
            ->limit(1)
    ;
    $this->view->no_ads = 0;
    $min_year = $useradsTable->fetchRow($select);
    $date = explode(' ', $min_year['min_year']);
    $yr = explode('-', $date[0]);
    $current_yr = date('Y', time());
    $year_array = array();
    $year_array[$current_yr] = $current_yr;
    while ($current_yr != $yr[0]) {
      $current_yr--;
      $year_array[$current_yr] = $current_yr;
    }

    $adcampaignsTable = Engine_Api::_()->getDbtable('adcampaigns', 'communityad');
    $adcampaignsName = $adcampaignsTable->info('name');

    $selectobj = $useradsTable->select();
    $selectobj
            ->setIntegrityCheck(false)
            ->from($useradsName, array($useradsName . '.userad_id', $useradsName . '.cads_title', $adcampaignsName . '.adcampaign_id', $adcampaignsName . '.name'))
            ->join($adcampaignsName, $adcampaignsName . '.adcampaign_id = ' . $useradsName . '.campaign_id', array())
            ->where($useradsName . '.owner_id = ?', $this->_viewer_id)
            ->where($adcampaignsName . '.owner_id = ?', $this->_viewer_id)
            ->distinct(true)
    ;
    $this->view->showMarkerInDate = $this->showMarkerInDate();
    $data = $useradsTable->fetchAll($selectobj);
    $data_array = $data->toarray();
    if (empty($data_array)) {
      $this->view->no_ads = 1;
    }
    $ads = array();
    $camps = array();
    foreach ($data as $datum) {
      $ads[$datum['userad_id']] = $datum['cads_title'] . ' (' . $datum['name'] . ')';
      $camps[$datum['adcampaign_id']] = $datum['name'];
    }
    $this->view->reportform = $reportform = new Communityad_Form_Statistics_Report();
    $reportform->ad_list->setMultiOptions($ads);
    $reportform->campaign_list->setMultiOptions($camps);
    $reportform->year_start->setMultiOptions($year_array);
    $reportform->year_end->setMultiOptions($year_array);
    $this->view->prefield = 0;

    // populate form
    if (!empty($_GET['type'])) {
      $this->view->prefield = 1;
      $reportform->populate($_GET);
      $this->view->filter_value = $_GET['filter'];

      // Get Form Values
      $values = $reportform->getValues();
      $start_cal_date = $values['start_cal'];
      $end_cal_date = $values['end_cal'];
      $start_tm = strtotime($start_cal_date);
      $end_tm = strtotime($end_cal_date);
      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      $url_values = explode('?', $url_string);

      if (empty($values['format_report'])) {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('start_daily_time' => $start_tm, 'end_daily_time' => $end_tm), 'communityad_webpagereport', true) . '?' . $url_values[1];
      } else {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('module' => 'communityad', 'controller' => 'statistics', 'action' => 'export-excel', 'start_daily_time' => $start_tm, 'end_daily_time' => $end_tm), 'default', true) . '?' . $url_values[1];
      }
      // Session Object
      $session = new Zend_Session_Namespace('empty_redirect');
      if (isset($session->empty_session) && !empty($session->empty_session)) {
        unset($session->empty_session);
      } else {
        header("Location: $url");
      }
    }
    $this->view->empty = $this->_getParam('empty', 0);
  }

  public function exportExcelAction() {
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
          'user_report' => '5',
          'type' => 'Advertising Performance',
          'viewer_id' => $this->_viewer_id,
              ), $values);
      $this->view->values = $values;
      // REPORT TYPE
      if ($values['type'] == 'Advertising Performance') {
        $adstatisticsTable = Engine_Api::_()->getDbTable('adstatistics', 'communityad');
        $stat_object = $adstatisticsTable->getStats($values);
        $rawdata = $adstatisticsTable->fetchAll($stat_object);
      }
      $this->view->rawdata = $rawdata;
      $rawdata_array = $rawdata->toarray();
      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      $url_values = explode('?', $url_string);
      $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('empty' => 1), 'communityad_reports', true) . '?' . $url_values[1];
      if (empty($rawdata_array)) {
        // Session Object
        $session = new Zend_Session_Namespace('empty_redirect');
        $session->empty_session = 1;
        header("Location: $url");
      }
    }
  }

  public function exportWebpageAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->post = $post = 0;
    // Hack navigation
    foreach ($this->_navigation->getPages() as $page) {
      if ($page->route != 'communityad_reports')
        continue;
      $page->active = true;
      break;
    }

    $start_daily_time = $this->_getParam('start_daily_time', time());
    $end_daily_time = $this->_getParam('end_daily_time', time());
    if (!empty($_GET)) {
      $this->view->post = $post = 1;
      $values = $_GET;
      $values = array_merge(array(
          'start_daily_time' => $start_daily_time,
          'end_daily_time' => $end_daily_time,
          'user_report' => '5',
          'type' => 'Advertising Performance',
          'viewer_id' => $this->_viewer_id,
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
          if (!empty($totalViews)) {
            $this->view->totalCtr = $totalCtr = round(($totalClicks / $totalViews) * 100, 4);
          }
          else
            $this->view->totalCtr = $totalCtr = 0;
        }
        $stat_object = $adstatisticsTable->getStats($values);
        $rawdata = $adstatisticsTable->fetchAll($stat_object);
      }
      $this->view->rawdata = $rawdata;
      $rawdata_array = $rawdata->toarray();
      $url_string = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
      $url_values = explode('?', $url_string);
      $url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('empty' => 1), 'communityad_reports', true) . '?' . $url_values[1];
      if (empty($rawdata_array)) {

        // Session Object
        $session = new Zend_Session_Namespace('empty_redirect');
        $session->empty_session = 1;
        header("Location: $url");
      }
    }
  }

  public function showMarkerInDate() {
    $localeObject = Zend_Registry::get('Locale');
    $dateLocaleString = $localeObject->getTranslation('long', 'Date', $localeObject);
    $dateLocaleString = preg_replace('~\'[^\']+\'~', '', $dateLocaleString);
    $dateLocaleString = strtolower($dateLocaleString);
    $dateLocaleString = preg_replace('/[^ymd]/i', '', $dateLocaleString);
    $dateLocaleString = preg_replace(array('/y+/i', '/m+/i', '/d+/i'), array('y', 'm', 'd'), $dateLocaleString);
    $dateFormat = $dateLocaleString;
    return $dateFormat == "mdy" ? 1 : 0;
  }

  public function removeOldStatistics() {
    $campsIDS = Engine_Api::_()->getDbtable('adcampaigns', 'communityad')->getCampaignsIds($this->_viewer_id);
    if ($campsIDS)
      Engine_Api::_()->getDbTable('adstatistics', 'communityad')->removeOldStatistics(array('adcampaign_id IN(?)' => $campsIDS));
  }

}

?>
