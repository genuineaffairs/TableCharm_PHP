<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Api
 *
 * @author abakivn
 */
class Mgslapi_Controller_Action_Api extends Core_Controller_Action_Standard
{

  protected $_translator;
  protected $_logger;
  protected $_deviceType;
  protected $_safaCode = 'm3u3vAkA2jCqCGT';

  public function init()
  {
    $this->_translator = Zend_Registry::get('Zend_Translate');
    $this->_logger = new Zend_Log();
    $this->_logger->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/api.log'));
    Zend_Registry::set('is_app', 1);
  }

  public function loginAction()
  {

    $this->getRequest()->setParam('format', 'json');
    $this->getRequest()->setParam('from_app', 1);
    try {
      $result = json_decode($this->view->action('login', 'auth', 'zulu', $this->getRequest()->getParams()));
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }
    if ($result->status == true) {
      $user = Engine_Api::_()->user()->getViewer();
      // Response successful login data
      $data = array();
      $data['user_id'] = $user->user_id;
      $data['user_name'] = $user->displayname;
      $data['user_photo'] = $user->getPhotoUrl();
      $data['session_name'] = Zend_Session::getOptions('name');
      $data['session_id'] = Zend_Session::getId();

      $this->_initOpenfireAccount($user->user_id, $this->_getParam('password'));
      $this->_jsonSuccessOutput($data);
    } else {
      $this->_jsonErrorOutput($result->error);
    }
  }

  public function logoutAction()
  {
    // Check if already logged out
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ALREADY_LOG_OUT);
    }

    // Run logout hook
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserLogoutBefore', $viewer);

    // Test activity @todo remove
    Engine_Api::_()->getDbtable('actions', 'activity')
            ->addActivity($viewer, $viewer, 'logout');

    // Update online status
    $onlineTable = Engine_Api::_()->getDbtable('online', 'user')
            ->delete(array(
        'user_id = ?' => $viewer->getIdentity(),
    ));

    // Logout
    Engine_Api::_()->user()->getAuth()->clearIdentity();

    if (!empty($_SESSION['login_id'])) {
      Engine_Api::_()->getDbtable('logins', 'user')->update(array(
          'active' => false,
              ), array(
          'login_id = ?' => $_SESSION['login_id'],
      ));
      unset($_SESSION['login_id']);
    }


    // Run logout hook
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserLogoutAfter', $viewer);

//    $doRedirect = true;
    // Clear twitter/facebook session info
    // facebook api
    $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
    $facebook = $facebookTable->getApi();
    $settings = Engine_Api::_()->getDbtable('settings', 'core');
    if ($facebook && 'none' != $settings->core_facebook_enable) {
//      if (method_exists($facebook, 'getAccessToken') &&
//              ($access_token = $facebook->getAccessToken())) {
//        $doRedirect = false; // javascript will run to log them out of fb
//        $this->view->appId = $facebook->getAppId();
//        $access_array = explode("|", $access_token);
//        if (($session_key = $access_array[1])) {
//          $this->view->fbSession = $session_key;
//        }
//      }
      try {
        $facebook->clearAllPersistentData();
      } catch (Exception $e) {
        // Silence
      }
    }

    unset($_SESSION['facebook_lock']);
    unset($_SESSION['facebook_uid']);

    unset($_SESSION['twitter_lock']);
    unset($_SESSION['twitter_token']);
    unset($_SESSION['twitter_secret']);
    unset($_SESSION['twitter_token2']);
    unset($_SESSION['twitter_secret2']);

    // Response
    $this->_jsonSuccessOutput(array(
        'message' => Zend_Registry::get('Zend_Translate')->_('You are now logged out.')
    ));
  }

  public function registerUserAction()
  {
    $struct = Engine_Api::_()->fields()->getFieldsStructureFull('user', 1, 1);
    $processingFields = include_once APPLICATION_PATH .
      '/application/modules/Zulu/settings/user-import-fields.php';

    if ($this->getRequest()->isPost()) {

      // Get post params
      $params = $this->getRequest()->getPost();

      // Define required fields
      $requiredFields = array('safa_code', 'first_name', 'last_name', 'email', 'password', 'phone_number');

      // Check if the request comes from safa app
      if (!array_key_exists('safa_code', $params) || $params['safa_code'] !== $this->_safaCode) {
        $this->_jsonErrorOutput("Invalid request!");
      }

      // Check if data is valid
      if (!is_numeric($params['phone_number'])) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
      }

      foreach ($requiredFields as $field) {
        if (!array_key_exists($field, $params)) {
          $this->_jsonErrorOutput("Missing param: {$field}");
        }
      }

      // Initialize field values table
      $fieldValuesTable = Engine_Api::_()->fields()->getTable('user', 'values');

      $userTable = Engine_Api::_()->getItemTable('user');
      $userSelect = $userTable->select()->from($userTable)
        ->orWhere('phone_number = ?', $params['phone_number'])
        ->orWhere('email = ?', $params['email']);

      // Check if the user already existed
      $user = $userTable->fetchRow($userSelect);

      if (null !== $user) {
        $this->_jsonErrorOutput('User already existed!');
      }
      // Create user
      $user = Engine_Api::_()->getDbtable('users', 'user')->createRow();
      $user->email = $params['email'];
      $user->phone_number = $params['phone_number'];
      $user->password = $params['password'];
      $user->save();

      /* @var $map Fields_Model_Map */
      /* @var $field Fields_Model_Meta */
      foreach ($processingFields as $alias => $fskey) {
        if (array_key_exists($fskey, $struct)) {
          $parts = explode('_', $fskey);
          if (count($parts) != 3) {
            continue;
          }
          // Only process first name and last name
          if ($alias !== 'first_name' && $alias !== 'last_name') {
            continue;
          }
          $value = $params[$alias];
          // Extract field parts
          list ($parent_id, $option_id, $field_id) = $parts;

          $valueRow = $fieldValuesTable->createRow();
          $valueRow->field_id = $field_id;
          $valueRow->item_id = $user->getIdentity();

          $valueRow->value = htmlspecialchars($value);
          $valueRow->privacy = 'everyone';
          $valueRow->save();
        }
      }
      // Update user
      $user->displayname = $params['first_name'] . ' ' . $params['last_name'];
      $user->enabled = 1;
      $user->verified = 1;
      $user->save();

      $this->_jsonSuccessOutput(array(
        'message' => 'User created!',
        'user_id' => $user->getIdentity()
      ));
    } else {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }
  }

  public function updateUserAction() {
    if($this->getRequest()->isPost()) {
      // Get post params
      $params = $this->getRequest()->getPost();

      // Check if the request comes from safa app
      if (!array_key_exists('safa_code', $params) || $params['safa_code'] !== $this->_safaCode) {
        $this->_jsonErrorOutput("Invalid request!");
      }

      // Look up user
      $userTable = Engine_Api::_()->getItemTable('user');
      // Get data from params
      $identity = $params['username'];
      $password = $params['password'];

      // Password cannot be empty
      if (empty($password)) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
      }

      $userSelect = $userTable->select()
        ->from($userTable);

      if (is_numeric($identity)) {
        $userSelect->where('`phone_number` = ?', $identity);
      } else {
        $userSelect->where('`email` = ?', $identity);
      }
      $user = $userTable->fetchRow($userSelect);

      if(!$user) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::USER_NOT_FOUND);
      }
      $user->password = $password;
      $user->save();

      $this->_jsonSuccessOutput(array(
        'message' => 'User updated!',
        'user_id' => $user->getIdentity()
      ));
    } else {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }
  }

  public function showFeedsAction()
  {
    // check public settings
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.portal', 1);
    if (!$require_check) {
      if (!$this->_helper->requireUser()->isValid()) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
      }
    }

    if (!Engine_Api::_()->user()->getViewer()->getIdentity()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    // Get deletable option
    $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');
    $allow_delete = $coreSettingsApi->getSetting('activity_userdelete');
    $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');

    $subject = null;
    if ($this->_getParam('user_id')) {
      $subject = Engine_Api::_()->getItem('user', (int) $this->_getParam('user_id'));
      if (!$subject->user_id) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::USER_NOT_FOUND);
      }
    } elseif ($this->_getParam('subject')) {
      $subject = Engine_Api::_()->getItemByGuid($this->_getParam('subject'));
    }
    $request = Zend_Controller_Front::getInstance()->getRequest();

    $length = $request->getParam('limitNumber');

    if (empty($length)) {
      $length = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.length', 15);
    }

    if ($length > 50) {
      $length = 50;
    }

    $actionTable = Engine_Api::_()->getDbtable('actions', 'advancedactivity');
    $itemActionLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.userlength', 5);

    $viewAllComments = $request->getParam('viewAllComments', $request->getParam('show_comments', false));

    // Get config options for activity
    $config = array(
        'action_id' => (int) $request->getParam('action_id'),
        'max_id' => (int) $request->getParam('maxid'),
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

    $hideItems = array();
    if (empty($subject)) {
      if ($viewer->getIdentity()) {
        $hideItems = Engine_Api::_()->getDbtable('hide', 'advancedactivity')->getHideItemByMember($viewer);
      }
    }

    $grouped_actions = array();

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

          // skip the hide actions and content        
          if (!empty($hideItems)) {
            if (isset($hideItems[$action->getType()]) && in_array($action->getIdentity(), $hideItems[$action->getType()])) {
              continue;
            }
            if (!$action->getTypeInfo()->is_object_thumb && isset($hideItems[$action->getSubject()->getType()]) && in_array($action->getSubject()->getIdentity(), $hideItems[$action->getSubject()->getType()])) {
              continue;
            }
            if (($action->getTypeInfo()->is_object_thumb || $action->getObject()->getType() == 'user' ) && isset($hideItems[$action->getObject()->getType()]) && in_array($action->getObject()->getIdentity(), $hideItems[$action->getObject()->getType()])) {
              continue;
            }
          }

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

          /* Start Working group feed. */
          if (!empty($action->getTypeInfo()->is_grouped) && isset($action->getTypeInfo()->is_grouped)) {
            if ($action->type == 'friends') {
              $object_guid = $action->getSubject()->getGuid();
              $total_guid = $action->type . '_' . $object_guid;

              if (!isset($grouped_actions[$total_guid])) {
                $grouped_actions[$total_guid] = array();
              }
              $grouped_actions[$total_guid][] = $action->getObject();
            } elseif ($action->type == 'tagged') {
              foreach ($action->getAttachments() as $attachment) {
                $object_guid = $attachment->item->getGuid();
                $Subject_guid = $action->getSubject()->getGuid();
                $total_guid = $action->type . '_' . $object_guid . '_' . $Subject_guid;
              }
              if (!isset($grouped_actions[$total_guid])) {
                $grouped_actions[$total_guid] = array();
              }
              $grouped_actions[$total_guid][$action->getObject()->getGuid()] = $action->getObject();
            } else {
              $object_guid = $action->getObject()->getGuid();
              $total_guid = $action->type . '_' . $object_guid;

              if (!isset($grouped_actions[$total_guid])) {
                $grouped_actions[$total_guid] = array();
              }
              $grouped_actions[$total_guid][] = $action->getSubject();
            }

            if (count($grouped_actions[$total_guid]) > 1) {
              continue;
            }
          }
          /* End Working group feed. */

          // Refined attachments array to reduce json payload
          $refinedAttachments = array();
          // remove items with disabled module attachments
          try {
            // Should be refactored in future
            $attachments = $action->getAttachments();

            if ($action->attachment_count > 0 && count($attachments) > 0) {
              $i = 0;

              foreach ($attachments as $attachment) {
                $videoType = 0;
                $item = $attachment->item;
                $refinedAttachments[$i] = array();

                // Get rich content type
                if (count($attachments) == 1 && null != $this->view->getRichContent($item)) {
                  if (isset($item->type)) {
                    $videoType = 1;
                  } else {
                    continue;
                  }
                  $video_location = $this->_helper->videoAPI->getVideoLocation($item);
                }

                $refinedAttachments[$i]['attachment_type'] = $attachment->meta->type;
                $refinedAttachments[$i]['attachment_core_link'] = $item->getHref();

                if ($refinedAttachments[$i]['attachment_type'] == 'activity_action') {
                  $actor = (isset($item->getTypeInfo()->is_object_thumb) && !empty($item->getTypeInfo()->is_object_thumb)) ? $item->getObject() : $item->getSubject();
                  if ($actor instanceof Core_Model_Item_Abstract) {
                    // attachment actor info
                    $refinedAttachments[$i]['attachment_actor_info'] = array();
                    $refinedAttachments[$i]['attachment_actor_info']['actor_id'] = $actor->getIdentity();
                    $refinedAttachments[$i]['attachment_actor_info']['actor_name'] = $actor->getTitle();

                    // attachment checkin info
                    if (isset($item->params['checkin']) && is_array($item->params['checkin'])) {
                      $refinedAttachments[$i]['attachment_checkin_info'] = array();
                      $refinedAttachments[$i]['attachment_checkin_info']['address'] = $item->params['checkin']['label'];
                      $refinedAttachments[$i]['attachment_checkin_info']['latitude'] = $item->params['checkin']['latitude'];
                      $refinedAttachments[$i]['attachment_checkin_info']['longitude'] = $item->params['checkin']['longitude'];
                    }
                  }
                } else if ($action->type == 'share') {
                  // attachment actor info
                  $refinedAttachments[$i]['attachment_actor_info'] = array();
                  $refinedAttachments[$i]['attachment_actor_info']['actor_id'] = $action->getObject()->getIdentity();
                  $refinedAttachments[$i]['attachment_actor_info']['actor_name'] = $action->getObject()->getTitle();
                }

//                array(
//                    'attachmentType' => $attachment->meta->type,
//                    'videoType' => $videoType,
//                    'mode' => $attachment->meta->mode,
//                );
                // if attachment is video type
                if ($videoType) {
                  if ($item->photo_id) {
                    $thumb = $item->getPhotoUrl('thumb.video.activity');
                  } else {
                    $thumb = Zend_Registry::get('StaticBaseUrl') . 'application/modules/Video/externals/images/video.png';
                  }

//                  if ($item->duration) {
//                    if ($item->duration >= 3600) {
//                      $duration = gmdate("H:i:s", $item->duration);
//                    } else {
//                      $duration = gmdate("i:s", $item->duration);
//                    }
//                    $duration = ltrim($duration, '0:');
//                  }
                  $duration = $item->duration;

                  if ($item->description) {
                    $tmpBody = strip_tags($item->description);
                    $description = (Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody);
                  }

                  $refinedAttachments[$i]['attachment_id'] = $item->getIdentity();
                  $refinedAttachments[$i]['attachment_title'] = $item->title;
                  $refinedAttachments[$i]['attachment_description'] = $description ? $description : '';
                  $refinedAttachments[$i]['attachment_thumbnail_photo_url'] = $thumb ? $thumb : '';
                  $refinedAttachments[$i]['attachment_detail_photo_url'] = '';
                  $refinedAttachments[$i]['attachment_video_url'] = $video_location;
                  $refinedAttachments[$i]['attachment_video_duration'] = $duration ? $duration : '';
                  $refinedAttachments[$i]['attachment_video_type'] = $this->_helper->videoAPI->getMobileVideoType($item);
//                  $refinedAttachments[$i]['link'] = $item->getHref();
                }
                // if other types
                else {
                  $meta_mode = $attachment->meta->mode;
                  // not sure why
                  if ($meta_mode == 0 || $meta_mode == 4) {
                    continue;
                  }

                  // Get attachment general info
                  $refinedAttachments[$i]['attachment_id'] = $item->getIdentity();
                  $refinedAttachments[$i]['attachment_title'] = $item->getTitle() ? $item->getTitle() : '';
                  $refinedAttachments[$i]['attachment_description'] = strip_tags($item->getDescription());
                  $refinedAttachments[$i]['attachment_video_url'] = '';
                  $refinedAttachments[$i]['attachment_video_duration'] = '';
                  $refinedAttachments[$i]['attachment_thumbnail_photo_url'] = (string) $item->getPhotoUrl('thumb.feed');
                  $refinedAttachments[$i]['attachment_detail_photo_url'] = (string) $item->getPhotoUrl();

                  // Get attachment likers
                  if (method_exists($item, 'likes')) {
                    $refinedAttachments[$i]['attachment_like_info'] = $this->_helper->commonAPI->getLikeInfo($item, $viewer);
                  }

                  // Get comment count
                  if (method_exists($item, 'comments')) {
                    $refinedAttachments[$i]['attachment_comment_count'] = $item->comments()->getCommentCount();
                  }
                }

                // Get photo permissions
                $permission_info = array();
                if (preg_match('/photo/', $item->getType()) && method_exists($item, 'getAlbum')) {
                  $album = $item->getAlbum();
                  $permission_info['can_edit'] = (int) $album->authorization()->isAllowed($viewer, 'edit');
                  $permission_info['can_delete'] = (int) $album->authorization()->isAllowed($viewer, 'delete');
                  $permission_info['can_comment'] = (int) $item->authorization()->isAllowed($viewer, 'comment');
                  $permission_info['can_share'] = 1;
                }
                $refinedAttachments[$i]['attachment_permission_info'] = (object) $permission_info;

                $i++;
              }
            }
            // Should be refactored in future
          } catch (Exception $e) {
            // if a module is disabled, getAttachments() will throw an Engine_Api_Exception; catch and continue
            continue;
          }

          try {
            // Get refined comments of the feed
            $refinedComments = $this->_helper->feedAPI->getRefinedComments($action, $viewAllComments, $viewer);
          } catch (Exception $e) {
            continue;
          }

          // add to list
          $activity_count = count($activity);
          if ($activity_count < $length) {
            $activity[$activity_count]['feed_id'] = $action->action_id;
            $activity[$activity_count]['feed_type'] = $action->type;
            $activity[$activity_count]['created_date'] = $action->date;

            $activity[$activity_count]['body'] = array();
            $activity[$activity_count]['body']['links'] = array();
            $activity[$activity_count]['body']['is_contain_link'] = 0;
            // Process action body
            if (class_exists('DOMDocument') && $action->body) {
              $dom = new Zend_Dom_Query($action->body);

              if ($dom) {
                $aQuery = $dom->query('a');

                if (count($aQuery) > 0) {
                  $activity[$activity_count]['body']['is_contain_link'] = 1;
                  foreach ($aQuery as $link) {
                    $activity[$activity_count]['body']['links'][] = $link->getAttribute('href');
                  }
                }
              }
            }
            // Just to make sure that we won't output the unprocessed message to phone app users
            if (preg_match('/\{|\}/', $action->body)) {
              $activity[$activity_count]['body']['message'] = '';
            } else {
              $activity[$activity_count]['body']['message'] = strip_tags($action->body);
            }

            $activity[$activity_count]['shareable'] = (int) ($action->getTypeInfo()->shareable && $action->shareable);
            $activity[$activity_count]['privacy'] = $action->privacy;
            $activity[$activity_count]['user_agent'] = $action->user_agent;

            $activity[$activity_count]['allow_delete'] = (int) ($activity_moderate ||
                    ($allow_delete && (('user' == $action->subject_type && $this->view->viewer()->getIdentity() == $action->subject_id) || ('user' == $action->object_type && $this->view->viewer()->getIdentity() == $action->object_id))));

            // get actor info
            $actor = (isset($action->getTypeInfo()->is_object_thumb) && !empty($action->getTypeInfo()->is_object_thumb)) ? $action->getObject() : $action->getSubject();
            $activity[$activity_count]['actor_info'] = array();
            $activity[$activity_count]['actor_info']['actor_id'] = $actor->getIdentity();
            $activity[$activity_count]['actor_info']['actor_type'] = $actor->getType();
            $activity[$activity_count]['actor_info']['actor_photo'] = $actor->getPhotoUrl('thumb.icon');
            $activity[$activity_count]['actor_info']['actor_name'] = $actor->getTitle();

            // get top message
            $tmp_body = $action->body;
            $action->body = '';
            // remove message body from top message
            $activity[$activity_count]['top_message_info']['plain_text'] = preg_replace('/\r\n$/', '', $this->_helper->feedAPI->getTopMessage($action));
            $action->body = $tmp_body;
            // remove body part from template
            $activity[$activity_count]['top_message_info']['template'] = preg_replace('/[\r]*[\n]*' . preg_quote("{body:\$body}") . '/', '', $action->getTypeInfo()->body);

            // get top message object info
            $object = $action->getObject();
            $activity[$activity_count]['top_message_info']['target_object_info'] = array();
            $activity[$activity_count]['top_message_info']['target_object_info']['object_id'] = $object->getIdentity();
            $activity[$activity_count]['top_message_info']['target_object_info']['object_type'] = $object->getType();
            $activity[$activity_count]['top_message_info']['target_object_info']['object_photo'] = $object->getPhotoUrl('thumb.icon');
            $activity[$activity_count]['top_message_info']['target_object_info']['object_name'] = $object->getTitle();

            // tagged users info
            $tagContent = Engine_Api::_()->advancedactivity()->getTag($action)->toArray();
            $activity[$activity_count]['tagged_items'] = array();
            if (!empty($tagContent)) {
//              $activity[$activity_count]['tagged_info'] = array();
              $tagged_count = 0;
              foreach ($tagContent as $tag) {
                $taggedItem = Engine_Api::_()->getItem($tag['tag_type'], $tag['tag_id']);
                $activity[$activity_count]['tagged_items'][$tagged_count]['tagged_item_id'] = $tag['tag_id'];
                $activity[$activity_count]['tagged_items'][$tagged_count]['tagged_item_type'] = $tag['tag_type'];
                $activity[$activity_count]['tagged_items'][$tagged_count]['tagged_item_name'] = $taggedItem->getTitle();
                $tagged_count++;
              }
            }

            // checkin info
            if (isset($action->params['checkin']) && is_array($action->params['checkin'])) {
              $activity[$activity_count]['checkin_info'] = array();
              $activity[$activity_count]['checkin_info']['address'] = $action->params['checkin']['label'];
              $activity[$activity_count]['checkin_info']['latitude'] = $action->params['checkin']['latitude'];
              $activity[$activity_count]['checkin_info']['longitude'] = $action->params['checkin']['longitude'];
            }

            // like info
            $activity[$activity_count]['like_info'] = $this->_helper->feedAPI->getLikeInfo($action, $viewer);

            // attachment_info
            $activity[$activity_count]['attachment_info'] = array();
            $activity[$activity_count]['attachment_info']['attachment_count'] = $action->attachment_count;
            $activity[$activity_count]['attachment_info']['attachment_objects'] = $refinedAttachments;

            // comment info
            $activity[$activity_count]['comment_info'] = array();
            $activity[$activity_count]['comment_info']['comment_count'] = count($refinedComments);
            $canComment = Engine_Api::_()->authorization()->isAllowed($action->getCommentObject(), null, 'comment');
            $activity[$activity_count]['comment_info']['commentable'] = (int) ($action->getTypeInfo()->commentable && $action->commentable && $canComment);
            $activity[$activity_count]['comment_info']['comments'] = $refinedComments;

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

    if (count($activity) < $length || count($activity) <= 0) {
      $endOfFeed = true;
    }

    $data = array(
        'activity' => $activity,
        'activityCount' => count($activity),
        'nextid' => $nextid,
//        'firstid' => $firstid,
        'endOfFeed' => (int) $endOfFeed,
//        'viewer' => $viewer,
//        'subject' => $subject,
    );

    $this->_jsonSuccessOutput($data);
  }

  public function postFeedAction()
  {
    set_time_limit(0);

    // Make sure user exists
    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    // Get subject if necessary
    $strName = str_replace('www.', '', $_SERVER['HTTP_HOST']);
    $viewer = Engine_Api::_()->user()->getViewer();
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

    // Make form
    $form = new Activity_Form_Post();
    // Check auth
    if (!$this->_helper->feedAPI->checkPostFeedAuth()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    $getStrLen = strlen($strName);
    $getComposerValue = 0;
    if ($getStrLen > $strLimit) {
      $strName = substr($strName, 0, $strLimit);
    }

    // Check if post
    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }

    // Check if form is valid
    $postData = $this->getRequest()->getPost();
    $body = @$postData['body'];
    $privacy = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.content', 'everyone');
    $elementView = Engine_Api::_()->getApi('settings', 'core')->getSetting('aaf.get.element.view', 0);
    if (isset($postData['auth_view'])) {
      $privacy = @$postData['auth_view'];
    }
    $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
    $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
    $category_id = 0;
    if (isset($postData['category_id'])) {
      $category_id = @$postData['category_id'];
    }
    //$body = htmlentities($body, ENT_QUOTES, 'UTF-8');
    $postData['body'] = $body;

    if (!$form->isValid($postData)) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
    }

    $composerDatas = $this->getRequest()->getParam('composer', null);
    // Check one more thing
    if ($form->body->getValue() === '' && $form->getValue('attachment_type') === '' && (!isset($postData['composer']['checkin']) || empty($postData['composer']['checkin']) )) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
    }
    if (empty($elementView)) {
      for ($str = 0; $str < strlen($strName); $str++) {
        $getComposerValue += ord($strName[$str]);
      }
    }

    Engine_Api::_()->getApi('settings', 'core')->setSetting('aaf.list.view.value', $getComposerValue);
    // set up action variable
    $action = null;

    // Process
    $db = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getAdapter();
    $db->beginTransaction();

    try {
      // Try attachment getting stuff
      $attachment = null;

      $attachmentType = $this->getRequest()->getPost('attachmentType');

      $attachmentData = null;

      if ($attachmentType == 'video') {
        $attachmentData = $this->_processVideo();
      } else if ($attachmentType == 'photo') {
        $attachmentData = $this->_processPhoto();
      } else if ($attachmentType == 'link') {
        $attachmentData = $this->_processLink();
      }

      if (!empty($attachmentData)) {
        $attachmentData['type'] = $attachmentType;

        if (Engine_Api::_()->core()->hasSubject('sitepage_page')) {
          $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
          if ($sitepage) {
            if ($attachmentType == 'photo') {
              $attachmentData['type'] = 'sitepagephoto';
            } elseif ($attachmentType == 'video') {
              $attachmentData['type'] = 'sitepagevideo';
            }
          }
        }
      }

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

          for ($i = 1; $i < count($typeExplode); $i++) {
            $typeExplode[$i] = ucfirst($typeExplode[$i]);
          }

          $type = implode("", $typeExplode);
          $plugin = Engine_Api::_()->loadClass($config['plugin']);
          $method = 'onAttach' . ucfirst($type);
          $attachment = $plugin->$method($attachmentData);
        }
      }


      // Get body
      $body = $form->getValue('body');
      $body = preg_replace('/<br[^<>]*>/', "\n", $body);

      // Is double encoded because of design mode
      //$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
      //$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
      //$body = htmlentities($body, ENT_QUOTES, 'UTF-8');
      // Special case: status
      //CHECK IF BOTH FACEBOOK AND TWITTER IS DISABLED.
      $web_values = Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.fb.twitter', 0);
      $currentcontent_type = 1;

      if (isset($_POST['activity_type'])) {
        $currentcontent_type = $_POST['activity_type'];
      }

      if (($currentcontent_type == 1)) {
        $showPrivacyDropdown = in_array('userprivacy', Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedactivity.composer.options', array("withtags", "emotions", "userprivacy")));

        if ($viewer->isSelf($subject) && $showPrivacyDropdown) {
          Engine_Api::_()->getDbtable('userSettings', 'seaocore')->setSetting($viewer, "aaf_post_privacy", $privacy);
        } elseif (!$viewer->isSelf($subject)) {
          $privacy = null;
        }
        $activityTable = Engine_Api::_()->getDbtable('actions', 'advancedactivity');

        // No attachment
        if (!$attachment && $viewer->isSelf($subject)) {
          $type = 'status';
          if ($body != '') {
            $viewer->status = $body;
            $viewer->status_date = date('Y-m-d H:i:s');
            $viewer->save();

            $viewer->status()->setStatus($body);
          }
          if (isset($postData['composer']['checkin']) && !empty($postData['composer']['checkin'])) {
            if ($body != '') {
              $type = 'sitetagcheckin_status';
            } else {
              $type = 'sitetagcheckin_checkin';
            }
          }

          $action = $activityTable->addActivity($viewer, $subject, $type, $body, $privacy, array('aaf_post_category_id' => $category_id));
        } else {
          // General post
          $type = 'post';
          if (isset($postData['composer']['checkin']) && !empty($postData['composer']['checkin'])) {
            $type = 'sitetagcheckin_post';
          }
          if ($viewer->isSelf($subject)) {
            $type = 'post_self';
            if (isset($postData['composer']['checkin']) && !empty($postData['composer']['checkin'])) {
              $type = 'sitetagcheckin_post_self';
            }
            if ($type == 'post_self') {
              $attachment_media_type = $attachment->getMediaType();
              if ($attachment_media_type == 'image') {
                $attachment_media_type = 'photo';
              } else if ($attachment_media_type == 'item') {
                $attachment_type = $attachment->getType();
                if (strpos($attachment_type, 'music') !== false || strpos($attachment_type, 'song') !== false) {
                  $attachment_media_type = 'music';
                }
              }

              $tempType = $type . "_" . $attachment_media_type;
              $typeInfo = Engine_Api::_()->getDbtable('actions', 'activity')->getActionType($tempType);

              if ($typeInfo && $typeInfo->enabled) {
                $type = $tempType;
              }
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
            if (Engine_Api::_()->sitepage()->isPageOwner($subject) && Engine_Api::_()->sitepage()->isFeedTypePageEnable()) {
              $activityFeedType = 'sitepage_post_self';
            } elseif ($subject->all_post || Engine_Api::_()->sitepage()->isPageOwner($subject)) {
              $activityFeedType = 'sitepage_post';
            }

            if ($activityFeedType) {
              $action = $activityTable->addActivity($viewer, $subject, $activityFeedType, $body, null, null);
              Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
            }
          } else if ($subject->getType() == "sitebusiness_business") {
            $activityFeedType = null;

            if (Engine_Api::_()->sitebusiness()->isBusinessOwner($subject) && Engine_Api::_()->sitebusiness()->isFeedTypeBusinessEnable()) {
              $activityFeedType = 'sitebusiness_post_self';
            } elseif ($subject->all_post || Engine_Api::_()->sitebusiness()->isBusinessOwner($subject)) {
              $activityFeedType = 'sitebusiness_post';
            }

            if ($activityFeedType) {
              $action = $activityTable->addActivity($viewer, $subject, $activityFeedType, $body, null, null);
              Engine_Api::_()->getApi('subCore', 'sitebusiness')->deleteFeedStream($action);
            }
          } elseif ($subject->getType() == "sitegroup_group") {
            $activityFeedType = null;

            if (Engine_Api::_()->sitegroup()->isGroupOwner($subject) && Engine_Api::_()->sitegroup()->isFeedTypeGroupEnable()) {
              $activityFeedType = 'sitegroup_post_self';
            } elseif ($subject->all_post || Engine_Api::_()->sitegroup()->isGroupOwner($subject)) {
              $activityFeedType = 'sitegroup_post';
            }

            if ($activityFeedType) {
              $action = $activityTable->addActivity($viewer, $subject, $activityFeedType, $body, null, null);
              Engine_Api::_()->getApi('subCore', 'sitegroup')->deleteFeedStream($action);
            }
          } elseif ($subject->getType() == "sitestore_store") {
            $activityFeedType = null;
            if (Engine_Api::_()->sitestore()->isStoreOwner($subject) && Engine_Api::_()->sitestore()->isFeedTypeStoreEnable()) {
              $activityFeedType = 'sitestore_post_self';
            } elseif ($subject->all_post || Engine_Api::_()->sitestore()->isStoreOwner($subject)) {
              $activityFeedType = 'sitestore_post';
            }

            if ($activityFeedType) {
              $action = $activityTable->addActivity($viewer, $subject, $activityFeedType, $body, null, null);
              Engine_Api::_()->getApi('subCore', 'sitestore')->deleteFeedStream($action);
            }
          } elseif ($subject->getType() == "siteevent_event") {
            $activityFeedType = Engine_Api::_()->siteevent()->getActivtyFeedType($subject, 'siteevent_post');

            $action = $activityTable->addActivity($viewer, $subject, $activityFeedType, $body, null, null);
          } else {
            $action = $activityTable->addActivity($viewer, $subject, $type, $body, $privacy, array('aaf_post_category_id' => $category_id));
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

        $composerDatas = $this->getRequest()->getParam('composer', null);
        if ($action && !empty($composerDatas)) {
          foreach ($composerDatas as $composerDataType => $composerDataValue) {
            if (empty($composerDataValue)) {
              continue;
            }
            foreach (Zend_Registry::get('Engine_Manifest') as $data) {
              if (isset($data['composer'][$composerDataType]['plugin']) && !empty($data['composer'][$composerDataType]['plugin'])) {
                $pluginClass = $data['composer'][$composerDataType]['plugin'];
                $plugin = Engine_Api::_()->loadClass($pluginClass);
                $method = 'onAAFComposer' . ucfirst($composerDataType);

                if (method_exists($plugin, $method)) {
                  $plugin->$method(array($composerDataType => $composerDataValue), array('action' => $action));
                }
              }
            }
          }

          // Tag and post location
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
              $locationLink = $view->htmlLink('https://maps.google.com/?q=' . urlencode($label), $label, array('target' => '_blank'));
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

      $publishMessage = html_entity_decode($form->getValue('body'));
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

      if (isset($postData['composer']['checkin']) && !empty($postData['composer']['checkin'])) {
        $checkinArray = array();
        parse_str($postData['composer']['checkin'], $checkinArray);
        if (!empty($publishMessage)) {
          $publishMessage = $publishMessage . ' - ' . $this->view->translate('at') . ' ' . $checkinArray['label'];
        } else {
          $publishMessage = '- ' . $this->view->translate('was at') . ' ' . $checkinArray['label'];
        }
      }

      // Publish to facebook, if checked & enabled
      if ((($currentcontent_type == 3) || isset($_POST['post_to_facebook']))) {
        try {

          $session = new Zend_Session_Namespace();

          $facebookApi = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();

          if ($facebookApi && Seaocore_Api_Facebook_Facebookinvite::checkConnection(null, $facebookApi)) {
            //ADD CHECKIN LOCATION TEXT ALSO.IF CHECKED IN.

            $fb_data = array(
                'message' => strip_tags($publishMessage),
            );
            if ($publishUrl) {
              if (isset($_POST['attachment'])) {
                $fb_data['link'] = $publishUrl;
              }
//              if ($attachment && $currentcontent_type == 3) {
//                $fb_data['link'] = $attachment->uri;
//              }
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

            $subjectPostFBArray = array('sitepage_page', 'sitebusiness_business', 'sitegroup_group', 'sitestore_store');

            if ($subject && in_array($subject->getType(), $subjectPostFBArray)) {
              $publish_fb_array = array('0' => 1, '1' => 2);
              $fb_publish = Engine_Api::_()->getApi('settings', 'core')->getSetting(strtolower($subject->getModuleName()) . '.publish.facebook', serialize($publish_fb_array));
              if (!empty($fb_publish) && !is_array($fb_publish)) {
                $fb_publish = unserialize($fb_publish);
              }
              if (((isset($_POST['post_to_facebook_profile']) && $_POST['post_to_facebook_profile'] == 'true') || (!isset($_POST['post_to_facebook_profile']) && !empty($fb_publish) && $fb_publish[(count($fb_publish) - 1)] == 2))) {
                $res = $facebookApi->api('/me/feed', 'POST', $fb_data);
              }
            } else {
              $res = $facebookApi->api('/me/feed', 'POST', $fb_data);
            }

            if ($subject && isset($subject->fbpage_url) && !empty($subject->fbpage_url)) {
              //explode the subject type
              $subject_explode = explode("_", $subject->getType());
              $subjectFbPostSettingVar = $subject_explode[0] . '.post' . $subject_explode[1];
              //EXTRACTING THE PAGE ID FROM THE PAGE URL.
              $url_expload = explode("?", $subject->fbpage_url);
              $url_expload = explode("/", $url_expload[0]);
              $count = count($url_expload);
              $page_id_string = '';
              for ($i = $count - 1; $i >= 0; $i--) {

                if (!empty($url_expload[$i]) && empty($page_id_string)) {
                  $page_id_string = $url_expload[$i];
                }
                if (is_numeric($url_expload[$i])) {
                  $page_id = $url_expload[$i];
                  break;
                }
              }
              if (empty($page_id)) {
                $page_id = $page_id_string;
              }

              //$manages_pages = $facebookApi->api('/me/accounts', 'GET');
              //NOW IF THE USER WHO IS COMENTING IS OWNER OF THIS FACEBOOK PAGE THEN GETTING THE PAGE ACCESS TOKEN TO WITH THIS SITE PAGE IS INTEGRATED.

              if (in_array($subject->getType(), $subjectPostFBArray) && (isset($_POST['post_to_facebook_page']) && $_POST['post_to_facebook_page'] == 'true') && Engine_Api::_()->getApi('settings', 'core')->getSetting($subjectFbPostSettingVar, 1) && !empty($fb_publish) && $fb_publish[0] == 1) {
                if ($subject->getType() != 'sitegroup_group') {
                  $pageinfo = $facebookApi->api('/' . $page_id . '?fields=access_token', 'GET');
                  if (isset($pageinfo['access_token']))
                    $fb_data['access_token'] = $pageinfo['access_token'];
                  $fb_data['message'] = $fb_data['message'] . '
                  ' . $this->view->translate('Posted via') . ' ' . (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . _ENGINE_R_BASE;
                } else {
                  if (!is_numeric($page_id) && isset($subject->fbpage_title)) {
                    //GET THE NUMERIC ID OF GROUP.
                    if (!empty($subject->fbpage_title))
                      $page_id = trim($subject->fbpage_title);
                    $group_info = $facebookApi->api('/search?q=' . urlencode($page_id) . '&type=group', 'GET');
                    if (!empty($group_info) && isset($group_info['data']) && isset($group_info['data']['0'])) {
                      $page_id = $group_info['data']['0']['id'];
                    }
                  }
                }
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
                if (isset($_POST['attachment'])) {
                  $shortURL = Engine_Api::_()->getApi('Bitly', 'seaocore')->get_bitly_short_url((_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $attachment->getHref(), $login, $appkey, $format = 'txt');
                  $BitlayLength = strlen($shortURL);
                } else {
                  $BitlayLength = 0;
                  $shortURL = '';
                }
                $twitterFeed = substr(html_entity_decode($_POST['body']), 0, (140 - ($BitlayLength + 1))) . ' ' . $shortURL;
              } else
                $twitterFeed = html_entity_decode($_POST['body']);
            }
            else {
              $twitterFeed = substr(html_entity_decode($_POST['body']), 0, 137) . '...';
            }
            if ((empty($twitterFeed) || !isset($_POST['attachment'])) && !empty($publishMessage))
              $twitterFeed = substr($publishMessage, 0, 137) . '...';

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
            if ($attachment) {
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
            }
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

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput($e->getMessage());
    }

    // Check if action was created
    $post_fail = 0;
    if ($currentcontent_type == 1 && !$action) {
      $post_fail = 1;
    }
    $feed_stream = "";
    $last_id = 0;
    if ($action) {
      $feed_stream = $this->view->advancedActivity($action, array('onlyactivity' => true));
      $last_id = $action->getIdentity();
    }

    $this->_jsonSuccessOutput(array(
        'post_fail' => $post_fail,
        'last_id' => $last_id
    ));
  }

  protected function _processVideo()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }

    // Upload video
    if (isset($_GET['ul']) || (isset($_FILES['Filedata']) && !empty($_FILES['Filedata']['name']))) {
      return $this->_uploadVideo();
    }

    $video_title = $this->_getParam('title');
    $video_url = $this->_getParam('uri');
    $video_type = $this->_getParam('type');
    $composer_type = $this->_getParam('c_type', 'wall');

    try {
      // extract code
      if ($video_type != Ynvideo_Plugin_Factory::getUploadedType()) {
        $adapter = Ynvideo_Plugin_Factory::getPlugin((int) $video_type);
        $adapter->setParams(array('link' => $video_url));
        $valid = $adapter->isValid();
      }
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }

    // check to make sure the user has not met their quota of # of allowed video uploads
    // set up data needed to check quota
    $values['user_id'] = $viewer->getIdentity();
    $paginator = Engine_Api::_()->getApi('core', 'ynvideo')->getVideosPaginator($values);
    //$quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'max');
    // TODO [DangTH] : get the max value
    $quota = Engine_Api::_()->ynvideo()->getAllowedMaxValue('video', $viewer->level_id, 'max');
    $current_count = $paginator->getTotalItemCount();

    if (($current_count >= $quota ) && !empty($quota)) {
      // return error message
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::MAX_VIDEO_UPLOADED);
    } else if ($valid) {
      $db = Engine_Api::_()->getDbtable('videos', 'ynvideo')->getAdapter();
      $db->beginTransaction();

      try {
        $table = Engine_Api::_()->getDbtable('videos', 'ynvideo');
        $video = $table->createRow();
        $video->owner_id = $viewer->getIdentity();
        $video->type = $video_type;
        $video->parent_type = 'user';
        $video->parent_id = $viewer->getIdentity();
        $video->code = $video_url;

        if ($video_type == Ynvideo_Plugin_Factory::getVideoURLType()) {
          $video->title = Ynvideo_Plugin_Adapter_VideoURL::getDefaultTitle();
        } else {
          if ($adapter->fetchLink()) {
            // create video
            $video->storeThumbnail($adapter->getVideoThumbnailImage(), 'small');
            $video->storeThumbnail($adapter->getVideoLargeImage(), 'large');
            $video->title = $adapter->getVideoTitle();
            $video->description = $adapter->description;
            $video->duration = $adapter->getVideoDuration();
            $video->code = $adapter->getVideoCode();
            $video->save();
          }
        }

        // If video is from the composer, keep it hidden until the post is complete
        if ($composer_type) {
          $video->search = 0;
        }
        $video->status = 1;
        $video->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        $this->_jsonErrorOutput($e->getMessage());
      }

// make the video public
      if ($composer_type === 'wall') {
// CREATE AUTH STUFF HERE
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        foreach ($roles as $i => $role) {
          $auth->setAllowed($video, $role, 'view', ($i <= $roles));
          $auth->setAllowed($video, $role, 'comment', ($i <= $roles));
        }
      }

      $data['video_id'] = $video->video_id;
      $data['photo_id'] = $video->photo_id;
      $data['title'] = $video->title;
      $data['description'] = $video->description;
      if ($video_type == Ynvideo_Plugin_Factory::getVideoURLType()) {
        $data['src'] = Zend_Registry::get('StaticBaseUrl') . 'application/modules/Video/externals/images/video.png';
      } else {
        $data['src'] = $video->getPhotoUrl();
      }

      return $data;
    } else {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::VIDEO_URL_NOT_FOUND);
    }
  }

  protected function _uploadVideo()
  {
    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::UPLOAD_MAX_SIZE);
    }

    if (Engine_Api::_()->core()->hasSubject()) {
      $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    } else {
      $sitepage = null;
    }

    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }

    $values = $this->getRequest()->getPost();

    if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_UPLOAD);
    }

    $illegal_extensions = array('php', 'pl', 'cgi', 'html', 'htm', 'txt');
    if (in_array(pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION), $illegal_extensions)) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_UPLOAD);
    }

    if ($sitepage) {
      $db = Engine_Api::_()->getDbtable('videos', 'ynvideo')->getAdapter();
    } else {
      $db = Engine_Api::_()->getDbtable('videos', 'sitepagevideo')->getAdapter();
    }
    $db->beginTransaction();

    try {
      $viewer = Engine_Api::_()->user()->getViewer();
      $values['owner_id'] = $viewer->getIdentity();
      $values['c_type'] = 'wall';

      $params = array(
          'owner_type' => 'user',
          'owner_id' => $viewer->getIdentity(),
          'parent_type' => 'user',
          'parent_id' => $viewer->getIdentity()
      );

      if ($sitepage) {
        $video = Engine_Api::_()->sitepagevideo()->createSitepagevideo($params, $_FILES['Filedata'], $values);
        $video->page_id = $sitepage->getIdentity();
        $video->type = 3;
      } else {
        $video = Engine_Api::_()->ynvideo()->createVideo($params, $_FILES['Filedata'], $values);
      }

      $data['name'] = $_FILES['Filedata']['name'];
      $data['code'] = $video->code;
      $data['title'] = $_FILES['Filedata']['name'];
      $data['description'] = '';
      $data['video_id'] = $video->video_id;

// sets up title and owner_id now just incase members switch page as soon as upload is completed
      $video->title = $_FILES['Filedata']['name'];
      $video->owner_id = $viewer->getIdentity();
      $video->save();

      $db->commit();

      return $data;
    } catch (Exception $e) {
      $db->rollBack();
      Zend_Registry::get('Zend_Log')->log($e, Zend_Log ::ERR);

      $this->_jsonErrorOutput();
    }
  }

  protected function _processPhoto()
  {
    if (!Engine_Api::_()->user()->getViewer()->getIdentity()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }

    if (empty($_FILES['Filedata'])) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
    }

    if (Engine_Api::_()->core()->hasSubject('sitepage_page')) {
      $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    } else {
      $sitepage = null;
    }

    // Get album
    $viewer = Engine_Api::_()->user()->getViewer();

    if ($sitepage) {
      $table = Engine_Api::_()->getDbtable('albums', 'sitepage');
    } else {
      $table = Engine_Api::_()->getDbtable('albums', 'album');
    }
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $type = $this->_getParam('type', 'wall');

      if (empty($type))
        $type = 'wall';

      if ($sitepage) {
        $album = $table->getSpecialAlbum($sitepage, $type);
        $page_id = $sitepage->getIdentity();

        $photoTable = Engine_Api::_()->getDbtable('photos', 'sitepage');
        $photo = $photoTable->createRow();
        $photo->setFromArray(array(
            'page_id' => $page_id,
            'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity()
        ));
      } else {
        $album = $table->getSpecialAlbum($viewer, $type);

        $photoTable = Engine_Api::_()->getDbtable('photos', 'album');
        $photo = $photoTable->createRow();
        $photo->setFromArray(array(
            'owner_type' => 'user',
            'owner_id' => Engine_Api::_()->user()->getViewer()->getIdentity()
        ));
      }
      $photo->save();
      $photo->setPhoto($_FILES['Filedata']);

      if ($type == 'message') {
        $photo->title = Zend_Registry::get('Zend_Translate')->_('Attached Image');
      }

      if ($sitepage) {
        $photo->album_id = $album->album_id;
        $photo->collection_id = $album->album_id;
        $photo->save();

        if (!$album->photo_id) {
          $album->photo_id = $photo->file_id;
          $album->save();
        }
      } else {
        $photo->order = $photo->photo_id;
        $photo->album_id = $album->album_id;
        $photo->save();

        if (!$album->photo_id) {
          $album->photo_id = $photo->getIdentity();
          $album->save();
        }
      }

      if ($type != 'message') {
        // Authorizations
        $auth = Engine_Api::_()->authorization()->context;
        $auth->setAllowed($photo, 'everyone', 'view', true);
        $auth->setAllowed($photo, 'everyone', 'comment', true);
      }

      $db->commit();

      $data['photo_id'] = $photo->photo_id;
      $data['album_id'] = $album->album_id;
      $data['src'] = $photo->getPhotoUrl();

      return $data;
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput($e->getMessage());
    }
  }

  /**
   * Process user input link and return link data
   * This function goes along with _previewImage, _previewText, _previewHtml
   * 
   * @return array
   * @throws Exception Unknown exceptions
   */
  protected function _processLink()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }
    if (!$this->_helper->requireAuth()->setAuthParams('core_link', null, 'create')->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    // clean URL for html code
    $uri = trim(strip_tags($this->_getParam('uri')));
    // add http if needed
    if (!preg_match('/^[a-zA-Z]{1,5}\:\/\//', $uri)) {
      $uri = 'http://' . $uri;
    }

    //$uri = $this->_getParam('uri');
    $info = parse_url($uri);

    try {
      $client = new Zend_Http_Client($uri, array(
          'maxredirects' => 2,
          'timeout' => 10,
      ));

      // Try to mimic the requesting user's UA
      $client->setHeaders(array(
          'User-Agent' => $_SERVER['HTTP_USER_AGENT'],
          'X-Powered-By' => 'Zend Framework'
      ));

      $response = $client->request();

      // Get content-type
      list($contentType) = explode(';', $response->getHeader('content-type'));

      // Handling based on content-type
      switch (strtolower($contentType)) {

        // Images
        case 'image/gif':
        case 'image/jpeg':
        case 'image/jpg':
        case 'image/tif': // Might not work
        case 'image/xbm':
        case 'image/xpm':
        case 'image/png':
        case 'image/bmp': // Might not work
          return $this->_previewImage($uri, $response);

        // HTML
        case '':
        case 'text/html':
          return $this->_previewHtml($uri, $response);

        // Plain text
        case 'text/plain':
          return $this->_previewText($uri, $response);

        // Unknown
        default:
          break;
      }
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }
  }

  protected function _previewImage($uri, Zend_Http_Response $response)
  {
    $data = array(
        'imageCount' => 1,
        'thumb' => array($uri),
        'uri' => $uri
    );
    return $data;
  }

  protected function _previewText($uri, Zend_Http_Response $response)
  {
    $body = $response->getBody();
    if (preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getHeader('content-type'), $matches) ||
            preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getBody(), $matches)) {
      $charset = trim($matches[1]);
    } else {
      $charset = 'UTF-8';
    }

    // Reduce whitespace
    $body = preg_replace('/[\n\r\t\v ]+/', ' ', $body);

    $data = array();
    $data['title'] = substr($body, 0, 63);
    $data['description'] = substr($body, 0, 255);
    $data['uri'] = $uri;

    return $data;
  }

  protected function _previewHtml($uri, Zend_Http_Response $response)
  {
    $body = $response->getBody();
    $body = trim($body);
    if (preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getHeader('content-type'), $matches) ||
            preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getBody(), $matches)) {
      $charset = trim($matches[1]);
    } else {
      $charset = 'UTF-8';
    }

    // Get DOM
    if (class_exists('DOMDocument')) {
      $dom = new Zend_Dom_Query($body);
    } else {
      $dom = null; // Maybe add b/c later
    }

    $title = null;
    if ($dom) {
      $titleList = $dom->query('title');
      if (count($titleList) > 0) {
        $title = trim($titleList->current()->textContent);
        $title = substr($title, 0, 255);
      }
    }

    $description = null;
    if ($dom) {
      $descriptionList = $dom->queryXpath("//meta[@name='description']");
      // Why are they using caps? -_-
      if (count($descriptionList) == 0) {
        $descriptionList = $dom->queryXpath("//meta[@name='Description']");
      }
      if (count($descriptionList) > 0) {
        $description = trim($descriptionList->current()->getAttribute('content'));
        $description = substr($description, 0, 255);
      }
    }

    $thumb = null;
    if ($dom) {
      $thumbList = $dom->queryXpath("//link[@rel='image_src']");
      if (count($thumbList) > 0) {
        $thumb = $thumbList->current()->getAttribute('href');
      }
    }

    // Try to get image when thumb image is not found
    if ($dom && !$thumb) {
      // Get baseUrl and baseHref to parse . paths
      $baseUrlInfo = parse_url($uri);
      $baseUrl = null;
      $baseHostUrl = null;
      if ($dom) {
        $baseUrlList = $dom->query('base');
        if ($baseUrlList && count($baseUrlList) > 0 && $baseUrlList->current()->getAttribute('href')) {
          $baseUrl = $baseUrlList->current()->getAttribute('href');
          $baseUrlInfo = parse_url($baseUrl);
          $baseHostUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/';
        }
      }
      if (!$baseUrl) {
        $baseHostUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/';
        if (empty($baseUrlInfo['path'])) {
          $baseUrl = $baseHostUrl;
        } else {
          $baseUrl = explode('/', $baseUrlInfo['path']);
          array_pop($baseUrl);
          $baseUrl = join('/', $baseUrl);
          $baseUrl = trim($baseUrl, '/');
          $baseUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/' . $baseUrl . '/';
        }
      }

      $imageQuery = $dom->query('img');
      foreach ($imageQuery as $image) {
        $src = $image->getAttribute('src');
        // Ignore images that don't have a src
        if (!$src || false === ($srcInfo = @parse_url($src))) {
          continue;
        }
        $ext = ltrim(strrchr($src, '.'), '.');
        // Detect absolute url
        if (strpos($src, '/') === 0) {
          // If relative to root, add host
          $src = $baseHostUrl . ltrim($src, '/');
        } else if (strpos($src, './') === 0) {
          // If relative to current path, add baseUrl
          $src = $baseUrl . substr($src, 2);
        } else if (!empty($srcInfo['scheme']) && !empty($srcInfo['host'])) {
          // Contians host and scheme, do nothing
        } else if (empty($srcInfo['scheme']) && empty($srcInfo['host'])) {
          // if not contains scheme or host, add base
          $src = $baseUrl . ltrim($src, '/');
        } else if (empty($srcInfo['scheme']) && !empty($srcInfo['host'])) {
          // if contains host, but not scheme, add scheme?
          $src = $baseUrlInfo['scheme'] . ltrim($src, '/');
        } else {
          // Just add base
          $src = $baseUrl . ltrim($src, '/');
        }
        // Ignore images that don't come from the same domain
        //if( strpos($src, $srcInfo['host']) === false ) {
        // @todo should we do this? disabled for now
        //continue;
        //}
        // Ignore images that don't end in an image extension
        if (!in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
          // @todo should we do this? disabled for now
          //continue;
        }
        $thumb = $src;
        break;
      }
    }

    $data = array();
    $data['uri'] = $uri;
    $data['title'] = $title;
    $data['description'] = $description;
    $data['thumb'] = $thumb;

    return $data;
  }

  public function removeFeedAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');

    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    // Identify if it's an action_id or comment_id being deleted
    $comment_id = $this->_getParam('comment_id', null);
    $action_id = $this->_getParam('feed_id', null);

    $action = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getActionById($action_id);
    if (!$action) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::FEED_NOT_FOUND);
    }

    // Return error if not POST
    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }
    $is_owner = false;
    if (Engine_Api::_()->core()->hasSubject()) {
      $subject = Engine_Api::_()->core()->getSubject();
      if ($subject->getType() == 'siteevent_event' && ($subject->getParent()->getType() == 'sitepage_page' || $subject->getParent()->getType() == 'sitbusiness_business' || $subject->getParent()->getType() == 'sitegroup_group' || $subject->getParent()->getType() == 'sitestore_store')) {
        $subject = Engine_Api::_()->getItem($subject->getParent()->getType(), $subject->getParent()->getIdentity());
      }
      switch ($subject->getType()) {
        case 'user':
          $is_owner = $viewer->isSelf($subject);
          break;
        case 'sitepage_page':
        case 'sitebusiness_business':
        case 'sitegroup_group':
        case 'sitestore_store':
          $is_owner = $subject->isOwner($viewer);
          break;
        case 'sitepageevent_event':
        case 'sitebusinessevent_event':
        case 'sitegroupevent_event':
        case 'sitestoreevent_event':
          $is_owner = $viewer->isSelf($subject);
          if (empty($is_owner)) {
            $is_owner = $subject->getParent()->isOwner($viewer);
          }
          break;
        default :
          $is_owner = $viewer->isSelf($subject->getOwner());
          break;
      }
    }

    // Both the author and the person being written about get to delete the action_id
    if (!$comment_id && (
            $activity_moderate || $is_owner ||
            ('user' == $action->subject_type && $viewer->getIdentity() == $action->subject_id) || // owner of profile being commented on
            ('user' == $action->object_type && $viewer->getIdentity() == $action->object_id))) {   // commenter
      // Delete action item and all comments/likes
      $db = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getAdapter();
      $db->beginTransaction();
      try {
        if ($action->getTypeInfo()->commentable <= 1) {
          $comments = $action->getComments(1);
          if ($comments) {
            foreach ($comments as $action_comments) {
              $action_comments->delete();
            }
          }
        }
        $action->deleteItem();
        $db->commit();

        $this->_jsonSuccessOutput(array(
            'message' => Zend_Registry::get('Zend_Translate')->_('This activity item has been removed.')
        ));
      } catch (Exception $e) {
        $db->rollback();

        $this->_jsonErrorOutput();
      }
    } elseif ($comment_id) {
      $comment = $action->comments()->getComment($comment_id);
      // allow delete if profile/entry owner
      $db = Engine_Api::_()->getDbtable('comments', 'activity')->getAdapter();
      $db->beginTransaction();
      // Change feed removing permission to follow UI logic
//      if ($activity_moderate || $is_owner ||
//              ('user' == $comment->poster_type && $viewer->getIdentity() == $comment->poster_id) ||
//              ('user' == $action->object_type && $viewer->getIdentity() == $action->object_id)) {
      if ($viewer->getIdentity() &&
              (('user' == $action->subject_type && $viewer->getIdentity() == $action->subject_id) ||
              ("user" == $comment->poster_type && $viewer->getIdentity() == $comment->poster_id) ||
              ("user" !== $comment->poster_type && Engine_Api::_()->getItemByGuid($comment->poster_type . "_" . $comment->poster_id)->isOwner($viewer)) ||
              $activity_moderate )) {
        try {
          $action->comments()->removeComment($comment_id);
          $db->commit();

          $this->_jsonSuccessOutput(array(
              'message' => Zend_Registry::get('Zend_Translate')->_('Comment has been deleted')
          ));
        } catch (Exception $e) {
          $db->rollback();

          $this->_jsonErrorOutput();
        }
      } else {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOW_COMMENT_DELETE);
      }
    } else {
      // neither the item owner, nor the item subject.  Denied!
      $this->_jsonErrorOutput();
    }
  }

  public function likeFeedAction()
  {
    // Make sure user exists
    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    // Collect params
    $action_id = $this->_getParam('feed_id');
    $comment_id = $this->_getParam('comment_id');
//    $isShare = $this->_getParam('isShare');
    $viewer = Engine_Api::_()->user()->getViewer();

    // Start transaction
    $db = Engine_Api::_()->getDbtable('likes', 'activity')->getAdapter();
    $db->beginTransaction();

    try {
      $action = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getActionById($action_id);

      if (!$action) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::FEED_NOT_FOUND);
      }

      // Action
      if (!$comment_id) {

        // Check authorization
        if ($action && !Engine_Api::_()->authorization()->isAllowed($action->getCommentObject(), null, 'comment')) {
          $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOW_FEED_LIKE);
        }

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
        $comment = $action->comments()->getComment($comment_id);

        // Check authorization
        $commentItem = $comment;
        if ($comment->getType() == 'core_comment' && isset($comment->resource_type)) {
          $commentItem = Engine_Api::_()->getItem($comment->resource_type, $comment->resource_id);
        }
        if (!$comment || !Engine_Api::_()->authorization()->isAllowed($commentItem, null, 'comment')) {
          $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOW_FEED_LIKE);
        }

        $comment->likes()->addLike($viewer);

        // @todo make sure notifications work right
        if ($comment->poster_id != $viewer->getIdentity() && $comment->getPoster()->getType() == 'user') {
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

      //FEED LIKE NOTIFICATION WORK
      $object_type = $action->object_type;
      $object_id = $action->object_id;

      if ($object_type == 'sitepage_page' && (Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version >= '4.2.9p3')) {
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $object_id);
        Engine_Api::_()->sitepage()->sendNotificationEmail($sitepage, $action, 'sitepage_activitylike', '', 'Activity Comment');
      } elseif ($object_type == 'sitebusiness_business') {
        $sitebusiness = Engine_Api::_()->getItem('sitebusiness_business', $object_id);
        Engine_Api::_()->sitebusiness()->sendNotificationEmail($sitebusiness, $action, 'sitebusiness_activitylike', '', 'Activity Comment');
      } elseif ($object_type == 'sitegroup_group') {
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $object_id);
        Engine_Api::_()->sitegroup()->sendNotificationEmail($sitegroup, $action, 'sitegroup_activitylike', '', 'Activity Comment');
      } elseif ($object_type == 'sitestore_store') {
        $sitestore = Engine_Api::_()->getItem('sitestore_store', $object_id);
        Engine_Api::_()->sitestore()->sendNotificationEmail($sitestore, $action, 'sitestore_activitylike', '', 'Activity Comment');
      } elseif ($object_type == 'siteevent_event') {
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $object_id);
        Engine_Api::_()->siteevent()->sendNotificationEmail($siteevent, $action, 'siteevent_activitylike', '', 'Activity Comment', null, 'like', $viewer);
      }

      // Stats
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.likes');

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput($e->getMessage());
    }

    // Success
    $this->_jsonSuccessOutput(array(
        'message' => Zend_Registry::get('Zend_Translate')->_('You now like this action.')
    ));
  }

  public function unlikeFeedAction()
  {
    // Make sure user exists
    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    // Collect params
    $action_id = $this->_getParam('feed_id');
    $comment_id = $this->_getParam('comment_id');
    $isShare = $this->_getParam('isShare');
    $viewer = Engine_Api::_()->user()->getViewer();

    // Start transaction
    $db = Engine_Api::_()->getDbtable('likes', 'activity')->getAdapter();
    $db->beginTransaction();

    try {
      $action = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getActionById($action_id);

      if (!$action) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::FEED_NOT_FOUND);
      }

      // Action
      if (!$comment_id) {

        // Check authorization
        if (!Engine_Api::_()->authorization()->isAllowed($action->getCommentObject(), null, 'comment')) {
          $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOW_FEED_UNLIKE);
        }

        $action->likes()->removeLike($viewer);
      }

      // Comment
      else {
        $comment = $action->comments()->getComment($comment_id);

        // Check authorization
        $commentItem = $comment;
        if ($comment->getType() == 'core_comment' && isset($comment->resource_type)) {
          $commentItem = Engine_Api::_()->getItem($comment->resource_type, $comment->resource_id);
        }
        if (!$comment || !Engine_Api::_()->authorization()->isAllowed($commentItem, null, 'comment')) {
          $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOW_FEED_UNLIKE);
        }

        $comment->likes()->removeLike($viewer);
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput($e->getMessage());
    }

    // Success
    $this->_jsonSuccessOutput(array(
        'message' => Zend_Registry::get('Zend_Translate')->_('You no longer like this action.')
    ));
  }

  public function shareFeedAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    $type = $this->_getParam('type');
    $id = $this->_getParam('id');
    $parent_action_id = $this->_getparam('feed_id', null);

    $viewer = Engine_Api::_()->user()->getViewer();
    $attachment = Engine_Api::_()->getItem($type, $id);
    $form = new Activity_Form_Share();

    if (!$attachment) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::FEED_NOT_SHAREABLE);
    }

    // hide facebook and twitter option if not logged in
    $Api_facebook = Engine_Api::_()->getApi('facebook_Facebookinvite', 'seaocore');
    $facebook_userfeed = $Api_facebook->getFBInstance();
    $fb_checkconnection = '';
    if (!empty($facebook_userfeed)) {
      $fb_checkconnection = $Api_facebook->checkConnection(null, $facebook_userfeed);
    }
