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

<div class="seaocore_gutter_blocks">
	<ul>
		<form id='filter_form_search_box' class='global_form_box' method='get' action='<?php echo $this->url(array('action' => 'browse'), 'document_browse', true) ?>'>
			<input id="search" name="<?php echo $this->translate('search')?>" type='text' />
		</form>
	</ul>
</div>