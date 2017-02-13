<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if($this->total_results <= 0): ?>
	<div class="tip">
		<span>
			<?php echo $this->translate('Nobody has created a document yet.');  ?>
		</span>
	</div>
<?php endif; ?>