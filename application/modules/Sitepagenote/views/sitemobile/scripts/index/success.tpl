<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: success.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
?>

<?php 
  //include APPLICATION_PATH . '/application/modules/Sitepagenote/views/scripts/pageNoteHeader.tpl';
?>
<?php 

$breadcrumb = array(
    array("href"=>$this->sitepage->getHref(),"title"=>$this->sitepage->getTitle(),"icon"=>"arrow-r"),
    array("href"=>$this->sitepage->getHref(array('tab' => $this->tab_selected_id)),"title"=>"Notes","icon"=>"arrow-d")
     );

echo $this->breadcrumb($breadcrumb);
?>
<div class="layout_middle" id="form-upload-success">
  <div class='global_form'>
    <form method="post" class="global_form" data-ajax="false">
      <div>
        <div>
          <h3><?php echo $this->translate('Add Photos'); ?></h3>
          <p>
            <?php if ($this->sitepagenote->draft == 0) : ?>
              <?php echo $this->translate('The note for your Page has been successfully published. Would you like to add some photos to it?'); ?>
            <?php else : ?>
              <?php echo $this->translate('The note for your Page has been successfully drafted. Would you like to add some photos to it?'); ?>
            <?php endif; ?>
          </p>
          <br />
          <p>
            <input type="hidden" name="confirm" value="true"/>
            <button type='submit' data-theme="b"><?php echo $this->translate('Add Photos'); ?></button>
            <div style="text-align: center"><?php echo $this->translate('or'); ?> </div>
            <a href= '<?php echo $this->sitepage->getHref(array('tab'=>$this->tab_selected_id));?>' data-role="button">
              <?php echo $this->translate('Continue to Page') ?>
            </a>
          </p>
        </div>
      </div>
    </form>
  </div>
</div>	



<script type="text/javascript">

  sm4.core.runonce.add(function() { 
    if (DetectAllWindowsMobile()) {
      $.mobile.activePage.find('#form-upload-success').css('display', 'none');
      $.mobile.activePage.find('#show_supported_message').css('display', 'block');
    } else {
      $.mobile.activePage.find('#form-upload-success').css('display', 'block');
      $.mobile.activePage.find('#show_supported_message').css('display', 'none');
    } 
  });

</script>


<div style="display:none" id="show_supported_message" class='tip'>

  <span><?php echo $this->translate("Sorry, the browser you are using does not support Photo uploading. You can upload photo for note from your Desktop."); ?><span>

</div>