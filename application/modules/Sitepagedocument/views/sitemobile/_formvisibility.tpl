<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formvisibility.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php

echo '
	<div id="default_visibility-wrapper" class="form-wrapper"><div id="default_visibility-label" class="form-label"><label for="default_visibility" class="optional">' . $this->translate("Document Visibility") . '</label></div>
	<div id="default_visibility-element" class="form-element">
		<select name="default_visibility" id="default_visibility">
			<option value="private" label="Only on this website">' . $this->translate("Only on this website") . '</option>
			<option value="public" label="Public on Scribd.com">' . $this->translate("Public on Scribd.com") . '</option>
		</select>
		<span class="sitepagedocument_show_tooltip_wrapper">
			<div class="sitepagedocument_show_tooltip">
				' . $this->translate("Documents visible only on this website will be private and available only on your website, whereas the ones which will be public on Scribd.com will be available to everyone on Scribd") .
 '</div>
			&nbsp;&nbsp;<img src="'. $this->layout()->staticBaseUrl . 'application/modules/Sitepagedocument/externals/images/help16.gif">
		</span>
	</div>
  '
?>