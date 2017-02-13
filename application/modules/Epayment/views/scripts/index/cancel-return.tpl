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
?> 

<div class='global_form'>
  <div>
      <div>
        <h3><?php echo $this->translate('Payment Cancel');?></h3>
        <p>
          <?php echo $this->translate('Your payment has not been processed due to error/cancel on PayPal.'); ?>
        </p>
        <br />
        <p>
          <button type='submit' onclick="window.location.href='<?php echo $this->subject()->getCheckoutHref()?>'; return false;"><?php echo $this->translate('Check Out Again');?></button>
          <?php echo $this->translate('or');?>
          <?php echo $this->htmlLink($this->subject()->getHref(), $this->translate('view %s page', $this->subject()->getTitle()))?>
        </p>
    </div>
  </div>
</div>
