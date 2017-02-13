<?php

class Ynevent_EventController extends Core_Controller_Action_Standard {
	private static $_log;

    /**
     * @return Zend_Log
     */
    public function getLog()
    {
        if (self::$_log == null)
        {
            self::$_log = new Zend_Log(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/Ynevent.log'));
        }
        return self::$_log;
    }

    /**
     * write log to temporary/h.log
     * @param string $intro
     * @param string $message
     * @param string $type [Zend_Log::INFO]
     */
    public function log($intro = null, $message, $type)
    {
        return $this -> getLog() -> log(PHP_EOL . $intro . PHP_EOL . $message, $type);
    }
    public function init() {
        $id = $this->_getParam('event_id', $this->_getParam('id', null));
        if ($id) {
            $event = Engine_Api::_()->getItem('event', $id);
            if ($event) {
                Engine_Api::_()->core()->setSubject($event);
            }
        }
    }
	
	private function setNotify(Ynevent_Model_Event $event,$viewer,$type = '',$event_next = NULL){		
		//$db->beginTransaction();
        try {
            // Rebuild privacy
            $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($event) as $action) {
                $actionTable->resetActivityBindings($action);
            }

            //$db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
		
        //send notify for users following this event
        $followTable = Engine_Api::_()->getDbtable('follow', 'ynevent');
        $follows = $followTable->getUserFollow($event->event_id);
        if (count($follows) > 0) {

            $friends = array();
            foreach ($follows as $follow) {
                if ($follow->user_id != $viewer->user_id) {
                    $friends[] = Engine_Api::_()->getItem('user', $follow->user_id);
                }
            }
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            foreach ($friends as $friend) {
            	if($type=='ynevent_delete'){
            		$notifyApi->addNotification($friend, $viewer, $friend, $type, array("ynevent_title"=>$event->getTitle()));
					
            	}					
				else if($type=='ynevent_edit_delete'){
					$notifyApi->addNotification($friend, $viewer, $event_next, $type, array("ynevent_title"=>$event->getTitle()));
					
				}					
				else	{
					$notifyApi->addNotification($friend, $viewer, $event, $type );//$type = 'ynevent_change_details'
					
				}
                	
            }
			
        }		
	}
	
	private function setAuth(Ynevent_Model_Event $event,$values){
		// Process privacy
        $auth = Engine_Api::_()->authorization()->context;

        if ($event->parent_type == 'group') {
            $roles = array('owner', 'member', 'parent_member', 'registered', 'everyone');
        } else {
            $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        }

        $viewMax = array_search($values['auth_view'], $roles);
        $commentMax = array_search($values['auth_comment'], $roles);
        $photoMax = array_search($values['auth_photo'], $roles);

        foreach ($roles as $i => $role) {
            $auth->setAllowed($event, $role, 'view', ($i <= $viewMax));
            $auth->setAllowed($event, $role, 'comment', ($i <= $commentMax));
            $auth->setAllowed($event, $role, 'photo', ($i <= $photoMax));
        }
        
        $rolesVideo = array('owner', 'member', 'parent_member', 'registered', 'everyone');
        $videoMax = array_search($values['auth_video'], $rolesVideo);
        foreach ($rolesVideo as $i => $r) {
        	$auth->setAllowed($event, $r, 'video', ($i <= $videoMax));
        }

        $auth->setAllowed($event, 'member', 'invite', $values['auth_invite']);
		
		
		
	}
	
    public function editAction() {
		
		// Get navigation
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('ynevent_main');
		
        $event_id = $this->getRequest()->getParam('event_id');
        $event = Engine_Api::_()->getItem('event', $event_id);
		/*****************************/
		
		//Keep info to check changing
		if(is_object($event))		
			$event_temp = clone $event;		
		
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!($this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() || $event->isOwner($viewer))) {
            return;
        }

        // Create form
        $event = Engine_Api::_()->core()->getSubject();
        $this->view->gEndDate = Engine_Api::_() -> getApi('settings', 'core')->getSetting("ynevent.day","");
        $this->view->form = $form = new Ynevent_Form_Edit(array('parent_type' => $event->parent_type, 'parent_id' => $event->parent_id));
		
		$this->view->formcheck = $formcheck = new Ynevent_Form_Check();     

        if (!$this->getRequest()->isPost()) {
            // Populate auth
            $auth = Engine_Api::_()->authorization()->context;

            if ($event->parent_type == 'group') {
                $roles = array('owner', 'member', 'parent_member', 'registered', 'everyone');
            } else {
                $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            }

            foreach ($roles as $role) {
                if (isset($form->auth_view->options[$role]) && $auth->isAllowed($event, $role, 'view')) {
                    $form->auth_view->setValue($role);
                    
                }
                if (isset($form->auth_comment->options[$role]) && $auth->isAllowed($event, $role, 'comment')) {
                    $form->auth_comment->setValue($role);
                }
                if (isset($form->auth_photo->options[$role]) && $auth->isAllowed($event, $role, 'photo')) {
                    $form->auth_photo->setValue($role);
                }
            }
            
            $rolesVideo = array('owner', 'member', 'parent_member', 'registered', 'everyone');
            foreach ($rolesVideo as $i => $r) {
            	if (isset($form->auth_video->options[$r]) && $auth->isAllowed($event, $r, 'video')) {
            		$form->auth_video->setValue($r);
            	
            	}
            	//$auth->setAllowed($event, $r, 'video', ($i <= $videoMax));
            }
            
            $form->auth_invite->setValue($auth->isAllowed($event, 'member', 'invite'));

            // Sub category
            $eventArray = $event->toArray();
//            if ($node->parent_id > 1) {
//                $eventArray['category_id'] = $node->parent_id;
//                $eventArray['sub_category_id'] = $event->category_id;
//            }


			$st_address = "";
			if ($eventArray['address'] != '')
				$st_address .= $eventArray['address'];
		
			if ($eventArray['city'] != '')
				$st_address .= "," . $eventArray['city'];
				
			if ($eventArray['country'] != '')
				$st_address .= "," . $eventArray['country'];

			if ($eventArray['zip_code'] != '')
				$st_address .= "," . $eventArray['zip_code'];

			$pos = strpos($st_address, ",");
			if ($pos === 0)
				$st_address = substr($st_address, 1);
			
			$eventArray['full_address'] = $st_address;
            $form->populate($eventArray);

            // Convert and re-populate times
            $start = strtotime($event->starttime);
            $end = strtotime($event->endtime);
			if($event->end_repeat!="")
            $end_repeat = strtotime($event->end_repeat);
            $oldTz = date_default_timezone_get();
            date_default_timezone_set($viewer->timezone);
            $start = date('Y-m-d H:i:s', $start);
            $end = date('Y-m-d H:i:s', $end);
			if($event->end_repeat!="")
            $end_repeat = date('Y-m-d 23:59:59', $end_repeat);
            date_default_timezone_set($oldTz);
            
			if($event->end_repeat!="")
				$form->populate(array(
	                'starttime'=> $start,
	                'endtime' => $end,
	                'repeatend' => $end_repeat,
	            ));
			else
			{
				$form->populate(array(
	                'starttime' => $start,
	                'endtime' => $end,	               
	            ));
			}				
			
			$form->populate(array(
					'f_repeat_type'=> $event->repeat_type,
					'g_repeat_type'=> $event->repeat_type,
			));
						
			if($event->repeat_type ==0){
				$rp_type = 0;	
				$req = 0;
			}			
			else{
				$rp_type = 1;
				switch($event->repeat_type){
					case 1: $req = 1;
						break;
					case 7 : $req = 7;
						break;
					case 30 : $req = 30;
						break;
					default : $req = 1;
						break;				
				}
			}
           
			$form->populate(array(
					'repeat_type'=> $rp_type,
					'repeat_frequency'=> $req,
			));
			
			//Keep info to check changing		
			//$event_temp = clone $event;				
			
		
			
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
						
        // Process

        $values = $form->getValues();
        if (!empty($values['sub_category_id']) && $values['sub_category_id'] > 0) {
            $values["category_id"] = $values['sub_category_id'];
        }
			
				
        // Check parent
        if (!isset($values['host']) && $event->parent_type == 'group' && Engine_Api::_()->hasItemType('group')) {
            $group = Engine_Api::_()->getItem('group', $event->parent_id);
            $values['host'] = $group->getTitle();
        }

        // Process
        $db = Engine_Api::_()->getItemTable('event')->getAdapter();
        $db->beginTransaction();
		
        try {
        	
        	$values['user_id'] = $viewer->getIdentity();
        	$values['parent_type'] = $event->parent_type;
			$values['parent_id'] = $event->parent_id;
			$values['end_repeat'] = ($values['repeatend']=="0000-00-00")?"":$values['repeatend'];

            // Set event info
            
            if($values['repeat_type'] == 1){
            	switch($values['repeat_frequency']){
					case 1: $values['repeat_type'] = 1;
						break;
					case 7 : $values['repeat_type'] = 7;
						break;
					case 30 : $values['repeat_type'] = 30;
						break;								
				}            	 	
            }			
			
			$copyvalues = $values;				
			
			// Convert times to server time
	        $oldTz = date_default_timezone_get();
	        date_default_timezone_set($viewer->timezone);
	        $start1 = strtotime($copyvalues['starttime']);
	        $end1 = strtotime($copyvalues['endtime']);
	        date_default_timezone_set($oldTz);
	        $copyvalues['starttime'] = date('Y-m-d H:i:s', $start1);
	        $copyvalues['endtime'] = date('Y-m-d H:i:s', $end1);
					
           
            $event->setFromArray($copyvalues);
			
            // 2: Apply for following repeating events
			// 1: Apply for all repeating events
			// 0: Apply for only current event		
			// not care with case following events , just only or all		
			// if change basic info (for only and all)
			// if change start_date, end_date (for only and all events)
			// if change repeat information (not for only and only for all)								
			//0,1,2 cho nay se show cai popup len va chon gia tri	
			$apply_for = ($values['apply_for_action']=="") ? 0 : $values['apply_for_action'];
			 
			//Have change all
			$is_change = false;
			// Change relate repeat
			$is_repeat_change = false; 
            
             //Start time of event
	        $first_date = $values['starttime']; 
			//End time of event
			$first_end_date = $values['endtime'];
		    
            //Maxinstance
            $maxInstance = Engine_Api::_() -> getApi('settings', 'core')-> getSetting('ynevent.instance',50);
            if($maxInstance == '')
				$maxInstance = 50; 
            list($year, $month, $day) = explode('-', date("Y-m-d",strtotime($first_date)));
			
			//Redirect Event
			$redirectEvent = NULL;
			
														
			if($event_temp != $event){
				$is_change = true;
				//check basic or repeating change	
				echo( "Repeat type(1:repeat, 0: no repeat) ".$values['repeat_type']." ||".	
					$event_temp->repeat_type ."!=". $event->repeat_type. "||".
					$event_temp->starttime ."!=".  $event->starttime." ||". 
					$event_temp->endtime ."!=".  $event->endtime." ||".
					date("Y-m-d 12:50:00", strtotime($event_temp->end_repeat)) ."!=". $event->end_repeat
				)."<br/>";
				if(	($values['repeat_type'] != 0 && (
					$event_temp->repeat_type !=  $event->repeat_type ||
					$event_temp->starttime !=  $event->starttime || 
					$event_temp->endtime !=  $event->endtime ||
					date("Y-m-d 12:50:00", strtotime($event_temp->end_repeat)) !=  $event->end_repeat))					
					
				){					
					$is_repeat_change = true;
				}						
			}			
			
									
			
			
			
			if($is_change){
				
				//Repeat change			
				if($is_repeat_change){
						
					//End repeat date
					$configDate = Engine_Api::_() -> getApi('settings', 'core')-> getSetting('ynevent.day','');
					if($values['starttime'] > $values['repeatend']){
						$form->addError("Start time of the event must be less than the end repeat time");
						return;
					}
					
					//$duration = Engine_Api::_()->ynevent()->dateDiff($values['starttime'],$values['endtime']);			
					$duration = Engine_Api::_()->ynevent()->dateDiffBySec($values['starttime'],$values['endtime']);
															
					
					$repeat_end =	strtotime($values['repeatend']);				
					$repeat_end =	date('Y-m-d H:i:s', $repeat_end);
					$step = $values['repeat_frequency'];
					
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
    				}//End loopstart
    				
    				
    				
    				$this->log('loopstart1', var_export($arrStart,true),Zend_Log::INFO);
					
					if($maxInstance <= count($arrStart)){
						$str = "You are allowed creating only {$maxInstance} in the repeat event chain.";
						$form->addError($str);
						return;
					}
					
					// Create event
					$table = Engine_Api::_()->getDbtable('events', 'ynevent');
					
					//Set repeat group
					$values['repeat_group'] = microtime(true)*10000;
					
					// 0: none repeat
					// !0: repeating
					$type = $values['repeat_type'];
										
					//Only current event	
					if($apply_for == 0){																		

						//Set repeat order		
						$repeat_order = 0;
																								
						if(is_array($arrStart)){
							foreach ($arrStart as $key => $value) {
								$repeat_order++;
								$values['repeat_order'] = $repeat_order;
								
								//create new row
								$new_event = $table->createRow();
								
								//repeat
								if($type != 0){								
									
									$values['starttime'] = $value;								
									//$values['endtime'] = $first_end_date;				
									//$first_end_date = Engine_Api::_()->ynevent()->dateAdd($first_end_date,$step);
									$values['endtime'] = Engine_Api::_()->ynevent()->dateAddBySec($value,$duration);//$duration
									
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
									
															
								}//End $type == 1
								
													
								$new_event->setFromArray($values);
								$new_event->save();
								
								if($redirectEvent == NULL)
									$redirectEvent = $new_event;
												
								// Add owner as member
								$new_event->membership()->addMember($viewer)
								->setUserApproved($viewer)
								->setResourceApproved($viewer);
					
								// Add owner rsvp
								$new_event->membership()
								->getMemberInfo($viewer)
								->setFromArray(array('rsvp' => 2))
								->save();
												
							
								// Add photo
								if (!empty($values['photo'])) {
									$new_event->setPhoto($form->photo);				
								}
								
								
								//Add owner follow
								Engine_Api::_()->ynevent()->setEventFollow($new_event, $viewer);
					
								// Process privacy
								$this->setAuth($new_event,$values);		
																
								
								//Add activity only one
								if($repeat_order <= 1){
									// Add action
									$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
						
									$action = $activityApi->addActivity($viewer, $new_event, 'ynevent_create');
						
									if ($action) {
										$activityApi->attachActivity($action, $new_event);
									}					
								}
					
							}//End foreach create events
							
						}//End is_array($arrStart)	
						
						// Add notify delete event when edit
						$this->setNotify($event,$viewer,'ynevent_edit_delete',$redirectEvent);
						
						//Delete Current Event
						$event->delete();
					}					
					
					//For all repeating events
					if($apply_for==1){
						//Set repeat order		
						$repeat_order = 0;						
						if(is_array($arrStart)){
							foreach ($arrStart as $key => $value) {
								$repeat_order++;
								$values['repeat_order'] = $repeat_order;
								
                                //check maxinstance
                                if($maxInstance >= $repeat_order){
								//create new row
								$new_event = $table->createRow();
																								
								//repeat
								if($type != 0){
									
									$values['starttime'] = $value;
									$values['endtime'] = Engine_Api::_()->ynevent()->dateAddBySec($value,$duration);//$duration								
									//$values['endtime'] = $first_end_date;
									//$first_end_date = Engine_Api::_()->ynevent()->dateAdd($first_end_date,$step);
									
									//$this->log("1111key$key", var_export($values,true),Zend_Log::INFO);
									
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
								}//End $type == 1
								
								//$this->log("$type-key-$key", var_export($values,true),Zend_Log::INFO);
															
								$new_event->setFromArray($values);
								$new_event->save();
								
								//Set redirect event
								if($redirectEvent == NULL)
									$redirectEvent = $new_event;
												
								// Add owner as member
								$new_event->membership()->addMember($viewer)
								->setUserApproved($viewer)
								->setResourceApproved($viewer);
					
								// Add owner rsvp
								$new_event->membership()
								->getMemberInfo($viewer)
								->setFromArray(array('rsvp' => 2))
								->save();
												
							
								// Add photo
								if (!empty($values['photo'])) {
									$new_event->setPhoto($form->photo);				
								}
								
								
								//Add owner follow
								Engine_Api::_()->ynevent()->setEventFollow($new_event, $viewer);
					
								// Process privacy
								$this->setAuth($new_event,$values);		
								
								// Add notify 
								//$this->setNotify($event,$viewer,'ynevent_change_details');
								
								//Add activity only one
								if($repeat_order <= 1){
									// Add action
									$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
						
									$action = $activityApi->addActivity($viewer, $new_event, 'ynevent_create');
						
									if ($action) {
										$activityApi->attachActivity($action, $new_event);
									}					
								}
                                
                                }//End Maxinstances
					
							}//End foreach create events
							
						}//End is_array($arrStart)	
						
						//Get all events in group repeat	
						$event_list = $table->getRepeatEvent($event->repeat_group);
						
						//Remove series events
						foreach ($event_list as $objevent) {														
														
							// Add notify 
							$this->setNotify($objevent,$viewer,'ynevent_edit_delete',$redirectEvent);
							
							$objevent->delete();
						}		
											
					}//End $apply_for==1
					
					//For following events
					if($apply_for==2){
							
						if(is_array($arrStart)){
							//Set repeat order		
						$repeat_order = 0;
							foreach ($arrStart as $key => $value) {
								
									$repeat_order++;
									$values['repeat_order'] = $repeat_order;
									 //check maxinstance
                                    if($maxInstance >= $repeat_order){
									//create new row
									$new_event = $table->createRow();
									
									//repeat
									if($type!=0){
										
										$values['starttime'] = $value;//date('Y-m-d H:i:s', $value);
										//$values['endtime'] = $first_end_date;
										//$first_end_date = Engine_Api::_()->ynevent()->dateAdd($first_end_date,$step);
										$values['endtime'] = Engine_Api::_()->ynevent()->dateAddBySec($value,$duration);//$duration
																				
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
															
									}//End $type == 1
																
									$new_event->setFromArray($values);
									$new_event->save();
									
									//Set redirect event
									if($redirectEvent == NULL)
										$redirectEvent = $new_event;
														
									// Add owner as member
									$new_event->membership()->addMember($viewer)
									->setUserApproved($viewer)
									->setResourceApproved($viewer);
						
									// Add owner rsvp
									$new_event->membership()
									->getMemberInfo($viewer)
									->setFromArray(array('rsvp' => 2))
									->save();
													
								
									// Add photo
									if (!empty($values['photo'])) {
										$new_event->setPhoto($form->photo);				
									}
									
									//Add owner follow
									Engine_Api::_()->ynevent()->setEventFollow($new_event, $viewer);
																		
									// Process privacy
									$this->setAuth($new_event,$values);		
									
									// Add notify 
									//$this->setNotify($event,$viewer,'ynevent_change_details');	
									
									//Add activity only one
									if($repeat_order <= 1){
										// Add action
										$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
							
										$action = $activityApi->addActivity($viewer, $new_event, 'ynevent_create');
							
										if ($action) {
											$activityApi->attachActivity($action, $new_event);
										}					
									}															
								}//End checkmaxinstance
                                
							}//End foreach create events
							
						}//End is_array($arrStart)	
						
						//Get all events in group repeat	
						$event_list = $table->getRepeatEvent($event->repeat_group);
						
						//Remove series events
						foreach ($event_list as $objevent) {
							if($objevent->repeat_order >= $event->repeat_order){														
															
								// Add notify 
								$this->setNotify($objevent,$viewer,'ynevent_edit_delete',$redirectEvent);
								
								$objevent->delete();
							}
						}	
						
					}//End Apply_for = 2
				}
				//Basic info change
				else{										
					
					//Only current event
					if($apply_for==0){
						// change basic information for currents event

						if($values['repeat_type'] == 0)//&& $event->repeat_type != 0
                        	$event->repeat_group = microtime(true)*10000;
											
						$event->save();
						
						//Set redirect event
						if($redirectEvent == NULL)
							$redirectEvent = $event;
														
						// Edit photo
						if (!empty($values['photo'])) {
              				 $event->setPhoto($form->photo);
            			}

								
						// Process privacy
						$this->setAuth($event,$values);		
						
						// Add notify 
						$this->setNotify($event,$viewer,'ynevent_change_details');
																											
					}
										
					//For all repeating events
					if($apply_for==1){
						//Get DBTable Events
						$tblEvents  = Engine_Api::_() -> getDbTable('events','ynevent');
						//Get all events in group repeat	
						$event_list = $tblEvents->getRepeatEvent($event->repeat_group);
						
						$i = 0;
						foreach ($event_list as $objevent) {
													
							if($values['repeat_type'] == 0 && $objevent->repeat_type != 0){
								$i++;
																
								//Create only new new one
								if($i == 1){
									$event = $tblEvents->createRow();
									$values['repeat_group'] = microtime(true)*10000;										
									$event->setFromArray($values);
									$event->save();
									
									//Set redirect event
									if($redirectEvent == NULL)
										$redirectEvent = $event;
														
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
																		
									// Process privacy
									$this->setAuth($event,$values);		
									
									// Add notify 
									//$this->setNotify($event,$viewer,'ynevent_change_details');	
									
									//Add owner follow
									Engine_Api::_()->ynevent()->setEventFollow($event, $viewer);
									
									// Add action
									$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
							
									$action = $activityApi->addActivity($viewer, $event, 'ynevent_create');
							
									if ($action) {
										$activityApi->attachActivity($action, $event);
									}					
										
								}//If $i==1						
								
								// Add notify 
								$this->setNotify($objevent,$viewer,'ynevent_edit_delete',$redirectEvent);								
								$objevent->delete();								
							}
							else{
								//unset($values['repeat_type']);
	                            unset($values['starttime']);
	                            unset($values['endtime']);
	                            unset($values['end_repeat']);
	                            //$values['repeat_group'] = microtime(true)*10000;
															                        
								$objevent->setFromArray($values);
								$objevent->save();
								
								// Edit photo
								if (!empty($values['photo'])) {
		              				 $objevent->setPhoto($form->photo);
		            			}
							
								// Process privacy
								$this->setAuth($objevent,$values);	
								
								// Add notify 
								$this->setNotify($objevent,$viewer,'ynevent_change_details');	
							}
						    
						}
					}
					//For following events
					if($apply_for==2){		
						
						//Get DBTable Events
						$tblEvents  = Engine_Api::_() -> getDbTable('events','ynevent');
						//Get all events in group repeat	
						$event_list = $tblEvents->getRepeatEvent($event->repeat_group);		
						$i = 0;				
						foreach ($event_list as $objevent) {							
							if($objevent->repeat_order >= $event->repeat_order){
								if($values['repeat_type'] == 0 && $objevent->repeat_type != 0)
								{
								
								$i++;
																
								//Create only new new one
								if($i == 1){
									$event = $tblEvents->createRow();
									
									/*																				
									$oldTz = date_default_timezone_get();
									date_default_timezone_set($viewer->timezone);
									$start = strtotime($values['starttime']);
									$end = strtotime($values['endtime']);
									date_default_timezone_set($oldTz);
									$values['starttime'] = date('Y-m-d H:i:s', $start);
									$values['endtime'] = date('Y-m-d H:i:s', $end);
									*/		
									$values['repeat_group'] = microtime(true)*10000;	
									$event->setFromArray($values);
									$event->save();
									
									//Set redirect event
									if($redirectEvent == NULL)
										$redirectEvent = $event;
														
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
																		
									// Process privacy
									$this->setAuth($event,$values);		
									
									// Add notify 
									//$this->setNotify($event,$viewer,'ynevent_change_details');	
									
									
									// Add action
									$activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
							
									$action = $activityApi->addActivity($viewer, $event, 'ynevent_create');
							
									if ($action) {
										$activityApi->attachActivity($action, $event);
									}					
										
								}//If $i==1
								
								// Add notify 
								$this->setNotify($objevent,$viewer,'ynevent_edit_delete',$redirectEvent);								
								$objevent->delete();
							}
							else{
			                        //unset($values['repeat_type']);
	                                unset($values['starttime']);
	                                unset($values['endtime']);
	                                unset($values['end_repeat']);
									//$values['repeat_group'] = microtime(true)*10000;
									$objevent->setFromArray($values);
									$objevent->save();
									
									// Edit photo
									if (!empty($values['photo'])) {
			              				 $objevent->setPhoto($form->photo);
			            			}
									
									// Process privacy
									$this->setAuth($objevent,$values);	
									
									// Add notify 
									$this->setNotify($objevent,$viewer,'ynevent_change_details');
								}		
							}										
						}//End Event list	
						
					}//End apply_for == 2
										
				}//Basic info change
				
			}//End is_change
		
            // Commit
            $db->commit();
	
        } catch (Engine_Image_Exception $e) {
            $db->rollBack();
            $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // Redirect
        if ($this->_getParam('ref') === 'profile' && $redirectEvent != NULL ) {
            //$this->_redirectCustom($event);
            $this->_redirectCustom($redirectEvent);								
            
        } else {
            $this->_redirectCustom(array('route' => 'event_general', 'action' => 'manage'));
        }
    }
	
	
    public function inviteAction() {

        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireSubject('event')->isValid())
            return;
        // @todo auth
        // Prepare data
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->event = $event = Engine_Api::_()->core()->getSubject();
        $this->view->friends = $friends = $viewer->membership()->getMembers();

        // Prepare form
        $this->view->form = $form = new Ynevent_Form_Invite();

        $count = 0;
        foreach ($friends as $friend) {
            if ($event->membership()->isMember($friend, null))
                continue;
            $form->users->addMultiOption($friend->getIdentity(), $friend->getTitle());
            $count++;
        }
        $this->view->count = $count;
        // Not posting
        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        // Process
        $table = $event->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $usersIds = $form->getValue('users');
			if ($form->getElement('message')) {
				$message = $form->getElement('message')->getValue();
			}
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            foreach ($friends as $friend) {
                if (!in_array($friend->getIdentity(), $usersIds)) {
                    continue;
                }

                $event->membership()->addMember($friend)
                        ->setResourceApproved($friend);

                if (isset($message) && !empty($message) ) {
                	$notifyApi->addNotification($friend, $viewer, $event, 'ynevent_invite_message', array('message' => $message));
                } else {
                	$notifyApi->addNotification($friend, $viewer, $event, 'ynevent_invite');
                }
            }


            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }


