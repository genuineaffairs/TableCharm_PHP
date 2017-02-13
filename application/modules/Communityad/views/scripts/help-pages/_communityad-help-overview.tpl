<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _communityad-help-overview.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
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
					<h3><?php echo $this->translate('Overview');?></h3>
			   	<?php if(Engine_Api::_()->communityad()->enableCreateLink()) : ?>
						<div class="cmad_hr_link">
							<?php $create_ad_url = $this->url(array(), 'communityad_listpackage', true); ?>
							<a href="<?php echo $create_ad_url; ?>"><?php echo $this->translate("Create an Ad"); ?> &raquo;</a>
						</div>
					<?php endif;?>
				</div>
				<div class="cmadd_hlm_overview">
					<div>
						<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/target_img.gif" alt=""/>
					</div>
					<div>
						<b class="headlinetxt"><?php echo $this->translate('_communityad_help_overview_1'); ?></b>
						<ul>
							<li><?php echo $this->translate('_communityad_help_overview_2');?></li>
							<li><?php echo $this->translate('_communityad_help_overview_3');?></li>
							<li><?php echo $this->translate('_communityad_help_overview_4');?></li>
						</ul>
					</div>
				</div>
				
				<div class="cmadd_hlm_overview">
					<div>
						<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/rel_img.gif" alt=""/>
					</div>
					<div>
						<b class="headlinetxt"><?php echo $this->translate('_communityad_help_overview_5'); ?></b>
						<ul>
							<li><?php echo $this->translate('_communityad_help_overview_6');?></li>
							<li><?php echo $this->translate('_communityad_help_overview_7');?></li>
							<li><?php echo $this->translate('_communityad_help_overview_8');?></li>
						</ul>
					</div>
				</div>
		
				<div class="cmadd_hlm_overview">
					<div>
						<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/budget_img.gif" alt=""/>
					</div>	
					<div>
						<b class="headlinetxt"><?php echo $this->translate('_communityad_help_overview_9'); ?></b>
						<ul>
							<li><?php echo $this->translate('_communityad_help_overview_10');?></li>
							<li><?php echo $this->translate('_communityad_help_overview_11');?></li>
							<li><?php echo $this->translate('_communityad_help_overview_12');?></li>
						</ul>
					</div>
				</div>		
				<p><?php
					$url = $this->url(array('page_id' => $contact_team), 'communityad_help', true);
					echo $this->translate('_communityad_help_overview_13') . ' <a href="' . $url . '">'. $this->translate('Contact our Sales Team') . '</a>.'
				?></p>
			</div>
		</div>
	</div>		
</div>