//    $facebook = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();

    if (!$Api_facebook || !$fb_checkconnection) {
      $form->removeElement('post_to_facebook');
    }

    try {
      $Api_twitter = Engine_Api::_()->getApi('twitter_Api', 'seaocore');
      $twitterOauth = $twitter = $Api_twitter->getApi();
      if ($twitter && $Api_twitter->isConnected()) {
        // @todo truncation?
        // @todo attachment
        $twitterData = (array) $twitterOauth->get(
                        'statuses/home_timeline', array('count' => 1)
        );
        if (isset($twitterData['errors'])) {
          $form->removeElement('post_to_twitter');
        }
      } else {
        $form->removeElement('post_to_twitter');
      }
    } catch (Exception $e) {
      $form->removeElement('post_to_twitter');
    }

    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
    }

    // Process
    $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
    $db->beginTransaction();

    try {
      // Get body
      $body = $form->getValue('body');

      // Set Params for Attachment
      if (method_exists($attachment, 'getMediaType')) {
        $label = $attachment->getMediaType();
      } else {
        $label = $this->getMediaType($attachment);
      }

      if (empty($label)) {
        $label = $attachment->getShortType();
      }
      $suffix = "";
      if ($attachment->getType() == 'activity_action') {
        $suffix = "_no";
      }
      $params = array(
          'type' => '<a href="' . $attachment->getHref() . '" class="sea_add_tooltip_link' . $suffix . ' feed_' . $attachment->getType() . '_title"  rel="' . $attachment->getType() . ' ' . $attachment->getIdentity() . '" >' . $label . '</a>',
      );
      // Add activity
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $api->addActivity($viewer, $attachment->getOwner(), 'share', $body, $params);
      if ($action) {
        $api->attachActivity($action, $attachment);
        if (!empty($parent_action_id) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
          $shareTable = Engine_Api::_()->getDbtable('shares', 'advancedactivity');
          $shareTable->insert(array(
              'resource_type' => (string) $type,
              'resource_id' => (int) $id,
              'parent_action_id' => $parent_action_id,
              'action_id' => $action->action_id,
          ));
        }
      }
      $db->commit();
      // Notifications
      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
      // Add notification for owner of activity (if user and not viewer)
      if ($action->subject_type == 'user' && $attachment->getOwner()->getIdentity() != $viewer->getIdentity()) {
        $notifyApi->addNotification($attachment->getOwner(), $viewer, $action, 'shared', array(
            'label' => $label,
        ));
      }

      // Preprocess attachment parameters
      $publishMessage = html_entity_decode($form->getValue('body'));
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
          $facebookApi = Seaocore_Api_Facebook_Facebookinvite::getFBInstance();
          $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
          $fb_uid = $facebookTable->find($viewer->getIdentity())->current();

          if ($facebookApi && Seaocore_Api_Facebook_Facebookinvite::checkConnection(null, $facebookApi)
          ) {
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
              $fb_data['description'] = strip_tags($publishDesc);
            }
            if ($publishPicUrl) {
              $fb_data['picture'] = $publishPicUrl;
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
            if ($subject && isset($subject->fbpage_id) && !empty($subject->fbpage_id)) {
              $manages_pages = $facebookApi->api('/me/accounts', 'GET');
              //NOW GETTING THE PAGE ACCESS TOKEN TO WITH THIS SITE PAGE IS INTEGRATED:

              foreach ($manages_pages['data'] as $page) {
                if ($page['id'] == $subject->fbpage_id) {
                  $fb_data['access_token'] = $page['access_token'];
                  $res = $facebookApi->api('/' . $subject->fbpage_id . '/feed', 'POST', $fb_data);
                  break;
                }
              }
            }
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

            $twitterOauth = $twitter = $Api_twitter->getApi();
            $login = Engine_Api::_()->getApi('settings', 'core')->getSetting('bitly.apikey');
            $appkey = Engine_Api::_()->getApi('settings', 'core')->getSetting('bitly.secretkey');


            //TWITTER ONLY ACCEPT 140 CHARACTERS MAX..
            //IF BITLY IS CONFIGURED ON THE SITE..
            if (!empty($login) && !empty($appkey)) {
              if (strlen(html_entity_decode($_POST['body'])) > 140 || $attachment) {
                if ($attachment) {
                  $shortURL = Engine_Api::_()->getApi('Bitly', 'seaocore')->get_bitly_short_url((_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $attachment->getHref(), $login, $appkey, $format = 'txt');
                  $BitlayLength = strlen($shortURL);
                } else {
                  $BitlayLength = 0;
                  $shortURL = '';
                }
                $twitterFeed = substr(html_entity_decode($_POST['body']), 0, (140 - ($BitlayLength + 1))) . ' ' . $shortURL;
              } else {
                $twitterFeed = html_entity_decode($_POST['body']);
              }
            } else {
              $twitterFeed = substr(html_entity_decode($_POST['body']), 0, 137) . '...';
            }

            $lastfeedobject = $twitterOauth->post(
                    'statuses/update', array('status' => $twitterFeed)
            );
          }
        } catch (Exception $e) {
          // Silence
        }
      }


      // Publish to janrain
      if ('publish' == Engine_Api::_()->getApi('settings', 'core')->core_janrain_enable) {
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
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput($e->getMessage());
    }

    // If we're here, we're done
    $this->_jsonSuccessOutput(array(
        'message' => Zend_Registry::get('Zend_Translate')->_('Share successfully.')
    ));
  }

  function fetchFeedInfoAction()
  {
    // check public settings
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.portal', 1);
    if (!$require_check) {
      if (!$this->_helper->requireUser()->isValid()) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
      }
    }

    if (!Engine_Api::_()->user()->getViewer()->getIdentity()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    $subject = null;
    if ($this->_getParam('user_id')) {
      $subject = Engine_Api::_()->getItem('user', (int) $this->_getParam('user_id'));
      if (!$subject->user_id) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::USER_NOT_FOUND);
      }
    }

    $request = Zend_Controller_Front::getInstance()->getRequest();

    // Get config options for activity
    $config = array(
        'action_id' => (int) $request->getParam('feed_id')
    );

    $actionTable = Engine_Api::_()->getDbtable('actions', 'advancedactivity');
    $viewAllComments = $request->getParam('viewAllComments', $request->getParam('show_comments', false));

    // Get current batch
    $actions = null;

    // Where the Activity Feed is Fetched
    if (!empty($subject)) {
      $actions = $actionTable->getActivityAbout($subject, $viewer, $config);
    } else {
      $actions = $actionTable->getActivity($viewer, $config);
    }

    if (count($actions) > 0) {
      $refinedComments = $this->_helper->feedAPI->getRefinedComments($actions[0], $viewAllComments, $viewer);

      $activity['like_info'] = $this->_helper->feedAPI->getLikeInfo($actions[0], $viewer);

      // comment info
      $activity['comment_info'] = array();
      $activity['comment_info']['comment_count'] = count($refinedComments);
      $activity['comment_info']['commentable'] = (int) ($actions[0]->getTypeInfo()->commentable && $actions[0]->commentable);
      $activity['comment_info']['comments'] = $refinedComments;

      $this->_jsonSuccessOutput($activity);
    } else {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::FEED_NOT_FOUND);
    }
  }

  /**
   * Comments on a feed
   */
  function commentAction()
  {
    // Make sure user exists
    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    // Make form
    $form = new Activity_Form_Comment();
    // Not post
    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }
    $settings = Engine_Api::_()->getApi('settings', 'core');
    // Not valid
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
    }
    if (!empty($settings->aaf_composer_value) && ($settings->aaf_composer_value != ($settings->aaf_list_view_value + $settings->aaf_publish_str_value))) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
    }
    // Start transaction
    $db = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getAdapter();
    $db->beginTransaction();

    try {
      $viewer = Engine_Api::_()->user()->getViewer();
      $action_id = $this->_getParam('feed_id', $this->_getParam('action', null));
      $action = Engine_Api::_()->getDbtable('actions', 'advancedactivity')->getActionById($action_id);

      if (!$action) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::FEED_NOT_FOUND);
      }

      $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type . "_" . $action->subject_id);
      $body = $form->getValue('body');

      // Check authorization
      if (!Engine_Api::_()->authorization()->isAllowed($action->getCommentObject(), null, 'comment')) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOW_COMMENT);
      }

      // Add the comment
      $subject = $viewer;
      if (Engine_Api::_()->advancedactivity()->isBaseOnContentOwner($viewer, $action->getObject())) {
        $subject = $action->getObject();
      }
      if ($subject->getType() == 'siteevent_event') {
        $subject = $subject->getParent();
      }
      $comment = $action->comments()->addComment($subject, $body);

      // Notifications
      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

      // Add notification for owner of activity (if user and not viewer)
      if ($action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity()) {
        $notifyApi->addNotification($actionOwner, $subject, $action, 'commented', array(
            'label' => 'post'
        ));
      }

      // Add a notification for all users that commented or like except the viewer and poster
      // @todo we should probably limit this
      foreach ($action->comments()->getAllCommentsUsers() as $notifyUser) {
        if ($notifyUser->getType() == 'user' && $notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity()) {
          $notifyApi->addNotification($notifyUser, $subject, $action, 'commented_commented', array(
              'label' => 'post'
          ));
        }
      }

      // Add a notification for all users that commented or like except the viewer and poster
      // @todo we should probably limit this
      foreach ($action->likes()->getAllLikesUsers() as $notifyUser) {
        if ($notifyUser->getType() == 'user' && $notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity()) {
          $notifyApi->addNotification($notifyUser, $subject, $action, 'liked_commented', array(
              'label' => 'post'
          ));
        }
      }

      //PAGE COMMENT CREATE NOTIFICATION WORK
      $object_type = $action->object_type;
      $object_id = $action->object_id;

      $sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;
      if ($object_type == 'sitepage_page' && $sitepageVersion >= '4.2.9p3') {
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $object_id);
        Engine_Api::_()->sitepage()->sendNotificationEmail($sitepage, $action, 'sitepage_activitycomment', '', 'Activity Comment');
      } else if ($object_type == 'sitegroup_group') {
        $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $object_id);
        Engine_Api::_()->sitegroup()->sendNotificationEmail($sitegroup, $action, 'sitegroup_activitycomment', '', 'Activity Comment');
      } else if ($object_type == 'sitestore_store') {
        $sitestore = Engine_Api::_()->getItem('sitestore_store', $object_id);
        Engine_Api::_()->sitestore()->sendNotificationEmail($sitestore, $action, 'sitestore_activitycomment', '', 'Activity Comment');
      } else if ($object_type == 'sitebusiness_business') {
        $sitebusiness = Engine_Api::_()->getItem('sitebusiness_business', $object_id);
        Engine_Api::_()->sitebusiness()->sendNotificationEmail($sitebusiness, $action, 'sitebusiness_activitycomment', '', 'Activity Comment');
      } else if ($object_type == 'siteevent_event') {
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $object_id);
        Engine_Api::_()->siteevent()->sendNotificationEmail($siteevent, $action, 'siteevent_activitycomment', '', 'Activity Comment', null, 'comment', $viewer);
      }

      // Stats
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput($e->getMessage());
    }

    // Assign message for json
    $this->_jsonSuccessOutput(array(
        'message' => $this->_translator->_('Comment posted'),
        'comment_id' => $comment->getIdentity()
    ));
  }

  public function fetchAboutInfoAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $subject = null;
    if (!Engine_Api::_()->core()->hasSubject()) {
      $id = $this->_getParam('user_id');

      if ($id) {
        $subject = Engine_Api::_()->user()->getUser($id);
      } else {
        $subject = $viewer;
      }
    }
    Engine_Api::_()->core()->setSubject($subject);

    // Don't render this if not authorized
    if (!$subject) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::USER_NOT_FOUND);
    }
    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    $partialStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($subject);

    $arrInfo = $this->_helper->profileAPI->fieldValueLoop($subject, $partialStructure);

    $this->_jsonSuccessOutput($arrInfo);
  }

  public function fetchMedicalRecordAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    $subject = null;
    if (!Engine_Api::_()->core()->hasSubject()) {
      $id = $this->_getParam('user_id');

      if ($id) {
        $subject = Engine_Api::_()->user()->getUser($id);
      } else {
        $subject = $viewer;
      }
    }
    Engine_Api::_()->core()->setSubject($subject);

    // Don't render this if not authorized
    if (!$subject) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::USER_NOT_FOUND);
    }
    if (!Engine_Api::_()->getDbTable('accessLevel', 'zulu')->isAllowed($subject, $viewer, 'view_clinical')) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    $spec = Engine_Api::_()->getDbTable('zulus', 'zulu')->getZuluByUserId($subject->getIdentity());

    // If user has medical record
    if ($spec) {
      $partialStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($spec);

      $arrInfo = $this->_helper->profileAPI->fieldValueLoop($spec, $partialStructure);

      $this->_jsonSuccessOutput($arrInfo);
    } else {
      $this->_jsonSuccessOutput(array());
    }
  }

  public function fetchMemberListAction($return = false)
  {
    // Parse field inputs from app
    $this->_helper->profileAPI->parseFieldInput();

    // Check form
    $form = new User_Form_Search(array(
        'type' => 'user'
    ));
    // Get viewer object
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$form->isValid($this->_getAllParams())) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
    }

    // Get search params
    $page = (int) $this->_getParam('page', 1);
    $items_per_page = (int) $this->_getParam('items_per_page', 50);
    if (!$items_per_page) {
      $items_per_page = 50;
    }
    $options = $form->getValues();

    // Process options
    $tmp = array();
    $originalOptions = $options;
    foreach ($options as $k => $v) {
      if (null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0)) {
        continue;
      } else if (false !== strpos($k, '_field_')) {
        list($null, $field) = explode('_field_', $k);
        $tmp['field_' . $field] = $v;
      } else if (false !== strpos($k, '_alias_')) {
        list($null, $alias) = explode('_alias_', $k);
        $tmp[$alias] = $v;
      } else {
        $tmp[$k] = $v;
      }
    }
    $options = $tmp;

    // Get table info
    $table = Engine_Api::_()->getItemTable('user');
    $userTableName = $table->info('name');

    $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
    $searchTableName = $searchTable->info('name');

    $displayname = @$options['displayname'];
    if (!empty($options['extra'])) {
      extract($options['extra']); // is_online, has_photo, submit
    }

    // Contruct query
    $select = $table->select()
            //->setIntegrityCheck(false)
            ->from($userTableName)
            ->joinLeft($searchTableName, "`{$searchTableName}`.`item_id` = `{$userTableName}`.`user_id`", null)
            //->group("{$userTableName}.user_id")
            ->where("{$userTableName}.enabled = ?", 1);
