<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: marketing.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript" >

function owner(thisobj) {
	var Obj_Url = thisobj.href  ;

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
      <?php $showMessage=true;?>
			<div class="global_form">
				<div>
					<div>
						<h3><?php echo $this->translate('Marketing your Page'); ?></h3>
						<p class="form-description"><?php echo $this->translate('Below are some effective tools to market your Page and increase its popularity.'); ?></p>
						<div class="sitepage_marketing mtop10">
							<ul class="sitepage_getstarted">
		      			<?php $sitepagecommunityadEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad'); ?>

								<?php								// check if it is upgraded version
									$updated_ads = 0;
									$adversion = null;
									if($sitepagecommunityadEnabled) {
										$communityadmodulemodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('communityad');
										$adversion = $communityadmodulemodule->version;
										if($adversion >= '4.1.5') {
												$updated_ads = 1;
										}
									}
								?>
								<?php if($sitepagecommunityadEnabled && $updated_ads): ?>
									<li><?php $showMessage=false;?>
										<div class="sitepage_getstarted_num">
											<?php echo $this->htmlLink(array('route' => 'communityad_listpackage', 'type' => 'sitepage', 'type_id' => $this->page_id), '<img alt="" src="'. $this->layout()->staticBaseUrl .'application/modules/Sitepage/externals/images/advertise.png" />') ?>

						    		</div>
						    		<div class="sitepage_getstarted_des">
						      		<b>
												<?php $site_title =  Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 'Advertisement')?>
												<?php echo $this->htmlLink(array('route' => 'communityad_listpackage', 'type' => 'sitepage', 'type_id' => $this->page_id), $this->translate("Advertise on %s", $site_title)) ?>
											</b>
											<p><?php echo $this->translate('Popularize your Page on %s with an attractive ad.', $site_title) ?></p>
										</div>
									</li>
								<?php endif; ?>

								<?php $sitepageinviteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageinvite'); ?>
								<?php if($sitepageinviteEnabled && !empty($this->enableInvite) ): ?>
									<li><?php $showMessage=false;?>
										<div class="sitepage_getstarted_num">
											<?php echo $this->htmlLink(array('route' => 'sitepageinvite_invite', 'user_id' => $this->viewer_id, 'sitepage_id' => $this->page_id), '<img alt="" src="'. $this->layout()->staticBaseUrl .'application/modules/Sitepage/externals/images/friends.png" />') ?>

						    		</div>
						    		<div class="sitepage_getstarted_des">
						      		<b>
												<?php echo $this->htmlLink(array('route' => 'sitepageinvite_invite', 'user_id' => $this->viewer_id, 'sitepage_id' => $this->page_id), $this->translate('Invite &amp; Promote')) ?>
											</b>
											<p><?php echo $this->translate('Tell your friends, fans and customers about this page and make it popular.') ?></p>
										</div>
									</li>
								<?php endif;?>
                <?php if(!empty($this->enableSendUpdate)):?>
								<li><?php $showMessage=false;?>
									<div class="sitepage_getstarted_num">
										<?php echo $this->htmlLink(array('route' => 'sitepage_like', 'page_id' => $this->page_id, 'action' => 'send-update'), '<img alt="" src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/message48.png" />', array('onclick' => 'owner(this);return false')) ?>
					    		</div>
					    		<div class="sitepage_getstarted_des">
					      		<b>
											<?php echo $this->htmlLink(array('route' => 'sitepage_like', 'page_id' => $this->page_id, 'action' => 'send-update'), $this->translate('Send an Update'), array('onclick' => 'owner(this);return false')) ?>
										</b>
										<p><?php echo $this->translate('Send updates to people who have liked your Page.') ?></p>
									</div>
								</li>
                <?php endif; ?>
                <?php if(!empty($this->enableFoursquare)):?>
								<li><?php $showMessage=false;?>
									<div class="sitepage_getstarted_num">
										<?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'page_id' => $this->page_id, 'action' => 'foursquare'), '<img alt="" src="'. $this->layout()->staticBaseUrl  . 'application/modules/Sitepage/externals/images/foursquare.png" />', array('onclick' => 'owner(this);return false')) ?>
					    		</div>
					    		<div class="sitepage_getstarted_des">
					      		<b>
											<?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'page_id' => $this->page_id, 'action' => 'foursquare'), $this->translate("'Save to foursquare' Button"), array('onclick' => 'owner(this);return false')) ?>
										</b>
										<p><?php echo $this->translate('This button will enable visitors on your Page to add your place or tip to their foursquare To-Do List.') ?></p>
									</div>
								</li>
               <?php endif; ?>
								<?php $sitepagelikebox_isActivate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagelikebox.isActivate', null); ?>
               <?php if(!empty($this->enableLikeBox) && !empty($sitepagelikebox_isActivate)):?>
									<li><?php $showMessage=false;?>
										<div class="sitepage_getstarted_num">
											<?php echo $this->htmlLink(array('route' => 'sitepagelikebox_general', 'page_id' => $this->page_id, 'action' => 'like-box'), '<img alt="" src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/likebox.png" />', array()) ?>
										</div>
										<div class="sitepage_getstarted_des">
											<b>
												<?php echo $this->htmlLink(array('route' => 'sitepagelikebox_general', 'page_id' => $this->page_id, 'action' => 'like-box'), $this->translate("Promote this Page on your external blogs or websites"), array()) ?>
											</b>
											<p><?php echo $this->translate("Attract people to your Page and gain popularity by using attractive embeddable badge of your Page which can be shared across the web.",Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title')) ?></p>
										</div>
									</li>
              <?php endif; ?>
							<?php if($this->enabletwitter):?>
								<li><?php $showMessage=false;?>
									<div class="sitepage_getstarted_num">
										<?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'page_id' => $this->page_id, 'action' => 'twitter'), '<img alt="" src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepagetwitter/externals/images/twitter.png" />', array('onclick' => 'owner(this);return false')) ?>
					    		</div>
					    		<div class="sitepage_getstarted_des">
					      		<b>
											<?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'page_id' => $this->page_id, 'action' => 'twitter'), $this->translate("Add Twitter Profile Widget"), array('onclick' => 'owner(this);return false')) ?>
										</b>
										<p><?php echo $this->translate('Attract people and involve them in your conversation on Twitter by displaying your recent tweets.') ?></p>
									</div>
								</li>
               <?php endif; ?>
               
                                <?php 
                                $advfeedmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('advancedactivity');
                                
                      if(!empty($this->fblikebox_id) || (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postfbpage', 1) && !empty ($advfeedmodule) && !empty($advfeedmodule->enabled) && $advfeedmodule->version > '4.2.5' )):?>
								<li><?php $showMessage=false;?>
									<div class="sitepage_getstarted_num">
										<?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'page_id' => $this->page_id, 'action' => 'facebook', 'fblikebox_id' => $this->fblikebox_id), '<img alt="" src="'.$this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/sitepage-facebook.png" />', array('onclick' => 'owner(this);return false')) ?>
					    		</div>
					    		<div class="sitepage_getstarted_des">
					      		<b>
											<?php echo $this->htmlLink(array('route' => 'sitepage_dashboard', 'page_id' => $this->page_id, 'action' => 'facebook', 'fblikebox_id' => $this->fblikebox_id), $this->translate("Link your Page to Facebook"), array('onclick' => 'owner(this);return false')) ?>
										</b>
										<p><?php 
										$description_fblikebox = '';
										         if (!empty ($advfeedmodule) && !empty($advfeedmodule->enabled) && $advfeedmodule->version > '4.2.5'  && !empty($this->fblikebox_id))
																$description_fblikebox = "Publish your Page's updates to your Facebook Page and show your Facebook Page Like Box on your Page.";
																
														 else if (!empty($this->fblikebox_id))
																$description_fblikebox = "Show your Facebook Page Like Box on your Page.";
																
																
														 else if (!empty ($advfeedmodule) && !empty($advfeedmodule->enabled) && $advfeedmodule->version > '4.2.5' ) 
																$description_fblikebox = "Publish your Page's updates to your Facebook Page."; 
										echo $this->translate($description_fblikebox) ?></p>
									</div>
								</li>
						<?php endif; ?>		
             
             <?php if($showMessage):?>
                <li>
                  <div class="tip">
                    <span>
                  <?php  if (Engine_Api::_()->sitepage()->hasPackageEnable()):?>
									<?php echo $this->translate("In this package not any effective tools available to market your Page.");?>
                  <?php else:?>
                  	<?php echo $this->translate("For this level user not any effective tools available to market your Page.");?>
                  <?php endif; ?>
                    </span>
                  </div>
								</li>
              <?php endif; ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
	<?php if (empty($this->is_ajax)) : ?>
		</div>
	</div>
  </div>
<?php endif; ?>
