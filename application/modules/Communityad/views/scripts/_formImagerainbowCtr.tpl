<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formimagerainbowCtr.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
 
<script type="text/javascript">
  function hexcolorTonumbercolor(hexcolor) {
  var hexcolorAlphabets = "0123456789ABCDEF";
	var valueNumber = new Array(3);
  var j = 0;
	if(hexcolor.charAt(0) == "#")
  hexcolor = hexcolor.slice(1);
	hexcolor = hexcolor.toUpperCase();
	for(var i=0;i<6;i+=2) {
		valueNumber[j] = (hexcolorAlphabets.indexOf(hexcolor.charAt(i)) * 16) + hexcolorAlphabets.indexOf(hexcolor.charAt(i+1));
		j++;
	}
	return(valueNumber);
}

	window.addEvent('domready', function() {

		var r = new MooRainbow('myRainbow3', {
    
			id: 'myDemo3',
			'startColor':hexcolorTonumbercolor("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.graphctr.color', '#CD6839') ?>"),
			'onChange': function(color) {
				$('communityad_graphctr_color').value = color.hex;
			}
		});
	});	
</script>

<?php
echo '
	<div id="communityad_graphctr_color-wrapper" class="form-wrapper">
		<div id="communityad_graphctr_color-label" class="form-label">
			<label for="communityad_graphctr_color" class="optional">
				'.$this->translate('CTR Line Color').'
			</label>
		</div>
		<div id="communityad_graphctr_color-element" class="form-element">
			<p class="description">'.$this->translate('Select the color of the lines which are used to represent CTR in the graphs. (Click on the rainbow below to choose your color.)').'</p>
			<input name="communityad_graphctr_color" id="communityad_graphctr_color" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.graphctr.color', '#CD6839') . ' type="text">
			<input name="myRainbow3" id="myRainbow3" src="' . $this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>