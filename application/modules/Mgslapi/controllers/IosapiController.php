<?php

class Mgslapi_IosapiController extends Mgslapi_Controller_Action_Api {

  private $_currentModuleCoreApi;
  private $_viewer;
  protected $_deviceType = Mgslapi_Model_DbTable_Devices::IOS_DEVICE_TYPE;

  public function init() {
    parent::init();
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender();
    $this->_currentModuleCoreApi = Engine_Api::_()->getApi('core', 'mgslapi');
  }

//    public function loginAction() 
//    { 
//        if($user = $this->checkAuth())
//        {         
//            $response = array();
//            $response['response']['success'] = 'success';
//            $response['response']['data']['user_id'] = $user->user_id;
//            $response['response']['data']['user_name'] = $user->displayname;                     
//            $response['response']['data']['user_image'] = $this->mybase64_encode(Engine_Api::_()->mgslapi()->getItemPhotoUrl($user));                    
//            $response['response']['hash'] = md5( serialize( $response ));
//            $this->JSONSuccessOutput($response);
//        }
//    }

  public function allfeed($viewer, $subject = null, $newest_feed_id = null) {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
    // Get some options
    $this->view->feedOnly = $feedOnly = $request->getParam('feedOnly', false);
    $this->view->length = $length = $request->getParam('limit', Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.length', 15));
    $this->view->itemActionLimit = $itemActionLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.userlength', 5);

    $this->view->updateSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.liveupdate');
    $this->view->viewAllLikes = $request->getParam('viewAllLikes', $request->getParam('show_likes', false));
    $this->view->viewAllComments = $request->getParam('viewAllComments', $request->getParam('show_comments', false));
    $this->view->getUpdate = $request->getParam('getUpdate');
    $this->view->checkUpdate = $request->getParam('checkUpdate');
    $this->view->action_id = (int) $request->getParam('action_id');
    $this->view->post_failed = (int) $request->getParam('pf');

    if ($length > 50) {
      $this->view->length = $length = 50;
    }
    $minid = (int) $request->getParam('minid');
    if ($newest_feed_id) {
      $minid = (int) $newest_feed_id + 1;
    }
    // Get config options for activity
    $config = array(
        'action_id' => (int) $request->getParam('action_id'),
        'max_id' => (int) $request->getParam('maxid'),
        'min_id' => (int) $minid,
        'limit' => (int) $length,
            //'showTypes' => $actionTypeFilters,
    );

    // Pre-process feed items
    $selectCount = 0;
    $nextid = null;
    $firstid = null;
    $tmpConfig = $config;
    $activity = array();
    $endOfFeed = false;

    $friendRequests = array();
    $itemActionCounts = array();
    $enabledModules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();

    do {
      // Get current batch
      $actions = null;

      // Where the Activity Feed is Fetched
      if (!empty($subject)) {
        $actions = $actionTable->getActivityAbout($subject, $viewer, $tmpConfig);
      } else {
        $actions = $actionTable->getActivity($viewer, $tmpConfig);
      }
      $selectCount++;

      // Are we at the end?
      if (count($actions) < $length || count($actions) <= 0) {
        $endOfFeed = true;
      }

      // Pre-process
      if (count($actions) > 0) {
        foreach ($actions as $action) {
          // get next id
          if (null === $nextid || $action->action_id <= $nextid) {
            $nextid = $action->action_id - 1;
          }
          // get first id
          if (null === $firstid || $action->action_id > $firstid) {
            $firstid = $action->action_id;
          }
          // skip disabled actions
          if (!$action->getTypeInfo() || !$action->getTypeInfo()->enabled)
            continue;
          // skip items with missing items
          if (!$action->getSubject() || !$action->getSubject()->getIdentity())
            continue;
          if (!$action->getObject() || !$action->getObject()->getIdentity())
            continue;
          // track/remove users who do too much (but only in the main feed)
          if (empty($subject)) {
            $actionSubject = $action->getSubject();
            $actionObject = $action->getObject();
            if (!isset($itemActionCounts[$actionSubject->getGuid()])) {
              $itemActionCounts[$actionSubject->getGuid()] = 1;
            } else if ($itemActionCounts[$actionSubject->getGuid()] >= $itemActionLimit) {
              continue;
            } else {
              $itemActionCounts[$actionSubject->getGuid()] ++;
            }
          }
          // remove duplicate friend requests
          if ($action->type == 'friends') {
            $id = $action->subject_id . '_' . $action->object_id;
            $rev_id = $action->object_id . '_' . $action->subject_id;
            if (in_array($id, $friendRequests) || in_array($rev_id, $friendRequests)) {
              continue;
            } else {
              $friendRequests[] = $id;
              $friendRequests[] = $rev_id;
            }
          }

          // remove items with disabled module attachments
          try {
            $attachments = $action->getAttachments();
          } catch (Exception $e) {
            // if a module is disabled, getAttachments() will throw an Engine_Api_Exception; catch and continue
            continue;
          }

          // add to list
          if (count($activity) < $length) {
            $activity[] = $action;
            if (count($activity) == $length) {
              $actions = array();
            }
          }
        }
      }

      // Set next tmp max_id
      if ($nextid) {
        $tmpConfig['max_id'] = $nextid;
      }
      if (!empty($tmpConfig['action_id'])) {
        $actions = array();
      }
    } while (count($activity) < $length && $selectCount <= 3 && !$endOfFeed);
    $data = array(
        'activity' => $activity,
        'activityCount' => count($activity),
        'nextid' => $nextid,
        'firstid' => $firstid,
        'endOfFeed' => $endOfFeed,
        'viewer' => $viewer,
        'subject' => $subject,
    );
    return $data;
  }

  public function feedAction() {
    if ($viewer = $this->checkAuth()) {
      $request = Zend_Controller_Front::getInstance()->getRequest();
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      extract($this->getRequest()->getPost());
      $subject = null;
      if ($user_id) {
        $subject = Engine_Api::_()->getItem('user', (int) $user_id);
        if (!$subject->user_id)
          $this->_jsonErrorOutput('User not found');
      }

      $data = $this->allfeed($viewer, $subject);
      if ($subject) {
        $html = $this->view->partial(
                '_view_profile_feed.tpl', $data
        );
      } else {
        $html = $this->view->partial(
                '_view_feed.tpl', $this->_currentModuleCoreApi->getModuleName(), $data
        );
      }

      $response = array();

      if (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== FALSE) {
        echo $html;
        exit;
      }

      $response['response']['body'] = $html;
      $response['response']['hash'] = md5(serialize($response));
      $this->_jsonSuccessOutput($response);
    }
  }

  public function getcalenderfeedAction() {
    if ($viewer = $this->checkAuth()) {
      $request = Zend_Controller_Front::getInstance()->getRequest();
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      extract($this->getRequest()->getPost());
      $subject = null;
      if (empty($event_id)) {
        $this->_jsonErrorOutput('Event id required');
      }
      if ($event_id) {
        $subject = Engine_Api::_()->getItem('event', (int) $event_id);
        if (!$subject->event_id)
          $this->_jsonErrorOutput('Event not found');
      }

      $data = $this->allfeed($viewer, $subject);
      $html = $this->view->partial(
              '_view_event_feed.tpl', $this->_currentModuleCoreApi->getModuleName(), $data
      );

      $response = array();

      if (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== FALSE) {
        echo $html;
        exit;
      }
      //echo $html ; exit;
      $response['response']['body'] = $html;
      $response['response']['hash'] = md5(serialize($response));
      $this->_jsonSuccessOutput($response);
    }
  }

  public function getlatestfeedAction() {
    if ($viewer = $this->checkAuth()) {
      $request = Zend_Controller_Front::getInstance()->getRequest();
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      extract($this->getRequest()->getPost());
      if (empty($newest_feed_id)) {
        $this->_jsonErrorOutput('Feed id required');
      }
      $subject = null;
      if ($circle_id = $request->getParam('circle_id', NULL)) {
        $subject = Engine_Api::_()->getItem('sitepage_page', (int) $circle_id);
        if (!$subject) {
          $this->_jsonErrorOutput('Cricle not found');
        }
      } elseif ($event_id = $request->getParam('event_id', NULL)) {
        $subject = Engine_Api::_()->getItem('event', (int) $event_id);
        if (!$subject->event_id) {
          $this->_jsonErrorOutput('Event not found');
        }
      } elseif ($user_id) {
        $subject = Engine_Api::_()->getItem('user', (int) $user_id);
        if (!$subject->user_id)
          $this->_jsonErrorOutput('User not found');
      }
      $data = $this->allfeed($viewer, $subject, $newest_feed_id);
      if (!$data['activityCount']) {
        $response['response']['body'] = '';
        $response['response']['hash'] = md5(serialize($response));
        $this->_jsonSuccessOutput($response);
      } else {
        $html = $this->view->partial(
                '_view_latest_feed.tpl', $this->_currentModuleCoreApi->getModuleName(), $data
        );

        $response = array();
        //echo $html ; exit;
        $response['response']['body'] = base64_encode($html);
        $response['response']['hash'] = md5(serialize($response));
        $this->_jsonSuccessOutput($response);
      }
    }
  }

  public function previousfeedAction() {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    //echo 'max_id:'.$request->getParam('maxid').' viwer_id: '.$request->getParam('viewer_id').' user_id: '.$request->getParam('user_id'); exit;
    $viewer = Engine_Api::_()->getItem('user', $request->getParam('viewer_id'));

    extract($this->getRequest()->getPost());
    $subject = null;
    if ($circle_id = $request->getParam('circle_id', NULL)) {
      $subject = Engine_Api::_()->getItem('sitepage_page', (int) $circle_id);
      if (!$subject) {
        echo 'Cricle not found';
        return;
      }
    } elseif ($event_id = $request->getParam('event_id', NULL)) {
      $subject = Engine_Api::_()->getItem('event', (int) $event_id);
      if (!$subject->event_id) {
        echo 'Event not found';
        return;
      }
    } elseif ($user_id = $request->getParam('user_id', NULL)) {
      $subject = Engine_Api::_()->getItem('user', (int) $user_id);
      if (!$subject->user_id) {
        echo 'User not found';
        return;
      }
    }

    $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
    $maxId = ($request->getParam('maxid') - 1);
    if (!$maxId)
      return;
    // Get some options
    $this->view->feedOnly = $feedOnly = $request->getParam('feedOnly', false);
    $this->view->length = $length = $request->getParam('limit', Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.length', 15));
    $this->view->itemActionLimit = $itemActionLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.userlength', 5);

    $this->view->updateSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.liveupdate');
    $this->view->viewAllLikes = $request->getParam('viewAllLikes', $request->getParam('show_likes', false));
    $this->view->viewAllComments = $request->getParam('viewAllComments', $request->getParam('show_comments', false));
    $this->view->getUpdate = $request->getParam('getUpdate');
    $this->view->checkUpdate = $request->getParam('checkUpdate');
    $this->view->action_id = (int) $request->getParam('action_id');
    $this->view->post_failed = (int) $request->getParam('pf');

    if ($length > 50) {
      $this->view->length = $length = 50;
    }

    // Get config options for activity
    $config = array(
        'action_id' => (int) $request->getParam('action_id'),
        'max_id' => ((int) $request->getParam('maxid') - 1),
        'min_id' => (int) $request->getParam('minid'),
        'limit' => (int) $length,
            //'showTypes' => $actionTypeFilters,
    );

    // Pre-process feed items
    $selectCount = 0;
    $nextid = null;
    $firstid = null;
    $tmpConfig = $config;
    $activity = array();
    $endOfFeed = false;

    $friendRequests = array();
    $itemActionCounts = array();
    $enabledModules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
    //echo $request->getParam('maxid'); exit;
    do {
      // Get current batch
      $actions = null;

      // Where the Activity Feed is Fetched
      if (!empty($subject)) {
        $actions = $actionTable->getActivityAbout($subject, $viewer, $tmpConfig);
      } else {
        $actions = $actionTable->getActivity($viewer, $tmpConfig);
      }
      $selectCount++;

      // Are we at the end?
      if (count($actions) < $length || count($actions) <= 0) {
        $endOfFeed = true;
      }

      // Pre-process
      if (count($actions) > 0) {
        foreach ($actions as $action) {
          // get next id
          if (null === $nextid || $action->action_id <= $nextid) {
            $nextid = $action->action_id - 1;
          }
          // get first id
          if (null === $firstid || $action->action_id > $firstid) {
            $firstid = $action->action_id;
          }
          // skip disabled actions
          if (!$action->getTypeInfo() || !$action->getTypeInfo()->enabled)
            continue;
          // skip items with missing items
          if (!$action->getSubject() || !$action->getSubject()->getIdentity())
            continue;
          if (!$action->getObject() || !$action->getObject()->getIdentity())
            continue;
          // track/remove users who do too much (but only in the main feed)
          if (empty($subject)) {
            $actionSubject = $action->getSubject();
            $actionObject = $action->getObject();
            if (!isset($itemActionCounts[$actionSubject->getGuid()])) {
              $itemActionCounts[$actionSubject->getGuid()] = 1;
            } else if ($itemActionCounts[$actionSubject->getGuid()] >= $itemActionLimit) {
              continue;
            } else {
              $itemActionCounts[$actionSubject->getGuid()] ++;
            }
          }
          // remove duplicate friend requests
          if ($action->type == 'friends') {
            $id = $action->subject_id . '_' . $action->object_id;
            $rev_id = $action->object_id . '_' . $action->subject_id;
            if (in_array($id, $friendRequests) || in_array($rev_id, $friendRequests)) {
              continue;
            } else {
              $friendRequests[] = $id;
              $friendRequests[] = $rev_id;
            }
          }

          // remove items with disabled module attachments
          try {
            $attachments = $action->getAttachments();
          } catch (Exception $e) {
            // if a module is disabled, getAttachments() will throw an Engine_Api_Exception; catch and continue
            continue;
          }

          // add to list
          if (count($activity) < $length) {
            $activity[] = $action;
            if (count($activity) == $length) {
              $actions = array();
            }
          }
        }
      }

      // Set next tmp max_id
      if ($nextid) {
        $tmpConfig['max_id'] = $nextid;
      }
      if (!empty($tmpConfig['action_id'])) {
        $actions = array();
      }
    } while (count($activity) < $length && $selectCount <= 3 && !$endOfFeed);
    $html = $this->view->partial(
            '_dumy_feed.tpl', $this->_currentModuleCoreApi->getModuleName(), array(
        'activity' => $activity,
        'activityCount' => count($activity),
        'nextid' => $nextid,
        'firstid' => $firstid,
        'endOfFeed' => $endOfFeed,
        'max_id' => (int) $request->getParam('maxid'),
        'viewer' => $viewer,
            )
    );
    header('Content-Type: text/plain');
    echo $html;
  }

  public function getmessagelistAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      $conversation_table = Engine_Api::_()->getItemTable('messages_conversation');
      $select = $conversation_table->getInboxSelect($viewer);
      if ($page == '-1') {
        $conversations = $conversation_table->fetchAll($select);
        $total = count($conversations);
      } else {
        $conversations = Zend_Paginator::factory($select);
        $conversations->setItemCountPerPage(10);
        $conversations->setCurrentPageNumber($page ? (int) $page : 1);
        $total = $conversations->getTotalItemCount();
      }
      $i = 0;
      $response = array();
      if ($total > 0) {
        $response['response']['success'] = 'success';
        foreach ($conversations as $conversation) {
          $message = $conversation->getInboxMessage($viewer);
          $recipient = $conversation->getRecipientInfo($viewer);
          $resource = "";
          $sender = "";
          if ($conversation->hasResource() &&
                  ($resource = $conversation->getResource())) {
            $sender = $resource;
          } else if ($conversation->recipients > 1) {
            $sender = $viewer;
          } else {
            foreach ($conversation->getRecipients() as $tmpUser) {
              if ($tmpUser->getIdentity() != $viewer->getIdentity()) {
                $sender = $tmpUser;
              }
            }
          }
          if ((!isset($sender) || !$sender) && $viewer->getIdentity() !== $conversation->user_id) {
            $sender = Engine_Api::_()->user()->getUser($conversation->user_id);
          }
          if (!isset($sender) || !$sender) {
            //continue;
            $sender = new User_Model_User(array());
          }

          // code for title
          !( isset($message) && '' != ($title = trim($message->getTitle())) || !isset($conversation) && '' != ($title = trim($conversation->getTitle())) || $title = '<em>' . $this->view->translate('(No Subject)') . '</em>' );

          //code for sender
          if (!empty($resource)):
            $sender_name = $resource->toString();
          elseif ($conversation->recipients == 1):
            $sender_name = $sender->getTitle();
          else:
            $sender_name = $this->view->translate(array('%s person', '%s people', $conversation->recipients), $this->view->locale()->toNumber($conversation->recipients));
          endif;
          //end code for sender

          $imageURL = $this->mybase64_encode(Engine_Api::_()->mgslapi()->getItemPhotoUrl($sender));
          $response['response']['data'][$i]['id'] = $message->conversation_id;
          $response['response']['data'][$i]['sender'] = $sender_name;
          $response['response']['data'][$i]['sender_image'] = $imageURL;
          $response['response']['data'][$i]['title'] = strip_tags($title);
          $response['response']['data'][$i]['message'] = html_entity_decode($message->body);
          $response['response']['data'][$i]['date'] = $this->view->customtimestamp($message->date);
          $response['response']['data'][$i]['message_unread'] = (!$recipient->inbox_read ) ? 'yes' : 'no';
          $i++;
        }
      }
      else {
        $response['response']['data'] = 'No message found';
      }
      $response['response']['total'] = $total;
      $response['response']['hash'] = md5(serialize($response));
      //echo '<pre>';            print_r($response); exit;
      $this->_jsonSuccessOutput($response);
    }
  }

  public function getpreviousconversactionsAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      if (empty($message_id)) {
        $this->_jsonErrorOutput('Message id required');
      }

      $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
      if (!$conversation->conversation_id) {
        $this->_jsonErrorOutput('No message found');
      }
      $params = array();
      if ($last_conversation_id)
        $params = array('previous_conversations_id' => $last_conversation_id);
      $messages = Engine_Api::_()->mgslapi()->getMessagesPaginator($viewer, $conversation, $params);
      $messages->setItemCountPerPage(10);
      $total = $messages->getTotalItemCount();
      $response = $this->getConversation($messages);
      $conversation->setAsRead($viewer);

