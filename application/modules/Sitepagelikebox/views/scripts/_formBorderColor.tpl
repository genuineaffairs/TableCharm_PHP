<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagelikebox
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formBorderColor.tpl 2011-10-10 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script src="application/modules/Sitepage/externals/scripts/mooRainbow.js" type="text/javascript"></script>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitepage/externals/styles/mooRainbow.css');
?>
<script type="text/javascript">
  window.addEvent('domready', function() {
    var s = new MooRainbow('border_color_rainbow', {
      id: 'myDemo2',
      'onChange': function(color) {
        $('border_color').value = color.hex;
      }
    });

  });
</script>
<div id="border_color-wrapper" class="form-wrapper">
	<label for="border_color" class="optional"><?php echo $this->translate('Border Color'); ?>
		<a href="javascript:void(0);" class="sitepagelikebox_show_tooltip_wrapper"> [?]
			<span class="sitepagelikebox_show_tooltip">
				<img src="application/modules/Sitepage/externals/images/tooltip_arrow.png"><?php echo $this->translate('Border color of the embeddable badge. Choose color code of your choice by clicking on the rainbow.'); ?>
			</span>
		</a>
	</label>
	<div id="border_color-element" class="form-element">
		<input type="text" value="" style="width:80px; max-width:80px;" onblur="setLikeBox()" />
		<input name="border_color_rainbow" id="border_color_rainbow" src="application/modules/Sitepage/externals/images/rainbow.png" link="true" type="image" />
		<input type="hidden" name="border_color" id="border_color" value=""  onblur="setLikeBox()" />
	</div>
</div>