//      ->order("{$userTableName}.displayname ASC");

    try {
      Engine_Api::_()->getApi('core', 'sharedResources')->addSiteSeprationCondition($select);
    } catch (Exception $ex) {
      // Silent?
    }

    if ($this->_getParam('user_type') != 'friends') {
      $select->where("{$userTableName}.search = ?", 1);
    }

    if ($this->_getParam('medical_record_shared')) {
      $select->join('engine4_zulu_profileshare', "`engine4_zulu_profileshare`.`subject_id` = `{$userTableName}`.`user_id`", null)
              ->where('`engine4_zulu_profileshare`.`viewer_id` = ?', $viewer->getIdentity());
    }

    if (isset($options['order'])) {
      switch ($options['order']) {
        case 'recent':
          $select->order("{$userTableName}.creation_date DESC");
          break;
        case 'alphabet':
          $select->order("{$userTableName}.displayname ASC");
          break;
      }
    } else {
      $select->order("{$userTableName}.displayname ASC");
    }

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

    // Add displayname
    if (!empty($displayname)) {
      $select->where("(`{$userTableName}`.`username` LIKE ? || `{$userTableName}`.`displayname` LIKE ?)", "%{$displayname}%");
    }

    // Build search part of query
    $searchParts = Engine_Api::_()->fields()->getSearchQuery('user', $options);
    foreach ($searchParts as $k => $v) {
      $select->where("`{$searchTableName}`.{$k}", $v);
    }

    $owner = null;

    if ($this->_getParam('user_type') === 'friends') {
      $id = $this->_getParam('user_id');
      // Get the owner's identity of the friend list
      if ($id) {
        // Check if the user exists
        $owner = Engine_Api::_()->user()->getUser($id);
        if (!$owner->getIdentity()) {
          $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::USER_NOT_FOUND);
        }
        $resource_id = $id;
        $items_per_page = 1000;
      } else {
        $resource_id = $viewer->getIdentity();
      }

      $userMembershipTable = Engine_Api::_()->getDbTable('membership', 'user');
      $select->setIntegrityCheck(false)->join(array('um' => $userMembershipTable->info('name')), "`{$userTableName}`.`user_id` = `um`.`user_id`");
      $select->where('um.active = 1')->where('um.resource_id = ?', $resource_id);
    }

    if ($this->_getParam('participation_level')) {
      $valueTable = Engine_Api::_()->fields()->getTable('user', 'values');
      $valueTableName = $valueTable->info('name');

      $participationLevelField = Engine_Api::_()->user()->getParticipationLevelField();
      $participationLevelValue = $this->_getParam('participation_level');

      $select->join($valueTableName, "`{$userTableName}`.`user_id` = `{$valueTableName}`.`item_id`", null);
      $select->where("`{$valueTableName}`.`field_id` = ?", $participationLevelField->field_id);
      $select->where("`{$valueTableName}`.`value` = ?", $participationLevelValue);
    }

    // Build paginator
    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($items_per_page);
    $paginator->setCurrentPageNumber($page);

    $users = array();
    $i = 0;
    foreach ($paginator as $user) {
      $users[$i]['user_id'] = $user->user_id;
      $users[$i]['user_name'] = $user->getTitle();
      $users[$i]['user_photo'] = $user->getPhotoUrl('thumb.profile');
//      if ($owner) {
//        $users[$i]['friendship_status'] = $this->_helper->profileAPI->getUserRelationshipAction($user, $owner);
//      } else {
//        $users[$i]['friendship_status'] = $this->_helper->profileAPI->getUserRelationshipAction($user);
//      }
      $users[$i]['friendship_status'] = $this->_helper->profileAPI->getUserRelationshipAction($user);
      $i++;
    }
    $data['users'] = $users;
    $data['totalUsers'] = $paginator->getTotalItemCount();
    $data['userCount'] = $paginator->getCurrentItemCount();

    if ($return) {
      return $data;
    }

    $this->_jsonSuccessOutput($data);
  }

  public function fetchGeneralInfoAction()
  {
    $subject = Engine_Api::_()->user()->getUser($this->_getParam('user_id'));
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    $permissionInfo['medical_record'] = array();
    $permissionInfo['feed'] = array();

    if (!$subject->getIdentity()) {
      $subject = $viewer;
    } else {
      Engine_Api::_()->core()->setSubject($subject);
    }
    $permissionInfo['feed']['post_new'] = (int) $this->_helper->feedAPI->checkPostFeedAuth();

    // -- Get General Info
    $country_of_residence = 'country_of_residence';
    $primary_sport = 'primary_sport';
    $participation_level = 'participation_level';

    // get the subject profile field structured
    $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($subject);

    // nessary profile field contianer
    $generalInfo = array();

    // get the profile field map
    foreach ($fieldStructure as $map) {
      $field = $map->getChild();
      $value = $field->getValue($subject);

      // If the value is object
      if (is_object($value)) {
        if (in_array($field->alias, array($country_of_residence, $primary_sport))) {
          $generalInfo[$field->alias]['label'] = $field->label;
          $generalInfo[$field->alias]['value'] = $value->getValue() ? $value->getValue() : $field->getOption($value->getValue())->label;
        }
      }

      // Handle Participation Level
      if ($field->alias == 'Participation Level') {
        $generalInfo[$participation_level]['label'] = $field->label;
        $generalInfo[$participation_level]['value'] = array();
        foreach ($value as $single_value) {
          $generalInfo[$participation_level]['value'][] = $field->getOption($single_value->value)->label;
        }
      }
    }

    // get sport name
    if ($generalInfo[$primary_sport]['value'] != null) {
      $generalInfo[$primary_sport]['value'] = $this->view->translate($generalInfo[$primary_sport]['value']);
    }

    // get the resident name
    if ($generalInfo[$country_of_residence]['value'] != null) {
      try {
        $locale = new Zend_Locale(Zend_Locale::BROWSER);
        $countries = $locale->getTranslationList('Territory', Zend_Locale::BROWSER, 2);
      } catch (exception $e) {
        $locale = new Zend_Locale('en_US');
        $countries = $locale->getTranslationList('Territory', 'en_US', 2);
      }
      $generalInfo[$country_of_residence]['value'] = $countries[$generalInfo[$country_of_residence]['value']];
    }
    // -- Get General Info
    // -- Get User Info
    $userInfo['user_id'] = $subject->user_id;
    $userInfo['user_name'] = $subject->displayname;
    $userInfo['user_photo'] = $subject->getPhotoUrl();
    $userInfo['user_thumb_photo'] = $subject->getPhotoUrl('thumb.icon');
    // -- Get User Info
    // -- Get Permission Info
    $permissionInfo['medical_record']['allow_edit'] = Engine_Api::_()->getDbTable('accessLevel', 'zulu')->isAllowed($subject, $viewer, 'edit') === Authorization_Api_Core::LEVEL_MODERATE ? 1 : 0;
    $permissionInfo['medical_record']['allow_view'] = Engine_Api::_()->getDbTable('accessLevel', 'zulu')->isAllowed($subject, $viewer, 'view_clinical') ? 1 : 0;
    // -- Get Permission Info
    // -- Profile permission
    $permissionInfo['user_profile']['allow_edit'] = $subject->authorization()->isAllowed($viewer, 'edit') ? 1 : 0;
    $permissionInfo['user_profile']['allow_view'] = $subject->authorization()->isAllowed($viewer, 'view') ? 1 : 0;

    // Get the relationship between the viewed user and the viewer
    $userInfo['friendship_status'] = $this->_helper->profileAPI->getUserRelationshipAction($subject);

    $profileData = array();

    $profileData['generalInfo'] = $generalInfo;
    $profileData['userInfo'] = $userInfo;
    $profileData['permissionInfo'] = $permissionInfo;

    $this->_jsonSuccessOutput($profileData);
  }

  public function fetchPhotoLibraryAction()
  {
    // Return $subject, $viewer
    extract($this->_helper->profileAPI->profileAuth());

    // Get paginator
    $paginator = Engine_Api::_()->getItemTable('album')
            ->getAlbumPaginator(array('owner' => $subject));

    $items_per_page = $this->_getParam('itemCountPerPage');
    if (!$items_per_page) {
      $items_per_page = 50;
    }

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($items_per_page);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0) {
      $this->_jsonSuccessOutput();
    }

    // Add count to title if configured
    $totalItem = $paginator->getTotalItemCount();

    // Set total items
    $data['totalItem'] = $totalItem;
    // Array of albums
    $albums = array();

    $i = 0;
    foreach ($paginator as $album) {
      $isSelf = $album->getOwner()->isSelf(Engine_Api::_()->user()->getViewer());
      $canEdit = $this->_helper->requireAuth()->setAuthParams($album, null, 'edit')->checkRequire();

      $can_edit = (int) ($isSelf || $canEdit);

      $albums[$i] = array();
      $albums[$i]['album_id'] = $album->getIdentity();
      $albums[$i]['album_cover'] = $album->getPhotoUrl('thumb.normal');
      $albums[$i]['album_title'] = $album->getTitle();
      $albums[$i]['album_like_count'] = $album->likes()->getLikeCount();

      // get comment info
      $commentInfo = $this->_helper->commonAPI->getCommentInfo($album);
      $albums[$i]['album_comment_count'] = $commentInfo['comment_count'];

      $albums[$i]['album_photo_count'] = $album->count();
      $albums[$i]['can_delete'] = $can_edit;
      $albums[$i]['can_edit'] = $can_edit;
      $i++;
    }
    $data['albums'] = $albums;

    $this->_jsonSuccessOutput($data);
  }

  public function fetchPhotoAlbumDetailsAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    // Check if album exists
    $album_id = (int) $this->_getParam('album_id');
    if (null === ($album = Engine_Api::_()->getItem('album', $album_id))) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ALBUM_NOT_FOUND);
    }

    // Check if user is allowed to view the album
    if (!$this->_helper->requireAuth()->setAuthParams($album, null, 'view')->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    // Prepare params
    $page = $this->_getParam('page');

    // Prepare data
    $photoTable = Engine_Api::_()->getItemTable('album_photo');
    $paginator = $photoTable->getPhotoPaginator(array(
        'album' => $album,
    ));
    $items_per_page = $this->_getParam('itemCountPerPage');
    if (!$items_per_page) {
      $items_per_page = 50;
    }
    $paginator->setItemCountPerPage($items_per_page);
    $paginator->setCurrentPageNumber($page);

    // Do other stuff
    $mine = true;
    if (!$album->getOwner()->isSelf($viewer)) {
      $album->getTable()->update(array(
          'view_count' => new Zend_Db_Expr('view_count + 1'),
              ), array(
          'album_id = ?' => $album->getIdentity(),
      ));
      $mine = false;
    }
    $canEdit = $this->_helper->requireAuth()->setAuthParams($album, null, 'edit')->checkRequire() || $mine;

    // Get photo array
    $photos = array();
    $i = 0;
    foreach ($paginator as $photo) {
      $photos[$i]['photo_id'] = $photo->getIdentity();
      $photos[$i]['thumb_photo_url'] = $photo->getPhotoUrl('thumb.normal');
      $photos[$i]['big_photo_url'] = $photo->getPhotoUrl();
      $photos[$i]['photo_title'] = $photo->getTitle();
      $photos[$i]['photo_description'] = $photo->getDescription();
      $photos[$i]['can_edit'] = (int) $album->authorization()->isAllowed($viewer, 'edit');
      $photos[$i]['can_delete'] = (int) $album->authorization()->isAllowed($viewer, 'delete');
      $photos[$i]['can_comment'] = (int) $photo->authorization()->isAllowed($viewer, 'comment');
      $photos[$i]['can_share'] = 1;

      // Get likers
      $photos[$i]['like_info'] = $this->_helper->commonAPI->getLikeInfo($photo, $viewer);
      // Get comment count
      $photos[$i]['comment_count'] = $photo->comments()->getCommentCount();

      $i++;
    }

    $data = array(
        'album_title' => $album->getTitle(),
        'album_description' => $album->getDescription(),
        'canEdit' => (int) $canEdit,
        'photos' => $photos,
        'totalPhoto' => $paginator->getTotalItemCount(),
        'isLiked' => (int) $album->likes()->isLike($viewer),
        'like_info' => $this->_helper->commonAPI->getLikeInfo($album, $viewer),
        'commentInfo' => $this->_helper->commonAPI->getCommentInfo($album)
    );

    $this->_jsonSuccessOutput($data);
  }

  public function fetchPhotoDetailsAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    // Check if album exists
    $photo_id = (int) $this->_getParam('photo_id');
    if (null === ($photo = Engine_Api::_()->getItem('album_photo', $photo_id))) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::PHOTO_NOT_FOUND);
    }
    $album = $photo->getAlbum();

    // Increase view count
    if (!$viewer || !$viewer->getIdentity() || !$album->isOwner($viewer)) {
      $photo->view_count = new Zend_Db_Expr('view_count + 1');
      $photo->save();
    }

    // if this is sending a message id, the user is being directed from a coversation
    // check if member is part of the conversation
    $message_id = $this->getRequest()->getParam('message');
    $message_view = false;
    if ($message_id) {
      $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
      if ($conversation->hasRecipient(Engine_Api::_()->user()->getViewer())) {
        $message_view = true;
      }
    }

    // Check if user is allowed to view the album
    if (!$message_view && !$this->_helper->requireAuth()->setAuthParams($photo, null, 'view')->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    $data = array();
    $data['thumb_photo_url'] = $photo->getPhotoUrl('thumb.normal');
    $data['big_photo_url'] = $photo->getPhotoUrl();
    $data['photo_title'] = $photo->getTitle();
    $data['photo_description'] = $photo->getDescription();
    $data['canEdit'] = (int) $album->authorization()->isAllowed($viewer, 'edit');
    $data['canDelete'] = (int) $album->authorization()->isAllowed($viewer, 'delete');
    $data['canTag'] = (int) $album->authorization()->isAllowed($viewer, 'tag');
    $data['canUntag'] = (int) $album->isOwner($viewer);

    // Get like info
    $data['like_info'] = $this->_helper->commonAPI->getLikeInfo($photo, $viewer);
    // Get comment info
    $data['comment_info'] = $this->_helper->commonAPI->getCommentInfo($photo);

    $this->_jsonSuccessOutput($data);
  }

  public function fetchVideoLibraryAction()
  {
    // Return $subject, $viewer
    extract($this->_helper->profileAPI->profileAuth());

    $params = array(
        'user_id' => $subject->getIdentity(),
    );
    if ($viewer->getIdentity() != $subject->getIdentity()) {
      $params['status'] = 1;
      $params['search'] = 1;
    }

    $paginator = Engine_Api::_()->ynvideo()->getVideosPaginator($params);

    // Set item count per page and current page number
    $items_per_page = $this->_getParam('itemCountPerPage');
    if (!$items_per_page) {
      $items_per_page = 50;
    }
    $paginator->setItemCountPerPage($items_per_page);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Get videos
    $videos = array();
    $i = 0;
    foreach ($paginator as $video) {

      if (!isset($video->type)) {
        continue;
      }
      // Get video location
      $video_location = $this->_helper->videoAPI->getVideoLocation($video);

      $videos[$i]['video_id'] = $video->getIdentity();
      $videos[$i]['video_duration'] = $video->duration;
      // Get thumb photo
      if ($video->video_id) {
        $videos[$i]['video_thumb_photo'] = $video->getPhotoUrl('thumb.normal');
      } else {
        $videos[$i]['video_thumb_photo'] = $this->view->serverUrl('/application/modules/Ynvideo/externals/images/video.png');
      }
      $videos[$i]['video_title'] = $video->getTitle();
      $videos[$i]['video_url'] = $video_location;
      $videos[$i]['video_view_count'] = $video->view_count;
      $videos[$i]['video_like_count'] = $video->likes()->getLikeCount();
      $videos[$i]['video_rating'] = $video->rating;
      $videos[$i]['video_type'] = $this->_helper->videoAPI->getMobileVideoType($video);

      // Get comment info
      $commentInfo = $this->_helper->commonAPI->getCommentInfo($video);
      $videos[$i]['comment_count'] = $commentInfo['comment_count'];

      // Get permission info
      $videos[$i]['can_edit'] = $this->_helper->requireAuth()->setAuthParams($video, null, 'edit')->checkRequire();
      $videos[$i]['can_delete'] = $this->_helper->requireAuth()->setAuthParams($video, null, 'delete')->checkRequire();

      $i++;
    }

    $data = array(
        'totalVideos' => $paginator->getTotalItemCount(),
        'videos' => $videos
    );

    $this->_jsonSuccessOutput($data);
  }

  public function fetchVideoDetailsAction()
  {
    $video_id = $this->_getParam('video_id');
    $video = Engine_Api::_()->getItem('video', $video_id);

    // Check if the video exists
    if (!$video) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::VIDEO_NOT_FOUND);
    }

    // Authentication
    $viewer = Engine_Api::_()->user()->getViewer();

    $watchLaterTbl = Engine_Api::_()->getDbTable('watchlaters', 'ynvideo');
    $watchLaterTbl->update(array(
        'watched' => '1',
        'watched_date' => date('Y-m-d H:i:s')
            ), array(
        "video_id = {$video->getIdentity()}",
        "user_id = {$viewer->getIdentity()}"
    ));

    // if this is sending a message id, the user is being directed from a coversation
    // check if member is part of the conversation
    $message_id = $this->getRequest()->getParam('message');
    $message_view = false;
    if ($message_id) {
      $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
      if ($conversation->hasRecipient(Engine_Api::_()->user()->getViewer())) {
        $message_view = true;
      }
    }

    if (!$message_view && !$this->_helper->requireAuth()->setAuthParams($video, null, 'view')->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    // Get video tags
    $videoTags = $video->tags()->getTagMaps();
    $tagString = '';
    foreach ($videoTags as $tag) {
      $tagString .= $tag->getTag()->text . ', ';
    }
    $tagString = preg_replace('/, $/', '', $tagString);

    // Check if edit/delete is allowed
    $can_edit = $this->_helper->requireAuth()->setAuthParams($video, null, 'edit')->checkRequire();
    $can_delete = $this->_helper->requireAuth()->setAuthParams($video, null, 'delete')->checkRequire();

    // Get video location
    $video_location = $this->_helper->videoAPI->getVideoLocation($video);
    // Get number of rating
    $rating_count = Engine_Api::_()->ynvideo()->ratingCount($video->getIdentity());
    // Check if user has already rated this video
    $isRated = Engine_Api::_()->ynvideo()->checkRated($video->getIdentity(), $viewer->getIdentity());
    // Get video rating
    $rating = $video->rating;
    // Rating Info
    $ratingInfo = array(
        'rating_count' => $rating_count,
        'isRated' => (int) $isRated,
        'video_rating' => $rating
    );

    // Get video categories
    $categories = Engine_Api::_()->getDbTable('categories', 'ynvideo')->getCategories(array($video->category_id, $video->subcategory_id));
    $categoryString = '';
    foreach ($categories as $category) {
      if ($category->category_id) {
        $categoryString .= $category->category_name;
      }
    }
    $categoryString = preg_replace('/, $/', '', $categoryString);

    // Get video owner
    $owner = $video->getOwner();
    $ownerInfo = array(
        'name' => $owner->getTitle(),
        'user_id' => $owner->getIdentity(),
    );

    // Get video comment

    $data = array(
        'tagString' => $tagString,
        'categoryString' => $categoryString,
        'favorite_count' => $video->favorite_count,
        'creation_date' => strip_tags($this->view->timestamp($video->creation_date)),
        'ownerInfo' => $ownerInfo,
        'ratingInfo' => $ratingInfo,
        'isLiked' => (int) $video->likes()->isLike($viewer),
        'view_count' => $video->view_count,
        'video_title' => $video->getTitle(),
        'video_description' => $video->getDescription(),
        'video_location' => $video_location,
        'video_type' => $this->_helper->videoAPI->getMobileVideoType($video),
        'can_edit' => $can_edit,
        'can_delete' => $can_delete,
        'commentInfo' => $this->_helper->commonAPI->getCommentInfo($video)
    );

    $this->_jsonSuccessOutput($data);
  }

  /**
   * Comments on an item
   * Corresponding URL: /core/comment/create
   */
  public function postItemCommentAction()
  {
    $this->_initCommonItemAction();

    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    $form = new Core_Form_Comment_Create();

    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }

    if (!$form->isValid($this->_getAllParams())) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
    }

    // Process
    // Filter HTML
    $filter = new Zend_Filter();
    $filter->addFilter(new Engine_Filter_Censor());
    $filter->addFilter(new Engine_Filter_HtmlSpecialChars());

    $body = $form->getValue('body');
    $body = $filter->filter($body);


    $db = $subject->comments()->getCommentTable()->getAdapter();
    $db->beginTransaction();

    try {
      $comment = $subject->comments()->addComment($viewer, $body);

      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
      $subjectOwner = $subject->getOwner('user');

      // Activity
      $action = $activityApi->addActivity($viewer, $subject, 'comment_' . $subject->getType(), '', array(
          'owner' => $subjectOwner->getGuid(),
          'body' => $body
      ));

      //$activityApi->attachActivity($action, $subject);
      // Notifications
      // Add notification for owner (if user and not viewer)
      $this->view->subject = $subject->getGuid();
      $this->view->owner = $subjectOwner->getGuid();
      if ($subjectOwner->getType() == 'user' && $subjectOwner->getIdentity() != $viewer->getIdentity()) {
        $notifyApi->addNotification($subjectOwner, $viewer, $subject, 'commented', array(
            'label' => $subject->getShortType()
        ));
      }

      // Add a notification for all users that commented or like except the viewer and poster
      // @todo we should probably limit this
      $commentedUserNotifications = array();
      foreach ($subject->comments()->getAllCommentsUsers() as $notifyUser) {
        if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity())
          continue;

        // Don't send a notification if the user both commented and liked this
        $commentedUserNotifications[] = $notifyUser->getIdentity();

        $notifyApi->addNotification($notifyUser, $viewer, $subject, 'commented_commented', array(
            'label' => $subject->getShortType()
        ));
      }

      // Add a notification for all users that liked
      // @todo we should probably limit this
      foreach ($subject->likes()->getAllLikesUsers() as $notifyUser) {
        // Skip viewer and owner
        if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity())
          continue;

        // Don't send a notification if the user both commented and liked this
        if (in_array($notifyUser->getIdentity(), $commentedUserNotifications))
          continue;

        $notifyApi->addNotification($notifyUser, $viewer, $subject, 'liked_commented', array(
            'label' => $subject->getShortType()
        ));
      }

      // Increment comment count
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput($e->getMessage());
    }

    $this->_jsonSuccessOutput(array(
        'message' => 'Comment added',
        'comment_id' => $comment->getIdentity()
    ));
  }

  /**
   * Likes an item
   * Corresponding URL: core/comment/like
   */
  public function likeItemAction()
  {
    $this->_initCommonItemAction();

    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $comment_id = $this->_getParam('comment_id');

    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }

    if ($comment_id) {
      $commentedItem = $subject->comments()->getComment($comment_id);
    } else {
      $commentedItem = $subject;
    }

    // Process
    $db = $commentedItem->likes()->getAdapter();
    $db->beginTransaction();

    try {

      $commentedItem->likes()->addLike($viewer);

      // Add notification
      $owner = $commentedItem->getOwner();
      $this->view->owner = $owner->getGuid();
      if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity()) {
        $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
        $notifyApi->addNotification($owner, $viewer, $commentedItem, 'liked', array(
            'label' => $commentedItem->getShortType()
        ));
      }

      // Stats
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.likes');

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput($e->getMessage());
    }

    $this->_jsonSuccessOutput(array(
        'message' => Zend_Registry::get('Zend_Translate')->_('Like added')
    ));
  }

  /**
   * Unlikes an item
   * Corresponding URL: core/comment/unlike
   */
  public function unlikeItemAction()
  {
    $this->_initCommonItemAction();

    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $comment_id = $this->_getParam('comment_id');

    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }

    if ($comment_id) {
      $commentedItem = $subject->comments()->getComment($comment_id);
    } else {
      $commentedItem = $subject;
    }

    // Process
    $db = $commentedItem->likes()->getAdapter();
    $db->beginTransaction();

    try {
      $commentedItem->likes()->removeLike($viewer);

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput($e->getMessage());
    }

    $this->_jsonSuccessOutput(array(
        'message' => Zend_Registry::get('Zend_Translate')->_('Like removed')
    ));
  }

  /**
   * Deletes an item's comment
   * Corresponding URL: core/comment/delete
   */
  public function deleteCommentAction()
  {
    $this->_initCommonItemAction();

    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    // Comment id
    $comment_id = $this->_getParam('comment_id');
    if (!$comment_id) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::COMMENT_NOT_FOUND);
    }

    // Comment
    $comment = $subject->comments()->getComment($comment_id);
    if (!$comment) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::COMMENT_NOT_FOUND);
    }

    // Authorization
    if (!$subject->authorization()->isAllowed($viewer, 'edit') &&
            ($comment->poster_type != $viewer->getType() ||
            $comment->poster_id != $viewer->getIdentity())) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    // Method
    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }

    // Process
    $db = $subject->comments()->getCommentTable()->getAdapter();
    $db->beginTransaction();

    try {
      $subject->comments()->removeComment($comment_id);

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput($e->getMessage());
    }

    $this->_jsonSuccessOutput(array(
        'message' => Zend_Registry::get('Zend_Translate')->_('Comment deleted')
    ));
  }

  public function deleteAlbumAction()
  {
    $parent_guid = $this->getRequest()->getParam('parent_guid');
    if ($parent_guid) {
      $parent = Engine_Api::_()->getItemByGuid($parent_guid);
      switch ($parent->getType()) {
        case 'sitepage_page':
          try {
            $params = $this->getRequest()->getParams();
            if (!Engine_Api::_()->getItem('sitepage_album', $params['album_id'])) {
              $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ALBUM_NOT_FOUND);
            }
            $params['page_id'] = $parent->getIdentity();
            // Transfer request to desired controller
            $this->_customDispatch('delete', 'album', 'sitepage', $params);
          } catch (Exception $e) {
            $this->_jsonErrorOutput($e->getMessage());
          }
          break;
      }
    } else {
      // No parent guid means performing actions on user's item
      if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid()) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
      }

      if (0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
              null !== ($photo = Engine_Api::_()->getItem('album_photo', $photo_id))) {
        Engine_Api::_()->core()->setSubject($photo);
      } else if (0 !== ($album_id = (int) $this->_getParam('album_id')) &&
              null !== ($album = Engine_Api::_()->getItem('album', $album_id))) {
        Engine_Api::_()->core()->setSubject($album);
      }

      $viewer = Engine_Api::_()->user()->getViewer();
      $album = Engine_Api::_()->getItem('album', $this->getRequest()->getParam('album_id'));
      if (!$this->_helper->requireAuth()->setAuthParams($album, null, 'delete')->isValid()) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
      }

      if (!$album) {
        $this->_jsonErrorOutput(Zend_Registry::get('Zend_Translate')->_("Album doesn't exists or not authorized to delete"));
      }

      if (!$this->getRequest()->isPost()) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
      }

      $db = $album->getTable()->getAdapter();
      $db->beginTransaction();

      try {
        $album->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        $this->_jsonErrorOutput($e->getMessage());
      }
    }

    $this->_jsonSuccessOutput(array(
        'message' => Zend_Registry::get('Zend_Translate')->_('Album has been deleted.')
    ));
  }

  public function deletePhotoAction()
  {

    $parent_guid = $this->getRequest()->getParam('parent_guid');
    if ($parent_guid) {
      $parent = Engine_Api::_()->getItemByGuid($parent_guid);
      switch ($parent->getType()) {
        case 'sitepage_page':
          try {
            $this->getRequest()->setPost('confirm', true);
            $params = $this->getRequest()->getParams();
            $photo = Engine_Api::_()->getItem('sitepage_photo', $params['photo_id']);
            if (!$photo) {
              $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ITEM_NOT_FOUND);
            }
            $params['page_id'] = $photo->page_id;
            $params['album_id'] = $photo->album_id;
            // Transfer request to desired controller
            $this->_customDispatch('remove', 'photo', 'sitepage', $params);
          } catch (Exception $e) {
            $this->_jsonErrorOutput($e->getMessage());
          }
          break;
      }
    } else {
      if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid()) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
      }

      if (0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
              null !== ($photo = Engine_Api::_()->getItem('album_photo', $photo_id))) {
        Engine_Api::_()->core()->setSubject($photo);
      }

      if (!$this->_helper->requireSubject('album_photo')->isValid()) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::PHOTO_NOT_FOUND);
      }
      if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'delete')->isValid()) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
      }

      $photo = Engine_Api::_()->core()->getSubject('album_photo');

      $form = new Album_Form_Photo_Delete();

      if (!$this->getRequest()->isPost()) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
      }
      if (!$form->isValid($this->getRequest()->getPost())) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
      }

      $db = $photo->getTable()->getAdapter();
      $db->beginTransaction();

      try {
        $photo->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        $this->_jsonErrorOutput($e->getMessage());
      }
    }

    $this->_jsonSuccessOutput(array(
        'message' => Zend_Registry::get('Zend_Translate')->_('Photo deleted')
    ));
  }

  public function shareItemAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    $type = $this->_getParam('type');
    $id = $this->_getParam('id');

    $viewer = Engine_Api::_()->user()->getViewer();
    $attachment = Engine_Api::_()->getItem($type, $id);
    $form = new Activity_Form_Share();

    if (!$attachment) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::FEED_NOT_SHAREABLE);
    }

    // hide facebook and twitter option if not logged in
    $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
    if (!$facebookTable->isConnected()) {
      $form->removeElement('post_to_facebook');
    }

    $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
    if (!$twitterTable->isConnected()) {
      $form->removeElement('post_to_twitter');
    }

    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
    }

    // Process

    $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
    $db->beginTransaction();

    try {
      // Get body
      $body = $form->getValue('body');
      // Set Params for Attachment
      $params = array(
          'type' => '<a href="' . $attachment->getHref() . '">' . $attachment->getMediaType() . '</a>',
      );

      // Add activity
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
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
      $publishMessage = html_entity_decode($form->getValue('body'));
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
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput($e->getMessage());
    }

    $this->_jsonSuccessOutput(array(
        'feed_id' => $action->getIdentity(),
        'message' => Zend_Registry::get('Zend_Translate')->_('Success!')
    ));
  }

  public function deleteVideoAction()
  {

    $parent_guid = $this->getRequest()->getParam('parent_guid');
    if ($parent_guid) {
      $parent = Engine_Api::_()->getItemByGuid($parent_guid);
      switch ($parent->getType()) {
        case 'sitepage_page':
          try {
            $params = $this->getRequest()->getParams();
            if (!Engine_Api::_()->getItem('sitepagevideo_video', $params['video_id'])) {
              $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::VIDEO_NOT_FOUND);
            }
            $params['page_id'] = $parent->getIdentity();
            // Transfer request to desired controller
            $this->_customDispatch('delete', 'index', 'sitepagevideo', $params);
          } catch (Exception $e) {
            $this->_jsonErrorOutput($e->getMessage());
          }
          break;
      }
    } else {
      // Default is user video
      $video = Engine_Api::_()->getItem('video', $this->getRequest()->getParam('video_id'));
      if (!$this->_helper->requireAuth()->setAuthParams($video, null, 'delete')->isValid()) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
      }

      if (!$video) {
        $this->_jsonErrorOutput(Zend_Registry::get('Zend_Translate')->_("Video doesn't exists or not authorized to delete."));
      }

      if (!$this->getRequest()->isPost()) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
      }

      $db = $video->getTable()->getAdapter();
      $db->beginTransaction();

      try {
        Engine_Api::_()->getApi('core', 'ynvideo')->deleteVideo($video);
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        $this->_jsonErrorOutput($e->getMessage());
      }
    }

    $this->_jsonSuccessOutput(array(
        'message' => Zend_Registry::get('Zend_Translate')->_('Video has been deleted.')
    ));
  }

