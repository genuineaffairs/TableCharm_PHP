<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<div class="quicklinks layout_sitepagecontent_link">
	<ul>
		<li>
			<?php echo $this->htmlLink(array('route' => 'sitepagedocument_browse'), $this->translate('Browse Documents'), array(
												'class' => 'buttonlink item_icon_sitepagedocument_detail'
			)) ?>
		</li>
	</ul>
</div>		