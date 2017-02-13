<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: group.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
	$modName = 'sitepagemember';
	$moduleId = $this->modContentId;

	$this->headTranslate(array('Please select at-least one entry above to send suggestion to.', 'Search Members', 'Selected', 'No more suggestions are available.', 'Sorry, no more suggestions.'));
	$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitepageevent/externals/scripts/core.js');
	$displayUserStr = '';
	$array = $this->suggest_user_id;
	$array = array_diff($array, $this->user_ids); 
	if(!empty($array) && is_array($array)) { 
		$displayUserStr = implode('::', $array); 
	}
?>

<script type="text/javascript">
	var action_module = "<?php echo $modName;  ?>";
	var action_session_id = "<?php echo $moduleId;  ?>";
	var select_text_flag = "<?php echo $this->translate("Selected"); ?>";
	var suggestion_string = '';
	var show_selected = '<?php echo $this->show_selected;?>';
	var friends_count = '<?php echo $this->friends_count;?>';
	var suggestion_string_temp = '<?php echo $this->selected_checkbox;?>';
	//var tempSelectedFriend = '<?php //echo $this->tempSelectedFriend; ?>';
	var memberSearch = '<?php echo $this->search ?>';
	var memberPage = <?php echo sprintf('%d', $this->members->getCurrentPageNumber()) ?>;
	var notification_type = 'null';
	var entity = 'null';
	var item_type = 'null';
	var findFriendFunName = 'null';
	var notificationType = 0;
	var modError = 1;
	var modName = 'sitepagemember';
	var modItemType = 'null';
	var displayUserStr = '<?php echo $displayUserStr; ?>';
	var paginationArray = new Array();
	var SelectedPopupContent = new Array();
	var dontHaveResult = 1;
	var popupFlag = 0;
</script>
<?php if ( !$this->search_true ): ?>
	<div class="seaocore_popup">
		<div class="seaocore_popup_top">
			<div class="seaocore_popup_title"><?php echo $this->translate('Invite Members of "%s"', $this->pagevent_title); ?></div>
			<div class="seaocore_popup_des"><?php echo $this->translate("Selected Members will get a invitation from you to view this page event entry."); ?></div>
		</div>
		<?php if( empty($this->selectedFriendFlag) && !empty($this->mod_combind_path)): ?>
			<div class="seaocore_popup_options">
				<div class="seaocore_popup_options_tbs">
					<a href="javascript:void(0);" onclick="selectAllFriend('<?php echo $displayUserStr; ?>')" id="newcheckbox"><?php echo $this->translate('Select All'); ?></a>
				</div>
			</div>
		<?php endif; ?>
		<div id="main_box">
<?php endif; ?>

		<form name="suggestion" method="POST">
			<?php if( !empty($hiddenFieldName) ) { $modName = $hiddenFieldName; }?>
			<input type="hidden" name="entity" value="<?php echo $modName;  ?>" />
			<input type="hidden" name="entity_id" value="<?php echo $moduleId;  ?>" />
			<div id="hidden_checkbox"> </div>
			<!--Member list start here-->
			<div class="seaocore_popup_content">
				<div class="seaocore_popup_content_inner">	
					<?php 
						$div_id = 1;
						$send_request_user_info_array = array();
						if ( !empty($this->mod_combind_path) ) {
							$dontHaveResult = 1;
							foreach($this->suggest_user as $user_info): ?>
					<?php $userIdsArray = in_array($user_info, $this->user_ids); ?>
					<div id="suggestion_friend_<?php echo $div_id; ?>" class="seaocore_popup_items">
						<?php $allFriendId[] = $user_info; ?>
						<?php if ($userIdsArray !== false): ?>
							<a id="check_<?php echo $user_info; ?>" href="javascript:void(0);">
								<?php $user_subject = Engine_Api::_()->user()->getUser($user_info);
								$getPhotoUrl = $user_subject->getPhotoUrl('thumb.icon');
								if( empty($getPhotoUrl) ) {
									$getPhotoUrl = $this->layout()->staticBaseUrl . 'application/modules/User/externals/images/nophoto_user_thumb_icon.png';
								}
								?>
								<span style="background-image: url(<?php echo $getPhotoUrl; ?>);">
									<span></span>
								</span>
								<p><?php	 echo $user_subject->getTitle(); ?></p>
							</a>
						<?php else : ?>
						<a class="suggestion_pop_friend <?php if(!empty($this->show_selected)){ echo 'selected'; } ?>" id="check_<?php echo $user_info; ?>" href="javascript:void(0);" onclick="moduleSelect('<?php echo $user_info; ?>');" >
							<?php //echo $user_info;die;
							$user_subject = Engine_Api::_()->user()->getUser($user_info);
							$getPhotoUrl = $user_subject->getPhotoUrl('thumb.icon');
							if( empty($getPhotoUrl) ) {
								$getPhotoUrl = $this->layout()->staticBaseUrl . 'application/modules/User/externals/images/nophoto_user_thumb_icon.png';
							}
							?>
							<span style="background-image: url(<?php echo $getPhotoUrl; ?>);">
								<span></span>
							</span>
							<p><?php	echo $user_subject->getTitle(); ?></p>
						</a>
						<?php endif; ?>
					</div>
					
					<?php
						$div_id++;
						endforeach;	
						} else { $dontHaveResult = 0; 
					?>
					<div class='tip' style="margin:10px">
						<span>
							<?php echo $this->translate('No Members were found to whom you can make this invitation.');
							?>
						</span>
					</div>
					<?php } ?>	
				</div>
			</div>
			
			
			<!--Member list end here-->
			<div class="popup_btm">
				<div class="fleft">
					<div id="check_error"></div>
					<?php if( !empty($changeButtonText) ){ $buttonText = $changeButtonText; }else { $buttonText = $this->translate("Send Invitations"); } ?>
					<button type='button' onClick='javascript:doCheckAll();'><?php echo $buttonText; ?></button>
					<?php echo $this->translate("or"); ?>
					<a href="javascript:void(0);" onclick="cancelPopup();"><?php echo $this->translate("Cancel"); ?></a>
				</div>
				<?php if( $this->members->count() > 1 ): ?>
					<div class="pagination">
						<?php if( $this->members->getCurrentPageNumber() > 1 ): ?>
							<div id="user_mod_members_previous" class="paginator_previous" style="font-weight:bold;">
								<?php echo $this->htmlLink('javascript:void(0);', $this->translate("&laquo; Prev"), array(
									'onclick' => 'paginateMembers(memberPage - 1);'
								)); ?>
							</div>
						<?php endif; ?>
						<?php if( $this->members->getCurrentPageNumber() < $this->members->count() ): ?>
							<div id="user_mod_members_next" class="paginator_next" style="font-weight:bold;">
								<?php echo $this->htmlLink('javascript:void(0);', $this->translate("Next &raquo;") , array(
									'onclick' => 'paginateMembers(memberPage + 1);'
								)); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				
			</div>	
		</form>  
	<?php if (!$this->search_true) {?>
	</div>
	<?php } 


if( !empty($this->getArray) ) {
foreach ( $this->getArray as $key => $value ) {
?>
	<script type="text/javascript">
		paginationArray['<?php echo $key; ?>'] = '<?php echo $value; ?>';
		dontHaveResult = '<?php echo $dontHaveResult; ?>';
	</script>
<?php
} }
	?>