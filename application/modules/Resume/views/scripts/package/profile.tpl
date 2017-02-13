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

<?php $package = $this->package;?>
  <div class="resume_package_view">
    <h3 class="sep resume_main_header">
      <span><?php echo $this->translate($this->package->getTitle())?></span>
    </h3>
  
    <div class="resume_package_gutter">
    
      <?php if ($this->package->photo_id): ?>
        <?php // echo $this->itemPhoto($this->package, 'thumb.profile'); ?>    
      <?php endif; ?>
      
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
  
    </div>
  
    <div class="resume_package_body">
    <?php if ($this->package->body): ?>
      <?php echo $this->package->body; ?>
    <?php else: ?>
      <?php echo nl2br($this->package->getDescription()); ?>
    <?php endif; ?>
    </div>
    
      <?php if ($this->can_create): ?>

        <div class="resumes_packages_list_submit">
          <?php echo $this->htmlLink(array('route' => 'resume_general', 'action' => 'create', 'package' => $this->package->getIdentity()),
            $this->translate("Post New Resume &raquo;"),
            array('class'=>'resume_create_button')
          ); ?>
        </div>
      <?php endif; ?>    
    
  </div>    
  
