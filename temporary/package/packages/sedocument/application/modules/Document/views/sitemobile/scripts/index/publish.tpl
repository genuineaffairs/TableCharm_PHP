<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: publish.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class='global_form_popup'>
  <?php if ($this->success): ?>
    <div class="global_form_popup_message">
      <?php echo $this->translate('Your document has been published.'); ?> 
    </div>
  <?php else: ?>
    <form method="POST" action="<?php echo $this->url() ?>">
      <div>
        <h3><?php echo $this->translate('Publish Document ?'); ?></h3>
        <p>
          <?php echo $this->translate('Are you sure that you want to publish this document ?'); ?>
        </p>
       
        <br/>
       
          <input type="hidden" name="document_id" value="<?php echo $this->document_id ?>"/>
          <button type='submit' data-theme="b"><?php echo $this->translate('Publish'); ?></button>
           <?php echo $this->translate('or'); ?>
        <a href="#" data-rel="back" data-role="button">
          <?php echo $this->translate('Cancel') ?>
        </a>

      </div>
    </form>
  <?php endif; ?>
</div>
