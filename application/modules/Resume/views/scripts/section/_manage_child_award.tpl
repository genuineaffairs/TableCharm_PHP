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
?>

<div class="resume_sections_child_title">
  <span class="label">Honours or Awards</span><br />
  <?php echo $child->award; ?>
</div>
<div class="resume_sections_child_date">
<?php if ($child->year): ?>
  <span class="label">Year</span><br />
  <?php echo $child->year; ?>
<?php else: ?>
  &nbsp;  
<?php endif; ?>
</div>
<div class="resume_sections_child_details">
  <div class="resume_sections_child_company">
    <span class="label">Competition / League</span><br />
    <?php echo $child->competition; ?>
  </div>
  <div class="resume_sections_child_company">
    <span class="label">Level</span><br />
    <?php echo $child->level; ?>
  </div>
</div>