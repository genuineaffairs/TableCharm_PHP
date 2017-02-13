<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Likes.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Communityad_Model_DbTable_Likes extends Engine_Db_Table {

  protected $_name = 'communityad_likes';
  protected $_rowClass = 'Communityad_Model_Like';

  public function isFriendLiked($adID, $search = null) {
    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $membership_table = Engine_Api::_()->getDbtable('membership', 'user');
    $member_name = $membership_table->info('name');
    $likeTableName = $this->info('name');
    $user_table = Engine_Api::_()->getItemtable('user');
    $user_name = $user_table->info('name');
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($likeTableName, array('poster_id'))
            ->joinInner($member_name, "$member_name . user_id = $likeTableName . poster_id", null)
            ->joinInner($user_name, "$user_name . user_id = $member_name . user_id", null)
            ->where($member_name . '.resource_id = ?', $user_id)
            ->where($member_name . '.active = ?', 1)
            ->where($likeTableName . '.ad_id =?', $adID)
            ->where($user_name . '.displayname LIKE ?', '%' . $search . '%')
            ->order('RAND()');
    $fetchRecord = $select->query()->fetchAll();
    if (!empty($fetchRecord)) {
      return $fetchRecord;
    } else {
      return;
    }
  }

  public function isExist($adId) {
    $poster_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $isLikeName = $this->info('name');
    $isLikeSelect = $this->select()
            ->from($isLikeName, array('like_id'))
            ->where('poster_id =?', $poster_id)
            ->where('ad_id =?', $adId)
            ->limit(1);
    $fetchLikeResult = $isLikeSelect->query()->fetchAll();
    if (!empty($fetchLikeResult)) {
      return $fetchLikeResult[0]['like_id'];
    } else {
      return 0;
    }
  }

  public function getLikeCount( $ad_id )
  {
    $poster_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $isLikeName = $this->info('name');
    $isLikeSelect = $this->select()
			->from($isLikeName, array('COUNT(ad_id) AS ad_count'))
			->where('ad_id =?', $ad_id);
    $fetchLikeResult = $isLikeSelect->query()->fetchAll();
    if (!empty($fetchLikeResult)) {
      return $fetchLikeResult[0]['ad_count'];
    } else {
      return 0;
    }
  }

}