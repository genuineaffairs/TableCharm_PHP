<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
$breadcrumb = array(
    array("href"=>$this->sitepage->getHref(),"title"=>$this->sitepage->getTitle(),"icon"=>"arrow-r"),
    array("href"=>$this->sitepage->getHref(array('tab' => $this->tab_selected_id)),"title"=>"Documents","icon"=>"arrow-d")
    );

echo $this->breadcrumb($breadcrumb);
?>
<?php
	$this->headLink()
       ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/modules/Sitepagedocument/externals/styles/style_sitepagedocument.css')
?>
<script type="text/javascript">
  function hidebutton() {
    if($('#submit_button'))
      $('#submit_button').style.display='none';
//    if($('#loading_img'))
//      $('#loading_img').style.display='block';
  }  
  
  function showlightbox() {
    $('#light').css('display','block');
    $('#fade').css('display','block');
  }
</script>

<!--<div class="sitepage_viewpages_head">
  <?php echo $this->htmlLink($this->sitepage->getHref(), $this->itemPhoto($this->sitepage, 'thumb.icon', '', array('align' => 'left'))) ?>
  <h2>	
    <?php echo $this->sitepage->__toString() ?>	
    <?php echo $this->translate('&raquo; '); ?>
    <?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Documents')) ?>
  </h2>
</div>-->

<div class="layout_middle sitepagedocument_form" id="sitepagedocument_form">
  <?php echo $this->form->render($this) ?>
</div>	

<div id="fade" class="black_overlay"></div>



<script type="text/javascript">

  sm4.core.runonce.add(function() { 
    if (DetectAllWindowsMobile()) {
      $.mobile.activePage.find('#sitepagedocument_form').css('display', 'none');
      $.mobile.activePage.find('#form-upload').css('display', 'none');
      $.mobile.activePage.find('#show_supported_message').css('display', 'block');
    } else {
      $.mobile.activePage.find('#sitepagedocument_form').css('display', 'block');
      $.mobile.activePage.find('#form-upload').css('display', 'block');
      $.mobile.activePage.find('#show_supported_message').css('display', 'none');
    } 
  });

</script>


<div style="display:none" id="show_supported_message" class='tip'>

  <span><?php echo $this->translate("Sorry, the browser you are using does not support Document uploading. You can upload document from your Desktop."); ?><span>

</div>