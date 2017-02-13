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
      $('#submit_button').css('display','none');
  }  
  
  function showlightbox() {
    $('#light').css('display','block');
    $('#fade').css('display','block');
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
