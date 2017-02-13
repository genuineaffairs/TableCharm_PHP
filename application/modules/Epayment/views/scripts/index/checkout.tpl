<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Epayment
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 

/*
<div id="global_content">   
  
  <div class="epayment_checkout">
    <h2><?php echo $this->translate('Epayment Checkout'); ?></h2>
    <div class="epayment_checkout_description">
      <?php echo $this->translate('Please review and confirm the item you are about to check out, then press Pay Now button to proceed.'); ?>
    </div>
  
    <h3>
      <?php echo $this->subject()->__toString(); ?>
    </h3>
      
    <div class="epayment_checkout_item_details">

      <div class="">
        <?php echo $this->translate('Package: %s', $this->subject()->getPackage()->__toString()); ?>
        |
        <?php echo $this->translate('Term: %s', $this->subject()->getPackage()->getTerm()); ?>
      </div>
      
    </div>   
    
  </div>
</div>
*/
?>
<?php echo $this->form->render($this) ?>