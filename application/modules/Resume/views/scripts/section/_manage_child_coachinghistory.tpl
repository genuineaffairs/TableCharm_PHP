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

<div class="resume_sections_child_details">
  <div class="resume_sections_child_title">
    <span class="label">Team</span><br />
    <?php echo $child->team_name; ?>
  </div>
  <?php if($child->position) : ?>
  <div class="resume_sections_child_company">
    <span class="label">Position</span><br />
    <?php echo $child->position; ?>
  </div>
  <?php endif; ?>
</div>
<div class="resume_sections_child_details">
  <?php if($child->location) : ?>
  <div class="resume_sections_child_company">
    <span class="label">Location</span><br />
    <?php echo $child->location; ?>
  </div>
  <?php endif; ?>
  <?php if($child->competition) : ?>
  <div class="resume_sections_child_company">
    <span class="label">Grade of Competition</span><br />
    <?php echo $child->competition; ?>
  </div>
  <?php endif; ?>
</div>
<div class="resume_sections_child_date">
  <?php echo $time_period; ?>
</div>
