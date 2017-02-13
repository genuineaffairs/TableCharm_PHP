<?php

class Mgslapi_Controller_Action_Helper_FeedAPI extends Zend_Controller_Action_Helper_Abstract {

  function getRefinedComments($action, $viewAllComments, $viewer) {
    $comments = $action->getComments($viewAllComments);
    $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');

    $refinedComments = array();

    $i = 0;
    foreach ($comments as $comment) {
      $author = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);

//      $refinedComments[$i]['author_link'] = $author->getHref();
      $refinedComments[$i]['comment_id'] = $comment->getIdentity();
      $refinedComments[$i]['author_id'] = $author->getIdentity();
      $refinedComments[$i]['author_name'] = $author->getTitle();
      $refinedComments[$i]['author_photo'] = $author->getPhotoUrl('thumb.icon');
      $refinedComments[$i]['comment_body'] = $comment->body;
      $refinedComments[$i]['comment_date'] = $comment->creation_date;

      $allow_delete = 0;
      // Allow delete field
      if ($viewer->getIdentity() &&
              (('user' == $action->subject_type && $viewer->getIdentity() == $action->subject_id) ||
              ("user" == $comment->poster_type && $viewer->getIdentity() == $comment->poster_id) ||
              ("user" !== $comment->poster_type && Engine_Api::_()->getItemByGuid($comment->poster_type . "_" . $comment->poster_id)->isOwner($viewer)) ||
              $activity_moderate )) {
        $allow_delete = 1;
      }
      $refinedComments[$i]['allow_delete'] = $allow_delete;

      $refinedComments[$i]['like_info'] = array();
      $refinedComments[$i]['like_info']['comment_like_count'] = $this->getActionController()->view->locale()->toNumber($comment->likes()->getLikeCount());
      $refinedComments[$i]['like_info']['is_comment_liked'] = (int) $comment->likes()->isLike($viewer);
      $likers = $comment->likes()->getAllLikesUsers();
      if (count($likers) > 0) {
        $liker_count = 0;
        foreach ($likers as $liker) {
          $refinedComments[$i]['like_info']['likers'][$liker_count]['liker_id'] = $liker->getIdentity();
          $refinedComments[$i]['like_info']['likers'][$liker_count]['liker_type'] = $liker->getType();
          $refinedComments[$i]['like_info']['likers'][$liker_count]['liker_name'] = $liker->getTitle();
          $refinedComments[$i]['like_info']['likers'][$liker_count]['liker_photo'] = $liker->getPhotoUrl('thumb.icon');
          $liker_count++;
        }
      }
      $i++;
    }
    return $refinedComments;
  }

  function getLikeInfo($action, $viewer) {
    $likeInfo = array();
    $likeInfo['like_count'] = $action->likes()->getLikeCount();
    $likeInfo['is_action_liked'] = (int) $action->likes()->isLike($viewer);
    $likeInfo['likers'] = array();
    $likeInfo['likeable'] = Engine_Api::_()->authorization()->isAllowed($action->getCommentObject(), null, 'comment');
    $likers = $action->likes()->getAllLikesUsers();
    if (count($likers) > 0) {
      $liker_count = 0;
      foreach ($likers as $liker) {
        $likeInfo['likers'][$liker_count]['liker_id'] = $liker->getIdentity();
        $likeInfo['likers'][$liker_count]['liker_type'] = $liker->getType();
        $likeInfo['likers'][$liker_count]['liker_name'] = $liker->getTitle();
        $likeInfo['likers'][$liker_count]['liker_photo'] = $liker->getPhotoUrl('thumb.icon');
        $liker_count++;
      }
    }
    return $likeInfo;
  }

  function getTopMessage($action) {
    /* Start Working group feed. */
    $groupedFeeds = null;
    if ($action->type == 'friends') {
      $subject_guid = $action->getSubject()->getGuid();
      $total_guid = $action->type . '_' . $subject_guid;
    } elseif ($action->type == 'tagged') {
      foreach ($action->getAttachments() as $attachment) {
        $object_guid = $attachment->item->getGuid();
        $Subject_guid = $action->getSubject()->getGuid();
        $total_guid = $action->type . '_' . $object_guid . '_' . $Subject_guid;
      }
    } else {
      $subject_guid = $action->getObject()->getGuid();
      $total_guid = $action->type . '_' . $subject_guid;
    }

    if (!isset($grouped_actions[$total_guid]) && isset($this->groupedFeeds[$total_guid])) {
      $groupedFeeds = $this->groupedFeeds[$total_guid];
    }
    /* End Working group feed. */

    return strip_tags($this->getActionController()->view->getContent($action, false, $groupedFeeds));
  }

  function checkPostFeedAuth() {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $subject = null;
    if ($request->getParam('user_id')) {
      $subject = Engine_Api::_()->getItem('user', (int) $request->getParam('user_id'));
      if (!$subject->user_id) {
        $this->getActionController()->jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::USER_NOT_FOUND);
      }
    } elseif ($request->getParam('subject')) {
      $subject = Engine_Api::_()->getItemByGuid($request->getParam('subject'));
    } elseif (Engine_Api::_()->core()->hasSubject()) {
      $subject = Engine_Api::_()->core()->getSubject();
    }
    $viewer = Engine_Api::_()->user()->getViewer();

    // Check if viewer is allowed to post statuses on subject's wall or not
    $enableComposer = false;
    if ($viewer->getIdentity() && !$request->getParam('action_id')) {
      if (Engine_Api::_()->seaocore()->isLessThan420ActivityModule()) {
        if (!$subject || $subject->authorization()->isAllowed($viewer, 'comment')) {
          $enableComposer = true;
        }
      } else {
        if (!$subject || ($subject instanceof Core_Model_Item_Abstract && $subject->isSelf($viewer))) {
          if (Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'user', 'status')) {
            $enableComposer = true;
          }
        } else if ($subject) {
          if (Engine_Api::_()->authorization()->isAllowed($subject, $viewer, 'comment')) {
            $enableComposer = true;
          }
        }
      }
      if (!empty($subject)) {
        // Get subject
        $parentSubject = $subject;
        if ($subject->getType() == 'siteevent_event') {
          $parentSubject = Engine_Api::_()->getItem($subject->parent_type, $subject->parent_id);
          if (!Engine_Api::_()->authorization()->isAllowed($subject, $viewer, "post")) {
            $enableComposer = false;
          }
        } else if ($subject->getType() == 'sitepage_page' || $subject->getType() == 'sitepageevent_event' || $parentSubject->getType() == 'sitepage_page') {
          $pageSubject = $parentSubject;
          if ($subject->getType() == 'sitepageevent_event') {
            $pageSubject = Engine_Api::_()->getItem('sitepage_page', $subject->page_id);
          }
          $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($pageSubject, 'comment');
          if (!empty($isManageAdmin)) {
            $enableComposer = true;
            if (!$pageSubject->all_post && !Engine_Api::_()->sitepage()->isPageOwner($pageSubject)) {
              $enableComposer = false;
            }
          }
          if ($enableComposer) {
            $actionSettingsTable = Engine_Api::_()->getDbtable('actionSettings', 'activity');
            $activityFeedType = null;
            if (Engine_Api::_()->sitepage()->isPageOwner($pageSubject) && Engine_Api::_()->sitepage()->isFeedTypePageEnable()) {
              $activityFeedType = 'sitepage_post_self';
            } else {
              $activityFeedType = 'sitepage_post';
            }
            if (!$actionSettingsTable->checkEnabledAction($viewer, $activityFeedType)) {
              $enableComposer = false;
            }
          }
        } else if ($subject->getType() == 'sitebusiness_business' || $subject->getType() == 'sitebusinessevent_event' || $parentSubject->getType() == 'sitebusiness_business') {
          $businessSubject = $parentSubject;
          if ($subject->getType() == 'sitebusinessevent_event') {
            $businessSubject = Engine_Api::_()->getItem('sitebusiness_business', $subject->business_id);
          }
          $isManageAdmin = Engine_Api::_()->sitebusiness()->isManageAdmin($businessSubject, 'comment');
          if (!empty($isManageAdmin)) {
            $enableComposer = true;
            if (!$businessSubject->all_post && !Engine_Api::_()->sitebusiness()->isBusinessOwner($businessSubject)) {
              $enableComposer = false;
            }
          }
          if ($enableComposer) {
            $actionSettingsTable = Engine_Api::_()->getDbtable('actionSettings', 'activity');
            $activityFeedType = null;

            if (Engine_Api::_()->sitebusiness()->isBusinessOwner($businessSubject) && Engine_Api::_()->sitebusiness()->isFeedTypeBusinessEnable()) {
              $activityFeedType = 'sitebusiness_post_self';
            } elseif ($businessSubject->all_post || Engine_Api::_()->sitebusiness()->isBusinessOwner($businessSubject)) {
              $activityFeedType = 'sitebusiness_post';
            }
            if (!empty($activityFeedType) && !$actionSettingsTable->checkEnabledAction($viewer, $activityFeedType)) {
              $enableComposer = false;
            }
          }
        } elseif ($subject->getType() == 'sitegroup_group' || $subject->getType() == 'sitegroupevent_event' || $parentSubject->getType() == 'sitebusiness_business') {
          $groupSubject = $parentSubject;
          if ($subject->getType() == 'sitegroupevent_event') {
            $groupSubject = Engine_Api::_()->getItem('sitegroup_group', $subject->group_id);
          }
          $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($groupSubject, 'comment');
          if (!empty($isManageAdmin)) {
            $enableComposer = true;
            if (!$groupSubject->all_post && !Engine_Api::_()->sitegroup()->isGroupOwner($groupSubject)) {
              $enableComposer = false;
            }
          }
          if ($enableComposer) {
            $actionSettingsTable = Engine_Api::_()->getDbtable('actionSettings', 'activity');
            $activityFeedType = null;
            if (Engine_Api::_()->sitegroup()->isGroupOwner($groupSubject) && Engine_Api::_()->sitegroup()->isFeedTypeGroupEnable()) {
              $activityFeedType = 'sitegroup_post_self';
            } else {
              $activityFeedType = 'sitegroup_post';
            }
            if (!$actionSettingsTable->checkEnabledAction($viewer, $activityFeedType)) {
              $enableComposer = false;
            }
          }
        } elseif ($subject->getType() == 'sitestore_store' || $subject->getType() == 'sitestoreevent_event' || $parentSubject->getType() == 'sitestore_store') {
          $storeSubject = $parentSubject;
          if ($subject->getType() == 'sitestoreevent_event') {
            $storeSubject = Engine_Api::_()->getItem('sitestore_store', $subject->store_id);
          }
          $isManageAdmin = Engine_Api::_()->sitestore()->isManageAdmin($storeSubject, 'comment');
          if (!empty($isManageAdmin)) {
            $enableComposer = true;
            if (!$storeSubject->all_post && !Engine_Api::_()->sitestore()->isStoreOwner($storeSubject)) {
              $enableComposer = false;
            }
          }
          if ($enableComposer) {
            $actionSettingsTable = Engine_Api::_()->getDbtable('actionSettings', 'activity');
            $activityFeedType = null;
            if (Engine_Api::_()->sitestore()->isStoreOwner($storeSubject) && Engine_Api::_()->sitestore()->isFeedTypeStoreEnable()) {
              $activityFeedType = 'sitestore_post_self';
            } else {
              $activityFeedType = 'sitestore_post';
            }
            if (!$actionSettingsTable->checkEnabledAction($viewer, $activityFeedType)) {
              $enableComposer = false;
            }
          }
        }
      }
    }
    return $enableComposer;
  }

}
