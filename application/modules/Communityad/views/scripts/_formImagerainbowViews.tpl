<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formimagerainbowViews.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
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

		var r = new MooRainbow('myRainbow1', {
    
			id: 'myDemo1',
			'startColor':hexcolorTonumbercolor("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.graphview.color', '#3299CC') ?>"),
			'onChange': function(color) {
				$('communityad_graphview_color').value = color.hex;
			}
		});
	});	
</script>

<?php
echo '
	<div id="communityad_graphview_color-wrapper" class="form-wrapper">
		<div id="communityad_graphview_color-label" class="form-label">
			<label for="communityad_graphview_color" class="optional">
				'.$this->translate('Views Line Color').'
			</label>
		</div>
		<div id="communityad_graphview_color-element" class="form-element">
			<p class="description">'.$this->translate('Select the color of the lines which are used to represent Views in the graphs. (Click on the rainbow below to choose your color.)').'</p>
			<input name="communityad_graphview_color" id="communityad_graphview_color" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.graphview.color', '#3299CC') . ' type="text">
			<input name="myRainbow1" id="myRainbow1" src="' . $this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>