      $response['response']['total'] = $total;
      $response['response']['hash'] = md5(serialize($response));
      //echo '<pre>';            print_r($response); exit;
      $this->_jsonSuccessOutput($response);
    }
  }

  public function getlatestconversactionsAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      if (empty($message_id)) {
        $this->_jsonErrorOutput('Message id required');
      }
      $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
      if (!$conversation->conversation_id) {
        $this->_jsonErrorOutput('No message found');
      }
      $params = array();
      if ($last_conversation_id)
        $params = array('latest_conversations_id' => $last_conversation_id);
      $messages = Engine_Api::_()->mgslapi()->getMessagesPaginator($viewer, $conversation, $params);
      $messages->setItemCountPerPage(10);

      $response = $this->getConversation($messages);
      $total = $messages->getTotalItemCount();
      $conversation->setAsRead($viewer);
      $response['response']['total'] = $total;
      $response['response']['hash'] = md5(serialize($response));
      //echo '<pre>';            print_r($response); exit;
      $this->_jsonSuccessOutput($response);
    }
  }

  public function replymessageAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      if (empty($message_id)) {
        $this->_jsonErrorOutput('Conversation id required');
      }
      if (empty($last_conversation_id)) {
        $this->_jsonErrorOutput('Last conversation id required');
      }
      $body = trim($body);
      if (empty($body)) {
        $this->_jsonErrorOutput('Message body required');
      }
      $conversation = Engine_Api::_()->getItem('messages_conversation', (int) $message_id);
      if (!$conversation)
        $this->_jsonErrorOutput('Message not found');
      $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
      $db->beginTransaction();
      try {
        $attachment = null;
        $conversation->reply(
                $viewer, trim(strip_tags($body)), $attachment
        );
        $recipients = $conversation->getRecipients();
        // Send notifications
        foreach ($recipients as $user) {
          if ($user->getIdentity() == $viewer->getIdentity()) {
            continue;
          }
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
                  $user, $viewer, $conversation, 'message_new'
          );
        }

        // Increment messages counter
        Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
        $db->commit();
        $params = array('latest_conversations_id' => $last_conversation_id);
        $messages = Engine_Api::_()->mgslapi()->getMessages($viewer, $conversation, $params);
        $response = $this->getConversation($messages, 'today');

        $response['response']['success'] = 'Reply successfully.';
        $this->_jsonSuccessOutput($response);
      } catch (Exception $e) {
        $db->rollBack();
        $this->_jsonErrorOutput('unable to reply message');
      }
    }
  }

  public function postnewmessageAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      if (empty($user_id)) {
        $this->_jsonErrorOutput('User id required');
      }
      $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
      $db->beginTransaction();

      try {
        $toUser = Engine_Api::_()->getItem('user', $user_id);
        $isMsgable = ( 'friends' != Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth') ||
                $viewer->membership()->isMember($toUser) );

        if ($toUser instanceof User_Model_User &&
                (!$viewer->isBlockedBy($toUser) && !$toUser->isBlockedBy($viewer)) &&
                isset($toUser->user_id) &&
                $isMsgable) {
          $toObject = $toUser;
        }
        // Prepopulated
        if ($toObject instanceof User_Model_User) {
          $recipientsUsers = array($toObject);
          $recipients = $toObject;
          // Validate friends
          if ('friends' == Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth')) {
            if (!$viewer->membership()->isMember($recipients)) {
              $this->_jsonErrorOutput('Member specified is not in your friends list.');
            }
          }
        } else if ($toObject instanceof Core_Model_Item_Abstract &&
                method_exists($toObject, 'membership')) {
          $recipientsUsers = $toObject->membership()->getMembers();
          $recipients = $toObject;
        }
        // Normal
        else {
          $recipients = preg_split('/[,. ]+/', $user_id);
          // clean the recipients for repeating ids
          // this can happen if recipient is selected and then a friend list is selected
          $recipients = array_unique($recipients);
          // Slice down to 10
          $recipients = array_slice($recipients, 0, $maxRecipients);
          // Get user objects
          $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);
          // Validate friends
          if ('friends' == Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth')) {
            foreach ($recipientsUsers as &$recipientUser) {
              if (!$viewer->membership()->isMember($recipientUser)) {
                $this->_jsonErrorOutput('Member specified is not in your friends list.');
              }
            }
          }
        }
        // Create conversation
        $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
                $viewer, $recipients, trim(strip_tags($subject)), trim(strip_tags($body)), null
        );

        // Send notifications
        foreach ($recipientsUsers as $user) {
          if ($user->getIdentity() == $viewer->getIdentity()) {
            continue;
          }
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
                  $user, $viewer, $conversation, 'message_new'
          );
        }

        // Increment messages counter
        Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

        // Commit
        $db->commit();
        $response['response']['success'] = 'Message send successfully.';
        $this->_jsonSuccessOutput($response);
      } catch (Exception $e) {
        $db->rollBack();
        //throw $e;
        $this->_jsonErrorOutput('unable to send message');
      }
    }
  }

  public function getalbumsAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      $table = Engine_Api::_()->getItemTable('album');
      if (!in_array($order, $table->info('cols'))) {
        $order = 'modified_date';
      }
      $select = $table->select()
              ->where("search = 1")
              ->order('modified_date ASC');
      $album_select = $select;
      $new_select = array();
      $i = 0;
      foreach ($album_select->getTable()->fetchAll($album_select) as $album) {
        if (Engine_Api::_()->authorization()->isAllowed($album, $viewer, 'view')) {
          $new_select[$i++] = $album;
        }
      }
      if ($page == '-1') {
        $paginator = $table->fetchAll($select);
        $total = count($paginator);
      } else {
        $paginator = Zend_Paginator::factory($new_select);
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page ? (int) $page : 1);
        $total = $paginator->getTotalItemCount();
      }

      $i = 0;
      $response = array();
      if ($total > 0) {
        $response['response']['success'] = 'success';
        foreach ($paginator as $album) {
          $response['response']['data'][$i]['id'] = $album->album_id;
          $response['response']['data'][$i]['title'] = $album->getTitle(); //$this->view->string()->chunk($this->view->string()->truncate($album->getTitle(), 45)); 
          $response['response']['data'][$i]['created_by'] = $album->getOwner()->getTitle();
          $response['response']['data'][$i]['number_of_photo'] = $album->count();
          $response['response']['data'][$i]['photo_url'] = $this->mybase64_encode(Engine_Api::_()->mgslapi()->getItemPhotoUrl($album));

          $i++;
        }
      } else {
        $response['response']['data'] = 'No album found';
      }
      $response['response']['total'] = $total;
      $response['response']['hash'] = md5(serialize($response));
      //echo '<pre>';            print_r($response); exit;
      $this->_jsonSuccessOutput($response);
    }
  }

  public function getmyalbumsAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      $table = Engine_Api::_()->getItemTable('album');
      if (!in_array($order, $table->info('cols'))) {
        $order = 'modified_date';
      }

      $select = $table->select()
              ->where('owner_id = ?', $viewer->getIdentity())
              ->order('modified_date DESC');
      ;

      $album_select = $select;
      $new_select = array();
      $i = 0;
      foreach ($album_select->getTable()->fetchAll($album_select) as $album) {
        if (Engine_Api::_()->authorization()->isAllowed($album, $viewer, 'view')) {
          $new_select[$i++] = $album;
        }
      }
      if ($page == '-1') {
        $paginator = $table->fetchAll($select);
        $total = count($paginator);
      } else {
        $paginator = Zend_Paginator::factory($new_select);
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page ? (int) $page : 1);
        $total = $paginator->getTotalItemCount();
      }

      $i = 0;
      $response = array();
      if ($total > 0) {
        $response['response']['success'] = 'success';
        foreach ($paginator as $album) {
          $response['response']['data'][$i]['id'] = $album->album_id;
          $response['response']['data'][$i]['title'] = $album->getTitle(); //$this->view->string()->chunk($this->view->string()->truncate($album->getTitle(), 45)); 
          $response['response']['data'][$i]['created_by'] = $album->getOwner()->getTitle();
          $response['response']['data'][$i]['number_of_photo'] = $album->count();
          $response['response']['data'][$i]['photo_url'] = $this->mybase64_encode(Engine_Api::_()->mgslapi()->getItemPhotoUrl($album));

          $i++;
        }
      } else {
        $response['response']['data'] = 'No album found';
      }
      $response['response']['total'] = $total;
      $response['response']['hash'] = md5(serialize($response));
      //echo '<pre>';            print_r($response); exit;
      $this->_jsonSuccessOutput($response);
    }
  }

  public function getalbumphotoAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      if (empty($album_id)) {
        $this->_jsonErrorOutput('Album id required');
      }

      $album = Engine_Api::_()->getItem('album', $album_id);

      if (!$album || empty($album->album_id)) {
        $this->_jsonErrorOutput('No album found');
      }

      // Prepare data
      $photoTable = Engine_Api::_()->getItemTable('album_photo');

      if ($page == '-1') {
        $select = $photoTable->getPhotoSelect((array('album' => $album)));
        $photos = $photoTable->fetchAll($select);
        $total = count($photos);
      } else {
        $photos = $photoTable->getPhotoPaginator(array(
            'album' => $album,
        ));
        $photos->setItemCountPerPage(10);
        $photos->setCurrentPageNumber($page ? (int) $page : 1);
        $total = $photos->getTotalItemCount();
      }



      if (!$album->getOwner()->isSelf($viewer)) {
        $album->getTable()->update(array(
            'view_count' => new Zend_Db_Expr('view_count + 1'),
                ), array(
            'album_id = ?' => $album->getIdentity(),
        ));
      }
      $i = 0;
      if ($total > 0) {
        $response['response']['success'] = 'success';
        foreach ($photos as $photo) {
          $response['response']['data'][$i]['photo_id'] = $photo->photo_id;
          $response['response']['data'][$i]['album_id'] = $album->album_id;
          $response['response']['data'][$i]['photo_thumb_url_encoded'] = $this->mybase64_encode(Engine_Api::_()->mgslapi()->getItemPhotoUrl($photo, 'thumb.normal'));
          $response['response']['data'][$i]['photo_big_url_encoded'] = $this->mybase64_encode(Engine_Api::_()->mgslapi()->getItemPhotoUrl($photo, 'thumb.main'));
          $response['response']['data'][$i]['photo_thumb_url'] = Engine_Api::_()->mgslapi()->getItemPhotoUrl($photo, 'thumb.normal');
          $response['response']['data'][$i]['photo_big_url'] = Engine_Api::_()->mgslapi()->getItemPhotoUrl($photo, 'thumb.main');
          $response['response']['data'][$i]['photo_description'] = $photo->title ? $photo->title : '';
          $response['response']['data'][$i]['photo_added_by_name'] = $photo->getOwner()->getTitle();
          $response['response']['data'][$i]['photo_added_user_id'] = $photo->getOwner()->user_id;
          $response['response']['data'][$i]['photo_added_date'] = $this->view->customtimestamp($photo->creation_date);
          $response['response']['data'][$i]['photo_total_likes'] = $photo->likes()->getLikeCount();
          $response['response']['data'][$i]['photo_total_comments'] = $photo->comments()->getCommentCount();
          $response['response']['data'][$i]['photo_liked_by_me'] = $photo->likes()->isLike($viewer) ? 'yes' : 'no';
          $i++;
        }
      } else {
        $response['response']['data'] = 'No photo found';
      }
      $response['response']['total'] = $total;
      $response['response']['hash'] = md5(serialize($response));
      //echo '<pre>';            print_r($response); exit;
      $this->_jsonSuccessOutput($response);
    }
  }

  public function getlatestcommentsofaphotoAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      if (empty($photo_id)) {
        return;
        //$this->JSONErrorOutput('Photo id required');
      }

      $subject = Engine_Api::_()->getItem('album_photo', $photo_id);

      if (!$subject) {
        return;
        //$this->JSONErrorOutput('No photo found');
      }

      // Prepare data


      if ($page == '-1') {
        echo 'not done';
        exit;
//                $select = $photoTable->getPhotoSelect((array('album' => $album)));
//                $photos = $photoTable->fetchAll($select);
//                $total = count($photos);
      } else {
        $page = $page ? $page : 1;
        // If not has a page, show the
        $commentSelect = $subject->comments()->getCommentSelect();
        $commentSelect->order('comment_id DESC');
        $comments = Zend_Paginator::factory($commentSelect);
        $comments->setCurrentPageNumber($page);
        $comments->setItemCountPerPage(10);

        $total = $comments->getTotalItemCount();

        $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
        $canDelete = $subject->authorization()->isAllowed($viewer, 'edit');

        // Likes
        $likes = $subject->likes()->getLikePaginator();
      }

      if ($total < 1) {
        return;
        //$this->JSONErrorOutput('No comment found');
      }

      $html = $this->view->partial(
              '_view_photo_comment.tpl', $this->_currentModuleCoreApi->getModuleName(), array('comments' => $comments,
          'viewer' => $viewer,
          'canComment' => $canComment,
          'page' => $page ? $page : 1
              )
      );
//            if(strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== FALSE)
//            {
//                echo $html ; exit;
//            }
      header('Content-Type: text/plain');
      echo $html;
    }
  }

  public function getpreviouscommentsofaphotoAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      if (empty($photo_id)) {
        $this->_jsonErrorOutput('Photo id required');
      }
      if (empty($last_comment_id)) {
        $this->_jsonErrorOutput('Comment id required');
      }

      $subject = Engine_Api::_()->getItem('album_photo', $photo_id);

      if (!$subject) {
        $this->_jsonErrorOutput('No photo found');
      }

      // Prepare data
      // If not has a page, show the
      $commentSelect = $subject->comments()->getCommentSelect();
      $commentSelect->where('comment_id < ? ', (int) $last_comment_id);
      $commentSelect->order('comment_id DESC');
      $comments = Zend_Paginator::factory($commentSelect);
      $comments->setCurrentPageNumber(1);
      $comments->setItemCountPerPage(50);

      $total = $comments->getTotalItemCount();

      $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
      $canDelete = $subject->authorization()->isAllowed($viewer, 'edit');

      // Likes
      $likes = $subject->likes()->getLikePaginator();


      if ($total < 1) {
        $this->_jsonErrorOutput('No comment found');
      }

      $html = $this->view->partial(
              '_view_photo_comment.tpl', $this->_currentModuleCoreApi->getModuleName(), array('comments' => $comments,
          'viewer' => $viewer,
          'canComment' => $canComment,
          'page' => $page ? $page : 1
              )
      );
//            if(strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== FALSE)
//            {
//                echo $html ; exit;
//            }
      header('Content-Type: text/plain');
      echo $html;
    }
  }

  public function getnewestcommentsofaphotoAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      if (empty($photo_id)) {
        $this->_jsonErrorOutput('Photo id required');
      }
      if (empty($newest_comment_id)) {
        $this->_jsonErrorOutput('Comment id required');
      }

      $subject = Engine_Api::_()->getItem('album_photo', $photo_id);

      if (!$subject) {
        $this->_jsonErrorOutput('No photo found');
      }

      // Prepare data
      // If not has a page, show the
      $commentSelect = $subject->comments()->getCommentSelect();
      $commentSelect->where('comment_id > ? ', (int) $newest_comment_id);
      $commentSelect->order('comment_id DESC');
      $comments = Zend_Paginator::factory($commentSelect);
      $comments->setCurrentPageNumber(1);
      $comments->setItemCountPerPage(50);

      $total = $comments->getTotalItemCount();

      $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
      $canDelete = $subject->authorization()->isAllowed($viewer, 'edit');

      // Likes
      $likes = $subject->likes()->getLikePaginator();


      if ($total < 1) {
        $this->_jsonErrorOutput('No comment found');
      }

      $html = $this->view->partial(
              '_view_photo_comment.tpl', $this->_currentModuleCoreApi->getModuleName(), array('comments' => $comments,
          'viewer' => $viewer,
          'canComment' => $canComment,
          'page' => $page ? $page : 1
              )
      );
//            if(strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== FALSE)
//            {
//                echo $html ; exit;
//            }
//            header('Content-Type: text/plain');   
//            echo $html ; 

      $response = array();
      $response['response']['body'] = base64_encode($html);
      $response['response']['hash'] = md5(serialize($response));
      $this->_jsonSuccessOutput($response);
    }
  }

  public function getfriendsAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      $select = $viewer->membership()->getMembersObjectSelect();
      $select = $select->order('displayname ASC');
      if ($page == '-1') {
        $friends = $select->getTable()->fetchAll($select);
        $total = count($friends);
      } else {
        $friends = Zend_Paginator::factory($select);
        $friends->setItemCountPerPage(10);
        $friends->setCurrentPageNumber($page ? (int) $page : 1);
        $total = $friends->getTotalItemCount();
      }

      // Get stuff
