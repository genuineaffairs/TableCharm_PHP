<?php

class Ynevent_IndexController extends Core_Controller_Action_Standard {

	public function init() {
		if (!$this->_helper->requireAuth()->setAuthParams('event', null, 'view')->isValid())
		return;

		$id = $this->_getParam('event_id', $this->_getParam('id', null));
		if ($id) {
			$event = Engine_Api::_()->getItem('event', $id);
			if ($event) {

				Engine_Api::_()->core()->setSubject($event);
			}
		}
	}

	public function browseAction() {
		// Prepare
		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('event', null, 'create');

		$filter = $this->_getParam('filter', 'future');
		if ($filter != 'past' && $filter != 'future')
		$filter = 'future';
		$this->view->filter = $filter;

		// Create form
		$this->view->formFilter = $formFilter = new Ynevent_Form_Filter_Browse();
		$defaultValues = $formFilter->getValues();

		if (!$viewer || !$viewer->getIdentity()) {
			$formFilter->removeElement('view');
		}

		$val = $this->_getAllParams();

		if(isset($val['start_date'])){
			//$val['start_date'] = date('m-d-Y',strtotime($val['start_date']));
		}

		// Populate form data
		if ($formFilter->isValid($val)) {
			$this->view->formValues = $values = $formFilter->getValues();
		} else {
			$formFilter->populate($defaultValues);
			$this->view->formValues = $values = array();
			$this->view->message = "The search value is not valid !";
			return;
		}

		// Prepare data
		$this->view->formValues = $values = array_merge($formFilter->getValues(), $_GET);

		if ($viewer->getIdentity() && @$values['view'] == 5) {
			$values['users'] = array();
			foreach ($viewer->membership()->getMembersInfo(true) as $memberinfo) {
				$values['users'][] = $memberinfo->user_id;
			}
		}
		if ($viewer->getIdentity() && @$values['view'] == 4) {
			$followTable = Engine_Api::_()->getDbtable('follow', 'ynevent');
			$values['events'] = array();
			foreach ($followTable->getFollowEvents($viewer->user_id) as $event) {
				$values['events'][] = $event->resource_id;
			}
		} else {
			if ($viewer->getIdentity() && @$values['view'] != null) {
				$memberTable = Engine_Api::_()->getDbtable('membership', 'ynevent');
				$values['events'] = array();
				foreach ($memberTable->getMemberEvents($viewer->user_id, $values['view']) as $event) {
					$values['events'][] = $event->resource_id;
				}
			}
		}

		//search in sub categories
		if (!empty($values['category_id']) && $values['category_id'] > 0) {
			$parentCat = $values['category_id'];
			$parentNode = Engine_Api::_()->getDbtable('categories', 'ynevent')->getNode($parentCat);
			if ($parentNode) {
				$childsNode = $parentNode->getAllChildrenIds();
				$values['arrayCat'] = $childsNode;
			}
		}

		if (!empty($values['start_date'])) {
			$temp = explode("-",$values['start_date']);
			//Date format is Y-m-d;
			if (count($temp) == 3 )
			$values['start_date'] = $temp[0]."-". $temp[1]."-".$temp[2];
		}
		if (!empty($values['end_date'])) {
			$temp = explode("-",$values['end_date']);
			//Date format is Y-m-d;
			if (count($temp) == 3 )
			$values['end_date'] = $temp[0]."-". $temp[1]."-".$temp[2];
		}

		$values['search'] = 1;
		if ($selected_day = $this->_getParam('selected_day')) {
			$values['selected_day'] = $selected_day;
		} else {
			if ($filter == "past") {
				$values['past'] = 1;
			} else {
				$values['future'] = 1;
				$values['order'] = new Zend_Db_Expr("ABS(TIMESTAMPDIFF(SECOND,NOW(), starttime))");
				$values['direction'] = 'asc';
			}
		}
		// check to see if request is for specific user's listings
		if (($user_id = $this->_getParam('user'))) {
			$values['user_id'] = $user_id;
		}

		//request for selected day
		if ($selected_day = $this->_getParam('selected_day')) {
			$values['selected_day'] = $selected_day;
		}

		// Get paginator
		$this->view->paginator = $paginator = Engine_Api::_()->getItemTable('event')
		->getEventPaginator($values);
		$paginator->setCurrentPageNumber($this->_getParam('page'));

		// Render
		$this->_helper->content->setEnabled();
	}