//  public function editProfileAction() {
//    $this->_profileEditActionInit();
//    $this->view->user = $user = Engine_Api::_()->core()->getSubject();
//    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
//
//    $editUserHelper = $this->_helper->editUser;
//
//    // General form w/o profile type
//    $aliasedFields = $user->fields()->getFieldsObjectsByAlias();
//    $topLevelId = 0;
//    $topLevelValue = null;
//    if (isset($aliasedFields['profile_type'])) {
//      $aliasedFieldValue = $aliasedFields['profile_type']->getValue($user);
//      $topLevelId = $aliasedFields['profile_type']->field_id;
//      $topLevelValue = ( is_object($aliasedFieldValue) ? $aliasedFieldValue->value : null );
//      if (!$topLevelId || !$topLevelValue) {
//        $topLevelId = null;
//        $topLevelValue = null;
//      }
//    }
//
//    // Get form
//    $form = new Zulu_Form_Edit_ProfileFields(array(
//        'item' => Engine_Api::_()->core()->getSubject(),
//        'topLevelId' => $topLevelId,
//        'topLevelValue' => $topLevelValue,
//        'hasPrivacy' => true,
//        'privacyValues' => $this->getRequest()->getParam('privacy'),
//    ));
//
//    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
//      $editUserHelper->uploadPhoto();
//
//      $form->saveValues();
//
//      // Update display name
//      $aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($user);
//      $user->setDisplayName($aliasValues);
//      //$user->modified_date = date('Y-m-d H:i:s');
//      $user->save();
//
//      // update networks
//      Engine_Api::_()->network()->recalculate($user);
//
//      $this->_jsonSuccessOutput(array(
//          'message' => Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.')
//      ));
//    }
//  }
//
  public function fetchInboxConversationsAction()
  {
    $this->_initMessageAction();

    $viewer = Engine_Api::_()->user()->getViewer();

    $paginator = $this->_helper->messageAPI->getAllMessagesPaginator();
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $unread = Engine_Api::_()->messages()->getUnreadMessageCount($viewer);

    $conversations = array();
    foreach ($paginator as $conversation) {
      $message = $conversation->getInboxMessage($viewer);
      if (!$message) {
        $message = $conversation->getOutboxMessage($viewer);
      }
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

      // Get sender name
      if (!empty($resource)) {
        $sender_name = $resource->toString();
      } elseif ($conversation->recipients == 1) {
        $sender_name = $sender->getTitle();
      } else {
        $sender_name = $this->view->translate(array('%s person', '%s people', $conversation->recipients), $this->view->locale()->toNumber($conversation->recipients));
      }

      // Get conversation title
      !( isset($message) && '' != ($title = trim($message->getTitle())) ||
              isset($conversation) && '' != ($title = trim($conversation->getTitle())) ||
              $title = $this->view->translate('(No Subject)') );

      $conversations[] = array(
          'conversation_id' => $conversation->conversation_id,
          'is_many_people' => (int) ($conversation->recipients > 1),
          'sender_info' => array(
              'name' => $sender_name,
              'photo' => $sender->getPhotoUrl('thumb.icon'),
              'id' => $sender->getIdentity(),
          ),
          'conversation_date' => $message->date,
          'conversation_title' => $title,
          'conversation_body' => $message->body,
          'is_unread' => !$recipient->inbox_read ? 1 : 0
      );
    }
    $data = array(
        'unreadMessageCount' => (int) $unread,
        'conversations' => $conversations,
        'totalMessages' => $paginator->getTotalItemCount(),
        'itemCountPerPage' => $paginator->getItemCountPerPage(),
        'currentPageNumber' => $paginator->getCurrentPageNumber(),
    );

    $this->_jsonSuccessOutput($data);
  }

  public function fetchOutboxConversationsAction()
  {
    $this->_initMessageAction();

    $viewer = Engine_Api::_()->user()->getViewer();
    $paginator = Engine_Api::_()->getItemTable('messages_conversation')->getOutboxPaginator($viewer);
    $paginator->setCurrentPageNumber($this->_getParam('page'));

    $conversations = array();
    foreach ($paginator as $conversation) {
      $message = $conversation->getOutboxMessage($viewer);
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
            break;
          }
        }
      }
      if ((!isset($sender) || !$sender)) {
        if ($viewer->getIdentity() !== $conversation->user_id) {
          $sender = Engine_Api::_()->user()->getUser($conversation->user_id);
        } else {
          $sender = $viewer;
        }
      }
      if (!isset($sender) || !$sender) {
        //continue;
        $sender = new User_Model_User(array());
      }

      // Get sender name
      if (!empty($resource)) {
        $sender_name = $resource->toString();
      } elseif ($conversation->recipients == 1) {
        $sender_name = $sender->getTitle();
      } else {
        $sender_name = $this->view->translate(array('%s person', '%s people', $conversation->recipients), $this->view->locale()->toNumber($conversation->recipients));
      }

      // Get conversation title
      !( isset($message) && '' != ($title = trim($message->getTitle())) ||
              isset($conversation) && '' != ($title = trim($conversation->getTitle())) ||
              $title = $this->view->translate('(No Subject)') );

      $conversations[] = array(
          'conversation_id' => $conversation->conversation_id,
          'is_many_people' => (int) ($conversation->recipients > 1),
          'sender_info' => array(
              'name' => $sender_name,
              'photo' => $sender->getPhotoUrl('thumb.icon'),
              'id' => $sender->getIdentity(),
          ),
          'conversation_date' => $message->date,
          'conversation_title' => $title,
          'conversation_body' => $message->body,
      );
    }

    $data = array(
        'conversations' => $conversations,
        'totalMessages' => $paginator->getTotalItemCount(),
        'itemCountPerPage' => $paginator->getItemCountPerPage(),
        'currentPageNumber' => $paginator->getCurrentPageNumber(),
    );

    $this->_jsonSuccessOutput($data);
  }

  public function composeMessageAction()
  {
    $this->_initMessageAction();

    // Make form
    $form = new Messages_Form_Compose();

    // Get params
    $multi = $this->_getParam('multi');
    $to = $this->_getParam('to');
    $viewer = Engine_Api::_()->user()->getViewer();
    $toObject = null;

    // Build
    $isPopulated = false;
    if (!empty($to) && (empty($multi) || $multi == 'user')) {
      $multi = null;
      // Prepopulate user
      $toUser = Engine_Api::_()->getItem('user', $to);
      $isMsgable = ( 'friends' != Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth') ||
              $viewer->membership()->isMember($toUser) );
      if ($toUser instanceof User_Model_User &&
              (!$viewer->isBlockedBy($toUser) && !$toUser->isBlockedBy($viewer)) &&
              isset($toUser->user_id) &&
              $isMsgable) {
        $toObject = $toUser;
        $form->toValues->setValue($toUser->getGuid());
        $isPopulated = true;
      } else {
        $multi = null;
        $to = null;
      }
    } else if (!empty($to) && !empty($multi)) {
      // Prepopulate group/event/etc
      $item = Engine_Api::_()->getItem($multi, $to);
      // Potential point of failure if primary key column is something other
      // than $multi . '_id'
      $item_id = $multi . '_id';
      if ($item instanceof Core_Model_Item_Abstract &&
              isset($item->$item_id) && (
              $item->isOwner($viewer) ||
              $item->authorization()->isAllowed($viewer, 'edit')
              )) {
        $toObject = $item;
        $form->toValues->setValue($item->getGuid());
        $isPopulated = true;
      } else {
        $multi = null;
        $to = null;
      }
    }

    // Get config
    $maxRecipients = 10;

    // Check method/data
    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
    }

    // Process
    $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();

    try {
      // Try attachment getting stuff
      $attachment = null;

      $attachmentType = $this->getRequest()->getPost('attachmentType');

      $attachmentData = null;

      if ($attachmentType == 'video') {
        $attachmentData = $this->_processVideo();
      } else if ($attachmentType == 'photo') {
        $attachmentData = $this->_processPhoto();
      } else if ($attachmentType == 'link') {
        $attachmentData = $this->_processLink();
      }

      if (!empty($attachmentData)) {
        $attachmentData['type'] = $attachmentType;
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
          $parent = $attachment->getParent();
          if ($parent->getType() === 'user') {
            $attachment->search = 0;
            $attachment->save();
          } else {
            $parent->search = 0;
            $parent->save();
          }
        }
      }
      $values = $form->getValues();

      // Prepopulated
      if ($toObject instanceof User_Model_User) {
        $recipientsUsers = array($toObject);
        $recipients = $toObject;
        // Validate friends
        if ('friends' == Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth')) {
          if (!$viewer->membership()->isMember($recipients)) {
            $this->_jsonErrorOutput('One of the members specified is not in your friends list.');
          }
        }
      } else if ($toObject instanceof Core_Model_Item_Abstract &&
              method_exists($toObject, 'membership')) {
        $recipientsUsers = $toObject->membership()->getMembers();
        $recipients = $toObject;
      }
      // Normal
      else {
        $recipients = preg_split('/[,. ]+/', $values['toValues']);
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
              $this->_jsonErrorOutput('One of the members specified is not in your friends list.');
            }
          }
        }
      }

      // Create conversation
      $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
              $viewer, $recipients, $values['title'], $values['body'], $attachment
      );

      // Send notifications
//      foreach ($recipientsUsers as $user) {
//        if ($user->getIdentity() == $viewer->getIdentity()) {
//          continue;
//        }
//        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
//                $user, $viewer, $conversation, 'message_new'
//        );
//      }
      // Increment messages counter
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

      // Commit
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput($e->getMessage());
    }

    $this->_jsonSuccessOutput(array(
        'message' => Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.')
    ));
  }

  public function fetchMessagesOfConversationAction()
  {
    $this->_initMessageAction();

    $id = $this->_getParam('id');
    $viewer = Engine_Api::_()->user()->getViewer();

    // Get conversation info
    $conversation = Engine_Api::_()->getItem('messages_conversation', $id);

    // Make sure the user is part of the conversation
    if (!$conversation || !$conversation->hasRecipient($viewer)) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_BELONG_TO_CONVERSATION);
    }

    // Check for resource
    if (!empty($conversation->resource_type) &&
            !empty($conversation->resource_id)) {
      $resource = Engine_Api::_()->getItem($conversation->resource_type, $conversation->resource_id);
      if (!($resource instanceof Core_Model_Item_Abstract)) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_BELONG_TO_CONVERSATION);
      }
    }
    // Otherwise get recipients
    else {
      $recipients = $conversation->getRecipients();

      $blocked = false;
      $blocker = "";

      // This is to check if the viewered blocked a member
      $viewer_blocked = false;
      $viewer_blocker = "";

      foreach ($recipients as $recipient) {
        if ($viewer->isBlockedBy($recipient)) {
          $blocked = true;
          $blocker = $recipient;
        } elseif ($recipient->isBlockedBy($viewer)) {
          $viewer_blocked = true;
          $viewer_blocker = $recipient;
        }
      }
    }
    $canReply = !$conversation->locked && ((!$blocked && !$viewer_blocked) || (count($recipients) > 1));

    $messages = $conversation->getMessages($viewer);

    $refinedMessages = array();
    $i = 0;
    foreach ($messages as $message) {
      $user = Engine_Api::_()->user()->getUser($message->user_id);
      $refinedMessages[$i]['sender_photo'] = $user->getPhotoUrl('thumb.icon');
      $refinedMessages[$i]['sender_name'] = $user->getTitle();
      $refinedMessages[$i]['sender_id'] = $user->getIdentity();
      $refinedMessages[$i]['sent_date'] = strip_tags($this->view->timestamp($message->date));
      $refinedMessages[$i]['message_body'] = strip_tags($message->body);
      $refinedMessages[$i]['message_subject'] = trim($conversation->getTitle()) ? $conversation->getTitle() : $this->view->translate('(No Subject)');

      if (!empty($message->attachment_type) && null !== ($attachment = Engine_Api::_()->getInstance()->getItem($message->attachment_type, $message->attachment_id))) {
        $detail_photo = $video_location = $duration = '';
        // Process video attachment
        if (null != ($attachment->getRichContent(false, array('message' => $message->conversation_id)))) {
          $video_location = $this->_helper->videoAPI->getVideoLocation($attachment);

          $duration = $attachment->duration;
          if ($attachment->description) {
            $tmpBody = strip_tags($attachment->description);
            $description = (Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody);
          }

          if ($attachment->photo_id) {
            $thumb = $attachment->getPhotoUrl('thumb.video.activity');
          } else {
            $thumb = Zend_Registry::get('StaticBaseUrl') . 'application/modules/Video/externals/images/video.png';
          }
        }
        // Process other types
        else {
          $thumb = (string) $attachment->getPhotoUrl('thumb.feed');
          $detail_photo = (string) $attachment->getPhotoUrl();
          $description = strip_tags($attachment->getDescription());
        }

        $refinedAttachment = array();
        $refinedAttachment['attachment_id'] = $attachment->getIdentity();
        $refinedAttachment['attachment_type'] = $attachment->getType();
        $refinedAttachment['attachment_title'] = $attachment->getTitle();
        $refinedAttachment['attachment_description'] = $description ? $description : '';
        $refinedAttachment['attachment_thumbnail_photo_url'] = $thumb ? $thumb : '';
        $refinedAttachment['attachment_detail_photo_url'] = $detail_photo;
        $refinedAttachment['attachment_video_url'] = $video_location;
        $refinedAttachment['attachment_video_duration'] = $duration;

        // add attachment item to the output
        $refinedMessages[$i]['message_attachment_info'] = $refinedAttachment;
      }
      $i++;
    }

    $conversation->
            setAsRead($viewer);

    $this->_jsonSuccessOutput(array(
        'messages' => $refinedMessages,
        'canReply' => (int) $canReply
    ));
  }

  public function sendReplyAction()
  {
    $this->_initMessageAction();

    $id = $this->_getParam('id');
    $viewer = Engine_Api::_()->user()->getViewer();

    // Get conversation info
    $conversation = Engine_Api::_()->getItem('messages_conversation', $id);

//    $recipients = $conversation->getRecipients();
    // Can we reply?
    if (!$conversation->locked) {

      // Process form
      $form = new Messages_Form_Reply();
      if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
        $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
        $db->beginTransaction();
        try {
          // Try attachment getting stuff
          $attachment = null;

          $attachmentType = $this->getRequest()->getPost('attachmentType');

          $attachmentData = null;

          if ($attachmentType == 'video') {
            $attachmentData = $this->_processVideo();
          } else if ($attachmentType == 'photo') {
            $attachmentData = $this->_processPhoto();
          } else if ($attachmentType == 'link') {
            $attachmentData = $this->_processLink();
          }

          if (!empty($attachmentData)) {
            $attachmentData['type'] = $attachmentType;
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

              $parent = $attachment->getParent();
              if ($parent->getType() === 'user') {
                $attachment->search = 0;
                $attachment->save();
              } else {
                $parent->search = 0;
                $parent->save();
              }
            }
          }

          $values = $form->getValues();
          $values['conversation'] = (int) $id;

          $conversation->reply(
                  $viewer, $values['body'], $attachment
          );

          // Send notifications
//          foreach ($recipients as $user) {
//            if ($user->getIdentity() == $viewer->getIdentity()) {
//              continue;
//            }
//            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
//                    $user, $viewer, $conversation, 'message_new'
//            );
//          }
          // Increment messages counter
          Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

          $db->commit();
        } catch (Exception $e) {
          $db->rollBack();
          $this->_jsonErrorOutput($e->getMessage());
        }

        $this->_jsonSuccessOutput(array(
            'message' => Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.')
        ));
      }
    } else {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::CANNOT_REPLY);
    }
  }

  public function fetchNotificationsAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }
    /* @var $commonHelper Mgslapi_Controller_Action_Helper_CommonAPI */
    $commonHelper = $this->_helper->commonAPI;

    $page = $this->_getParam('page');
    $viewer = Engine_Api::_()->user()->getViewer();
    $notifications = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationsPaginator($viewer);
    $notifications->setCurrentPageNumber($page);
    $notifications->setItemCountPerPage(Engine_Api::_()->getApi('settings', 'core')->getSetting('aaf.notifications.inupdate', 10));

    $refinedNotifications = array();
    $i = 0;
    foreach ($notifications as $notification) {
      $body_template = $notification->getTypeInfo()->body;
      $subject = $notification->getSubject();
      $object = $notification->getObject();

      $refinedNotifications[$i] = array();

      $refinedNotifications[$i]['notification_id'] = $notification->getIdentity();
      $refinedNotifications[$i]['body'] = strip_tags($notification->__toString());

      $refinedNotifications[$i]['time'] = strip_tags($this->view->timestamp(strtotime($notification->date)));
      $refinedNotifications[$i]['is_unread'] = (int) !$notification->read;

      $subject_info = $commonHelper->getBasicInfoFromItem($subject);
      $object_info = $commonHelper->getBasicInfoFromItem($object);

      $refinedNotifications[$i]['subject_info'] = (object) $subject_info;

      // If cannot find object in body template
      if (strpos($body_template, 'object') === false) {
        $refinedNotifications[$i]['target_object_info'] = (object) $subject_info;
      } else {
        $refinedNotifications[$i]['target_object_info'] = (object) $object_info;
      }
      $refinedNotifications[$i]['notification_type'] = $notification->type;
      $i++;
    }
    $this->_jsonSuccessOutput(array(
        'notifications' => $refinedNotifications,
        'unreadNotificationCount' => Engine_Api::_()->getDbtable('notifications', 'activity')->hasNotifications($viewer),
        'totalNotifications' => $notifications->getTotalItemCount(),
        'itemCountPerPage' => $notifications->getItemCountPerPage()
    ));
  }

  public function updateNotificationReadStatusAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    $request = Zend_Controller_Front::getInstance()->getRequest();

    $action_id = $request->getParam('notification_id', 0);

    $viewer = Engine_Api::_()->user()->getViewer();
    $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
    $db = $notificationsTable->getAdapter();
    $db->beginTransaction();

    try {
      $notification = Engine_Api::_()->getItem('activity_notification', $action_id);
      if ($notification) {
        $notification->read = 1;
        $notification->save();
      }
      // Commit
      $db->commit();

      $this->_jsonSuccessOutput(array(
          'message' => $this->_translator->_('Read status updated')
      ));
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput($e->getMessage());
    }
  }

  public function fetchFriendRequestsAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    $requests = Engine_Api::_()->getDbtable('notifications', 'activity')->getRequestsPaginator($viewer);

    $refinedRequests = array();
    $i = 0;
    foreach ($requests as $notification) {
      if ($notification->type == 'friend_request') {
        $sender = Engine_Api::_()->getItem($notification->subject_type, $notification->subject_id);

        $refinedRequests[$i] = array(
            'notification_id' => $notification->getIdentity(),
            'sender_type' => $notification->subject_type,
            'sender_id' => $notification->subject_id,
            'sender_photo' => $sender->getPhotoUrl('thumb.icon'),
            'sender_name' => $sender->getTitle(),
            'requested_time' => strip_tags($this->view->timestamp(strtotime($notification->date))),
            'request_message' => $this->view->translate('%1$s has sent you a friend request.', $sender->getTitle())
        );
        $i++;
      }
    }

    $this->_jsonSuccessOutput(array(
        'requests' => $refinedRequests
    ));
  }

  public function acceptFriendRequestAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    // Get viewer and other user
    $viewer = Engine_Api::_()->user()->getViewer();
    if (null == ($user_id = $this->_getParam('user_id')) ||
            null == ($user = Engine_Api::_()->getItem('user', $user_id))) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::USER_NOT_FOUND);
    }

    // Make form
    $form = new User_Form_Friends_Confirm();

    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
    }

    $friendship = $viewer->membership()->getRow($user);
    if ($friendship->active) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ALLREADY_FRIENDS);
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try {
      $viewer->membership()->setResourceApproved($user);

      // Add activity
      if (!$user->membership()->isReciprocal()) {
        Engine_Api::_()->getDbtable('actions', 'activity')
                ->addActivity($user, $viewer, 'friends_follow', '{item:$subject} is now following {item:$object}.');
      } else {
        Engine_Api::_()->getDbtable('actions', 'activity')
                ->addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.');
        Engine_Api::_()->getDbtable('actions', 'activity')
                ->addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.');
      }

      // Add notification
      if (!$user->membership()->isReciprocal()) {
        Engine_Api::_()->getDbtable('notifications', 'activity')
                ->addNotification($user, $viewer, $user, 'friend_follow_accepted');
      } else {
        Engine_Api::_()->getDbtable('notifications', 'activity')
                ->addNotification($user, $viewer, $user, 'friend_accepted');
      }

      // Set the requests as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
              ->getNotificationBySubjectAndType($viewer, $user, 'friend_request');
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

      // Increment friends counter
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.friendships');

      $db->commit();

      $message = Zend_Registry::get('Zend_Translate')->_('You are now friends with %s');
      $message = sprintf($message, $user->__toString());

      $this->_jsonSuccessOutput(array(
          'message' => strip_tags($message)
      ));
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput();
    }
  }

  public function rejectFriendRequestAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    // Get viewer and other user
    $viewer = Engine_Api::_()->user()->getViewer();
    if (null == ($user_id = $this->_getParam('user_id')) ||
            null == ($user = Engine_Api::_()->getItem('user', $user_id))) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::USER_NOT_FOUND);
    }

    // Make form
    $form = new User_Form_Friends_Reject();

    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try {
      if ($viewer->membership()->isMember($user)) {
        $viewer->membership()->removeMember($user);
      }

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
              ->getNotificationBySubjectAndType($viewer, $user, 'friend_request');
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

      $message = Zend_Registry::get('Zend_Translate')->_('You ignored a friend request from %s');
      $message = sprintf($message, $user->__toString());

      $this->_jsonSuccessOutput(array(
          'message' => strip_tags($message)
      ));
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput();
    }
  }

  public function addFriendAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    // Get viewer and other user
    $viewer = Engine_Api::_()->user()->getViewer();
    if (null == ($user_id = $this->_getParam('user_id')) ||
            null == ($user = Engine_Api::_()->getItem('user', $user_id))) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::USER_NOT_FOUND);
    }

    // check that user is not trying to befriend 'self'
    if ($viewer->isSelf($user)) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::SELF_FRIEND);
    }

    // check that user is already friends with the member
    if ($user->membership()->isMember($viewer)) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ALLREADY_FRIENDS);
    }

    // check that user has not blocked the member
    if ($viewer->isBlocked($user)) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::BLOCKED_FRIEND);
    }

    // Make form
    $this->view->form = $form = new User_Form_Friends_Add();

    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try {

      // send request
      $user->membership()
              ->addMember($viewer)
              ->setUserApproved($viewer);

      if (!$viewer->membership()->isUserApprovalRequired() && !$viewer->membership()->isReciprocal()) {
        // if one way friendship and verification not required
        // Add activity
        Engine_Api::_()->getDbtable('actions', 'activity')
                ->addActivity($viewer, $user, 'friends_follow', '{item:$subject} is now following {item:$object}.');

        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')
                ->addNotification($user, $viewer, $viewer, 'friend_follow');

        $message = Zend_Registry::get('Zend_Translate')->_("You are now following this member.");
      } else if (!$viewer->membership()->isUserApprovalRequired() && $viewer->membership()->isReciprocal()) {
        // if two way friendship and verification not required
        // Add activity
        Engine_Api::_()->getDbtable('actions', 'activity')
                ->addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.');
        Engine_Api::_()->getDbtable('actions', 'activity')
                ->addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.');

        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')
                ->addNotification($user, $viewer, $user, 'friend_accepted');

        $message = Zend_Registry::get('Zend_Translate')->_("You are now friends with this member.");
      } else if (!$user->membership()->isReciprocal()) {
        // if one way friendship and verification required
        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')
                ->addNotification($user, $viewer, $user, 'friend_follow_request');

        $message = Zend_Registry::get('Zend_Translate')->_("Your friend request has been sent.");
      } else if ($user->membership()->isReciprocal()) {
        // if two way friendship and verification required
        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')
                ->addNotification($user, $viewer, $user, 'friend_request');

        $message = Zend_Registry::get('Zend_Translate')->_("Your friend request has been sent.");
      }

      $db->commit();


      $this->_jsonSuccessOutput(array(
          'message' => $message
      ));
    } catch (Exception $e) {
      $db->rollBack();

      $this->_jsonErrorOutput();
    }
  }

  public function removeFriendAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    // Get viewer and other user
    $viewer = Engine_Api::_()->user()->getViewer();
    if (null == ($user_id = $this->_getParam('user_id')) ||
            null == ($user = Engine_Api::_()->getItem('user', $user_id))) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::USER_NOT_FOUND);
    }

    // Make form
    $this->view->form = $form = new User_Form_Friends_Remove();

    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try {
      if ($this->_getParam('rev')) {
        $viewer->membership()->removeMember($user);
      } else {
        $user->membership()->removeMember($viewer);
      }

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

      $this->_jsonSuccessOutput(array(
          'message' => $message
      ));
    } catch (Exception $e) {
      $db->rollBack();

      $this->_jsonErrorOutput();
    }
  }

  public function fetchProfileEventsAction()
  {
    // Return $subject, $viewer
    extract($this->_helper->profileAPI->profileAuth());

    // Get paginator
    $membership = Engine_Api::_()->getDbtable('membership', 'event');
    $this->view->paginator = $paginator = Zend_Paginator::factory($membership->getMembershipsOfSelect($subject)->order('starttime DESC'));

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 10));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $refinedEvents = array();
    $i = 0;
    foreach ($paginator as $event) {
      $refinedEvents[$i]['item_id'] = $event->getIdentity();
      $refinedEvents[$i]['item_type'] = $event->getType();
      $refinedEvents[$i]['event_thumb_photo'] = $event->getPhotoUrl('thumb.normal');
      $refinedEvents[$i]['event_name'] = $event->getTitle();
      $refinedEvents[$i]['event_description'] = $event->getDescription();
      $refinedEvents[$i]['event_start_time'] = $this->view->locale()->toDateTime($event->starttime);
      $refinedEvents[$i]['event_guest_count'] = $event->member_count;
      $refinedEvents[$i]['can_edit'] = ($viewer && $event->isOwner($viewer)) ? 1 : 0;
      $refinedEvents[$i]['can_delete'] = ($viewer && $event->isOwner($viewer)) ? 1 : 0;
      $i++;
    }

    $this->_jsonSuccessOutput(array(
        'totalEvents' => $paginator->getTotalItemCount(),
        'events' => $refinedEvents
    ));
  }

  public function deleteEventAction()
  {
    $event = Engine_Api::_()->getItem('event', $this->getRequest()->getParam('event_id'));
    if (!$this->_helper->requireAuth()->setAuthParams($event, null, 'delete')->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    if (!$event) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::EVENT_NOT_FOUND);
    }

    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }

    $db = $event->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $event->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput($e->getMessage());
    }

    $this->_jsonSuccessOutput(array(
        'message' => Zend_Registry::get('Zend_Translate')->_('The selected event has been deleted.')
    ));
  }

  public function editAlbumAction()
  {
    $this->_initAlbumAction();

    if (!$this->_helper->requireSubject('album')->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ALBUM_NOT_FOUND);
    }

    $album = Engine_Api::_()->core()->getSubject();

    $form = new Album_Form_Album_Edit();

    $form->populate($album->toArray());
    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
    foreach ($roles as $role) {
      if (1 === $auth->isAllowed($album, $role, 'view') && isset($form->auth_view)) {
        $form->auth_view->setValue($role);
      }
      if (1 === $auth->isAllowed($album, $role, 'comment') && isset($form->auth_comment)) {
        $form->auth_comment->setValue($role);
      }
      if (1 === $auth->isAllowed($album, $role, 'tag') && isset($form->auth_tag)) {
        $form->auth_tag->setValue($role);
      }
    }

    // If API only requests for form data
    if ($this->getRequest()->getParam('fetch_mode') == 'form_data') {
      $form_data = $this->_getFormData($form);

      $this->_jsonSuccessOutput($form_data);
    }

    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
    }

    // Process
    $db = $album->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $values = $form->getValues();
      $album->setFromArray($values);
      $album->save();

      if (empty($values['auth_view'])) {
        $values['auth_view'] = key($form->auth_view->options);
        if (empty($values['auth_view'])) {
          $values['auth_view'] = 'everyone';
        }
      }
      if (empty($values['auth_comment'])) {
        $values['auth_comment'] = key($form->auth_comment->options);
        if (empty($values['auth_comment'])) {
          $values['auth_comment'] = 'owner_member';
        }
      }
      if (empty($values['auth_tag'])) {
        $values['auth_tag'] = key($form->auth_tag->options);
        if (empty($values['auth_tag'])) {
          $values['auth_tag'] = 'owner_member';
        }
      }

      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);
      $tagMax = array_search($values['auth_tag'], $roles);

      foreach ($roles as $i => $role) {
        $auth->setAllowed($album, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($album, $role, 'comment', ($i <= $commentMax));
        $auth->setAllowed($album, $role, 'tag', ($i <= $tagMax));
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput($e->getMessage());
    }

    $db->beginTransaction();
    try {
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actionTable->getActionsByObject($album) as $action) {
        $actionTable->resetActivityBindings($action);
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput($e->getMessage());
    }

    $this->_jsonSuccessOutput(array(
        'message' => $this->_translator->_('Your changes have been saved.')
    ));
  }

  public function editPhotoAction()
  {
    $this->_initAlbumAction();

    $photo_id = (int) $this->_getParam('photo_id');
    if (null === ($photo = Engine_Api::_()->getItem('album_photo', $photo_id))) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::PHOTO_NOT_FOUND);
    }

    // Prepare data
    $album = $photo->getAlbum();

    // Get albums
    $albumTable = Engine_Api::_()->getItemTable('album');
    $myAlbums = $albumTable->select()
            ->from($albumTable, array('album_id', 'title'))
            ->where('owner_type = ?', 'user')
            ->where('owner_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity())
            ->query()
            ->fetchAll();

    $albumOptions = array('' => '');
    foreach ($myAlbums as $myAlbum) {
      $albumOptions[$myAlbum['album_id']] = $myAlbum['title'];
    }
    if (count($albumOptions) == 1) {
      $albumOptions = array();
    }

    $form = new Album_Form_Album_EditPhoto(array('elementsBelongTo' => 'general_form'));
    $form->populate($photo->toArray());

    if ((int) $album->photo_id != (int) $photo->getIdentity()) {
      $form->addElement('Checkbox', 'cover', array(
          'label' => "Album Cover",
      ));
    }

    if (empty($albumOptions)) {
      $form->removeElement('move');
    } else {
      $form->move->setMultiOptions($albumOptions);
    }

    // If API only requests for form data
    if ($this->getRequest()->getParam('fetch_mode') == 'form_data') {
      $form_data = $this->_getFormData($form);

      $this->_jsonSuccessOutput($form_data);
    }

    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
    }

    $table = $album->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $values = $form->getValues()['general_form'];

      if (array_key_exists('cover', $values) && $values['cover'] == 1) {
        $album->photo_id = $photo->getIdentity();
        $album->save();
      }

      if (isset($values['delete']) && $values['delete'] == '1') {
        $photo->delete();
      } else if (!empty($values['move'])) {
        $nextPhoto = $photo->getNextPhoto();

        $photo->album_id = $values['move'];
        $photo->save();

        // Change album cover if necessary
        if (($nextPhoto instanceof Album_Model_Photo) &&
                (int) $album->photo_id == (int) $photo->getIdentity()) {
          $album->photo_id = $nextPhoto->getIdentity();
          $album->save();
        }

        // Remove activity attachments for this photo
        Engine_Api::_()->getDbtable('actions', 'activity')->detachFromActivity($photo);
      } else {
        $photo->setFromArray($values);
        $photo->save();
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput($e->getMessage());
    }

    $this->_jsonSuccessOutput(array(
        'message' => $this->_translator->_('Your changes have been saved.')
    ));
  }

  protected function _getFormData($form)
  {
    $form_data = array();

    $i = 0;
    foreach ($form->getElements() as $el) {
      $type = $this->_processType($el->getType());
      $form_data[$i]['field_label'] = $el->getLabel();
      $form_data[$i]['field_name'] = $el->getName();
      $form_data[$i]['field_type'] = $type;

      $options = array();
      if ($el->options) {
        foreach ($el->options as $k => $v) {
          $options[] = array($k => $v);
//          array_push($options, $v);
        }
      }

      $form_data[$i]['field_options'] = $options;
      $form_data[$i]['field_value'] = $el->getValue();
      $i++;
    }

    return $form_data;
  }

  /**
   * Used to get the last word of field type
   * @param type $type
   * @return type
   */
  protected function _processType($type)
  {
    $ret = strtolower($type);
    if (strpos($type, '_')) {
      $rets = explode('_', $ret);
      $ret = end($rets);
    }
    return $ret;
  }

  /**
   * members/edit/clinical
   */
  public function editMedicalRecordSharingAction()
  {
    Engine_Api::_()->authorization()->addAdapter(Engine_Api::_()->getDbTable('accessLevel', 'zulu'));

    $this->_initProfileEditAction();

    $user = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    $plugin = new Zulu_Plugin_Edit_ProfileSharing();
    if ($this->getRequest()->isPost()) {
      // Confirm that there was no transaction started up to this point
      Zend_Registry::set('trans_start', 0);

      $plugin->onSubmit($this->getRequest());
      $plugin->onProcess();
    } else {
      $plugin->resetSession();
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }

    $this->_jsonSuccessOutput(array(
        'message' => Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.')
    ));
  }

  public function fetchMedicalSharingAccessListsAction()
  {
    Engine_Api::_()->authorization()->addAdapter(Engine_Api::_()->getDbTable('accessLevel', 'zulu'));

    $this->_initProfileEditAction();

    $user = Engine_Api::_()->core()->getSubject();

    $profileShareTable = Engine_Api::_()->getDbTable('profileshare', 'zulu');
    $access_list = $profileShareTable->getAccessListOfUser($user->user_id);

    $refinedAccessList = array('full' => array(), 'read_only' => array(), 'limited' => array());
    $i = 0;
    foreach ($access_list as $key => $list) {
      foreach ($list as $user_id) {
        $refinedAccessList[$key][] = $this->_helper->commonAPI->getBasicInfoFromItem(Engine_Api::_()->getItem('user', $user_id));
        $i++;
      }
    }
    $this->_jsonSuccessOutput($refinedAccessList);
  }

  protected function _initProfileEditAction()
  {
    if (!Engine_Api::_()->core()->hasSubject()) {
      // Can specifiy custom id
      $id = $this->_getParam('id', null);
      $subject = null;
      if (null === $id) {
        $subject = Engine_Api::_()->user()->getViewer();
        Engine_Api::_()->core()->setSubject($subject);
      } else {
        $subject = Engine_Api::_()->getItem('user', $id);
        Engine_Api::_()->core()->setSubject($subject);
      }
    }

    if (!empty($id)) {
      $params = array('params' => array('id' => $id));
    } else {
      $params = array();
    }
    Zend_Controller_Action_HelperBroker::addHelper(new Zulu_Controller_Action_Helper_EditUser());

    $arrSharedAction = array('clinical');
    if (in_array($this->getRequest()->getActionName(), $arrSharedAction)) {
      Engine_Api::_()->authorization()->addAdapter(Engine_Api::_()->getDbTable('accessLevel', 'zulu'));
    }

    // Set up require's
    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }
    if (!$this->_helper->requireSubject('user')->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::USER_NOT_FOUND);
    }
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }
  }

  public function fetchCircleListAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $customFieldValues = array();
    $values = array();
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $select_category = $this->_getParam('category_id', 0);
    if (!empty($select_category) && empty($_GET['category'])) {
      $category = $select_category;
      $category_id = $select_category;
    } else {
      $category = $request->getParam('category_id', null);
      $category_id = $request->getParam('category', null);
    }
    $subcategory = $request->getParam('subcategory_id', null);
    $subcategory_id = $request->getParam('subcategory', null);
    $categoryname = $request->getParam('categoryname', null);
    $subcategoryname = $request->getParam('subcategoryname', null);
    $subsubcategory = $request->getParam('subsubcategory_id', null);
    $subsubcategory_id = $request->getParam('subsubcategory', null);
    $subsubcategoryname = $request->getParam('subsubcategoryname', null);

    if ($category)
      $_GET['category'] = $category;
    if ($subcategory)
      $_GET['subcategory'] = $subcategory;
    if ($categoryname)
      $_GET['categoryname'] = $categoryname;
    if ($subcategoryname)
      $_GET['subcategoryname'] = $subcategoryname;

    if ($subsubcategory)
      $_GET['subsubcategory'] = $subsubcategory;
    if ($subcategoryname)
      $_GET['subsubcategoryname'] = $subsubcategoryname;

    if ($category_id)
      $_GET['category'] = $values['category'] = $category_id;
    if ($subcategory_id)
      $_GET['subcategory'] = $values['subcategory'] = $subcategory_id;
    if ($subsubcategory_id)
      $_GET['subsubcategory'] = $values['subsubcategory'] = $subsubcategory_id;

    $values['tag'] = $request->getParam('tag', null);
    if (!empty($values['tag']))
      $_GET['tag'] = $values['tag'];

    if (isset($_GET['tag']) && !empty($_GET['tag'])) {
      $tag = $_GET['tag'];
      $page = 1;
      if (isset($_GET['page']) && !empty($_GET['page'])) {
        $page = $_GET['page'];
      }
      unset($_GET);
      $_GET['tag'] = $tag;
      $_GET['page'] = $page;
    }

    $values['sitepage_location'] = $request->getParam('sitepage_location', null);
    if (!empty($values['sitepage_location']))
      $_GET['sitepage_location'] = $values['sitepage_location'];

    if (isset($_GET['sitepage_location']) && !empty($_GET['sitepage_location'])) {
      $sitepage_location = $_GET['sitepage_location'];
      $_GET['sitepage_location'] = $sitepage_location;
    }

    //GET VALUE BY POST TO GET DESIRED SITEPAGES
    if (!empty($_GET)) {
      $values = $_GET;
    }

    //FORM GENERATION
    //$form = new Sitepage_Form_Search(array('type' => 'sitepage_page'));
    $form = Zend_Registry::isRegistered('Sitepage_Form_Search') ? Zend_Registry::get('Sitepage_Form_Search') : new Sitepage_Form_Search(array('type' => 'sitepage_page'));

    if (!empty($_GET))
      $form->populate($_GET);
    $values = $form->getValues();

    //BADGE CODE
    if ((int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge')) {
      if (isset($_POST['badge_id']) && !empty($_POST['badge_id'])) {
        $values['badge_id'] = $_POST['badge_id'];
      }
    }

    if (!empty($_GET['page'])) {
      $values['page'] = $_GET['page'];
    } else {
      $values['page'] = 1;
    }

    //GET LISITNG FPR PUBLIC PAGE SET VALUE
    $values['type'] = 'browse';
    $values['type_location'] = 'browsePage';

    if (@$values['show'] == 2) {

      //GET AN ARRAY OF FRIEND IDS
      $friends = $viewer->membership()->getMembers();

      $ids = array();
      foreach ($friends as $friend) {
        $ids[] = $friend->user_id;
      }

      $values['users'] = $ids;
    }

    //GEO-LOCATION WORK
    if ((int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagegeolocation') && isset($values['has_currentlocation']) && !empty($values['has_currentlocation'])) {

      $session = new Zend_Session_Namespace('Current_location');
      if (!isset($session->latitude) || !isset($session->longitude)) {
        $locationResult = null;
        $apiType = Engine_Api::_()->getApi('core', 'sitepagegeolocation')->getGeoApiType();
        if ($apiType == 1) {
          $locationResult = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getMaxmindCurrentLocation();
        } elseif ($apiType == 2) {
          $locationResult = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getMaxmindGeoLiteCountry();
        }
        if (($apiType == 1 || $apiType == 2) && !empty($locationResult)) {
          $values['latitude'] = $session->latitude = $locationResult['latitude'];
          $values['longitude'] = $session->longitude = $locationResult['longitude'];
        }
      } else {
        $values['latitude'] = $session->latitude;
        $values['longitude'] = $session->longitude;
      }
    }

    //CUSTOM FIELD WORK
    $customFieldValues = array_intersect_key($values, $form->getFieldElements());
    $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('sitepage', 'show');
    if ($viewer->getIdentity() && !empty($row) && !empty($row->display) && $form->show->getValue() == 3 && !isset($_GET['show'])) {
      @$values['show'] = 3;
    }

    //DON'T SEND CUSTOM FIELDS ARRAY IF EMPTY
    $has_value = 0;
    foreach ($customFieldValues as $customFieldValue) {
      if (!empty($customFieldValue)) {
        $has_value = 1;
        break;
      }
    }

    if (empty($has_value)) {
      $customFieldValues = null;
    }

    $values['browse_page'] = 1;

    // GET SITEPAGES
    $paginator = Engine_Api::_()->sitepage()->getSitepagesPaginator($values, $customFieldValues);

    $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.page', 10);
    $paginator->setItemCountPerPage($items_count);
    $paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));

    $refinedSitepages = array();
    $i = 0;
    foreach ($paginator as $sitepage) {
      $refinedSitepages[$i] = $this->_helper->circleAPI->getCircleBasicInfo($sitepage);
      $i++;
    }

    $this->_jsonSuccessOutput(array(
        'circles' => $refinedSitepages,
        'totalCircles' => $paginator->getTotalItemCount(),
        'itemCountPerPage' => $items_count
    ));
