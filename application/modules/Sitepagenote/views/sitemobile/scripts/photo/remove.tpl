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

  <div class='global_form'>
    <form method="post" class="global_form">
        <div>
          <h3><?php echo $this->translate('Delete Note Photo?'); ?></h3>
          <p> <?php echo $this->translate('Are you sure you want to delete this photo?'); ?> </p>
          <br />
          <input type="hidden" name="confirm" value="true"/>
          <button type='submit' target="_parent" data-theme="b" data-inline="true"><?php echo $this->translate('Delete Photo'); ?></button> 
          <?php echo $this->translate('or'); ?>
          <a href="#" data-rel="back" data-role="button" data-inline="true">
            <?php echo $this->translate('Cancel') ?>
          </a>
        </div>
    </form>
</div>