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
$child = $this->child;
$time_period = $this->partial('section/_employment_time_period.tpl', 'resume', array('employment' => $this->child));
?>

<div class="resume_body_employment_date">
  <?php echo $time_period; ?>
</div>

<div class="resume_body_employment_details">
  <span class="resume_body_employment_company">
    <?php echo $child->company; ?>
  </span>
  <?php if ($child->location): ?>
    <span class="resume_body_employment_location">
      <?php echo $child->location; ?>
    </span>
  <?php endif; ?>
</div>

<div class="resume_body_employment_position">
  <?php echo $child->getTitle(); ?>
</div>
<?php if ($child->getDescription()): ?>
<div class="resume_body_description resume_body_employment_description">
  <?php echo $child->getDescription(); ?>
</div>
<?php endif; ?>