//    //PAGE-RATING IS ENABLED OR NOT
//    $this->view->ratngShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview');
//
//    //PAGE OFFER IS INSTALLED OR NOT
//    $this->view->sitepageOfferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer');
//    
//    $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.price.field', 1);
    //CAN CREATE PAGES OR NOT
//    $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('sitepage_page', $viewer, 'create');
  }

  public function fetchJoinedCirclesAction()
  {
    // Return $subject, $viewer
    extract($this->_helper->profileAPI->profileAuth());

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    $values = array();

    $pageAdminJoined = $this->_getParam('pageAdminJoined', 2);
    $values['category_id'] = $this->_getParam('category_id', 0);

    if ($pageAdminJoined == 1) {

      //GET PAGES
      $adminpages = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdminPages($subject->getIdentity());
      //GET STUFF
      $ids = array();
      foreach ($adminpages as $adminpage) {
        $ids[] = $adminpage->page_id;
      }
      $values['adminpages'] = $ids;

      $onlymember = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinPages($subject->getIdentity(), 'onlymember');

      $onlymemberids = array();
      foreach ($onlymember as $onlymembers) {
        $onlymemberids[] = $onlymembers->page_id;
      }
      if (!empty($onlymemberids)) {
        $values['adminpages'] = array_merge($onlymemberids, $values['adminpages']);
      }
    } else {
      $onlymember = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinPages($subject->getIdentity(), 'onlymember');

      $onlymemberids = array();
      foreach ($onlymember as $onlymembers) {
        $onlymemberids[] = $onlymembers->page_id;
      }
      $values['onlymember'] = $onlymemberids;
      if (empty($onlymemberids)) {
//        return $this->setNoRender();
      }
    }

    $values['type'] = 'browse';
    $values['orderby'] = 'creation_date';
    //	$values['type_location'] = 'manage';
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
      $values['type_location'] = 'profilebrowsePage';
    }

    $paginator = Engine_Api::_()->sitepage()->getSitepagesPaginator($values);
    $items_count = $this->_getParam('itemCountPerPage', 10);
    $paginator->setItemCountPerPage($items_count);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $refinedSitepages = array();
    $total_item = 0;
    if (!empty($onlymemberids)) {
      $i = 0;
      foreach ($paginator as $sitepage) {
        $refinedSitepages[$i] = $this->_helper->circleAPI->getCircleBasicInfo($sitepage);
        $refinedSitepages[$i]['verified'] = (int) (!empty($sitepage->page_owner_id) && $viewer_id != $sitepage->owner_id && empty($sitepage->member_approval));
        $i++;
      }
      $total_item = $paginator->getTotalItemCount();
    }

    $this->_jsonSuccessOutput(array(
        'circles' => $refinedSitepages,
        'totalCircles' => $total_item,
        'itemCountPerPage' => $items_count
    ));
  }

  public function fetchMyCirclesAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }
    $viewer = Engine_Api::_()->user()->getViewer();
//    $viewer_id = $viewer->getIdentity();
//    $can_edit = $this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'edit')->checkRequire();
//    $can_delete = $this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'delete')->checkRequire();

    Engine_Api::_()->getDbtable('pagestatistics', 'sitepage')->setViews();

    //FORM GENERATION
    $this->view->form = $form = new Sitepage_Form_Managesearch(array(
        'type' => 'sitepage_page'
    ));
    $form->removeElement('show');

    //PROCESS FORM
    if ($form->isValid($this->_getAllParams())) {
      $values = $form->getValues();
      if ($values['subcategory_id'] == 0) {
        $values['subsubcategory_id'] = 0;
        $values['subsubcategory'] = 0;
      }
    } else {
      $values = array();
    }
    //CHECK TO SEE IF REQUEST IS FOR SPECIFIC USER'S PAGES
    $values['user_id'] = $viewer->getIdentity();
    $values['type'] = 'manage';
    $values['type_location'] = 'manage';

    //GET PAGINATOR
    $paginator = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinPages($values['user_id'], 'getAllJoinedCircle', 0);
    $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.page', $this->_getParam('itemCountPerPage', 10));

    $paginator->setItemCountPerPage($items_count);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $refinedSitepages = array();
    $i = 0;
    foreach ($paginator as $sitepage) {
      $refinedSitepages[$i] = $this->_helper->circleAPI->getCircleBasicInfo($sitepage);
      $refinedSitepages[$i]['approved_date'] = $sitepage->aprrove_date;
      $refinedSitepages[$i]['can_edit'] = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
      $refinedSitepages[$i]['can_delete'] = $refinedSitepages[$i]['can_edit'];
      $i++;
    }

    $this->_jsonSuccessOutput(array(
//        'can_edit' => $can_edit,
//        'can_delete' => $can_delete,
        'circles' => $refinedSitepages,
        'totalCircles' => $paginator->getTotalItemCount(),
        'itemCountPerPage' => $items_count
    ));
  }

  public function fetchCirclesByLocationAction()
  {
    // Make form
    $form = new Sitepage_Form_Locationsearch(array('type' => 'sitepage_page'));

    $values = $_POST;
    $customFieldValues = array_intersect_key($values, $form->getFieldElements());

    unset($values['or']);
    //$this->view->formValues = array_filter($values);
    $viewer = Engine_Api::_()->user()->getViewer();
    if (@$values['show'] == 2) {

      //GET AN ARRAY OF FRIEND IDS
      $friends = $viewer->membership()->getMembers();

      $ids = array();
      foreach ($friends as $friend) {
        $ids[] = $friend->user_id;
      }

      $values['users'] = $ids;
    }
    $values['type'] = 'browse';
    $values['type_location'] = 'browseLocation';

    if (isset($values['show'])) {
      if ($form->show->getValue() == 3) {
        @$values['show'] = 3;
      }
    }

    $page = $this->_getParam('page', 1);

    //check for miles or street.
    if (isset($values['locationmiles']) && !empty($values['locationmiles'])) {
      if (isset($values['sitepage_street']) && !empty($values['sitepage_street'])) {
        $values['sitepage_location'] = $values['sitepage_street'] . ',';
        unset($values['sitepage_street']);
      }

      if (isset($values['sitepage_city']) && !empty($values['sitepage_city'])) {
        $values['sitepage_location'].= $values['sitepage_city'] . ',';
        unset($values['sitepage_city']);
      }

      if (isset($values['sitepage_state']) && !empty($values['sitepage_state'])) {
        $values['sitepage_location'].= $values['sitepage_state'] . ',';
        unset($values['sitepage_state']);
      }

      if (isset($values['sitepage_country']) && !empty($values['sitepage_country'])) {
        $values['sitepage_location'].= $values['sitepage_country'];
        unset($values['sitepage_country']);
      }
    }

    $result = Engine_Api::_()->sitepage()->getSitepagesSelect($values, $customFieldValues);
    $paginator = Zend_Paginator::factory($result);
    $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.page', $this->_getParam('itemCountPerPage', 15));
    $paginator->setItemCountPerPage($items_count);
    $paginator->setCurrentPageNumber($page);

    $refinedSitepages = array();
    $i = 0;
    foreach ($paginator as $sitepage) {
      $refinedSitepages[$i] = $this->_helper->circleAPI->getCircleBasicInfo($sitepage);
      $refinedSitepages[$i]['approved_date'] = $sitepage->aprrove_date;
      $i++;
    }

    $this->_jsonSuccessOutput(array(
        'circles' => $refinedSitepages,
        'totalCircles' => $paginator->getTotalItemCount(),
        'itemCountPerPage' => $items_count
    ));
  }

  public function fetchCircleInfoAction()
  {
    $this->_initCircleAction();

    //DONT RENDER IF NOT AUTHORIZED
    if (!Engine_Api::_()->core()->hasSubject()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::CIRCLE_NOT_FOUND);
    }

    //GET SUBJECT
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

    $refinedCircle = $this->_helper->circleAPI->getCircleBasicInfo($sitepage);
    $refinedCircle['last_updated_date'] = $this->_translator->_(gmdate('M d, Y', strtotime($sitepage->modified_date)));
    $refinedCircle['follow_count'] = $this->sitepage->follow_count;
    $refinedCircle['category'] = 1;

    $this->_jsonSuccessOutput($refinedCircle);
  }

  /**
   * Currently unused
   * @throws Exception
   */
  public function createCircleAction()
  {

    $this->_initCircleAction();

    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    //PAGE CREATE PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'create')->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    $package_id = 0;
    $viewer = Engine_Api::_()->user()->getViewer();
    $sitepage_is_approved = 'approved';
    $getPackageAuth = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepage');

    $package = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.lsettings', 0);

    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      //REDIRECT
      $package_id = $this->_getParam('id');
      if (empty($package_id)) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ITEM_NOT_FOUND);
      }
      $this->view->package = $package = Engine_Api::_()->getItemTable('sitepage_package')->fetchRow(array('package_id = ?' => $package_id, 'enabled = ?' => '1'));
      if (empty($this->view->package)) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ITEM_NOT_FOUND);
      }

      if (!empty($package->level_id) && !in_array($viewer->level_id, explode(",", $package->level_id))) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ITEM_NOT_FOUND);
      }
    } else {
      $package_id = Engine_Api::_()->getItemtable('sitepage_package')->fetchRow(array('defaultpackage = ?' => 1))->package_id;
    }

    $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
    $row = $manageadminsTable->createRow();

    //FORM VALIDATION
    $form = new Sitepage_Form_Create(array("packageId" => $package_id, "owner" => $viewer));

    $sitepageUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageurl');
    $show_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.showurl.column', 1);
    if (!empty($sitepageUrlEnabled) && empty($show_url)) {
      $form->removeElement('page_url');
      $form->removeElement('page_url_msg');
    }

    //SET UP DATA NEEDED TO CHECK QUOTA

    $sitepage_category = Zend_Registry::isRegistered('sitepage_category') ? Zend_Registry::get('sitepage_category') : null;
    $values['user_id'] = $viewer->getIdentity();

    //IF NOT POST OR FORM NOT VALID, RETURN
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $table = Engine_Api::_()->getItemTable('sitepage_page');
      $db = $table->getAdapter();
      $db->beginTransaction();

      try {
        // Create sitepage
        $values = array_merge($form->getValues(), array(
            'owner_id' => $viewer->getIdentity(),
            'package_id' => $package_id
        ));

        $is_error = 0;
        if (isset($values['category_id']) && empty($values['category_id'])) {
          $is_error = 1;
        }
        if (empty($values['subcategory_id'])) {
          $values['subcategory_id'] = 0;
        }
        if (empty($values['subsubcategory_id'])) {
          $values['subsubcategory_id'] = 0;
        }

        //SET ERROR MESSAGE
        if ($is_error == 1) {
          $error = Zend_Registry::get('Zend_Translate')->_('Page Category * Please complete this field - it is required.');
          $this->_jsonErrorOutput($error);
        }
        $sitepage = $table->createRow();

        if (Engine_Api::_()->getApi('subCore', 'sitepage')->pageBaseNetworkEnable()) {
          if (isset($values['networks_privacy']) && !empty($values['networks_privacy'])) {
            if (in_array(0, $values['networks_privacy'])) {
              $values['networks_privacy'] = new Zend_Db_Expr('NULL');
            } else {
              $values['networks_privacy'] = (string) ( is_array($values['networks_privacy']) ? join(",", $values['networks_privacy']) : $netowrkIds );
            }
          }
        }
        if (!empty($sitepageUrlEnabled)) {
          if (empty($show_url)) {
            $resultPageTable = $table->select()->where('title =?', $values['title'])->from($table, 'title')
                            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            $count_index = count($resultPageTable);
            $resultPageUrl = $table->select()->where('page_url =?', $values['title'])->from($table, 'page_url')
                            ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
            $count_index_url = count($resultPageUrl);
          }
          $urlArray = Engine_Api::_()->sitepage()->getBannedUrls();
          if (!empty($show_url)) {
            if (in_array(strtolower($values['page_url']), $urlArray)) {
              $this->_jsonErrorOutput(Zend_Registry::get('Zend_Translate')->_('Sorry, this URL has been restricted by our automated system. Please choose a different URL.'));
            }
          } elseif (!empty($sitepageUrlEnabled)) {
            $lastpage_id = $table->select()
                    ->from($table->info('name'), array('page_id'))->order('page_id DESC')
                    ->query()
                    ->fetchColumn();
            $values['page_url'] = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($values['title']))), '-');
            if (!empty($count_index) || !empty($count_index_url)) {
              $lastpage_id = $lastpage_id + 1;
              $values['page_url'] = $values['page_url'] . '-' . $lastpage_id;
            } else {
              $values['page_url'] = $values['page_url'];
            }
            if (in_array(strtolower($values['page_url']), $urlArray)) {
              $this->_jsonErrorOutput(Zend_Registry::get('Zend_Translate')->_('Sorry, this Page Title has been restricted by our automated system. Please choose a different Title.', array('escape' => false)));
            }
          }
        }
        $sitepage->setFromArray($values);


        $user_level = $viewer->level_id;
        if (!Engine_Api::_()->sitepage()->hasPackageEnable()) {
          $sitepage->featured = Engine_Api::_()->authorization()->getPermission($user_level, 'sitepage_page', 'featured');
          $sitepage->sponsored = Engine_Api::_()->authorization()->getPermission($user_level, 'sitepage_page', 'sponsored');
          $sitepage->approved = Engine_Api::_()->authorization()->getPermission($user_level, 'sitepage_page', 'approved');
        } else {
          $sitepage->featured = $package->featured;
          $sitepage->sponsored = $package->sponsored;
          if ($package->isFree() && !empty($sitepage_is_approved) && !empty($getPackageAuth)) {
            $sitepage->approved = $package->approved;
          } else {
            $sitepage->approved = 0;
          }
        }

        if (!empty($sitepage->approved)) {
          $sitepage->pending = 0;
          $sitepage->aprrove_date = date('Y-m-d H:i:s');

          if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
            $expirationDate = $package->getExpirationDate();
            if (!empty($expirationDate))
              $sitepage->expiration_date = date('Y-m-d H:i:s', $expirationDate);
            else
              $sitepage->expiration_date = '2250-01-01 00:00:00';
          }
          else {
            $sitepage->expiration_date = '2250-01-01 00:00:00';
          }
        }
        if (!empty($sitepage_category)) {
          $sitepage->save();
          $page_id = $sitepage->page_id;
        }

        if (!empty($sitepage->approved)) {
          Engine_Api::_()->sitepage()->sendMail("ACTIVE", $sitepage->page_id);
        } else {
          Engine_Api::_()->sitepage()->sendMail("APPROVAL_PENDING", $sitepage->page_id);
        }

        $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
        $row = $manageadminsTable->createRow();
        $row->user_id = $sitepage->owner_id;
        $row->page_id = $sitepage->page_id;
        $row->save();

        //START PROFILE MAPS WORK
        Engine_Api::_()->getDbtable('profilemaps', 'sitepage')->profileMapping($sitepage);


        $page_id = $sitepage->page_id;
        if (!empty($sitepageUrlEnabled) && empty($show_url)) {
          $values['page_url'] = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($values['title']))), '-');
          if (!empty($count_index) || !empty($count_index_url)) {
            $values['page_url'] = $values['page_url'] . '-' . $page_id;
            $table->update(array('page_url' => $values['page_url']), array('page_id = ?' => $page_id));
          } else {
            $values['page_url'] = $values['page_url'];
            $table->update(array('page_url' => $values['page_url']), array('page_id = ?' => $page_id));
          }
        }

        $sitepageFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform');
        if ($sitepageFormEnabled) {
          $tablecontent = Engine_Api::_()->getDbtable('content', 'core');
          $params = $tablecontent->select()
                          ->from($tablecontent->info('name'), 'params')
                          ->where('name = ?', 'sitepageform.sitepage-viewform')
                          ->query()->fetchColumn();
          $decodedParam = Zend_Json::decode($params);
          $tabName = $decodedParam['title'];
          if (empty($tabName))
            $tabName = 'Form';
          $sitepageformtable = Engine_Api::_()->getDbtable('sitepageforms', 'sitepageform');
          $optionid = Engine_Api::_()->getDbtable('pagequetions', 'sitepageform');
          $table_option = Engine_Api::_()->fields()->getTable('sitepageform', 'options');
          $sitepageform = $table_option->createRow();
          $sitepageform->setFromArray($values);
          $sitepageform->label = $values['title'];
          $sitepageform->field_id = 1;
          $option_id = $sitepageform->save();
          $optionids = $optionid->createRow();
          $optionids->option_id = $option_id;
          $optionids->page_id = $page_id;
          $optionids->save();
          $sitepageforms = $sitepageformtable->createRow();
          if (isset($sitepageforms->offer_tab_name))
            $sitepageforms->offer_tab_name = $tabName;
          $sitepageforms->description = 'Please leave your feedback below and enter your contact details.';
          $sitepageforms->page_id = $page_id;
          $sitepageforms->save();
        }
        //SET PHOTO
        if (!empty($values['photo'])) {
          $sitepage->setPhoto($form->photo);
          $sitepageinfo = $sitepage->toarray();
          $albumTable = Engine_Api::_()->getDbtable('albums', 'sitepage');
          $album_id = $albumTable->update(array('photo_id' => $sitepageinfo['photo_id'], 'owner_id' => $sitepageinfo['owner_id']), array('page_id = ?' => $sitepageinfo['page_id']));
        } else {
          $sitepageinfo = $sitepage->toarray();
          $albumTable = Engine_Api::_()->getDbtable('albums', 'sitepage');
          $album_id = $albumTable->insert(array(
              'photo_id' => 0,
              'owner_id' => $sitepageinfo['owner_id'],
              'page_id' => $sitepageinfo['page_id'],
              'title' => $sitepageinfo['title'],
              'creation_date' => $sitepageinfo['creation_date'],
              'modified_date' => $sitepageinfo['modified_date']));
        }

        //ADD TAGS
        $tags = preg_split('/[,]+/', $values['tags']);
        $tags = array_filter(array_map("trim", $tags));
        $sitepage->tags()->addTagMaps($viewer, $tags);

        if (!empty($page_id)) {
          $sitepage->setLocation();
        }

        // Set privacy
        $auth = Engine_Api::_()->authorization()->context;

        //get the page admin list.
        $ownerList = $sitepage->getPageOwnerList();

        $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
        if (!empty($sitepagememberEnabled)) {
          $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        } else {
          $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        }

        if (!isset($values['auth_view']) || empty($values['auth_view'])) {
          $values['auth_view'] = "everyone";
        }

        if (!isset($values['auth_comment']) || empty($values['auth_comment'])) {
          $values['auth_comment'] = "everyone";
        }

        $viewMax = array_search($values['auth_view'], $roles);
        $commentMax = array_search($values['auth_comment'], $roles);

        foreach ($roles as $i => $role) {
          $auth->setAllowed($sitepage, $role, 'view', ($i <= $viewMax));
          $auth->setAllowed($sitepage, $role, 'comment', ($i <= $commentMax));
          $auth->setAllowed($sitepage, $role, 'print', 1);
          $auth->setAllowed($sitepage, $role, 'tfriend', 1);
          $auth->setAllowed($sitepage, $role, 'overview', 1);
          $auth->setAllowed($sitepage, $role, 'map', 1);
          $auth->setAllowed($sitepage, $role, 'insight', 1);
          $auth->setAllowed($sitepage, $role, 'layout', 1);
          $auth->setAllowed($sitepage, $role, 'contact', 1);
          $auth->setAllowed($sitepage, $role, 'form', 1);
          $auth->setAllowed($sitepage, $role, 'offer', 1);
          $auth->setAllowed($sitepage, $role, 'invite', 1);
        }

        $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
        if (!empty($sitepagememberEnabled)) {
          $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        } else {
          $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        }

        //START WORK FOR SUB PAGE.
        if (empty($values['auth_sspcreate'])) {
          $values['auth_sspcreate'] = "owner";
        }

        $createMax = array_search($values['auth_sspcreate'], $roles);
        foreach ($roles as $i => $role) {
          if ($role === 'like_member') {
            $role = $ownerList;
          }
          $auth->setAllowed($sitepage, $role, 'sspcreate', ($i <= $createMax));
        }
        //END WORK FOR SUBPAGE
        //START SITEPAGEDISCUSSION PLUGIN WORK      
        $sitepagediscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion');
        if ($sitepagediscussionEnabled) {
          //START DISCUSSION PRIVACY WORK
          if (empty($values['sdicreate'])) {
            $values['sdicreate'] = "registered";
          }

          $createMax = array_search($values['sdicreate'], $roles);
          foreach ($roles as $i => $role) {
            if ($role === 'like_member') {
              $role = $ownerList;
            }
            $auth->setAllowed($sitepage, $role, 'sdicreate', ($i <= $createMax));
          }
          //END DISCUSSION PRIVACY WORK
        }
        //END SITEPAGEDISCUSSION PLUGIN WORK        
        //START SITEPAGEALBUM PLUGIN WORK      
        $sitepagealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum');
        if ($sitepagealbumEnabled) {
          //START PHOTO PRIVACY WORK
          if (empty($values['spcreate'])) {
            $values['spcreate'] = "registered";
          }

          $createMax = array_search($values['spcreate'], $roles);
          foreach ($roles as $i => $role) {
            if ($role === 'like_member') {
              $role = $ownerList;
            }
            $auth->setAllowed($sitepage, $role, 'spcreate', ($i <= $createMax));
          }
          //END PHOTO PRIVACY WORK
        }
        //END SITEPAGEALBUM PLUGIN WORK
        //START SITEPAGEDOCUMENT PLUGIN WORK
        $sitepageDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument');
        if ($sitepageDocumentEnabled) {
          $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
          if (!empty($sitepagememberEnabled)) {
            $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
          } else {
            $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
          }

          if (empty($values['sdcreate'])) {
            $values['sdcreate'] = "registered";
          }

          $createMax = array_search($values['sdcreate'], $roles);
          foreach ($roles as $i => $role) {
            if ($role === 'like_member') {
              $role = $ownerList;
            }
            $auth->setAllowed($sitepage, $role, 'sdcreate', ($i <= $createMax));
          }
        }
        //END SITEPAGEDOCUMENT PLUGIN WORK
        //START SITEPAGEVIDEO PLUGIN WORK
        $sitepageVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo');
        if ($sitepageVideoEnabled) {
          if (empty($values['svcreate'])) {
            $values['svcreate'] = "registered";
          }

          $createMax = array_search($values['svcreate'], $roles);
          foreach ($roles as $i => $role) {
            if ($role === 'like_member') {
              $role = $ownerList;
            }
            $auth->setAllowed($sitepage, $role, 'svcreate', ($i <= $createMax));
          }
        }
        //END SITEPAGEVIDEO PLUGIN WORK
        //START SITEPAGEPOLL PLUGIN WORK
        $sitepagePollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll');
        if ($sitepagePollEnabled) {
          if (empty($values['splcreate'])) {
            $values['splcreate'] = "registered";
          }

          $createMax = array_search($values['splcreate'], $roles);
          foreach ($roles as $i => $role) {
            if ($role === 'like_member') {
              $role = $ownerList;
            }
            $auth->setAllowed($sitepage, $role, 'splcreate', ($i <= $createMax));
          }
        }
        //END SITEPAGEPOLL PLUGIN WORK
        //START SITEPAGENOTE PLUGIN WORK
        $sitepageNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote');
        if ($sitepageNoteEnabled) {
          if (empty($values['sncreate'])) {
            $values['sncreate'] = "registered";
          }

          $createMax = array_search($values['sncreate'], $roles);
          foreach ($roles as $i => $role) {
            if ($role === 'like_member') {
              $role = $ownerList;
            }
            $auth->setAllowed($sitepage, $role, 'sncreate', ($i <= $createMax));
          }
        }
        //END SITEPAGENOTE PLUGIN WORK
        //START SITEPAGEMUSIC PLUGIN WORK
        $sitepageMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic');
        if ($sitepageMusicEnabled) {
          if (empty($values['smcreate'])) {
            $values['smcreate'] = "registered";
          }

          $createMax = array_search($values['smcreate'], $roles);
          foreach ($roles as $i => $role) {
            if ($role === 'like_member') {
              $role = $ownerList;
            }
            $auth->setAllowed($sitepage, $role, 'smcreate', ($i <= $createMax));
          }
        }
        //END SITEPAGEMUSIC PLUGIN WORK
        //START SITEPAGEEVENT PLUGIN WORK
        if ((Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {
          if (empty($values['secreate'])) {
            $values['secreate'] = "registered";
          }

          $createMax = array_search($values['secreate'], $roles);
          foreach ($roles as $i => $role) {
            if ($role === 'like_member') {
              $role = $ownerList;
            }
            $auth->setAllowed($sitepage, $role, 'secreate', ($i <= $createMax));
          }
        }
        //END SITEPAGEEVENT PLUGIN WORK
        //START SITEPAGEMEMBER PLUGIN WORK
        $sitepageMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
        if ($sitepageMemberEnabled) {
          $membersTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
          $row = $membersTable->createRow();
          $row->resource_id = $sitepage->page_id;
          $row->page_id = $sitepage->page_id;
          $row->user_id = $sitepage->owner_id;
          $row->notification = '0';
          //$row->action_notification = '["posted","created"]';
          $row->save();
          $sitepage->member_count++;
          $sitepage->save();
        }
        $memberInvite = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.invite.option', 1);
        $member_approval = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.member.approval.option', 1);
        if (empty($memberInvite)) {
          $memberInviteOption = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.invite.automatically', 1);
          $sitepage->member_invite = $memberInviteOption;
          $sitepage->save();
        }
        if (empty($member_approval)) {
          $member_approvalOption = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.member.approval.automatically', 1);
          $sitepage->member_approval = $member_approvalOption;
          $sitepage->save();
        }
        //END SITEPAGEMEMBER PLUGIN WORK
        //START INTERGRATION EXTENSION WORK
        //START BUSINESS INTEGRATION WORK
        $business_id = $this->_getParam('business_id');
        if (!empty($business_id)) {
          $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
          $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessintegration');
          if (!empty($moduleEnabled)) {
            $contentsTable = Engine_Api::_()->getDbtable('contents', 'sitebusinessintegration');
            $row = $contentsTable->createRow();
            $row->owner_id = $viewer_id;
            $row->resource_owner_id = $sitepage->owner_id;
            $row->business_id = $business_id;
            $row->resource_type = 'sitepage_page';
            $row->resource_id = $sitepage->page_id;
            $row->save();
          }
        }
        $group_id = $this->_getParam('group_id');
        if (!empty($group_id)) {
          $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
          $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration');
          if (!empty($moduleEnabled)) {
            $contentsTable = Engine_Api::_()->getDbtable('contents', 'sitegroupintegration');
            $row = $contentsTable->createRow();
            $row->owner_id = $viewer_id;
            $row->resource_owner_id = $sitepage->owner_id;
            $row->group_id = $group_id;
            $row->resource_type = 'sitepage_page';
            $row->resource_id = $sitepage->page_id;
            $row->save();
          }
        }
        //END BUSINESS INTEGRATION WORK
        //START STORE INTEGRATION WORK
        $store_id = $this->_getParam('store_id');
        if (!empty($store_id)) {
          $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
          $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration');
          $sitestoreEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore');
          if (!empty($moduleEnabled) && !empty($sitestoreEnabled)) {
            $contentsTable = Engine_Api::_()->getDbtable('contents', 'sitestoreintegration');
            $row = $contentsTable->createRow();
            $row->owner_id = $viewer_id;
            $row->resource_owner_id = $sitepage->owner_id;
            $row->store_id = $store_id;
            $row->resource_type = 'sitepage_page';
            $row->resource_id = $sitepage->page_id;
            $row->save();
          }
        }
        //END STORE INTEGRATION WORK
        //END INTERGRATION EXTENSION WORK
        //START SUB PAGE WORK
        $parent_id = $this->_getParam('parent_id');
        if (!empty($parent_id)) {
          $sitepage->subpage = 1;
          $sitepage->parent_id = $parent_id;
          $sitepage->save();
        }
        //END  SUB PAGE WORK
        //CUSTOM FIELD WORK
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.profile.fields', 1)) {
          $customfieldform = $form->getSubForm('fields');
          $customfieldform->setItem($sitepage);
          $customfieldform->saveValues();
        }

        //START DEFAULT EMAIL TO SUPERADMIN WHEN ANYONE CREATE PAGES.
        $emails = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.defaultpagecreate.email', Engine_API::_()->seaocore()->getSuperAdminEmailAddress());
        if (!empty($emails)) {
          $emails = explode(",", $emails);
          $host = $_SERVER['HTTP_HOST'];
          $newVar = _ENGINE_SSL ? 'https://' : 'http://';
          $object_link = $newVar . $host . $sitepage->getHref();
          $viewerGetTitle = $viewer->getTitle();
          $sender_link = '<a href=' . $newVar . $host . $viewer->getHref() . ">$viewerGetTitle</a>";
          foreach ($emails as $email) {
            $email = trim($email);
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'SITEPAGE_PAGE_CREATION', array(
                'sender' => $sender_link,
                'object_link' => $object_link,
                'object_title' => $sitepage->getTitle(),
                'object_description' => $sitepage->getDescription(),
                'queue' => true
            ));
          }
        }
        //END DEFAULT EMAIL TO SUPERADMIN WHEN ANYONE CREATE PAGES.
        // Commit
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      if (!empty($sitepage) && !empty($sitepage->draft) && empty($sitepage->pending)) {
        Engine_Api::_()->sitepage()->attachPageActivity($sitepage);

        //START AUTOMATICALLY LIKE THE PAGE WHEN MEMBER CREATE A PAGE.
        $autoLike = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.automatically.like', 1);
        if (!empty($autoLike)) {
          Engine_Api::_()->sitepage()->autoLike($sitepage->page_id, 'sitepage_page');
        }
        //END AUTOMATICALLY LIKE THE PAGE WHEN MEMBER CREATE A PAGE.
        //SENDING ACTIVITY FEED TO FACEBOOK.
        $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
        if (!empty($enable_Facebooksefeed)) {

          $sitepage_array = array();
          $sitepage_array['type'] = 'sitepage_new';
          $sitepage_array['object'] = $sitepage;

          Engine_Api::_()->facebooksefeed()->sendFacebookFeed($sitepage_array);
        }
      }

      $this->_jsonSuccessOutput(array(
          'message' => $this->_translator->_('Circle created')
      ));
    }
  }

  public function fetchCircleGeneralInfoAction()
  {
    $this->_initCircleAction();

    //DONT RENDER IF NOT AUTHORIZED
    if (!Engine_Api::_()->core()->hasSubject()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::CIRCLE_NOT_FOUND);
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    /* @var $sitepage Core_Model_Item_Abstract */
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

    // Get overview info
    $overview = empty($sitepage->overview) ? $this->_translator->_('No overview has been composed for this Page yet.') : $sitepage->overview;
    $overview_info = array(
        'can_view' => (int) Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'overview'),
        'can_edit' => (int) Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit'),
        'overview' => strip_tags($overview)
    );

    $page_cover = Engine_Api::_()->getItem('sitepage_photo', $sitepage->page_cover);
    // Temporarily unused
