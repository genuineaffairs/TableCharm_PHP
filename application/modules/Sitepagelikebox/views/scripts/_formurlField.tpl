<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagelikebox
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formurlField.tpl 2011-10-10 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
	$request = Zend_Controller_Front::getInstance()->getRequest();
	$sitepage = Engine_Api::_()->getItem( 'sitepage_page' , $request->getParam( 'page_id' ) ) ;
	$url = "http://" . $_SERVER['HTTP_HOST'] . $this->url( array ( 'page_url' => Engine_Api::_()->sitepage()->getPageUrl( $request->getParam( 'page_id' ) ) ) , 'sitepage_entry_view' , true ) ;
?>
<div id="url-wrapper" class="form-wrapper">
	<label for="url" class="optional"><?php echo $this->translate('Your Page URL'); ?>
		<a href="javascript:void(0);" class="sitepagelikebox_show_tooltip_wrapper"> [?]
			<span class="sitepagelikebox_show_tooltip">
				<img src="application/modules/Sitepage/externals/images/tooltip_arrow.png"><?php echo $this->translate('The Page Title and Page Photo will link to this URL.') ?>
			</span>
		</a>
	</label>
	<div id="url-element" class="form-element sitepagelikebox_show_tooltip_wrapper">
		<input type="text" name="url" id="url" value="<?php echo $url; ?>" style="width:250px; max-width:250px;" disabled="disabled">
		<span class="sitepagelikebox_show_tooltip">
			<img src="application/modules/Sitepage/externals/images/tooltip_arrow.png">
			<?php echo $url; ?>
		</span>
	</div>
</div>