<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<ul>
	<li>
		<b style="font-weight:bold;font-size:12px;"> <?php echo $this->translate('Advertise on '). $this->site_title ?></b>
		<div class="cont" style="padding:5px 0;">
			<?php echo $this->itemPhoto($this->viewer_object, 'thumb.icon', '' , array('align'=>'left'))."<b>". $this->viewer_object->getTitle(). "</b>, ". $this->site_title. "\t". $this->translate('Ads enable you to easily advertise your offering in a highly effective way. It is easy to use, so get started now!'); ?>
		</div>
		<div class="cmad_hr_link" style="float:left;clear:both;">
			<?php $create_ad_url = $this->url(array(), 'communityad_listpackage', true); ?>
			<a href="<?php echo $create_ad_url; ?>" style="padding:5px;"><?php echo $this->translate("Create an Ad"); ?> &raquo;</a>
		</div>
	</li>
</ul>