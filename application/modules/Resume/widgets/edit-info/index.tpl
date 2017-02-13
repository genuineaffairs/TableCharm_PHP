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

<?php 
$resume = $this->resume; 
$recentEpayment = $resume->getRecentEpayment();?>

<?php if (!$resume->published): ?>
    <div class="resume_published_tip tip">
      <span>
        <?php echo $this->translate("NOTICE: This resume is in DRAFT mode, and would not be viewable by others. Once you've reviewed its content, you can change it to LIVE mode by publish it.");?>
      </span>
    </div>
    <div class="resume_publish_action">
      <?php echo $this->htmlLink($resume->getActionHref('publish'), $this->translate('PUBLISH CV NOW'), array('class' => 'resume_publish_button smoothbox')); ?>
    </div>
    <br />
<?php endif; ?>

<div class="resume_edit_info">
  <ul>
    <li>
      <?php echo $this->translate('Resume ID: %s', $resume->getIdentity())?>
    </li>
    <li>
      <?php echo $this->translate('Title: %s', $this->htmlLink($resume->getHref(), $this->radcodes()->text()->truncate($resume->getTitle(), 26)))?>
    </li>
	  <li>
	    <?php echo $this->translate('Package: %s', $resume->getPackage()->toString()); ?>
	  </li>
    <li>
      <?php echo $this->translate('Publish: %s', $this->translate($resume->isPublished() ? 'Live' : 'Draft')); ?>
    </li>
	  <li>
	    <?php echo $this->translate('Status: %1$s - %2$s', $resume->getStatusText(), $this->locale()->toDate($resume->status_date));?>
	  </li>
	  <li>
	    <?php if ($resume->hasExpirationDate()): ?>
	      <?php $expire_date = $this->timestamp($resume->expiration_date); ?>
	    <?php else: ?>
	      <?php $expire_date =  $this->translate('Never')?>
	    <?php endif; ?>
	    <?php echo $this->translate('Expire: %s', $expire_date); ?>
	  </li>
	  <li>
	    <?php echo $this->translate('Featured: %s', $this->translate($resume->featured ? 'Yes' : 'No'))?>
	  </li>
	  <li>
	    <?php echo $this->translate('Sponsored: %s', $this->translate($resume->sponsored ? 'Yes' : 'No'))?>
	  </li>
	  <li class="resume_settings_epayment">
	    <?php if ($resume->requiresEpayment()): ?>
	      <?php if ($recentEpayment instanceof Epayment_Model_Epayment): ?>
	        <?php echo $this->translate('Payment: received'); ?>
    	    <br />
            #<?php echo $recentEpayment->getIdentity()?>
            <?php echo $this->translate($recentEpayment->getStatusText())?>
            <?php echo $this->locale()->toDate($recentEpayment->creation_date)?>
            <?php echo $this->htmlLink($resume->getActionHref('packages'), $this->translate('RENEW / UPGRADE'), array('class' => 'resume_paynow'))?>
	      <?php else: ?>
	        <?php echo $this->translate('Payment: required'); ?>
	        <?php echo $this->htmlLink($resume->getCheckoutHref(), $this->translate('PAY NOW'), array('class' => 'resume_paynow'))?>
	      <?php endif; ?>
	    <?php else: ?>
	      <?php echo $this->translate('Payment: not required'); ?>
          <?php echo $this->htmlLink($resume->getActionHref('packages'), $this->translate('RENEW / UPGRADE'), array('class' => 'resume_paynow'))?>
	    <?php endif; ?>
	  </li>
  </ul>
</div>