	public function manageAction() {
		// Create form
		if (!$this->_helper->requireAuth()->setAuthParams('event', null, 'edit')->isValid())
		return;

		// Get navigation
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('ynevent_main');

		// Render
    	$this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;
			
		// Get quick navigation
		$this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('ynevent_quick');

		$this->view->formFilter = $formFilter = new Ynevent_Form_Filter_Manage();
		$defaultValues = $formFilter->getValues();

		// Populate form data
		if ($formFilter->isValid($this->_getAllParams())) {
			$this->view->formValues = $values = $formFilter->getValues();
		} else {
			$formFilter->populate($defaultValues);
			$this->view->formValues = $values = array();
		}

		$viewer = Engine_Api::_()->user()->getViewer();
		$table = Engine_Api::_()->getDbtable('events', 'ynevent');
		$tableName = $table->info('name');

		// Only mine
		if (@$values['view'] == 2) {
			$select = $table->select()
			->where('user_id = ?', $viewer->getIdentity());
		}
		// All membership
		else {
			$membership = Engine_Api::_()->getDbtable('membership', 'ynevent');
			$select = $membership->getMembershipsOfSelect($viewer);
		}

		if (!empty($values['text'])) {
			$select->where("`{$tableName}`.title LIKE ?", '%' . $values['text'] . '%');
		}
		$select->order('starttime ASC');
		$select->group('repeat_group');

		$this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$this->view->text = $values['text'];
		$this->view->view = $values['view'];
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page'));

		// Check create
		$this->view->canCreate = $canCreate = Engine_Api::_()->authorization()->isAllowed('event', null, 'create');
	}