        return $this->_forward('success', 'utility', 'core', array(
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Members invited')),
                    'layout' => 'default-simple',
                    'parentRefresh' => true,
                ));
    }

    public function styleAction() {
        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid())
            return;
        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'style')->isValid())
            return;

        $user = Engine_Api::_()->user()->getViewer();
        $event = Engine_Api::_()->core()->getSubject('event');

        // Make form
        $this->view->form = $form = new Ynevent_Form_Style();

        // Get current row
        $table = Engine_Api::_()->getDbtable('styles', 'core');
        $select = $table->select()
                ->where('type = ?', 'event')
                ->where('id = ?', $event->getIdentity())
                ->limit(1);

        $row = $table->fetchRow($select);

        // Check post
        if (!$this->getRequest()->isPost()) {
            $form->populate(array(
                'style' => ( null === $row ? '' : $row->style )
            ));
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Cool! Process
        $style = $form->getValue('style');

        // Save
        if (null == $row) {
            $row = $table->createRow();
            $row->type = 'event';
            $row->id = $event->getIdentity();
        }

        $row->style = $style;
        $row->save();

        $this->view->draft = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.');
        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh' => false,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'))
        ));
    }

    public function deleteAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $event = Engine_Api::_()->getItem('event', $this->getRequest()->getParam('event_id'));
        
        if (!$this->_helper->requireAuth()->setAuthParams($event, null, 'delete')->isValid())
            return;

        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');

        // Make form
        $this->view->form = $form = new Ynevent_Form_Delete();

        if (!$event) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Event doesn't exists or not authorized to delete");
            return;
        }
		$tblEvents  = Engine_Api::_() -> getDbTable('events','ynevent');
		//Get all events in group repeat	
		$event_list = $tblEvents->getRepeatEvent($event->repeat_group);
		if($event->repeat_type == 0 || count($event_list) < 2 ){
			$form->setDescription('Are you sure you want to delete this event?');
			$form->removeElement('apply_for');
		}

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }
		
		$values = $this->getRequest()->getPost();
		
        $db = $event->getTable()->getAdapter();
        $db->beginTransaction();

        try {
        	
			$apply_for = $values['apply_for'];
			
			//Only current event	
			if($apply_for == 0){								
								
				// Add notify 
				$this->setNotify($event,$viewer,'ynevent_delete');
				
				//Delete Current Event
				$event->delete();
			}
			
			//For all repeating events
			if($apply_for==1){
				//Get DbTable Events
				$tblEvents  = Engine_Api::_() -> getDbTable('events','ynevent');
				//Get all events in group repeat	
				$event_list = $tblEvents->getRepeatEvent($event->repeat_group);
				
				//Remove series events
				foreach ($event_list as $objevent) {														
					
					// Add notify 
					$this->setNotify($objevent,$viewer,'ynevent_delete');
					
					$objevent->delete();
				}		
			}
			
			//For following events
			if($apply_for==2){
					//Get DbTable Events
				$tblEvents  = Engine_Api::_() -> getDbTable('events','ynevent');
				//Get all events in group repeat	
				$event_list = $tblEvents->getRepeatEvent($event->repeat_group);
				
				//Remove series events
				foreach ($event_list as $objevent) {
					if($objevent->repeat_order >= $event->repeat_order){			
						
						// Add notify 
						$this->setNotify($objevent,$viewer,'ynevent_delete');
						
						$objevent->delete();
					}
				}
			}
			
			
			
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('The selected event has been deleted.');
        return $this->_forward('success', 'utility', 'core', array(
                    'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'event_general', true),
                    'messages' => Array($this->view->message)
                ));
    }

    public function promoteAction() {
    	$viewer = Engine_Api::_()->user()->getViewer();
        $event = Engine_Api::_()->getItem('event', $this->getRequest()->getParam('event_id'));
    	if(!$event) {
			return $this->_helper->requireAuth->forward ();
		}
        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');
        // Make form
        //$this->view->form = $form = new Ynevent_Form_Promote();
		$this->view->event = $event;
    }
    
}