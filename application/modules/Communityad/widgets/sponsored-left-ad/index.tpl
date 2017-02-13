<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if(!empty($this->ajax_enabled)) : ?>
	<script type="text/javascript">
	window.addEvent('load', function() {
		var url = en4.core.baseUrl + 'widget/index/mod/communityad/name/sponsored-left-ad';
		var request = new Request.HTML({    
			url : url,
			method: 'get',
			data : {
				format : 'html',
				'load_content' : 1,
				'identity' : '<?php echo $this->identity?>'
			},    
			onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
				$('sponsored_left_ajax_id').innerHTML = responseHTML;				
			}
			});  
			request.send();
	});
	</script>
<?php endif; ?>
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

<?php if (empty ($this->load_content)) { ?>
<div id="sponsored_left_ajax_id">
<?php };?>
<?php if (!empty ($this->load_content)) { ?>
<div class="cmad_ad_clm">
	<div>
	<div class="cmad_bloack_top">
		<?php echo '<b>'. $this->translate('Sponsored'). '</b>'; ?>
		<?php if(Engine_Api::_()->communityad()->enableCreateLink()) : ?>
			<?php echo '<a href="'.$this->url(array(), 'communityad_listpackage', true).'">' . $this->translate('Create an Ad') . '</a>';?>
		<?php endif; ?>
	</div>
	<?php $div_id = 0;
		
		foreach ($this->communityads_array as $community_ad):
			$encode_adId = Engine_Api::_()->communityad()->getDecodeToEncode("".$community_ad['userad_id']."");
			if( !empty($community_ad['resource_type']) && !empty($community_ad['resource_id']) ) {
				$resource_url = Engine_Api::_()->communityad()->resourceUrl( $community_ad['resource_type'], $community_ad['resource_id'] );
			}	?>
			<?php Engine_Api::_()->communityad()->adViewCount( $community_ad['userad_id'], $community_ad['campaign_id'] ); ?>
			<!-- DIV: Which show when click on cross of advertisment. -->
			<div id="sponsored_left_col1_first_widget_ad_cancel_<?php echo $div_id; ?>" style="display:none;" class="cmadrem">
				<div class="cmadrem_rl">
					<?php echo '<a class="" title="' . $this->translate('Cancel reporting this ad') . '" href="javascript:void(0);" onclick="adUndo('.$div_id.', \'sponsored_left_col1_first_widget\');">'. $this->translate("Undo"). '</a>'; ?>
				</div>
				<div class="cmadrem_con">
					<?php echo $this->translate("Do you want to report this? Why didn't you like it?"); ?>
					<form>
						<?php $ads_id = $encode_adId; ?>
						<div><input type="radio" name="adAction" value="0" onclick="adSave('Misleading', '<?php echo $ads_id; ?>', <?php echo $div_id; ?>, 'sponsored_left_col1_first_widget')"/><?php echo $this->translate('Misleading'); ?></div>
						<div><input type="radio" name="adAction" value="1" onclick="adSave('Offensive', '<?php echo $ads_id; ?>', <?php echo $div_id; ?>, 'sponsored_left_col1_first_widget')"/><?php echo $this->translate('Offensive'); ?></div>
						<div><input type="radio" name="adAction" value="2" onclick="adSave('Inappropriate', '<?php echo $ads_id; ?>', <?php echo $div_id; ?>, 'sponsored_left_col1_first_widget')"/><?php echo $this->translate('Inappropriate'); ?></div>
						<div><input type="radio" name="adAction" value="3" onclick="adSave('Licensed Material', '<?php echo $ads_id; ?>', <?php echo $div_id; ?>, 'sponsored_left_col1_first_widget')"/><?php echo $this->translate('Licensed Material'); ?></div>
						<div><input type="radio" name="adAction" value="4" onclick="otherAdCannel(4, <?php echo $div_id; ?>, 'sponsored_left_col1_first_widget')" id="sponsored_left_col1_first_widget_other_<?php echo $div_id; ?>"/><?php echo $this->translate('Other'); ?></div>
						<div><textarea name="sponsored_left_col1_first_widget_other_text_<?php echo $div_id; ?>" onclick="this.value=''" onblur="if(this.value=='')this.value='<?php echo $this->translate('Specify your reason here..') ?>';"  id= "sponsored_left_col1_first_widget_other_text_<?php echo $div_id; ?>" style="display:none;" ><?php echo $this->translate('Specify your reason here..') ?></textarea>
						</div>
						<div><?php echo '<a href="javascript:void(0);" onclick="adSave(\'Other\', \''.$ads_id.'\', '.$div_id.', \'sponsored_left_col1_first_widget\')" id="sponsored_left_col1_first_widget_other_button_'.$div_id.'"  style="display:none" class="cmadrem_button">' . $this->translate('Report') . '</a>'; ?></div>
					</form>
				</div>	
			</div>
		
			<!-- DIV:  Which default show, This div contain the all information about advertisment. start -->
			<div class="cmaddis" id="sponsored_left_col1_first_widget_ad_<?php echo $div_id; ?>">
				<div class="cmaddis_close">
					<?php $is_identity = $this->viewer()->getIdentity(); 
								$adcancel_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('adcancel.enable', 1);
								if(!empty($is_identity)  && !empty($adcancel_enable)) {
									echo	'<a class="" title="' . $this->translate('Report this ad') . '" href="javascript:void(0);" onclick="adCancel('.$div_id.', \'sponsored_left_col1_first_widget\');"></a>'; 
								} ?>
				</div>
				<div class="cmad_addis">
					<!--tital code start here for both-->
					<div class="cmaddis_title">
							<?php // Title if has existence on site then "_blank" not work else work.
								if ( !empty($community_ad['resource_type']) && !empty($community_ad['resource_id']) ) {
									$set_target = '';
								} else {
									$set_target = 'target="_blank"';
								}
								echo '<a href="'. $this->url(array('adId' => $encode_adId), 'communityad_adredirect', true)  .'" '.$set_target.' >' . ucfirst($community_ad['cads_title']) . "</a>";
							?>
					</div>
					<!--title code end here for both-->
				
					<?php
						// Condition: If content has existence in the site then show `like` or `unlike` options.
						if ( !empty($community_ad['resource_type']) && !empty($community_ad['resource_id']) ) { ?>
							<div class="cmaddis_adinfo">
								<?php 
									if( !empty($resource_url['status']) ) {
										echo '<a href="'. $this->url(array('adId' => $encode_adId), 'communityad_adredirect', true)  .'">' . $resource_url['title'] . "</a>";
									}else {
										echo $resource_url['title'];
									} ?>
							</div>
						<?php } else if( !empty($this->hideCustomUrl) )  {
							//condition: link code start here for not existenc ad
								$ad_url = Engine_Api::_()->communityad()->adSubTitle( $community_ad['cads_url'] );
							echo '<div class="cmaddis_adinfo"><a title="'. $ad_url .'" href="'. $this->url(array('adId' => $encode_adId), 'communityad_adredirect', true)  .'" target="_blank" >' . $this->translate(Engine_Api::_()->communityad()->truncation($ad_url, 20)) . "</a></div>"; 
						} ?>

					<!--image code start here for both-->
					<?php
					// Display image if 'Advertisment' is the content of the site then show the content image.
							$community_ad_image = $this->itemPhoto($community_ad, '', '');
					?>
					<div class="cmaddis_image">
						<?php 
							echo '<a href="'. $this->url(array('adId' => $encode_adId), 'communityad_adredirect', true)  .'" '.$set_target.' >' .  $community_ad_image . "</a>"; 
						?>
					</div>
					<!--image code end here for both-->
				
					<!--description code start here for both-->
					<div class="cmaddis_body">
						<?php 
							echo '<a href="'. $this->url(array('adId' => $encode_adId), 'communityad_adredirect', true)  .'" '.$set_target.' >' .  $community_ad['cads_body'] . "</a>";
						?>
					</div>
					<!--description code end here for both-->
				</div>	
				<?php
					// Condition: If Like:1 & Resource Type & Resource Id is available in database then this means that advertisment has existence in community site then we show the 'Like' option of the user.
						$like_id = '';
						$is_module_enabled = Engine_Api::_()->communityad()->isModuleEnabled($community_ad['resource_type']);
						if( !empty($this->user_id) && !empty($community_ad['like']) && !empty($community_ad['resource_type']) && !empty($community_ad['resource_id']) && !empty($is_module_enabled) && !empty($resource_url) ) {
							$checkResourceType = $resourceType = $community_ad['resource_type'];
							$resource_id = $community_ad['resource_id'];
							$owner_id = $community_ad['owner_id'];
							$ad_id = $community_ad['userad_id'];

							// Queary: Return array of 'My Friend Id' which liked this advertisment.
							$resource_info = Engine_Api::_()->getDbtable('modules', 'communityad')->getModuleInfo($resourceType);
							if (!empty($resource_info)) {
								$checkResourceType = $resource_info['table_name'];
							}
							$like_ids = Engine_Api::_()->communityad()->check_availability( $ad_id );
							$likeAdInfo = Engine_Api::_()->communityad()->likeAdInfo( $resource_id, $resourceType, $ad_id, $like_ids );

							if( empty($likeAdInfo) ) {
								echo '<div class="cmaddis_cont">' . $this->translate('You like this.') . '</div>';
							}else {
								if( empty($likeAdInfo['is_like']) && empty($likeAdInfo['friend_like']) ) {
									if( !empty($likeAdInfo['total_like']) ) {
										$peoplesLike =	$this->translate(array('%s person likes this.', '%s people like this.', $likeAdInfo['total_like']),$this->locale()->toNumber($likeAdInfo['total_like']));		
										$peoplesLike = '<a href="'.$this->url(array('module' => 'communityad', 'controller' => 'display', 'action' => 'adfriendlike', 'resource_type' => $resourceType, 'resource_id' => $resource_id, 'call_status' => 'public', 'ad_id' => $ads_id), 'default', true).'" class="smoothbox">' . $peoplesLike . '</a>';	
										$peoplesLike = '<div class="cmaddis_cont">' . $peoplesLike . '</div>';
										echo $peoplesLike;
									}
								}else if( !empty($likeAdInfo['is_like']) && empty($likeAdInfo['friend_like']) ) {
									if( !empty($likeAdInfo['total_like']) ) {
										$peoplesLike =	$this->translate(array('%s other likes this.', '%s others like this.', $likeAdInfo['total_like']),$this->locale()->toNumber($likeAdInfo['total_like']));
									}else {
										$peoplesLike = '';
									}	
									$peoplesLike = '<a href="'.$this->url(array('module' => 'communityad', 'controller' => 'display', 'action' => 'adfriendlike', 'resource_type' => $resourceType, 'resource_id' => $resource_id, 'call_status' => 'public', 'ad_id' => $ads_id), 'default', true).'" class="smoothbox">' . $peoplesLike . '</a>';
									$show_label = Zend_Registry::get('Zend_Translate')->_('You and %s');
									$show_label = sprintf($show_label, $peoplesLike);
									$show_label = '<div class="cmaddis_cont">' . $show_label . '</div>';
									echo $show_label;
								}else if( empty($likeAdInfo['is_like']) && !empty($likeAdInfo['friend_like']) ) {
									if( !empty($likeAdInfo['total_like']) ) {
										$peoplesLike =	$this->translate(array('%s other likes this.', '%s others like this.', $likeAdInfo['total_like']),$this->locale()->toNumber($likeAdInfo['total_like']));
									}else {
										$peoplesLike = '';
									}
									$peoplesLike = '<a href="'.$this->url(array('module' => 'communityad', 'controller' => 'display', 'action' => 'adfriendlike', 'resource_type' => $resourceType, 'resource_id' => $resource_id, 'call_status' => 'public', 'ad_id' => $ads_id), 'default', true).'" class="smoothbox">' . $peoplesLike . '</a>';
									$show_label = Zend_Registry::get('Zend_Translate')->_('%s %s');
									$show_label = sprintf($show_label, $likeAdInfo['friend_like'], $peoplesLike);
									$show_label = '<div class="cmaddis_cont">' . $show_label . '</div>';
									echo $show_label;
								}else if( !empty($likeAdInfo['is_like']) && !empty($likeAdInfo['friend_like']) ) {
									if( !empty($likeAdInfo['total_like']) ) {
										$peoplesLike =	$this->translate(array('%s other likes this.', '%s others like this.', $likeAdInfo['total_like']),$this->locale()->toNumber($likeAdInfo['total_like']));
									}else {
										$peoplesLike = '';
									}	
									$peoplesLike = '<a href="'.$this->url(array('module' => 'communityad', 'controller' => 'display', 'action' => 'adfriendlike', 'resource_type' => $resourceType, 'resource_id' => $resource_id, 'call_status' => 'public', 'ad_id' => $ads_id), 'default', true).'" class="smoothbox">' . $peoplesLike . '</a>';	
									$show_label = Zend_Registry::get('Zend_Translate')->_('%s %s');
									$show_label = sprintf($show_label, $likeAdInfo['friend_like'], $peoplesLike);
									$show_label = '<div class="cmaddis_cont">' . $show_label . '</div>';
									echo $show_label;
								}
							}
							// Function Calling: Return int or empty, If loggden user does not liked this advertisment then return empty and if loggden user done liked this advertisment then return id of liked from 'core_likes' table.							
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
							?>
							<?php if(empty($like_id)) { // Condition: Show 'Like Link' only when loggden user not like this advertisment. else show message that you have liked this advertismant.   ?>
								<div class="cmad_like_button" id= "<?php echo $resourceType ?>_sponsored_left_col1_unlikes_<?php echo $encode_adId;?>" style ='display:none;' >
									<?php echo '<div class="cmaddis_cont">' .$this->translate('You like this.'). '</div>'; ?>
									<a href = "javascript:void(0);" onclick = "communityad_likeinfo('<?php echo $encode_adId; ?>', '<?php echo $resourceType ?>', '<?php echo $resource_id ?>', '<?php echo $owner_id ?>', 'sponsored_left_col1', '<?php echo $resource_url['like']; ?>' )">
									<i class="like_thumbdown_icon"></i>
									<span><?php echo $this->translate('Unlike') ?></span>
									</a>									
								</div>
								<div class="cmad_like_button" id= "<?php echo $resourceType ?>_sponsored_left_col1_most_likes_<?php echo $encode_adId;?>" style ='<?php echo $like_show;?>'>
									<a href = "javascript:void(0);" onclick = "communityad_likeinfo('<?php echo $encode_adId; ?>', '<?php echo $resourceType; ?>', '<?php echo $resource_id ?>', '<?php echo $owner_id ?>', 'sponsored_left_col1', '<?php echo $resource_url['like']; ?>' )">
									<i class="like_thumbup_icon"></i>
									<span><?php echo $this->translate('Like') ?></span>
									</a>
								</div>
								<input type ="hidden" id = "sponsored_left_col1_likeid_info_<?php echo $encode_adId;?>" value = "<?php echo $like_id; ?>"  />
							<?php }
					}
				?>
				<input type ="hidden" id = "mixinfolike_<?php echo $encode_adId;?>" value = "<?php echo $like_id; ?>"  />
			</div>
			<!-- DIV:  Which default show, This div contain the all information about advertisment. end -->
			<?php $div_id++; ?>
		<?php endforeach; ?>
		<?php 
		      $is_show_adboard = Engine_Api::_()->getApi('settings', 'core')->getSetting('show.adboard', 1);
		      if( !empty($is_show_adboard) ):
			echo '<div class="cmaddis_more"><a href="'.$this->url(array(), 'communityad_display', true).'">' . $this->translate('More Ads') . '</a></div>'; 
		      endif;
		?>
	</div>
</div>
<?php };?>
<?php if (empty ($this->load_content)) { ?>
	</div>
<?php };?>