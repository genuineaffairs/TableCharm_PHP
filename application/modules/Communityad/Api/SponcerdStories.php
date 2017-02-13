<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: SponsoredStories.php  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Communityad_Api_SponcerdStories extends Core_Api_Abstract {

  protected $_GetFriendArray;

  /**
   * Get the "Friend String" of the loggden user or pass user.
   *
   * @param $viewer_id: For this id, function will find out the friend. 
   * @return null
   */
  public function getMembership($viewer_id = null) {
	if (empty($viewer_id)) {
	  $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	}

	$friendStr = $friendArray = null;

	//FETCH FRIEND ID FROM DATABASE.
	$table = Engine_Api::_()->getDbtable('membership', 'user');
	$tableName = $table->info('name');

	$userTable = Engine_Api::_()->getItemTable('user');
	$userTableName = $userTable->info('name');

	$select = $table->select()
					->setIntegrityCheck(false)
					->from($tableName, array('resource_id'))
					->joinLeft($userTableName, "$userTableName.user_id = $tableName.user_id", null)
					->where($tableName . '.user_id = ?', $viewer_id)
					->where($tableName . '.active =?', 1);
	$fetch_record = $select->query()->fetchAll();
	foreach ($fetch_record as $friend_id) {
	  $friendArray[] = $friend_id['resource_id'];
	}
	if (!empty($friendArray) && is_array($friendArray)) {
	  $friendStr = implode(',', $friendArray);
	}
	$friendStr = $this->getTrimStr($friendStr);
	$this->_GetFriendArray = $friendStr;
	return;
  }

  /**
   * Sponsored story, which like by viewer friend.
   *
   * @param $resultArray: Array of the info like "limit" & "Sponsored Story Type".
   * @param $notShowIdsStr: String of the user which should not be return from the SQL.
   * @return Array
   */
  public function getContent($resultArray, $notShowIdsStr) {

	$limit = $resultArray['limit'];
	$storyType = $resultArray['storyType'];
  
	$notShowIdsStr = $this->getTrimStr($notShowIdsStr);
	$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

	$table = Engine_Api::_()->getItemTable('userads');
	$tableName = $table->info('name');

	$likeTable = Engine_Api::_()->getDbtable('likes', 'core');
	$likeTableName = $likeTable->info('name');

	$totalAd = $table->fetchAll(array('ad_type' => 'sponsored_stories'))->count();
	if (empty($totalAd)) {
	  return;
	}

	$select = $table->select()
	    ->setIntegrityCheck(false)
	    ->from($tableName, array('userad_id', 'resource_type', 'resource_id', 'story_type', 'owner_id'))
          
//	    ->joinInner($likeTableName, '' . $likeTableName . '.resource_type = ' . $tableName . '.resource_type AND ' . $likeTableName . '.resource_id = ' . $tableName . '.resource_id', array('like_id', 'poster_id'))
//	     ->where($likeTableName . ".poster_id != ?", $viewer_id)
//	     ->where($likeTableName . ".poster_id IN (" . $this->_GetFriendArray . ")")
          
	    ->where($tableName . '.story_type = ?', $storyType)
	    ->where($tableName . '.ad_type = ?', 'sponsored_stories')
	    ->where($tableName . ".userad_id NOT IN (" . $notShowIdsStr . ")")
	    ->order('RAND()')
	    ->limit($limit);

	$select = Engine_Api::_()->communityad()->getCommanConditionsForSQL($select, 'sponsored_stories', $tableName, $totalAd);
	$select = Engine_Api::_()->communityad()->getTargettingSQL($select);

	$result = $select->query()->fetchAll();
  if( !empty($result[0]['resource_type']) ) {
    if( strstr($result[0]['resource_type'], "sitereview") ) {
      $tempResourceType = "sitereview_listing";
      $tempResourceId = $result[0]['resource_id'];
    }else {
      $tempResourceType = $result[0]['resource_type'];
      $tempResourceId = $result[0]['resource_id'];
    }

    $likeSelect = $likeTable->select()
            ->setIntegrityCheck(false)
            ->from($likeTableName, array('like_id', 'poster_id'))
            ->where($likeTableName . ".resource_type =?", $tempResourceType)
            ->where($likeTableName . ".resource_id =?", $tempResourceId)
            ->where($likeTableName . ".poster_id != ?", $viewer_id)
            ->where($likeTableName . ".poster_id IN (" . $this->_GetFriendArray . ")");

    $tempFetchData = $likeSelect->query()->fetchAll();
    if( empty($tempFetchData) ) {
      return;
    }
    $result[0]['like_id'] = $tempFetchData[0]['like_id'];
    $result[0]['poster_id'] = $tempFetchData[0]['poster_id'];
  }

	return $result;
  }

  /**
   * Sponsored story, which like by viewer friend.
   *
   * @param $resource_type: Type of the content.
   * @param $resource_id: Id of the content.
   * @return Bool
   */
  public function isUserLike($resource_type, $resource_id) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $resource_obj = Engine_Api::_()->getItem($resource_type, $resource_id);
    $is_like = Engine_Api::_()->getDbtable('likes', 'core')->isLike($resource_obj, $viewer);
    return $is_like;
  }

