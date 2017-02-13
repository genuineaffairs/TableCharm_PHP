<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manageadmins.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript" >
  var submitformajax = 1;
  var manage_admin_formsubmit = 1;
</script>
<script type="text/javascript">
  var url = '<?php  echo $this->url(array(), 'sitepage_general', true) ?>';
</script>
  <script type="text/javascript" >
	function owner(thisobj) {
		var Obj_Url = thisobj.href ;
		Smoothbox.open(Obj_Url);
	}
</script>
<?php if (empty($this->is_ajax)) : ?>
	<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
	<div class="layout_middle">
		<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
		<div class="sitepage_edit_content">
			<div class="sitepage_edit_header">
				<?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($this->sitepage->page_id, $this->sitepage->owner_id, $this->sitepage->getSlug()),$this->translate('VIEW_PAGE')) ?>
				<h3><?php echo $this->translate('Dashboard: ').$this->sitepage->title; ?></h3>
			</div>
		  <div id="show_tab_content">
<?php endif; ?> 
		<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/scripts/core.js'); ?>
		<div class="sitepage_form">
			<div>
				<div>
					<div class="sitepage_manage_member_role">
						<h3> <?php echo $this->translate('Manage Member Roles'); ?> </h3>
						<?php $manageMemberSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.category.settings', 1);
						if ($manageMemberSettings == 2) : ?>
						<p class="form-description"><?php echo $this->translate("Below you can see all the member roles added by you, which members of your page can choose. You can also add new member roles and delete them.") ?></p>
						<?php else : ?>
						<p class="form-description"><?php echo $this->translate("Below you can see all the member roles added by you and our site administrators, which members of your page can choose. You can also add new member roles and delete them. Note that you can only delete the roles created by you.") ?></p>
						<?php endif; ?>
						<br />
						<?php if (count($this->manageRolesHistories) > 0) : ?>
            <div class="sitepage_manage_member_role_list clr">
            	<div class="fleft"><b><?php echo  $this->translate("Role") ?></b></div>
							<div class="sitepage_manage_member_role_list_option fright"><b><?php echo $this->translate("Options") ?></b></div>
						</div>
						<?php endif; ?>
						<?php foreach ($this->manageRolesHistories as $item):?>
							<div id='<?php echo $item->role_id ?>_page' class="sitepage_manage_member_role_list clr">
								<div class="fleft"><?php echo $item->role_name; ?></div>
								<?php if (empty($item->is_admincreated)) : ?>
									<div class="sitepage_manage_member_role_list_option fright">
										<?php $url = $this->url(array('action' => 'delete-member-category'), 'sitepage_dashboard', true);?>
											<a href="javascript:void(0);" onclick="deleteMemberCategory('<?php echo $item->role_id?>', '<?php echo $url;?>', '<?php echo $this->page_id ?>')"; ><?php echo $this->translate('Delete Member Role');?></a>
											| 	<?php if (!empty($this->is_ajax)) : ?>
													<?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'action' => 'edit-role', 'role_id' => $item->role_id, 'page_id' => $this->page_id), $this->translate('Edit Member Role'), array(' class' => 'smoothbox', 'onclick' => 'owner(this);return false')); ?>
													<?php else : ?>
													<?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'action' => 'edit-role', 'role_id' => $item->role_id, 'page_id' => $this->page_id), $this->translate('Edit Member Role'), array(' class' => 'smoothbox')); ?>
												<?php endif; ?>
									</div>
									<?php else: ?>
									<div class="sitepage_manage_member_role_list_option fright">
									<?php echo $this->translate('Delete Member Role'); ?>
									</div>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
						<br />	<br />	
            <?php  $item = count($this->paginator) ?>
            <input type="hidden" id='count_div' value='<?php echo $item ?>' />
            <form id='video_selected' method='post' class="global_form mtop10" action='<?php echo $this->url(array('action' => 'manage-member-category', 'page_id' => $this->page_id), 'sitepage_dashboard') ?>'>
              <div class="fleft">
                <div>
                  <?php if (!empty($this->message)): ?>
                  <div class="tip">
                    <span>
                      <?php echo $this->message; ?>
                    </span>
                  </div>
                  <?php  endif;?>
                  <div class="sitepage_manageadmins_input">
                  <?php echo $this->translate("Enter the member role name.") ?> <br />	
                    <input type="text" id="category_name" name="category_name" value="" />
                    <input type="hidden" id="user_id" name="user_id" />
                  </div>
                  <div class="sitepage_manageadmins_button">	
                    <button type="submit"  name="submit"><?php echo $this->translate("Add Member Role") ?></button>
                  </div>	
                </div>
              </div>
            </form>
           </div> 
				</div>
			</div>
		</div>
		<br />	
		<div id="show_tab_content_child">
		</div>
<?php if (empty($this->is_ajax)) : ?>
		  </div>
	  </div>
  </div>
<?php endif; ?>
<style type="text/css">
.global_form > div > div{background:none;border:none;padding:0px;}
</style>