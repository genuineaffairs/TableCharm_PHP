<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _communityad-help-faq.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php	

	include_once APPLICATION_PATH . '/application/modules/Communityad/views/scripts/help-pages/_communityad-help-navigation.tpl';?>

<div class="cmad_halm_tabs_content" id="dynamic_app_info">
	<div class="cmad_halmc_form">
		<div>
			<ul class="communityad_faq">
				<?php
				
				if( $this->page_id == 100 ) {  ?>
					<div class="cadcomp_vad_header">
						<h3><?php echo $this->translate('Sponsored Stories');?></h3>
				   	<?php if(Engine_Api::_()->communityad()->enableCreateLink()) : ?>
							<div class="cmad_hr_link">
								<?php $create_ad_url = $this->url(array(), 'communityad_listpackage', true); ?>
								<a href="<?php echo $create_ad_url; ?>"><?php echo $this->translate("Create an Ad"); ?> &raquo;</a>
							</div>
						<?php endif;?>
					</div>
					<?php 
						echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_1\');">' . $this->translate("Q: ") . $this->translate("_communityad_help_sponsored_story_qus_1") . '</a>';
						echo '<div class="faq" style="display: none;" id="faq_1">' . $this->translate("Ans: ") . $this->translate("_communityad_help_sponsored_story_ans_1") . '</div></li>';
				
						echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_2\');">' . $this->translate("Q: ") . $this->translate("_communityad_help_sponsored_story_qus_3") . '</a>';
						echo '<div class="faq" style="display: none;" id="faq_2">' . $this->translate("Ans: ") . $this->translate("_communityad_help_sponsored_story_ans_3") . '</div></li>';
				
						echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_3\');">' . $this->translate("Q: ") . $this->translate("_communityad_help_sponsored_story_qus_4") . '</a>';
						echo '<div class="faq" style="display: none;" id="faq_3">' . $this->translate("Ans: ") . $this->translate("_communityad_help_sponsored_story_ans_4") . '</div></li>';
				
						echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_4\');">' . $this->translate("Q: ") . $this->translate("_communityad_help_sponsored_story_qus_5") . '</a>';
						echo '<div class="faq" style="display: none;" id="faq_4">' . $this->translate("Ans: ") . $this->translate("_communityad_help_sponsored_story_ans_5") . '</div></li>';
				
						echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_5\');">' . $this->translate("Q: ") . $this->translate("_communityad_help_sponsored_story_qus_6") . '</a>';
						echo '<div class="faq" style="display: none;" id="faq_5">' . $this->translate("Ans: ") . $this->translate("_communityad_help_sponsored_story_ans_6") . '</div></li>';
				
						echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_6\');">' . $this->translate("Q: ") . $this->translate("_communityad_help_sponsored_story_qus_7") . '</a>';
						echo '<div class="faq" style="display: none;" id="faq_6">' . $this->translate("Ans: ") . $this->translate("_communityad_help_sponsored_story_ans_7") . '</div></li>';									
						$faqId = 8;
				}
				?>
			</ul>
		</div>
	</div>
</div>
<script type="text/javascript">
  function faq_show(id) {
    if($(id).style.display == 'block') {
        $(id).style.display = 'none';
    } else {
        $(id).style.display = 'block';
    }
  }
</script>