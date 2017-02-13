<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sponcerdStoriesPartial.tpl  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  // FUNCTION: Call for show 'Like' and 'Unlike' in the widgets.
  var communityad_likeinfo = function(ad_id, resource_type, resource_id, owner_id, widgetType, core_like) {
    // SENDING REQUEST TO AJAX
    var request = createLike(ad_id, resource_type, resource_id, owner_id, widgetType, core_like);
    // RESPONCE FROM AJAX
    request.addEvent('complete', function(responseJSON) {
      if(responseJSON.like_id )
      {
        $(widgetType + '_likeid_info_'+ ad_id).value = responseJSON.like_id;
        $(resource_type + '_' + widgetType + '_most_likes_' + ad_id).style.display = 'none';
        $(resource_type + '_' + widgetType + '_unlikes_'+ ad_id).style.display = 'block';
      }
      else
      {
        $(widgetType + '_likeid_info_'+ ad_id).value = 0;
        $(resource_type + '_' + widgetType + '_most_likes_' + ad_id).style.display = 'block';
        $(resource_type +'_' + widgetType + '_unlikes_'+ ad_id).style.display = 'none';
      }
    });
  }
</script>


<?php
$sponcerdStoriesObj = $this->modArray;
$isAdboard = !empty($this->isAdboard) ? $this->isAdboard : null;

$flag = 0;
$numOfResult = @COUNT($sponcerdStoriesObj);
$numOfResult = $numOfResult - 1;
$viewer = Engine_Api::_()->user()->getViewer();

// if (true)
  $div_id = 0;