//    $hasLeave = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id, $sitepage->page_id, $params = "Leave");
    $hasInvite = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id, $sitepage->page_id, $params = 'Invite');
    $can_edit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
      $allowPage = Engine_Api::_()->sitepage()->allowInThisPage($sitepage, "sitepagemember", 'smecreate');
    }

    // Check if current viewer wanted to be member of this circle
    $hasMember = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id, $sitepage->page_id);

    if (!$hasMember) {
      // User has neither joined nor requested to join this circle yet
      $join_status = 0;
    } else {
      $hasRequest = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id, $sitepage->page_id, $params = 'Cancel');
      if ($hasRequest) {
        // User has requested to join this circle
        $join_status = 1;
      } else {
        // User has already been a member of this circle
        $join_status = 2;
      }
    }

    $hasLike = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($sitepage->getType(), $sitepage->getIdentity());

    $refinedCircle = $this->_helper->circleAPI->getCircleBasicInfo($sitepage);
    $refinedCircle['last_updated_date'] = $this->_translator->_(gmdate('M d, Y', strtotime($sitepage->modified_date)));
    $refinedCircle['follow_count'] = $this->sitepage->follow_count;
    $refinedCircle['category'] = 1;

    $event_can_create = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'secreate') || $can_edit;

    $data = array(
        'id' => $sitepage->getIdentity(),
        'type' => $sitepage->getType(),
        'cover_photo' => $page_cover ? $page_cover->getPhotoUrl('thumb.cover') : '',
        'thumb_photo' => (string) $sitepage->getPhotoUrl('thumb.profile'),
        'title' => $sitepage->getTitle(),
        'member_count' => $sitepage->member_count,
        'comment_count' => $sitepage->comment_count,
        'like_count' => $sitepage->like_count,
        'view_count' => $sitepage->view_count,
        'is_liked' => $hasLike ? 1 : 0,
        'follow_count' => $sitepage->follow_count,
        'is_followed' => (int) $sitepage->follows()->isFollow($viewer),
        'like_id' => $hasLike ? $hasLike[0]['like_id'] : null,
        'join_status' => $join_status,
        'category' => 1,
        'last_updated_date' => $this->_translator->_(gmdate('M d, Y', strtotime($sitepage->modified_date))),
        'description' => $sitepage->body,
        'location' => Zend_Registry::get('Zend_Translate')->_($sitepage->location),
        'posted_by' => $this->_helper->commonAPI->getBasicInfoFromItem($sitepage->getOwner()),
        'creation_date' => strip_tags($this->view->timestamp($sitepage->creation_date)),
        'overview' => $overview_info,
        'permission_info' => array(
            'can_like' => (int) !empty($viewer_id),
            'can_follow' => (int) !(empty($viewer_id) || $viewer_id == $sitepage->getOwner()->getIdentity()),
            'can_add_people' => (int) (!empty($hasInvite) && (!empty($can_edit) || empty($sitepage->member_invite))),
//            'can_leave' => (int) ($viewer_id && !empty($hasLeave) && $viewer_id != $sitepage->owner_id && !empty($allowPage)),
            'can_join' => (int) ($viewer_id != $sitepage->owner_id && !empty($allowPage)),
            'can_view_circle_details' => (int) Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view'),
            'can_post_status' => (int) $this->_helper->feedAPI->checkPostFeedAuth(),
            'can_edit' => (int) $can_edit,
            'can_delete' => (int) Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'delete'),
            'video_can_create' => (int) (Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'svcreate') || $can_edit),
            'photo_can_create' => (int) (Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate') || $can_edit),
            'event_can_create' => (int) ($event_can_create),
            'event_can_view_myevents' => (int) ($event_can_create && Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepageevent')),
            'note_can_create' => (int) (Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sncreate') || $can_edit),
            'document_can_create' => (int) (Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdcreate') || $can_edit),
            'can_view_requests' => (int) $can_edit,
        )
    );

    $this->_jsonSuccessOutput($data);
  }

  public function fetchCircleMemberListAction()
  {
    $this->_initCircleAction(true);

    //DONT RENDER IF NOT AUTHORIZED
    if (!Engine_Api::_()->core()->hasSubject()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::CIRCLE_NOT_FOUND);
    }

    //GET SUBJECT
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

    $item_per_page = 24;

    $values = array();
    $values['page_id'] = $sitepage->page_id;

    $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
    $paginator = $membershipTable->getSitepagemembersPaginator($values);
    $paginator->setItemCountPerPage($item_per_page)->setCurrentPageNumber($this->_getParam('page', 1));
    $can_edit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

    $users = array();
    $i = 0;
    foreach ($paginator as $user) {
      $users[$i]['user_id'] = $user->user_id;
      $users[$i]['user_name'] = $user->getTitle();
      $users[$i]['user_photo'] = $user->getPhotoUrl('thumb.icon');
      $users[$i]['member_id'] = $user->member_id;
      $users[$i]['permission_info'] = array(
          'can_remove' => (int) ($can_edit && $sitepage->owner_id != $user->user_id)
      );
      $i++;
    }

    $data['users'] = $users;
    $data['totalUsers'] = $paginator->getTotalItemCount();
    $data['userCount'] = $paginator->getCurrentItemCount();
    $data['itemCountPerPage'] = $item_per_page;

    $this->_jsonSuccessOutput($data);
  }

  public function fetchCirclePhotoLibraryAction()
  {
    $this->_initCircleAction(true);

    //DONT RENDER IF NOT AUTHORIZED
    if (!Engine_Api::_()->core()->hasSubject()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::CIRCLE_NOT_FOUND);
    }

    //GET SUBJECT
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

    //GET REQUEST
    $zendRequest = Zend_Controller_Front::getInstance()->getRequest();

    $albums_per_page = $this->_getParam('itemCount', 50);

    //GET CURRENT PAGE NUMBER OF ALBUM
    $currentAlbumPageNumbers = $this->_getParam('page', 1);

    //SET ALBUMS PARAMS
    $paramsAlbum = array();
    $paramsAlbum['page_id'] = $sitepage->page_id;

    //GET ALBUM COUNT
    $album_count = Engine_Api::_()->getDbtable('albums', 'sitepage')->getAlbumsCount($paramsAlbum);

    //START ALBUMS PAGINATION
    $pages_vars = Engine_Api::_()->sitepage()->makePage($album_count, $albums_per_page, $currentAlbumPageNumbers);
    $pages_array = Array();
    for ($y = 0; $y <= $pages_vars[2] - 1; $y++) {
      if ($y + 1 == $pages_vars[1]) {
        $links = "1";
      } else {
        $links = "0";
      }
      $pages_array[$y] = Array('pages' => $y + 1,
          'links' => $links);
    }

    //ALBUMS ORDER
    $albums_order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.albumsorder', 1);

    //END ALBUMS PAGINATION
    //SET ALBUMS PARAMS
    $paramsAlbum['start'] = $albums_per_page;
    $paramsAlbum['end'] = $pages_vars[0];
    if (empty($albums_order)) {
      $paramsAlbum['orderby'] = 'album_id ASC';
    } else {
      $paramsAlbum['orderby'] = 'album_id DESC';
    }
    $paramsAlbum['getSpecialField'] = 0;

    $fecthAlbums = Engine_Api::_()->getDbtable('albums', 'sitepage')->getAlbums($paramsAlbum);

    //SET PHOTOS PARAMS
    $paramsPhoto = array();
    $paramsPhoto['page_id'] = $sitepage->page_id;
    $paramsPhoto['user_id'] = $sitepage->owner_id;
    $paramsPhoto['album_id'] = Engine_Api::_()->getItemTable('sitepage_album')->getDefaultAlbum($sitepage->page_id)->album_id;

    //FETCHING ALL PHOTOS
    $total_photo = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotosCount($paramsPhoto);

    //SEND CURRENT PAGE NUMBER TO THE TPL
    $currentPageNumbers = $this->_getParam('pages', 1);

    //SEND PHOTOS PER PAGE TO THE TPL
    $photos_per_page = $zendRequest->getParam('itemCount_photo', 100);

    //START PHOTOS PAGINATION
    $page_vars = Engine_Api::_()->sitepage()->makePage($total_photo, $photos_per_page, $currentPageNumbers);
    $paramsPhoto['start'] = $photos_per_page;
    $paramsPhoto['end'] = $page_vars[0];
    $paramsPhoto['widgetName'] = 'Photos By Others';
    if (empty($albums_order)) {
      $paramsPhoto['photosorder'] = 'album_id ASC';
    } else {
      $paramsPhoto['photosorder'] = 'album_id DESC';
    }
    $photos = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotos($paramsPhoto);

    $can_edit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    $albums = array();
    $i = 0;
    foreach ($fecthAlbums as $album) {
      $albums[$i] = array();
      $albums[$i]['album_id'] = $album->getIdentity();
      $albums[$i]['album_cover'] = $album->getPhotoUrl('thumb.normal');
      $albums[$i]['album_title'] = $album->getTitle();
      $albums[$i]['album_like_count'] = $album->like_count;
      $albums[$i]['album_photo_count'] = $album->count();

      // get comment info
      $commentInfo = $this->_helper->commonAPI->getCommentInfo($album);
      $albums[$i]['album_comment_count'] = $commentInfo['comment_count'];

      $albums[$i]['can_delete'] = (int) ($can_edit && $album->default_value != 1);
      $albums[$i]['can_edit'] = (int) $can_edit;
      $i++;
    }

    $data['albums'] = $albums;
    $data['totalItem'] = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitepage', 'albums');
    $data['itemCountPerPage'] = $albums_per_page;

    $this->_jsonSuccessOutput($data);
  }

  public function fetchCirclePhotoAlbumDetailsAction()
  {
    $this->_initCircleAction();

    //GET REQUEST
    $request = Zend_Controller_Front::getInstance()->getRequest();

    $engineApiSitepage = Engine_Api::_()->sitepage();

    $photosorder = $this->_getParam('photosorder', 1);

    $album_id = $request->getParam('album_id');

    $album = Engine_Api::_()->getItem('sitepage_album', $album_id);

    // Attempt to get the circle object
    if (Engine_Api::_()->core()->hasSubject()) {
      $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    } else {
      $sitepage = $album->getParent();
    }

    // Is the current user eligible to view the circle?
    if (!Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view')) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    $page_id = $sitepage->page_id;

    $viewer = Engine_Api::_()->user()->getViewer();

    //SET ALBUMS PARAMS
    $paramsAlbum = array();
    $paramsAlbum['page_id'] = $page_id;
    $paramsAlbum['viewPage'] = 1;

    //SET PAGE PHOTO PARAMS
    $paramsPhoto = array();
    $paramsPhoto['page_id'] = $page_id;
    $paramsPhoto['album_id'] = $album_id;
    $paramsPhoto['order'] = 'order ASC';
    $paramsPhoto['viewPage'] = 1;
    //FETCHING ALL PHOTOS
    $total_photo = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotosCount($paramsPhoto);

    $photos_per_page = $request->getParam('itemCount_photo', 100);

    //SEND CURRENT PAGE NUMBER TO THE TPL
    $currentPageNumbers = $this->_getParam('page', 1);

    //MAKING PAGINATION 
    $page_vars = $engineApiSitepage->makePage($total_photo, $photos_per_page, $currentPageNumbers);

    //SET PAGE PHOTO PARAMS
    $paramsPhoto['start'] = $photos_per_page;
    $paramsPhoto['end'] = $page_vars[0];
    $paramsPhoto['viewPage'] = 1;
    $paramsPhoto['photosorder'] = $photosorder;
    $paramsPhoto['widgetName'] = 'Album Content';

    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      $paramsPhoto['albumviewPage'] = 1;
    }
    //GETTING THE PHOTOS ACCORDING TO LIMIT
    $fetchedPhotos = Engine_Api::_()->getDbtable('photos', 'sitepage')->getPhotos($paramsPhoto);

    //SET PAGE PHOTO PARAMS
    $paramsPhoto['start'] = $photos_per_page;
    $paramsPhoto['end'] = $page_vars[0];
    $paramsPhoto['viewPage'] = 1;
    $paramsPhoto['photosorder'] = $photosorder;
    $paramsPhoto['widgetName'] = 'Album Content';

    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      $paramsPhoto['albumviewPage'] = 1;
    }

    // Album permissions
    // Upload photo permission
    $isManageAdmin = $engineApiSitepage->isManageAdmin($sitepage, 'spcreate');
    if (empty($isManageAdmin)) {
      $canCreatePhoto = 0;
    } else {
      $canCreatePhoto = 1;
    }
    $upload_photo = 0;
    if ($canCreatePhoto == 1 && ($engineApiSitepage->isPageOwner($sitepage) || $album->default_value == 1)) {
      $upload_photo = 1;
    }
    // Edit album permission
    $isManageAdmin = $engineApiSitepage->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    // Delete album permission
    $can_delete = $can_edit && $album->default_value != 1;

    // Get photo array
    $photos = array();
    $i = 0;
    foreach ($fetchedPhotos as $photo) {
      $photos[$i]['photo_id'] = $photo->getIdentity();
      $photos[$i]['thumb_photo_url'] = $photo->getPhotoUrl('thumb.normal');
      $photos[$i]['big_photo_url'] = $photo->getPhotoUrl();
      $photos[$i]['photo_title'] = $photo->getTitle();
      $photos[$i]['photo_description'] = $photo->getDescription();
      // Get photo permissions
      $photos[$i]['can_edit'] = (int) ($can_edit || $viewer->getIdentity() == $photo->user_id);
      $photos[$i]['can_delete'] = (int) ($can_edit || $viewer->getIdentity() == $photo->user_id);
      $photos[$i]['can_comment'] = (int) $photo->authorization()->isAllowed($viewer, 'comment');
      $photos[$i]['can_share'] = 0;

      // Get likers
      $photos[$i]['like_info'] = $this->_helper->commonAPI->getLikeInfo($photo, $viewer);
      // Get comment count
      $photos[$i]['comment_count'] = $this->_helper->commonAPI->getCommentInfo($photo)['comment_count'];

      $i++;
    }

    $data = array(
        'album_title' => $album->getTitle(),
        'album_description' => $album->getDescription(),
        'can_edit' => (int) $can_edit,
        'can_upload_photo' => (int) $upload_photo,
        'can_delete' => (int) $can_delete,
        'photos' => $photos,
        'totalPhoto' => $total_photo,
        'isLiked' => (int) $album->likes()->isLike($viewer),
        'like_info' => $this->_helper->commonAPI->getLikeInfo($album, $viewer),
        'commentInfo' => $this->_helper->commonAPI->getCommentInfo($album)
    );
    $this->_jsonSuccessOutput($data);
  }

  public function fetchCircleVideoLibraryAction()
  {
    $this->_initCircleAction(true);

    //DONT RENDER IF NOT AUTHORIZED
    if (!Engine_Api::_()->core()->hasSubject()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::CIRCLE_NOT_FOUND);
    }

    //GET SUBJECT
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    $values = array();
    $values['orderby'] = 'creation_date';
    $values['page_id'] = $sitepage->page_id;

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    //FETCH VIDEOS
    if ($can_edit) {
      $values['show_video'] = 0;
      $paginator = Engine_Api::_()->getItemTable('sitepagevideo_video')->getSitepagevideosPaginator($values);
    } else {
      $values['show_video'] = 1;
      $values['video_owner_id'] = $viewer_id;
      $paginator = Engine_Api::_()->getItemTable('sitepagevideo_video')->getSitepagevideosPaginator($values);
    }

    $item_per_page = 50;
    $paginator->setItemCountPerPage($item_per_page);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $videos = array();
    $i = 0;
    foreach ($paginator as $video) {
      if (!isset($video->type)) {
        continue;
      }
      // Get video location
      $video_location = $this->_helper->videoAPI->getVideoLocation($video);

      $videos[$i]['video_id'] = $video->getIdentity();
      $videos[$i]['video_duration'] = $video->duration;
      // Get thumb photo
      if ($video->video_id) {
        $videos[$i]['video_thumb_photo'] = $video->getPhotoUrl('thumb.normal');
      } else {
        $videos[$i]['video_thumb_photo'] = $this->view->serverUrl('/application/modules/Ynvideo/externals/images/video.png');
      }
      $videos[$i]['video_title'] = $video->getTitle();
      $videos[$i]['video_url'] = $video_location;
      $videos[$i]['video_view_count'] = $video->view_count;
      $videos[$i]['video_like_count'] = $video->likes()->getLikeCount();
      $videos[$i]['video_rating'] = $video->rating;
      $videos[$i]['video_type'] = $this->_helper->videoAPI->getMobileVideoType($video);

      // Get comment info
      $commentInfo = $this->_helper->commonAPI->getCommentInfo($video);
      $videos[$i]['comment_count'] = $commentInfo['comment_count'];

      $videos[$i]['can_edit'] = (int) ($this->can_edit == 1 || $video->owner_id == $viewer_id);
      $videos[$i]['can_delete'] = (int) ($this->can_edit == 1 || $video->owner_id == $viewer_id);
      $i++;
    }

    $data['videos'] = $videos;
    $data['totalItem'] = $paginator->getTotalItemCount();
    $data['itemCountPerPage'] = $item_per_page;

    $this->_jsonSuccessOutput($data);
  }

  public function likeCircleAction()
  {
    $this->getRequest()->setParam('format', 'json');
    try {
      $result = json_decode($this->view->action('like', 'like', 'seaocore', $this->getRequest()->getParams()));
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }
    if ($result->status == true) {
      $like_id = $this->_getParam('like_id');

      if ($like_id) {
        $this->_jsonSuccessOutput(array('message' => $this->_translator->_('Successfully Unliked.')));
      } else {
        $this->_jsonSuccessOutput(array(
            'message' => $this->_translator->_('Successfully Liked.'),
            'like_id' => $result->like_id
        ));
      }
    }
  }

  public function followCircleAction()
  {
    $this->getRequest()->setParam('format', 'json');
    try {
      $result = json_decode($this->view->action('global-follows', 'follow', 'seaocore', $this->getRequest()->getParams()));
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }
    if ($result->status == true) {
      $follow_id = $this->_getParam('follow_id');

      if ($follow_id) {
        $this->_jsonSuccessOutput(array('message' => $this->_translator->_('Successfully Unfollowed.')));
      } else {
        $this->_jsonSuccessOutput(array('message' => $this->_translator->_('Successfully Followed.')));
      }
    }
  }

  public function leaveCircleAction()
  {
    $this->getRequest()->setParam('format', 'json');
    try {
      $this->view->action('leave', 'member', 'sitepagemember', $this->getRequest()->getParams());
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }
    $this->_jsonSuccessOutput(array('message' => $this->_translator->_('You have successfully left this page.')));
  }

  public function joinCircleAction()
  {
    $page_id = $this->_getParam('page_id');
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    $this->getRequest()->setParam('format', 'json');

    if (!$sitepage) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::CIRCLE_NOT_FOUND);
    } else {
      if (!empty($sitepage->member_approval)) {
        $this->view->action('join', 'member', 'sitepagemember', $this->getRequest()->getParams());
        $join_status = 2;
        $message = Zend_Registry::get('Zend_Translate')->_('You are now a member of this page.');
      } else {
        $this->view->action('request', 'member', 'sitepagemember', $this->getRequest()->getParams());
        $join_status = 1;
        $message = Zend_Registry::get('Zend_Translate')->_('You have already sent a membership request.');
      }
      $this->_jsonSuccessOutput(array(
          'message' => $message,
          'join_status' => $join_status
      ));
    }
  }

  public function cancelCircleMembershipRequestAction()
  {
    $this->getRequest()->setParam('format', 'json');
    try {
      $this->view->action('cancel', 'member', 'sitepagemember', $this->getRequest()->getParams());
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }
    $this->_jsonSuccessOutput(array('message' => $this->_translator->_('Page membership request cancelled.')));
  }

  public function deleteCircleAction()
  {
    $this->_initCircleAction();

    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    //GET PAGE ID AND OBJECT
    $page_id = $this->_getParam('page_id');
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'delete');
    if (empty($isManageAdmin)) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }
    //END MANAGE-ADMIN CHECK

    if ($this->getRequest()->isPost()) {

      //START SUB PAGE WORK
      $getSubPageids = Engine_Api::_()->getDbTable('pages', 'sitepage')->getsubPageids($page_id);
      foreach ($getSubPageids as $getSubPageid) {
        Engine_Api::_()->sitepage()->onPageDelete($getSubPageid['page_id']);
      }
      //END SUB PAGE WORK

      Engine_Api::_()->sitepage()->onPageDelete($page_id);
      $this->_jsonSuccessOutput(array(
          'message' => $this->_translator->_('Circle deleted')
      ));
    } else {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }
  }

  public function addMembersToCircleAction()
  {
    $this->getRequest()->setParam('format', 'json');
    try {
      $this->view->action('invite-members', 'member', 'sitepagemember', $this->getRequest()->getParams());
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }
    $this->_jsonSuccessOutput(array('message' => $this->_translator->_('The selected members have been successfully added to this page.')));
  }

  /**
   * sitepage/index/getmembers
   */
  public function fetchCircleSuggestedPeopleToInviteAction()
  {
    $data = array();

    //GET COUPON ID.
    $page_id = $this->_getParam('page_id', null);

    $usersTable = Engine_Api::_()->getDbtable('users', 'user');
    $usersTableName = $usersTable->info('name');

    $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
    $membershipTableName = $membershipTable->info('name');

    $pageJoinType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.join.type', null);
    $pagePhraseNum = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.phrase.num', null);

    $select = $membershipTable->select()
            ->from($membershipTableName, 'user_id')
            ->where('page_id = ?', $page_id);
    $user_ids = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);

    $select = $usersTable->select()
            ->where('displayname  LIKE ? ', '%' . $this->_getParam('user_name', null) . '%')
            ->where($usersTableName . '.user_id NOT IN (?)', (array) $user_ids)
            ->order('displayname ASC')
            ->limit('40');
    $users = $usersTable->fetchAll($select);

    foreach ($users as $user) {
      $user_photo = $this->view->itemPhoto($user, 'thumb.icon');
      $data[] = array(
          'user_id' => $user->user_id,
          'user_name' => $user->displayname,
          'user_photo' => $user_photo
      );
    }
    $this->_jsonSuccessOutput($data);
  }

  public function removeMemberFromCircleAction()
  {
    $this->getRequest()->setParam('format', 'json');
    try {
      $this->view->action('remove', 'index', 'sitepagemember', $this->getRequest()->getParams());
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }
    $this->_jsonSuccessOutput(array('message' => $this->_translator->_('Member removed.')));
  }

  public function messageCircleOwnerAction()
  {
    $this->getRequest()->setParam('format', 'json');
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $page_id = $this->_getParam('page_id');

    if (!$page_id) {
      $this->_jsonErrorOput(Mgslapi_Controller_Action_Helper_Error::CIRCLE_NOT_FOUND);
    }

    //GET ADMINS ID FOR SENDING MESSAGE
    $manageAdminData = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdmin($page_id)->toArray();
    $ids = '';
    if (!empty($manageAdminData)) {
      foreach ($manageAdminData as $key => $user_ids) {
        $user_id = $user_ids['user_id'];
        if ($viewer_id != $user_id) {
          $ids = $ids . $user_id . ',';
        }
      }
    }
    $ids = trim($ids, ',');
    $this->getRequest()->setPost('toValues', $ids);

    try {
      // Transfer request to desired controller
      $this->_customDispatch('message-owner', 'profile', 'sitepage', $this->getRequest()->getParams());
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }

    $errors = $this->view->form->getErrorMessages();
    if (empty($errors)) {
      $this->_jsonSuccessOutput(array('message' => $this->_translator->_('Your message has been sent successfully.')));
    } else {
      $this->_jsonErrorOutput($errors);
    }
  }

  public function fetchCircleOverviewAction()
  {
    $this->_initCircleAction(true);

    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::CIRCLE_NOT_FOUND);
    }

    //GET SUBJECT
    if (Engine_Api::_()->core()->getSubject()->getType() == 'sitepage_page') {
      $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    } else {
      $sitepage = Engine_Api::_()->core()->getSubject()->getParent();
    }

    $can_view = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'overview');
    $can_edit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');

    $overview = empty($sitepage->overview) ? $this->_translator->_('No overview has been composed for this Page yet.') : $sitepage->overview;

    $this->_jsonSuccessOutput(array(
        'can_view' => (int) $can_view,
        'can_edit' => (int) $can_edit,
        'overview' => strip_tags($overview)
    ));
  }

  public function fetchCircleEventsAction()
  {
    $this->getRequest()->setParam('selectbox', 'starttime');

    $this->_initCircleAction(true);
    // SET NO RENDER IF NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::CIRCLE_NOT_FOUND);
    }

    // GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET SUBJECT
    $sitepage_subject = Engine_Api::_()->core()->getSubject('sitepage_page');

    //GET PAGE ID
    $page_id = $sitepage_subject->page_id;

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage_subject->package_id, "modules", "sitepageevent")) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage_subject, 'secreate');
      if (empty($isPageOwnerAllow)) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
      }
    }

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'view');
    if (empty($isManageAdmin)) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    //END MANAGE-ADMIN CHECK
    //GET VIEWER INFORMATION
    $viewer_id = $viewer->getIdentity();

    $get_event = Engine_Api::_()->getItemTable('sitepageevent_event')->getEventUserType();
    if (empty($get_event)) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NO_DATA);
    }

    //WHICH LINK HAS BEEN CLICKED
    $clicked = $this->_getParam('clicked_event', 'upcomingevent');

    $getIsEvent = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepageevent');

    //GET SEARCHING PARAMETERS
    $search = $this->_getParam('search');
    $selectbox = $this->_getParam('selectbox');

    //MAKING THE SEACHING PARAMATER ARRAY
    $values = array();
    if (!empty($search)) {
      $values['search'] = $search;
    }

    if ($clicked == 'upcomingevent') {
      if ($selectbox == 'allmyevent' || $selectbox == 'eventilead') {
        if (empty($values['orderby'])) {
          $values['orderby'] = 'starttime';
          $selectbox = 'starttime';
        } else {
          $values['orderby'] = $selectbox;
        }
      }
      $values['orderby'] = $selectbox;
      $values['clicked'] = $clicked;
      $params['selectedbox'] = '';
    } else if ($clicked == 'pastevent') {
      $values['clicked'] = $clicked;
      $values['orderby'] = 'endtime';
      $params['selectedbox'] = '';
    } else if ($clicked == 'myevent') {
      $values['selectedbox'] = 'allmyevent';
      if ($selectbox == 'allmyevent') {
        $values['selectedbox'] = $selectbox;
      } elseif ($selectbox == 'eventilead') {
        $values['selectedbox'] = $selectbox;
        $values['user_id'] = $viewer_id;
      }
    }

    if (empty($selectbox)) {
      $values['orderby'] = 'starttime';
    }

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'secreate');
    if (empty($isManageAdmin) && empty($can_edit)) {
      $can_create = 0;
    } else {
      $can_create = 1;
    }
    //END MANAGE-ADMIN CHECK

    $values['page_id'] = $page_id;
    if ($can_edit) {
      $values['show_event'] = 0;
    } else {
      $values['show_event'] = 1;
      $values['event_owner_id'] = $viewer_id;
    }

    // MAKE PAGINATOR
    $currentPageNumber = $this->_getParam('page', 1);
    $paginator = Engine_Api::_()->getDbTable('events', 'sitepageevent')->getSitepageeventsPaginator($values);
    $item_per_page = 10;
    $paginator->setItemCountPerPage($item_per_page)->setCurrentPageNumber($currentPageNumber);

    $refinedEvents = array();
    $i = 0;
    foreach ($paginator as $event) {
      $refinedEvents[$i]['item_id'] = $event->getIdentity();
      $refinedEvents[$i]['item_type'] = $event->getType();
      $refinedEvents[$i]['event_thumb_photo'] = $event->getPhotoUrl('thumb.normal');
      $refinedEvents[$i]['event_name'] = $event->getTitle();
      $refinedEvents[$i]['event_description'] = $event->getDescription();
      $refinedEvents[$i]['event_start_time'] = $this->view->locale()->toDateTime($event->starttime);
      $refinedEvents[$i]['event_guest_count'] = $event->member_count;
      $refinedEvents[$i]['event_view_count'] = $event->view_count;
      $refinedEvents[$i]['event_led_by'] = $this->_helper->commonAPI->getBasicInfoFromItem($event->getOwner());
      $refinedEvents[$i]['can_edit'] = (int) ($viewer_id == $event->user_id || $can_edit == 1);
      $refinedEvents[$i]['can_delete'] = (int) ($viewer_id == $event->user_id || $can_edit == 1);
//      $refinedEvents[$i]['can_create'] = (int) $can_create;
//      $refinedEvents[$i]['can_view_myevents'] = (int) ($getIsEvent && $can_create);
      $i++;
    }

    $this->_jsonSuccessOutput(array(
        'totalEvents' => $paginator->getTotalItemCount(),
        'events' => $refinedEvents,
        'itemCountPerPage' => $item_per_page
    ));
  }

  public function fetchCircleNotesAction()
  {
    $this->_initCircleAction(true);

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //SET NO RENDER IF NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::CIRCLE_NOT_FOUND);
    }

    //GET VIEWER INFORMATION
    $viewer_id = $viewer->getIdentity();

    //GET SUBJECT AND PAGE ID AND PAGE OWNER ID
    $sitepageSubject = Engine_Api::_()->core()->getSubject('sitepage_page');
    $page_id = $sitepageSubject->page_id;

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepageSubject->package_id, "modules", "sitepagenote")) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepageSubject, 'sncreate');
      if (empty($isPageOwnerAllow)) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
      }
    }
    //PACKAGE BASE PRIYACY END
    //TOTAL NOTE
    $noteCount = Engine_Api::_()->sitepage()->getTotalCount($page_id, 'sitepagenote', 'notes');
    $noteCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepageSubject, 'sncreate');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepageSubject, 'view');
    if (empty($isManageAdmin)) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepageSubject, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    if (empty($noteCreate) && empty($noteCount) && empty($can_edit) && !(Engine_Api::_()->sitepage()->showTabsWithoutContent())) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NO_DATA);
    }
    //END MANAGE-ADMIN CHECK

    $getLevelAuth = Engine_Api::_()->sitepagenote()->getLevelAuth();

    if (empty($getLevelAuth)) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    //SHOWING THE NOTES
    //GET THE TAB ID
    //GET SEARCHING PARAMETERS
    $page = $this->_getParam('page', 1);
    $search = $this->_getParam('search');
    $selectbox = $this->_getParam('selectbox');
    $checkbox = $this->_getParam('checkbox');

    //MAKING THE SEACHING PARAMATER ARRAY
    $values = array();
    if (!empty($selectbox) && $selectbox == 'featured') {
      $values['featured'] = 1;
      $values['orderby'] = 'creation_date';
    }
    if (!empty($search)) {
      $values['search'] = $search;
    }
    if (!empty($selectbox)) {
      $values['orderby'] = $selectbox;
    } else {
      $values['orderby'] = 'creation_date';
    }
    if (!empty($checkbox) && $checkbox == 1) {
      $values['owner_id'] = $viewer_id;
    }

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepageSubject, 'sncreate');
    if (empty($isManageAdmin) && empty($can_edit)) {
      $can_create = 0;
    } else {
      $can_create = 1;
    }
    //END MANAGE-ADMIN CHECK
    //FETCH NOTES
    $values['page_id'] = $page_id;

    if ($can_edit == 1) {
      $values['show_pagenotes'] = 0;
    } else {
      $values['show_pagenotes'] = 1;
      $values['note_owner_id'] = $viewer_id;
    }

    //MAKE PAGINATOR
    $paginator = Engine_Api::_()->getDbTable('notes', 'sitepagenote')->getSitepagenotesPaginator($values);
    $item_per_page = 10;
    $paginator->setItemCountPerPage($item_per_page)->setCurrentPageNumber($page);

    $notes = array();
    $i = 0;
    foreach ($paginator as $note) {
      $notes[$i]['item_id'] = $note->getIdentity();
      $notes[$i]['item_type'] = $note->getType();
      $notes[$i]['thumb_photo'] = $note->getPhotoUrl('thumb.normal');
      $notes[$i]['name'] = $note->getTitle();
      $notes[$i]['description'] = strip_tags($note->body);
      $notes[$i]['posted_by'] = $this->_helper->commonAPI->getBasicInfoFromItem($note->getOwner());
      $notes[$i]['creation_date'] = $note->creation_date;
      $notes[$i]['view_count'] = $note->view_count;
      $notes[$i]['comment_count'] = $note->comment_count;
      $notes[$i]['like_count'] = $note->like_count;
      $notes[$i]['can_edit'] = (int) ($viewer_id == $note->owner_id || $can_edit == 1);
      $notes[$i]['can_delete'] = (int) ($viewer_id == $note->owner_id || $can_edit == 1);
//      $notes[$i]['can_create'] = (int) $can_create;
      $i++;
    }

    $this->_jsonSuccessOutput(array(
        'totalNotes' => $paginator->getTotalItemCount(),
        'notes' => $notes,
        'itemCountPerPage' => $item_per_page
    ));
  }

  public function fetchCircleDocumentsAction()
  {
    include_once APPLICATION_PATH . '/application/modules/Sitepagedocument/Api/Scribdsitepage.php';

    $this->_initCircleAction(true);

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //SET NO RENDER IF NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::CIRCLE_NOT_FOUND);
    }

    //GET SUBJECT
    if (Engine_Api::_()->core()->getSubject()->getType() == 'sitepage_page') {
      $sitepage_subject = Engine_Api::_()->core()->getSubject('sitepage_page');
    } else {
      $sitepage_subject = Engine_Api::_()->core()->getSubject()->getParent();
    }

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage_subject->package_id, "modules", "sitepagedocument")) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage_subject, 'sdcreate');
      if (empty($isPageOwnerAllow)) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
      }
    }
    //PACKAGE BASE PRIYACY END
    //TOTAL DOCUMENT
    $documentCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage_subject->page_id, 'sitepagedocument', 'documents');
    $documentCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'sdcreate');