//            $ids = array();
//            foreach( $friends as $friend ) {
//              $ids[] = $friend->resource_id;
//            }
//            
//            $friendUsers = array();
//            foreach( Engine_Api::_()->getItemTable('user')->find($ids) as $friendUser ) {
//              $friendUsers[$friendUser->getIdentity()] = $friendUser;
//            }


      $i = 0;
      $response = array();
      if ($total > 0) {
        $response['response']['success'] = 'success';
        foreach ($friends as $member) {
          if (!$member->user_id)
            continue;
//                    $member = $friendUsers[$membership->resource_id];
          $response['response']['data'][$i]['friend_id'] = $member->getIdentity();
          $response['response']['data'][$i]['friend_name'] = $member->displayname;
          $response['response']['data'][$i]['friend_profile_photo'] = $this->mybase64_encode(Engine_Api::_()->mgslapi()->getItemPhotoUrl($member));
          $i++;
        }
      }
      else {
        $response['response']['data'] = 'No friend found';
      }
      $response['response']['total'] = $total;
      $response['response']['hash'] = md5(serialize($response));
      //echo '<pre>';            print_r($response); exit;
      $this->_jsonSuccessOutput($response);
    }
  }

  public function getmembersAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      // Get table info
      $table = Engine_Api::_()->getItemTable('user');
      $userTableName = $table->info('name');

      $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
      $searchTableName = $searchTable->info('name');


      // Contruct query
      $select = $table->select()
              //->setIntegrityCheck(false)
              ->from($userTableName)
              ->joinLeft($searchTableName, "`{$searchTableName}`.`item_id` = `{$userTableName}`.`user_id`", null)
              //->group("{$userTableName}.user_id")
              ->where("{$userTableName}.search = ?", 1)
              ->where("{$userTableName}.enabled = ?", 1)
              ->order("{$userTableName}.displayname ASC");

      // Build the photo and is online part of query
      if (isset($has_photo) && !empty($has_photo)) {
        $select->where($userTableName . '.photo_id != ?', "0");
      }

      if (isset($is_online) && !empty($is_online)) {
        $select
                ->joinRight("engine4_user_online", "engine4_user_online.user_id = `{$userTableName}`.user_id", null)
                ->group("engine4_user_online.user_id")
                ->where($userTableName . '.user_id != ?', "0");
      }

      if ($page == '-1') {
        $paginator = $table->fetchAll($select);
        $total = count($paginator);
      } else {
        // Build paginator
        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page);
        $total = $paginator->getTotalItemCount();
      }

      // getting common friend
      $viewerFriendId = array();
      $viewerFriendsSelect = $viewer->membership()->getMembersObjectSelect();
      $viewerFriends = $table->fetchAll($viewerFriendsSelect);
      foreach ($viewerFriends as $viewerFriend) {
        $viewerFriendId[] = $viewerFriend->getIdentity();
      }
      $i = 0;
      $response = array();
      if ($total > 0) {
        foreach ($paginator as $user) {
          $friendOfFriendId = array();
          $friendOfFriendselect = $user->membership()->getMembersObjectSelect();
          $friendOfFriends = $table->fetchAll($friendOfFriendselect);
          foreach ($friendOfFriends as $friendOfFriend) {
            $friendOfFriendId[] = $friendOfFriend->getIdentity();
          }
          $commonFriends = array_intersect($viewerFriendId, $friendOfFriendId);

          $response['response']['data'][$i]['member_id'] = $user->user_id;
          $response['response']['data'][$i]['name'] = $user->displayname;
          $response['response']['data'][$i]['profile_photo'] = $this->mybase64_encode(Engine_Api::_()->mgslapi()->getItemPhotoUrl($user));
          $response['response']['data'][$i]['total_mutual_friends'] = count($commonFriends);
          $i++;
        }
      } else {
        $response['response']['data'] = 'No member found';
      }
      $response['response']['total'] = $total;
      $response['response']['hash'] = md5(serialize($response));
      //echo '<pre>';            print_r($response); exit;
      $this->_jsonSuccessOutput($response);
    }
  }

  public function statusupdateAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      $subject = null;
      if ($subject_id AND $subject_type) {
        $subject = Engine_Api::_()->getItem($subject_type, (int) $subject_id);
        if (!$subject)
          $this->_jsonErrorOutput('Item not found');
      }
      // Use viewer as subject if no subject
      if (null === $subject) {
        $subject = $viewer;
      }
      $body = preg_replace('/<br[^<>]*>/', "\n", $body);

      // set up action variable
      $action = null;

      // Process
      $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
      $db->beginTransaction();
      try {
        $attachment = null;
//                $attachmentData = $this->getRequest()->getParam('attachment');
//                if( !empty($attachmentData) && !empty($attachmentData['type']) ) {
//                  $type = $attachmentData['type'];
//                  $config = null;
//                  foreach( Zend_Registry::get('Engine_Manifest') as $data ) {
//                    if( !empty($data['composer'][$type]) ) {
//                      $config = $data['composer'][$type];
//                    }
//                  }
//                  if( !empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1]) ) {
//                    $config = null;
//                  }
//                  if( $config ) {
//                    $plugin = Engine_Api::_()->loadClass($config['plugin']);
//                    $method = 'onAttach'.ucfirst($type);
//                    $attachment = $plugin->$method($attachmentData);
//                  }
//                }
        // Special case: status
        if (!$attachment && $viewer->isSelf($subject)) {
          if ($body != '') {
            $viewer->status = $body;
            $viewer->status_date = date('Y-m-d H:i:s');
            $viewer->save();

            $viewer->status()->setStatus($body);
          }

          $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, 'status', $body);
        } else { // General post
          $type = 'post';
          if ($viewer->isSelf($subject)) {
            $type = 'post_self';
          }

          // Add notification for <del>owner</del> user
          $subjectOwner = $subject->getOwner();

          if (!$viewer->isSelf($subject) &&
                  $subject instanceof User_Model_User) {
            $notificationType = 'post_' . $subject->getType();
            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($subjectOwner, $viewer, $subject, $notificationType, array(
                'url1' => $subject->getHref(),
            ));
          }

          // Add activity
          $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, $type, $body);

          // Try to attach if necessary
          if ($action && $attachment) {
            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);
          }
        }

        // Preprocess attachment parameters
        $publishMessage = preg_replace('/<br[^<>]*>/', "\n", $body);
        $publishUrl = null;
        $publishName = null;
        $publishDesc = null;
        $publishPicUrl = null;
        // Add attachment
        if ($attachment) {
          $publishUrl = $attachment->getHref();
          $publishName = $attachment->getTitle();
          $publishDesc = $attachment->getDescription();
          if (empty($publishName)) {
            $publishName = ucwords($attachment->getShortType());
          }
          if (($tmpPicUrl = $attachment->getPhotoUrl())) {
            $publishPicUrl = $tmpPicUrl;
          }
          // prevents OAuthException: (#100) FBCDN image is not allowed in stream
          if ($publishPicUrl &&
                  preg_match('/fbcdn.net$/i', parse_url($publishPicUrl, PHP_URL_HOST))) {
            $publishPicUrl = null;
          }
        } else {
          $publishUrl = !$action ? null : $action->getHref();
        }
        // Check to ensure proto/host
        if ($publishUrl &&
                false === stripos($publishUrl, 'http://') &&
                false === stripos($publishUrl, 'https://')) {
          $publishUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishUrl;
        }
        if ($publishPicUrl &&
                false === stripos($publishPicUrl, 'http://') &&
                false === stripos($publishPicUrl, 'https://')) {
          $publishPicUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishPicUrl;
        }
        // Add site title
        if ($publishName) {
          $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title
                  . ": " . $publishName;
        } else {
          $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
        }



        // Publish to facebook, if checked & enabled
        if ($this->_getParam('post_to_facebook', false) &&
                'publish' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable) {
          try {

            $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
            $facebook = $facebookApi = $facebookTable->getApi();
            $fb_uid = $facebookTable->find($viewer->getIdentity())->current();

            if ($fb_uid &&
                    $fb_uid->facebook_uid &&
                    $facebookApi &&
                    $facebookApi->getUser() &&
                    $facebookApi->getUser() == $fb_uid->facebook_uid) {
              $fb_data = array(
                  'message' => $publishMessage,
              );
              if ($publishUrl) {
                $fb_data['link'] = $publishUrl;
              }
              if ($publishName) {
                $fb_data['name'] = $publishName;
              }
              if ($publishDesc) {
                $fb_data['description'] = $publishDesc;
              }
              if ($publishPicUrl) {
                $fb_data['picture'] = $publishPicUrl;
              }
              $res = $facebookApi->api('/me/feed', 'POST', $fb_data);
            }
          } catch (Exception $e) {
            // Silence
          }
        } // end Facebook
        // Publish to twitter, if checked & enabled
        if ($this->_getParam('post_to_twitter', false) &&
                'publish' == Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable) {
          try {
            $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
            if ($twitterTable->isConnected()) {
              // @todo truncation?
              // @todo attachment
              $twitter = $twitterTable->getApi();
              $twitter->statuses->update($publishMessage);
            }
          } catch (Exception $e) {
            // Silence
          }
        }

        // Publish to janrain
        if (//$this->_getParam('post_to_janrain', false) &&
                'publish' == Engine_Api::_()->getApi('settings', 'core')->core_janrain_enable) {
          try {
            $session = new Zend_Session_Namespace('JanrainActivity');
            $session->unsetAll();

            $session->message = $publishMessage;
            $session->url = $publishUrl ? $publishUrl : 'http://' . $_SERVER['HTTP_HOST'] . _ENGINE_R_BASE;
            $session->name = $publishName;
            $session->desc = $publishDesc;
            $session->picture = $publishPicUrl;
          } catch (Exception $e) {
            // Silence
          }
        }
        $db->commit();
        if (!empty($newest_feed_id)) {
          $data = $this->allfeed($viewer, $subject, $newest_feed_id);

          $html = $this->view->partial(
                  '_view_latest_feed.tpl', $this->_currentModuleCoreApi->getModuleName(), $data
          );
          $response = array();
          //echo $html ; exit;
          $response['response']['body'] = base64_encode($html);
          $response['response']['hash'] = md5(serialize($response));
          $this->_jsonSuccessOutput($response);
        } else {
          $response['response']['success'] = ' Status updated';
          $this->_jsonSuccessOutput($response);
        }
      } catch (Exception $ex) {
        $db->rollBack();
        //throw $e; // This should be caught by error handler
        $this->_jsonErrorOutput('unable to status update');
      }
    }
  }

  public function photouploadAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      $subject = null;
      if ($subject_id AND $subject_type) {
        $subject = Engine_Api::_()->getItem($subject_type, (int) $subject_id);
        if (!$subject)
          $this->_jsonErrorOutput('Item not found');
      }
      // Use viewer as subject if no subject
      if (null === $subject) {
        $subject = $viewer;
      }
      $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
      $body = preg_replace('/<br[^<>]*>/', "\n", $body);

      // set up action variable
      $action = null;

      if ($_FILES['image']['name'] === '') {
        $this->_jsonErrorOutput('Image not found');
      }

      if ($_FILES['image']['name'] != '') {
        // Get album
        $table = Engine_Api::_()->getDbtable('albums', 'album');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
          $type = 'wall';
          $album = $table->getSpecialAlbum($viewer, $type);

          $photoTable = Engine_Api::_()->getItemTable('album_photo');
          $photo = $photoTable->createRow();
          $photo->setFromArray(array(
              'owner_type' => 'user',
              'owner_id' => $viewer->getIdentity()
          ));
          $photo->save();
          $photo->setPhoto($_FILES['image']);


          if ($type === 'message') {
            $photo->title = Zend_Registry::get('Zend_Translate')->_('Attached Image');
          }
          $photo->album_id = $album->album_id;
          //$photo->collection_id = $album->album_id;
          $photo->save();

          if (!$album->photo_id) {
            $album->photo_id = $photo->getIdentity();
            $album->save();
          }
          if ($type != 'message') {
            // Authorizations
            $auth = Engine_Api::_()->authorization()->context;
            $auth->setAllowed($photo, 'everyone', 'view', true);
            $auth->setAllowed($photo, 'everyone', 'comment', true);
            $auth->setAllowed($album, 'everyone', 'view', true);
            $auth->setAllowed($album, 'everyone', 'comment', true);
          }
          $db->commit();
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
          //$this->JSONErrorOutput('Unable to upload photo');
        }
      }

      // Process
      $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
      $db->beginTransaction();
      try {
        $attachment = null;
        if (isset($photo)) {
          $attachmentData['type'] = 'photo';
          $attachmentData['photo_id'] = $photo->photo_id;
        }
        if (!empty($attachmentData) && !empty($attachmentData['type'])) {
          $type = $attachmentData['type'];
          $config = null;
          foreach (Zend_Registry::get('Engine_Manifest') as $data) {
            if (!empty($data['composer'][$type])) {
              $config = $data['composer'][$type];
            }
          }
          if ($config) {
            $plugin = Engine_Api::_()->loadClass($config['plugin']);
            $method = 'onAttach' . ucfirst($type);
            $attachment = $plugin->$method($attachmentData);
          }
        }

        // Special case: status
        if (!$attachment && $viewer->isSelf($subject)) {
          if ($body != '') {
            $viewer->status = $body;
            $viewer->status_date = date('Y-m-d H:i:s');
            $viewer->save();

            $viewer->status()->setStatus($body);
          }

          $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, 'status', $body);
        } else { // General post
          $type = 'post';
          if ($viewer->isSelf($subject)) {
            $type = 'post_self';
          }

          // Add notification for <del>owner</del> user
          $subjectOwner = $subject->getOwner();

          if (!$viewer->isSelf($subject) &&
                  $subject instanceof User_Model_User) {
            $notificationType = 'post_' . $subject->getType();
            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($subjectOwner, $viewer, $subject, $notificationType, array(
                'url1' => $subject->getHref(),
            ));
          }

          // Add activity
          $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, $type, $body);

          // Try to attach if necessary
          if ($action && $attachment) {
            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);
          }
        }

        $db->commit();
        if (!empty($newest_feed_id)) {
          $data = $this->allfeed($viewer, $subject, $newest_feed_id);

          $html = $this->view->partial(
                  '_view_latest_feed.tpl', $this->_currentModuleCoreApi->getModuleName(), $data
          );
          $response = array();
          //echo $html ; exit;
          $response['response']['body'] = base64_encode($html);
          $response['response']['hash'] = md5(serialize($response));
          $this->_jsonSuccessOutput($response);
        } else {
          $response['response']['success'] = 'Status updated';
          $this->_jsonSuccessOutput($response);
        }
      } catch (Exception $ex) {
        $db->rollBack();
        //throw $e; // This should be caught by error handler
        $this->_jsonErrorOutput('unable to status update');
      }
    }
  }

  public function videouploadAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      $subject = null;
      if ($subject_id AND $subject_type) {
        $subject = Engine_Api::_()->getItem($subject_type, (int) $subject_id);
        if (!$subject)
          $this->_jsonErrorOutput('Item not found');
      }
      // Use viewer as subject if no subject
      if (null === $subject) {
        $subject = $viewer;
      }
      $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
      $body = preg_replace('/<br[^<>]*>/', "\n", $body);

      // set up action variable
      $action = null;

      if ($_POST['youtube'] != '' || $_POST['vimeo'] != '') {
        try {
          $this->_setParam('c_type', 'wall');
          $composer_type = $this->_getParam('c_type', 'wall');
          if ($_POST['youtube'] != '') {
            $video_type = 1;
            $code = $this->extractCode($_POST['youtube'], 1);
            $valid = $this->checkYouTube($code);
          }
          if ($_POST['vimeo'] != '') {
            $video_type = 2;
            $code = $this->extractCode($_POST['vimeo'], 2);
            $valid = $this->checkVimeo($code);
          }

          $values['user_id'] = $viewer->getIdentity();
          $paginator = Engine_Api::_()->getApi('core', 'video')->getVideosPaginator($values);
          $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'max');
          $current_count = $paginator->getTotalItemCount();

          if (($current_count >= $quota) && !empty($quota)) {
            // return error message
            $this->_jsonErrorOutput('You have already uploaded the maximum number of videos allowed. If you would like to upload a new video, please delete an old one first');
          } else if ($valid) {
            $table = Engine_Api::_()->getDbtable('videos', 'video');
            $db = $table->getAdapter();
            $db->beginTransaction();

            try {
              $information = $this->handleInformation($video_type, $code);
              // create video
              $video = $table->createRow();
              $video->title = $information['title'];
              $video->description = $information['description'];
              $video->duration = $information['duration'];
              $video->owner_id = $viewer->getIdentity();
              $video->code = $code;
              $video->type = $video_type;
              $video->save();

              // Now try to create thumbnail
              $thumbnail = $this->handleThumbnail($video->type, $video->code);
              $ext = ltrim(strrchr($thumbnail, '.'), '.');
              $thumbnail_parsed = @parse_url($thumbnail);
              $tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
              $thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;

              $src_fh = fopen($thumbnail, 'r');
              $tmp_fh = fopen($tmp_file, 'w');
              stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);

              $image = Engine_Image::factory();
              $image->open($tmp_file)
                      ->resize(120, 240)
                      ->write($thumb_file)
                      ->destroy();

              $thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
                  'parent_type' => $video->getType(),
                  'parent_id' => $video->getIdentity()
              ));

              // If video is from the composer, keep it hidden until the post is complete
              if ($composer_type)
                $video->search = 0;

              $video->photo_id = $thumbFileRow->file_id;
              $video->status = 1;
              $video->save();
              $db->commit();
            } catch (Exception $e) {
              $db->rollBack();
              throw $e;
            }

            if ($composer_type === 'wall') {
              // CREATE AUTH STUFF HERE
              $auth = Engine_Api::_()->authorization()->context;
              $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
              foreach ($roles as $i => $role) {
                $auth->setAllowed($video, $role, 'view', ($i <= $roles));
                $auth->setAllowed($video, $role, 'comment', ($i <= $roles));
              }
            }

            $attachmentData = array(
                'photo_id' => $video->photo_id,
                'video_id' => $video->video_id,
                'title' => $video->title,
                'description' => $video->description,
                'type' => 'video',
            );
          }
        } catch (Exception $e) {
          $this->_jsonErrorOutput('Unable to upload video');
        }
      } else {
        $this->_jsonErrorOutput('Video not found');
      }

      // Process
      $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
      $db->beginTransaction();
      try {
        $attachment = null;
        if (isset($photo)) {
          $attachmentData['type'] = 'photo';
          $attachmentData['photo_id'] = $photo->photo_id;
        }
        if (!empty($attachmentData) && !empty($attachmentData['type'])) {
          $type = $attachmentData['type'];
          $config = null;
          foreach (Zend_Registry::get('Engine_Manifest') as $data) {
            if (!empty($data['composer'][$type])) {
              $config = $data['composer'][$type];
            }
          }
          if ($config) {
            $plugin = Engine_Api::_()->loadClass($config['plugin']);
            $method = 'onAttach' . ucfirst($type);
            $attachment = $plugin->$method($attachmentData);
          }
        }

        // Special case: status
        if (!$attachment && $viewer->isSelf($subject)) {
          if ($body != '') {
            $viewer->status = $body;
            $viewer->status_date = date('Y-m-d H:i:s');
            $viewer->save();

            $viewer->status()->setStatus($body);
          }

          $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, 'status', $body);
        } else { // General post
          $type = 'post';
          if ($viewer->isSelf($subject)) {
            $type = 'post_self';
          }

          // Add notification for <del>owner</del> user
          $subjectOwner = $subject->getOwner();

          if (!$viewer->isSelf($subject) &&
                  $subject instanceof User_Model_User) {
            $notificationType = 'post_' . $subject->getType();
            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($subjectOwner, $viewer, $subject, $notificationType, array(
                'url1' => $subject->getHref(),
            ));
          }

          // Add activity
          $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, $type, $body);

          // Try to attach if necessary
          if ($action && $attachment) {
            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);
          }
        }

        $db->commit();
        if (!empty($newest_feed_id)) {
          $data = $this->allfeed($viewer, $subject, $newest_feed_id);

          $html = $this->view->partial(
                  '_view_latest_feed.tpl', $this->_currentModuleCoreApi->getModuleName(), $data
          );
          $response = array();
          //echo $html ; exit;
          $response['response']['body'] = base64_encode($html);
          $response['response']['hash'] = md5(serialize($response));
          $this->_jsonSuccessOutput($response);
        } else {
          $response['response']['success'] = 'Status updated';
          $this->_jsonSuccessOutput($response);
        }
      } catch (Exception $ex) {
        $db->rollBack();
        //throw $e; // This should be caught by error handler
        $this->_jsonErrorOutput('unable to status update');
      }
    }
  }

  public function postcommentAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      if (!in_array($comment_type, array('feed', 'album', 'album_photo', 'video'))) {
        $this->_jsonErrorOutput('Undefined comment type');
      }

      $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
      $db->beginTransaction();

      try {
        $body = trim(strip_tags($body));

        if ($comment_type == 'feed') {
          $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById((int) $id);
          if (!$action) {
            $this->_jsonErrorOutput('Activity does not exist');
          }
          $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type . "_" . $action->subject_id);
          // Check authorization
          if (!Engine_Api::_()->authorization()->isAllowed($action->getObject(), $viewer, 'comment'))
            $this->_jsonErrorOutput('This user is not allowed to comment on this item.');
        }
        else {
          $action = Engine_Api::_()->getItem($comment_type, (int) $id);
          if (!$action) {
            $this->_jsonErrorOutput('Item does not exist');
          }
        }

        // Add the comment
        $comment = $action->comments()->addComment($viewer, $body);

        // Notifications
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $subjectOwner = $action->getOwner('user');
        if ($comment_type == 'feed') {
          // Add notification for owner of activity (if user and not viewer)
          if ($action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity()) {
            $notifyApi->addNotification($actionOwner, $viewer, $action, 'commented', array(
                'label' => 'post'
            ));
          }
        } else {
          $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
          // Activity
          $action2 = $activityApi->addActivity($viewer, $action, 'comment_' . $action->getType(), '', array(
              'owner' => $subjectOwner->getGuid(),
              'body' => $body
          ));
          if ($subjectOwner->getType() == 'user' && $subjectOwner->getIdentity() != $viewer->getIdentity()) {
            $notifyApi->addNotification($subjectOwner, $viewer, $action, 'commented', array(
                'label' => $action->getShortType()
            ));
          }
        }
        // Add a notification for all users that commented or like except the viewer and poster
        // @todo we should probably limit this
        foreach ($action->comments()->getAllCommentsUsers() as $notifyUser) {
          if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity())
            continue;
          if ($comment_type == 'feed') {
            $notifyApi->addNotification($notifyUser, $viewer, $action, 'commented_commented', array(
                'label' => 'post'
            ));
          } else {
            $notifyApi->addNotification($notifyUser, $viewer, $action, 'commented_commented', array(
                'label' => $action->getShortType()
            ));
          }
        }
        // Add a notification for all users that commented or like except the viewer and poster
        // @todo we should probably limit this
        foreach ($action->likes()->getAllLikesUsers() as $notifyUser) {
          if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity())
            continue;
          if ($comment_type == 'feed') {
            $notifyApi->addNotification($notifyUser, $viewer, $action, 'liked_commented', array(
                'label' => 'post'
            ));
          } else {
            // Don't send a notification if the user both commented and liked this
            if (in_array($notifyUser->getIdentity(), $commentedUserNotifications))
              continue;

            $notifyApi->addNotification($notifyUser, $viewer, $action, 'liked_commented', array(
                'label' => $action->getShortType()
            ));
          }
        }

        // Stats
        Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');

        if ($last_comment_id) {
          $params = array(
              'comment_id' => $last_comment_id
          );
        } else {
          $params = array(
              'current_comment_id' => $comment->comment_id
          );
        }
        $comments = Engine_Api::_()->mgslapi()->getLatestCommentsBeforePosting($action, $params);
        if (count($comments) < 1)
          $this->_jsonErrorOutput('No comment found');

        $html = $this->view->partial(
                '_commentsAfterPost.tpl', $this->_currentModuleCoreApi->getModuleName(), array(
            'comments' => $comments,
                )
        );
        $db->commit();


        $response = array();
        $response['response']['success'] = 'success';
        $response['response']['data'] = base64_encode($html);
        $this->_jsonSuccessOutput($response);
      } catch (Exception $e) {
        $db->rollBack();
        //throw $e;
        $this->_jsonErrorOutput('unable to post comment');
      }
    }
  }

  public function postfeedlikeAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      if (empty($feed_id)) {
        $this->_jsonErrorOutput('Feed id required');
      }
      // Start transaction
      $db = Engine_Api::_()->getDbtable('likes', 'activity')->getAdapter();
      $db->beginTransaction();

      try {
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById((int) $feed_id);
        if (!$action->action_id) {
          $this->_jsonErrorOutput('Feed not found');
        }
        // Action
        if (!$comment_id) {
          // Check authorization
//                  if( $action && !Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') ) {
//                    $this->JSONErrorOutput('This user is not allowed to like this item'); 
//                  }

          $action->likes()->addLike($viewer);

          // Add notification for owner of activity (if user and not viewer)
          if ($action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity()) {
            $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type . "_" . $action->subject_id);

            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($actionOwner, $viewer, $action, 'liked', array(
                'label' => 'post'
            ));
          }
        }
        // Comment
        else {
          $comment = $action->comments()->getComment((int) $comment_id);

          // Check authorization
//                  if( !$comment || !Engine_Api::_()->authorization()->isAllowed($comment, null, 'comment') ) {
//                    $this->JSONErrorOutput('This user is not allowed to like this item'); 
//                  }

          $comment->likes()->addLike($viewer);

          // @todo make sure notifications work right
          if ($comment->poster_id != $viewer->getIdentity()) {
            Engine_Api::_()->getDbtable('notifications', 'activity')
                    ->addNotification($comment->getPoster(), $viewer, $comment, 'liked', array(
                        'label' => 'comment'
            ));
          }

          // Add notification for owner of activity (if user and not viewer)
          if ($action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity()) {
            $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type . "_" . $action->subject_id);
          }
        }

        // Stats
        Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.likes');

        $db->commit();
        $response['response']['success'] = 'You now like this action.';
        $this->_jsonSuccessOutput($response);
      } catch (Exception $e) {
        $db->rollBack();
        //throw $e;
        $this->_jsonErrorOutput('unable to like this action');
      }
    }
  }

  public function postfeedunlikeAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      if (empty($feed_id)) {
        $this->_jsonErrorOutput('Feed id required');
      }
      // Start transaction
      $db = Engine_Api::_()->getDbtable('likes', 'activity')->getAdapter();
      $db->beginTransaction();

      try {
        $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById((int) $feed_id);
        if (!$action->action_id) {
          $this->_jsonErrorOutput('Feed not found');
        }
        // Action
        if (!$comment_id) {

          // Check authorization
          if ($action && !Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment')) {
            //throw new Engine_Exception('This user is not allowed to like this item');
            $this->_jsonErrorOutput('This user is not allowed to like this item');
          }

          $action->likes()->removeLike($viewer);
        }
        // Comment
        else {
          $comment = $action->comments()->getComment((int) $comment_id);

          // Check authorization
          if (!$comment || !Engine_Api::_()->authorization()->isAllowed($comment, null, 'comment')) {
            //throw new Engine_Exception('This user is not allowed to like this item');
            $this->_jsonErrorOutput('This user is not allowed to like this item');
          }

          $comment->likes()->removeLike($viewer);
        }
        $db->commit();
        $response['response']['success'] = 'Remove like';
        $this->_jsonSuccessOutput($response);
      } catch (Exception $e) {
        $db->rollBack();
        //throw $e;
        $this->_jsonErrorOutput('Unable to remove like');
      }
    }
  }

  public function getnewestcommentofafeedAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      if (empty($feed_id)) {
        $this->_jsonErrorOutput('Feed id required');
      }
      if (empty($newest_comment_id)) {
        $this->_jsonErrorOutput('Comment id required');
      }
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById((int) $feed_id);
      if (!$action) {
        $this->_jsonErrorOutput('Activity does not exist');
      }
      $params = array(
          'newest_comment_id' => $newest_comment_id
      );
      $comments = Engine_Api::_()->mgslapi()->getPreviousComments($action, $params);
      if (!$comments)
        $this->_jsonErrorOutput('No comment found');

      $comments = Zend_Paginator::factory($comments);
      $comments->setItemCountPerPage(10);

      if ($comments->getTotalItemCount() < 1) {
//                $this->JSONErrorOutput('No comment found');
        return;
      }


      $html = $this->view->partial(
              '_commentsAfterPost.tpl', $this->_currentModuleCoreApi->getModuleName(), array(
          'comments' => $comments,
          'parpage' => $comments->getItemCountPerPage(),
          'total' => $comments->getTotalItemCount()
              )
      );
//            header('Content-Type: text/plain');   
//            echo $html ; exit;
//            if(strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== FALSE)
//            {
//                echo $html ; exit;
//            }
      $response = array();
      $response['response']['body'] = base64_encode($html);
      $response['response']['hash'] = md5(serialize($response));
      $this->_jsonSuccessOutput($response);
    }
  }

  public function getlatestcommentsofafeedAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      if (empty($feed_id)) {
        return;
        //$this->JSONErrorOutput('Feed id required');
      }
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById((int) $feed_id);
      if (!$action) {
        return;
        //$this->JSONErrorOutput('Activity does not exist');
      }

      $comments = Engine_Api::_()->mgslapi()->getLatestComments($action);
      if (!$comments) {
        return;
        //$this->JSONErrorOutput('No comment found');
      }


      $comments = Zend_Paginator::factory($comments);
      $comments->setItemCountPerPage(10);
      if ($comments->getTotalItemCount() < 1) {
        return;
        //$this->JSONErrorOutput('No comment found');
      }


