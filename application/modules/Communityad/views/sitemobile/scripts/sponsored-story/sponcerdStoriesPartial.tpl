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

<?php $this->headScriptSM()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/scripts/sitemobile/core.js');?>


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
?>


<div class="">
 <ul class="feeds">
    <?php foreach($sponcerdStoriesObj as $content):?>
			<?php 
				if(Engine_Api::_()->hasModuleBootstrap('sitemobile') && $content['resource_type'] && !Engine_Api::_()->getDbtable('modules', 'sitemobile')->isModuleEnabled($content['resource_type'])) {
					continue;
				}
			?>
      <?php 
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
				if ($story_type == 2) {
					$ownerObj = $content['owner_object'];
					$getObj = $content['activity_body'];
				} else {
					$fetchFriendArray = $content['getFriendLikeArray'];
				}
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
      ?>
      <?php if ($story_type == 1): ?>
				<?php $is_identity = $this->viewer()->getIdentity();
							$adcancel_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('adcancel.enable', 1);
				?>
				<li data-activity-feed-item="20" class="activty_ul_li Hide_user_1" id="activity-item-20">
					<div id="main-feed-20">
						<div class="feed_item_header">
							<div class="feed_items_options_btn"></div>
							<div class="feed_item_photo">
								<?php echo $mainImage;?>
							</div>
							<div class="feed_item_status">
								<div class="feed_item_generated">
									<?php echo $mainBody;?></div>  
								</div>
							</div> 
						<div class="feed_item_body">
							<div style="width: 100%; position: relative;" id="feed_item_attachments_20" class="feed_item_attachments_wapper">
								<div class="feed_item_attachments">
									<span class="feed_item_attachment feed_attachment_sitepage_page">
										<div><?php echo $contentImage;?>                                                              
											<div>
												<div class="feed_item_link_title">
													<?php echo $getContantTitle;?>          
												</div>
											</div>
										</div>
									</span>
								</div>
							</div>                     
						</div>
						<div class="feed_item_btm">

					<?php 
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
		$like_ids = Engine_Api::_()->getApi('SponcerdStories', 'communityad')->likeAvailability($getResourceType, $resource_id);?>


<?php 								

									// Function Calling: Return int or empty, If loggden user does not liked this advertisment then return empty and if 	loggden user done liked this advertisment then return id of liked from 'core_likes' table.							
									if (!empty($like_ids[0]['like_id'])) {
										$unlike_show = "display:block";
										$like_show = "display:none";
										$like_id = $like_ids[0]['like_id'];
									}
									else {
										$unlike_show = "display:none;";
										$like_show = "display:block;";
										$like_id = 0;
									}
									if(empty($like_id)) { ?>
										<a id= "<?php echo $resourceType ?>_stories_unlikes_<?php echo $encode_adId; ?>" style ='display:none;'  href = "javascript:void(0);" onclick = "sm4.communityad.do_like.createLike('<?php echo $encode_adId; ?>', '<?php echo $resourceType ?>', '<?php echo $resource_id ?>', '<?php echo $owner_id ?>', 'stories', '0' )"  >
											<i class="ui-icon ui-icon-thumbs-down"></i>  <span><?php echo $this->translate('Unlike This %s', $resource_info['module_title']) ?></span>
										</a>
									<a  href = "javascript:void(0);" onclick = "sm4.communityad.do_like.createLike('<?php echo $encode_adId; ?>', '<?php echo $resourceType; ?>', '<?php echo $resource_id ?>', '<?php echo $owner_id ?>', 'stories', '0' )" id= "<?php echo $resourceType ?>_stories_most_likes_<?php echo $encode_adId; ?>" style ='<?php echo $like_show; ?>'>
										<i class="ui-icon ui-icon-thumbs-up"></i>  <span><?php echo $this->translate('Like This %s', $resource_info['module_title']) ?></span>
									</a>
									<input type ="hidden" id = "stories_likeid_info_<?php echo $encode_adId;?>" value = "<?php echo $like_id; ?>"  />
								<?php }} ?>
								</div>	
						<div class="feed_item_option"></div> 
					</div>
          <div style="clear:both;"></div>
				</li>
      <?php endif;?>
   <?php endforeach;?>
 </ul>
</div>