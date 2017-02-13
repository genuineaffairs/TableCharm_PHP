<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formimagerainbowGraphbg.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/scripts/mooRainbow.js" type="text/javascript"></script>
<?php
	$baseUrl = $this->layout()->staticBaseUrl;
  $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Communityad/externals/styles/mooRainbow.css');
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

		var r = new MooRainbow('myRainbow4', {
    
			id: 'myDemo4',
			'startColor':hexcolorTonumbercolor("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.graph.bgcolor', '#ffffff') ?>"),
			'onChange': function(color) {
				$('communityad_graph_bgcolor').value = color.hex;
			}
		});
	});	
</script>

<?php
echo '
	<div id="communityad_graph_bgcolor-wrapper" class="form-wrapper">
		<div id="communityad_graph_bgcolor-label" class="form-label">
			<label for="communityad_graph_bgcolor" class="optional">
				'.$this->translate('Background Color').'
			</label>
		</div>
		<div id="communityad_graph_bgcolor-element" class="form-element">
			<p class="description">'.$this->translate('Select the background color of the graphs showing ads and campaigns statistics. (Click on the rainbow below to choose your color.)').'</p>
			<input name="communityad_graph_bgcolor" id="communityad_graph_bgcolor" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.graph.bgcolor', '#ffffff') . ' type="text">
			<input name="myRainbow4" id="myRainbow4" src="' . $this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>