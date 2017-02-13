<?php

class Ecalendar_IndexController extends Core_Controller_Action_Standard
{
	public function init()
	{
		$this->_helper->requireUser();

	}
	public function indexAction()
	{

	}
	
	public function alleventsAction()
	{

	}

	public function eventsAction(){

		$log = Zend_Registry::get('Zend_Log');
		$viewer = Engine_Api::_()->user()->getViewer();
		$membership = Engine_Api::_()->getDbtable('membership', 'event');
		$events = Engine_Api::_()->getDbtable('events', 'event');
		$select = $membership->getMembershipsOfSelect($viewer);
		//$select = $events->select();
		$select->where('starttime >= ?',$this->_getParam("startDate"));
		$select->where('starttime <= ?',$this->_getParam("endDate"));
                $select->where('endtime >= ?',$this->_getParam("startDate"));
		$select->order('starttime ASC');

		$events = $membership->fetchAll($select);
		$data = array();
		
				
		
		foreach ($events as $event){
			$not = 0;
			$waiting=0;
			$maybe=0;
			$attend=0;
			
			$select=$membership->select()->from($membership,array('rsvp','count(*) as cnt'))->where('resource_id = ?',$event->event_id)->group('rsvp');
			$attendings = $membership->fetchAll($select);
				
			foreach ( $attendings as $attending)
			{
				if($attending->rsvp == "2")
				{
					$attend = $attending->cnt;
						
				}
				elseif($attending->rsvp == "1")
				{
					$maybe = $attending->cnt;
				}
				elseif($attending->rsvp == "3")
				{
					$waiting =$attending->cnt;
				}
				else
				{
					$not =$attending->cnt;
				}
			}
			
			$params = array(
			      'route' => 'event_profile',
			      'reset' => true,
			      'id' => $event->event_id,
			);

			$categoryTable = Engine_Api::_()->getDbtable('categories', 'event');
			$category = $categoryTable->find($event->category_id)->current();
			$catName = "";
			if(isset($category)){
				$catName = $category->title;
			}
			$route = $params['route'];
			$reset = $params['reset'];
			unset($params['route']);
			unset($params['reset']);
			$href = Zend_Controller_Front::getInstance()->getRouter()
			->assemble($params, $route, $reset);
			$tmpBody = strip_tags($event->description);
			$desc= ( Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody );


			$data[] = array('title'=>$event->title,
			                'start'=> $this->view->locale()->toDateTime($event->starttime),
			                'end'=>$this->view->locale()->toDateTime($event->endtime),
			                'location'=>$event->location,
			                'description'=>$desc,
			                'host'=>$event->host,
			                'href'=>$href,
			                'category'=>$catName == null ? '' : $catName,
			                'attending'=> $attend == null ? '0' : $attend,
			                'maybe'=> $maybe == null ? '0' : $maybe,
			                'notattend'=> $not  == null ? '0' : $not, 
			                'waiting'=> $waiting == null ? '0' : $waiting


			);

		}
                
                if(Engine_Api::_()->hasModuleBootstrap('sitepageevent')) {
                  $calendarData = Engine_Api::_()->sitepageevent()->getPersonalCalendarCircleEventsData();
                  $data = array_merge($calendarData, $data);
                }

		return $this->_helper->json($data);
	}
	
	public function allAction(){

		$log = Zend_Registry::get('Zend_Log');
		$viewer = Engine_Api::_()->user()->getViewer();
		$membership = Engine_Api::_()->getDbtable('membership', 'event');
		$events = Engine_Api::_()->getDbtable('events', 'event');
		//$select = $membership->getMembershipsOfSelect($viewer);
		$select = $events->select();
		$select->where('starttime >= ?',$this->_getParam("startDate"));
		$select->where('starttime <= ?',$this->_getParam("endDate"));
                $select->where('endtime >= ?',$this->_getParam("startDate"));
		$select->order('starttime ASC');

		$events = $membership->fetchAll($select);
		$data = array();
		
				
		
		foreach ($events as $event){
			$not = 0;
			$waiting=0;
			$maybe=0;
			$attend=0;
			
			$select=$membership->select()->from($membership,array('rsvp','count(*) as cnt'))->where('resource_id = ?',$event->event_id)->group('rsvp');
			$attendings = $membership->fetchAll($select);
				
			foreach ( $attendings as $attending)
			{
				if($attending->rsvp == "2")
				{
					$attend = $attending->cnt;
						
				}
				elseif($attending->rsvp == "1")
				{
					$maybe = $attending->cnt;
				}
				elseif($attending->rsvp == "3")
				{
					$waiting =$attending->cnt;
				}
				else
				{
					$not =$attending->cnt;
				}
			}
			
			$params = array(
			      'route' => 'event_profile',
			      'reset' => true,
			      'id' => $event->event_id,
			);

			$categoryTable = Engine_Api::_()->getDbtable('categories', 'event');
			$category = $categoryTable->find($event->category_id)->current();
			$catName = "";
			if(isset($category)){
				$catName = $category->title;
			}
			$route = $params['route'];
			$reset = $params['reset'];
			unset($params['route']);
			unset($params['reset']);
			$href = Zend_Controller_Front::getInstance()->getRouter()
			->assemble($params, $route, $reset);
			$tmpBody = strip_tags($event->description);
			$desc= ( Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody );


			$data[] = array('title'=>$event->title,
			                'start'=> $this->view->locale()->toDateTime($event->starttime),
			                'end'=>$this->view->locale()->toDateTime($event->endtime),
			                'location'=>$event->location,
			                'description'=>$desc,
			                'host'=>$event->host,
			                'href'=>$href,
			                'category'=>$catName == null ? '' : $catName,
			                'attending'=> $attend == null ? '0' : $attend,
			                'maybe'=> $maybe == null ? '0' : $maybe,
			                'notattend'=> $not  == null ? '0' : $not, 
			                'waiting'=> $waiting == null ? '0' : $waiting


			);

		}
			

		return $this->_helper->json($data);
	}

}