	public function createAction() {

		if (!$this->_helper->requireUser->isValid())
		return;
		if (!$this->_helper->requireAuth()->setAuthParams('event', null, 'create')->isValid())
		return;
		
		// Render
    	$this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;
		
		// Get navigation
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('ynevent_main');

		$viewer = Engine_Api::_()->user()->getViewer();
		$parent_type = $this->_getParam('parent_type');
		$parent_id = $this->_getParam('parent_id', $this->_getParam('subject_id'));

		if ($parent_type == 'group' && Engine_Api::_()->hasItemType('group')) {
			$this->view->group = $group = Engine_Api::_()->getItem('group', $parent_id);
			if (!$this->_helper->requireAuth()->setAuthParams($group, null, 'event')->isValid()) {
				return;
			}
		} else {
			$parent_type = 'user';
			$parent_id = $viewer->getIdentity();
		}

		// Create form
		$this->view->parent_type = $parent_type;
		$this->view->gEndDate = Engine_Api::_() -> getApi('settings', 'core')->getSetting("ynevent.day","");
		$this->view->form = $form = new Ynevent_Form_Create(array(
				'parent_type' => $parent_type,
				'parent_id' => $parent_id
		));

		// Not post/invalid
		if (!$this->getRequest()->isPost()) {
			return;
		}

		if (!$form->isValid($this->getRequest()->getPost())) {
			return;
		}
		// Process

		$values = $form->getValues();


			

		//Start time of event
		$first_date = $values['starttime'];
		//End time of event
		$first_end_date = $values['endtime'];

		list($year, $month, $day) = explode('-', date("Y-m-d",strtotime($first_date)));
		
		$maxInstance = Engine_Api::_() -> getApi('settings', 'core')-> getSetting('ynevent.instance',50);
		if($maxInstance=='')
			$maxInstance = 50;
		
		//If repeat
		if($values['repeat_type']== 1)
		{
			//End repeat date
			$configDate = Engine_Api::_() -> getApi('settings', 'core')-> getSetting('ynevent.day','');
			if($values['starttime'] > $values['repeatend']){
				$form->addError("Start time of the event must be less than the end repeat time");
				return;
			}
			
			
			if($configDate != '' && $values['repeatend'] > $configDate){				
				$values['repeatend'] = $configDate;
			}

			$repeat_end =	strtotime($values['repeatend']);
			$repeat_end =	date('Y-m-d 23:59:59', $repeat_end);

			// 1, 7, monthly~30
			$step = $values['repeat_frequency'];

			//Duration between starttime and endtime
			//$duration = Engine_Api::_()->ynevent()->dateDiff($values['starttime'],$values['endtime']);
			$duration = Engine_Api::_()->ynevent()->dateDiffBySec($values['starttime'],$values['endtime']);

			//Start of repeat
			$loopstart = $first_date;
			$i = 1;

			//When start date still <= end repeat date
			while($loopstart <= $repeat_end ){
				list($year1, $month1, $day1) = explode('-', date("Y-m-d",strtotime($loopstart)));

				//If not monthly repeat
				if($step != 30)				{
					$arrStart[] = $loopstart;
					$loopstart = Engine_Api::_()->ynevent()->dateAdd($loopstart,$step);
				}
				else{
					if($day == $day1)
					$arrStart[] = $loopstart;
					$loopstart = Engine_Api::_()->ynevent()->monthAdd($first_date,$i);
					$i++;
				}
			}
		}
		else{
			//Not repeat event
			$arrStart[] = $first_date;
		}
		
		if($maxInstance <= count($arrStart)){
			$str = $this->view->translate(
            			array(
            				'You are allowed creating only %s event in the repeat chain.', 
            				'You are allowed creating only %s events in the repeat chain.', 
            				$maxInstance
            			), 
            			$this->view->locale()->toNumber($maxInstance)
            		);
			//$str = "You are allowed creating only {$maxInstance} in the repeat event chain.";
			$form->addError($str);
			return;
		}

		//Set value
		$values['user_id'] = $viewer->getIdentity();
		$values['parent_type'] = $parent_type;
		$values['parent_id'] = $parent_id;
		if ($parent_type == 'group' && Engine_Api::_()->hasItemType('group') && empty($values['host'])) {
			$values['host'] = $group->getTitle();
		}
			
		$db = Engine_Api::_()->getDbtable('events', 'ynevent')->getAdapter();
		$db->beginTransaction();

		try {
			// Create event
			$table = Engine_Api::_()->getDbtable('events', 'ynevent');
				
			//Generate repeat group value
			$values['repeat_group'] = microtime(true)*10000;
				
			//type = 0 : not repeat
			//type = 1 : repeat
			$type = $values['repeat_type'];
				
			//if($values['repeat_type']== 1){
			$repeat_order = 0;
			if(is_array($arrStart)){
				foreach ($arrStart as $key => $value) {
					$repeat_order++;
					$values['repeat_order'] = $repeat_order;

					//check maxinstance
					if($maxInstance >= $repeat_order){

						$event = $table->createRow();

						//Set viewer time zone
						//echo 'viewer : ' . $viewer->timezone."<br/>";
						$oldTz = date_default_timezone_get();
						date_default_timezone_set($viewer->timezone);
						$start = strtotime($values['starttime']);
						$end = strtotime($values['endtime']);
						date_default_timezone_set($oldTz);
						$values['starttime'] = date('Y-m-d H:i:s', $start);
						$values['endtime'] = date('Y-m-d H:i:s', $end);

						//Repeat
						if($type == 1){
							$values['starttime'] = $value;
							//$values['endtime'] = Engine_Api::_()->ynevent()->dateAdd($value,$duration);//$duration
							$values['endtime'] = Engine_Api::_()->ynevent()->dateAddBySec($value,$duration);//$duration
							/*
							$values['endtime'] = $first_end_date;
							echo "<br/>First end date1 :".$first_end_date;
							$first_end_date = Engine_Api::_()->ynevent()->dateAdd($first_end_date,$step);//$duration
							echo "<br/>First end date2:".$first_end_date;
							*/ 
							$oldTz = date_default_timezone_get();
							date_default_timezone_set($viewer->timezone);
							$start = strtotime($values['starttime']);
							$end = strtotime($values['endtime']);
							date_default_timezone_set($oldTz);
							$values['starttime'] = date('Y-m-d H:i:s', $start);
							$values['endtime'] = date('Y-m-d H:i:s', $end);
								
							$repeat_end =	strtotime($values['repeatend']);
							$oldTz = date_default_timezone_get();
							date_default_timezone_set($oldTz);
							$repeat_end = date('Y-m-d H:i:s', $repeat_end);
							$values['end_repeat'] = $repeat_end;
								
							$values['repeat_type'] = $values['repeat_frequency'];
						}

						$event->setFromArray($values);
						$event->save();

						// Add owner as member
						$event->membership()->addMember($viewer)
						->setUserApproved($viewer)
						->setResourceApproved($viewer);
						 
						// Add owner rsvp
						$event->membership()
						->getMemberInfo($viewer)
						->setFromArray(array('rsvp' => 2))
						->save();
							
						// Add photo
						if (!empty($values['photo'])) {
							$event->setPhoto($form->photo);
						}

						//Add owner follow
						Engine_Api::_()->ynevent()->setEventFollow($event, $viewer);
						 
						// Set auth
						$auth = Engine_Api::_()->authorization()->context;
						 
						if ($values['parent_type'] == 'group') {
							$roles = array('owner', 'member', 'parent_member', 'registered', 'everyone');
						} else {
							$roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
						}
						 
						if (empty($values['auth_view'])) {
							$values['auth_view'] = 'everyone';
						}
						 
						if (empty($values['auth_comment'])) {
							$values['auth_comment'] = 'everyone';
						}
						 
						$viewMax = array_search($values['auth_view'], $roles);
						$commentMax = array_search($values['auth_comment'], $roles);
						$photoMax = array_search($values['auth_photo'], $roles);
						$videoMax = array_search($values['auth_video'], $roles);

						foreach ($roles as $i => $role) {
							$auth->setAllowed($event, $role, 'view', ($i <= $viewMax));
							$auth->setAllowed($event, $role, 'comment', ($i <= $commentMax));
							$auth->setAllowed($event, $role, 'photo', ($i <= $photoMax));
							$auth->setAllowed($event, $role, 'video', ($i <= $videoMax));
						}
						 
						$auth->setAllowed($event, 'member', 'invite', $values['auth_invite']);

						//Add activity only one
						if($repeat_order <= 1){
							// Add action
							$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

							$action = $activityApi->addActivity($viewer, $event, 'ynevent_create');

							if ($action) {
								$activityApi->attachActivity($action, $event);
							}
						}
					}//end check maxinstance
					else{
						echo "bablla";
					}
				}//End foreach
			}
			//}

			// Commit
			$db->commit();

			// Redirect
			$this->_redirectCustom(array('route' => 'event_general', 'action' => 'manage'));
			//return $this->_helper->redirector->gotoRoute(array('id' => $event->getIdentity()), 'event_profile', true);
		} catch (Engine_Image_Exception $e) {
			$db->rollBack();
			$form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}
	}

