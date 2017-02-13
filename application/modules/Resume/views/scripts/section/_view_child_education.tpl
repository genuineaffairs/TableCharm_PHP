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
$p1 = array();
if ($child->concentration) $p1[] = $child->concentration;
if ($child->degree) $p1[] = $child->degree;

?>

<div class="resume_body_education_school">
  <span class='bold'>Institution</span><br />
  <span class='nobold'><?php echo $child->getTitle(); ?></span>
</div>

<?php if ($child->degree || $child->concentration || $child->class_year): ?>
<div class="resume_body_education_details">
  <?php if ($child->degree): ?>
    <span class='bold'>Course</span><br />
    <span class="resume_body_education_degree nobold"><?php echo $child->degree; ?></span>
  <?php endif; ?>
  <?php if ($child->degree && $child->concentration): ?>
    <?php echo $this->translate('in')?>
  <?php endif; ?>
  <?php if ($child->concentration): ?>
    <span class="resume_body_education_concentration"><?php echo $child->concentration; ?></span>
  <?php endif; ?>
  <?php if ($child->class_year): ?>
  <br /><span class='bold'>Year Graduated</span><br />
  <span class="resume_body_education_date nobold">
    <?php echo $child->class_year; ?>
  </span>
  <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($child->getDescription()): ?>
<div class="resume_body_description resume_body_education_description">
  <?php echo $child->getDescription(); ?>
</div>
<?php endif; ?>