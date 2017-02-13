<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
  include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?>
<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/modules/Sitepagedocument/externals/styles/style_sitepagedocument.css')
?>
<?php

$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
  function hidebutton() {
    if(document.getElementById('submit_button'))
      document.getElementById('submit_button').style.display='none';
    if(document.getElementById('loading_img'))
      document.getElementById('loading_img').style.display='block';
  }  
  
  function showlightbox() {
    document.getElementById('light').style.display='block';
    document.getElementById('fade').style.display='block';
  }
</script>

<?php if ($this->excep_error == 1): ?>
  <ul class="form-errors">
    <li>
      <?php echo $this->excep_message ?>
    </li>
  </ul>
<?php endif; ?>

<?php if ($this->is_error == 1): ?>
  <ul class="form-errors">
    <li>
      <?php echo $this->api_error ?>
    </li>
  </ul>
<?php endif; ?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
<div class="sitepage_viewpages_head">
  <?php echo $this->htmlLink($this->sitepage->getHref(), $this->itemPhoto($this->sitepage, 'thumb.icon', '', array('align' => 'left'))) ?>
  <?php if(!empty($this->can_edit)):?>
		<div class="fright">
			<a href='<?php echo $this->url(array('page_id' => $this->sitepage->page_id), 'sitepage_edit', true) ?>' class='buttonlink icon_sitepages_dashboard'><?php echo $this->translate('Dashboard');?></a>
		</div>
	<?php endif;?>
  <h2>	
    <?php echo $this->sitepage->__toString() ?>	
    <?php echo $this->translate('&raquo; '); ?>
    <?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Documents')) ?>
  </h2>
</div>
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addocumentcreate', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)): ?>
  <div class="layout_right" id="communityad_documentcreate">

	<?php
		echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addocumentcreate', 3),"loaded_by_ajax"=>0,'widgetId'=>'page_documentcreate')); 			 
	?>
  </div>
<?php endif; ?>
<div class="layout_middle sitepagedocument_form">
  <?php echo $this->form->render($this) ?>
</div>
<div id="light" class="white_content">
  <?php echo $this->translate('Uploading'); ?>
  <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepagedocument/externals/images/sitepagedocument-uploading.gif" alt="" />
</div>
<div id="fade" class="black_overlay"></div>