//            foreach ($comments as $comment)
//            {
//                $comment->comment_id.'<';
//            }


      $html = $this->view->partial(
              '_activityComments.tpl', $this->_currentModuleCoreApi->getModuleName(), array(
          'comments' => $comments,
          'parpage' => $comments->getItemCountPerPage(),
          'total' => $comments->getTotalItemCount()
              )
      );
      header('Content-Type: text/plain');
      echo $html;
    }
  }

  public function getpreviouscommentsofafeedAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      if (empty($feed_id)) {
        return;
        //$this->JSONErrorOutput('Feed id required');
      }
      if (empty($last_comment_id)) {
        return;
        //$this->JSONErrorOutput('Last comment id required');
      }
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById((int) $feed_id);
      if (!$action) {
        return;
        //$this->JSONErrorOutput('Activity does not exist');
      }
      $params = array(
          'comment_id' => $last_comment_id
      );
      $comments = Engine_Api::_()->mgslapi()->getPreviousComments($action, $params);
      if (!$comments) {
        return;
        //$this->JSONErrorOutput('No comment found');
      }


      $comments = Zend_Paginator::factory($comments);
      $comments->setItemCountPerPage(10);
      if ($comments->getTotalItemCount() < 1) {
//                $this->JSONErrorOutput('No comment found');
        return;
      }


      $html = $this->view->partial(
              '_commentsAfterPost.tpl', $this->_currentModuleCoreApi->getModuleName(), array(
          'comments' => $comments,
          'parpage' => $comments->getItemCountPerPage(),
          'total' => $comments->getTotalItemCount()
              )
      );
      header('Content-Type: text/plain');
      echo $html;
      exit;
    }
  }

  public function getvideolistAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      $values = array();
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('ynvideo')) {
        $select = Engine_Api::_()->getApi('core', 'ynvideo')->getVideosSelect($values);
      } else {
        $select = Engine_Api::_()->getApi('core', 'video')->getVideosSelect($values);
      }
      if ($page == '-1') {
        $videos = $select->getTable()->fetchAll($select);
        $total = count($videos);
      } else {
        $select->limit(3);
        $videos = Zend_Paginator::factory($select);
        $videos->setItemCountPerPage(10);
        $videos->setCurrentPageNumber($page ? (int) $page : 1);
        $total = $videos->getTotalItemCount();
      }
      $i = 0;
      $response = array();
      if ($total > 0) {
        $response['response']['success'] = 'success';
        foreach ($videos as $video) {
          if ($video->type == 1)
            $type = 'youtube';
          elseif ($video->type == 2)
            $type = 'vimeo';
          else
            $type = 'unknown';

          $response['response']['data'][$i]['id'] = $video->video_id;
          $response['response']['data'][$i]['title'] = $video->title ? $video->title : '';
          $response['response']['data'][$i]['type'] = $type;
          $response['response']['data'][$i]['code'] = $video->code;
          $response['response']['data'][$i]['image'] = $this->mybase64_encode(Engine_Api::_()->mgslapi()->getItemPhotoUrl($video));
          $response['response']['data'][$i]['uploaded_by'] = $video->getOwner()->getTitle();
          $response['response']['data'][$i]['uploaded_datetime'] = $this->view->customtimestamp($video->creation_date);
          $i++;
        }
      }
      else {
        $response['response']['data'] = 'No album found';
      }
      $response['response']['total'] = $total;
      $response['response']['hash'] = md5(serialize($response));
      //echo '<pre>';            print_r($response); exit;
      $this->_jsonSuccessOutput($response);
    }
  }

  public function getmyvideolistAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      $values = array();
      $values['user_id'] = $viewer->getIdentity();
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('ynvideo')) {
        $select = Engine_Api::_()->getApi('core', 'ynvideo')->getVideosSelect($values);
      } else {
        $select = Engine_Api::_()->getApi('core', 'video')->getVideosSelect($values);
      }

      if ($page == '-1') {
        $videos = $select->getTable()->fetchAll($select);
        $total = count($videos);
      } else {
        $select->limit(3);
        $videos = Zend_Paginator::factory($select);
        $videos->setItemCountPerPage(10);
        $videos->setCurrentPageNumber($page ? (int) $page : 1);
        $total = $videos->getTotalItemCount();
      }
      $i = 0;
      $response = array();
      if ($total > 0) {
        $response['response']['success'] = 'success';
        foreach ($videos as $video) {
          if ($video->type == 1)
            $type = 'youtube';
          elseif ($video->type == 2)
            $type = 'vimeo';
          else
            $type = 'unknown';

          $response['response']['data'][$i]['id'] = $video->video_id;
          $response['response']['data'][$i]['title'] = $video->title ? $video->title : '';
          $response['response']['data'][$i]['type'] = $type;
          $response['response']['data'][$i]['code'] = $video->code;
          $response['response']['data'][$i]['image'] = $this->mybase64_encode(Engine_Api::_()->mgslapi()->getItemPhotoUrl($video));
          $response['response']['data'][$i]['uploaded_by'] = $video->getOwner()->getTitle();
          $response['response']['data'][$i]['uploaded_datetime'] = $this->view->customtimestamp($video->creation_date);
          $i++;
        }
      }
      else {
        $response['response']['data'] = 'No album found';
      }
      $response['response']['total'] = $total;
      $response['response']['hash'] = md5(serialize($response));
      //echo '<pre>';            print_r($response); exit;
      $this->_jsonSuccessOutput($response);
    }
  }

  public function getcalenderlistAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      $values = array();
      $select = Engine_Api::_()->getItemTable('event')->getEventSelect($values);
      if ($page == '-1') {
        $events = $select->getTable()->fetchAll($select);
        $total = count($events);
      } else {
        $select->limit(10);
        $events = Zend_Paginator::factory($select);
        $events->setItemCountPerPage(10);
        $events->setCurrentPageNumber($page ? (int) $page : 1);
        $total = $events->getTotalItemCount();
      }
      $i = 0;
      $response = array();
      if ($total > 0) {
        $response['response']['success'] = 'success';
        foreach ($events as $event) {
          $response['response']['data'][$i]['id'] = $event->event_id;
          $response['response']['data'][$i]['title'] = $event->title;
          $response['response']['data'][$i]['image'] = $this->mybase64_encode(Engine_Api::_()->mgslapi()->getItemPhotoUrl($event));
          $response['response']['data'][$i]['location'] = $event->location ? $event->location : '';
          $response['response']['data'][$i]['starttime'] = $this->view->locale()->toDateTime($event->starttime);
          $i++;
        }
      } else {
        $response['response']['data'] = 'No event found';
      }
      $response['response']['total'] = $total;
      $response['response']['hash'] = md5(serialize($response));
      $this->_jsonSuccessOutput($response);
    }
  }

  public function getmycalenderlistAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      $values = array();
      $values['user_id'] = $viewer->getIdentity();
      $select = Engine_Api::_()->getItemTable('event')->getEventSelect($values);
      if ($page == '-1') {
        $events = $select->getTable()->fetchAll($select);
        $total = count($events);
      } else {
        $select->limit(10);
        $events = Zend_Paginator::factory($select);
        $events->setItemCountPerPage(10);
        $events->setCurrentPageNumber($page ? (int) $page : 1);
        $total = $events->getTotalItemCount();
      }
      $i = 0;
      $response = array();
      if ($total > 0) {
        $response['response']['success'] = 'success';
        foreach ($events as $event) {
          $response['response']['data'][$i]['id'] = $event->event_id;
          $response['response']['data'][$i]['title'] = $event->title;
          $response['response']['data'][$i]['image'] = $this->mybase64_encode(Engine_Api::_()->mgslapi()->getItemPhotoUrl($event));
          $response['response']['data'][$i]['location'] = $event->location ? $event->location : '';
          $response['response']['data'][$i]['starttime'] = $this->view->locale()->toDateTime($event->starttime);
          $i++;
        }
      } else {
        $response['response']['data'] = 'No event found';
      }
      $response['response']['total'] = $total;
      $response['response']['hash'] = md5(serialize($response));
      $this->_jsonSuccessOutput($response);
    }
  }

  public function calenderrsvpAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      if (!in_array($type, array('attending', 'maybe', 'not'))) {
        $this->_jsonErrorOutput('Unkonwn type');
      }

      if (empty($event_id)) {
        $this->_jsonErrorOutput('Event id required');
      }
      $event = Engine_Api::_()->getItem('event', (int) $event_id);

      if (!$event) {
        $this->_jsonErrorOutput('Event not found');
      }

      if (!$event->membership()->isMember($viewer, true)) {
        $this->_jsonErrorOutput('You are not member of this event');
      }
      $option_id = 0;
      if ($type == 'attending') {
        $option_id = 2;
      } elseif ($type == 'maybe') {
        $option_id = 1;
      } elseif ($type == 'not') {
        $option_id = 0;
      }

      $row = $event->membership()->getRow($viewer);
      $row->rsvp = $option_id;
      $response = array();
      if ($row->save()) {
        $response['response']['success'] = 'success';
        $response['response']['data'] = 'your RSVP change';
        $this->_jsonSuccessOutput($response);
      }
      $this->_jsonErrorOutput('Unable to save');
    }
  }

  public function getcirclelistAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());

      $customFieldValues = null;
      $values = array();
      $values['browse_page'] = 1;
      $values['type'] = 'browse';
      $values['type_location'] = 'browsePage';
      if ($page == '-1') {
        echo 'not completed';
        return;
        $events = $select->getTable()->fetchAll($select);
        $total = count($events);
      } else {

        $values['page'] = $page ? (int) $page : 1;
        $circles = Engine_Api::_()->sitepage()->getSitepagesPaginator($values, $customFieldValues);
        $circles->setItemCountPerPage(10);
        $total = $circles->getTotalItemCount();
      }
      $i = 0;
      $response = array();
      if ($total > 0) {
        $response['response']['success'] = 'success';
        foreach ($circles as $circle) {
          $response['response']['data'][$i]['id'] = $circle->page_id;
          $response['response']['data'][$i]['title'] = $circle->title;
          $response['response']['data'][$i]['image'] = $this->mybase64_encode(Engine_Api::_()->mgslapi()->getItemPhotoUrl($circle));
          $response['response']['data'][$i]['posted_by'] = $circle->getOwner()->getTitle();
          $response['response']['data'][$i]['posted_time'] = $this->view->customtimestamp($circle->creation_date);
          $response['response']['data'][$i]['total_like'] = $circle->like_count;
          $response['response']['data'][$i]['total_follow'] = $circle->follow_count;
          $response['response']['data'][$i]['total_view'] = $circle->view_count;
          $response['response']['data'][$i]['total_comment'] = $circle->comment_count;
          $i++;
        }
      } else {
        $response['response']['data'] = 'No album found';
      }
      $response['response']['total'] = $total;
      $response['response']['hash'] = md5(serialize($response));
      $this->_jsonSuccessOutput($response);
    }
  }

  public function getmycirclelistAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      $values = array();
      $values['user_id'] = $viewer->getIdentity();
      $values['type'] = 'manage';
      $values['type_location'] = 'manage';
      if ($page == '-1') {
        echo 'not completed';
        return;
        $events = $select->getTable()->fetchAll($select);
        $total = count($events);
      } else {
        $values['page'] = $page ? (int) $page : 1;
        $circles = Engine_Api::_()->sitepage()->getSitepagesPaginator($values, null);
        $circles->setItemCountPerPage(10);
        $total = $circles->getTotalItemCount();
      }
      $i = 0;
      $response = array();
      if ($total > 0) {
        $response['response']['success'] = 'success';
        foreach ($circles as $circle) {
          $response['response']['data'][$i]['id'] = $circle->page_id;
          $response['response']['data'][$i]['title'] = $circle->title;
          $response['response']['data'][$i]['image'] = $this->mybase64_encode(Engine_Api::_()->mgslapi()->getItemPhotoUrl($circle));
          $response['response']['data'][$i]['posted_by'] = $circle->getOwner()->getTitle();
          $response['response']['data'][$i]['posted_time'] = $this->view->customtimestamp($circle->creation_date);
          $response['response']['data'][$i]['total_like'] = $circle->like_count;
          $response['response']['data'][$i]['total_follow'] = $circle->follow_count;
          $response['response']['data'][$i]['total_view'] = $circle->view_count;
          $response['response']['data'][$i]['total_comment'] = $circle->comment_count;
          $i++;
        }
      } else {
        $response['response']['data'] = 'No album found';
      }
      $response['response']['total'] = $total;
      $response['response']['hash'] = md5(serialize($response));
      $this->_jsonSuccessOutput($response);
    }
  }

  public function likeacircleAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());

      if (empty($circle_id)) {
        $this->_jsonErrorOutput('Circle id required');
      }
      $resource = Engine_Api::_()->getItem('sitepage_page', $circle_id);
      $resource_id = $circle_id;
      $resource_type = 'sitepage_page';
      if (!$resource) {
        $this->_jsonErrorOutput('Circle not found');
      }
      $like_id_temp = Engine_Api::_()->getApi('like', 'seaocore')->hasLike('sitepage_page', $circle_id);

      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage')) {
        $sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;
      }
      //CHECK THE THE ITEM IS LIKED OR NOT.
      if (empty($like_id_temp[0]['like_id'])) {

        $response = array();
        $likeTable = Engine_Api::_()->getItemTable('core_like');
        $notify_table = Engine_Api::_()->getDbtable('notifications', 'activity');
        $db = $likeTable->getAdapter();
        $db->beginTransaction();
        try {

          $label = '{"label":"page"}';
          $getOwnerId = Engine_Api::_()->getItem($resource_type, $resource_id)->getOwner()->user_id;

          $object_type = $resource_type;

          if (!empty($getOwnerId) && $getOwnerId != $viewer->getIdentity()) {
            $notifyData = $notify_table->createRow();
            $notifyData->user_id = $getOwnerId;
            $notifyData->subject_type = $viewer->getType();
            $notifyData->subject_id = $viewer->getIdentity();
            $notifyData->object_type = $object_type;
            $notifyData->object_id = $resource_id;
            $notifyData->type = 'liked';
            $notifyData->params = $resource->getShortType();
            $notifyData->date = date('Y-m-d h:i:s', time());
            $notifyData->save();
          }
          //END NOTIFICATION WORK.

          if (!empty($resource)) {

            //START PAGE MEMBER PLUGIN WORK.
            if ($resource_type == 'sitepage_page' && $sitepageVersion >= '4.3.0p1') {
              if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
                Engine_Api::_()->sitepagemember()->joinLeave($resource, 'Join');
              }
              Engine_Api::_()->sitepage()->itemCommentLike($resource, 'sitepage_contentlike');
            } elseif ($resource_type == 'siteevent_event') {
              Engine_Api::_()->siteevent()->itemCommentLike($resource, 'siteevent_contentlike', '', 'like');
            }
            //END PAGE MEMBER PLUGIN WORK.

            $like_id = $likeTable->addLike($resource, $viewer)->like_id;
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike'))
              Engine_Api::_()->sitelike()->setLikeFeed($viewer, $resource);
          }

          //PASS THE LIKE ID VALUE.
          $this->view->like_id = $like_id;
          $db->commit();
          $response['response']['success'] = 'success';
          $response['response']['data'] = 'Successfully Liked.';
          $this->_jsonSuccessOutput($response);
        } catch (Exception $e) {
          $db->rollBack();
          //throw $e ;
          $this->_jsonErrorOutput('An error has occur');
        }
      } else {
        $this->_jsonErrorOutput('Already liked');
      }
    }
  }

  public function unlikeacircleAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());

      if (empty($circle_id)) {
        $this->_jsonErrorOutput('Circle id required');
      }
      if (empty($like_id)) {
        $this->_jsonErrorOutput('Like id required');
      }
      $resource = Engine_Api::_()->getItem('sitepage_page', $circle_id);
      $resource_id = $circle_id;
      $resource_type = 'sitepage_page';
      if (!$resource) {
        $this->_jsonErrorOutput('Circle not found');
      }


      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage')) {
        $sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;
      }

      //START PAGE MEMBER PLUGIN WORK
      if ($resource_type == 'sitepage_page' && $sitepageVersion >= '4.3.0p1') {
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
          Engine_Api::_()->sitepagemember()->joinLeave($resource, 'Leave');
        }
      }
      //END PAGE MEMBER PLUGIN WORK
      $notificationTable = Engine_Api::_()->getDbtable('notifications', 'activity');
      $db = $notificationTable->getAdapter();
      $db->beginTransaction();
      try {
        //START DELETE NOTIFICATION
        $notificationTable->delete(array('type = ?' => 'liked', 'subject_id = ?' => $viewer->getIdentity(), 'subject_type = ?' => $viewer->getType(), 'object_type = ?' => $resource_type, 'object_id = ?' => $resource_id));
        //END DELETE NOTIFICATION
        //START UNLIKE WORK.
        //HERE 'PAGE OR LIST PLUGIN' CHECK WHEN UNLIKE
        if (!empty($resource) && isset($resource->like_count)) {
          $resource->like_count--;
          $resource->save();
        }

        try {
          $contentTable = Engine_Api::_()->getDbTable('likes', 'core')->delete(array('like_id =?' => $like_id));
        } catch (Exception $ex) {
          $this->_jsonErrorOutput('Unable to remove');
        }
        //END UNLIKE WORK.
        //REMOVE LIKE ACTIVITY FEED.
        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike'))
          Engine_Api::_()->sitelike()->removeLikeFeed($viewer, $resource);
        $db->commit();
        $response = array();
        $response['response']['success'] = 'success';
        $response['response']['data'] = 'Successfully Unliked.';
        $this->_jsonSuccessOutput($response);
      } catch (Exception $ex) {
        $db->rollBack();
        $this->_jsonErrorOutput('An error occur');
      }
    }
  }

  public function followacricleAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());

      if (empty($circle_id)) {
        $this->_jsonErrorOutput('Circle id required');
      }
      $viewer_id = $viewer->getIdentity();
      $resource_id = $circle_id;
      $resource_type = 'sitepage_page';

      $manageAdminsIds = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdmin($resource_id, $viewer_id);

      //GET FOLLOW TABLE
      $followTable = Engine_Api::_()->getDbTable('follows', 'seaocore');
      $follow_name = $followTable->info('name');


      //GET OBJECT
      $resource = Engine_Api::_()->getItem($resource_type, $resource_id);

      //CHECKING IF USER HAS MAKING DUPLICATE ENTRY OF LIKING AN APPLICATION.
      $follow_id_temp = $resource->follows()->isFollow($viewer);

      if (empty($follow_id_temp)) {

        if (!empty($resource)) {
          $follow_id = $followTable->addFollow($resource, $viewer);
          if ($viewer_id != $resource->getOwner()->getIdentity()) {
            if ($resource_type == 'sitepage_page' || $resource_type == 'sitebusiness_business' || $resource_type == 'sitegroup_group') {
              foreach ($manageAdminsIds as $value) {
                $action_notification = unserialize($value['action_notification']);
                $user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
                //ADD NOTIFICATION
                if (!empty($value['notification']) && in_array('follow', $action_notification)) {
                  Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user_subject, $viewer, $resource, 'follow_' . $resource_type, array());
                }
              }
            }

            //ADD ACTIVITY FEED
            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            if ($resource_type != 'sitepage_page' || $resource_type != 'sitebusiness_business' || $resource_type == 'sitegroup_group') {
              $action = $activityApi->addActivity($viewer, $resource, 'follow_' . $resource_type, '', array(
                  'owner' => $resource->getOwner()->getGuid(),
              ));
            } else {
              $action = $activityApi->addActivity($viewer, $resource, 'follow_' . $resource_type);
            }

            $activityApi->attachActivity($action, $resource);
            $response = array();
            $response['response']['success'] = 'success';
            $response['response']['data'] = 'Successfully Followd';
            $this->_jsonSuccessOutput($response);
          } else {
            $this->_jsonErrorOutput('Follow not possible');
          }
        } else {
          $this->_jsonErrorOutput('Resource not found');
        }
        $follow_msg = $this->view->translate('Successfully Followd.');
      } else {
        $this->_jsonErrorOutput('Already Followd');
      }
    }
  }

  public function unfollowacricleAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());

      if (empty($circle_id)) {
        $this->_jsonErrorOutput('Circle id required');
      }
      $viewer_id = $viewer->getIdentity();
      $resource_id = $circle_id;
      $resource_type = 'sitepage_page';

      $manageAdminsIds = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdmin($resource_id, $viewer_id);

      //GET FOLLOW TABLE
      $followTable = Engine_Api::_()->getDbTable('follows', 'seaocore');
      $follow_name = $followTable->info('name');


      //GET OBJECT
      $resource = Engine_Api::_()->getItem($resource_type, $resource_id);

      if (!empty($resource)) {
        try {
          $followTable->removeFollow($resource, $viewer);
          if ($viewer_id != $resource->getOwner()->getIdentity()) {
            Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('object_type = ?' => "$resource_type", 'object_id = ?' => $resource_id, 'subject_id = ?' => $resource_id, 'subject_type = ?' => "$resource_type", 'user_id = ?' => $viewer_id));
            foreach ($manageAdminsIds as $value) {
              $user_subject = Engine_Api::_()->user()->getUser($value['user_id']);
              //DELETE NOTIFICATION
              $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($user_subject, $resource, 'follow_' . $resource_type);
              if ($notification) {
                $notification->delete();
              }
            }



            //DELETE ACTIVITY FEED
            $action_id = Engine_Api::_()->getDbtable('actions', 'activity')
                    ->select()
                    ->from('engine4_activity_actions', 'action_id')
                    ->where('type = ?', "follow_$resource_type")
                    ->where('subject_id = ?', $viewer_id)
                    ->where('subject_type = ?', 'user')
                    ->where('object_type = ?', $resource_type)
                    ->where('object_id = ?', $resource->getIdentity())
                    ->query()
                    ->fetchColumn();

            if (!empty($action_id)) {
              $activity = Engine_Api::_()->getItem('activity_action', $action_id);
              if (!empty($activity)) {
                $activity->delete();
              }
            }
            $response = array();
            $response['response']['success'] = 'success';
            $response['response']['data'] = 'Successfully Unfollowd';
            $this->_jsonSuccessOutput($response);
          } else {
            $this->_jsonErrorOutput('Unollow not possible');
          }
        } catch (Exception $ex) {
          $this->_jsonErrorOutput('No follow to remove');
        }
      } else {
        $this->_jsonErrorOutput('Resource not found');
      }
    }
  }

  public function joinacircleAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      $viewer_id = $viewer->getIdentity();
      if (empty($circle_id)) {
        $this->_jsonErrorOutput('Circle id required');
      }
      $page_id = (int) $circle_id;
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      if (!$sitepage) {
        $this->_jsonErrorOutput('Page not found');
      }
      $pagetitle = $sitepage->title;
      $page_url = Engine_Api::_()->sitepage()->getPageUrl($page_id);
      $page_baseurl = ((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => $page_url), 'sitepage_entry_view', true);
      $page_title_link = '<a href="' . $page_baseurl . '"  >' . $pagetitle . ' </a>';

      $hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id, $page_id);
      $pageJoinType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.join.type', null);

      //IF MEMBER IS ALREADY PART OF THE PAGE
      if (!empty($hasMembers)) {
        $this->_jsonErrorOutput('You have already sent a membership request.');
      }
      $sitepagemember = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id);
      $pagePhraseNum = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.phrase.num', null);

      //GET MANAGE ADMIN AND SEND NOTIFICATIONS TO ALL MANAGE ADMINS.
      $manageadmins = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdmin($page_id);

      if ($pageJoinType == $pagePhraseNum) {
        foreach ($manageadmins as $manageadmin) {
          $user_subject = Engine_Api::_()->user()->getUser($manageadmin['user_id']);

          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user_subject, $viewer, $sitepage, 'sitepagemember_approve');

          //Email to all page admins.
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($user_subject->email, 'SITEPAGEMEMBER_REQUEST_EMAIL', array(
              'page_title' => $pagetitle,
              'page_title_with_link' => $page_title_link,
              'object_link' => $page_baseurl,
              //'email' => $email,
              'queue' => true
          ));
        }

        $membersTable = Engine_Api::_()->getDbtable('membership', 'sitepage');

        $row = $membersTable->createRow();
        $row->resource_id = $page_id;
        $row->page_id = $page_id;
        $row->user_id = $viewer_id;
        $row->active = 0;
        $row->resource_approved = 0;
        $row->user_approved = 0;

        if (!empty($sitepagemember->featured) && $sitepagemember->featured == 1) {
          $row->featured = 1;
        }
        echo $row->member_id;
        exit;
        $row->save();
      }
      $this->_jsonErrorOutput('Your page membership request has been sent successfully.');
    }
  }

  public function leaveacircleAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());

      if (empty($circle_id)) {
        $this->_jsonErrorOutput('Circle id required');
      }
      $page_id = (int) $circle_id;
      $viewer_id = $viewer->getIdentity();
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      if (!$sitepage) {
        $this->_jsonErrorOutput('Page not found');
      }

      //DELETE THE RESULT FORM THE TABLE.
      Engine_Api::_()->getDbtable('membership', 'sitepage')->delete(array('resource_id =?' => $page_id, 'user_id = ?' => $viewer_id));

      //DELETE ACTIVITY FEED OF JOIN PAGE ACCORDING TO USER ID.
      $action_id = Engine_Api::_()->getDbtable('actions', 'activity')->fetchRow(array('type = ?' => 'sitepage_join', 'subject_id = ?' => $viewer_id, 'object_id = ?' => $page_id));
      $action = Engine_Api::_()->getItem('activity_action', $action_id->action_id);
      if (!empty($action)) {
        $action->delete();
      }
      //REMOVE THE NOTIFICATION.
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType($sitepage->getOwner(), $sitepage, 'sitepage_join');
      if ($notification) {
        $notification->delete();
      }
      //MEMBER COUNT DECREASE IN THE PAGE TABLE WHEN MEMBER LEAVE THE PAGE.
      $sitepage->member_count--;
      $sitepage->save();
      $this->_jsonErrorOutput('You have successfully left this page.');
    }
  }

  public function getmembersofacircleAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());

      if (empty($circle_id)) {
        $this->_jsonErrorOutput('Circle id required');
      }
      $sitepage = Engine_Api::_()->getItem('sitepage_page', (int) $circle_id);

      if (!$sitepage) {
        $this->_jsonErrorOutput('Page not found');
      }

      if (!Engine_Api::_()->sitepage()->allowInThisPage($sitepage, "sitepagemember", 'smecreate')) {
        $this->_jsonErrorOutput('Not allow to view');
      }
      //START MANAGE-ADMIN CHECK
      $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
      if (empty($isManageAdmin)) {
        $this->_jsonErrorOutput('Not allow to view');
      }

      $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
      $rolesTable = Engine_Api::_()->getDbtable('roles', 'sitepagemember');

      $rolesParams = array();
      $roleParamsArray = $rolesTable->rolesParams(array($sitepage->category_id), 0, $rolesParams, 1);
      $viewer_id = $viewer->getIdentity();
      $page_id = $sitepage->page_id;
      $memberJoinType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.join.type', null);
      $pageMemberPhraseNum = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.phrase.num', null);
