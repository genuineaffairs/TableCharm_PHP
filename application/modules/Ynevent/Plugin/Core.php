<?php

class Ynevent_Plugin_Core
{

	public function onBeforeActivityNotificationsUpdate($event)
	{
		/*
		$table = Engine_Api::_() -> getDbTable('remind', "ynevent");
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$table -> setRemindTime($event_id = 0, $user_id = $viewer -> getIdentity(), $remain = 0);
		*/
		$viewer = Engine_Api::_()->user()->getViewer();
        $table = Engine_Api::_()->getDbTable("remind", "ynevent");
        $reminds = $table->getRemindEvents($viewer->getIdentity());
		$view  = Zend_Registry::get('Zend_View');
        if (count($reminds)) {
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

            foreach ($reminds as $event) {
               
                $date = $view->locale()->toDateTime($event->starttime);
                $params = array("label" => $date);
                $notifyApi->addNotification($viewer, $viewer, $event, 'ynevent_remind', $params);
                //set remind is read
                $remind = $table->getRemindRow($event->event_id, $viewer->getIdentity());
                $remind->is_read = 1;
                $remind->save();
            }
        }
	}

	public function onStatistics($event)
	{
		$table = Engine_Api::_() -> getItemTable('event');
		$select = new Zend_Db_Select($table -> getAdapter());
		$select -> from($table -> info('name'), 'COUNT(*) AS count');
		$event -> addResponse($select -> query() -> fetchColumn(0), 'event');
	}

	public function onUserDeleteBefore($event)
	{
		$payload = $event -> getPayload();
		if ($payload instanceof User_Model_User)
		{
			// Delete posts
			$postTable = Engine_Api::_() -> getDbtable('posts', 'ynevent');
			$postSelect = $postTable -> select() -> where('user_id = ?', $payload -> getIdentity());
			foreach ($postTable->fetchAll($postSelect) as $post)
			{
				//$post->delete();
			}

			// Delete topics
			$topicTable = Engine_Api::_() -> getDbtable('topics', 'ynevent');
			$topicSelect = $topicTable -> select() -> where('user_id = ?', $payload -> getIdentity());
			foreach ($topicTable->fetchAll($topicSelect) as $topic)
			{
				//$topic->delete();
			}

			// Delete photos
			$photoTable = Engine_Api::_() -> getDbtable('photos', 'ynevent');
			$photoSelect = $photoTable -> select() -> where('user_id = ?', $payload -> getIdentity());
			foreach ($photoTable->fetchAll($photoSelect) as $photo)
			{
				$photo -> delete();
			}

			// Delete memberships
			$membershipApi = Engine_Api::_() -> getDbtable('membership', 'ynevent');
			foreach ($membershipApi->getMembershipsOf($payload) as $event)
			{
				$membershipApi -> removeMember($event, $payload);
			}

			// Delete events
			$eventTable = Engine_Api::_() -> getDbtable('events', 'ynevent');
			$eventSelect = $eventTable -> select() -> where('user_id = ?', $payload -> getIdentity());
			foreach ($eventTable->fetchAll($eventSelect) as $event)
			{
				$event -> delete();
			}
		}
	}

	public function addActivity($event)
	{
		$payload = $event -> getPayload();
		$subject = $payload['subject'];
		$object = $payload['object'];

		// Only for object=event
		if ($object instanceof Event_Model_Event && Engine_Api::_() -> authorization() -> context -> isAllowed($object, 'member', 'view'))
		{
			$event -> addResponse(array(
				'type' => 'event',
				'identity' => $object -> getIdentity()
			));
		}
	}

	public function getActivity($event)
	{
		// Detect viewer and subject
		$payload = $event -> getPayload();
		$user = null;
		$subject = null;
		if ($payload instanceof User_Model_User)
		{
			$user = $payload;
		}
		else
		if (is_array($payload))
		{
			if (isset($payload['for']) && $payload['for'] instanceof User_Model_User)
			{
				$user = $payload['for'];
			}
			if (isset($payload['about']) && $payload['about'] instanceof Core_Model_Item_Abstract)
			{
				$subject = $payload['about'];
			}
		}
		if (null === $user)
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			if ($viewer -> getIdentity())
			{
				$user = $viewer;
			}
		}
		if (null === $subject && Engine_Api::_() -> core() -> hasSubject())
		{
			$subject = Engine_Api::_() -> core() -> getSubject();
		}

		// Get feed settings
		$content = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('activity.content', 'everyone');

		// Get event memberships
		if ($user)
		{
			$data = Engine_Api::_() -> getDbtable('membership', 'ynevent') -> getMembershipsOfIds($user);
			if (!empty($data) && is_array($data))
			{
				$event -> addResponse(array(
					'type' => 'event',
					'data' => $data,
				));
			}
		}
	}

	public function onEventUpdateAfter($event)
	{

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$event = $event -> getPayload();
		if (!($event instanceof Ynevent_Model_Event))
		{
			return;
		}

		//Update remind_time in event_remind if exist
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$remindTable = Engine_Api::_() -> getDbtable('remind', 'ynevent');
		$remind = $remindTable -> getRemindRow($event -> getIdentity(), $viewer -> getIdentity());
		if (count($remind) > 0)
		{
			if ($remind -> is_read == 0)
			{
				$remain = $remind -> remain_time;
				//                $date_start = new DateTime($event->starttime);
				//                date_sub($date_start, date_interval_create_from_date_string("$remind->remain_time minutes"));
				//                $remind->remind_time = date_format($date_start, "Y-m-d H:i:s");
				$dayRemind = strtotime("-$remain minutes", strtotime($event -> starttime));
				$dayRemind = date('Y-m-d H:i:s', $dayRemind);
				$remind -> remind_time = $dayRemind;
				$remind -> save();
			}
		}
	}

	public function onActivityActionCreateAfter($event)
	{

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$action = $event -> getPayload();
		if (!($action instanceof Activity_Model_Action))
		{
			return;
		}
		if ($action -> type == "ynevent_join")
		{

			$table = Engine_Api::_() -> getDbTable('follow', 'ynevent');
			$row = $table -> getFollowEvent($action -> object_id, $action -> subject_id);
			if (!$row)
			{
				$values = array(
					'resource_id' => $action -> object_id,
					'user_id' => $action -> subject_id,
					'follow' => 0
				);
				$row = $table -> createRow();
				$row -> setFromArray($values);
				$db = $table -> getAdapter();
				$db -> beginTransaction();
				try
				{
					$row -> save();
					$db -> commit();
				}
				catch (Exception $e)
				{
					$db -> rollBack();
					throw $e;
				}
			}
		}
	}
	
	public function onVideoCreateAfter($event){
		$payload = $event->getPayload();
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$view  = Zend_Registry::get('Zend_View');
		$event_id = $request->getParam("subject_id", null);
		$widget_id = $request->getParam("tab", null);
		if($event_id){
			$type = $payload->getType();
			if ($type == 'video')
			{
				$key = 'predispatch_url:'.$request->getParam('module').'.index.view';
				$value = $view->url(array('id'=>$event_id, 'tab' => $widget_id), 'event_profile', true);
				$_SESSION[$key]= $value;
			}
		}
			
	}
	

}
