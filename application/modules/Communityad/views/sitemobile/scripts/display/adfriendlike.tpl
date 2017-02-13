<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: adfriendlike.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
 var likeMemberPage = <?php if(empty($this->no_result_msg)){ echo sprintf('%d', $paginater_vari); } else { echo 1; } ?>;
 var call_status = '<?php echo $this->call_status; ?>';
 var resource_id = '<?php echo $this->resource_id; ?>';// Resource Id which are send to controller in the 'pagination' & 'searching'.
 var resource_type = '<?php echo $this->resource_type; ?>';// Resource Type which are send to controller in the 'pagination' & 'searching'.
 var resource_title  = '<?php echo $this->resource_title; ?>';
 var communityad_id  = '<?php echo $this->communityad_id; ?>';
	var likeMembers = function(call_status) {
		sm4.core.request.send({
			type: "GET", 
			dataType: "html", 
			url : sm4.core.baseUrl + 'communityad/display/adfriendlike',
			data: {
				'format':'html',
				'resource_type' : resource_type,
				'resource_id' : resource_id,
				'resource_title' : resource_title,
				'communityad_id' : communityad_id,
				'call_status' : call_status,
				'is_ajax':1
			}
		},{
			'element' : $.mobile.activePage.find("#like_members"),
			'showLoading': true
		}
		);
	};
</script>

<?php if(empty($this->is_ajax)):?>
<?php
	$viewer_id = $this->viewer()->getIdentity();
	if( !empty($this->getStoryStatus) && !empty($viewer_id) ) {
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
<?php endif;?>

<div id="like_members">
	<?php 
	if($this->resource_type == 'member') {
		$module_name = 'profile';
	} 
	elseif($this->resource_type == 'list_listing') {
		$module_name = 'list';
	} 
	else {
		$module_name = $this->resource_type;
	}
	if($this->call_status == 'public') {
		$title = Zend_Registry::get('Zend_Translate')->_('People Who Like This Ad');
		$title = sprintf($title, $this->resource_title);
	} else {
		$title = Zend_Registry::get('Zend_Translate')->_('Friends Who Like This Ad');
		$title = sprintf($title, $this->resource_title);
	} ?>
	<div class="top">
		<div class="heading"><strong><?php echo $title; ?></strong></div>
	</div>
	<div class="sm-content-list">
		<select name="auth_view" onchange="likeMembers($(this).val());" >
			<option value="public" <?php if($this->call_status == 'public'):?> selected="selected" <?php endif;?>><?php echo $this->translate("All") ?></option> 
			<option value="friend" <?php if($this->call_status == 'friend'):?> selected="selected" <?php endif;?> ><?php echo $this->translate("Friends") ?></option> 
		</select>
		<?php if($this->user_obj && $this->user_obj->getTotalItemCount() > 0): ?>
			<ul data-role="listview" data-icon="arrow-r">
				<?php foreach ($this->user_obj as $user): ?>
					<li>
						<a href="<?php echo $user->getHref(); ?>">
							<?php echo $this->itemPhoto($user, 'thumb.icon'); ?>
							<h3><?php echo $user->getTitle() ?></h3>
						</a> 
					</li>
				<?php endforeach; ?>
				<?php if ($this->user_obj->count() > 1): ?>
					<?php
						echo $this->paginationAjaxControl(
									$this->user_obj, 0, "like_members", array('call_status' => $this->call_status, 'url' => $this->url(array('module' => 'communityad', 'controller' => 'display', 'action' => 'adfriendlike', 'resource_id' => $this->resource_id, 'resource_type' => $this->resource_type, 'resource_title' => $this->resource_title, 'communityad_id' => $this->communityad_id), 'default', true)));
					?>
				<?php endif; ?>
			</ul>
		<?php else:?>
			<div class="tip">
				<span>
					<span><?php echo $this->no_result_msg;?></span>
				</span>
			</div>
		<?php endif; ?>
	</div>
</div>