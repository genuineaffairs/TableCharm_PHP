<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _communityad-pages.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php	
	include_once APPLICATION_PATH . '/application/modules/Communityad/views/scripts/help-pages/_communityad-help-navigation.tpl';
?>
<div class="cmad_halm_tabs_content" id="dynamic_app_info">
<?php 
		if( !empty($this->pageObject) ) {
      if ( empty($this->display_faq) && empty($click_responce) && empty($this->page_id) ) {
				echo $this->pageObject[0]['description'];
			}else {
				if( !empty($this->content_data) ) {
					echo $this->content_data;
				}
			}
			if( !empty($this->viewFaq) ) { ?>
			<div class="cmad_halmc_form">
				<div>
					<ul class="communityad_faq" style="max-height:1000px;">
						<?php
							$faqId = 1;
							foreach ( $this->viewFaq as $fetchFAQ ) {
								// For: Display Questions.
								echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_'.$faqId.'\');">' . $this->translate("Qus: ") . $fetchFAQ['question'] . '</a>';
								// For: Display Answers for the Questions.
								echo '<div class="faq" style="display: none;" id="faq_'.$faqId.'">' . $this->translate("Ans: ") . $fetchFAQ['answer'] . '</div></li>';
								$faqId++;
							}
						?>
					</ul>
				</div>
			</div>	
			<?php } else if ( !empty($this->contactTeam) ) { ?>
			<div class="cmad_halmc_form">
				<div>
					<p><?php echo $this->translate("Contact us for any assistance that you may require with advertising on this community.");?></p>
					<div class="form-wrapper">
						<div class="form-label">
							<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/mobile.png" alt="" />
							<?php echo $this->translate("Sales Team Contact Number:"); ?>
						</div>
						<div class="form-element">
							<?php echo $this->contactTeam['numbers']; ?>
						</div>
					</div>	
					
					<div class="form-wrapper">
						<div class="form-label">
							<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/mail.png" alt="" />
							<?php echo $this->translate("Sales Team Contact Email:"); ?>
						</div>
						<div class="form-element">
							<?php echo $this->contactTeam['emails']; ?>
						</div>
					</div>	
					<div class="form-wrapper">
						<div class="form-label">
							<?php echo '<a href="'.$this->url(array('module' => 'communityad', 'controller' => 'display', 'action' => 'send-messages'), 'default', true).'" onclick="Smoothbox.open(this.href);return false;" class="cmad_icon_type_contact buttonlink">Contact us</a>'; ?>
						</div>
					</div>
				</div>
			</div>	
				<?php
				}
		} else {
			echo '<div class="tip" style="margin:10px 0 0 270px;"><span>' . $this->translate('No items could be found.') . '</span></div>';
		} ?>
</div>

<style type="text/css">
.cmad_halmc_form .form-wrapper
{
	clear:both;
	margin-top:20px;
	overflow:auto;
}
.cmad_halmc_form .form-wrapper .form-label
{
	float:left;
	font-weight:bold;
	width:230px;
}
.cmad_halmc_form .form-wrapper .form-label img
{
	float:left;
	margin-right: 5px;
}
.cmad_halmc_form .form-wrapper .form-element
{
	float:left;
	padding-left:0px;
	max-width:400px;
	overflow:hidden;
}
</style>