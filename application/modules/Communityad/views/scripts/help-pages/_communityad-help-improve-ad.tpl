<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _communityad-help-improve-ad.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php	
	include_once APPLICATION_PATH . '/application/modules/Communityad/views/scripts/help-pages/_communityad-help-navigation.tpl';?>

<div class="cmad_halm_tabs_content" id="dynamic_app_info">
	<div class="cmad_halmc_form">
		<div>
			<div class="cmadd_hlm">
				<div class="cadcomp_vad_header">
					<h3><?php echo $this->translate('Improve Your Ads');?></h3>
			   	<?php if(Engine_Api::_()->communityad()->enableCreateLink()) : ?>
						<div class="cmad_hr_link">
							<?php $create_ad_url = $this->url(array(), 'communityad_listpackage', true); ?>
							<a href="<?php echo $create_ad_url; ?>"><?php echo $this->translate("Create an Ad"); ?> &raquo;</a>
						</div>
					<?php endif;?>
				</div>
				<p><?php echo $this->translate('_communityad_help_improve_ad_1');?></p>	
				<p><?php echo $this->translate('_communityad_help_improve_ad_2');?></p>
				
				<b class="headlinetxt"><?php echo $this->translate('_communityad_help_improve_ad_3'); ?></b>
				<div style="padding:0 5px;">
					<p><?php echo $this->translate('_communityad_help_improve_ad_4');?></p>	
					<p><?php echo $this->translate('_communityad_help_improve_ad_5');?></p>
					<p><?php echo $this->translate('_communityad_help_improve_ad_6');?></p>
				</div>
				<b class="headlinetxt"><?php echo $this->translate('_communityad_help_improve_ad_7'); ?></b>
				<div style="padding:0 5px;">
					<p><?php echo $this->translate('_communityad_help_improve_ad_8');?></p>	
					<p><?php echo $this->translate('_communityad_help_improve_ad_9');?></p>
				</div>
				
				<b class="headlinetxt"><?php echo $this->translate('_communityad_help_improve_ad_10'); ?></b>
				<div style="padding:0 5px;">
					<p><?php echo $this->translate('_communityad_help_improve_ad_11');?></p>	
					<p><?php echo $this->translate('_communityad_help_improve_ad_12');?></p>
				</div>
				
				<b class="headlinetxt"><?php echo $this->translate('_communityad_help_improve_ad_13'); ?></b>
				<div style="padding:0 5px;">
					<p><?php echo $this->translate('_communityad_help_improve_ad_14');?></p>	
					<p><?php echo $this->translate('_communityad_help_improve_ad_15');?></p>
				</div>
				<p><?php
					$url = $this->url(array('page_id' => $contact_team), 'communityad_help', true);
					echo $this->translate('_communityad_help_improve_ad_16') . ' <a href="' . $url . '">'. $this->translate('Contact our Sales Team') . '</a>.'
				?></p>
			</div>
		</div>
	</div>		
</div>