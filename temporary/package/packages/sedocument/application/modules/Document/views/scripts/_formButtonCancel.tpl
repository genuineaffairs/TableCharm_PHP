<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formButtonCancel.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div id="submit-wrapper" class="form-wrapper">
	<div id="submit-label" class="form-label"> </div>
	<div id="submit-element" class="form-element">
		<button type="submit" id="done" name="done" onclick="javascript:showlightbox();" >
			<?php echo ( $this->element->getLabel() ? $this->element->getLabel() : $this->translate('Save Changes')) ?>
		</button>
		<?php echo $this->translate('or');?>
		<?php echo $this->htmlLink(array('route' => 'document_manage', 'action' => 'manage'), $this->translate('cancel')) ?>
	</div>
</div>