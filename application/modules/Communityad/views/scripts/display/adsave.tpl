<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: adsave.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if( !empty($this->showMsg) ): ?>
	<div class='tip'>
		<span>
			<?php 
				echo $this->translate('Thanks for your feedback. Your report has been submitted.');
			?>
		</span>
	</div>
<?php endif; ?>