//   // Return the body of the activity post.
//   public function getPostBody( $object_type, $object_id, $subject_id ) {
// 
//     $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
//     $activityTable = Engine_Api::_()->getItemtable('activity_action');
//     $activityTableName = $activityTable->info('name');
// 
//     $select = $activityTable->select()
//       ->from($activityTableName, array('action_id', 'body', 'date', 'attachment_count'))
//       ->where($activityTableName . '.body != ?', '')
//       ->where($activityTableName . ".object_type = ?", $object_type)
//       ->where($activityTableName . ".object_id = ?", $object_id)
//       ->where($activityTableName . ".subject_id = ?", $subject_id)
//       ->order($activityTableName . '.date DESC')
//       ->limit(5);
// 
//     $result = $select->query()->fetchAll();
//     if( !empty($result) ) {
//       $getArray = array(1,2,3,4,5);
//       $randNum = array_rand($getArray, 1);
//       $getResult = $result[$randNum];
//       return $getResult;
//     }
//     return null;
//   }

  /**
   * Return the array of the result as "Sponsored Story" which call from the "Widget" and "Adboard".
   *
   * @param $params: Array of the information, like limit and etc.
   * @return Array
   */
  public function getSponcerdStories($params) {
	$flag = 0;
	$limit = $params['limit'];
	// $sponcerdType = array(1 => 1, 2 => 2);
	$sponcerdType = array(1 => 1);
	$this->getMembership();
	$getFinalObject = $posterIdsArray = $notShowIdsArray = array();
	$tempModIdArray = $modView = $reultArray = array();
	// for ($i = 0; $i < 50; $i++) {
	while ($flag != $limit) {
	  $notShowIdsStr = '';
	  $notShowIdsStr .= implode(',', $notShowIdsArray);
	  $resultArray['storyType'] = $adType = array_rand($sponcerdType, 1);
	  $resultArray['limit'] = 1;

	  $getContent = $this->getContent($resultArray, $notShowIdsStr);
	  if (!empty($getContent)) {
		//	if( !empty($isUserLike) ) {
		foreach ($getContent as $id) {
		  $tempReultArray = array();

      $module_info = Engine_Api::_()->getDbTable('modules', 'communityad')->getModuleInfo($id['resource_type']);
      $getResourceType = $module_info['module_name'];
      $getModType = Engine_Api::_()->getDbTable('modules', 'communityad')->getModuleType($module_info['table_name']);
		  $isModuleEnabled = Engine_Api::_()->communityad()->isModuleEnabled($getResourceType);

		  if( !empty($isModuleEnabled) ) {
		  $ContentObject = Engine_Api::_()->getItem($module_info['table_name'], $id['resource_id']);

		  $isUserLike = $this->isUserLike($module_info['table_name'], $id['resource_id']);

		  if (!empty($isUserLike) || empty($ContentObject) ||  empty($isModuleEnabled)) {
		    if( !in_array( $id['userad_id'], $tempModIdArray ) ) {
		      $notShowIdsArray[] = $id['userad_id'];
		      $tempModIdArray[] =  $id['userad_id'];
		      continue;
		    }
		  }
      
		if( !in_array( $id['userad_id'], $tempModIdArray ) ) {
		    $tempReultArray['getFriendLikeArray'] = $this->getMyFriendLike($module_info['table_name'], $id['resource_id']);
		    $notShowIdsArray[] = $id['userad_id'];
		    $modView[] = $id['userad_id'];

		    $tempReultArray['resource_type'] = $module_info['table_name'];
		    $tempReultArray['resource_id'] = $id['resource_id'];
		    $tempReultArray['owner_id'] = $id['owner_id'];
		    $tempReultArray['userad_id'] = $id['userad_id'];
		    $tempReultArray['story_type'] = $id['story_type'];
		    $tempReultArray['getResourceType'] = $getResourceType;
		    $tempReultArray['module_info'] = $module_info;
		    $tempReultArray['content_object'] = $ContentObject;

		    $reultArray[] = $tempReultArray;
		  }
      }else {
        $modView[] = true;
      }
		}
	  }
	  // Unset the "Sponcerd Story" type from the array.
	  if (empty($getContent)) {
	    unset($sponcerdType[$adType]);
	  }
	  $flag = COUNT($modView);
	  if (empty($sponcerdType) || ($flag >= $limit)) {
	    break;
	  }
	}
	return $reultArray;
  }

  /**
   * Return the array of "Loggden user" friend which like on "Resource Type" & "Resource Id"
   *
   * @param $resourceType: Type of the content.
   * @param $resourceId: Id of the contant.
   * @return Array
   */
  public function getMyFriendLike($resourceType, $resourceId) {
	$likeTable = Engine_Api::_()->getItemTable('core_like');
	$likeTableName = $likeTable->info('name');
	$sub_status_select = $likeTable->select()
					->from($likeTableName, array('poster_id'))
					->where('resource_type = ?', $resourceType)
					->where('resource_id = ?', $resourceId)
					->where("poster_id IN (" . $this->_GetFriendArray . ")")
					->order("like_id DESC")
					->limit(3);
	$fetch = $sub_status_select->query()->fetchAll();
	$result = $getTempInfoArray = array();
	foreach ($fetch as $id) {
	  $getTempInfoArray['resource_type'] = $resourceType;
	  $getTempInfoArray['resource_id'] = $resourceId;
	  $getTempInfoArray['poster_id'] = $id['poster_id'];
	  $result[] = $getTempInfoArray;
	}
	return $result;
  }

  /**
   * check the item is like or not.
   *
   * @param Stirng $RESOURCE_TYPE
   * @param Int $RESOURCE_ID
   * @return results
   */
  public function likeAvailability($RESOURCE_TYPE, $RESOURCE_ID) {

	//GET THE VIEWER.
	$viewer = Engine_Api::_()->user()->getViewer();
	$likeTable = Engine_Api::_()->getItemTable('core_like');
	$likeTableName = $likeTable->info('name');
	$sub_status_select = $likeTable->select()
	  ->from($likeTableName, array('like_id'))
	  ->where('resource_type = ?', $RESOURCE_TYPE)
	  ->where('resource_id = ?', $RESOURCE_ID)
	  ->where('poster_type =?', $viewer->getType())
	  ->where('poster_id =?', $viewer->getIdentity())
	  ->limit(1);
	return $sub_status_select->query()->fetchAll();
  }

  /**
   * Trim the string
   *
   * @param $str String for Trim
   * @return String
   */
  public function getTrimStr($str) {
	$str = trim($str, ',');
	if (empty($str)) {
	  return 0;
	} else {
	  $str = trim($str, ",");
	  return $str;
	}
  }

}
?>
