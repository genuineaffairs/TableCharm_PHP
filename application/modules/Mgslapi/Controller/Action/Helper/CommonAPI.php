<?php

class Mgslapi_Controller_Action_Helper_CommonAPI extends Zend_Controller_Action_Helper_Abstract {

  public function getCommentInfo($item) {
    if (!($item instanceof Core_Model_Item_Abstract) || !$item->getIdentity() || !method_exists($item, 'comments')) {
      return array();
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    // Get comment info
    $commentSelect = $item->comments()->getCommentSelect();
    $commentSelect->order('comment_id DESC');
    $comments = Zend_Paginator::factory($commentSelect);
    $comments->setCurrentPageNumber(1);
    $comments->setItemCountPerPage(50);

    // Get comment permissions
    $canComment = $item->authorization()->isAllowed($viewer, 'comment');
    $canDelete = $item->authorization()->isAllowed($viewer, 'edit');

    // Get comments
    $commentObjects = array();
    $i = 0;
    foreach ($comments as $comment) {
      $commentObjects[$i]['comment_is_liked'] = (int) $comment->likes()->isLike($viewer);
      // Get poster info
      $poster = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);
      $commentObjects[$i]['author_id'] = $poster->getIdentity();
      $commentObjects[$i]['author_photo'] = $poster->getPhotoUrl('thumb.icon');
      $commentObjects[$i]['author_name'] = $poster->getTitle();
      $commentObjects[$i]['author_type'] = $poster->getType();
      $commentObjects[$i]['comment_id'] = $comment->getIdentity();
      $commentObjects[$i]['comment_body'] = $comment->body;
      $commentObjects[$i]['comment_date'] = strip_tags($this->getActionController()->view->timestamp($comment->creation_date));
      $commentObjects[$i]['comment_can_delete'] = (int) ($canDelete || $poster->isSelf($viewer));

      $commentObjects[$i]['like_info'] = $this->getLikeInfo($comment, $viewer);
      $i++;
    }

    return array(
        'can_comment' => (int) $canComment,
        'comments' => $commentObjects,
        'comment_count' => $comments->getTotalItemCount()
    );
  }

  function getLikeInfo($item, $viewer) {
    // Get permission info
    $canComment = $item->authorization()->isAllowed($viewer, 'comment');

    $likeInfo = array();
    $likeInfo['like_count'] = $item->likes()->getLikeCount();
    $likeInfo['is_liked'] = (int) $item->likes()->isLike($viewer);
    $likeInfo['likeable'] = (int) $canComment;
    $likeInfo['likers'] = array();
    $likers = $item->likes()->getAllLikesUsers();
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

  function getBasicInfoFromItem($item) {
    $info = array();

    if ($item instanceof Core_Model_Item_Abstract) {
      $info['item_id'] = $item->getIdentity();
      $info['item_type'] = $item->getType();
      $info['name'] = $item->getTitle();
      $info['thumb_photo'] = $item->getPhotoUrl('thumb.icon');
      $info['detail_photo'] = $item->getPhotoUrl();
    }
    return $info;
  }

}