//    //START MANAGE-ADMIN CHECK
//    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'view');
//    if (empty($isManageAdmin)) {
//      return $this->setNoRender();
//    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    if (empty($documentCreate) && empty($documentCount) && empty($can_edit) && !(Engine_Api::_()->sitepage()->showTabsWithoutContent())) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NO_DATA);
    }

    //END MANAGE-ADMIN CHECK
    //GET DOCUMENT TABLE
    $documentTable = Engine_Api::_()->getDbtable('documents', 'sitepagedocument');

    //GET SEARCHING PARAMETERS
    $page = $this->_getParam('page', 1);
    $search = $this->_getParam('search');
    $selectbox = $this->_getParam('selectbox');
    $checkbox = $this->_getParam('checkbox');

    $values = array();
    $values['orderby'] = '';
    if (!empty($search)) {
      $values['search'] = $search;
    }
    if (!empty($selectbox) && $selectbox == 'featured') {
      $values['featured'] = 1;
      $values['orderby'] = 'document_id';
    } elseif (!empty($selectbox) && $selectbox == 'highlighted') {
      $values['highlighted'] = 1;
      $values['orderby'] = 'document_id';
    } elseif (!empty($selectbox)) {
      $values['orderby'] = $selectbox;
    }
    if (!empty($checkbox) && $checkbox == 1) {
      $values['owner_id'] = $viewer_id;
    }

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'sdcreate');
    if (empty($isManageAdmin) && empty($can_edit)) {
      $can_create = 0;
    } else {
      $can_create = 1;
    }
    //BLOCK FOR UPDATING CONVERSION STATUS OF THE DOCUMENT
    $this->scribd_api_key = Engine_Api::_()->getApi('settings', 'core')->sitepagedocument_api_key;
    $this->scribd_secret = Engine_Api::_()->getApi('settings', 'core')->sitepagedocument_secret_key;
    $this->scribdsitepage = new Scribdsitepage($this->scribd_api_key, $this->scribd_secret);

    //FETCH DOCUMENTS
    $params = array();
    $params['page_id'] = $sitepage_subject->page_id;
    $params['profile_page_widget'] = 1;
    $doc_forUpdate = $documentTable->widgetDocumentsData($params);
    $stat = "";
    foreach ($doc_forUpdate as $value) {

      if (empty($value->doc_id)) {
        continue;
      }

      $this->scribdsitepage->my_user_id = $value->owner_id;

      try {
        $stat = trim($this->scribdsitepage->getConversionStatus($value->doc_id));
      } catch (Exception $e) {
        $this->_jsonErrorOutput($e->getMessage());
      }

      if ($stat == 'DONE') {
        try {
          //GETTING DOCUMENT'S FULL TEXT
          $texturl = $this->scribdsitepage->getDownloadUrl($value->doc_id, 'txt');
          //for some reason, the URL comes back with leading and trailing spaces
          $texturl = trim($texturl['download_link']);

          $file_contents = file_get_contents($texturl);
          if (empty($file_contents)) {
            $site_url = $texturl;
            $ch = curl_init();
            $timeout = 0; // set to zero for no timeout
            curl_setopt($ch, CURLOPT_URL, $site_url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

            ob_start();
            curl_exec($ch);
            curl_close($ch);
            $file_contents = ob_get_contents();
            ob_end_clean();
          }
          $full_text = $file_contents;

          $setting = $this->scribdsitepage->getSettings($value->doc_id);
          $thumbnail_url = trim($setting['thumbnail_url']);

          //UPDATING DOCUMENT STATUS AND FULL TEXT
          $value->fulltext = $full_text;
          $value->thumbnail = $thumbnail_url;
          $value->status = 1;
          $value->save();
        } catch (Exception $e) {
          if ($e->getCode() == 619) {
            $value->status = 3;
            $value->save();

            //SEND EMAIL TO DOCUMENT OWNER IF PAGE DOCUMENT HAS BEEN DELETED FROM SCRIBD
            Engine_Api::_()->sitepagedocument()->emailDocumentDelete($value, $sitepage_subject->title, $sitepage_subject->owner_id);
          }
        }

        //ADD ACTIVITY ONLY IF DOCUMENT IS PUBLISHED
        $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', $value->document_id);
        if ($sitepagedocument->draft == 0 && $sitepagedocument->approved == 1 && $sitepagedocument->status == 1 && $sitepagedocument->search == 1 && $sitepagedocument->activity_feed == 0) {
          $api = Engine_Api::_()->getDbtable('actions', 'activity');
          $creator = Engine_Api::_()->getItem('user', $sitepagedocument->owner_id);
          // $action = $api->addActivity($creator, $sitepage_subject, 'sitepagedocument_new');
          $activityFeedType = null;
          if (Engine_Api::_()->sitepage()->isPageOwner($sitepage_subject, $creator) && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
            $activityFeedType = 'sitepagedocument_admin_new';
          elseif ($sitepage_subject->all_post || Engine_Api::_()->sitepage()->isPageOwner($sitepage_subject, $creator))
            $activityFeedType = 'sitepagedocument_new';

          if ($activityFeedType) {
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($creator, $sitepage_subject, $activityFeedType);
            Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
          }
          //MAKE SURE ACTION EXISTS BEFORE ATTACHING THE DOUCMENT TO THE ACTIVITY
          if ($action != null) {
            $api->attachActivity($action, $sitepagedocument);
            $sitepagedocument->activity_feed = 1;
            $sitepagedocument->save();
          }
        }
      } elseif ($stat == 'ERROR') {
        $value->status = 2;
        $value->save();
      }

      //DELETE DOCUMENT FROM SERVER IF ALLOWED BY ADMIN AND HAS STATUS ONE OR TWO
      $sitepagedocument_save_local_server = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.save.local.server', 1);
      if ($sitepagedocument_save_local_server == 0 && ($value->status == 1 || $value->status == 2)) {
        Engine_Api::_()->sitepagedocument()->deleteServerDocument($value->document_id);
      }
    }

    //CHECK THAT RATING IS VIEABLE OR NOT
    $this->view->show_rate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.rating', 1);

    //FETCH DOCUMENTS
    $values['page_id'] = $sitepage_subject->page_id;
    if ($can_edit == 1) {
      $values['show_document'] = 0;
    } else {
      $values['show_document'] = 1;
      $values['document_owner_id'] = $viewer_id;
    }

    $paginator = $documentTable->getSitepagedocumentsPaginator($values);
    //10 DOCUMENTS PER PAGE
    $item_per_page = 10;
    $paginator->setItemCountPerPage($item_per_page)->setCurrentPageNumber($page);

    $documents = array();
    $i = 0;
    foreach ($paginator as $document) {
      $documents[$i]['item_id'] = $document->getIdentity();
      $documents[$i]['item_type'] = $document->getType();
      $documents[$i]['thumb_photo'] = $document->getPhotoUrl('thumb.normal');
      $documents[$i]['name'] = $document->getTitle();
      $documents[$i]['description'] = strip_tags($document->sitepagedocument_description);
      $documents[$i]['posted_by'] = $this->_helper->commonAPI->getBasicInfoFromItem($document->getOwner());
      $documents[$i]['creation_date'] = $document->creation_date;
      $documents[$i]['view_count'] = $document->views;
      $documents[$i]['comment_count'] = $document->comment_count;
      $documents[$i]['like_count'] = $document->like_count;
      $documents[$i]['can_edit'] = (int) ($viewer_id == $document->owner_id || $can_edit == 1);
      $documents[$i]['can_delete'] = (int) ($viewer_id == $document->owner_id || $can_edit == 1);
//      $documents[$i]['can_create'] = (int) $can_create;
      $i++;
    }

    $this->_jsonSuccessOutput(array(
        'totalDocuments' => $paginator->getTotalItemCount(),
        'documents' => $documents,
        'itemCountPerPage' => $item_per_page
    ));
  }

  public function fetchCircleVideoDetailsAction()
  {
    //GET VIDEO ID AND OBJECT
    $video_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('video_id', $this->_getParam('video_id', null));
    $sitepagevideo = Engine_Api::_()->getItem('sitepagevideo_video', $video_id);

    if (empty($sitepagevideo)) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::VIDEO_NOT_FOUND);
    }

    $getPackagevideoView = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepagevideo');

    //GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //SET SITEPAGE SUBJECT
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagevideo->page_id);

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagevideo")) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'svcreate');
      if (empty($isPageOwnerAllow)) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
      }
    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'svcreate');
    if (empty($isManageAdmin)) {
      $can_create = 0;
    } else {
      $can_create = 1;
    }
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'comment');
    if (empty($isManageAdmin)) {
      $can_comment = 0;
    } else {
      $can_comment = 1;
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin) && $viewer_id != $sitepagevideo->owner_id) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    if ($viewer_id != $sitepagevideo->owner_id && $can_edit != 1 && ($sitepagevideo->status != 1 || $sitepagevideo->search != 1) || empty($getPackagevideoView)) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::VIDEO_NOT_FOUND);
    }
    //END MANAGE-ADMIN CHECK
    //GET VIDEO TAGS
    $videoTags = $sitepagevideo->tags()->getTagMaps();
    $tagString = '';
    foreach ($videoTags as $tag) {
      $tagString .= $tag->getTag()->text . ', ';
    }
    $tagString = preg_replace('/, $/', '', $tagString);

    //SET PAGE-VIDEO SUBJECT
    if (Engine_Api::_()->core()->hasSubject()) {
      Engine_Api::_()->core()->clearSubject();
    }
    Engine_Api::_()->core()->setSubject($sitepagevideo);

    $rating_count = Engine_Api::_()->getDbTable('ratings', 'sitepagevideo')->ratingCount($sitepagevideo->getIdentity());
    $rated = Engine_Api::_()->getDbTable('ratings', 'sitepagevideo')->checkRated($sitepagevideo->getIdentity(), $viewer->getIdentity());

    // Rating Info
    $ratingInfo = array(
        'rating_count' => $rating_count,
        'isRated' => (int) $rated,
        'video_rating' => $sitepagevideo->rating
    );

    // Get video owner
    $owner = $sitepagevideo->getParent();
    $ownerInfo = array(
        'name' => $owner->getTitle(),
        'user_id' => $owner->getIdentity(),
    );

    $data = array(
        'tagString' => $tagString,
        'categoryString' => null,
        'favorite_count' => null,
        'creation_date' => strip_tags($this->view->timestamp($sitepagevideo->creation_date)),
        'ownerInfo' => $ownerInfo,
        'ratingInfo' => $ratingInfo,
        'isLiked' => (int) $sitepagevideo->likes()->isLike($viewer),
        'view_count' => $sitepagevideo->view_count,
        'video_title' => $sitepagevideo->getTitle(),
        'video_description' => $sitepagevideo->getDescription(),
        'video_location' => $this->_helper->videoAPI->getVideoLocation($sitepagevideo),
        'video_type' => $this->_helper->videoAPI->getMobileVideoType($sitepagevideo),
        'can_edit' => $can_edit,
        'can_delete' => $can_edit,
        'commentInfo' => $this->_helper->commonAPI->getCommentInfo($sitepagevideo)
    );

    $this->_jsonSuccessOutput($data);
  }

  public function fetchCircleNoteDetailsAction()
  {
    //GET PLAYLIST ID AND OBJECT
    $note_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('note_id', $this->_getParam('note_id', null));
    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET NOTE ITEM
    $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $note_id);
    if (empty($sitepagenote)) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ITEM_NOT_FOUND);
    }

    //PAGE ID
    $page_id = $sitepagenote->page_id;

    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //GETTING THE NOTE TAGS
    $noteTags = $sitepagenote->tags()->getTagMaps();

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sncreate');
    if (empty($isManageAdmin)) {
      $can_create = 0;
    } else {
      $can_create = 1;
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'comment');
    if (empty($isManageAdmin)) {
      $can_comment = 0;
    } else {
      $can_comment = 1;
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin) && $viewer_id != $sitepagenote->owner_id) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //CHECKING THE USER HAVE THE PERMISSION TO VIEW THE NOTE OR NOT
    if ($viewer_id != $sitepagenote->owner_id && $can_edit != 1 && ($sitepagenote->draft == 1 || $sitepagenote->search != 1)) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    //SHOW PHOTO
    $photoNotes = Engine_Api::_()->getDbTable('photos', 'sitepagenote')->getNotePhotos($sitepagenote->note_id);

    // Gets note's photos
    $photos = array();
    $i = 0;
    foreach ($photoNotes as $photo) {
      $photos[$i]['photo_id'] = $photo->getIdentity();
      $photos[$i]['thumb_photo_url'] = $photo->getPhotoUrl('thumb.normal');
      $photos[$i]['big_photo_url'] = $photo->getPhotoUrl();
      $photos[$i]['photo_title'] = $photo->getTitle();
      $photos[$i]['photo_description'] = $photo->getDescription();
      // Get photo permissions
      $photos[$i]['can_comment'] = (int) $photo->authorization()->isAllowed($viewer, 'comment');
      $photos[$i]['can_share'] = 0;

      // Get likers
      $photos[$i]['like_info'] = $this->_helper->commonAPI->getLikeInfo($photo, $viewer);
      // Get comment count
      $photos[$i]['comment_count'] = $this->_helper->commonAPI->getCommentInfo($photo)['comment_count'];

      $i++;
    }

    //INCREMENT IN NUMBER OF VIEWS
    if (!$sitepagenote->getOwner()->isSelf($viewer)) {
      $sitepagenote->view_count++;
    }
    $sitepagenote->save();

    // Gets note's thumb photo
    if ($sitepagenote->photo_id != 0) {
      $thum_photo = $sitepagenote->getPhotoUrl('thumb.normal');
    } elseif ($sitepage->photo_id != 0) {
      $thum_photo = $sitepage->getPhotoUrl('thumb.normal');
    } else {
      $thum_photo = $this->view->getNoPhoto($sitepagenote, 'thumb.normal');
    }

    // Gets note owner
    $owner = $sitepagenote->getOwner();
    $ownerInfo = array(
        'name' => $owner->getTitle(),
        'user_id' => $owner->getIdentity(),
    );

    // Gets comment info
    $commentInfo = $this->_helper->commonAPI->getCommentInfo($sitepagenote);
    // Gets like info
    $likeInfo = $this->_helper->commonAPI->getLikeInfo($sitepagenote, $viewer);

    $this->_jsonSuccessOutput(array(
        'thumb_photo' => $thum_photo,
        'title' => $sitepagenote->getTitle(),
        'ownerInfo' => $ownerInfo,
        'creation_date' => strip_tags($this->view->timestamp($sitepagenote->creation_date)),
        'view_count' => $sitepagenote->view_count,
        'comment_count' => $sitepagenote->comment_count,
        'like_count' => $sitepagenote->like_count,
        'description' => strip_tags($sitepagenote->body),
        'photos' => $photos,
        'can_comment' => $commentInfo['can_comment'],
        'can_like' => $likeInfo['likeable'],
        'is_liked' => $likeInfo['is_liked'],
        'can_edit' => $can_edit,
        'can_delete' => $can_edit,
        'commentInfo' => $commentInfo
    ));
  }

  public function fetchCircleDocumentDetailsAction()
  {

    include_once APPLICATION_PATH . '/application/modules/Sitepagedocument/Api/Scribdsitepage.php';

    //SET SCRIBD API AND SCECRET KEY
    $this->scribd_api_key = Engine_Api::_()->getApi('settings', 'core')->sitepagedocument_api_key;
    $this->scribd_secret = Engine_Api::_()->getApi('settings', 'core')->sitepagedocument_secret_key;
    $this->scribdsitepage = new Scribdsitepage($this->scribd_api_key, $this->scribd_secret);

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET DOCUMNET MODEL
    $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', Zend_Controller_Front::getInstance()->getRequest()->getParam('document_id'));
    if (empty($sitepagedocument)) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ITEM_NOT_FOUND);
    }

    //SET PAGE SUBJECT
    $sitepage_subject = null;
    $page_id = $sitepagedocument->page_id;
    if (null !== $page_id) {
      $sitepage_subject = Engine_Api::_()->getItem('sitepage_page', $page_id);
    }

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage_subject->package_id, "modules", "sitepagedocument")) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage_subject, 'sdcreate');
      if (empty($isPageOwnerAllow)) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
      }
    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'view');
    if (empty($isManageAdmin)) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'comment');
    if (empty($isManageAdmin)) {
      $can_comment = 0;
    } else {
      $can_comment = 1;
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'edit');
    if (empty($isManageAdmin) && $viewer_id != $sitepagedocument->owner_id) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    if (($can_edit != 1 && $viewer_id != $sitepagedocument->owner_id) && ($sitepagedocument->draft == 1 || $sitepagedocument->status != 1 || $sitepagedocument->approved != 1 || $sitepagedocument->search != 1)) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    //CHECK THAT VIEWER CAN RATE THE DOCUMENT OR NOT
    $can_rate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.rating', 1);

    //GET OWNER INFORMATION
    $owner = $sitepagedocument->getOwner();

    //INCREMENT IN NUMBER OF VIEWS
    if (!$owner->isSelf($viewer)) {
      $sitepagedocument->views++;
    }

    //SET SCRIBD USER ID
    $this->scribdsitepage->my_user_id = $sitepagedocument->owner_id;
    Engine_Api::_()->sitepagedocument()->setDocumentPackages();

    $stat = null;
    if (!empty($sitepagedocument->doc_id)) {
      try {
        $stat = trim($this->scribdsitepage->getConversionStatus($sitepagedocument->doc_id));
      } catch (Exception $e) {
        $this->_jsonErrorOutput($e->getMessage());
      }
    }

    //CHECK VIEWER CAN DOWNLOAD AND EMAIL THIS DOCUMENT OR NOT
    if (!empty($viewer_id)) {
      $download_allow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.download.allow', 1);
      $download_format = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.download.format', 'pdf');
    } else {
      $download_allow = 0;
      $download_format = 0;
    }

    if (!empty($viewer_id)) {
      if ($download_allow && $stat == 'DONE' && $sitepagedocument->download_allow) {
        try {
          $link = $this->scribdsitepage->getDownloadUrl($sitepagedocument->doc_id, $download_format);
        } catch (Exception $e) {
          
        }
        $sitepagedocument_include_full_text = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.include.full.text', 1);
        if ($sitepagedocument_include_full_text == 1) {
          $doc_full_text = $sitepagedocument->fulltext;
        }
      }
    } else { //WE SHOW FULL TEXT IN CASE OF NONLOGGEDIN USER IF DOCUMENT IS AVAILABLE FOR DOWNLOADING  AND ADMIN HAD ALLOWED FOR FULL TEXT IN GLOBAL SETTINGS
      if ($stat == 'DONE') {
        $sitepagedocument_include_full_text = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.include.full.text', 1);
        $sitepagedocument_visitor_fulltext = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.visitor.fulltext', 1);
        if ($sitepagedocument_include_full_text == 1 && $sitepagedocument_visitor_fulltext == 1 && $sitepagedocument->download_allow) {
          $doc_full_text = $sitepagedocument->fulltext;
        }
      }
    }

    //IF STAT IS DONE THAN UPDATE DOCUMENT STATUS AND OTHER INFORMATION
    if ($stat == 'DONE') {
      try {
        //GETTING DOCUMENT'S FULL TEXT
        $texturl = $this->scribdsitepage->getDownloadUrl($sitepagedocument->doc_id, 'txt');
        if ($sitepagedocument->status != 1) {
          $texturl = trim($texturl['download_link']);
          $file_contents = file_get_contents($texturl);
          if (empty($file_contents)) {
            $site_url = $texturl;
            $ch = curl_init();
            $timeout = 0; //SET ZERO FOR NO TIMEOUT
            curl_setopt($ch, CURLOPT_URL, $site_url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

            ob_start();
            curl_exec($ch);
            curl_close($ch);
            $file_contents = ob_get_contents();
            ob_end_clean();
          }
          $full_text = $file_contents;

          $setting = $this->scribdsitepage->getSettings($sitepagedocument->doc_id);
          $thumbnail_url = trim($setting['thumbnail_url']);

          //UPDATING DOCUMENT STATUS AND FULL TEXT
          $sitepagedocument->fulltext = $full_text;
          $sitepagedocument->thumbnail = $thumbnail_url;
          $sitepagedocument->status = 1;

          //ADD ACTIVITY ONLY IF DOCUMENT IS PUBLISHED
          if ($sitepagedocument->draft == 0 && $sitepagedocument->approved == 1 && $sitepagedocument->status == 1 && $sitepagedocument->search == 1 && $sitepagedocument->activity_feed == 0) {
            $api = Engine_Api::_()->getDbtable('actions', 'activity');
            $creator = Engine_Api::_()->getItem('user', $sitepagedocument->owner_id);
            $activityFeedType = null;
            if (Engine_Api::_()->sitepage()->isPageOwner($sitepage_subject) && Engine_Api::_()->sitepage()->isFeedTypePageEnable()) {
              $activityFeedType = 'sitepagedocument_admin_new';
            } elseif ($sitepage_subject->all_post || Engine_Api::_()->sitepage()->isPageOwner($sitepage_subject)) {
              $activityFeedType = 'sitepagedocument_new';
            }

            if ($activityFeedType) {
              $action = $api->addActivity($creator, $sitepage_subject, $activityFeedType);
              Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
            }
            //MAKE SURE ACTION EXISTS BEFORE ATTACHING THE DOUCMENT TO THE ACTIVITY
            if ($action != null) {
              $api->attachActivity($action, $sitepagedocument);
              $sitepagedocument->activity_feed = 1;
              $sitepagedocument->save();
            }

            //PAGE DOCUMENT CREATE NOTIFICATION AND EMAIL WORK
            $sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;
            if ($sitepageVersion >= '4.2.9p3') {
              Engine_Api::_()->sitepage()->sendNotificationEmail($sitepagedocument, $action, 'sitepagedocument_create', 'SITEPAGEDOCUMENT_CREATENOTIFICATION_EMAIL', 'Pageevent Invite');

              $isPageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->isPageAdmins($viewer->getIdentity(), $page_id);
              if (!empty($isPageAdmins)) {
                //NOTIFICATION FOR ALL FOLLWERS.
                Engine_Api::_()->sitepage()->sendNotificationToFollowers($sitepagedocument, $action, 'sitepagedocument_create');
              }
            }
          }
        }
      } catch (Exception $e) {
        if ($sitepagedocument->status != 3 && $e->getCode() == 619) {
          $sitepagedocument->status = 3;

          //SEND EMAIL TO DOCUMENT OWNER IF PAGE DOCUMENT HAS BEEN DELETED FROM SCRIBD
          Engine_Api::_()->sitepagedocument()->emailDocumentDelete($sitepagedocument, $sitepage_subject->title, $sitepage_subject->owner_id);
        }
      }
      $sitepagedocument->save();
    } elseif ($stat == 'ERROR') {
      if ($sitepagedocument->status != 2) {
        $sitepagedocument->status = 2;
        $sitepagedocument->save();
      }
    } else {
      $sitepagedocument->save();
    }

    //DELETE DOCUMENT FROM SERVER IF ALLOWED BY ADMIN AND HAS STATUS ONE OR TWO
    $sitepagedocument_save_local_server = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.save.local.server', 1);
    if ($sitepagedocument_save_local_server == 0 && ($sitepagedocument->status == 1 || $sitepagedocument->status == 2)) {
      Engine_Api::_()->sitepagedocument()->deleteServerDocument($sitepagedocument->document_id);
    }

    //RATING WORK
    $rating_count = Engine_Api::_()->getDbTable('ratings', 'sitepagedocument')->countRating($sitepagedocument->getIdentity());
    $sitepagedocument_rated = Engine_Api::_()->getDbTable('ratings', 'sitepagedocument')->previousRated($sitepagedocument->getIdentity(), $viewer->getIdentity());

    // Gets note owner
    $ownerInfo = array(
        'name' => $owner->getTitle(),
        'user_id' => $owner->getIdentity(),
    );

    // Gets comment info
    $commentInfo = $this->_helper->commonAPI->getCommentInfo($sitepagedocument);
    // Gets like info
    $likeInfo = $this->_helper->commonAPI->getLikeInfo($sitepagedocument, $viewer);

    // Checks if user can download
    $can_download = true;
    $view_download_allow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.download.allow', 1);
    if (empty($view_download_allow) || empty($sitepagedocument->download_allow)) {
      $can_download = false;
    }

    $this->_jsonSuccessOutput(array(
        'title' => $sitepagedocument->getTitle(),
        'description' => strip_tags($sitepagedocument->sitepagedocument_description),
        'ownerInfo' => $ownerInfo,
        'creation_date' => strip_tags($this->view->timestamp($sitepagedocument->creation_date)),
        'category' => $this->_translator->_((string) $sitepagedocument->categoryName()),
        'comment_count' => $sitepagedocument->comment_count,
        'view_count' => $sitepagedocument->views,
        'like_count' => $sitepagedocument->like_count,
        'can_comment' => $commentInfo['can_comment'],
        'can_like' => $likeInfo['likeable'],
        'is_liked' => $likeInfo['is_liked'],
        'can_edit' => $can_edit,
        'can_delete' => $can_edit,
        'can_download' => $can_download,
        'commentInfo' => $commentInfo,
        'document_url' => $link
    ));
  }

  /**
   * Delete a circle note
   * Corresponding URL: circle-notes/delete/:note_id/:circle_id
   */
  public function deleteCircleNoteAction()
  {
    try {
      $this->getRequest()->setPost('confirm', true);
      $params = $this->getRequest()->getParams();
      $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $params['note_id']);
      if (!$sitepagenote) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ITEM_NOT_FOUND);
      }
      $params['page_id'] = $sitepagenote->page_id;
      // Transfer request to desired controller
      $this->_customDispatch('delete', 'index', 'sitepagenote', $params);
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }

    $this->_jsonSuccessOutput(array(
        'message' => $this->_translator->_('Note deleted')
    ));
  }

  public function uploadCircleNotePhotoAction()
  {
    try {
      $params = $this->getRequest()->getParams();
      $params['format'] = 'json';
      $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $params['note_id']);
      if (!$sitepagenote) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ITEM_NOT_FOUND);
      }
      $this->getRequest()->setPost('Filename', $_FILES['Filedata']['name']);
      $params['page_id'] = $sitepagenote->page_id;
      // Transfer request to desired controller
      $result = json_decode($this->view->action('upload-photo', 'photo', 'sitepagenote', $params));
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }

    if ($result->status == true) {
      $this->_jsonSuccessOutput(array(
          'photo_id' => $result->photo_id,
          'message' => $this->_translator->_('Photo uploaded')
      ));
    } else {
      $this->_jsonErrorOutput($result->error);
    }
  }

  /**
   * Delete a circle note
   * Corresponding URL: circle-documents/delete/:document_id/:circle_id
   */
  public function deleteCircleDocumentAction()
  {
    try {
      $this->getRequest()->setPost('confirm', true);
      $params = $this->getRequest()->getParams();
      $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', $params['document_id']);
      if (!$sitepagedocument) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ITEM_NOT_FOUND);
      }
      $params['page_id'] = $sitepagedocument->page_id;
      // Transfer request to desired controller
      $this->_customDispatch('delete', 'index', 'sitepagedocument', $params);
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }

    $this->_jsonSuccessOutput(array(
        'message' => $this->_translator->_('Document deleted')
    ));
  }

  /**
   * Fetches membership requests
   * Corresponding URL: circle-members/index/request-member/page_id/:page_id
   */
  public function fetchCircleMembershipRequestsAction()
  {
    $this->_initCircleAction(true);

    if (!Engine_Api::_()->core()->hasSubject()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::CIRCLE_NOT_FOUND);
    }

    $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
    $sitepage = Engine_Api::_()->core()->getSubject();
    $values = array(
        'page_id' => $sitepage->page_id
    );
    $paginator = $membershipTable->getSitepagemembersPaginator($values, 'request');
    $item_per_page = 24;

    $requests = array();
    $i = 0;
    foreach ($paginator as $request) {
      $requests[$i]['user_id'] = $request->user_id;
      $requests[$i]['user_name'] = $request->getTitle();
      $requests[$i]['user_photo'] = $request->getOwner()->getPhotoUrl('thumb.icon');
      $requests[$i]['member_id'] = $request->member_id;
      $requests[$i]['page_id'] = $request->page_id;
      $requests[$i]['permission_info'] = array(
          'can_accept_reject' => (int) ($request->active == 0 && $request->user_approved == 0)
      );
      $i++;
    }
    $paginator->setItemCountPerPage($item_per_page);

    $data['users'] = $requests;
    $data['totalUsers'] = $paginator->getTotalItemCount();
    $data['userCount'] = $paginator->getCurrentItemCount();
    $data['itemCountPerPage'] = $item_per_page;

    $this->_jsonSuccessOutput($data);
  }

  public function rejectCircleMembershipRequestAction()
  {
    try {
      $params = $this->getRequest()->getParams();
      // Transfer request to desired controller
      $this->_customDispatch('reject', 'index', 'sitepagemember', $params);
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }

    $this->_jsonSuccessOutput(array(
        'message' => $this->_translator->_('Request rejected')
    ));
  }

  public function acceptCircleMembershipRequestAction()
  {
    try {
      $params = $this->getRequest()->getParams();
      // Transfer request to desired controller
      $this->_customDispatch('approve', 'index', 'sitepagemember', $params);
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }

    $this->_jsonSuccessOutput(array(
        'message' => $this->_translator->_('Request accepted')
    ));
  }

  public function fetchCirclePhotoDetailsAction()
  {
    $photo_id = (int) $this->_getParam('photo_id');
    $photo = Engine_Api::_()->getItem('sitepage_photo', $photo_id);

    //CHECK SUBJECT IS THERE OR NOT
    if ($photo == null) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::PHOTO_NOT_FOUND);
    }

    //GET ALBUM INFORMATION
    $album = $photo->getCollection();

    if (!$album) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ALBUM_NOT_FOUND);
    }

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET SITEPAGE ITEM
    if (!empty($album)) {
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $album->page_id);
    }

    //START MANAGE-ADMIN CHECK
    if (!empty($sitepage)) {
      $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
      if (empty($isManageAdmin)) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
      }

      $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'comment');
      if (empty($isManageAdmin)) {
        $can_comment = 0;
      } else {
        $can_comment = 1;
      }

      $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
      if (empty($isManageAdmin)) {
        $can_edit = 0;
      } else {
        $can_edit = 1;
      }
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'spcreate');
      if (empty($isPageOwnerAllow)) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
      }
    }
    //END MANAGE-ADMIN CHECK

    if ($can_edit) {
      $canTag = 1;
    } else {
      $canTag = $album->authorization()->isAllowed($viewer, 'tag');
    }

    //PHOTO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT AND DELETE PHOTO
    if ($viewer_id == $photo->user_id || $can_edit == 1) {
      $canDelete = 1;
      $canEdit = 1;
    } else {
      $canDelete = 0;
      $canEdit = 0;
    }

    //INCREMENT VIEWS
    if (!$photo->getOwner()->isSelf(Engine_Api::_()->user()->getViewer())) {
      $photo->view_count++;
    }
    //SAVE
    $photo->save();

    $data = array();
    $data['thumb_photo_url'] = $photo->getPhotoUrl('thumb.normal');
    $data['big_photo_url'] = $photo->getPhotoUrl();
    $data['photo_title'] = $photo->getTitle();
    $data['photo_description'] = $photo->getDescription();
    $data['canEdit'] = (int) $canEdit;
    $data['canDelete'] = (int) $canDelete;
    $data['canTag'] = (int) $canTag;
    $data['canUntag'] = (int) $can_edit;

    // Get like info
    $data['like_info'] = $this->_helper->commonAPI->getLikeInfo($photo, $viewer);
    // Get comment info
    $data['comment_info'] = $this->_helper->commonAPI->getCommentInfo($photo);

    $this->_jsonSuccessOutput($data);
  }

  public function testPushNotificationAction()
  {
    if ($this->getRequest()->isPost() && ($message = $this->getRequest()->getPost('message'))) {
      $device_token = $this->_getParam('device_token');
      $device_type = $this->_getParam('device_type');
      Engine_Api::_()->getApi('core', 'mgslapi')->sendPushNotification($device_token, $device_type, $message);
      $this->_jsonSuccessOutput(array(
          'message' => 'Successfully sent notifications!'
      ));
    }
  }

  /**
   * Api Group: Push notification API
   * Unregister push notification
   */
  public function unregisterDeviceAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    $device_token = $this->_getParam('device_token');
    $app_id = $this->_getParam('app_id');

    if (empty($device_token)) {
      $this->_jsonErrorOutput('Device token required');
    }

    if (empty($app_id)) {
      $app_id = $device_token;
    }

    //echo $device_type; exit;
    $device_table = Engine_Api::_()->getDbtable('devices', 'mgslapi');
    $db = $device_table->getAdapter();
    $db->beginTransaction();
    $select = $device_table->select()
            ->where('user_id = ?', $viewer->getIdentity())
            ->where('app_id = ?', $app_id)
    ;
    $registeredDevice = $device_table->fetchRow($select);
    $registeredDevice->delete();
    $db->commit();

    $message = $this->view->translate('Device unregistered');
    $this->_jsonSuccessOutput(array(
        'message' => $message
    ));
  }

  /**
   * Api Group: Push notification API
   * Register/Update device info in the database whenever device info is changed
   */
  public function updateDeviceInfoAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    $device_token = strip_tags(trim($this->_getParam('device_token')));
    $app_id = $this->_getParam('app_id');

    if (empty($device_token)) {
      $this->_jsonErrorOutput('Device token required');
    }

    if (empty($app_id)) {
      $app_id = $device_token;
    }

    //echo $device_type; exit;
    $device_table = Engine_Api::_()->getDbtable('devices', 'mgslapi');
    $db = $device_table->getAdapter();
    $db->beginTransaction();
    try {
      $select = $device_table->select()
              ->where('user_id = ?', $viewer->getIdentity())
              ->where('device_token = ?', $device_token)
      ;
      $registeredDevice = $device_table->fetchRow($select);

      if ($registeredDevice->device_id) {
        $registeredDevice->app_id = $app_id;
        $registeredDevice->save();
      } else {
        $select = $device_table->select()
                ->where('user_id = ?', $viewer->getIdentity())
                ->where('app_id = ?', $app_id)
        ;

        $registeredDevice = $device_table->fetchRow($select);
        if ($registeredDevice->device_id) {
          $registeredDevice->device_token = $device_token;
          $registeredDevice->save();
        } else {
          $device = $device_table->createRow();
          $device->user_id = $viewer->getIdentity();
          $device->device_type = $this->_deviceType;
          $device->device_token = $device_token;
          $device->app_id = $app_id;
          $device->save();
        }
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
//      $this->_jsonErrorOutput($e->getMessage());
    }
    $message = $this->view->translate('Device info updated');
    $this->_jsonSuccessOutput(array(
        'message' => $message
    ));
  }

  public function fetchChatHistoryOfUserAction()
  {
    $user = Engine_Api::_()->getItem('user', $this->_getParam('user_id'));
    if (!$user) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::USER_NOT_FOUND);
    }
    /* @var $viewer User_Model_User */
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }
    /* @var $whisperTable Chat_Model_DbTable_Whispers */
    $whisperTable = Engine_Api::_()->getDbTable('whispers', 'chat');
    $select = $whisperTable->select()
            ->where("recipient_id = {$viewer->getIdentity()} AND sender_id = {$user->getIdentity()}")
            ->orWhere("recipient_id = {$user->getIdentity()} AND sender_id = {$viewer->getIdentity()}")
            ->order('whisper_id DESC');
    $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber((int) $this->_getParam('page'));
    $item_per_page = 50;
    $paginator->setItemCountPerPage($item_per_page);

    $db = $whisperTable->getAdapter();

    $db->beginTransaction();
    $messages = array();
    $i = 0;
    foreach ($paginator as $message) {
      $messages[$i]['whisper_id'] = $message->whisper_id;
      $messages[$i]['body'] = $message->body;
      $messages[$i]['sender_id'] = $message->sender_id;
      $messages[$i]['recipient_id'] = $message->recipient_id;
      $messages[$i]['date'] = $message->date;
      $messages[$i]['type'] = $message->sender_id == $viewer->getIdentity() ? 'sender' : 'receiver';

      // If the message was sent from the viewer, it has been read by the viewer
      if ($messages[$i]['type'] == 'sender') {
        $messages[$i]['is_read'] = 1;
      } else {
        $messages[$i]['is_read'] = $message->is_read;
      }

      if ($message->is_read == 0 && $message->recipient_id == $viewer->getIdentity()) {
        $message->is_read = 1;
        $message->save();
      }

      $i++;
    }
    $db->commit();
    $this->_jsonSuccessOutput(array(
        'messages' => $messages,
        'total' => $paginator->getTotalItemCount(),
        'item_per_page' => $item_per_page
    ));
  }

  public function fetchUnreadChatMessageCountsAction()
  {
    /* @var $viewer User_Model_User */
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }
    /* @var $whisperTable Chat_Model_DbTable_Whispers */
    $whisperTable = Engine_Api::_()->getDbTable('whispers', 'chat');
    $select = $whisperTable->select()
            ->from($whisperTable->info('name'), array('sender_id', new Zend_Db_Expr('COUNT(*) as count')))
            ->where("recipient_id = {$viewer->getIdentity()}")
            ->where("is_read = 0")
            ->order('whisper_id DESC')
            ->group('sender_id')
    ;
    $data = $select->query()->fetchAll();

    $this->_jsonSuccessOutput($data);
  }

  /**
   * API Group: Resume
   * Corresponding URL: resumes/browse
   */
  public function fetchResumeListAction()
  {
    $this->_initResumeAction();

    $this->_helper->profileAPI->parseFieldInput('resume');

    ($limit = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('resume.perpage', 10)) || ($limit = (int) $this->_getParam('max', 10));

    $values = array(
        'live' => true,
        'search' => 1,
        'limit' => $limit,
        'preorder' => (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('resume.preorder', 1),
    );

    $form = new Resume_Form_Filter_Browse();

    // Populate form data
    if ($form->isValid($this->_getAllParams())) {
      $requestVals = $form->getValues();
    } else {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
    }
    $requestVals = Engine_Api::_()->getApi('filter', 'radcodes')->removeKeyEmptyValues($requestVals);

    $values = array_merge($values, $requestVals);

    $paginator = Engine_Api::_()->resume()->getResumesPaginator($values);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $refinedResumes = array();
    $viewer = Engine_Api::_()->user()->getViewer();

    foreach ($paginator as $resume) {
      $tmp = $this->_helper->resumeAPI->getResumeBasicInfo($resume);
      $tmp['title'] = $resume->getOwner()->getTitle();
      $tmp['allow_view_detail'] = $resume->authorization()->isAllowed($viewer, 'view');
      $refinedResumes[] = $tmp;
    }

    $this->_jsonSuccessOutput(array(
        'resumes' => $refinedResumes,
        'totalResumes' => $paginator->getTotalItemCount(),
        'itemCountPerPage' => $limit
    ));
  }

  public function fetchSubfieldOptionsAction()
  {
    $fieldValueMaps = include_once APPLICATION_PATH . "/application/modules/Mgslapi/settings/resumefield-value-maps.php";

    // Hard coded, will remove soon?
    $fieldName = 'sport';
    $fieldValue = $this->_getParam('parent_field_value');

    if (array_key_exists($fieldValue, $fieldValueMaps[$fieldName]['subfield']['options'])) {
      $subfieldOptions = array_values($fieldValueMaps[$fieldName]['subfield']['options'][$fieldValue]['values']);
    } else {
      $subfieldOptions = array();
    }
    $this->_jsonSuccessOutput($subfieldOptions);
  }

  /**
   * API Group: Resume
   * Corresponding URL: resumes/manage
   */
  public function fetchMyResumesAction()
  {
    $this->_initResumeAction();

    $viewer = Engine_Api::_()->user()->getViewer();

    ($limit = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('resume.perpage', 10)) || ($limit = (int) $this->_getParam('max', 10));

    $values = array(
        'limit' => $limit,
        'user' => $viewer,
    );

    $paginator = Engine_Api::_()->resume()->getResumesPaginator($values);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $refinedResumes = array();
    $i = 0;
    foreach ($paginator as $resume) {

      $recentEpayment = $resume->getRecentEpayment();

      if ($resume->requiresEpayment()) {
        if ($recentEpayment instanceof Epayment_Model_Epayment) {
          $payment = 'received';
          $button_type = 0;
        } else {
          $payment = 'required';
          $button_type = 1;
        }
      } else {
        $payment = 'not received';
        $button_type = 0;
      }

      $refinedResumes[$i] = $this->_helper->resumeAPI->getResumeBasicInfo($resume);
      $refinedResumes[$i]['title'] = $resume->getOwner()->getTitle();
      $refinedResumes[$i]['can_edit'] = (int) $resume->authorization()->isAllowed($viewer, 'edit');
      $refinedResumes[$i]['can_delete'] = (int) $resume->authorization()->isAllowed($viewer, 'delete');
      $refinedResumes[$i]['package_info'] = array(
          'package' => $resume->getPackage()->getTitle(),
          'publish' => $this->_translator->_($resume->isPublished() ? 'Live' : 'Draft'),
          'status' => $this->view->translate('%1$s - %2$s', $resume->getStatusText(), $this->view->locale()->toDate($resume->status_date)),
          'expire' => strip_tags($resume->hasExpirationDate() ? $this->view->timestamp($resume->expiration_date) : $this->_translator->_('Never')),
          'featured' => $this->view->translate($resume->featured ? 'Yes' : 'No'),
          'sponsored' => $this->view->translate($resume->sponsored ? 'Yes' : 'No'),
          'payment' => $this->view->translate($payment),
          'button_type' => $button_type
      );
      $i++;
    }

    $this->_jsonSuccessOutput(array(
        'resumes' => $refinedResumes,
        'totalResumes' => $paginator->getTotalItemCount(),
        'itemCountPerPage' => $limit
    ));
  }

  /**
   * API Group: Resume
   * Corresponding URL: resumes/packages
   */
  public function fetchResumePackagesAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $can_create = Engine_Api::_()->authorization()->isAllowed('resume', $viewer, 'create');

    $packages = Engine_Api::_()->resume()->getPackages(array('enabled' => 1));

    $refinedPackages = array();
    $i = 0;
    foreach ($packages as $package) {
      $refinedPackages[$i]['title'] = $package->getTitle();
      $refinedPackages[$i]['term'] = $package->getTerm();
      $refinedPackages[$i]['featured'] = $this->view->translate($package->featured ? 'Yes' : 'No');
      $refinedPackages[$i]['sponsored'] = $this->view->translate($package->sponsored ? 'Yes' : 'No');
      $refinedPackages[$i]['description'] = strip_tags($package->getDescription());

      $i++;
    }

    $this->_jsonSuccessOutput(array(
        'resume_can_create' => $can_create,
        'packages' => $refinedPackages,
    ));
  }

  /**
   * API Group: Resume
   * Corresponding URL: resume detail
   */
  public function fetchResumeSummaryAction()
  {
    $this->_initResumeProfileAction();
    $resume = Engine_Api::_()->core()->getSubject();

    // Initialize what we are gonna return
    $summary = array(
        ['Gender' => (string) $resume->getFieldValueString('Gender')],
        ['Date of Birth' => (string) $resume->getFieldValueString('Date of Birth')],
        ['Participation Level' => Engine_Api::_()->getItem('resume_category', $resume->category_id)->category_name],
    );

    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($resume);

    $this->_helper->resumeAPI->removeFieldsFromFieldStructureByLabels($fieldStructure, array('Gender', 'Date of Birth'));

    $fieldValues = $this->view->fieldValueLoop($resume, $fieldStructure);

    $xml = new DOMDocument();
    $xml->loadHTML($fieldValues);

    $lis = $xml->getElementsByTagName('li');

    foreach ($lis as $li) {
      /* @var $li DOMElement */
      $spans = $li->getElementsByTagName('span');
      $label = trim($spans->item(0)->nodeValue);
      $value = trim($spans->item(1)->nodeValue);
      $summary[] = [$label => $value];
    }

    $this->_jsonSuccessOutput(array(
        'summary' => $summary,
    ));
  }

  public function fetchResumeGeneralInfoAction()
  {
    $this->_initResumeProfileAction();
    $resume = Engine_Api::_()->core()->getSubject();

    $data = $this->_helper->resumeAPI->getResumeBasicInfo($resume);
    unset($data['sport']);
    unset($data['participation_level']);
    unset($data['thumb_photo']);

    $this->_jsonSuccessOutput($data);
  }

  public function fetchResumeMapAction()
  {
    $this->_initResumeProfileAction();
    $resume = Engine_Api::_()->core()->getSubject();

    $resume_info = $this->_helper->resumeAPI->getResumeBasicInfo($resume);
    $location = $resume->getLocation();

    $this->_jsonSuccessOutput(array(
        'resume_info' => $resume_info,
        'location_info' => array(
            'address' => $location->formatted_address,
            'latitude' => $location->lat,
            'longitude' => $location->lng,
        )
    ));
  }

  public function fetchResumePhotosAction()
  {
    $this->_initResumeProfileAction();
    /* @var $resume Resume_Model_Resume */
    $resume = Engine_Api::_()->core()->getSubject();

    $album = $resume->getSingletonAlbum();
    $paginator = $album->getCollectiblesPaginator();
    $canUpload = $resume->authorization()->isAllowed(null, 'photo');

    // Set item count per page and current page number
    $item_per_page = 50;
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', $item_per_page));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $photos = array();
    $i = 0;
    foreach ($paginator as $photo) {
      $photos[$i]['photo_id'] = $photo->getIdentity();
      $photos[$i]['thumb_photo_url'] = $photo->getPhotoUrl('thumb.normal');
      $photos[$i]['big_photo_url'] = $photo->getPhotoUrl();
      $photos[$i]['photo_title'] = $photo->getTitle();
      $photos[$i]['photo_description'] = $photo->getDescription();
      $photos[$i]['photo_creation_date'] = strip_tags($this->view->timestamp($photo->creation_date));
      $photos[$i]['photo_posted_by'] = $this->_helper->commonAPI->getBasicInfoFromItem($photo->getOwner());
      // Get photo permissions
//      $photos[$i]['can_edit'] = (int) ($photo->canEdit(Engine_Api::_()->user()->getViewer()));
      $photos[$i]['can_delete'] = $photos[$i]['can_edit'];

      $i++;
    }

    $this->_jsonSuccessOutput(array(
        'canUpload' => (int) $canUpload,
        'itemCountPerPage' => $item_per_page,
        'totalPhoto' => $paginator->getTotalItemCount(),
        'photos' => $photos
    ));
  }

  public function deleteResumeAction()
  {
    if (0 !== ($resume_id = (int) $this->_getParam('resume_id')) &&
            null !== ($resume = Engine_Api::_()->getItem('resume', $resume_id))) {
      Engine_Api::_()->core()->setSubject($resume);
    }
    if (Engine_Api::_()->core()->hasSubject('resume')) {
      /* @var $resume Resume_Model_Resume */
      $resume = Engine_Api::_()->core()->getSubject('resume');
    } else {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ITEM_NOT_FOUND);
    }

    if (!$this->_helper->requireAuth()->setAuthParams($resume, null, 'delete')->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }
    $form = new Resume_Form_Resume_Delete();

    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $resume->delete();
      $db->commit();
      $this->_jsonSuccessOutput(array(
          'message' => $this->_translator->_('CV Profiler deleted')
      ));
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput($e->getMessage());
    }
  }

  public function uploadResumePhotoAction()
  {
    $resume = Engine_Api::_()->getItem('resume', $this->_getParam('resume_id'));

    if (!$this->_helper->requireAuth()->setAuthParams($resume, null, 'photo')->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }
    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }
    if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_UPLOAD);
    }
    try {
      $params = $this->getRequest()->getParams();
      $params['format'] = 'json';
      $this->getRequest()->setPost('Filename', $_FILES['Filedata']['name']);
      // Transfer request to desired controller
      $result = json_decode($this->view->action('upload-photo', 'photo', 'resume', $params));
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }

    if ($result->status == true) {
      $photo_id = $result->photo_id;
      // Add action and attachments
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $resume, 'resume_photo_upload', null, array('count' => 1));
      $photo = Engine_Api::_()->getItem("resume_photo", $photo_id);

      if ($action instanceof Activity_Model_Action) {
        $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
      }
      $this->_jsonSuccessOutput(array(
          'photo_id' => $photo_id,
          'message' => $this->_translator->_('Photo uploaded')
      ));
    } else {
      $this->_jsonErrorOutput($result->error);
    }
  }

  public function fetchPersonalEventsAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if ($this->_getParam('filter') == 'myevent') {
      $paginator = $this->_helper->eventAPI->getMyEvents();
    } else {
      $paginator = $this->_helper->eventAPI->getPersonalEvents();
    }
    $item_per_page = 10;

    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $paginator->setItemCountPerPage($item_per_page);

    $refinedEvents = array();
    $i = 0;
    foreach ($paginator as $event) {
      $can_edit = $viewer && $event->isOwner($viewer);

      $refinedEvents[$i]['item_id'] = $event->getIdentity();
      $refinedEvents[$i]['item_type'] = $event->getType();
      $refinedEvents[$i]['event_thumb_photo'] = $event->getPhotoUrl('thumb.normal');
      $refinedEvents[$i]['event_name'] = $event->getTitle();
      $refinedEvents[$i]['event_description'] = $event->getDescription();
      $refinedEvents[$i]['event_start_time'] = $this->view->locale()->toDateTime($event->starttime);
      $refinedEvents[$i]['event_guest_count'] = $event->member_count;
      $refinedEvents[$i]['event_view_count'] = $event->view_count;
      $refinedEvents[$i]['event_led_by'] = $this->_helper->commonAPI->getBasicInfoFromItem($event->getOwner());
      $refinedEvents[$i]['can_edit'] = (int) $can_edit;
      $refinedEvents[$i]['can_delete'] = (int) $refinedEvents[$i]['can_edit'];
      $i++;
    }

    $this->_jsonSuccessOutput(array(
        'canCreate' => Engine_Api::_()->authorization()->isAllowed('event', null, 'create'),
        'totalEvents' => $paginator->getTotalItemCount(),
        'events' => $refinedEvents,
        'itemCountPerPage' => $item_per_page
    ));
  }

  public function fetchEventGeneralInfoAction()
  {
    $this->_initEventAction();

    $viewer = Engine_Api::_()->user()->getViewer();
    // Event subject
    $subject = Engine_Api::_()->core()->getSubject();

    // Convert the dates for the viewer
    $startDateObject = new Zend_Date(strtotime($subject->starttime));
    $endDateObject = new Zend_Date(strtotime($subject->endtime));
    if ($viewer && $viewer->getIdentity()) {
      $tz = $viewer->timezone;
      $startDateObject->setTimezone($tz);
      $endDateObject->setTimezone($tz);
    }
    // Get join status of the current user
    $join_status = $this->_helper->eventAPI->getJoinStatusOfUser($subject, $viewer);

    $data = array(
        'id' => $subject->getIdentity(),
        'title' => $subject->getTitle(),
        'description' => $subject->description,
        'event_start' => $this->view->locale()->toDate($startDateObject),
        'event_end' => $this->view->locale()->toDate($endDateObject),
        'rsvp_info' => array(
            'current_rsvp' => $row ? (int) $row->rsvp : 0,
            'attending' => $this->view->locale()->toNumber($subject->getAttendingCount()),
            'maybe_attending' => $this->view->locale()->toNumber($subject->getMaybeCount()),
            'not_attending' => $this->view->locale()->toNumber($subject->getNotAttendingCount()),
            'awaiting_reply' => $this->view->locale()->toNumber($subject->getAwaitingReplyCount())
        ),
        'join_status' => $join_status,
        'permission_info' => array(
            'can_view_event_details' => (int) $subject->authorization()->isAllowed($viewer, 'view'),
            'can_upload_photo' => (int) $subject->authorization()->isAllowed(null, 'photo'),
            'can_invite' => (int) $subject->authorization()->isAllowed($viewer, 'invite')
        )
    );
    $this->_jsonSuccessOutput($data);
  }

  public function updateEventRsvpAction()
  {
    $this->_initEventAction();

    $event = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$event->membership()->isMember($viewer, true)) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_A_MEMBER_OF_EVENT);
    }
    $row = $event->membership()->getRow($viewer);
    if (!$row) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_A_MEMBER_OF_EVENT);
    }
    if ($this->getRequest()->isPost()) {
      $option_id = $this->getRequest()->getParam('option_id');

      $row->rsvp = $option_id;
      $row->save();
    }
    $this->_jsonSuccessOutput(array(
        'message' => $this->_translator->_('Your changes have been saved.')
    ));
  }

  public function fetchEventGuestsAction()
  {
    $this->_initEventAction();

    $event = Engine_Api::_()->core()->getSubject();

    $user = Engine_Api::_()->user()->getViewer();
    if (!$event->authorization()->isAllowed($user, 'view')) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    $waiting = $this->_getParam('waiting', false);
    $search = $this->_getParam('search_text');

    /* @var $paginator Zend_Paginator */
    $paginator = null;
    if ($user->getIdentity() && $event->isOwner($user)) {
      $waitingMembers = Zend_Paginator::factory($event->membership()->getMembersSelect(false));
      if ($waiting) {
        $paginator = $waitingMembers;
      }
    }
    if (!$paginator) {
      $select = $event->membership()->getMembersObjectSelect();
      if ($search) {
        $select->where('displayname LIKE ?', '%' . $search . '%');
      }
      $paginator = Zend_Paginator::factory($select);
    }
    $item_per_page = 10;

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', $item_per_page));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $users = array();
    $i = 0;
    foreach ($paginator as $user) {
      if (!empty($user->resource_id)) {
        $memberInfo = $user;
        $user = $this->view->item('user', $memberInfo->user_id);
      } else {
        $memberInfo = $event->membership()->getMemberInfo($user);
      }

      $users[$i]['user_id'] = $user->user_id;
      $users[$i]['user_name'] = $user->getTitle();
      $users[$i]['user_photo'] = $user->getPhotoUrl('thumb.icon');

      if ($memberInfo->rsvp == 0) {
        $rsvp_text = $this->view->translate('Not Attending');
      } elseif ($memberInfo->rsvp == 1) {
        $rsvp_text = $this->view->translate('Maybe Attending');
      } elseif ($memberInfo->rsvp == 2) {
        $rsvp_text = $this->view->translate('Attending');
      } else {
        $rsvp_text = $this->view->translate('Awaiting Reply');
      }
      $users[$i]['rsvp_text'] = $rsvp_text;
      $users[$i]['join_status'] = $this->_helper->eventAPI->getJoinStatusOfUser($event, $user);
      $i++;
    }

    $data['users'] = $users;
    $data['totalUsers'] = $paginator->getTotalItemCount();
    $data['userCount'] = $paginator->getCurrentItemCount();
    $data['itemCountPerPage'] = $item_per_page;

    $this->_jsonSuccessOutput($data);
  }

  public function acceptEventRequestAction()
  {
    $this->_initEventAction(false);

    // Get user
    if (0 === ($user_id = (int) $this->_getParam('user_id')) ||
            null === (Engine_Api::_()->getItem('user', $user_id))) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::USER_NOT_FOUND);
    }

    try {
      $params = $this->getRequest()->getParams();
      // Transfer request to desired controller
      $this->_customDispatch('approve', 'member', 'event', $params);
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }

    $this->_jsonSuccessOutput(array(
        'message' => $this->_translator->_('Event request approved')
    ));
  }

  public function rejectEventRequestAction()
  {
    $this->_initEventAction(false);

    // Get user
    if (0 === ($user_id = (int) $this->_getParam('user_id')) ||
            null === (Engine_Api::_()->getItem('user', $user_id))) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::USER_NOT_FOUND);
    }

    try {
      $params = $this->getRequest()->getParams();
      // Transfer request to desired controller
      $this->_customDispatch('remove', 'member', 'event', $params);
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }

    $this->_jsonSuccessOutput(array(
        'message' => $this->_translator->_('Event member removed.')
    ));
  }

  public function cancelEventInviteAction()
  {
    $this->_initEventAction(false);

    // Get user
    if (0 === ($user_id = (int) $this->_getParam('user_id')) ||
            null === (Engine_Api::_()->getItem('user', $user_id))) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::USER_NOT_FOUND);
    }

    try {
      $params = $this->getRequest()->getParams();
      // Transfer request to desired controller
      $this->_customDispatch('cancel', 'member', 'event', $params);
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }

    $this->_jsonSuccessOutput(array(
        'message' => $this->_translator->_('Your invite request has been cancelled.')
    ));
  }

  public function cancelEventRequestAction()
  {
    $this->_initEventAction(false);

    try {
      $params = $this->getRequest()->getParams();
      // Transfer request to desired controller
      $this->_customDispatch('cancel', 'member', 'event', $params);
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }

    $this->_jsonSuccessOutput(array(
        'message' => $this->_translator->_('Your invite request has been cancelled.')
    ));
  }

  public function inviteGuestsToEventAction()
  {
    $this->_initEventAction(false);

    $users = explode(',', $this->_getParam('users'));
    $this->getRequest()->setPost('users', $users);

    try {
      $params = $this->getRequest()->getParams();
      // Transfer request to desired controller
      $this->_customDispatch('invite', 'member', 'event', $params);
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }

    $this->_jsonSuccessOutput(array(
        'message' => $this->_translator->_('Members invited')
    ));
  }

  public function joinEventAction()
  {
    $this->_initEventAction();
    $event = Engine_Api::_()->core()->getSubject();
    Engine_Api::_()->core()->clearSubject();
    $this->getRequest()->setParam('format', 'json');

    if (!$this->getRequest()->getPost('rsvp')) {
      $this->getRequest()->setPost('rsvp', 1);
    }

    try {
      if (!$event) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::EVENT_NOT_FOUND);
      } else {
        if (!$event->membership()->isResourceApprovalRequired()) {
          $this->view->action('join', 'member', 'event', $this->getRequest()->getParams());
          $join_status = 2;
          $message = Zend_Registry::get('Zend_Translate')->_('You are now a member of this event.');
        } else {
          $this->view->action('request', 'member', 'event', $this->getRequest()->getParams());
          $join_status = 1;
          $message = Zend_Registry::get('Zend_Translate')->_('You have already sent a membership request.');
        }
      }
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }
    $this->_jsonSuccessOutput(array(
        'message' => $message,
        'join_status' => $join_status
    ));
  }

  public function leaveEventAction()
  {
    $this->_initEventAction();
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    try {
      if ($subject->isOwner($viewer)) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::OWNER_CAN_NOT_LEAVE);
      }
      if ($this->getRequest()->isPost()) {
        $db = $subject->membership()->getReceiver()->getTable()->getAdapter();
        $db->beginTransaction();

        $subject->membership()->removeMember($viewer);
        $db->commit();
      }
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }

    $this->_jsonSuccessOutput(array(
        'message' => $this->_translator->_('Event left')
    ));
  }

  public function acceptEventInviteAction()
  {
    $this->_initEventAction(false);

    if (!$this->getRequest()->getPost('rsvp')) {
      $this->getRequest()->setPost('rsvp', 1);
    }

    try {
      $params = $this->getRequest()->getParams();
      // Transfer request to desired controller
      $this->_customDispatch('accept', 'member', 'event', $params);
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }

    $this->_jsonSuccessOutput(array(
        'message' => $this->_translator->_('Event invite accepted')
    ));
  }

  public function rejectEventInviteAction()
  {
    $this->_initEventAction(false);

    try {
      $params = $this->getRequest()->getParams();
      // Transfer request to desired controller
      $this->_customDispatch('reject', 'member', 'event', $params);
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }

    $this->_jsonSuccessOutput(array(
        'message' => $this->_translator->_('Event invite rejected')
    ));
  }

  public function fetchEventPhotosAction()
  {
    $this->_initEventAction();
    /* @var $event Event_Model_Event */
    $event = Engine_Api::_()->core()->getSubject();

    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$event->authorization()->isAllowed($viewer, 'view')) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    $album = $event->getSingletonAlbum();
    $paginator = $album->getCollectiblesPaginator();
    $canUpload = $event->authorization()->isAllowed(null, 'photo');

    // Set item count per page and current page number
    $item_per_page = 50;
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', $item_per_page));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $photos = array();
    $i = 0;
    foreach ($paginator as $photo) {
      $photos[$i]['photo_id'] = $photo->getIdentity();
      $photos[$i]['thumb_photo_url'] = $photo->getPhotoUrl('thumb.normal');
      $photos[$i]['big_photo_url'] = $photo->getPhotoUrl();
      $photos[$i]['photo_title'] = $photo->getTitle();
      $photos[$i]['photo_description'] = $photo->getDescription();
      $photos[$i]['photo_creation_date'] = strip_tags($this->view->timestamp($photo->creation_date));
      $photos[$i]['photo_posted_by'] = $this->_helper->commonAPI->getBasicInfoFromItem($photo->getOwner());
      // Get photo permissions
//      $photos[$i]['can_edit'] = (int) ($photo->canEdit(Engine_Api::_()->user()->getViewer()));
      $photos[$i]['can_delete'] = (int) ($photo->canEdit(Engine_Api::_()->user()->getViewer()));

      $i++;
    }

    $this->_jsonSuccessOutput(array(
        'canUpload' => (int) $canUpload,
        'itemCountPerPage' => $item_per_page,
        'totalPhoto' => $paginator->getTotalItemCount(),
        'photos' => $photos
    ));
  }

  public function deleteEventPhotoAction()
  {
    if (0 === ($photo_id = (int) $this->_getParam('photo_id')) ||
            null === ($photo = Engine_Api::_()->getItem('event_photo', $photo_id))) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::PHOTO_NOT_FOUND);
    }
    if (!$this->_helper->requireAuth()->setAuthParams($photo, null, 'edit')->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }
    $form = new Event_Form_Photo_Delete();

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
    }

    // Process
    $db = Engine_Api::_()->getDbtable('photos', 'event')->getAdapter();
    $db->beginTransaction();

    try {
      $photo->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput($e->getMessage());
    }
    $this->_jsonSuccessOutput(array(
        'message' => Zend_Registry::get('Zend_Translate')->_('Photo deleted')
    ));
  }

  public function uploadEventPhotoAction()
  {
    $this->_initEventAction();
    $event = Engine_Api::_()->core()->getSubject();

    if (!$this->_helper->requireAuth()->setAuthParams($event, null, 'photo')->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_UPLOAD);
    }

    try {
      $params = $this->getRequest()->getParams();
      $params['format'] = 'json';
      $this->getRequest()->setPost('Filename', $_FILES['Filedata']['name']);
      // Transfer request to desired controller
      $result = json_decode($this->view->action('upload-photo', 'photo', 'event', $params));
    } catch (Exception $e) {
      $this->_jsonErrorOutput($e->getMessage());
    }

    if ($result->status == true) {
      $photo_id = $result->photo_id;
      // Add action and attachments
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $event, 'event_photo_upload', null, array('count' => 1));
      $photo = Engine_Api::_()->getItem("event_photo", $photo_id);

      if ($action instanceof Activity_Model_Action) {
        $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
      }
      $this->_jsonSuccessOutput(array(
          'photo_id' => $photo_id,
          'message' => $this->_translator->_('Photo uploaded')
      ));
    } else {
      $this->_jsonErrorOutput($result->error);
    }
  }

  public function fetchEventDiscussionsAction()
  {
    $this->_initEventAction();

    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('event');
    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    // Get paginator
    $table = Engine_Api::_()->getItemTable('event_topic');
    $select = $table->select()
            ->where('event_id = ?', $subject->getIdentity())
            ->order('sticky DESC')
            ->order('modified_date DESC');
    $paginator = Zend_Paginator::factory($select);

    $item_per_page = 10;

    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', $item_per_page));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $refinedTopics = array();
    $i = 0;
    foreach ($paginator as $topic) {
      /* @var $topic Core_Model_Item_Abstract */
      $refinedTopics[$i]['topic_id'] = $topic->getIdentity();
      $refinedTopics[$i]['topic_title'] = $topic->getTitle();
      $refinedTopics[$i]['topic_description'] = strip_tags($topic->getDescription());
      $refinedTopics[$i]['reply_count'] = $topic->post_count - 1;
      $refinedTopics[$i]['last_poster'] = $this->_helper->commonAPI->getBasicInfoFromItem($topic->getLastPoster());
      $refinedTopics[$i]['last_modifed_time'] = strip_tags($this->view->timestamp(strtotime($topic->modified_date)));
      $i++;
    }
    $this->_jsonSuccessOutput(array(
        'itemCountPerPage' => $item_per_page,
        'totalItem' => $paginator->getTotalItemCount(),
        'topics' => $refinedTopics
    ));
  }

  public function fetchEventDiscussionDetailsAction()
  {
    $topic = Engine_Api::_()->getItem('event_topic', $this->_getParam('topic_id'));

    if (!$topic) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::TOPIC_NOT_FOUND);
    }
    /* @var $topic Event_Model_Topic */
    $viewer = Engine_Api::_()->user()->getViewer();
    $event = $topic->getParentEvent();

    $canEdit = $event->authorization()->isAllowed($viewer, 'edit');
    $canPost = $event->authorization()->isAllowed($viewer, 'comment');
    $canAdminEdit = Engine_Api::_()->authorization()->isAllowed($event, null, 'edit');

    if (!$viewer || !$viewer->getIdentity() || $viewer->getIdentity() != $topic->user_id) {
      $topic->view_count = new Zend_Db_Expr('view_count + 1');
      $topic->save();
    }
    $isWatching = null;
    if ($viewer->getIdentity()) {
      $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'event');
      $isWatching = $topicWatchesTable
              ->select()
              ->from($topicWatchesTable->info('name'), 'watch')
              ->where('resource_id = ?', $event->getIdentity())
              ->where('topic_id = ?', $topic->getIdentity())
              ->where('user_id = ?', $viewer->getIdentity())
              ->limit(1)
              ->query()
              ->fetchColumn(0)
      ;
      if (false === $isWatching) {
        $isWatching = null;
      } else {
        $isWatching = (bool) $isWatching;
      }
    }
    $itemCountPerPage = 25;

    $table = Engine_Api::_()->getDbtable('posts', 'event');
    $select = $table->select()
            ->where('event_id = ?', $event->getIdentity())
            ->where('topic_id = ?', $topic->getIdentity())
            ->order('creation_date ASC');

    $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $paginator->setItemCountPerPage($itemCountPerPage);

    $posts = array();
    $i = 0;
    foreach ($paginator as $post) {
      $posts[$i]['post_id'] = $post->getIdentity();
      $posts[$i]['poster'] = $this->_helper->commonAPI->getBasicInfoFromItem(Engine_Api::_()->getItem('user', $post->user_id));
      $posts[$i]['post_body'] = strip_tags($post->body);
      $posts[$i]['post_time'] = strip_tags($this->view->timestamp(strtotime($post->creation_date)));
      $posts[$i]['can_delete'] = (int) ($canEdit || $canAdminEdit);
      $i++;
    }

    $this->_jsonSuccessOutput(array(
        'itemCountPerPage' => $itemCountPerPage,
        'totalItem' => $paginator->getTotalItemCount(),
        'posts' => $posts,
        'can_post' => (int) ($canPost && !$topic->closed),
        'is_watching' => (int) $isWatching
    ));
  }

  public function postEventDiscussionTopicAction()
  {
    $this->_initEventAction();

    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    $event = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    // Make form
    $form = new Event_Form_Topic_Create();

    // Check method/data
    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
    }

    // Process
    $values = $form->getValues();
    $values['user_id'] = $viewer->getIdentity();
    $values['event_id'] = $event->getIdentity();

    $topicTable = Engine_Api::_()->getDbtable('topics', 'event');
    $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'event');
    $postTable = Engine_Api::_()->getDbtable('posts', 'event');

    $db = $event->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      // Create topic
      $topic = $topicTable->createRow();
      $topic->setFromArray($values);
      $topic->save();

      // Create post
      $values['topic_id'] = $topic->topic_id;

      $post = $postTable->createRow();
      $post->setFromArray($values);
      $post->save();

      // Create topic watch
      $topicWatchesTable->insert(array(
          'resource_id' => $event->getIdentity(),
          'topic_id' => $topic->getIdentity(),
          'user_id' => $viewer->getIdentity(),
          'watch' => (bool) $values['watch'],
      ));

      // Add activity
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $activityApi->addActivity($viewer, $topic, 'event_topic_create');
      if ($action) {
        $action->attach($topic);
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput($e->getMessage());
    }
    $this->_jsonSuccessOutput(array(
        'message' => $this->_translator->_('Topic created'),
        'topic_id' => $topic->getIdentity()
    ));
  }

  public function postEventDiscussionReplyAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }
    $topic = Engine_Api::_()->getItem('event_topic', $this->_getParam('topic_id'));
    if (!$topic) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::TOPIC_NOT_FOUND);
    }
    Engine_Api::_()->core()->setSubject($topic);
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    $event = $topic->getParentEvent();

    if ($topic->closed) {
      $this->_jsonErrorOutput('This has been closed for posting.');
    }

    // Make form
    $form = new Event_Form_Post_Create();

    // Check method/data
    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
    }

    // Process
    $viewer = Engine_Api::_()->user()->getViewer();
    $topicOwner = $topic->getOwner();
    $isOwnTopic = $viewer->isSelf($topicOwner);

    $postTable = Engine_Api::_()->getDbtable('posts', 'event');
    $topicWatchesTable = Engine_Api::_()->getDbtable('topicWatches', 'event');
    $userTable = Engine_Api::_()->getItemTable('user');
    $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
    $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

    $values = $form->getValues();
    $values['user_id'] = $viewer->getIdentity();
    $values['event_id'] = $event->getIdentity();
    $values['topic_id'] = $topic->getIdentity();

    $watch = (bool) $values['watch'];
    $isWatching = $topicWatchesTable
            ->select()
            ->from($topicWatchesTable->info('name'), 'watch')
            ->where('resource_id = ?', $event->getIdentity())
            ->where('topic_id = ?', $topic->getIdentity())
            ->where('user_id = ?', $viewer->getIdentity())
            ->limit(1)
            ->query()
            ->fetchColumn(0)
    ;

    $db = $event->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      // Create post
      $post = $postTable->createRow();
      $post->setFromArray($values);
      $post->save();

      // Watch
      if (false === $isWatching) {
        $topicWatchesTable->insert(array(
            'resource_id' => $event->getIdentity(),
            'topic_id' => $topic->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'watch' => (bool) $watch,
        ));
      } else if ($watch != $isWatching) {
        $topicWatchesTable->update(array(
            'watch' => (bool) $watch,
                ), array(
            'resource_id = ?' => $event->getIdentity(),
            'topic_id = ?' => $topic->getIdentity(),
            'user_id = ?' => $viewer->getIdentity(),
        ));
      }

      // Activity
      $action = $activityApi->addActivity($viewer, $topic, 'event_topic_reply');
      if ($action) {
        $action->attach($post, Activity_Model_Action::ATTACH_DESCRIPTION);
      }

      // Notifications
      $notifyUserIds = $topicWatchesTable->select()
              ->from($topicWatchesTable->info('name'), 'user_id')
              ->where('resource_id = ?', $event->getIdentity())
              ->where('topic_id = ?', $topic->getIdentity())
              ->where('watch = ?', 1)
              ->query()
              ->fetchAll(Zend_Db::FETCH_COLUMN)
      ;

      foreach ($userTable->find($notifyUserIds) as $notifyUser) {
        // Don't notify self
        if ($notifyUser->isSelf($viewer)) {
          continue;
        }
        if ($notifyUser->isSelf($topicOwner)) {
          $type = 'event_discussion_response';
        } else {
          $type = 'event_discussion_reply';
        }
        $notifyApi->addNotification($notifyUser, $viewer, $topic, $type, array(
            'message' => $this->view->BBCode($post->body),
        ));
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput($e->getMessage());
    }

    $this->_jsonSuccessOutput(array(
        'message' => $this->_translator->_('Post created'),
        'post_id' => $post->getIdentity()
    ));
  }

  public function deleteEventTopicThreadAction()
  {
    if (0 !== ($post_id = (int) $this->_getParam('post_id')) &&
            null !== ($post = Engine_Api::_()->getItem('event_post', $post_id))) {
      Engine_Api::_()->core()->setSubject($post);
    } else {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ITEM_NOT_FOUND);
    }

    $post = Engine_Api::_()->core()->getSubject('event_post');
    $event = $post->getParent('event');
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$event->isOwner($viewer) && !$post->isOwner($viewer)) {
      if (!$this->_helper->requireAuth()->setAuthParams($event, null, 'edit')->isValid()) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
      }
    }

    $this->view->form = $form = new Event_Form_Post_Delete();

    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_DATA);
    }

    // Process
    $table = $post->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $post->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput($e->getMessage());
    }
    $this->_jsonSuccessOutput(array(
        'message' => $this->_translator->_('Post deleted'),
        'post_id' => $post->getIdentity()
    ));
  }

  public function fetchBadgeNumberAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    // Get friend requests count
    $requestCountsByType = Engine_Api::_()->getDbtable('notifications', 'activity')->getRequestCountsByType($viewer);
    $friendRequestCount = array_key_exists('friend_request', $requestCountsByType) ? $requestCountsByType['friend_request']['count'] : 0;

    // Get number of unread messages
    $unread = Engine_Api::_()->messages()->getUnreadMessageCount($viewer);

    $this->_jsonSuccessOutput(array(
        'numberOfFriendRequests' => $friendRequestCount,
        'numberOfUnreadInboxMessages' => (int) $unread,
        'numberOfUnreadNotifications' => (int) Engine_Api::_()->getDbtable('notifications', 'activity')->hasNotifications($viewer),
    ));
  }

  public function fetchAdAction()
  {
    // Get campaign
    if (!($campaign = Engine_Api::_()->getItemTable('core_adcampaign')->fetchRow(array("`name` LIKE ?" => '%mobile%')))) {
      $this->_setNoRender();
    }
    echo $this->view->content()->renderWidget('mgslapi.ad-campaign', array('adcampaign_id' => $campaign->adcampaign_id));
  }

  public function deleteUserAccountAction()
  {
    $user = Engine_Api::_()->user()->getViewer();
    if (!$this->_helper->requireAuth()->setAuthParams($user, null, 'delete')->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    if (1 === count(Engine_Api::_()->user()->getSuperAdmins()) && 1 === $user->level_id) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    if (!$this->getRequest()->isPost()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::INVALID_REQUEST_METHOD);
    }

    // Process
    $db = Engine_Api::_()->getDbtable('users', 'user')->getAdapter();
    $db->beginTransaction();

    try {
      $user->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->_jsonErrorOutput($e->getMessage());
    }

    // Unset viewer, remove auth, clear session
    Engine_Api::_()->user()->setViewer(null);
    Zend_Auth::getInstance()->getStorage()->clear();
    Zend_Session::destroy();

    $this->_jsonSuccessOutput(array(
        'message' => 'Account deleted'
    ));
  }

  public function fetchPaypalParamsAction()
  {
    $guid = $this->_getParam('subject');
    $subject = Engine_Api::_()->getItemByGuid($guid);

    if (!$guid || !$subject) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ITEM_NOT_FOUND);
    }

    $package = null;
    if ($this->_hasParam('package_id')) {
      $package = Engine_Api::_()->resume()->getPackage($this->_getParam('package_id'));
    }
    if (!($package instanceof Resume_Model_Package)) {
      $package = $subject->getPackage();
    }

    $form = new Epayment_Form_Checkout(array('item' => $subject, 'package' => $package));
    $view = Zend_Registry::get('Zend_View');
    $item_name = $view->translate('%s resume with %s package', $form->getItem()->getTitle(), $form->getPackage()->getTitle());

    $this->_jsonSuccessOutput(array(
        'rm' => $form->rm->getValue(),
        'cmd' => $form->cmd->getValue(),
        'business' => $form->business->getValue(),
        'currency_code' => $form->currency_code->getValue(),
        'notify_url' => $form->notify_url->getValue(),
        'custom' => $form->custom->getValue(),
        'item_number' => $form->item_number->getValue(),
        'item_name' => $item_name,
        'amount' => $form->amount->getValue()
    ));
  }

  protected function _setNoRender()
  {
    if ($this->_deviceType == Mgslapi_Model_DbTable_Devices::IOS_DEVICE_TYPE) {
      $this->_redirectCustom(array('route' => 'mgslapi_ios', 'action' => 'no-display'));
    } else {
      $this->_redirectCustom(array('route' => 'mgslapi_android', 'action' => 'no-display'));
    }
  }

  public function noDisplayAction()
  {
    
  }

  protected function _initEventAction($setSubject = true)
  {
    $subject = Engine_Api::_()->getItem('event', $this->_getParam('event_id'));

    if (!$subject) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::EVENT_NOT_FOUND);
    }
    if ($setSubject) {
      Engine_Api::_()->core()->setSubject($subject);
    }
  }

  protected function _initResumeProfileAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->getItem('resume', $this->_getParam('resume_id'));

    if (!($subject instanceof Resume_Model_Resume)) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ITEM_NOT_FOUND);
    }
    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }
    Engine_Api::_()->core()->setSubject($subject);

    $error = '';

    if (!$subject->isPublished()) {
      $error = Mgslapi_Controller_Action_Helper_Error::RESUME_DRAFT;
    } else if (!$subject->isApprovedStatus()) {
      $error = Mgslapi_Controller_Action_Helper_Error::RESUME_NOT_APPROVED;
    } else if ($subject->isExpired()) {
      $error = Mgslapi_Controller_Action_Helper_Error::RESUME_EXPIRED;
    }

    // hack to work around SE v4.1.8 User::isAdmin bug "Registry is already initialized"
    try {
      $is_admin = $viewer->isAdmin();
    } catch (Exception $ex) {
      $is_admin = Engine_Api::_()->getApi('core', 'authorization')->isAllowed('admin', null, 'view');
    }

    if ($error && !$is_admin && !$viewer->isSelf($subject->getOwner())) {
      $this->_jsonErrorOutput($error);
    }
  }

  protected function _initResumeAction()
  {
    if (!$this->_helper->requireAuth()->setAuthParams('resume', null, 'view')->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }
    if (!Engine_Api::_()->core()->hasSubject()) {
      if (0 !== ($resume_id = (int) $this->_getParam('resume_id')) &&
              null !== ($resume = Engine_Api::_()->getItem('resume', $resume_id))) {
        Engine_Api::_()->core()->setSubject($resume);
      } else if (0 !== ($user_id = (int) $this->_getParam('user_id')) &&
              null !== ($user = Engine_Api::_()->getItem('user', $user_id))) {
        Engine_Api::_()->core()->setSubject($user);
      }
    }
  }

  /**
   * Api Group: Push notification API
   */
  protected function _initOpenfireAccount($user_id, $password)
  {

    /* @var $api Mgslapi_Api_Openfire */
    $api = Engine_Api::_()->getApi('openfire', 'mgslapi');
    $api->addOrUpdateAccount($user_id, $password);

    $this->_setParam('user_type', 'friends');
    $friends = $this->fetchMemberListAction(true);

    $xmppDomain = $api->getXMPPDomain();
    $multi_jid = '';
    foreach ($friends['users'] as $user) {
      $multi_jid .= $user['user_id'] . '@' . $xmppDomain . ',';
    }
    $multi_jid = preg_replace('/,$/', '', $multi_jid);
    $api->addRosterMulti($user_id, $multi_jid);
  }

  protected function _initCircleAction($check_view_details = false)
  {
    //CHECK VIEW PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'view')->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    //GET PAGE ID AND PAGE URL
    $page_url = $this->_getParam('page_url', null);
    $page_id = $this->_getParam('page_id', null);

    if ($page_url) {
      $page_id = Engine_Api::_()->sitepage()->getPageId($page_url);
    }
    if ($page_id) {
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      if ($sitepage) {
        Engine_Api::_()->core()->setSubject($sitepage);
      }
    }

    //FOR UPDATE EXPIRATION
    if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.task.updateexpiredpages') + 900) <= time()) {
      Engine_Api::_()->sitepage()->updateExpiredPages();
    }

    if ($check_view_details) {
      if (!Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view')) {
        $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
      }
    }
  }

  protected function _initAlbumAction()
  {
    if (!$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    if (0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
            null !== ($photo = Engine_Api::_()->getItem('album_photo', $photo_id))) {
      Engine_Api::_()->core()->setSubject($photo);
    } else if (0 !== ($album_id = (int) $this->_getParam('album_id')) &&
            null !== ($album = Engine_Api::_()->getItem('album', $album_id))) {
      Engine_Api::_()->core()->setSubject($album);
    }

    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }
  }

  protected function _initMessageAction()
  {
    if (!$this->_helper->requireUser()->isValid()) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }
    if (!$this->_helper->
                    requireAuth()->setAuthParams('messages', null, 'create')) {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }
  }

  protected function _initCommonItemAction()
  {
    $type = $this->_getParam('type');
    $identity = $this->_getParam('id');
    if ($type && $identity) {
      try {
        $item = Engine_Api::_()->getItem($type, $identity);
      } catch (Exception $e) {
        $this->_jsonErrorOutput($e->getMessage());
      }
      if ($item instanceof Core_Model_Item_Abstract &&
              (method_exists($item, 'comments') || method_exists($item, 'likes'))) {
        if (!Engine_Api::_()->core()->hasSubject()) {
          Engine_Api::_()->core()->setSubject($item);
        }
      }
    } else {
      $this->_jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::ITEM_NOT_FOUND);
    }
  }

  /**
   * This function is used for dispatching a request to an action of another controller
   * 
   * @param  string $action 
   * @param  string $controller 
   * @param  string $module Defaults to default module
   * @param  array $params 
   */
  protected function _customDispatch($action, $controller, $module = null, array $params = array())
  {
    $params['format'] = 'json';
    $front = Zend_Controller_Front::getInstance();
    $request = clone $front->getRequest();
    $response = clone $front->getResponse();
    $dispatcher = clone $front->getDispatcher();

    $request->setParams($params)
            ->setModuleName($module)
            ->setControllerName($controller)
            ->setActionName($action)
            ->setDispatched(true);

    $this->_helper->redirector->setExit(false);

    $dispatcher->dispatch($request, $response);
  }

  protected function _jsonSuccessOutput($data = array())
  {
    header('Content-Type: application/json');
    $response = array();

    $response['status'] = 'success';

    $response['hash'] = md5(serialize($data));
    $response['data'] = $data;
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
  }

  /**
   * Public implementation of json error output 
   * @param string $errorMessage
   */
  public function jsonErrorOutput($errorMessage = 'An error occurred.')
  {
    $this->_jsonErrorOutput($errorMessage);
  }

  protected function _jsonErrorOutput($errorMessage = 'An error occurred.')
  {
    header('Content-Type: application/json');

    $errorMessage = $this->_translator->_($errorMessage);

    if (!is_array($errorMessage)) {
      $errorMessage = array($errorMessage);
    }

    $response = array();

    $response['status'] = 'error';
    $response['errorMessages'] = $errorMessage;
    echo json_encode($response);
    exit;
  }

  protected function _writeLog($message)
  {
    $this->_logger->log($message, Zend_Log::INFO);
  }

}
