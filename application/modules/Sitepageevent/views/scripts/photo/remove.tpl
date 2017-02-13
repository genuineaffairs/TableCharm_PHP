<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: remove.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form method="post" class="global_form_popup">
  <div>
    <div>
      <h3><?php echo $this->translate('Delete Event Photo?'); ?></h3>
      <p> <?php echo $this->translate('Are you sure you want to delete this photo?'); ?> </p>
      <br />
      <input type="hidden" name="confirm" value="true"/>
      <?php if ($this->format == 'smoothbox'): ?>
        <input type="hidden" name='format' value='<?php echo $this->format; ?>'>
      <?php endif; ?>
      <button type='submit' target="_parent"><?php echo $this->translate('Delete Photo'); ?></button>
      <?php echo $this->translate('or'); ?> 
      <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>

    </div>
  </div>	
</form>
