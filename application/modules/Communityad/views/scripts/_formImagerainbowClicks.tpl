<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formimagerainbowClicks.tpl  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/scripts/mooRainbow.js" type="text/javascript"></script>
<?php
	$baseUrl = $this->layout()->staticBaseUrl;
  $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Communityad/externals/styles/mooRainbow.css');
?>
<script type="text/javascript">
	window.addEvent('domready', function() { 
		var s = new MooRainbow('myRainbow2', { 
			id: 'myDemo2',
			'startColor': hexcolorTonumbercolor("<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.graphclick.color', '#458B00') ?>"),
			'onChange': function(color) {
				$('communityad_graphclick_color').value = color.hex;
			}
		});
	});
</script>

<?php
echo '
	<div id="communityad_graphclick_color-wrapper" class="form-wrapper">
		<div id="communityad_graphclick_color-label" class="form-label">
			<label for="communityad_graphclick_color" class="optional">
				'. $this->translate('Clicks Line Color').'
			</label>
		</div>
		<div id="communityad_graphclick_color-element" class="form-element">
			<p class="description">'.$this->translate('Select the color of the lines which are used to represent Clicks in the graphs. (Click on the rainbow below to choose your color.)').'</p>
			<input name="communityad_graphclick_color" id="communityad_graphclick_color" value=' . Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.graphclick.color', '#458B00') . ' type="text">
			<input name="myRainbow2" id="myRainbow2" src="' . $this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/rainbow.png" link="true" type="image">
		</div>
	</div>
'
?>