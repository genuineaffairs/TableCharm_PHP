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
			<?php if ($this->can_create):  ?>
				<?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array(), 'document_create').'">', '</a>'); ?>
			<?php endif; ?>
		</span>
	</div>
<?php endif; ?>