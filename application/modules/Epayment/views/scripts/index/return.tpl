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
        <h3><?php echo $this->translate('Payment Success');?></h3>
        <p>
          <?php echo $this->translate('Thank you for making payment.'); ?>
          <br/>
          <?php echo $this->translate('We will process your transaction as soon as it is confirmed by PayPal.')?>
        </p>
        <br />
        <p>
          <?php echo $this->htmlLink($this->subject()->getHref(), $this->translate('Continue to view %s page', $this->subject()->getTitle()))?>
        </p>
    </div>
  </div>
</div>