	public function remindAction() {

		$remain = $this->_getParam('remain_time');
		$event_id = $this->_getParam('event_id');
		$table = Engine_Api::_()->getDbTable('remind', "ynevent");

		$event = Engine_Api::_()->getItem('event',$event_id);

		$tblEvents  = Engine_Api::_() -> getDbTable('events','ynevent');
		//Get all events in group repeat
		$event_list = $tblEvents->getRepeatEvent($event->repeat_group);

		$viewer = Engine_Api::_()->user()->getViewer();
		$user_id = $viewer->getIdentity();
		//Remove series events
		foreach ($event_list as $objevent) {
			$table->setRemindTime($objevent->event_id, $user_id, $remain);
		}

	}

	public function rateAction() {
		$viewer = Engine_Api::_()->user()->getViewer();
		$user_id = $viewer->getIdentity();

		$rating = $this->_getParam('rating');
		$event_id = $this->_getParam('event_id');


		$table = Engine_Api::_()->getDbtable('ratings', 'ynevent');
		$db = $table->getAdapter();
		$db->beginTransaction();

		try {
			Engine_Api::_()->ynevent()->setRating($event_id, $user_id, $rating);

			$event = Engine_Api::_()->getItem('event', $event_id);
			$event->rating = Engine_Api::_()->ynevent()->getRating($event->getIdentity());
			$event->save();

			$db->commit();
		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}

		$total = Engine_Api::_()->ynevent()->ratingCount($event->getIdentity());

		$data = array();
		$data[] = array(
				'total' => $total,
				'rating' => $rating,
		);
		return $this->_helper->json($data);
		$data = Zend_Json::encode($data);
		$this->getResponse()->setBody($data);
	}

