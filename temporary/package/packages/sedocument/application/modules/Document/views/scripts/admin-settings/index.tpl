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

<?php 
	if( !empty($this->isModsSupport) ):
		foreach( $this->isModsSupport as $modName ) {
			echo "<div class='tip'><span>" . $this->translate("Note: You do not have the latest version of the '%s'. Please upgrade it to the latest version to enable its integration with Documents / Scribd iPaper plugin.", ucfirst($modName)) . "</span></div>";
		}
	endif;
?>

<h2><?php echo $this->translate('Documents Plugin')?></h2>

<?php if( count($this->navigation) ): ?>
	<div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
	</div>
<?php endif; ?>
<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_upgrade_messages.tpl'; ?>

<script type="text/javascript">
	function dismissWidget(modName) {
		document.cookie= modName + "_dismissWidget" + "=" + 1;
		$('dismissWidget_modules').style.display = 'none';
	}
</script>

<?php $moduleName = Zend_Controller_Front::getInstance()->getRequest()->getModuleName(); ?>
	
<?php if( !isset($_COOKIE[$moduleName . '_dismissWidget']) ): ?>
	<div id="dismissWidget_modules">
		<div class="seaocore-notice">
			<div class="seaocore-notice-icon">
				<img src="./application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
			</div>
			<div style="float:right;">
				<button onclick="dismissWidget('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
			</div>
			<div class="seaocore-notice-text ">
				<?php echo $this->translate('We have moved the widget settings in Layout Editor section. You can change number of items and other new settings by clicking on the edit link placed alongside the widget.'); ?>
			</div>	
		</div>
	</div>
<?php endif; ?>

<div class='clear seaocore_settings_form'>
	<a href="<?php echo $this->url(array('module' => 'document', 'controller' => 'settings', 'action' => 'faq', 'show' => 1), 'admin_default', true) ?>"
		class="buttonlink" style="background-image:url(./application/modules/Document/externals/images/help16.gif);padding-left:23px;"><?php echo
		$this->translate("How do I get my Scribd API details ?") ?>
	</a>

  <div class='settings' style="margin-top:15px;">
    <?php echo $this->form->render($this) ?>
  </div>
</div>

<script type="text/javascript">

	$('document_default_visibility-public').addEvent('click', function(){
			$('document_visibility_option-wrapper').setStyle('display', ($(this).get('value') == 'public'?'block':'none'));
	});

	$('document_default_visibility-private').addEvent('click', function(){
			$('document_visibility_option-wrapper').setStyle('display', ($(this).get('value') == 'private'?'none':'block'));
	});

	$('document_include_full_text').addEvent('click', function(){
			$('document_visitor_fulltext-wrapper').setStyle('display', ($(this).checked?'block':'none'));
	});

	$('document_licensing_option').addEvent('click', function(){
			$('document_licensing_scribd-wrapper').setStyle('display', ($(this).checked?'none':'block'));
	});

	$('document_viewer-1').addEvent('click', function(){
		$('document_fullscreen_button-wrapper').setStyle('display', ($(this).get('value') == 1?'block':'none'));
		$('document_flash_mode-wrapper').setStyle('display', ($(this).get('value') == 1?'block':'none'));
		$('document_disable_button-wrapper').setStyle('display', ($(this).get('value') == 1?'block':'none'));
	});

	$('document_viewer-0').addEvent('click', function(){
		$('document_fullscreen_button-wrapper').setStyle('display', ($(this).get('value') == 0?'none':'block'));
		$('document_flash_mode-wrapper').setStyle('display', ($(this).get('value') == 0?'none':'block'));
		$('document_disable_button-wrapper').setStyle('display', ($(this).get('value') == 0?'none':'block'));
	});
	
	window.addEvent('domready', function() {

		$('document_fullscreen_button-wrapper').setStyle('display', ($('document_viewer-1').checked ?'block':'none'));
		$('document_flash_mode-wrapper').setStyle('display', ($('document_viewer-1').checked ?'block':'none'));
		$('document_disable_button-wrapper').setStyle('display', ($('document_viewer-1').checked ?'block':'none'));
		$('document_visibility_option-wrapper').setStyle('display', ($('document_default_visibility-public').checked ?'block':'none'));
		$('document_visitor_fulltext-wrapper').setStyle('display', ($('document_include_full_text').checked?'block':'none'));
		$('document_licensing_scribd-wrapper').setStyle('display', ($('document_licensing_option').checked?'none':'block'));

	});

  <?php $settings = Engine_Api::_()->getApi('settings', 'core'); ?>

	window.addEvent('domready', function() {
		showDefaultNetwork('<?php echo $settings->getSetting('document.network', 0) ?>');
	});

	function showDefaultNetwork(option) {
		if($('document_default_show-wrapper')) {
			if(option == 0) {
				$('document_default_show-wrapper').style.display='block';
         showDefaultNetworkType($('document_default_show-1').checked);
			}
			else{
         showDefaultNetworkType(1);
				$('document_default_show-wrapper').style.display='none';
			}
		}
	}
  function showDefaultNetworkType(option) {
    if($('document_networks_type-wrapper')) {
      if(option == 1) {
        $('document_networks_type-wrapper').style.display='block';
      }else{
        $('document_networks_type-wrapper').style.display='none';
      }
    }
  }
  
</script>