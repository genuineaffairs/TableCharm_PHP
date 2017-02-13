<?php ?>
<!-- Start Preview Code -->
    <div class="cmaddis_preview_wrapper" style="margin-bottom:15px;">
	    <b><?php echo $this->translate('Sample Ad') ?></b>
	    <div class="cadcp_preview">
	    	<div class="cmaddis">
					<div class="cmad_addis">

					<div class="cmad_show_tooltip_wrapper">  
						<div class="cmaddis_title">
							<?php
								if( !empty($this->module_type) && !empty($this->module_type_id) ) {
									$resource_url = Engine_Api::_()->communityad()->resourceUrl( $this->module_type, $this->module_type_id );
								}
								$set_target = 'target="_blank"';
								if($this->module_name == 'Sitebusiness') {
									$target_url = $this->url(array('business_url' => Engine_Api::_()->sitebusiness()->getBusinessUrl($this->module_type_id)), 'sitebusiness_entry_view', true);
								}
								else {
									$target_url = $resource_url['link'];
								}
								$title_field = $this->info['title_field'];
								echo '<a href="'. $target_url  .'" '.$set_target.'>' . ucfirst($this->module_subject->$title_field) . "</a>";
							?>
						</div>
						<div class="cmaddis_adinfo">
								<?php 

										if( !empty($resource_url['status']) ) {
											echo '<a href="'. $target_url  .'">' . $resource_url['title'] . "</a>";
										}
								?>
						</div>
						<div class="cmad_show_tooltip">
							<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/tooltip_arrow.png" />
							<?php echo $this->translate("Ad title linked to the ad destination URL.");?>
						</div>
					</div>
						
					<div class="cmad_show_tooltip_wrapper">
						<!--image code start here-->
						<div class="cmaddis_image">
							<?php 
								$no_img = 0;
								if(isset($this->module_subject->photo_id) && !empty($this->module_subject->photo_id)) {
										echo '<a href="'. $target_url .'" '.$set_target.'>' .  $this->itemPhoto($this->module_subject, 'thumb.normal', '' , array()) . "</a>";
								}
								else {
									$path = $this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/blankImage.png';
									$content_photo = '<img src="' . $path . '" alt=" " />';
									echo '<a href="'. $target_url .'" '.$set_target.'>' .  $content_photo . "</a>";
								}
							?>
						</div>
						<!--image code end here for both-->
						<div class="cmad_show_tooltip">
							<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/tooltip_arrow.png" />
							<?php echo $this->translate("Ad image linked to the ad destination URL.");?>
						</div>
					</div>
					
					<div class="cmad_show_tooltip_wrapper">
						<!--description code start here-->
						<div class="cmaddis_body">	
							<?php
								echo '<a href="'. $target_url  .'" '.$set_target.'>' . $this->translate("The description of your Ad will go here.") . "</a>"; 
							?>
						</div>
						<!--description code end here for both-->
						<div class="cmad_show_tooltip">
							<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/tooltip_arrow.png" />
							<?php echo $this->translate("Ad body text linked to the ad destination URL.");?>
						</div>
					</div>

						<!-- Like option only show in the case of if existence on the site -->
						<div class="cmad_show_tooltip_wrapper">
							<?php echo '<div class="cmaddis_cont" style="display:block;margin-top:5px;"><a href="javascript:void(0);" class="cmad_like_button"><i class="like_thumbup_icon"></i><span>'. $this->translate("Like"). '</span></a><span class="cmad_like_un">&nbsp;&middot;&nbsp;<a href="javascript:void(0);">' . $this->get_title . '</a>' . $this->translate(' likes this.') . '</span></div>'; ?>
							<div class="cmad_show_tooltip">
								<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/tooltip_arrow.png" />
								<?php echo $this->translate("Viewers will be able to like this ad and its content. They will also be able to see how many people like this ad, and which friends like this ad.");?>
							</div>
						</div>
						
				</div>
			</div>
		</div>
	</div>
<style type="text/css">
.cmaddis_image img{
	max-width: <?php echo $this->createWidth ?>px;
	max-height: <?php echo $this->createHeight ?>px;
}
</style>