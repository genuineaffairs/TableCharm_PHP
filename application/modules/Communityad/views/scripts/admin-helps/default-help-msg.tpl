<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: default-help-msg.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="settings" style="margin:15px 15px 0;">
	<form>
		<div>
			<div >
				<?php
					if( $this->item->page_default == 1 ) {
						$help_msg = $this->translate("For editing the text of this page, please go to 'Layout' > 'Language Manager' and change the language variables: '_communityad_help_overview_1' to '_communityad_help_overview_12'.");
					}else if( $this->item->page_default == 2 ) {
						$help_msg =	$this->translate("For editing the text of this page, please go to 'Layout' > 'Language Manager' and change the language variables: '_communityad_help_getstarted_1' to '_communityad_help_getstarted_12'.");
					}else if( $this->item->page_default == 3 ) {
						$help_msg =	$this->translate("For editing the text of this page, please go to 'Layout' > 'Language Manager' and change the language variables: '_communityad_help_improve_ad_1' to '_communityad_help_improve_ad_15'.");
					}else if( $this->item->page_default == 4 ) {
					    $help_msg =	$this->translate("For editing the text of this page, please go to 'Layout' > 'Language Manager' and change the following language variables: <br /><br />
					    '_communityad_help_sponsored_story_qus_1' to '_communityad_help_sponsored_story_qus_7' <br /><br />
					    '_communityad_help_sponsored_story_ans_1' to '_communityad_help_sponsored_story_ans_7'
					    ");
					}
					echo $help_msg;
				?>
			</div>
			<div style="margin-right:15px;float:right;margin-top:10px;">
				<button onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("Cancel") ?></button>
			</div>
		</div>
	</form>
</div>