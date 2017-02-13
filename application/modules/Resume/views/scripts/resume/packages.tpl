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

  <h3 class="sep resume_main_header">
    <span><?php echo $this->translate("Renew / Upgrade Package")?></span>
  </h3>
  
  <div class="resumes_packages_intro">
    <?php echo $this->translate("You may be able to renew or upgrade to other packages listed below. Renew or upgrade will take effective immediately upon receiving of your payment."); ?>
  </div>
  
  <?php $recentEpayment = $this->resume->getRecentEpayment(); ?>
  
  <ul class="resumes_packages_list">
  <?php foreach ($this->packages as $package): ?>
    <li>
      <div class="resumes_packages_list_title">
        <?php echo $this->htmlLink($package->getHref(), $this->translate($package->getTitle())); ?>
      </div>
      <ul class="resumes_packages_list_details">
        <li class="resumes_packages_list_details_term">
          <span><?php echo $this->translate('Term:')?></span>
          <?php echo $package->getTerm(); ?>
        </li>
        <li class="resumes_packages_list_details_featured">
          <span><?php echo $this->translate('Featured:')?></span>
          <?php echo $this->translate($package->featured ? 'Yes' : 'No'); ?>
        </li>
        <li class="resumes_packages_list_details_sponsored">
          <span><?php echo $this->translate('Sponsored:')?></span>
          <?php echo $this->translate($package->sponsored ? 'Yes' : 'No'); ?>
        </li>
      </ul>
      <?php ?>
      
      <?php if ($package->isSelf($this->resume->getPackage())): ?>
        <div class="tip">
          <span>
            <?php echo $this->translate('This resume is currently subscribing to this package.');?>
          </span>
        </div>
      <?php endif; ?>
      
      <?php if ($package->getDescription()): ?>
        <div class="resumes_packages_list_desc"><?php echo nl2br($package->getDescription())?></div>
      <?php endif; ?>

      <div class="resumes_packages_list_submit">
      <?php if ($package->isSelf($this->resume->getPackage())): ?>
        <?php if ($recentEpayment instanceof Epayment_Model_Epayment): ?>
          <?php if (!$package->isFree() && $package->allow_renew): ?>
  	        <?php echo $this->htmlLink($this->resume->getCheckoutHref(),
  	          $this->translate("Renew Now &raquo;"),
  	          array('class' => 'resume_post_renew')
  	        ); ?>
  	      <?php else: ?>
            <span class="resume_cannot_renew"><?php echo $this->translate('Cannot be renewed')?></span>
          <?php endif; ?>  
        <?php else: ?>
          <?php if ($package->isFree()): ?>
            <span class="resume_cannot_renew"><?php echo $this->translate('Cannot be renewed')?></span>
          <?php else: ?>
  	        <?php echo $this->htmlLink($this->resume->getCheckoutHref(),
  	          $this->translate("Pay Now &raquo;"),
  	          array('class' => 'resume_post_checkout')
  	        ); ?>
          <?php endif; ?>
        <?php endif; ?>
      <?php else: ?>
        <?php if (!$package->isFree() && $package->allow_upgrade): ?>
	        <?php echo $this->htmlLink($this->resume->getCheckoutHref(array('package_id'=>$package->getIdentity())),
	          $this->translate("Upgrade Now &raquo;"),
	          array('class' => 'resume_post_upgrade')
	        ); ?>
        <?php else: ?>
          <span class="resume_cannot_upgrade"><?php echo $this->translate('Cannot be upgraded')?></span>
        <?php endif; ?>
      <?php endif; ?>
      </div>
    </li>
  <?php endforeach; ?>
  </ul>

