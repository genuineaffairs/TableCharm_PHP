<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$coreSettings = Engine_Api::_()->getApi('settings', 'core');

if (!empty($this->isModulesActivated)):
  foreach ($this->isModulesActivated as $modKey => $modValue) {
    $url = $this->url(array('module' => $modKey, 'controller' => 'settings', 'action' => 'readme'), 'admin_default', true);
    if ($modKey == 'sitepageinvite') {
      $url = $this->url(array('module' => $modKey, 'controller' => 'global', 'action' => 'readme'), 'admin_default', true);
    }
		$showMessage = Zend_Registry::get('Zend_Translate')->_('<div class="tip"><span>Note: You have installed the "%s" plugin but have not activated it on your site. Please activate it first. <a href=%s>Click here</a> to activate the "%s" plugin.</span></div>');
		$showMessage = sprintf($showMessage, $modValue, $url, $modValue );
		echo $showMessage;
  }
endif;

if( !empty($this->isModsSupport) ):
	foreach( $this->isModsSupport as $modName ) {
		echo $this->translate('<div class="tip"><span>Note: You do not have the latest version of the "%s". Please upgrade it to the latest version to enable its integration with Directory/Pages Plugin.</span></div>', ucfirst($modName));
	}
endif;
?>


<h2 class="fleft"><?php echo $this->translate('Directory / Pages Plugin'); ?></h2>
<?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/manageExtensions.tpl'; ?>

<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
  </div>
<?php endif; ?>

<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_upgrade_messages.tpl'; ?>
<div>

<?php if (!$coreSettings->getSetting('seaocore.google.map.key')):  ?>
		<?php 
		 $URL_MAP = $this->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'map'), 'admin_default', true);
		echo $this->translate('<div class="tip"><span>Note: You have not entered Google Places API Key for your website. Please <a href="%s" target="_blank"> Click here </a></span></div>', $URL_MAP); ?>
<?php endif;  ?>

<?php if(!$this->hasLanguageDirectoryPermissions):?>
<div class="seaocore_tip">
  <span>
    <?php echo "Please log in over FTP and set CHMOD 0777 (recursive) on the application/languages/ directory for  change the pharse pages and page." ?>
  </span>
</div>
<?php endif; ?>

<div class='clear sitepage_settings_form'>
  <div class='settings'>
<?php echo $this->form->render($this); ?>
  </div>
</div>

