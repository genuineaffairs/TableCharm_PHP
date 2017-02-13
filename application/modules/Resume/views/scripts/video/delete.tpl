<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="layout_middle wrapper_padding">
  <div class="sitepage_viewpages_head">
    <h2>	
      <?php echo $this->resume->__toString() ?>	
      <?php echo $this->translate('&raquo; '); ?>
      <?php echo $this->htmlLink($this->resume->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Videos')) ?>
    </h2>
  </div>
  <div class='global_form'>
    <form method="post" class="global_form">
      <div>
        <div>
          <h3><?php echo $this->translate('Delete Page Video ?'); ?></h3>
          <p> 
            <?php echo $this->translate('Are you sure that you want to delete the page video titled "%1$s" last modified %2$s? It will not be recoverable after being deleted.', $this->title, $this->timestamp($this->resume_video->modified_date)) ?>
          </p>
          <br />
          <p>
            <input type="hidden" name="confirm" value="true"/>
            <button type='submit' ><?php echo $this->translate('Delete'); ?></button>
            	<?php echo $this->translate('or'); ?> <?php echo $this->htmlLink($this->resume->getHref(array('tab'=>$this->tab_selected_id)),$this->translate('cancel')) ?>
          </p>
        </div>
      </div>
    </form>
  </div>
</div>	