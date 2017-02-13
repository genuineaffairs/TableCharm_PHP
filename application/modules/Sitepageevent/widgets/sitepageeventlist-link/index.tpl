<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepageevent/externals/styles/style_sitepageevent.css');
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<div class="quicklinks layout_sitepagecontent_link">
	<ul>
		<li>
			<?php echo $this->htmlLink(array('route' => 'sitepageevent_browse'), $this->translate('Browse Events'), array('class' => 'buttonlink item_icon_sitepageevent_event')) ?>
		</li>
		<li>
			<?php echo $this->htmlLink(array('route' => 'sitepageevent_bylocation'), $this->translate('By Locations'), array('class' => 'buttonlink item_icon_sitepageevent_location')) ?>
		</li>
	</ul>
</div>		