//            if ($memberJoinType != $pageMemberPhraseNum) {
//                exit('this is an error');
//              }
      //CHECK IF USER IS JOIN THE PAGE OR NOT.
      $friendId = $viewer->membership()->getMembershipsOfIds();
      $select = $membershipTable->hasMembers($viewer_id, $sitepage->page_id);

      //TOTAL members
      $memberCount = Engine_Api::_()->sitepage()->getTotalCount($page_id, 'sitepage', 'membership');
      $can_edit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

      if (empty($memberCount) && empty($can_edit)) {
        $this->_jsonErrorOutput('Member not found');
      }
      $values = array();
      $values['page_id'] = $page_id;

      $paginator = $membershipTable->getSitepagemembersPaginator($values);
      $i = 0;
      $response = array();
      if ($paginator->getTotalItemCount() > 0) {
        foreach ($paginator as $sitepagemember) {
          $response['response']['data'][$i]['member_id'] = $sitepagemember->member_id;
          $response['response']['data'][$i]['name'] = $sitepagemember->getTitle();
          $response['response']['data'][$i]['profile_photo'] = $this->mybase64_encode(Engine_Api::_()->mgslapi()->getItemPhotoUrl($sitepagemember->getOwner()));
          $i++;
        }
      } else {
        $response['response']['data'] = 'No member found';
      }
      $response['response']['total'] = $paginator->getTotalItemCount();
      $response['response']['hash'] = md5(serialize($response));
      //echo '<pre>';            print_r($response); exit;
      $this->_jsonSuccessOutput($response);
    }
  }

  public function geteventofacircleAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());

      if (empty($circle_id)) {
        $this->_jsonErrorOutput('Circle id required');
      }
      $sitepage_subject = Engine_Api::_()->getItem('sitepage_page', (int) $circle_id);

      //GET PAGE ID
      $page_id = $sitepage_subject->page_id;

      //PACKAGE BASE PRIYACY START
      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage_subject->package_id, "modules", "sitepageevent")) {
          $this->_jsonErrorOutput('Package not enable');
        }
      } else {
        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage_subject, 'secreate');
        if (empty($isPageOwnerAllow)) {
          $this->_jsonErrorOutput('Not allow to view');
        }
      }
      //PACKAGE BASE PRIYACY END              
      $values = array();
      $values['page_id'] = $circle_id;
      $values['show_event'] = 0;
      $values['clicked'] = 'upcomingevent';
      $values['orderby'] = 'starttime';
      $paginator = Engine_Api::_()->getDbTable('events', 'sitepageevent')->getSitepageeventsPaginator($values);
      $paginator->setItemCountPerPage(10);
      $i = 0;
      $response = array();
      if ($paginator->getTotalItemCount() > 0) {
        foreach ($paginator as $sitepageevent) {
          $response['response']['data'][$i]['id'] = $sitepageevent->event_id;
          $response['response']['data'][$i]['title'] = $sitepageevent->title;
          $response['response']['data'][$i]['image'] = $this->mybase64_encode(Engine_Api::_()->mgslapi()->getItemPhotoUrl($sitepageevent));
          $response['response']['data'][$i]['location'] = $sitepageevent->location ? $sitepageevent->location : '';
          $response['response']['data'][$i]['starttime'] = $this->view->locale()->toDateTime($sitepageevent->starttime);
          $i++;
        }
      } else {
        $response['response']['data'] = 'No event found';
      }
      $response['response']['total'] = $paginator->getTotalItemCount();
      $response['response']['hash'] = md5(serialize($response));
      //echo '<pre>';            print_r($response); exit;
      $this->_jsonSuccessOutput($response);
    }
  }

  public function getcirclefeedAction() {
    if ($viewer = $this->checkAuth()) {
      $request = Zend_Controller_Front::getInstance()->getRequest();
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      extract($this->getRequest()->getPost());
      $subject = null;
      if (empty($circle_id)) {
        $this->_jsonErrorOutput('Circle id required');
      }
      if ($circle_id) {
        $subject = Engine_Api::_()->getItem('sitepage_page', (int) $circle_id);
        if (!$subject->page_id)
          $this->_jsonErrorOutput('Page not found');
      }
      $data = $this->allfeed($viewer, $subject);
      $html = $this->view->partial(
              '_view_circle_feed.tpl', $this->_currentModuleCoreApi->getModuleName(), $data
      );

      $response = array();

      if (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== FALSE) {
        echo $html;
        exit;
      }
      //echo $html ; exit;
      $response['response']['body'] = $html;
      $response['response']['hash'] = md5(serialize($response));
      $this->_jsonSuccessOutput($response);
    }
  }

  public function getallbadgecountAction() {
    if ($viewer = $this->checkAuth()) {
      $total_unread_messages = $total_unseen_notification = $total_friend_request = 0;

      //code for unread message
      $conversation_table = Engine_Api::_()->getItemTable('messages_conversation');
      $messages = $conversation_table->fetchAll($conversation_table->getInboxSelect($viewer));
      if (count($messages) > 0) {
        foreach ($messages as $message) {
          $recipient = $message->getRecipientInfo($viewer);
          if (!$recipient->inbox_read)
            $total_unread_messages++;
        }
      }
      //end code for unread message
      //code for unseen notification
      $total_unseen_notification = Engine_Api::_()->getDbtable('notifications', 'activity')->hasNotifications($viewer);
      //end code for unseen notification            
      //code for friend request
      $requests = Engine_Api::_()->getDbtable('notifications', 'activity')->getRequestsPaginator($viewer);
      $total_friend_request = $requests->getTotalItemCount();
      //end code for friend request            

      $response = array();
      $response['response']['total_unread_messages'] = $total_unread_messages;
      $response['response']['total_unseen_notification'] = $total_unseen_notification;
      $response['response']['total_friend_request'] = $total_friend_request;
      $response['response']['hash'] = md5(serialize($response));
      $this->_jsonSuccessOutput($response);
    }
  }

  public function clearbadgecountAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      if (!in_array($type, array('friends', 'notification', 'message')))
        $this->_jsonErrorOutput('Unknown type');

      $response = array();
      if ($type == 'notification') {
        Engine_Api::_()->getDbtable('notifications', 'activity')->markNotificationsAsRead($viewer);
        $response['response']['data'] = 'All notification clear';
      } elseif ($type == 'message') {
        $conversation_table = Engine_Api::_()->getItemTable('messages_conversation');
        $messages = $conversation_table->fetchAll($conversation_table->getInboxSelect($viewer));
        if (count($messages) > 0) {
          foreach ($messages as $message) {
            $recipient = $message->getRecipientInfo($viewer);
            if (!$recipient->inbox_read) {
              $conversation = Engine_Api::_()->getItem('messages_conversation', $message->conversation_id);
              $conversation->setAsRead($viewer);
            };
          }
        }
        $response['response']['data'] = 'All message read';
      } elseif ($type == 'friends') {
        $response['response']['data'] = 'What will happen';
      }
      $response['response']['success'] = 'success';
      $this->_jsonSuccessOutput($response);
    }
  }

  public function getallunreadmessageslistAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      $conversation_table = Engine_Api::_()->getItemTable('messages_conversation');
      $select = $conversation_table->getInboxSelect($viewer);
      if ($page == '-1') {
        $conversations = $conversation_table->fetchAll($select);
        $total = count($conversations);
      } else {
        $conversations = Zend_Paginator::factory($select);
        $conversations->setItemCountPerPage(10);
        $conversations->setCurrentPageNumber($page ? (int) $page : 1);
        $total = $conversations->getTotalItemCount();
      }
      $dataFound = 0;
      $i = 0;
      $response = array();
      if ($total > 0) {
        $response['response']['success'] = 'success';
        foreach ($conversations as $conversation) {
          $message = $conversation->getInboxMessage($viewer);
          $recipient = $conversation->getRecipientInfo($viewer);
          if ($recipient->inbox_read)
            continue;
          $resource = "";
          $sender = "";
          if ($conversation->hasResource() &&
                  ($resource = $conversation->getResource())) {
            $sender = $resource;
          } else if ($conversation->recipients > 1) {
            $sender = $viewer;
          } else {
            foreach ($conversation->getRecipients() as $tmpUser) {
              if ($tmpUser->getIdentity() != $viewer->getIdentity()) {
                $sender = $tmpUser;
              }
            }
          }
          if ((!isset($sender) || !$sender) && $viewer->getIdentity() !== $conversation->user_id) {
            $sender = Engine_Api::_()->user()->getUser($conversation->user_id);
          }
          if (!isset($sender) || !$sender) {
            //continue;
            $sender = new User_Model_User(array());
          }

          // code for title
          !( isset($message) && '' != ($title = trim($message->getTitle())) || !isset($conversation) && '' != ($title = trim($conversation->getTitle())) || $title = '<em>' . $this->view->translate('(No Subject)') . '</em>' );

          //code for sender
          if (!empty($resource)):
            $sender_name = $resource->toString();
          elseif ($conversation->recipients == 1):
            $sender_name = $sender->getTitle();
          else:
            $sender_name = $this->view->translate(array('%s person', '%s people', $conversation->recipients), $this->view->locale()->toNumber($conversation->recipients));
          endif;
          //end code for sender

          $imageURL = $this->mybase64_encode(Engine_Api::_()->mgslapi()->getItemPhotoUrl($sender));
          $response['response']['data'][$i]['id'] = $message->conversation_id;
          $response['response']['data'][$i]['sender'] = $sender_name;
          $response['response']['data'][$i]['sender_image'] = $imageURL;
          $response['response']['data'][$i]['title'] = strip_tags($title);
          $response['response']['data'][$i]['message'] = html_entity_decode($message->body);
          $response['response']['data'][$i]['date'] = $this->view->customtimestamp($message->date);
          $i++;
          $dataFound = 1;
        }
        if (!$dataFound) {
          $response['response']['data'] = 'No unread message found';
        }
      } else {
        $response['response']['data'] = 'No unread message found';
      }
      //$response['response']['total'] = $total;
      $response['response']['hash'] = md5(serialize($response));
      $this->_jsonSuccessOutput($response);
    }
  }

  public function getallunseennotificationAction() {
    if ($viewer = $this->checkAuth()) {
      $notifications = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationsPaginator($viewer);
      $notifications->setItemCountPerPage(100);
      $total = $notifications->getTotalItemCount();
      $i = 0;
      $notFound = 0;
      $response = array();
      if ($total > 0) {
        $response['response']['success'] = 'success';
        foreach ($notifications as $notification) {
          if ($notification->read)
            continue;
          $notFound++;
          $sender = Engine_Api::_()->getItem($notification->subject_type, $notification->subject_id);
          $response['response']['data'][$i]['id'] = $notification->notification_id;
          $response['response']['data'][$i]['notification_description'] = strip_tags($notification->__toString());
          $response['response']['data'][$i]['profile_name'] = $sender->getTitle();
          $response['response']['data'][$i]['image'] = $this->mybase64_encode(Engine_Api::_()->mgslapi()->getItemPhotoUrl($sender));
          $response['response']['data'][$i]['date'] = $this->view->customtimestamp($notification->date);
          $i++;
        }
        if (!$notFound)
          $response['response']['data'] = 'No new notification found';
      }
      else {
        $response['response']['data'] = 'No new notification found';
      }
      $response['response']['hash'] = md5(serialize($response));
      //echo '<pre>';            print_r($response); exit;
      $this->_jsonSuccessOutput($response);
    }
  }

  public function readnotificationAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      $id = (int) $notification_id;
      if (empty($id)) {
        $this->_jsonErrorOutput('Notification id required');
      }

      $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
      $db = $notificationsTable->getAdapter();
      $db->beginTransaction();

      try {
        $notification = Engine_Api::_()->getItem('activity_notification', $id);
        if ($notification) {
          $notification->read = 1;
          $notification->save();

          $response['response']['success'] = 'success';
          $response['response']['data'] = 'Notification readed';
        } else {
          $this->_jsonErrorOutput('No notification found');
        }
        // Commit
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        //throw $e;
        $this->_jsonErrorOutput('Notification read error');
      }
      $response['response']['hash'] = md5(serialize($response));
      $this->_jsonSuccessOutput($response);
    }
  }

  public function getallunseenfriendrequestsAction() {
    if ($viewer = $this->checkAuth()) {
      $requests = Engine_Api::_()->getDbtable('notifications', 'activity')->getRequestsPaginator($viewer);
      $total = $requests->getTotalItemCount();
      $requests->setItemCountPerPage(500);
      $i = 0;
      $response = array();
      if ($total > 0) {
        $response['response']['success'] = 'success';
        foreach ($requests as $request) {
          $sender = Engine_Api::_()->getItem('user', $request->subject_id);
          $response['response']['data'][$i]['id'] = $request->notification_id;
          $response['response']['data'][$i]['sender_name'] = $sender->getTitle();
          $response['response']['data'][$i]['sender_image'] = $this->mybase64_encode(Engine_Api::_()->mgslapi()->getItemPhotoUrl($sender));
          $response['response']['data'][$i]['message'] = $sender->getTitle() . ' has sent you a friend request.';
          $response['response']['data'][$i]['date'] = $this->view->customtimestamp($request->date);
          $i++;
        }
      } else {
        $response['response']['data'] = 'No friend request found';
      }
      //$response['response']['total'] = $total;
      $response['response']['hash'] = md5(serialize($response));
      $this->_jsonSuccessOutput($response);
    }
  }

