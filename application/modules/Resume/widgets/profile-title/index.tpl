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
<?php $resume = $this->resume; ?>
<div class="resume_profile_title resume_profile_title_featured_<?php echo $resume->featured ? 'yes' : 'no'?> resume_profile_title_sponsored_<?php echo $resume->sponsored ? 'yes' : 'no'?>">
  <h3><?php echo $resume->getTitle(); ?></h3>
  <?php if ($resume->getDescription()): ?>
  <div class="resume_description">
    <?php echo $resume->getDescription(); ?>
  </div>
  <?php endif; ?>
  <?php /*
  <div class="resume_details">
    <?php echo $this->partial('index/_details.tpl', 'resume', array('resume' => $resume))?>
  </div> */ ?>
  <div class="resume_meta">
    <?php echo $this->partial('index/_meta.tpl', 'resume', array('resume' => $resume, 'show_owner' => true))?>
  </div>  
</div>