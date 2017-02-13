<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: footermsg.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="settings" style="margin:15px 15px 0;">
	<form>
		<div>
			<div >
				<?php 
					echo $this->translate('To Enable the Ad Block in the Footer, please go to the "Layout Editor" and select "Site Footer" under "Editing". Then, drag-and-drop the "Footer Ads" widget in the footer.<br />Alternatively, to Disable the Ad Block in the Footer, please go to the "Layout Editor" and select "Site Footer" under "Editing". Then, remove the "Footer Ads" widget from the footer by clickin on the "x" mark on it.');
				?>
			</div>
			<div style="margin-right:15px;float:right;margin-top:10px;">
				<button onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("Cancel") ?></button>
			</div>
		</div>
	</form>
</div>