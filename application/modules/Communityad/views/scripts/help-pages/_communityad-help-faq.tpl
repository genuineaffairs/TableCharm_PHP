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
				
				if( !empty($this->viewFaq) ) {
					$faqType = $this->viewFaq[0]['type'];
					// Condition: Showing the 'General FAQ'.
					if( $faqType == 1 ) { ?>
					<div class="cadcomp_vad_header">
						<h3><?php echo $this->translate('General FAQ');?></h3>
				   	<?php if(Engine_Api::_()->communityad()->enableCreateLink()) : ?>
							<div class="cmad_hr_link">
								<?php $create_ad_url = $this->url(array(), 'communityad_listpackage', true); ?>
								<a href="<?php echo $create_ad_url; ?>"><?php echo $this->translate("Create an Ad"); ?> &raquo;</a>
							</div>
						<?php endif;?>
					</div>
					<?php
						echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_1\');">' . $this->translate("Q: ") . $this->translate("_communityad_help_generalfaq_1") . '</a>';

						echo '<div class="faq" style="display: none;" id="faq_1">' . $this->translate("Ans: ") . $this->translate("_communityad_help_generalfaq_2") . '</div></li>';

						echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_16\');">' . $this->translate("Q: ") . $this->translate("_communityad_help_generalfaq_16") . '</a>';
						echo '<div class="faq" style="display: none;" id="faq_16">' . $this->translate("Ans: ") . $this->translate("_communityad_help_generalfaq_17") . '</div></li>';
				
						echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_2\');">' . $this->translate("Q: ") . $this->translate("_communityad_help_generalfaq_3") . '</a>';
						echo '<div class="faq" style="display: none;" id="faq_2">' . $this->translate("Ans: ") . $this->translate("_communityad_help_generalfaq_4") . '</div></li>';
				
						echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_3\');">' . $this->translate("Q: ") . $this->translate("_communityad_help_generalfaq_5") . '</a>';
						echo '<div class="faq" style="display: none;" id="faq_3">' . $this->translate("Ans: ") . $this->translate("_communityad_help_generalfaq_6") . '</div></li>';
				
						echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_4\');">' . $this->translate("Q: ") . $this->translate("_communityad_help_generalfaq_7") . '</a>';
						echo '<div class="faq" style="display: none;" id="faq_4">' . $this->translate("Ans: ") . $this->translate("_communityad_help_generalfaq_8") . '</div></li>';
				
						echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_5\');">' . $this->translate("Q: ") . $this->translate("_communityad_help_generalfaq_9") . '</a>';
						echo '<div class="faq" style="display: none;" id="faq_5">' . $this->translate("Ans: ") . $this->translate("_communityad_help_generalfaq_10") . '</div></li>';
				
						echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_6\');">' . $this->translate("Q: ") . $this->translate("_communityad_help_generalfaq_11") . '</a>';
						echo '<div class="faq" style="display: none;" id="faq_6">' . $this->translate("Ans: ") . $this->translate("_communityad_help_generalfaq_12") . '</div></li>';
				
						echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_7\');">' . $this->translate("Q: ") . $this->translate("_communityad_help_generalfaq_13") . '</a>';
						echo '<div class="faq" style="display: none;" id="faq_7">' . $this->translate("Ans: ") . $this->translate("_communityad_help_generalfaq_14") . '</div></li>';
						$faqId = 8;
					}else if( $faqType == 2 ) { // Condition: Showing the 'Design Your FAQ'. ?>
					
						
					<div class="cadcomp_vad_header">
						<h3><?php echo $this->translate('Design Your Ad FAQ');?></h3>
				   	<?php if(Engine_Api::_()->communityad()->enableCreateLink()) : ?>
							<div class="cmad_hr_link">
								<?php $create_ad_url = $this->url(array(), 'communityad_listpackage', true); ?>
								<a href="<?php echo $create_ad_url; ?>"><?php echo $this->translate("Create an Ad"); ?> &raquo;</a>
							</div>
						<?php endif;?>
					</div>		
					<?php	
						echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_1\');">' . $this->translate("Q: ") . $this->translate("_communityad_help_designfaq_1") . '</a>';

						echo '<div class="faq" style="display: none;" id="faq_1">' . $this->translate("Ans: ") . $this->translate("_communityad_help_designfaq_2") . '</div></li>';
				
						echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_2\');">' . $this->translate("Q: ") . $this->translate("_communityad_help_designfaq_3") . '</a>';
						echo '<div class="faq" style="display: none;" id="faq_2">' . $this->translate("Ans: ") . $this->translate("_communityad_help_designfaq_4") . '</div></li>';
				
						echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_3\');">' . $this->translate("Q: ") . $this->translate("_communityad_help_designfaq_5") . '</a>';
						echo '<div class="faq" style="display: none;" id="faq_3">' . $this->translate("Ans: ") . $this->translate("_communityad_help_designfaq_6") . '</div></li>';
				
						echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_4\');">' . $this->translate("Q: ") . $this->translate("_communityad_help_designfaq_7") . '</a>';
						echo '<div class="faq" style="display: none;" id="faq_4">' . $this->translate("Ans: ") . $this->translate("_communityad_help_designfaq_8") . '</div></li>';
				
						echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_5\');">' . $this->translate("Q: ") . $this->translate("_communityad_help_designfaq_9") . '</a>';
						echo '<div class="faq" style="display: none;" id="faq_5">' . $this->translate("Ans: ") . $this->translate("_communityad_help_designfaq_10") . '</div></li>';
						$faqId = 6;
					}else if( $faqType == 3 ) { ?>
					
					<div class="cadcomp_vad_header">
						<h3><?php echo $this->translate('Targeting FAQ');?></h3>
				   	<?php if(Engine_Api::_()->communityad()->enableCreateLink()) : ?>
							<div class="cmad_hr_link">
								<?php $create_ad_url = $this->url(array(), 'communityad_listpackage', true); ?>
								<a href="<?php echo $create_ad_url; ?>"><?php echo $this->translate("Create an Ad"); ?> &raquo;</a>
							</div>
						<?php endif;?>
					</div>						
					<?php
						echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_1\');">' . $this->translate("Q: ") . $this->translate("_communityad_help_targetingfaq_1") . '</a>';

						echo '<div class="faq" style="display: none;" id="faq_1">' . $this->translate("Ans: ") . $this->translate("_communityad_help_targetingfaq_2") . '</div></li>';
				
						echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_2\');">' . $this->translate("Q: ") . $this->translate("_communityad_help_targetingfaq_3") . '</a>';
						echo '<div class="faq" style="display: none;" id="faq_2">' . $this->translate("Ans: ") . $this->translate("_communityad_help_targetingfaq_4") . '</div></li>';
				
						echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_3\');">' . $this->translate("Q: ") . $this->translate("_communityad_help_targetingfaq_5") . '</a>';
						echo '<div class="faq" style="display: none;" id="faq_3">' . $this->translate("Ans: ") . $this->translate("_communityad_help_targetingfaq_6") . '</div></li>';
						$faqId = 4;
					}
				
					// If site-admin ad any faqs then display from here.
					foreach ( $this->viewFaq as $fetchFAQ ) {
						if( empty($fetchFAQ['faq_default']) ) {
							// For: Display Questions.
							echo '<li><a href="javascript:void(0);" onClick="faq_show(\'faq_'.$faqId.'\');">' . $this->translate("Q: ") . $fetchFAQ['question'] . '</a>';
							// For: Display Answers for the Questions.
							echo '<div class="faq" style="display: none;" id="faq_'.$faqId.'">' . $this->translate("Ans: ") . $fetchFAQ['answer'] . '</div></li>';
							$faqId++;
						}
					}
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