<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: upload.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<!--BREADCRUMB WORK -->
<?php 
$breadcrumb = array(
	array("href"=>$this->sitepage->getHref(),"title"=>$this->sitepage->getTitle(),"icon"=>"arrow-r"),
	array("href"=>$this->sitepage->getHref(array('tab' => $this->tab_selected_id)),"title"=>"Events","icon"=> "arrow-d"),
);

echo $this->breadcrumb($breadcrumb);
?>

<!--BREADCRUMB WORK-->
<div class="layout_middle">
  <?php echo $this->form->render($this) ?>
</div>
<script type="text/javascript">
  var event_id = '<?php echo $this->event_id ?>';
</script>


<script type="text/javascript">

  sm4.core.runonce.add(function() { 
    if (DetectAllWindowsMobile()) {
      $.mobile.activePage.find('#form-upload').css('display', 'none');
      $.mobile.activePage.find('#show_supported_message').css('display', 'block');
    } else {
      $.mobile.activePage.find('#form-upload').css('display', 'block');
      $.mobile.activePage.find('#show_supported_message').css('display', 'none');
    } 
  });

</script>


<div style="display:none" id="show_supported_message" class='tip'>

  <span><?php echo $this->translate("Sorry, the browser you are using does not support Photo uploading. You can upload photo for event from your Desktop."); ?><span>

</div>