	public function draw_calendar($month, $year, $events = array()) {

		/* draw table */
		$viewer = Engine_Api::_()->user()->getViewer();
		$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

		/* table headings */

		$sun = $this->view->translate("Sunday");
		$mon = $this->view->translate("Monday");
		$tue = $this->view->translate("Tuesday");
		$wed = $this->view->translate("Wednesday");
		$thu = $this->view->translate("Thursday");
		$fri = $this->view->translate("Friday");
		$sat = $this->view->translate("Saturday");
		$headings = array($mon, $tue, $wed, $thu, $fri, $sat,$sun);

		$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">' . implode('</td><td class="calendar-day-head">', $headings) . '</td></tr>';

		/* days and weeks vars now ... */
		$running_day = date('w', mktime(0, 0, 0, $month, 1, $year));
		$days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
		$days_in_this_week = 1;
		$day_counter = 0;
		$dates_array = array();
		if($running_day == 0)
		{
			$running_day=6;
		}
		else
		{
			$running_day = $running_day - 1;
		}
		/* row for week one */
		$calendar.= '<tr class="calendar-row">';

		/* print "blank" days until the first of the current week */
		for ($x = 0; $x < $running_day; $x++){
			$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
			$days_in_this_week++;
		}

		/* keep going with days.... */
		for ($list_day = 1; $list_day <= $days_in_month; $list_day++){
			$calendar.= '<td class="calendar-day"><div style="height:100px;">';
			/* add in the day number */
			$calendar.= '<div class="day-number">' . $list_day . '</div>';
			$month1 = $month;
			$list_day1 = $list_day;
			if ($month < 10) {
				$month1 = '0' . $month;
			}
			if ($list_day < 10) {
				$list_day1 = '0' . $list_day;
			}

			$event_day = $year . '-' . $month1 . '-' . $list_day1;
	
			$count = 0;
			$showedViewMoreButton = false;
			$oldDay = $event_day;
			
			if (count($events)) {
				foreach ($events as $event) {
					$startDateObject = new Zend_Date(strtotime($event->starttime));
					if ($viewer && $viewer->getIdentity()) {
						$tz = $viewer->timezone;
						$startDateObject->setTimezone($tz);
					}
					$startDate = $startDateObject->toString('yyyy-MM-dd');
					$event_time = $this->view->locale()->toTime($startDateObject);
						
					if (strcmp($startDate, $event_day) == 0) {
						$count++;
						if ($count <= 3){
							$href = $event->getHref();
							$id = $event->getIdentity();
							$startDateObject = new Zend_Date(strtotime($event->starttime));

							$calendar.= '<a id="ynevent_myevent_' . $id . '" style="font-size: xx-small;"href="' . $event->getHref() . '" class="ynevent">' . $event_time . "-" . $this->view->string()->truncate($event->title, 20) . '</a>';
							$divTooltip = $this->view->partial('_calendar_tooltip.tpl', array('event' => $event));
							$calendar.=$divTooltip;
							$calendar.='<br>';
						}
						else {
							if (!($showedViewMoreButton)){
								$showedViewMoreButton = true;
								$oldDay = $startDate;
							}
						}
					}
					else {
						$count = 0;
						if ($showedViewMoreButton){
							$showedViewMoreButton = false;
							$calendar.= '</div>'.$this->view->htmlLink(
									$this->view->url(array('action' => 'view-more', 'selected_day' => $oldDay), 'event_general'),
									$this->view->translate('View more'),
									array('class' => 'smoothbox', 'style' =>'font-weight: bold;')).'</td>';
						}
					}
				}
			}
			$calendar.= '</div><br /></td>';	
			if ($running_day == 6){
				$calendar.= '</tr>';
				if (($day_counter + 1) != $days_in_month){
					$calendar.= '<tr class="calendar-row">';
				}
				$running_day = -1;
				$days_in_this_week = 0;
			}
			$days_in_this_week++;
			$running_day++;
			$day_counter++;
		}

		/* finish the rest of the days in the week */
		if ($days_in_this_week < 8 && $days_in_this_week > 1){
			for ($x = 1; $x <= (8 - $days_in_this_week); $x++){
				$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
			}
		}

		/* final row */
		$calendar.= '</tr>';

		/* end the table */
		$calendar.= '</table>';

		/** DEBUG * */
		$calendar = str_replace('</td>', '</td>' . "\n", $calendar);
		$calendar = str_replace('</tr>', '</tr>' . "\n", $calendar);

		/* all done, return result */
		return $calendar;
	}