//  public function acceptfriendrequestAction() {
//    if ($viewer = $this->checkAuth()) {
//      extract($this->getRequest()->getPost());
//      if (empty($user_id)) {
//        $this->_jsonErrorOutput('User id required');
//      }
//      if (null == ($user = Engine_Api::_()->getItem('user', $user_id))) {
//        $this->_jsonErrorOutput('No member specified');
//        return;
//      }
//
//      $friendship = $viewer->membership()->getRow($user);
//      if ($friendship->active) {
//        $this->_jsonErrorOutput('Already friends');
//        return;
//      }
//      // Process
//      $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
//      $db->beginTransaction();
//      try {
//        $viewer->membership()->setResourceApproved($user);
//
//        // Add activity
//        if (!$user->membership()->isReciprocal()) {
//          Engine_Api::_()->getDbtable('actions', 'activity')
//                  ->addActivity($user, $viewer, 'friends_follow', '{item:$subject} is now following {item:$object}.');
//        } else {
//          Engine_Api::_()->getDbtable('actions', 'activity')
//                  ->addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.');
//          Engine_Api::_()->getDbtable('actions', 'activity')
//                  ->addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.');
//        }
//
//        // Add notification
//        if (!$user->membership()->isReciprocal()) {
//          Engine_Api::_()->getDbtable('notifications', 'activity')
//                  ->addNotification($user, $viewer, $user, 'friend_follow_accepted');
//        } else {
//          Engine_Api::_()->getDbtable('notifications', 'activity')
//                  ->addNotification($user, $viewer, $user, 'friend_accepted');
//        }
//
//        // Set the requests as handled
//        $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
//                ->getNotificationBySubjectAndType($viewer, $user, 'friend_request');
//        if ($notification) {
//          $notification->mitigated = true;
//          $notification->read = 1;
//          $notification->save();
//        }
//        $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
//                ->getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
//        if ($notification) {
//          $notification->mitigated = true;
//          $notification->read = 1;
//          $notification->save();
//        }
//
//        // Increment friends counter
//        Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.friendships');
//
//        $db->commit();
//
//        $message = Zend_Registry::get('Zend_Translate')->_('You are now friends with %s');
//        $message = sprintf($message, $user->getTitle());
//
//
//        $response = array();
//        $response['response']['success'] = $message;
//        $this->_jsonSuccessOutput($response);
//      } catch (Exception $e) {
//        $db->rollBack();
//        $this->_jsonErrorOutput('An error has occurred.');
//      }
//    }
//  }

  public function denyfriendrequestAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      if (empty($user_id)) {
        $this->_jsonErrorOutput('User id required');
      }
      if (null == ($user = Engine_Api::_()->getItem('user', $user_id))) {
        $this->_jsonErrorOutput('No member specified');
        return;
      }

      // Process
      $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
      $db->beginTransaction();

      try {
        $user->membership()->removeMember($viewer);

        // Remove from lists?
        // @todo make sure this works with one-way friendships
        $user->lists()->removeFriendFromLists($viewer);
        $viewer->lists()->removeFriendFromLists($user);

        // Set the requests as handled
        $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                ->getNotificationBySubjectAndType($user, $viewer, 'friend_request');
        if ($notification) {
          $notification->mitigated = true;
          $notification->read = 1;
          $notification->save();
        }
        $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
                ->getNotificationBySubjectAndType($viewer, $user, 'friend_follow_request');
        if ($notification) {
          $notification->mitigated = true;
          $notification->read = 1;
          $notification->save();
        }

        $db->commit();

        $message = Zend_Registry::get('Zend_Translate')->_('This person has been removed from your friends.');
        $response = array();
        $response['response']['success'] = $message;
        $this->_jsonSuccessOutput($response);
      } catch (Exception $e) {
        $db->rollBack();
        $this->_jsonErrorOutput('An error has occurred.');
      }
    }
  }

  public function shareafeedAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      if (empty($id)) {
        $this->_jsonErrorOutput('ID required');
      }
      if (!(in_array($type, array('group', 'video', 'event', 'album_photo', 'activity_action')))) {
        $this->_jsonErrorOutput('Undefined type');
      }

      $attachment = Engine_Api::_()->getItem($type, $id);
      if (!$attachment) {
        $this->_jsonErrorOutput('You cannot share this item because it has been removed.');
      }

      // Process

      $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
      $db->beginTransaction();

      try {
        // Set Params for Attachment
        $params = array(
            'type' => '<a href="' . $attachment->getHref() . '">' . $attachment->getMediaType() . '</a>',
        );

        // Add activity
        $api = Engine_Api::_()->getDbtable('actions', 'activity');
        //$action = $api->addActivity($viewer, $viewer, 'post_self', $body);
        $action = $api->addActivity($viewer, $attachment->getOwner(), 'share', $body, $params);
        if ($action) {
          $api->attachActivity($action, $attachment);
        }
        $db->commit();

        // Notifications
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        // Add notification for owner of activity (if user and not viewer)
        if ($action->subject_type == 'user' && $attachment->getOwner()->getIdentity() != $viewer->getIdentity()) {
          $notifyApi->addNotification($attachment->getOwner(), $viewer, $action, 'shared', array(
              'label' => $attachment->getMediaType(),
          ));
        }

        // Preprocess attachment parameters
        $publishMessage = html_entity_decode($body);
        $publishUrl = null;
        $publishName = null;
        $publishDesc = null;
        $publishPicUrl = null;
        // Add attachment
        if ($attachment) {
          $publishUrl = $attachment->getHref();
          $publishName = $attachment->getTitle();
          $publishDesc = $attachment->getDescription();
          if (empty($publishName)) {
            $publishName = ucwords($attachment->getShortType());
          }
          if (($tmpPicUrl = $attachment->getPhotoUrl())) {
            $publishPicUrl = $tmpPicUrl;
          }
          // prevents OAuthException: (#100) FBCDN image is not allowed in stream
          if ($publishPicUrl &&
                  preg_match('/fbcdn.net$/i', parse_url($publishPicUrl, PHP_URL_HOST))) {
            $publishPicUrl = null;
          }
        } else {
          $publishUrl = $action->getHref();
        }
        // Check to ensure proto/host
        if ($publishUrl &&
                false === stripos($publishUrl, 'http://') &&
                false === stripos($publishUrl, 'https://')) {
          $publishUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishUrl;
        }
        if ($publishPicUrl &&
                false === stripos($publishPicUrl, 'http://') &&
                false === stripos($publishPicUrl, 'https://')) {
          $publishPicUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishPicUrl;
        }
        // Add site title
        if ($publishName) {
          $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title
                  . ": " . $publishName;
        } else {
          $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
        }


        // Publish to facebook, if checked & enabled
        if ($this->_getParam('post_to_facebook', false) &&
                'publish' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable) {
          try {

            $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
            $facebookApi = $facebook = $facebookTable->getApi();
            $fb_uid = $facebookTable->find($viewer->getIdentity())->current();

            if ($fb_uid &&
                    $fb_uid->facebook_uid &&
                    $facebookApi &&
                    $facebookApi->getUser() &&
                    $facebookApi->getUser() == $fb_uid->facebook_uid) {
              $fb_data = array(
                  'message' => $publishMessage,
              );
              if ($publishUrl) {
                $fb_data['link'] = $publishUrl;
              }
              if ($publishName) {
                $fb_data['name'] = $publishName;
              }
              if ($publishDesc) {
                $fb_data['description'] = $publishDesc;
              }
              if ($publishPicUrl) {
                $fb_data['picture'] = $publishPicUrl;
              }
              $res = $facebookApi->api('/me/feed', 'POST', $fb_data);
            }
          } catch (Exception $e) {
            // Silence
          }
        } // end Facebook
        // Publish to twitter, if checked & enabled
        if ($this->_getParam('post_to_twitter', false) &&
                'publish' == Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable) {
          try {
            $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
            if ($twitterTable->isConnected()) {

              // Get attachment info
              $title = $attachment->getTitle();
              $url = $attachment->getHref();
              $picUrl = $attachment->getPhotoUrl();

              // Check stuff
              if ($url && false === stripos($url, 'http://')) {
                $url = 'http://' . $_SERVER['HTTP_HOST'] . $url;
              }
              if ($picUrl && false === stripos($picUrl, 'http://')) {
                $picUrl = 'http://' . $_SERVER['HTTP_HOST'] . $picUrl;
              }

              // Try to keep full message
              // @todo url shortener?
              $message = html_entity_decode($form->getValue('body'));
              if (strlen($message) + strlen($title) + strlen($url) + strlen($picUrl) + 9 <= 140) {
                if ($title) {
                  $message .= ' - ' . $title;
                }
                if ($url) {
                  $message .= ' - ' . $url;
                }
                if ($picUrl) {
                  $message .= ' - ' . $picUrl;
                }
              } else if (strlen($message) + strlen($title) + strlen($url) + 6 <= 140) {
                if ($title) {
                  $message .= ' - ' . $title;
                }
                if ($url) {
                  $message .= ' - ' . $url;
                }
              } else {
                if (strlen($title) > 24) {
                  $title = Engine_String::substr($title, 0, 21) . '...';
                }
                // Sigh truncate I guess
                if (strlen($message) + strlen($title) + strlen($url) + 9 > 140) {
                  $message = Engine_String::substr($message, 0, 140 - (strlen($title) + strlen($url) + 9)) - 3 . '...';
                }
                if ($title) {
                  $message .= ' - ' . $title;
                }
                if ($url) {
                  $message .= ' - ' . $url;
                }
              }

              $twitter = $twitterTable->getApi();
              $twitter->statuses->update($message);
            }
          } catch (Exception $e) {
            // Silence
          }
        }


        // Publish to janrain
        if (//$this->_getParam('post_to_janrain', false) &&
                'publish' == Engine_Api::_()->getApi('settings', 'core')->core_janrain_enable) {
          try {
            $session = new Zend_Session_Namespace('JanrainActivity');
            $session->unsetAll();

            $session->message = $publishMessage;
            $session->url = $publishUrl ? $publishUrl : 'http://' . $_SERVER['HTTP_HOST'] . _ENGINE_R_BASE;
            $session->name = $publishName;
            $session->desc = $publishDesc;
            $session->picture = $publishPicUrl;
          } catch (Exception $e) {
            // Silence
          }
        }
        $response = array();
        $response['response']['success'] = 'Success!';
        $this->_jsonSuccessOutput($response);
      } catch (Exception $e) {
        $db->rollBack();
        //throw $e; // This should be caught by error handler
        $this->_jsonErrorOutput('An error has occurred.');
      }
    }
  }

  public function reportafeedAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      if (empty($id)) {
        $this->_jsonErrorOutput('ID required');
      }
      if (!(in_array($type, array('group', 'video', 'event', 'album_photo', 'activity_action')))) {
        $this->_jsonErrorOutput('Undefined type');
      }

      if (!(in_array($category, array('spam', 'abuse', 'licensed', 'inappropriate', 'other')))) {
        $this->_jsonErrorOutput('Undefined report type');
      }

      if (empty($description)) {
        $this->_jsonErrorOutput('Description required');
      }
      $subject = Engine_Api::_()->getItem($type, $id);
      if (!$subject->action_id) {
        $this->_jsonErrorOutput('Invalid subject');
      }
      // Process
      $advancedactivityPluginEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity');
      if (!$advancedactivityPluginEnable) {
        $this->_jsonErrorOutput('Advance activity module not anable');
      }
      $table = Engine_Api::_()->getItemTable('advancedactivity_report');
      $db = $table->getAdapter();
      $db->beginTransaction();

      try {
        $report = $table->createRow();
        $report->setFromArray(array(
            'category' => $category,
            'description' => trim(strip_tags($description)),
            'action_id' => $subject->getIdentity(),
            'user_id' => $viewer->getIdentity(),
        ));
        $report->save();

        $db->commit();
        $response = array();
        $response['response']['success'] = 'Your report has been submitted.';
        $this->_jsonSuccessOutput($response);
      } catch (Exception $e) {
        $db->rollBack();
        //throw $e;
        $this->_jsonErrorOutput('An error has occurred.');
      }
    }
  }

  public function deleteafeedAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      if (empty($feed_id)) {
        $this->_jsonErrorOutput('Feed id required');
      }

      $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');

      $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionById($feed_id);

      if (!$action) {
        $this->_jsonErrorOutput('You cannot delete this item because it has been removed.');
      }

      // Both the author and the person being written about get to delete the action_id
      if ($activity_moderate || ('user' == $action->subject_type && $viewer->getIdentity() == $action->subject_id) || // owner of profile being commented on
              ('user' == $action->object_type && $viewer->getIdentity() == $action->object_id)) {   // commenter
        // Delete action item and all comments/likes
        $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
        $db->beginTransaction();
        try {
          $action->deleteItem();
          $db->commit();
          $response = array();
          $response['response']['success'] = 'This activity item has been removed.';
          $this->_jsonSuccessOutput($response);
        } catch (Exception $e) {
          $db->rollback();
          $this->_jsonErrorOutput('An error has occurred.');
        }
      } else {
        $this->_jsonErrorOutput('You do not have the privilege to delete this feed');
      }
    }
  }

  public function getnearestlocationsofauserAction() {
    if ($viewer = $this->checkAuth()) {
      extract($this->getRequest()->getPost());

      $coreModule = Engine_Api::_()->getDbtable('modules', 'core');
      $data = array();

      $text = $this->_getParam('query_string', null);

      //INITIALISE SUGGESION
      $initial_suggestion = 0;

      //FOR FIRST TIME IF THERE IS NO TEXT 
      if (empty($text)) {
        //$text = $this->_getParam('location_detected', null);
        $initial_suggestion = 1;

        $latitude = $this->_getParam('latitude', 0);

        //GET LONGITUDE
        $longitude = $this->_getParam('longitude', 0);

        $text = $this->getaddress($latitude, $longitude);
      }
      //CHECK SITEPAGE IS ENABLED OR NOT
      $sitepageEnabled = $coreModule->isModuleEnabled('sitepage');
      $settings = Engine_Api::_()->getApi('settings', 'core');

      //CHECK SITEBUSIENSS IS ENABLED OR NOT
      $sitebusinessEnabled = $coreModule->isModuleEnabled('sitebusiness');
      $sitegroupEnabled = $coreModule->isModuleEnabled('sitegroup');
      $sitestoreEnabled = $coreModule->isModuleEnabled('sitestore');

      if (null !== $text) {
        //GET LATITUDE
        //$latitude = $this->_getParam('latitude', 0);
        //GET LONGITUDE
        //$longitude = $this->_getParam('longitude', 0);
        //COUNT
        $count = 0;

        //GET SITETAGCHECKIN API
        $apiSitetagcheckin = Engine_Api::_()->sitetagcheckin();

        //INITIALISE RESOURCE PAGE IDS
        $resourcePageIds = '';
        //INITIALISE PREVIOUS PAGE DATA
        $previousPageData = array();
        //INITIALISE PAGE DATA
        $pageData = array();
        //INITIALISE PAGE FlAG
        $pageFlag = 1;

        //INITIALISE RESOURCE BUSINESS IDS
        $resourceBusinessIds = '';
        //INITIALISE PREVIOUS BUSINESS DATA
        $previousBusinessData = array();
        //INITIALISE BUSINESS DATA
        $businessData = array();
        //INITIALISE BUSINESS FlAG
        $businessFlag = 1;

        //INITIALISE RESOURCE GROUP IDS
        $resourceGroupIds = '';
        //INITIALISE PREVIOUS GROUP DATA
        $previousGroupData = array();
        //INITIALISE GROUP DATA
        $groupData = array();
        //INITIALISE GROUP FlAG
        $groupFlag = 1;

        //INITIALISE RESOURCE STORE IDS
        $resourceStoreIds = '';
        //INITIALISE PREVIOUS GROUP DATA
        $previousStoreData = array();
        //INITIALISE GROUP DATA
        $storeData = array();
        //INITIALISE GROUP FlAG
        $storeFlag = 1;

        //INITIALISE PREVIOUS PLACES
        $previousPlaces = array();

        $tagged_location = $settings->getSetting('sitetagcheckin.tagged.location', 1);

        //SHOW SELECTABLE CONETNT
        $showSelectableContents = $settings->getSetting('sitetagcheckin.selectable', '');

        //INITIALISE GOOGLE PLACE FlAG
        $googleplacesFlag = 1;

        //IF ADMIN HAS SET TO DISPLAY THE PAGES / BUSINESSES / GOOGLE PLACES TO SHOW IN THE AUTOSUGGEST
        if (!empty($showSelectableContents)) {
          if (!in_array('pages', $showSelectableContents)) {
            $pageFlag = 0;
          }
          if (!in_array('businesses', $showSelectableContents)) {
            $businessFlag = 0;
          }
          if (!in_array('groups', $showSelectableContents)) {
            $groupFlag = 0;
          }
          if (!in_array('stores', $showSelectableContents)) {
            $storeFlag = 0;
          }
          if (!in_array('googleplaces', $showSelectableContents)) {
            $googleplacesFlag = 0;
          }
        }
        //CHECK INITIALISE SUGGESION AND ALSO IF HE WAT TO SAVE THE PREVIOUS CHECKIN LOCATIONS
        if ($initial_suggestion == 1 && $tagged_location) {

          //GET PREVIOUS GOOGLE PLACES
          if (!empty($googleplacesFlag)) {
            $previousGooglePlacesResults = $apiSitetagcheckin->getPreviousGooglePlacesResults();
          }

          //SITEPAGEENALEB THEN GETITNG THE PREVIOUS SUGGEST CONTENT
          if ($sitepageEnabled && $pageFlag) {
            $previousPageResult = $apiSitetagcheckin->getPreviousSuggestContent($text, 'sitepage_page');
            foreach ($previousPageResult as $pageResult) {
              $pageResult['id'] = 'sitetagcheckin_' . $count++;
              $resourcePageIds .= $pageResult['resource_id'] . ',';
              $previousPageData[] = $pageResult;
            }
          }

          //SITEBUSINESSENALEB THEN GETITNG THE PREVIOUS SUGGEST CONTENT
          if ($sitebusinessEnabled && $businessFlag) {
            $previousBusinessResult = $apiSitetagcheckin->getPreviousSuggestContent($text, 'sitebusiness_business');
            foreach ($previousBusinessResult as $businessResult) {
              $businessResult['id'] = 'sitetagcheckin_' . $count++;
              $resourceBusinessIds .= $businessResult['resource_id'] . ',';
              $previousBusinessData[] = $businessResult;
            }
          }

          //SITEGROUPENALEB THEN GETITNG THE PREVIOUS SUGGEST CONTENT
          if ($sitegroupEnabled && $groupFlag) {
            $previousGroupResult = $apiSitetagcheckin->getPreviousSuggestContent($text, 'sitegroup_group');
            foreach ($previousGroupResult as $groupResult) {
              $groupResult['id'] = 'sitetagcheckin_' . $count++;
              $resourceGroupIds .= $groupResult['resource_id'] . ',';
              $previousGroupData[] = $groupResult;
            }
          }

          //SITEGROUPENALEB THEN GETITNG THE PREVIOUS SUGGEST CONTENT
          if ($sitestoreEnabled && $storeFlag) {
            $previousStoreResult = $apiSitetagcheckin->getPreviousSuggestContent($text, 'sitestore_store');
            foreach ($previousStoreResult as $stroeResult) {
              $stroeResult['id'] = 'sitetagcheckin_' . $count++;
              $resourceStoreIds .= $stroeResult['resource_id'] . ',';
              $previousStoreData[] = $stroeResult;
            }
          }

          //MAKE PREVIOUS GOOGLE PLACE RESULTS ARRAY
          foreach ($previousGooglePlacesResults as $previousGooglePalces) {
            $previousGooglePalces['id'] = 'sitetagcheckin_' . $count++;
            $previousGooglePalces['type'] = 'place';
            $previousGooglePalces['prefixadd'] = 'in';
            $previousGooglePalces['photo'] = '<img class="thumb_icon item_photo_user" alt="" src="application/modules/Sitetagcheckin/externals/images/map_icon.png" />';
            $previousPlaces[] = $previousGooglePalces;
          }
        }

        //MAKE PAGE ARRAY
        if (!empty($pageFlag) && $sitepageEnabled) {
          $pageResult = $apiSitetagcheckin->getSuggestContent($text, 'sitepage_page', $resourcePageIds);


          foreach ($pageResult as $page) {
            $page['id'] = 'sitetagcheckin_' . $count++;
            $pageData[] = $page;
          }

          if (!empty($previousPageData)) {
            $pageData = array_merge($previousPageData, $pageData);
          }
        }


        //MAKE BUSINESS ARRAY
        if (!empty($businessFlag) && $sitebusinessEnabled) {
          $businessResult = $apiSitetagcheckin->getSuggestContent($text, 'sitebusiness_business', $resourceBusinessIds);
          foreach ($businessResult as $business) {
            $business['id'] = 'sitetagcheckin_' . $count++;
            $businessData[] = $business;
          }

          if (!empty($previousBusinessData)) {
            $businessData = array_merge($previousBusinessData, $businessData);
          }
        }

        //MAKE GROUP ARRAY
        if (!empty($groupFlag) && $sitegroupEnabled) {
          $groupResult = $apiSitetagcheckin->getSuggestContent($text, 'sitegroup_group', $resourceGroupIds);
          foreach ($groupResult as $group) {
            $group['id'] = 'sitetagcheckin_' . $count++;
            $groupData[] = $group;
          }

          if (!empty($previousGroupData)) {
            $groupData = array_merge($previousGroupData, $groupData);
          }
        }

        //MAKE STORE ARRAY
        if (!empty($storeFlag) && $sitestoreEnabled) {
          $storeResult = $apiSitetagcheckin->getSuggestContent($text, 'sitestore_store', $resourceStoreIds);
          foreach ($storeResult as $store) {
            $store['id'] = 'sitetagcheckin_' . $count++;
            $storeData[] = $store;
          }

          if (!empty($previousStoreData)) {
            $storeData = array_merge($previousStoreData, $storeData);
          }
        }

        //MAKE GOOGLE PLACE ARRAY
        if (!empty($googleplacesFlag)) {
          //$suggestGooglePalces = $apiSitetagcheckin->getSuggestGooglePalces($text, $latitude, $longitude);
          $suggestGooglePalces = Engine_Api::_()->mgslapi()->getSuggestGooglePalces($text, $latitude, $longitude);
          foreach ($suggestGooglePalces as $key => $palces) {
            if (!empty($previousGooglePlacesResults)) {
              foreach ($previousGooglePlacesResults as $previousGooglePlaces) {
                if (isset($palces['label']) && isset($previousGooglePlaces['label']) && $palces['label'] == $previousGooglePlaces['label']) {
                  unset($suggestGooglePalces[$key]);
                }
              }
            }
          }

          foreach ($suggestGooglePalces as $key => $palces) {
            $palces['id'] = 'sitetagcheckin_' . $count++;
            $palces['type'] = 'place';
            $palces['prefixadd'] = 'in';
            $palces['photo'] = '<img class="thumb_icon item_photo_user" alt="" src="application/modules/Sitetagcheckin/externals/images/map_icon.png" />';
            $previousPlaces[] = $palces;
          }
        }

        //MAKE PAGE AND BUSINESS DATA
        $data = array_merge($pageData, $businessData);

        $data = array_merge($data, $groupData);

        $data = array_merge($data, $storeData);

        //MAKE FINAL DATA
        $data = array_merge($data, $previousPlaces);


        //IF JUST USE BY ADMIN FOR LOCAITON THEN MAKE THE ARRAY FOR JUST USE
        $text = $this->_getParam('suggest', null);
        if (!empty($text)) {
          $data[] = array("id" => 'just_use_li', 'type' => 'just_use', 'label' => $text, 'prefixadd' => $this->view->translate('at'), 'latitude' => 0, 'longitude' => 0, 'li_html' => $this->view->translate('Just use') . ' "' . $text . '"', 'google_id' => 1);
        }
      }

      $i = 0;
      $response = array();


      if (count($data) > 0) {
        $response['response']['success'] = 'success';
        foreach ($data as $location) {
          $location_string = '';
          $location_string .= "resource_guid=" . str_replace("+", "%20", urlencode($location['resource_guid']));

          $location_string .= "&google_id=" . str_replace("+", "%20", urlencode($location['google_id']));

          $location_string .= "&label=" . str_replace("+", "%20", urlencode($location['label']));

          $location_string .= "&reference=" . str_replace("+", "%20", urlencode($location['reference']));

          $location_string .= "&id=" . str_replace("+", "%20", urlencode($location['id']));

          $location_string .= "&type=" . str_replace("+", "%20", urlencode($location['type']));

          $location_string .= "&prefixadd=" . str_replace("+", "%20", urlencode($location['prefixadd']));

          $location_string .= "&photo=" . str_replace("+", "%20", urlencode($location['photo']));

          $location_string .= "&name=" . str_replace("+", "%20", urlencode($location['name']));

          $location_string .= "&vicinity=" . str_replace("+", "%20", urlencode($location['vicinity']));

          $location_string .= "&latitude=" . str_replace("+", "%20", urlencode($location['latitude']));

          $location_string .= "&longitude=" . str_replace("+", "%20", urlencode($location['longitude']));

          $location_string .= "&icon=" . str_replace("+", "%20", urlencode($location['icon']));

          $location_string .= "&types=" . str_replace("+", "%20", urlencode($location['types'][0]));
//                    
          //echo http_build_query($location, null, '&'); continue;
          $location_shortname = ($location['type'] == 'place' && $location['vicinity']) ? (($location['name'] && $location['name'] != $location['vicinity']) ? $location['name'] . ', ' . $location['vicinity'] : $location['vicinity']) : $location['label'];
          $response['response']['data'][$i]['location_fullname'] = $location['label'] ? $location['label'] : '';
          $response['response']['data'][$i]['location_shortname'] = $location_shortname;
          $response['response']['data'][$i]['prefixadd'] = $location['prefixadd'];
          $response['response']['data'][$i]['location_string'] = $location_string;
//                      $response['response']['data'][$i]['location_string'] =  str_replace("+", "%20",http_build_query($location, null, '&'));
          $i++;
        }
      } else {
        $response['response']['data'] = 'No location found';
      }
      $response['response']['hash'] = md5(serialize($response));
      $this->_jsonSuccessOutput($response);
    }
  }

  public function postuserlocationAction() {
    if ($viewer = $this->checkAuth()) {
      // Get subject if necessary
      $strName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
      $strLimit = 6;
      $subject = null;

      $subject_guid = $this->_getParam('subject', null);
      if ($subject_guid) {
        $subject = Engine_Api::_()->getItemByGuid($subject_guid);
      }
      // Use viewer as subject if no subject
      if (null === $subject) {
        $subject = $viewer;
      }

      $is_ajax = $this->_getParam('is_ajax', 1);
      // Make form
      $form = $this->view->form = new Activity_Form_Post();
      $this->view->status = true;
      // Check auth
      if (Engine_Api::_()->core()->hasSubject()) {
        // Get subject
        $subject = Engine_Api::_()->core()->getSubject();
        if ($subject->getType() == 'sitepage_page' || $subject->getType() == 'sitepageevent_event') {
          $pageSubject = $subject;
          if ($subject->getType() == 'sitepageevent_event')
            $pageSubject = Engine_Api::_()->getItem('sitepage_page', $subject->page_id);
          $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($pageSubject, 'comment');
          if (empty($isManageAdmin)) {
            return $this->_helper->requireAuth()->forward();
          }
        } else if ($subject->getType() == 'sitebusiness_business' || $subject->getType() == 'sitebusinessevent_event') {
          $businessSubject = $subject;
          if ($subject->getType() == 'sitebusinessevent_event')
            $businessSubject = Engine_Api::_()->getItem('sitebusiness_business', $subject->business_id);
          $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($businessSubject, 'comment');
          if (empty($isManageAdmin)) {
            return $this->_helper->requireAuth()->forward();
          }
        } elseif ($subject->getType() == 'sitegroup_group' || $subject->getType() == 'sitegroupevent_event') {
          $groupSubject = $subject;
          if ($subject->getType() == 'sitegroupevent_event')
            $groupSubject = Engine_Api::_()->getItem('sitegroup_group', $subject->group_id);
          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($groupSubject, 'comment');
          if (empty($isManageAdmin)) {
            return $this->_helper->requireAuth()->forward();
          }
        } elseif ($subject->getType() == 'sitestore_store' || $subject->getType() == 'sitestoreevent_event') {
          $storeSubject = $subject;
          if ($subject->getType() == 'sitestoreevent_event')
            $storeSubject = Engine_Api::_()->getItem('sitestore_store', $subject->store_id);
          $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($storeSubject, 'comment');
          if (empty($isManageAdmin)) {
            return $this->_helper->requireAuth()->forward();
          }
        } else if (!$subject->authorization()->isAllowed($viewer, 'comment')) {
          return $this->_helper->requireAuth()->forward();
        }
      }

      $getStrLen = strlen($strName);
      $getComposerValue = 0;
      if ($getStrLen > $strLimit)
        $strName = substr($strName, 0, $strLimit);

      // Check if post
      if (!$this->getRequest()->isPost()) {
        if (empty($is_ajax)) {
          //$this->view->status = false;
          //$this->view->error = Zend_Registry::get('Zend_Translate')->_('Not post');
          $this->_jsonErrorOutput('Not post');
          return;
        } else {
          //echo Zend_Json::encode(array('status' => false, 'error' => Zend_Registry::get('Zend_Translate')->_('Not post')));
          $this->_jsonErrorOutput('Not post');
          exit();
        }
      }
      if (empty($is_ajax) && !Engine_Api::_()->seaocore()->isLessThan420ActivityModule()) {
        // Check token
        if (!($token = $this->_getParam('token'))) {
          //$this->view->status = false;
          //$this->view->error = Zend_Registry::get('Zend_Translate')->_('No token, please try again');
          $this->_jsonErrorOutput('No token, please try again');
          return;
        }
        $session = new Zend_Session_Namespace('ActivityFormToken');
        if ($token != $session->token) {

          //$this->view->status = false;
          //$this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid token, please try again');
          $this->_jsonErrorOutput('Invalid token, please try again');
          return;
        }

        $session->unsetAll();
      }

      extract($this->getRequest()->getPost());

      if (empty($location_string)) {
        $this->_jsonErrorOutput('Location string required');
      }
      // Check if form is valid
      $postData = $this->getRequest()->getPost();

      $postData['is_ajax'] = 1;
      $postData['activity_type'] = 1;
      $postData['compose-checkin'] = '';
      $postData['auth_view'] = 'friends';
      $postData['composer']['tag'] = '';
      $postData['composer']['checkin'] = $location_string;
      $body = @$postData['body'];

      $postData['toValues'] = '';
      $postData['friendas_tag_body_aaf'] = '';
      $postData['method'] = 'json';
      $postData['format'] = 'post';


      $privacy = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.content', 'everyone');
      $elementView = Engine_Api::_()->getApi('settings', 'core')->getSetting('aaf.get.element.view', 0);
      if (isset($postData['auth_view']))
        $privacy = @$postData['auth_view'];

      $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
      $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
      //$body = htmlentities($body, ENT_QUOTES, 'UTF-8');
      $postData['body'] = $body;

      if (!$form->isValid($postData)) {
        if (empty($is_ajax)) {
          //$this->view->status = false;
          //$this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
          $this->_jsonErrorOutput('Invalid data');
          return;
        } else {
          //echo Zend_Json::encode(array('status' => false, 'error' => Zend_Registry::get('Zend_Translate')->_('Invalid data')));
          $this->_jsonErrorOutput('Invalid data');
          exit();
        }
      }
      $composerDatas['tag'] = $this->getRequest()->getParam('composer', null);
      $composerDatas['checkin'] = $location_string; //$this->getRequest()->getParam('composer', null);            
      // Check one more thing
      if ($form->body->getValue() === '' && $form->getValue('attachment_type') === '' && (!isset($postData['composer']['checkin']) || empty($postData['composer']['checkin']) )) {
        if (empty($is_ajax)) {
          //$this->view->status = false;
          //$this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
          $this->_jsonErrorOutput('Invalid data');
          return;
        } else {
          //echo Zend_Json::encode(array('status' => false, 'error' => Zend_Registry::get('Zend_Translate')->_('Invalid data')));
          $this->_jsonErrorOutput('Invalid data');
          exit();
        }
      }
      if (empty($elementView)) {
        for ($str = 0; $str < strlen($strName); $str++)
          $getComposerValue += ord($strName[$str]);
      }

      Engine_Api::_()->getApi('settings', 'core')->setSetting('aaf.list.view.value', $getComposerValue);

      //CHECK FOR THE USER AGENT:
      $ua = $_SERVER['HTTP_USER_AGENT'];
      $checker = array(
          'iphone' => preg_match('/iPhone|iPod|iPad/', $ua),
          'blackberry' => preg_match('/BlackBerry/', $ua),
          'android' => preg_match('/Android/', $ua),
      );

      if ($checker['iphone'])
        $userAgent = 'iphone';
      elseif ($checker['blackberry'])
        $userAgent = 'blackberry';
      elseif ($checker['android'])
        $userAgent = 'android';
      else
        $userAgent = '';

      // set up action variable
      $action = null;

      // Process
      $db = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getAdapter();
      $db->beginTransaction();

      try {
        // Try attachment getting stuff
        $attachment = null;
        $attachmentData = $this->getRequest()->getParam('attachment');

        if (!empty($attachmentData) && !empty($attachmentData['type'])) {
          $type = $attachmentData['type'];

          $config = null;
          foreach (Zend_Registry::get('Engine_Manifest') as $data) {

            if (!empty($data['composer'][$type])) {
              $config = $data['composer'][$type];
            }
          }
          if (!empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1])) {
            $config = null;
          }

          if ($config) {
            $typeExplode = explode("-", $type);
            for ($i = 1; $i < count($typeExplode); $i++)
              $typeExplode[$i] = ucfirst($typeExplode[$i]);
            $type = implode("", $typeExplode);
            $plugin = Engine_Api::_()->loadClass($config['plugin']);
            $method = 'onAttach' . ucfirst($type);
            $attachment = $plugin->$method($attachmentData);
          }
        }


        // Get body
        //$body = $form->getValue('body');
        $body = preg_replace('/<br[^<>]*>/', "\n", $body);

        // Is double encoded because of design mode
        //$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        //$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        //$body = htmlentities($body, ENT_QUOTES, 'UTF-8');
        // Special case: status
        //CHECK IF BOTH FACEBOOK AND TWITTER IS DISABLED.
        $web_values = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.fb.twitter', 0);
        $currentcontent_type = 1;
        if (isset($_POST['activity_type']))
          $currentcontent_type = $_POST['activity_type'];
        if (($currentcontent_type == 1)) {
          $showPrivacyDropdown = in_array('userprivacy', Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.composer.options', array("withtags", "emotions", "userprivacy")));

          if ($viewer->isSelf($subject) && $showPrivacyDropdown) {
            Engine_Api::_()->getDbtable('userSettings', 'seaocore')->setSetting($viewer, "aaf_post_privacy", $privacy);
          } elseif (!$viewer->isSelf($subject)) {
            $privacy = null;
          }
          $activityTable = Engine_Api::_()->getDbtable('actions', 'advancedactivity');
          if (!$attachment && $viewer->isSelf($subject)) {
            $type = 'status';
            if ($body != '') {
              $viewer->status = $body;
              $viewer->status_date = date('Y-m-d H:i:s');
              $viewer->save();

              $viewer->status()->setStatus($body);
            }
            if (isset($postData['composer']['checkin']) && !empty($postData['composer']['checkin'])) {
              if ($body != '')
                $type = 'sitetagcheckin_status';
              else
                $type = 'sitetagcheckin_checkin';
            }

            $action = $activityTable->addActivity($viewer, $subject, $type, $body, $privacy, null, $userAgent);
          } else { // General post
            $type = 'post';
            if (isset($postData['composer']['checkin']) && !empty($postData['composer']['checkin'])) {
              $type = 'sitetagcheckin_post';
            }
            if ($viewer->isSelf($subject)) {
              $type = 'post_self';
              if (isset($postData['composer']['checkin']) && !empty($postData['composer']['checkin'])) {
                $type = 'sitetagcheckin_post_self';
              }
            } else {
              $birthDayPluginEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('birthday');
              if ($subject->getType() == 'user' && $birthDayPluginEnable) {
                $typeInfo = $activityTable->getActionType("birthday_post");
                if ($typeInfo && $typeInfo->enabled) {
                  $birthdayMemberIds = Engine_Api::_()->getApi('birthday', 'advancedactivity')->getMembersBirthdaysInRange(array('range' => 0));
                  if (!empty($birthdayMemberIds) && in_array($subject->getIdentity(), $birthdayMemberIds)) {
                    $type = 'birthday_post';
                  }
                }
              }
            }
            // Add notification for <del>owner</del> user
            $subjectOwner = $subject->getOwner();
            if (!$viewer->isSelf($subject) &&
                    $subject instanceof User_Model_User) {
              $notificationType = 'post_' . $subject->getType();
              Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($subjectOwner, $viewer, $subject, $notificationType, array(
                  'url1' => $subject->getHref(),
              ));
            }

            // Add activity
            if ($subject->getType() == "sitepage_page") {
              $activityFeedType = null;
              if (Engine_Api::_()->sitepage()->isPageOwner($subject) && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
                $activityFeedType = 'sitepage_post_self';
              elseif ($subject->all_post || Engine_Api::_()->sitepage()->isPageOwner($subject))
                $activityFeedType = 'sitepage_post';

              if ($activityFeedType) {
                $action = $activityTable->addActivity($viewer, $subject, $activityFeedType, $body, null, null, $userAgent);
                Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
              }
            } else if ($subject->getType() == "sitebusiness_business") {
              $activityFeedType = null;
              if (Engine_Api::_()->sitebusiness()->isBusinessOwner($subject) && Engine_Api::_()->sitebusiness()->isFeedTypeBusinessEnable())
                $activityFeedType = 'sitebusiness_post_self';
              elseif ($subject->all_post || Engine_Api::_()->sitebusiness()->isBusinessOwner($subject))
                $activityFeedType = 'sitebusiness_post';

              if ($activityFeedType) {
                $action = $activityTable->addActivity($viewer, $subject, $activityFeedType, $body, null, null, $userAgent);
                Engine_Api::_()->getApi('subCore', 'sitebusiness')->deleteFeedStream($action);
              }
            } elseif ($subject->getType() == "sitegroup_group") {
              $activityFeedType = null;
              if (Engine_Api::_()->sitegroup()->isGroupOwner($subject) && Engine_Api::_()->sitegroup()->isFeedTypeGroupEnable())
                $activityFeedType = 'sitegroup_post_self';
              elseif ($subject->all_post || Engine_Api::_()->sitegroup()->isGroupOwner($subject))
                $activityFeedType = 'sitegroup_post';

              if ($activityFeedType) {
                $action = $activityTable->addActivity($viewer, $subject, $activityFeedType, $body, null, null, $userAgent);
                Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action);
              }
            } elseif ($subject->getType() == "sitestore_store") {
              $activityFeedType = null;
              if (Engine_Api::_()->sitestore()->isStoreOwner($subject) && Engine_Api::_()->sitestore()->isFeedTypeStoreEnable())
                $activityFeedType = 'sitestore_post_self';
              elseif ($subject->all_post || Engine_Api::_()->sitestore()->isStoreOwner($subject))
                $activityFeedType = 'sitestore_post';

              if ($activityFeedType) {
                $action = $activityTable->addActivity($viewer, $subject, $activityFeedType, $body, null, null, $userAgent);
                Engine_Api::_()->getApi('subCore', 'sitestore')->deleteFeedStream($action);
              }
            } else {
              $action = $activityTable->addActivity($viewer, $subject, $type, $body, $privacy, null, $userAgent);
            }
            // Try to attach if necessary
            if ($action && $attachment) {
              // Item Privacy Work Start
              if (!empty($privacy)) {
                if (!in_array($privacy, array('everyone', 'networks', 'friends', 'onlyme'))) {
                  if (Engine_Api::_()->advancedactivity()->isNetworkBasePrivacy($privacy)) {
                    $privacy = 'networks';
                  } else {
                    $privacy = 'onlyme';
                  }
                }
                Engine_Api::_()->advancedactivity()->editContentPrivacy($attachment, $viewer, $privacy);
              }
              $activityTable->attachActivity($action, $attachment);
            }
          }

//                $composerDatas = $this->getRequest()->getParam('composer', null);
          $composerDatas['tag'] = $this->getRequest()->getParam('composer', null);
          $composerDatas['checkin'] = $location_string; //$this->getRequest()->getParam('composer', null);

          if ($action && !empty($composerDatas)) {
            foreach ($composerDatas as $composerDataType => $composerDataValue) {
              if (empty($composerDataValue))
                continue;
              foreach (Zend_Registry::get('Engine_Manifest') as $data) {
                if (isset($data['composer'][$composerDataType]['plugin']) && !empty($data['composer'][$composerDataType]['plugin'])) {
                  $pluginClass = $data['composer'][$composerDataType]['plugin'];
                  $plugin = Engine_Api::_()->loadClass($pluginClass);
                  $method = 'onAAFComposer' . ucfirst($composerDataType);
                  if (method_exists($plugin, $method))
                    $plugin->$method(array($composerDataType => $composerDataValue), array('action' => $action));
                }
              }
            }

            //START SITETAGCHECKIN CODE
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin') && isset($postData['toValues']) && !empty($postData['toValues'])) {
              $apiSitetagCheckin = Engine_Api::_()->sitetagcheckin();
              $users = array_values(array_unique(explode(",", $postData['toValues'])));
              $actionParams = (array) $action->params;
              if (isset($actionParams['checkin'])) {
                foreach (Engine_Api::_()->getItemMulti('user', $users) as $tag) {
                  $apiSitetagCheckin->saveCheckin($actionParams['checkin'], $action, $actionParams, $tag->user_id);
                }
              }
            }
            //END SITETAGCHECKIN CODE
          }
        }

        // Start the work for tagging
        if ($action && isset($postData['toValues']) && !empty($postData['toValues'])) {
          $actionTag = new Engine_ProxyObject($action, Engine_Api::_()->getDbtable('tags', 'core'));
          $users = array_values(array_unique(explode(",", $postData['toValues'])));
          $params = (array) $action->params;
          $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
          foreach (Engine_Api::_()->getItemMulti('user', $users) as $tag) {
            $actionTag->addTagMap($viewer, $tag, null);
            // Add notification
            $type_name = $this->view->translate(str_replace('_', ' ', 'post'));
            if (!(is_array($params) && isset($params['checkin']))) {
              Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
                      $tag, $viewer, $action, 'tagged', array(
                  'object_type_name' => $type_name,
                  'label' => $type_name,
                      )
              );
            } else {
              //GET LABEL
              $label = $params['checkin']['label'];
              $checkin_resource_guid = $params['checkin']['resource_guid'];
              //MAKE LOCATION LINK
              if (isset($checkin_resource_guid) && empty($checkin_resource_guid)) {
                $locationLink = $view->htmlLink('http://maps.google.com/?q=' . urlencode($label), $label, array('target' => '_blank'));
              } else {
                $pageItem = Engine_Api::_()->getItemByGuid($checkin_resource_guid);
                $pageLink = $pageItem->getHref();
                $pageTitle = $pageItem->getTitle();
                $locationLink = "<a href='$pageLink'>$pageTitle</a>";
              }
              //SEND NOTIFICATION
              Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($tag, $viewer, $action, "sitetagcheckin_tagged", array("location" => $locationLink, "label" => $type_name));
            }
          }
        }

