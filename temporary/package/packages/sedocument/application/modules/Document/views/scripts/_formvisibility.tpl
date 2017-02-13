<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formvisibility.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
	$front = Zend_Controller_Front::getInstance();
	$action = $front->getRequest()->getActionName();
	$selected_private = $selected_public = '';

	if($action == 'edit') {
		$curr_url = $front->getRequest()->getRequestUri();
		$explode_str = explode("/", $curr_url);
		$get_last_key = count($explode_str) - 1;
		$document_value = explode("?", $explode_str[$get_last_key]);
		$document_id = $document_value[0];

		$document_private == 'private';
		if(is_numeric($document_id)) {
			$document = Engine_Api::_()->getItem('document', $document_id);
			if(!empty($document->document_private)) {
				$document_private = $document->document_private;
			}
		}

		if($document_private == 'public') {
			$selected_public = 'selected="selected"';
		}
		else {
			$selected_private = 'selected="selected"';
		}
	}
?>

<?php
echo '
	<div id="default_visibility-wrapper" class="form-wrapper"><div id="default_visibility-label" class="form-label"><label for="default_visibility" class="optional">'.$this->translate("Document Visibility").'</label></div>
	<div id="default_visibility-element" class="form-element">
		<select name="default_visibility" id="default_visibility">
			<option value="private"'.$selected_private.'label="Only on this website">'.$this->translate("Only on this website").'</option>
			<option value="public"'.$selected_public.'label="Public on Scribd.com">'.$this->translate("Public on Scribd.com").'</option>
		</select>
		<span class="document_show_tooltip_wrapper">
			<div class="document_show_tooltip">
				'.$this->translate("Documents visible only on this website will be private and available only on your website, whereas the ones which will be public on Scribd.com will be available to everyone on Scribd. Public documents will be downloadable and emailable as attachments always.").
			'</div>
			&nbsp;&nbsp;<img src="application/modules/Document/externals/images/help16.gif">
		</span>
	</div>
  '    
?>