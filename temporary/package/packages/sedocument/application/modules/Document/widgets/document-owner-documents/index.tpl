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

<h3><?php echo $this->translate($this->owner->getTitle()), $this->translate("'s Documents") ?></h3>

<ul class="seaocore_sidebar_list">
	<?php  $this->partialLoop()->setObjectKey('document');
				 echo $this->partialLoop('application/modules/Document/views/scripts/partialloop_widget.tpl', $this->paginator);
	?>

	<li>
		<?php echo $this->htmlLink($this->url(array('user_id' => $this->owner->user_id), 'document_list'), $this->translate('More &raquo;'), array('class'=>'more_link')) ?>
	</li>
</ul>