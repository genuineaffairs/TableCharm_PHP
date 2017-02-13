<?php

class Ynevent_Widget_ProfileCalendarController extends Engine_Content_Widget_Abstract
{
	public function indexAction() {
		//$headScript = new Zend_View_Helper_HeadScript();
		//$headScript-> appendFile('application/modules/Ynevent/externals/scripts/jquery-1.4.4.min.js');
		
		// Don't render this if not authorized
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!Engine_Api::_()->core()->hasSubject()) {
			return $this->setNoRender();
		}
		
		$subject = Engine_Api::_()->core()->getSubject('event');
		if (!$subject->authorization()->isAllowed($viewer, 'view')) {
			return $this->setNoRender();
		}
		// Prepare data
		$this->view->event = $event = $subject;
	
		if ($subject->repeat_type == '0') {
			return $this->setNoRender();
		}
		
		//Getting user timezone or guest timezone
		$timezone = null;
		if ($viewer->getIdentity()){
			$timezone = $viewer->timezone;
		}
		else {
			if( Zend_Registry::isRegistered('timezone') ) {
		      $timezone = Zend_Registry::get('timezone');
		    }
		    if( null !== $timezone ) {
		      $timezone = date_default_timezone_get();
		    }
		}
		
		//Convert server time to local time
		$oldTz = date_default_timezone_get();
		date_default_timezone_set($timezone);
		$month = (int) (isset($_GET['month']) ? $_GET['month'] : date('m'));
		$year = (int) (isset($_GET['year']) ? $_GET['year'] : date('Y'));
		date_default_timezone_set($oldTz);

		//Searching deployment
		$search = Engine_Api::_()->ynevent()->getDateSearch($month, $year);
		$this->view->month = $month;
		$this->view->year = $year;

		$eventTable = Engine_Api::_()->getDbTable("events", "ynevent");
		$events = $eventTable->getRecurrenceInMonth($viewer->getIdentity(), $event, $search[0], $search[1]);
		
		if (count($events)) {
			$this->view->events = $events;
		}
		
		$calendar = $this->draw_mini_calendar($month, $year, $events);
		$this->view->calendar = $calendar;
		$this->view->enableTooltip = $enableTooltip = !Engine_Api::_()->hasModuleBootstrap('ynfbpp');
	}
	
	public function draw_mini_calendar($month, $year, $events = array()) {

		/* draw table */
		$viewer = Engine_Api::_()->user()->getViewer();
		$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

		/* table headings */

		$sun = $this->view->translate("Sun");
		$mon = $this->view->translate("Mon");
		$tue = $this->view->translate("Tue");
		$wed = $this->view->translate("Wed");
		$thu = $this->view->translate("Thu");
		$fri = $this->view->translate("Fri");
		$sat = $this->view->translate("Sat");
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
		for ($x = 0; $x < $running_day; $x++):
		$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
		$days_in_this_week++;
		endfor;

		/* keep going with days.... */
		for ($list_day = 1; $list_day <= $days_in_month; $list_day++):
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
					$href = $event->getHref();
					$id = $event->getIdentity();
					$startDateObject = new Zend_Date(strtotime($event->starttime));

					$calendar.= '<a id="ynevent_myevent_' . $id . '" style="font-size: xx-small;"href="' . $event->getHref() . '" class="ynevent">' . $event_time . "-" . $this->view->string()->truncate($event->title, 20) . '</a>';
					$divTooltip = $this->view->partial('_calendar_tooltip.tpl', array('event' => $event));
					$calendar.=$divTooltip;
					$calendar.='<br>';
				}
			}
		}

		$calendar.= '</div></td>';
		if ($running_day == 6):
		$calendar.= '</tr>';
		if (($day_counter + 1) != $days_in_month):
		$calendar.= '<tr class="calendar-row">';
		endif;
		$running_day = -1;
		$days_in_this_week = 0;
		endif;
		$days_in_this_week++;
		$running_day++;
		$day_counter++;
		endfor;

		/* finish the rest of the days in the week */
		if ($days_in_this_week < 8 && $days_in_this_week > 1):
		for ($x = 1; $x <= (8 - $days_in_this_week); $x++):
		$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
		endfor;
		endif;

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
      
}