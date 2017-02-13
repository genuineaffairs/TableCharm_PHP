<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: adboard.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $staticBaseUrl = $this->layout()->staticBaseUrl;
  $this->headScriptSM()
        ->prependFile($staticBaseUrl . 'application/modules/Communityad/externals/scripts/sitemobile/core.js');
?>

<?php
	$viewer_id = $this->viewer()->getIdentity();
	if( !empty($this->getStoryStatus) && !empty($viewer_id) && Engine_API::_()->sitemobile()->isSupportedModule('communityadsponsored')) {
		$communityadURL = $this->url(array(), 'communityad_display', true);
		$sponcerdURL = $this->url(array(), 'sponcerd_display', true);?>
	<div data-role="navbar" data-inset="false">
		<ul>
			<li>
				<a href="<?php echo $communityadURL?>" class="ui-btn-active ui-state-persist"><?php echo $this->translate($this->getCommunityadTitle) ?></a>            
			</li>
		<li>
				<a href="<?php echo $sponcerdURL?>"><?php echo $this->translate('Sponsored Stories') ?></a>            
			</li>
		</ul>
	</div>
<?php } ?>

<div class="ui-page-content">
  <?php if(empty($this->noResult)):?>
		<ul class="p_list_grid">
			<?php foreach ($this->communityads_array as $community_ad): ?>

				<?php 
         if(Engine_Api::_()->hasModuleBootstrap('sitemobile') && $community_ad['resource_type'] && !Engine_Api::_()->getDbtable('modules', 'sitemobile')->isModuleEnabled($community_ad['resource_type'])) {
					 continue;
        }
				?>

				<?php 
					$ads_id = $encode_adId = Engine_Api::_()->communityad()->getDecodeToEncode("".$community_ad['userad_id']."");
					if( !empty($community_ad['resource_type']) && !empty($community_ad['resource_id']) ) {
						$resource_url = Engine_Api::_()->communityad()->resourceUrl( $community_ad['resource_type'], $community_ad['resource_id'] );
					}
				?>
				<?php
					if ( !empty($community_ad['resource_type']) && !empty($community_ad['resource_id']) ) {
						$set_target = '';
					} else {
						$set_target = 'target="_blank"';
					}
				?>
				<li style="height:235px;">
					<a href="<?php echo $this->url(array('adId' => $encode_adId), 'communityad_adredirect', true); ?>"  <?php echo $set_target ?> class="ui-link-inherit">
						<div class="p_list_grid_top_sec">
							<div class="p_list_grid_img">
								<span style="background-image: url(<?php echo $community_ad->getPhotoUrl(); ?>);"></span>
							</div>
						</div>   
            <div class="p_list_grid_info" style="padding-bottom: 0;">	
							<span class="p_list_grid_stats">
								<b><?php echo ucfirst($community_ad['cads_title']);?></b>
							</span>
							<span class="p_list_grid_stats">
								<?php echo Engine_String::strlen($community_ad['cads_body']) > 40 ? Engine_String::substr($community_ad['cads_body'], 0, 40) . '...' : $community_ad['cads_body'];?>
							</span>
						</div> 
					</a>
          <div class="p_list_grid_info" style="padding-top:0;">	
						<span class="fright">
							<?php 
							// Condition: If Like:1 & Resource Type & Resource Id is available in database then this means that advertisment has existence in community site then we show the 'Like' option of the user.
							$like_id = '';
							$is_module_enabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($community_ad['resource_type']);
							if( !empty($this->user_id) && !empty($community_ad['like']) && !empty($community_ad['resource_type']) && !empty($community_ad['resource_id']) && !empty($is_module_enabled) && !empty($resource_url) ) {?>

<?php 								$checkResourceType = $resourceType = $community_ad['resource_type'];
								$resource_id = $community_ad['resource_id'];
								$owner_id = $community_ad['owner_id'];
								$ad_id = $community_ad['userad_id'];

								// Queary: Return array of 'My Friend Id' which liked this advertisment.
								//$friendLikeId = Engine_Api::_()->getDbTable('likes', 'communityad')->isFriendLiked( $ad_id );
								$resource_info = Engine_Api::_()->getDbtable('modules', 'communityad')->getModuleInfo($resourceType);
								if (!empty($resource_info)) {
									$checkResourceType = $resource_info['table_name'];
								}
								$like_ids = Engine_Api::_()->communityad()->check_availability( $ad_id );
								$likeAdInfo = Engine_Api::_()->communityad()->likeAdInfo( $resource_id, $resourceType, $ad_id, $like_ids );

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
                  <div id= "<?php echo $resourceType ?>_ad_board_unlikes_<?php echo $encode_adId;?>" style ='display:none;font-size:11px;'><?php echo $this->translate('You like this.');?>
										<a href = "javascript:void(0);" onclick = "sm4.communityad.do_like.createLike('<?php echo $encode_adId; ?>', '<?php echo $resourceType ?>', '<?php echo $resource_id ?>', '<?php echo $owner_id ?>', 'ad_board', '<?php echo $resource_url['like']; ?>')" >
											<i class="ui-icon ui-icon-thumbs-down"></i>
										</a>
                  </div>
									<a href = "javascript:void(0);" onclick = "sm4.communityad.do_like.createLike('<?php echo $encode_adId; ?>', '<?php echo $resourceType; ?>', '<?php echo $resource_id ?>', '<?php echo $owner_id ?>', 'ad_board', '<?php echo $resource_url['like']; ?>' )" id= "<?php echo $resourceType ?>_ad_board_most_likes_<?php echo $encode_adId;?>" style ='<?php echo $like_show;?>'>
										<i class="ui-icon ui-icon-thumbs-up"></i>
									</a>
									<input type ="hidden" id = "ad_board_likeid_info_<?php echo $encode_adId;?>" value = "<?php echo $like_id; ?>"  />
								<?php } ?>
            </span>      
						<span class="p_list_grid_stats">
							<?php
								if ( !empty($community_ad['resource_type']) && !empty($community_ad['resource_id']) ) { ?>
									<?php
											if( !empty($resource_url['status']) ) {
												echo '<a href="'. $this->url(array('adId' => $encode_adId), 'communityad_adredirect', true)  .'">' . $resource_url['title'] . "</a>";
											} else {
												echo $resource_url['title'];
											} ?>
								<?php } else if( !empty($this->hideCustomUrl) ) {
										$ad_url = Engine_Api::_()->communityad()->adSubTitle( $community_ad['cads_url'] );
										echo '<a title="'. $community_ad['cads_url'] .'"href="'. $this->url(array('adId' => $encode_adId), 'communityad_adredirect', true) .'" target="_blank" >' . $this->translate(Engine_Api::_()->communityad()->truncation($ad_url, 25)) . "</a>";
								}
								?>
						</span>
            <span class="p_list_grid_stats">
							 <?php
								if( empty($likeAdInfo) ) {
									echo $this->translate('You like this.');
								}else {
									if( empty($likeAdInfo['is_like']) && empty($likeAdInfo['friend_like']) ) {
										if( !empty($likeAdInfo['total_like']) ) {
											$peoplesLike =	$this->translate(array('%s person likes this.', '%s people like this.', $likeAdInfo['total_like']),$this->locale()->toNumber($likeAdInfo['total_like']));		
											$peoplesLike = '<a style="font-size:12px;" href="'.$this->url(array('module' => 'communityad', 'controller' => 'display', 'action' => 'adfriendlike', 'resource_type' => $resourceType, 'resource_id' => $resource_id, 'call_status' => 'public', 'ad_id' => $ads_id), 'default', true).'">' . $peoplesLike . '</a>';	
											$peoplesLike = '<div class="cmaddis_cont">' . $peoplesLike . '</div>';
											echo $peoplesLike;
										}
									}else if( !empty($likeAdInfo['is_like']) && empty($likeAdInfo['friend_like']) ) {
										if( !empty($likeAdInfo['total_like']) ) {
											$peoplesLike =	$this->translate(array('%s other likes this.', '%s others like this.', $likeAdInfo['total_like']),$this->locale()->toNumber($likeAdInfo['total_like']));
										}else {
											$peoplesLike = '';
										}	
										$peoplesLike = '<a style="font-size:12px;" href="'.$this->url(array('module' => 'communityad', 'controller' => 'display', 'action' => 'adfriendlike', 'resource_type' => $resourceType, 'resource_id' => $resource_id, 'call_status' => 'public', 'ad_id' => $ads_id), 'default', true).'" >' . $peoplesLike . '</a>';
										$show_label = Zend_Registry::get('Zend_Translate')->_('You and %s');
										$show_label = sprintf($show_label, $peoplesLike);
										$show_label = $show_label;
										echo $show_label;
									}else if( empty($likeAdInfo['is_like']) && !empty($likeAdInfo['friend_like']) ) {
										if( !empty($likeAdInfo['total_like']) ) {
											$peoplesLike =	$this->translate(array('%s other likes this.', '%s others like this.', $likeAdInfo['total_like']),$this->locale()->toNumber($likeAdInfo['total_like']));
										}else {
											$peoplesLike = '';
										}
										$peoplesLike = '<a style="font-size:12px;" href="'.$this->url(array('module' => 'communityad', 'controller' => 'display', 'action' => 'adfriendlike', 'resource_type' => $resourceType, 'resource_id' => $resource_id, 'call_status' => 'public', 'ad_id' => $ads_id), 'default', true).'" >' . $peoplesLike . '</a>';
										$show_label = Zend_Registry::get('Zend_Translate')->_('%s %s');
										$show_label = sprintf($show_label, $likeAdInfo['friend_like'], $peoplesLike);
										$show_label =  $show_label;
										echo $show_label;
									}else if( !empty($likeAdInfo['is_like']) && !empty($likeAdInfo['friend_like']) ) {
										if( !empty($likeAdInfo['total_like']) ) {
											$peoplesLike =	$this->translate(array('%s other likes this.', '%s others like this.', $likeAdInfo['total_like']),$this->locale()->toNumber($likeAdInfo['total_like']));
										}else {
											$peoplesLike = '';
										}	
										$peoplesLike = '<a style="font-size:12px;" href="'.$this->url(array('module' => 'communityad', 'controller' => 'display', 'action' => 'adfriendlike', 'resource_type' => $resourceType, 'resource_id' => $resource_id, 'call_status' => 'public', 'ad_id' => $ads_id), 'default', true).'">' . $peoplesLike . '</a>';	
										$show_label = Zend_Registry::get('Zend_Translate')->_('%s %s');
										$show_label = sprintf($show_label, $likeAdInfo['friend_like'], $peoplesLike);
										$show_label = $show_label;
										echo $show_label;
									}
								}
								?>
							<?php } ?>
						 </span>
						</div>
				</li>
			<?php endforeach; ?>
		</ul>
  <?php else:?>
    <div class="tip"><span><?php echo $this->translate('No advertisements have been created yet.'); ?></span></div>
  <?php endif;?>
</div>