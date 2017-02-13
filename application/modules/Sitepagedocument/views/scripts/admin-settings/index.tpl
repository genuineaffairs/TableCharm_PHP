<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?> 
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/pluginLink.tpl'; ?>
<h2><?php echo $this->translate('Directory / Pages - Documents Extension') ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>
<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_upgrade_messages.tpl'; ?>

<div class='clear sitepage_settings_form'>

  <a href="<?php echo $this->url(array('module' => 'sitepagedocument', 'controller' => 'settings', 'action' => 'faq', 'show' => 1), 'admin_default', true) ?>"
     class="buttonlink" style="background-image:url(<?php echo $this->layout()->staticBaseUrl;?>application/modules/Sitepagedocument/externals/images/help16.gif);"><?php echo
$this->translate("How do I get my Scribd API details ?") ?>
  </a>
	<br /><br />
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>
<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.navi.auth', null)): ?>
<script type="text/javascript">

	$('sitepagedocument_default_visibility-public').addEvent('click', function(){
			$('sitepagedocument_visibility_option-wrapper').setStyle('display', ($(this).get('value') == 'public'?'block':'none'));
	});

	$('sitepagedocument_default_visibility-private').addEvent('click', function(){
			$('sitepagedocument_visibility_option-wrapper').setStyle('display', ($(this).get('value') == 'private'?'none':'block'));
	});

	$('sitepagedocument_secure_allow-1').addEvent('click', function(){
			$('sitepagedocument_secure_show-wrapper').setStyle('display', ($(this).get('value') == '1'?'block':'none'));
	});

	$('sitepagedocument_secure_allow-0').addEvent('click', function(){
			$('sitepagedocument_secure_show-wrapper').setStyle('display', ($(this).get('value') == '0'?'none':'block'));
	});

	$('sitepagedocument_email_allow-1').addEvent('click', function(){
			$('sitepagedocument_email_show-wrapper').setStyle('display', ($(this).get('value') == '1'?'block':'none'));
	});

	$('sitepagedocument_email_allow-0').addEvent('click', function(){
			$('sitepagedocument_email_show-wrapper').setStyle('display', ($(this).get('value') == '0'?'none':'block'));
	});

	$('sitepagedocument_download_allow-1').addEvent('click', function(){
			$('sitepagedocument_download_show-wrapper').setStyle('display', ($(this).get('value') == '1'?'block':'none'));
	});

	$('sitepagedocument_download_allow-0').addEvent('click', function(){
			$('sitepagedocument_download_show-wrapper').setStyle('display', ($(this).get('value') == '0'?'none':'block'));
	});

	$('sitepagedocument_scribd_viewer-1').addEvent('click', function(){
		$('sitepagedocument_fullscreen_button-wrapper').setStyle('display', ($(this).get('value') == 1?'block':'none'));
		$('sitepagedocument_flash_mode-wrapper').setStyle('display', ($(this).get('value') == 1?'block':'none'));
		$('sitepagedocument_disable_button-wrapper').setStyle('display', ($(this).get('value') == 1?'block':'none'));
	});

	$('sitepagedocument_scribd_viewer-0').addEvent('click', function(){
		$('sitepagedocument_fullscreen_button-wrapper').setStyle('display', ($(this).get('value') == 0?'none':'block'));
		$('sitepagedocument_flash_mode-wrapper').setStyle('display', ($(this).get('value') == 0?'none':'block'));
		$('sitepagedocument_disable_button-wrapper').setStyle('display', ($(this).get('value') == 0?'none':'block'));
	});

	$('sitepagedocument_licensing_option').addEvent('click', function(){
	    $('sitepagedocument_licensing_scribd-wrapper').setStyle('display', ($(this).checked?'none':'block'));
	});

	$('sitepagedocument_include_full_text').addEvent('click', function(){
		$('sitepagedocument_visitor_fulltext-wrapper').setStyle('display', ($(this).checked?'block':'none'));
	});

	$('sitepagedocument_carousel-1').addEvent('click', function(){
			$('sitepagedocument_number_carousel-wrapper').setStyle('display', ($(this).get('value') == '1'?'block':'none'));
	});

	$('sitepagedocument_carousel-0').addEvent('click', function(){
			$('sitepagedocument_number_carousel-wrapper').setStyle('display', ($(this).get('value') == '0'?'none':'block'));
	});
		
	window.addEvent('domready', function() {
		$('sitepagedocument_visibility_option-wrapper').setStyle('display', ($('sitepagedocument_default_visibility-public').checked ?'block':'none'));
		$('sitepagedocument_secure_show-wrapper').setStyle('display', ($('sitepagedocument_secure_allow-1').checked ?'block':'none'));
		$('sitepagedocument_email_show-wrapper').setStyle('display', ($('sitepagedocument_email_allow-1').checked ?'block':'none'));
		$('sitepagedocument_download_show-wrapper').setStyle('display', ($('sitepagedocument_download_allow-1').checked ?'block':'none'));
		$('sitepagedocument_fullscreen_button-wrapper').setStyle('display', ($('sitepagedocument_scribd_viewer-1').checked ?'block':'none'));
		$('sitepagedocument_flash_mode-wrapper').setStyle('display', ($('sitepagedocument_scribd_viewer-1').checked ?'block':'none'));
		$('sitepagedocument_disable_button-wrapper').setStyle('display', ($('sitepagedocument_scribd_viewer-1').checked ?'block':'none'));
		$('sitepagedocument_licensing_scribd-wrapper').setStyle('display', ($('sitepagedocument_licensing_option').checked?'none':'block'));
		$('sitepagedocument_visitor_fulltext-wrapper').setStyle('display', ($('sitepagedocument_include_full_text').checked?'block':'none'));
		$('sitepagedocument_number_carousel-wrapper').setStyle('display', ($('sitepagedocument_carousel-1').checked ?'block':'none'));
	});

</script>
<?php endif; ?>