	public function calendarAction() {
		//$headScript = new Zend_View_Helper_HeadScript();
		//$headScript-> appendFile('application/modules/Ynevent/externals/scripts/jquery-1.4.4.min.js');

		if (!$this->_helper->requireUser->isValid())
		return;
		// Get navigation
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('ynevent_main');

		$viewer = Engine_Api::_()->user()->getViewer();
		$oldTz = date_default_timezone_get();
		date_default_timezone_set($viewer->timezone);
		$month = (int) (isset($_GET['month']) ? $_GET['month'] : date('m'));
		$year = (int) (isset($_GET['year']) ? $_GET['year'] : date('Y'));
		date_default_timezone_set($oldTz);

		$search = Engine_Api::_()->ynevent()->getDateSearch($month, $year);

		$this->view->month = $month;
		$this->view->year = $year;

		$eventTable = Engine_Api::_()->getDbTable("events", "ynevent");
		$events = $eventTable->getMyEventsInMonth($viewer->getIdentity(), $search[0], $search[1]);

		if (count($events)) {
			$this->view->events = $events;
		}
		$calendar = $this->draw_calendar($month, $year, $events);
		$this->view->calendar = $calendar;

		$this->view->enableTooltip = $enableTooltip = !Engine_Api::_()->hasModuleBootstrap('ynfbpp');
	}

	public function viewMoreAction(){
		//request for selected day
		if ($selected_day = $this->_getParam('selected_day')) {
			$this->view->selected_day = $values['selected_day'] = $selected_day;
		}
		$eventTbl = Engine_Api::_()->getItemTable('event');
		$this->view->events = $events = $eventTbl->fetchAll($eventTbl->getEventSelect($values));
	}

	public function getcategoriesAction() {
		$parent_id = $this->_getParam('parent_id', $this->_getParam('subject_id'));
		$table = Engine_Api::_()->getDbtable('categories', 'ynevent');
		$parentNode = $table->getNode($parent_id);
		$childs = $parentNode->getChilren();
		$categories = array();
		if ($childs) {
			foreach ($childs as $index => $child) {
				$categories[$index]['id'] = $child->category_id;
				$categories[$index]['title'] = $child->title;
			}
		}
		$this->view->categories = $categories;
	}

	/**
	 * Add location + check google map
	 */
	public function addLocationAction() {
		$this->view->form = $form = new Ynevent_Form_addLocation ();
		if (! $this->getRequest ()->isPost ()) {
			return;
		}
	}

	public function eventBadgeAction()
	{
		$this->_helper->layout->setLayout ( 'default-simple' );
		$event_id = $this->_getParam ( 'event_id' );
		$this->view->status = $status = $this->_getParam ( 'status' );
		$aStatus = str_split($status);
		$name = 0; $attending = 0; $led = 0;
		if (count($aStatus) == 3){
			if ($aStatus[0] == '1') $name = 1;
			if ($aStatus[1] == '1') $attending = 1;
			if ($aStatus[2] == '1') $led = 1;
		}
		$this->view->name = $name;
		$this->view->attending = $attending;
		$this->view->led = $led;

		$event = Engine_Api::_ ()->getItem ( 'event', $event_id );
		if(!$event)
		{
			return $this->_helper->requireAuth->forward ();
		}
		$this->view->event = $event;
	}

