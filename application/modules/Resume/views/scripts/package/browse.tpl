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
    <span><?php echo $this->translate("Resume Posting Packages")?></span>
  </h3>
  
  <div class="resumes_packages_intro">
    <?php echo $this->translate("Check out our flexible resume posting packages below."); ?>
  </div>
  
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
      
      <?php if ($package->getDescription()): ?>
        <div class="resumes_packages_list_desc"><?php echo nl2br($package->getDescription())?></div>
      <?php endif; ?>
        <div class="resumes_packages_list_view_details"><?php echo $this->htmlLink($package->getHref(), $this->translate('View Details'))?></div>
      <?php if ($this->can_create): ?>
        <div class="resumes_packages_list_submit">
          <?php echo $this->htmlLink(array('route' => 'resume_general', 'action' => 'create', 'package' => $package->getIdentity()),
            $this->translate("Post New Resume &raquo;"),
            array('class' => 'resume_create_button')
          ); ?>
        </div>
      <?php endif; ?>
    </li>
  <?php endforeach; ?>  
  </ul>
