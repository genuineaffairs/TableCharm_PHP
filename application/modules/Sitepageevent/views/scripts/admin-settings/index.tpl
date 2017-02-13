<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/pluginLink.tpl'; ?>
<h2><?php echo $this->translate('Directory / Pages - Events Extension') ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>
<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_upgrade_messages.tpl'; ?>

<script type="text/javascript">
function dismiss1(modName) {
	document.cookie= modName + "_dismiss_event" + "=" + 1;
	$('dismiss_modules_event').style.display = 'none';
}
</script>

<?php 
	$moduleName = 'sitepageevent';
	if( !isset($_COOKIE[$moduleName . '_dismiss_event']) ):
?>
<div id="dismiss_modules_event">
	<div class="seaocore-notice">
		<div class="seaocore-notice-icon">
			<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
		</div>
<div style="float:right;">
	<button onclick="dismiss1('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
</div>
		<div class="seaocore-notice-text ">
			<?php echo $this->translate('We have moved these "Widget Settings" to "Layout Editor". You can change the desired settings of the respective widgets from "Layout Editor" by clicking on the "edit" link.');?>
		</div>	
	</div>
</div>
<?php endif; ?>

<?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.google.map.key')):  ?>
		<?php 
		 $URL_MAP = $this->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'map'), 'admin_default', true);
		echo $this->translate('<div class="tip"><span>Note: You have not entered Google Places API Key for your website. Please <a href="%s" target="_blank"> Click here </a></span></div>', $URL_MAP); ?>
<?php endif;  ?>

<div class='clear sitepage_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>