<script type="text/javascript">
  var display_msg=0;
  window.addEvent('domready', function() {
   // $('translation_file').checked=false;
  //  toogleLaguagePhase('none');
    showlocationKM('<?php echo $coreSettings->getSetting('sitepage.proximitysearch', 1); ?>');	
    showclaim('<?php echo $coreSettings->getSetting('sitepage.claimlink', 1); ?>');
    showcategoryblock	('<?php echo $coreSettings->getSetting('sitepage.category.edit', 0); ?>');
    showlocationOption('<?php echo $coreSettings->getSetting('sitepage.locationfield', 1); ?>');
    showpackageOption('<?php echo $coreSettings->getSetting('sitepage.package.enable', 1); ?>');
    showDefaultNetwork('<?php echo $coreSettings->getSetting('sitepage.network', 0) ?>');
    showMapOptions('<?php echo $coreSettings->getSetting('sitepage.location', 1) ?>');
    showDescription('<?php echo $coreSettings->getSetting('sitepage.description.allow', 1) ?>');
    display_msg=1;
  });

  function showDescription(option) {
    if($('sitepage_requried_description-wrapper')) {
      if(option == 1) {
        $('sitepage_requried_description-wrapper').style.display='block';
      } else{
        $('sitepage_requried_description-wrapper').style.display='none';
      }
    }
  }

  //  HERE WE CREATE A FUNCTION FOR SHOWING THE LOCATION IN KM OR MILES
  function showpackageOption(option) {
    if($('sitepage_package_enable-wrapper')) {
      if(option == 1) {
        if($('sitepage_currency-wrapper'))
          $('sitepage_currency-wrapper').style.display='block';
        if($('sitepage_payment_benefit-wrapper'))
          $('sitepage_payment_benefit-wrapper').style.display='block';
      }
      else{
        if($('sitepage_currency-wrapper'))
          $('sitepage_currency-wrapper').style.display='none';
        if($('sitepage_payment_benefit-wrapper'))
          $('sitepage_payment_benefit-wrapper').style.display='none';
      }
    }
  }

  function showlocationOption(option) {

		
    if(option == 1) {
      if($('sitepage_location-wrapper'))
        $('sitepage_location-wrapper').style.display='block';
      if($('sitepage_proximitysearch-wrapper'))
        $('sitepage_proximitysearch-wrapper').style.display='block';
      if($('sitepage_proximitysearch-1'))
        if($('sitepage_proximitysearch-1').checked)
          showlocationKM(1);
      else
        showlocationKM(0);
      if($('sitepage_location-1'))
        if($('sitepage_location-1').checked)
          showMapOptions(1);
      else
        showMapOptions(0);
              if($('sitepage_multiple_location-wrapper'))
        $('sitepage_multiple_location-wrapper').style.display='block';
    }
    else{
      if($('sitepage_location-wrapper'))
        $('sitepage_location-wrapper').style.display='none';
      if($('sitepage_proximitysearch-wrapper'))
        $('sitepage_proximitysearch-wrapper').style.display='none';
      if($('sitepage_proximity_search_kilometer-wrapper'))
        $('sitepage_proximity_search_kilometer-wrapper').style.display='none';
              if($('sitepage_multiple_location-wrapper'))
        $('sitepage_multiple_location-wrapper').style.display='none';
      showMapOptions(0);
    }
		
  }
  //  HERE WE CREATE A FUNCTION FOR SHOWING THE LOCATION IN KM OR MILES
  function showlocationKM(option) {
    if($('sitepage_proximity_search_kilometer-wrapper')) {
      if(option == 1) {
        if($('sitepage_proximity_search_kilometer-wrapper'))
          $('sitepage_proximity_search_kilometer-wrapper').style.display='block';	
      }
      else{
        if($('sitepage_proximity_search_kilometer-wrapper'))
          $('sitepage_proximity_search_kilometer-wrapper').style.display='none';
      }
    }
  }

  //  HERE WE CREATE A FUNCTION FOR SHOWING BOUNCING
  function showMapOptions(option) {
    if($('sitepage_location-wrapper')) {
      if(option == 1) {
        if($('sitepage_map_sponsored-wrapper'))
          $('sitepage_map_sponsored-wrapper').style.display='block';
          if($('sitepage_map_zoom-wrapper'))
						$('sitepage_map_zoom-wrapper').style.display='block';
           if($('sitepage_map_city-wrapper'))
						$('sitepage_map_city-wrapper').style.display='block';
      }
      else{
        if($('sitepage_map_sponsored-wrapper'))
          $('sitepage_map_sponsored-wrapper').style.display='none';
         if($('sitepage_map_zoom-wrapper'))
					$('sitepage_map_zoom-wrapper').style.display='none';
         if($('sitepage_map_city-wrapper'))
					$('sitepage_map_city-wrapper').style.display='none';
      }
    }
  }

  function showclaim(option) 
  {
    if($('sitepage_claim_show_menu-wrapper')) {
      if(option == 1) { 
        $('sitepage_claim_show_menu-wrapper').style.display='block';	
      }
      else{
        $('sitepage_claim_show_menu-wrapper').style.display='none';
      }		
    }
    if($('sitepage_claim_email-wrapper')) {
      if(option == 1) { 
        $('sitepage_claim_email-wrapper').style.display='block';	
      }
      else{
        $('sitepage_claim_email-wrapper').style.display='none';
      }		
    }
  }

  function showcategoryblock(option)
  {
    if(option == 1 && display_msg == 1) {
      alert("<?php echo $this->string()->escapeJavascript($this->translate('After giving this permission members can edit categories but it can cause content loss like reviews rating data, profile type details etc.')) ?>");
    }
  }
  function showDefaultNetwork(option) {
    if($('sitepage_default_show-wrapper')) {
      if(option == 0) {
        $('sitepage_default_show-wrapper').style.display='block';        
        showDefaultNetworkType($('sitepage_default_show-1').checked);
        $('sitepage_privacybase-wrapper').style.display='block';
         $('sitepage_networkprofile_privacy-wrapper').style.display='none';
      }else{
        showDefaultNetworkType(1);
        $('sitepage_default_show-wrapper').style.display='none';
        $('sitepage_privacybase-wrapper').style.display='none';
        $('sitepage_networkprofile_privacy-wrapper').style.display='block';
      }
    }
  }

  function showDefaultNetworkType(option) {
    if($('sitepage_networks_type-wrapper')) {
      if(option == 1) {
        $('sitepage_networks_type-wrapper').style.display='block';
      }else{
        $('sitepage_networks_type-wrapper').style.display='none';
      }
    }
  }
  
//   function showUpdateWarning(){
//     if( $('translation_file').checked){
//       var r=confirm("Are you sure that you want to replace language files for Directory / Pages Plugin and Directory / Pages Plugin Extension installed at your site?");
//       if (r==false)
//       {
//         $('translation_file').checked=false;
//       }
//     }
//     
//     if($('translation_file').checked)
//       toogleLaguagePhase('block');
//     else
//       toogleLaguagePhase('none');
//   }
/*  
  function toogleLaguagePhase(display){
    <?php // $elements = Engine_Api::_()->getApi('language', 'sitepage')->getDataWithoutKeyPhase();
    //foreach($elements as $key=>$element):?>
			if($('<?php // echo $key ?>-wrapper'))
        $('<?php //echo $key ?>-wrapper').style.display=display;
    <?php //endforeach; ?>
  }*/
</script>
