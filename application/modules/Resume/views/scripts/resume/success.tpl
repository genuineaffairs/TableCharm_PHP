<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
?>

	<div class='global_form'>
	  <div>
	    <div class="resume_success_panel">
	      <h3><?php echo $this->translate('Resume Posted');?></h3>
	      <p>
	        <?php echo $this->translate('Your resume was successfully saved.');?>
	      </p>
	      <br />
	      <p class="resume_success_notice">
	        <?php if ($this->resume->isApprovedStatus()): ?>
	          <?php echo $this->translate('It has been automatically approved and live.')?>
	        <?php else: ?>
	          <?php echo $this->translate('Administrator will review your resume, once it is approved, it will be get listed.'); ?>
	        <?php endif;?>
	        <?php echo $this->translate('You can %1$s or %2$s.', 
	          $this->htmlLink($this->resume->getEditHref(), $this->translate('edit')),
	          $this->htmlLink($this->resume->getHref(), $this->translate('continue to view this resume'))
	        )?>
	      </p>
	      <?php if ($this->resume->requiresEpayment()): ?>
	        <p class="resume_payment_note">
	          <?php echo $this->translate('NOTE: Payment is required for posting this resume with %s package. In order to get publicly listed, please proceed with making payment by clicking on the Pay Now button below.',
	            $this->resume->getPackage()->toString()
	          )?>
	        </p>
	        <br />
	        <p>
	        <?php echo $this->htmlLink($this->resume->getCheckoutHref(), $this->translate('Pay Now'), array('class' => 'resume_paynow_button'))?>
	        </p>
	      <?php endif; ?>
	      <p>
	      </p>
	    </div>
	  </div>
	</div>