$div_limit = 1;
foreach ($sponcerdStoriesObj as $content) {
  $getResourceType = $content['resource_type'];
  $module_info = $content['module_info'];
  $ContentObject = $content['content_object'];
  $story_type = $content['story_type'];
  $resource_type = $content['resource_type'];
  $resource_id = $content['resource_id'];
  $owner_id = $content['owner_id'];
  $ad_id = $content['userad_id'];
  $campagn_id = !empty($content['campaign_id'])? $content['campaign_id']: 0;
  $encode_adId = Engine_Api::_()->communityad()->getDecodeToEncode("" . $ad_id . "");

  if (!empty($isAdboard) && $div_limit % 4 == 1) {
	echo '<div class="caab_spsa_list_row">';
  }

  if (!empty($isAdboard)) {
	echo '<div class="caab_spsa_list">';
  }
?>
  <!-- DIV: Which show when click on cross of advertisment. -->
  <div id="stories_widget_ad_cancel_<?php echo $div_id; ?>" style="display:none;" class="cmadrem">
    <div class="cmadrem_rl">
<?php echo '<a class="" title="' . $this->translate('Cancel reporting this story') . '" href="javascript:void(0);" onclick="adUndo(' . $div_id . ', \'stories_widget\');">' . $this->translate("Undo") . '</a>'; ?>
    </div>
    <div class="cmadrem_con">
<?php echo $this->translate("Do you want to report this? Why didn't you like it?"); ?>
	<form>
<?php $ads_id = $encode_adId; ?>
	  <div><input type="radio" name="adAction" value="0" onclick="adSave('Misleading', '<?php echo $ads_id; ?>', <?php echo $div_id; ?>, 'stories_widget')"/><?php echo $this->translate('Misleading'); ?></div>
	  <div><input type="radio" name="adAction" value="1" onclick="adSave('Offensive', '<?php echo $ads_id; ?>', <?php echo $div_id; ?>, 'stories_widget')"/><?php echo $this->translate('Offensive'); ?></div>
	  <div><input type="radio" name="adAction" value="2" onclick="adSave('Inappropriate', '<?php echo $ads_id; ?>', <?php echo $div_id; ?>, 'stories_widget')"/><?php echo $this->translate('Inappropriate'); ?></div>
	  <div><input type="radio" name="adAction" value="3" onclick="adSave('Licensed Material', '<?php echo $ads_id; ?>', <?php echo $div_id; ?>, 'stories_widget')"/><?php echo $this->translate('Licensed Material'); ?></div>
	  <div><input type="radio" name="adAction" value="4" onclick="otherAdCannel(4, <?php echo $div_id; ?>, 'stories_widget')" id="stories_widget_other_<?php echo $div_id; ?>"/><?php echo $this->translate('Other'); ?></div>
	  <div><textarea name="stories_widget_other_text_<?php echo $div_id; ?>" onclick="this.value=''" onblur="if(this.value=='')this.value='<?php echo $this->translate('Specify your reason here..') ?>';"  id= "stories_widget_other_text_<?php echo $div_id; ?>" style="display:none;" ><?php echo $this->translate('Specify your reason here..') ?></textarea>
	  </div>
	  <div><?php echo '<a href="javascript:void(0);" onclick="adSave(\'Other\', \'' . $ads_id . '\', ' . $div_id . ', \'stories_widget\')" id="stories_widget_other_button_' . $div_id . '"  style="display:none" class="cmadrem_button">' . $this->translate('Report') . '</a>'; ?></div>
	</form>
  </div>
</div>

<?php
	  if ($story_type == 2) {
		$ownerObj = $content['owner_object'];
		$getObj = $content['activity_body'];
	  } else {
		$fetchFriendArray = $content['getFriendLikeArray'];
	  }


	  // Work for Add view count.
	  Engine_Api::_()->communityad()->adViewCount($ad_id, $campagn_id);

	  // Make an template array according to the "Story Type".
	  switch ($story_type) {
		// Case 1: Indicate the "Page Like" story.
		case 1:
		  $contantURL = $this->url(array('adId' => $encode_adId), 'communityad_adredirect', true);
		  $contentImage = $this->htmlLink($contantURL, $this->itemPhoto($ContentObject, 'thumb.icon'));
		  $contantTitle = Engine_Api::_()->communityad()->truncation($ContentObject->getTitle(), $this->titleTruncationLimit);
		  $getContantTitle = str_replace(' ', '&nbsp;', $contantTitle);
		  $contantTitle = $this->htmlLink($contantURL, $contantTitle, array('title' => $ContentObject->getTitle()));
		  $getContantTitle = $this->htmlLink($contantURL, $getContantTitle, array('title' => $ContentObject->getTitle()));
		  $contentOption = 'Like';
		  $tempflag = 0;
		  $friendFlag = 0;
		  $tempTitleArray = array();
		  $getFriendArray = array();

		  foreach ($fetchFriendArray as $friendIdArray) {
			$friendId = $friendIdArray['poster_id'];
			$friendObj = Engine_Api::_()->getItem('user', $friendId);
			if (empty($friendFlag)) {
			  $mainImage = $this->htmlLink($friendObj->getHref(), $this->itemPhoto($friendObj, 'thumb.icon'));
			}
			$tempTitleArray[] = $getMainTitle = $this->htmlLink($friendObj->getHref(), Engine_Api::_()->communityad()->truncation($friendObj->getTitle(), $this->rootTitleTruncationLimit));

			if (empty($friendFlag)) {
			  $getMainTitle = $this->translate('<b>%s</b> likes %s.', $getMainTitle, $contantTitle);
			} else if ($friendFlag == 1) {
			  $getMainTitle = $this->translate('<b>%s</b> and %s like %s.', $tempTitleArray[0], $tempTitleArray[1], $contantTitle);
			} else {
			  $getMainTitle = $this->translate('<b>%s</b>, %s and %s like %s.', $tempTitleArray[0], $tempTitleArray[1], $tempTitleArray[2], $contantTitle);
			}
			$friendFlag++;
		  }
		  $mainBody = $getMainTitle;
		  break;
	  }



	  if ($story_type == 1) {
		echo '<div class="cmad_sdab" id="stories_widget_ad_' . $div_id . '">';
?><div class="cmaddis_close">
<?php
		$is_identity = $this->viewer()->getIdentity();
		$adcancel_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('adcancel.enable', 1);
		if (!empty($is_identity) && !empty($adcancel_enable)) {
		  echo '<a class="" title="' . $this->translate('Report this story') . '" href="javascript:void(0);" onclick="adCancel(' . $div_id . ', \'stories_widget\');"></a>';
		} ?>
		</div><?php
		echo '<div class="cmad_sdab_sp">' . $mainImage . '</div>';
		echo '<div class="cmad_sdab_body">';
		echo '<div class="cmad_sdab_title">' . $mainBody . '</div>';
		echo '<div class="cmad_sdab_cont">';
		echo '<div class="cmad_sdab_cont_img">' . $contentImage . '</div>';
		echo '<div class="cmad_sdab_cont_body">' . $getContantTitle . '</div>';
		echo '</div>';
	  }
	  $div_id++;


// LIKE WORK START FROM HERE:
	  // Condition: If Like:1 & Resource Type & Resource Id is available in database then this means that advertisment has existence in community site then we show the 'Like' option of the user.
	  $like_id = '';
	  if (!empty($viewer) && !empty($getResourceType) && !empty($resource_id)) {
		$checkResourceType = $resourceType = $getResourceType;
		$resource_id = $resource_id;
		$owner_id = $owner_id;
		// $encode_adId = $ad_id = $ad_id;
		$resource_url = null;
		if (!empty($getResourceType) && !empty($resource_id)) {
		  $resource_url = Engine_Api::_()->communityad()->resourceUrl($getResourceType, $resource_id);
		}

		// Queary: Return array of 'My Friend Id' which liked this advertisment.
		$resource_info = $module_info; //Engine_Api::_()->getDbtable('modules', 'communityad')->getModuleInfo($resourceType);
		if (!empty($resource_info)) {
		  $checkResourceType = $resource_info['table_name'];
		}
		$like_ids = Engine_Api::_()->getApi('SponcerdStories', 'communityad')->likeAvailability($getResourceType, $resource_id);

		// Function Calling: Return int or empty, If loggden user does not liked this advertisment then return empty and if loggden user done liked this advertisment then return id of liked from 'core_likes' table.
		if (!empty($like_ids[0]['like_id'])) {
		  $unlike_show = "display:block";
		  $like_show = "display:none";
		  $like_id = $like_ids[0]['like_id'];
		} else {
		  $unlike_show = "display:none;";
		  $like_show = "display:block;";
		  $like_id = 0;
		}
?>
<?php if (empty($like_id)) { // Condition: Show 'Like Link' only when loggden user not like this advertisment. else show message that you have liked this advertismant.
?>
		  <div class="cmad_like_button cmad_sdab_cont_stat" id= "<?php echo $resourceType ?>_stories_unlikes_<?php echo $encode_adId; ?>" style ='display:none;' >
<?php // echo '<div class="cmad_sdab_cont_stat">' . $this->translate('You like this.') . '</div>';  ?>
		    <a href = "javascript:void(0);" onclick = "communityad_likeinfo('<?php echo $encode_adId; ?>', '<?php echo $resourceType ?>', '<?php echo $resource_id ?>', '<?php echo $owner_id ?>', 'stories', '0' )">
			<i class="like_thumbdown_icon"></i>
			<span><?php echo $this->translate('Unlike') ?></span>
		  </a>
		</div>
		<div class="cmad_like_button cmad_sdab_cont_stat" id= "<?php echo $resourceType ?>_stories_most_likes_<?php echo $encode_adId; ?>" style ='<?php echo $like_show; ?>'>
		  <a href = "javascript:void(0);" onclick = "communityad_likeinfo('<?php echo $encode_adId; ?>', '<?php echo $resourceType; ?>', '<?php echo $resource_id ?>', '<?php echo $owner_id ?>', 'stories', '0' )">
			<i class="like_thumbup_icon"></i>
			<span><?php echo $this->translate('Like This %s', $resource_info['module_title']) ?></span>
		  </a>
		</div>
		<input type ="hidden" id = "stories_likeid_info_<?php echo $encode_adId; ?>" value = "<?php echo $like_id; ?>"  />
<?php
		}
	  }
?>
	  <input type ="hidden" id = "mixinfolike_<?php echo $encode_adId; ?>" value = "<?php echo $like_id; ?>"  /></div></div>
<?PHP
	  // LIKE WORK END FROM HERE:
	  if (!empty($isAdboard)) {
		echo '</div>';
	  }

	  if (!empty($isAdboard) && ($div_limit % 4 == 0)) {
		echo '</div>';
	  }
	  $div_limit++;
	}