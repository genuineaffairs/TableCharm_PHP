<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $include_file = 1;?>
<?php 

$breadcrumb = array(
    array("href"=>$this->sitepage->getHref(),"title"=>$this->sitepage->getTitle(),"icon"=>"arrow-r"),
    array("href"=>$this->sitepage->getHref(array('tab' => $this->tab_selected_id)),"title"=>"Notes","icon"=>"arrow-d"));

echo $this->breadcrumb($breadcrumb);
?>
<div class="layout_middle">
	<div class="sitepagenote_form">
	  <form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" class="global_form" id="form-upload-sitepagenote">
	    <div>
	      <div>
	        <h3>
	          <?php echo $this->translate($this->form->getTitle()) ?>        
	        </h3>
	        <?php echo $this->translate($this->form->getDescription()) ?>
	        <div class="form-elements">
	          <?php echo $this->form->title; ?>
	          <?php echo $this->form->tags; ?>
	          <?php echo $this->form->body; ?>
	          <?php echo $this->form->draft; ?>
	          <?php echo $this->form->search; ?>
	          <div class="form-wrapper">
	            <div class="form-label">&nbsp;</div>
	            <div class="form-element">
	              <?php echo $this->form->execute->render(); ?>
	            <div style="text-align: center"><?php echo $this->translate('or'); ?> </div>
            <a href="#" data-rel="back" data-role="button">
            <?php echo $this->translate('Cancel') ?>
            </a>
	            </div>
	          </div>			
	        </div>
	      </div>
	    </div>
	  </form>
	</div>
</div>	

<script type="text/javascript">
  sm4.core.runonce.add(function() { 
    if (DetectAllWindowsMobile()) {
      $.mobile.activePage.find('#dummy-wrapper').css('display', 'block');
      $.mobile.activePage.find('#photo-wrapper').css('display', 'none');
    } else {
      $.mobile.activePage.find('#photo-wrapper').css('display', 'block');
      $.mobile.activePage.find('#dummy-wrapper').css('display', 'none');
    } 
  });
</script>