	public function promoteCalendarAction() {
		// In smoothbox
		$this->_helper->layout->setLayout('default-simple');

		// process timezone
		$user_tz = date_default_timezone_get();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($viewer -> getIdentity())
		{
			$user_tz = $viewer -> timezone;
		}
		$oldTz = date_default_timezone_get();

		//user time zone
		date_default_timezone_set($user_tz);

		$month = $this ->_getParam('month', null);
		$year = $this ->_getParam('year', null);
		if ($month == null || $year == null)
		{
			$date = date('Y-m-d');
		}
		else
		$date = ("{$year}-{$month}-15");
		$arr = explode('-', $date);
		$day = 0;
		$month = 0;
		$year = 0;

		if (count($arr) == 3)
		{
			$day = $arr[2];
			$month = $arr[1];
			$year = $arr[0];
		}

		if ($day > 31 || $day < 1)
		{
			$day = date('d');
		}

		if ($month < 1 || $month > 12)
		{
			$month = date('m');
		}

		$thisYear = (int)date('Y');

		if ($year < $thisYear - 9 || $year > $thisYear + 9)
		{
			$year = date('Y');
		}

		$this -> view -> day = $day;
		$this -> view -> month = $month;
		$this -> view -> year = $year;


		date_default_timezone_set($oldTz);

		$search = Engine_Api::_()->ynevent()->getDateSearch($month, $year);
		$eventTable = Engine_Api::_()->getItemTable('event');
		//Get first date and last day in month server time zone
		$events = $eventTable->getAllEventsInMonth($search[0], $search[1]);

		$showedEvents = array();
		$auth = Engine_Api::_()->authorization()->context;
		foreach($events as $event) {
			if ($auth->isAllowed($event, $viewer, 'view')) {
				array_push($showedEvents, $event);
			}
		}

		$event_count = array();
		$i = 0;
		if (count($showedEvents)) {
			$eventDates = array();
			foreach ($showedEvents as $event) {
				$t_day = strtotime($event->starttime);
				$oldTz = date_default_timezone_get();
				date_default_timezone_set($user_tz);
				$dateObject = date('Y-n-j', $t_day);
				date_default_timezone_set($oldTz);
				$event_count[$dateObject][] = $event->event_id;
			}
			// date_default_timezone_set($oldTz);
			foreach ($event_count as $index => $evt) {
				$eventDates[$i]['day'] = $index;
				$eventDates[$i]['event_count'] = count($evt);
				$i++;
			}
			$this->view->numberOfEvents = count($eventDates);
			$this->view->eventDates = json_encode($eventDates);
		}

	}

	public function calendarBadgeAction(){
		// In smoothbox
		$this->_helper->layout->setLayout('default-simple');
		// process timezone
		$user_tz = date_default_timezone_get();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($viewer -> getIdentity())
		{
			$user_tz = $viewer -> timezone;
		}
		$oldTz = date_default_timezone_get();

		//user time zone
		date_default_timezone_set($user_tz);

		$month = $this ->_getParam('month', null);
		$year = $this ->_getParam('year', null);
		if ($month == null || $year == null)
		{
			$date = date('Y-m-d');
		}
		else {
			$date = ("{$year}-{$month}-15");
			$this->view->future = true;
		}

		$arr = explode('-', $date);
		$day = 0;
		$month = 0;
		$year = 0;

		if (count($arr) == 3)
		{
			$day = $arr[2];
			$month = $arr[1];
			$year = $arr[0];
		}

		if ($day > 31 || $day < 1)
		{
			$day = date('d');
		}

		if ($month < 1 || $month > 12)
		{
			$month = date('m');
		}

		$thisYear = (int)date('Y');

		if ($year < $thisYear - 9 || $year > $thisYear + 9)
		{
			$year = date('Y');
		}

		$this -> view -> day = $day;
		$this -> view -> month = $month;
		$this -> view -> year = $year;

		date_default_timezone_set($oldTz);

		$search = Engine_Api::_()->ynevent()->getDateSearch($month, $year);
		$eventTable = Engine_Api::_()->getItemTable('event');
		//Get first date and last day in month server time zone
		$events = $eventTable->getAllEventsInMonth($search[0], $search[1]);

		$showedEvents = array();
		$auth = Engine_Api::_()->authorization()->context;
		foreach($events as $event) {
			if ($auth->isAllowed($event, $viewer, 'view')) {
				array_push($showedEvents, $event);
			}
		}

		$event_count = array();
		$i = 0;
		if (count($showedEvents)) {
			$eventDates = array();
			foreach ($showedEvents as $event) {
				$t_day = strtotime($event->starttime);
				$oldTz = date_default_timezone_get();
				date_default_timezone_set($user_tz);
				$dateObject = date('Y-n-j', $t_day);
				date_default_timezone_set($oldTz);
				$event_count[$dateObject][] = $event->event_id;
			}
			// date_default_timezone_set($oldTz);
			foreach ($event_count as $index => $evt) {
				$eventDates[$i]['day'] = $index;
				$eventDates[$i]['event_count'] = count($evt);
				$i++;
			}
			$this->view->numberOfEvents = count($eventDates);
			$this->view->eventDates = json_encode($eventDates);
		}
	}


}