//              $publishMessage = html_entity_decode($form->getValue('body'));
        $publishMessage = html_entity_decode($body);
        $publishUrl = null;
        $publishName = null;
        $publishDesc = null;
        $publishPicUrl = null;
        // Add attachment
        if ($attachment) {
          $publishUrl = $attachment->getHref();
          $publishName = $attachment->getTitle();
          $publishDesc = $attachment->getDescription();
          if (empty($publishName)) {
            $publishName = ucwords($attachment->getShortType());
          }
          if (($tmpPicUrl = $attachment->getPhotoUrl())) {
            $publishPicUrl = $tmpPicUrl;
          }
          // prevents OAuthException: (#100) FBCDN image is not allowed in stream
          if ($publishPicUrl &&
                  preg_match('/fbcdn.net$/i', parse_url($publishPicUrl, PHP_URL_HOST))) {
            $publishPicUrl = null;
          }
        } else {
          $publishUrl = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . _ENGINE_R_BASE;
        }
        // Check to ensure proto/host
        if ($publishUrl &&
                false === stripos($publishUrl, 'http://') &&
                false === stripos($publishUrl, 'https://')) {
          $publishUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishUrl;
        }
        if ($publishPicUrl &&
                false === stripos($publishPicUrl, 'http://') &&
                false === stripos($publishPicUrl, 'https://')) {
          $publishPicUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishPicUrl;
        }
        // Add site title
        if ($publishName) {
          $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title
                  . ": " . $publishName;
        } else {
          $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
        }


        // Publish to facebook, if checked & enabled
        if ((($currentcontent_type == 3) || isset($_POST['post_to_facebook']))) {
          try {

            $session = new Zend_Session_Namespace();

            $facebookApi = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();

            if ($facebookApi && Seaocore_Api_Facebook_Facebookinvite::checkConnection(null, $facebookApi)) {
              $fb_data = array(
                  'message' => strip_tags($publishMessage),
              );
              if ($publishUrl) {
                if (isset($_POST['attachment'])) {
                  $fb_data['link'] = $publishUrl;
                }
                if ($attachment && $currentcontent_type == 3) {
                  $fb_data['link'] = $attachment->uri;
                }
              }
              if ($publishName) {
                $fb_data['name'] = $publishName;
              }
              if ($publishDesc) {
                $fb_data['description'] = $publishDesc;
              }
              if ($publishPicUrl) {
                $fb_data['picture'] = $publishPicUrl;
              }
              if (isset($_POST['attachment']) && $_POST['attachment']['type'] == 'music') {

                $file = Engine_Api::_()->getItem('storage_file', $attachment->file_id);
                $fb_data['source'] = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->seaddonsBaseUrl() . '/' . $file->storage_path;
                $fb_data['type'] = 'mp3';
                $fb_data['picture'] = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getBaseUrl() . '/application/modules/Advancedactivity/externals/images/music-button.png';
                ;
              }


              if (isset($fb_data['link']) && !empty($fb_data['link'])) {
                $appkey = Engine_Api::_()->getApi('settings', 'core')->getSetting('bitly.apikey');
                $appsecret = Engine_Api::_()->getApi('settings', 'core')->getSetting('bitly.secretkey');
                if (!empty($appkey) && !empty($appsecret)) {
                  $shortURL = Engine_Api::_()->getApi('Bitly', 'seaocore')->get_bitly_short_url($fb_data['link'], $appkey, $appsecret, $format = 'txt');
                  $fb_data['link'] = $shortURL;
                }
              }
              $res = $facebookApi->api('/me/feed', 'POST', $fb_data);
              if ($subject && isset($subject->fbpage_url) && !empty($subject->fbpage_url)) {
                //EXTRACTING THE PAGE ID FROM THE PAGE URL.
                $url_expload = explode("?", $subject->fbpage_url);
                $url_expload = explode("/", $url_expload[0]);
                $count = count($url_expload);
                $page_id = $url_expload[--$count];
                //$manages_pages = $facebookApi->api('/me/accounts', 'GET');
                //NOW IF THE USER WHO IS COMENTING IS OWNER OF THIS FACEBOOK PAGE THEN GETTING THE PAGE ACCESS TOKEN TO WITH THIS SITE PAGE IS INTEGRATED.
                if (($subject->getType() == 'sitepage_page' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postfbpage', 1) ) || ($subject->getType() == 'sitebusiness_business' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitebusiness.postfbbusiness', 1)) || ($subject->getType() == 'sitegroup_group' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.postfbgroup', 1)) || ($subject->getType() == 'sitestore_store' && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitestore.postfbstore', 1))) {
                  $pageinfo = $facebookApi->api('/' . $page_id . '?fields=access_token', 'GET');
                  if (isset($pageinfo['access_token']))
                    $fb_data['access_token'] = $pageinfo['access_token'];
                  $res = $facebookApi->api('/' . $page_id . '/feed', 'POST', $fb_data);
                }
              }

              if ($currentcontent_type == 3) {
                $last_fbfeedid = $_POST['fbmin_id'];

                $feed_stream = $this->view->content()->renderWidget("advancedactivity.advancedactivityfacebook-userfeed", array('getUpdate' => true, 'is_ajax' => 1, 'minid' => $last_fbfeedid, 'currentaction' => 'post_new'));
                echo Zend_Json::encode(array('status' => true, 'post_fail' => 0, 'feed_stream' => $feed_stream));
                exit();
              }
            }
          } catch (Exception $e) {
            // Silence
          }
        } // end Facebook
        // Publish to twitter, if checked & enabled
        if ((($currentcontent_type == 2) || isset($_POST['post_to_twitter']))) {
          try {
            $Api_twitter = Engine_Api::_()->getApi('twitter_Api', 'seaocore');
            if ($Api_twitter->isConnected()) {
              // @todo truncation?
              // @todo attachment
              $twitterOauth = $twitter = $Api_twitter->getApi();
              $login = Engine_Api::_()->getApi('settings', 'core')->getSetting('bitly.apikey');
              $appkey = Engine_Api::_()->getApi('settings', 'core')->getSetting('bitly.secretkey');


              //TWITTER ONLY ACCEPT 140 CHARACTERS MAX..
              //IF BITLY IS CONFIGURED ON THE SITE..
              if (!empty($login) && !empty($appkey)) {
                if (strlen(html_entity_decode($_POST['body'])) > 140 || isset($_POST['attachment'])) {
                  $shortURL = Engine_Api::_()->getApi('Bitly', 'seaocore')->get_bitly_short_url('http://' . $_SERVER['HTTP_HOST'] . $action->getHref(), $login, $appkey, $format = 'txt');
                  $BitlayLength = strlen($shortURL);
                  $twitterFeed = substr(html_entity_decode($_POST['body']), 0, (140 - ($BitlayLength + 1))) . ' ' . $shortURL;
                } else
                  $twitterFeed = html_entity_decode($_POST['body']);
              }
              else {
                $twitterFeed = substr(html_entity_decode($_POST['body']), 0, 137) . '...';
              }

              $lastfeedobject = $twitterOauth->post(
                      'statuses/update', array('status' => $twitterFeed)
              );


              if ($currentcontent_type == 2) {

                $feed_stream = $this->view->content()->renderWidget("advancedactivity.advancedactivitytwitter-userfeed", array('getUpdate' => true, 'currentaction' => 'post_new', 'feedobj' => $lastfeedobject));
                echo Zend_Json::encode(array('status' => true, 'post_fail' => 0, 'feed_stream' => $feed_stream));
                exit();
              }
            }
          } catch (Exception $e) {
            // Silence
          }
        }

        // Publish to linkedin, if checked & enabled
        if ((($currentcontent_type == 5) || isset($_POST['post_to_linkedin']))) {

          try {
            $Api_linkedin = Engine_Api::_()->getApi('linkedin_Api', 'seaocore');
            $OBJ_linkedin = $Api_linkedin->getApi();



            // $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
            if ($OBJ_linkedin) {
              if ($attachment):
                if ($publishUrl) {
                  $content['submitted-url'] = $publishUrl;
                }
                if ($currentcontent_type == 5) {
                  $content['submitted-url'] = $attachment->uri;
                }
                if ($publishName && $publishUrl) {
                  $content['title'] = $publishName;
                }
                if ($publishDesc) {
                  $content['description'] = $publishDesc;
                }
                if ($publishPicUrl) {
                  $content['submitted-image-url'] = $publishPicUrl;
                }
              endif;
              $content['comment'] = strip_tags($publishMessage);

              $lastfeedobject = $OBJ_linkedin->share('new', $content);

              if ($currentcontent_type == 5) {
                $last_linkedinfeedid = $_POST['linkedinmin_id'];

                $feed_stream = $this->view->content()->renderWidget("advancedactivity.advancedactivitylinkedin-userfeed", array('getUpdate' => true, 'currentaction' => 'post_new', 'minid' => $last_linkedinfeedid, 'is_ajax' => 1));
                echo Zend_Json::encode(array('status' => true, 'post_fail' => 0, 'feed_stream' => $feed_stream));
                exit();
              }
            }
          } catch (Exception $e) {
            // Silence
          }
        }
        if (empty($is_ajax) && !Engine_Api::_()->seaocore()->isLessThan420ActivityModule()) {
          // Publish to janrain
          if (//$this->_getParam('post_to_janrain', false) &&
                  'publish' == Engine_Api::_()->getApi('settings', 'core')->core_janrain_enable) {
            try {
              $session = new Zend_Session_Namespace('JanrainActivity');
              $session->unsetAll();

              $session->message = $publishMessage;
              $session->url = $publishUrl ? $publishUrl : 'http://' . $_SERVER['HTTP_HOST'] . _ENGINE_R_BASE;
              $session->name = $publishName;
              $session->desc = $publishDesc;
              $session->picture = $publishPicUrl;
            } catch (Exception $e) {
              // Silence
            }
          }
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e; // This should be caught by error handler
      }



      // If we're here, we're done
      $this->view->status = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Success!');
      // Check if action was created
      $post_fail = 0;
      if ($currentcontent_type == 1 && !$action) {
        $post_fail = 1;
      }
      $response = array();
      $feed_stream = "";
      $last_id = 0;
      if ($action) {
        //$feed_stream = $this->view->advancedActivity($action, array('onlyactivity' => true));
        $last_id = $action->getIdentity();

        $data = $this->allfeed($viewer, $subject, $last_id);

        $html = $this->view->partial(
                '_view_latest_feed.tpl', $this->_currentModuleCoreApi->getModuleName(), $data
        );

        $response['response']['success'] = 'success';
        $response['response']['body'] = base64_encode($html);
        $response['response']['hash'] = md5(serialize($response));
        $this->_jsonSuccessOutput($response);
      } else {
        $this->_jsonErrorOutput('Post not possible');
      }
    }
  }

  private function getConversation($messages, $flag = null) {
    if ($viewer = $this->checkAuth()) {
      $i = 0;
      $response = array();
      $response['response']['success'] = 'success';
      if (count($messages) > 0) {
        foreach ($messages as $message) {
          $response['response']['data'][$i]['id'] = $message->message_id;
          $response['response']['data'][$i]['sender_user_id'] = $message->user_id;
          $response['response']['data'][$i]['date'] = $this->view->customtimestamp($message->date);
          $response['response']['data'][$i]['body'] = $message->body;
          if ($message->user_id == $viewer->user_id) {
            $response['response']['data'][$i]['sender'] = 'me';
          } else {
            $response['response']['data'][$i]['sender'] = $this->view->user($message->user_id)->displayname;
          }

          if ($flag AND $flag == 'today') {
            $response['response']['data'][$i]['show_datetime'] = '';
          } elseif (strtotime($message->date) < strtotime("yesterday")) {
            //$response['response']['data'][$i]['show_datetime'] = $message->date;
            $date[$i] = date('Y-m-d', strtotime($message->date));
            if ($date[$i] != $date[$i - 1]) {
              $response['response']['data'][$i]['show_datetime'] = $this->view->locale()->toDate($message->date, array('format' => 'MMMM dd, y'));
//                            $response['response']['data'][$i]['show_datetime'] = $this->view->timeStamp($message->date); 
            } else {
              $response['response']['data'][$i]['show_datetime'] = '';
            }
          } elseif (strtotime($message->date) > strtotime("yesterday") AND strtotime($message->date) < strtotime("today")) {
            $date[$i] = date('Y-m-d', strtotime($message->date));
            if ($date[$i] != $date[$i - 1]) {
              $response['response']['data'][$i]['show_datetime'] = 'yesterday';
            } else {
              $response['response']['data'][$i]['show_datetime'] = '';
            }
          } elseif (strtotime($message->date) >= strtotime("today")) {
            //$response['response']['data'][$i]['show_datetime'] = 'today';
            $date[$i] = date('Y-m-d', strtotime($message->date));
            if ($date[$i] != $date[$i - 1]) {
              $response['response']['data'][$i]['show_datetime'] = 'today';
            } else {
              $response['response']['data'][$i]['show_datetime'] = '';
            }
          }

//                    if(strtotime($message->date) < strtotime('-24 hours'))                      
//                    {
//                        $date[$i] = date('Y-m-d',  strtotime($message->date));
//                        if($date[$i] != $date[$i-1])
//                        {
//                            $response['response']['data'][$i]['show_datetime'] = $this->view->customtimestamp($message->date); 
//                        }
//                        else
//                        {
//                            $response['response']['data'][$i]['show_datetime'] = '';
//                        }
//
//                    }
//                    elseif(strtotime($message->date) < strtotime("-60 minutes")) 
//                    {
//                        $date[$i] = $this->view->customtimestamp($message->date); 
//                        if($date[$i] != $date[$i-1])
//                        {
//                            $response['response']['data'][$i]['show_datetime'] = $this->view->customtimestamp($message->date); 
//                        }
//                        else
//                        {
//                            $response['response']['data'][$i]['show_datetime'] = '';
//                        }
//                    }
          // prepare for attachment
          if (!empty($message->attachment_type) && null !== ($attachment = $this->view->item($message->attachment_type, $message->attachment_id))) {
            $link_type = '';
            if ($attachment->getType() == 'core_link') {
              $link_type = 'core_link';
              $response['response']['data'][$i]['feed_attachment']['url'] = $this->view->serverUrl((string) $attachment->getHref());
              if ($attachment->getTitle()) {
                $response['response']['data'][$i]['feed_attachment']['title'] = $attachment->getTitle();
              }
            } elseif ($attachment->getType() == 'video') {
              $link_type = 'video';
              if (count($action->getAttachments()) == 1 && null != ( $richContent = current($action->getAttachments())->item)) {
                if ($richContent->type == 1)
                  $type = 'youtube';
                elseif ($richContent->type == 2)
                  $type = 'vimeo';
                else
                  $type = 'unknown';
                $response['response']['data'][$i]['feed_attachment']['video_title'] = strip_tags($richContent->title);
                $response['response']['data'][$i]['feed_attachment']['video_type'] = $type;
                $response['response']['data'][$i]['feed_attachment']['video_code'] = $richContent->code;
                //$response['response']['data']['feed_attachment']['video_image'] = $this->mybase64_encode($this->getItemPhotoUrl($richContent));                    }
              }
              elseif ($attachment->getItemPhotoUrl()) {
                $link_type = 'image';
                $response['response']['data'][$i]['feed_attachment']['url'] = $this->mybase64_encode($this->getItemPhotoUrl($attachment->item, 'thumb_normal'));
              }
              $response['response']['data'][$i]['feed_attachment_type'] = $link_type;
            }
          }
          $i++;
        }
      } else {
        $response['response']['data'] = 'No message found';
      }
      return $response;
    }
  }

  public function getjsbaseurlAction() {
    $response = array();
    $response['response']['success'] = 'success';
    $response['response']['data']['url'] = $this->view->serverUrl((string) $this->view->layout()->staticBaseUrl);
    $this->_jsonSuccessOutput($response);
  }

