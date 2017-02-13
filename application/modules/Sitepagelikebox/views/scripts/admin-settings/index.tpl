<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagelikebox
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-10-10 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<style type="text/css">
	.settings .form-element
	{
		max-width:400px;
	}
</style>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/pluginLink.tpl'; ?>
<h2><?php echo $this->translate( 'Directory / Pages - Embeddable Badges, Like Box Extension' ) ; ?></h2>
<div class='tabs'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer( $this->navigation )->render()
  ?>
</div>
<div class='clear sitepage_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render( $this ) ?>
  </div>
</div>

<script type="text/javascript">
window.addEvent('domready', function() {
showfileOptions('<?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.powred' , 1 ) ?>');

showheightOptions('<?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.hight' , 1 ) ?>');
showwidthOptions('<?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.width' , 1 ) ?>');
showcolorOptions('<?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.colorschme' , 1 ) ?>');

showfacesOptions('<?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.faces' , 1 ) ?>');
showheaderOptions('<?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.header' , 1 ) ?>');

if ('<?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.powred' , 1 ) ?>' == 1) { 
	showlogotitleOptions('<?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'logo.title' , 1 ) ?>');
}

var optionpowered = '<?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'likebox.powred' , 1 ) ?>';
	if(optionpowered == 1) {
		$('logo_photo-wrapper').style.display='block';
		$('logo_photo_preview-wrapper').style.display='block';
	}	else {
		$('logo_photo-wrapper').style.display='none';
		$('logo_photo_preview-wrapper').style.display='none';

	}

var optionlogo = '<?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'logo.title' , 1 ) ?>';
	if(optionlogo == 1 && optionpowered ==1 ) { 
		$('logo_photo-wrapper').style.display='block';
		$('logo_photo_preview-wrapper').style.display='block';
	}	else { 
		$('logo_photo-wrapper').style.display='none';
		$('logo_photo_preview-wrapper').style.display='none';
	}

});

function showfileOptions(option) {
	if($('likebox_powred-wrapper')) {
		if(option == 1) {
					$('logo_title-wrapper').style.display='block';
					if ('<?php echo Engine_Api::_()->getApi( 'settings' , 'core' )->getSetting( 'logo.title' , 1 ) ?>' == 1) {
					$('logo_photo-wrapper').style.display='block';
          $('logo_photo_preview-wrapper').style.display='block';
					 }
		}	else { 
				$('logo_photo-wrapper').style.display='none';
        $('logo_photo_preview-wrapper').style.display='none';
				$('logo_title-wrapper').style.display='none';
		}
	}
}



function showlogotitleOptions(option) { 
	if($('logo_title-wrapper')) {
		if(option == 1) {
					$('logo_photo-wrapper').style.display='block';
          $('logo_photo_preview-wrapper').style.display='block';
		}else{
				$('logo_photo-wrapper').style.display='none';
        $('logo_photo_preview-wrapper').style.display='none';
		}
	}
}


function showwidthOptions(option) {
	if($('likebox_width-wrapper')) {
		if(option == 0) {
					$('likebox_default_width-wrapper').style.display='block';
         
		}else{
				$('likebox_default_width-wrapper').style.display='none';
       
		}
	}
}


function showheightOptions(option) {
	if($('likebox_hight-wrapper')) {
		if(option == 0) {
					$('likebox_default_hight-wrapper').style.display='block';
		}else{
				$('likebox_default_hight-wrapper').style.display='none';
		}
	}
}

function showcolorOptions(option) {
	if($('likebox_colorschme-wrapper')) {
		if(option == 0) {
					$('likebox_default_colorschme-wrapper').style.display='block';
		}else{
				$('likebox_default_colorschme-wrapper').style.display='none';
		}
	}
}

function showfacesOptions(option) {
	if($('likebox_faces-wrapper')) {
		if(option == 0) {
					$('likebox_default_faces-wrapper').style.display='block';
		}else{
				$('likebox_default_faces-wrapper').style.display='none';
		}
	}
}

function showheaderOptions(option) {
	if($('likebox_header-wrapper')) {
		if(option == 0) {
					$('likebox_default_header-wrapper').style.display='block';
		}else{
				$('likebox_default_header-wrapper').style.display='none';
		}
	}
}
</script>