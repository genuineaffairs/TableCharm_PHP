<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagelikebox
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formColorCode.tpl 2011-10-10 9:40:21Z SocialEngineAddOns $
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
    var s = new MooRainbow('myRainbow2', { 
      id: 'myDemo2',
      'onChange': function(color) {
        $('sitepage_sponsored_color').value = color.hex;
      }
    });

  });
</script>

<?php
echo '
	<div class="splb-admin-colorpicker-wrapper">
		<div class="splb-admin-colorpicker-label">
				' . $this->translate('Color Code:') . '
		</div>
		<div class="splb-admin-colorpicker-element">
			<input name="sitepage_sponsored_color" id="sitepage_sponsored_color" value="#FC0505" type="text">
			<input name="myRainbow2" id="myRainbow2" src="application/modules/Sitepage/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>