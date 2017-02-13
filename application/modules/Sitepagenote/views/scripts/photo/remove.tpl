<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: remove.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (!$this->format): ?>
	<?php 
	  include APPLICATION_PATH . '/application/modules/Sitepagenote/views/scripts/pageNoteHeader.tpl';
	?>
<?php endif; ?>

<?php if (!$this->format): ?>
  <div class='global_form'>
    <form method="post" class="global_form">
<?php else: ?>
    <form method="post" class="global_form_popup">
<?php endif; ?>
      <div>
        <div>
          <h3><?php echo $this->translate('Delete Note Photo?'); ?></h3>
          <p> <?php echo $this->translate('Are you sure you want to delete this photo?'); ?> </p>
          <br />
          <input type="hidden" name="confirm" value="true"/>
          <?php if ($this->format == 'smoothbox'): ?>
            <input type="hidden" name='format' value='<?php echo $this->format; ?>'>
          <?php endif; ?>
          <button type='submit' target="_parent"><?php echo $this->translate('Delete Photo'); ?></button>
          <?php echo $this->translate('or'); ?> 
          <?php if ($this->format != 'smoothbox') : ?>
            <?php echo $this->htmlLink(array('route' => 'sitepagenote_detail_view', 'user_id' => $this->sitepagenote->owner_id, 'note_id' => $this->sitepagenote->note_id, 'slug' => $this->sitepagenote->getSlug(), 'tab' => $this->identity_temp), $this->translate('cancel')) ?>
          <?php else : ?>
            <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
          <?php endif; ?>          
        </div>
      </div>	
    </form>
<?php if (!$this->format): ?>  
  </div>
<?php endif; ?>