//  public function updatedeviceinfoAction() {
//    if ($user = $this->checkAuth()) {
//      extract($this->getRequest()->getPost());
//      if (empty($device_token)) {
//        $this->_jsonErrorOutput('device token required');
//      }
//      //echo $device_type; exit;
//      $device_table = Engine_Api::_()->getDbtable('devices', 'mgslapi');
//      $db = $device_table->getAdapter();
//      $db->beginTransaction();
//      try {
//        $select = $device_table->select()
//                ->where('user_id = ?', $user->getIdentity())
//        ;
//
//        $registeredDevice = $device_table->fetchRow($select);
//        if ($registeredDevice->device_id) {
//          $registeredDevice->device_type = 1;
//          $registeredDevice->device_token = strip_tags(trim($device_token));
//          $registeredDevice->save();
//        } else {
//          $device = $device_table->createRow();
//          $device->user_id = $user->getIdentity();
//          $device->device_type = 1;
//          $device->device_token = strip_tags(trim($device_token));
//          $device->save();
//        }
//
//        $db->commit();
//        $response['response']['success'] = $this->view->translate('update successful', $this->lang);
//        $this->_jsonSuccessOutput($response);
//      } catch (Exception $e) {
//        $db->rollBack();
//        //          throw $e;
//        $this->_jsonErrorOutput('An error has occurred.');
//      }
//    }
//  }

  public function sendpushnotificationAction() {
    if ($user = $this->checkAuth()) {
      extract($this->getRequest()->getPost());
      if (empty($device_token)) {
        $this->_jsonErrorOutput('device token required');
      }
      if (empty($message)) {
        $this->_jsonErrorOutput('Message required');
      }
      //echo $device_type; exit;
//            $device_table = Engine_Api::_()->getDbtable('devices', 'mgslapi');
//            $db = $device_table->getAdapter();
//            $db->beginTransaction();
      try {
//                $select = $device_table->select()
//                        ->where('user_id = ?', $user->getIdentity())
//                        ;
//                
//                $device = $device_table->fetchRow($select);
//                if($device->device_id)
//                {
//                    $device->device_type = 1;
//                    $device->device_token = strip_tags(trim($device_token));
//                    $device->save();
//                }
//                else
//                {
//                    $device = $device_table->createRow();
//                    $device->user_id = $user->getIdentity();
//                    $device->device_type = 1;
//                    $device->device_token = strip_tags(trim($device_token));
//                    $device->save();  
//                }
        //$db->commit();                
        $pushMessage = $message;
        $deviceToken = strip_tags(trim($device_token));
        $deviceType = 1;
        $customPushData = array(
            'id' => 131,
            'type' => 'friend_request'
        );
        Engine_Api::_()->getApi('core', 'mgslapi')->sendPushNotification($deviceToken, $deviceType, $pushMessage, $customPushData);
        $response['response']['success'] = $this->view->translate('Successful', $this->lang);
        $this->_jsonSuccessOutput($response);
      } catch (Exception $e) {
        //$db->rollBack();
        //          throw $e;
        $this->_jsonErrorOutput('An error has occurred.');
      }
    }
  }

//    public function testAction()
//    {
//        $con = mysql_connect('aa2sigv07928vx.cijeopa46pte.ap-southeast-2.rds.amazonaws.com', 'ebroot','Password1!') or die('fail');
//        mysql_select_db('ebdb') or die ('no database');
//        $sql = "ALTER TABLE `engine4_mgslapi_devices` DROP `sender_id`";
//        mysql_query($sql, $con) or die('fail');
//    }

  public function checkAuth() {
//        if (!$this->getRequest()->isPost()) {
//            $this->JSONErrorOutput('invalid_user_data_supplied');
//        }
//        $email = $this->getRequest()->getPost('email');      
//        $password =$this->getRequest()->getPost('password');
    $email = $this->_getParam('email');
    $password = $this->_getParam('password');
    if (!$email || empty($email)) {
      $this->_jsonErrorOutput('email required');
    }
    if (!$password || empty($password)) {
      $this->_jsonErrorOutput('password required');
    }

    $user_table = Engine_Api::_()->getDbtable('users', 'user');
    $user_select = $user_table->select()
            ->where('email = ?', $email);
    $user = $user_table->fetchRow($user_select);

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $coreSecret = $settings->getSetting('core.secret', 'none');

    // Get ip address
    $db = Engine_Db_Table::getDefaultAdapter();
    $ipObj = new Engine_IP();
    $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));

    if (empty($user)) {
      $data['error'] = 'No record of a member with that email was found.';
      $this->_jsonSuccessOutput($data);
    }
    if (!$user->enabled) {
      if (!$user->verified) {
        $this->_jsonErrorOutput('This account still requires either email verification');
      } else if (!$user->approved) {
        $this->_jsonErrorOutput('This account still requires admin approval');
      }
    }
    // check valid parent
    $result = Engine_Api::_()->mgslapi()->getUserByCredential($email, $password);
    if (!$result) {
      $this->_jsonErrorOutput('Invalid credentials supplied');
    }
    return $result;
  }

//    public function JSONSuccessOutput($response) 
//    {        
//        header('Content-Type: application/json');         
//        if(strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== FALSE)
//        {
//            echo json_encode($response, JSON_PRETTY_PRINT);
//        }
//        else
//        {
//            echo json_encode($response);
//        }
//        
//        exit;
//    }
//    public function JSONErrorOutput($errorMessage = 'Unknown Error') 
//    {
//        header('Content-Type: application/json'); 
//        $data = array();
//        //$data['error'] = $this->view->translate($errorMessage, $this->lang);
//        $data['error'] = $errorMessage;
//        echo json_encode($data);
//        exit;
//    }

  public function imageSizeAction() {

    $url = $this->mybase64_decode($this->_getParam('url'));
    $type = $this->_getParam('type', 'ratio');
    // load image
    $ext = strtolower($this->getExtension($url));
    if (!strcmp("jpg", $ext) || !strcmp("jpeg", $ext))
      $src_img = imagecreatefromjpeg($url);

    if (!strcmp("png", $ext))
      $src_img = imagecreatefrompng($url);

    if (!strcmp("gif", $ext))
      $src_img = imagecreatefromgif($url);
    //gets the dimmensions of the image
    $old_x = imageSX($src_img);
    $old_y = imageSY($src_img);

    $w = $this->_getParam('w') ? $this->_getParam('w') : $old_x;
    $h = $this->_getParam('h') ? $this->_getParam('h') : $old_y;

    if ($w > $old_x)
      $w = $old_x;
    if ($h > $old_y)
      $h = $old_y;

    // copy the image
    if ($type == 'ratio') {
      $ratio1 = $old_x / $w;
      $ratio2 = $old_y / $h;

      //echo $ratio1.' '.$ratio2;exit;


      if ($ratio1 > $ratio2) {
        $thumb_w = $w;
        $thumb_h = $old_y / $ratio1;
      } else {
        $thumb_h = $h;
        $thumb_w = $old_x / $ratio2;
      }
      $dst_img = ImageCreateTrueColor($thumb_w, $thumb_h);
      //$dst_img = imagecreatetruecolor( $w, $h );
      imagealphablending($dst_img, false);
      imagesavealpha($dst_img, true);
      imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
      echo imageSX($dst_img) . ',' . imageSY($dst_img);
    } else if ($type == 'exact') {
      $original_aspect = $old_x / $old_y;
      $thumb_aspect = $w / $h;


      if ($original_aspect >= $thumb_aspect) {
        // If image is wider than thumbnail (in aspect ratio sense)
        $new_height = $h;
        $new_width = $old_x / ($old_y / $h);
      } else {
        // If the thumbnail is wider than the image
        $new_width = $w;
        $new_height = $old_y / ($old_x / $w);
      }

      $dst_img = imagecreatetruecolor($w, $h);
      imagealphablending($dst_img, false);
      imagesavealpha($dst_img, true);
      $resize = imagecopyresampled($dst_img, $src_img, 0 - ($new_width - $w) / 2, // Center the image horizontally
              0 - ($new_height - $h) / 2, // Center the image vertically
              0, 0, $new_width, $new_height, $old_x, $old_y
      );
      echo imageSX($dst_img) . ',' . imageSY($dst_img);
    }
  }

  public function imageResizeAction() {
    header("Content-Type: text/html");
    //header("Expires: 0");

    $url = $this->mybase64_decode($this->_getParam('url'));
    $type = $this->_getParam('type', 'ratio');
    // load image
    $ext = strtolower($this->getExtension($url));
    if (!strcmp("jpg", $ext) || !strcmp("jpeg", $ext))
      $src_img = imagecreatefromjpeg($url);

    if (!strcmp("png", $ext))
      $src_img = imagecreatefrompng($url);

    if (!strcmp("gif", $ext))
      $src_img = imagecreatefromgif($url);
    //gets the dimmensions of the image
    $old_x = imageSX($src_img);
    $old_y = imageSY($src_img);

    $w = $this->_getParam('w') ? $this->_getParam('w') : $old_x;
    $h = $this->_getParam('h') ? $this->_getParam('h') : $old_y;

    if ($w > $old_x)
      $w = $old_x;
    if ($h > $old_y)
      $h = $old_y;

    // copy the image
    if ($type == 'ratio') {
      $ratio1 = $old_x / $w;
      $ratio2 = $old_y / $h;

      //echo $ratio1.' '.$ratio2;exit;


      if ($ratio1 > $ratio2) {
        $thumb_w = $w;
        $thumb_h = $old_y / $ratio1;
      } else {
        $thumb_h = $h;
        $thumb_w = $old_x / $ratio2;
      }
      $dst_img = ImageCreateTrueColor($thumb_w, $thumb_h);
      //$dst_img = imagecreatetruecolor( $w, $h );
      imagealphablending($dst_img, false);
      imagesavealpha($dst_img, true);
      imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
    } else if ($type == 'exact') {
      $original_aspect = $old_x / $old_y;
      $thumb_aspect = $w / $h;


      if ($original_aspect >= $thumb_aspect) {
        // If image is wider than thumbnail (in aspect ratio sense)
        $new_height = $h;
        $new_width = $old_x / ($old_y / $h);
      } else {
        // If the thumbnail is wider than the image
        $new_width = $w;
        $new_height = $old_y / ($old_x / $w);
      }

      $dst_img = imagecreatetruecolor($w, $h);
      imagealphablending($dst_img, false);
      imagesavealpha($dst_img, true);
      imagecopyresampled($dst_img, $src_img, 0 - ($new_width - $w) / 2, // Center the image horizontally
              0 - ($new_height - $h) / 2, // Center the image vertically
              0, 0, $new_width, $new_height, $old_x, $old_y
      );
    }

    //imagecopyresized($resized, $image, 0, 0, 0, 0, $w, $h, imagesx($image), imagesy($image));
    // output the image as PNG
    header('Content-type: image/jpg');
    if (!strcmp("png", $ext))
      imagepng($dst_img, null, 9);
    elseif (!strcmp("gif", $ext))
      imagegif($dst_img);
    else
      imagejpeg($dst_img, NULL, 100);

    imagedestroy($dst_img);
    imagedestroy($src_img);
  }

  private function getExtension($str) {
    $i = strrpos($str, ".");
    if (!$i) {
      return "";
    }
    if (strstr($str, "?")) {
      $l = strrpos($str, "?");
      $l = ($l - 1) - $i;
    } else {
      $l = strlen($str) - $i;
    }
    $ext = substr($str, $i + 1, $l);
    return $ext;
  }

  public function mybase64_encode($s) {
    return str_replace(array('+', '/'), array(',', '-'), base64_encode($s));
  }

  public function mybase64_decode($s) {
    return base64_decode(str_replace(array(',', '-'), array('+', '/'), $s));
  }

  public function extractCode($url, $type) {
    switch ($type) {
      //youtube
      case "1":
        try {
          // change new youtube URL to old one
          $url = preg_replace("/#!/", "?", $url);

          // get v variable from the url
          $arr = array();
          $arr = @parse_url($url);
          $code = "code";
          if (array_key_exists('query', $arr)) {
            $parameters = $arr["query"];
            parse_str($parameters, $data);
            $code = $data['v'];

            return $code;
          } else
            return 0;
        } catch (Exception $e) {
          $this->_str .= "<error>" . preg_replace('/\s+/', '_', strtolower($e->getMessage())) . "</error>";
        }
      //vimeo
      case "2":
        // get the first variable after slash
        $code = @pathinfo($url);
        return $code['basename'];
    }
  }

  // YouTube Functions
  public function checkYouTube($code) {
    if (!$data = @file_get_contents("http://gdata.youtube.com/feeds/api/videos/" . $code))
      return false;
    if ($data == "Video not found")
      return false;
    return true;
  }

  // Vimeo Functions
  public function checkVimeo($code) {
    //http://www.vimeo.com/api/docs/simple-api
    //http://vimeo.com/api/v2/video
    $data = @simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
    $id = count($data->video->id);
    if ($id == 0)
      return false;
    return true;
  }

  public function handleInformation($type, $code) {
    switch ($type) {
      //youtube
      case "1":
        $yt = new Zend_Gdata_YouTube();
        $youtube_video = $yt->getVideoEntry($code);
        $information = array();
        $information['title'] = $youtube_video->getTitle();
        $information['description'] = $youtube_video->getVideoDescription();
        $information['duration'] = $youtube_video->getVideoDuration();
        //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
        return $information;
      //vimeo
      case "2":
        //thumbnail_medium
        $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
        $thumbnail = $data->video->thumbnail_medium;
        $information = array();
        $information['title'] = $data->video->title;
        $information['description'] = $data->video->description;
        $information['duration'] = $data->video->duration;
        //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
        return $information;
    }
  }

  // handles thumbnails
  public function handleThumbnail($type, $code = null) {
    switch ($type) {
      //youtube
      case "1":
        //http://img.youtube.com/vi/Y75eFjjgAEc/default.jpg
        return "http://img.youtube.com/vi/$code/default.jpg";
      //vimeo
      case "2":
        //thumbnail_medium
        $data = simplexml_load_file("http://vimeo.com/api/v2/video/" . $code . ".xml");
        $thumbnail = $data->video->thumbnail_medium;
        return $thumbnail;
    }
  }

  function getaddress($lat, $lng) {
    $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim($lat) . ',' . trim($lng) . '&sensor=false';
    $json = @file_get_contents($url);
    $data = json_decode($json);
    $status = $data->status;
    if ($status == "OK")
      return $data->results[0]->formatted_address;
    else
      return false;
  }

}
