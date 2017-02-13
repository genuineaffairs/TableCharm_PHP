<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq-default-msg.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="settings" style="margin:15px 15px 0;">
	<form>
		<div>
			<div >
				<?php
					$faq_msg = Zend_Registry::get('Zend_Translate')->_("For editing the text of this FAQ, please go to 'Layout' > 'Language Manager'. For the question, please change the language variable: '%s' and for the answer, please change the language variable: '%s'.");
					$faq_msg = sprintf($faq_msg, $this->item->question, $this->item->answer);
					echo $faq_msg;
				?>
			</div>
			<div style="margin-right:15px;float:right;margin-top:10px;">
				<button onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("Cancel") ?></button>
			</div>
		</div>
	</form>
</div>