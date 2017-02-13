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
$sections = $this->resume->getSections();

$coms = array();
if ($resume->phone) {
  $coms[] = $this->translate('Phone: %s', $resume->phone);
}
if ($resume->mobile) {
  $coms[] = $this->translate('Mobile: %s', $resume->mobile);
}
if ($resume->fax) {
  $coms[] = $this->translate('Fax: %s', $resume->fax);
}
if ($resume->email) {
  $coms[] = $this->translate('Email: %s', $resume->email);
}
?>

<div class='resume_profile_body'>
  
  <div id="resume_section_contact">
    <div class="resume_section_contact_name"><?php echo $resume->name; ?></div>
    <?php if ($resume->location): ?>
    <div class="resume_section_contact_location">
      <?php echo $this->partial('index/_address.tpl', 'resume', array('resume'=>$resume))?>
    </div>
    <?php endif; ?>
    <?php if (!empty($coms)): ?>
    <div class="resume_section_contact_communication">
      <?php echo join(" <strong>&middot;</strong> ", $coms); ?>
    </div>
    <?php endif;?>
    <?php if ($resume->website): ?>
    <div class="resume_section_contact_website">
      <?php echo $this->translate('Website: %s', $resume->website) ?>
    </div>
    <?php endif; ?>
  </div>
  
<?php foreach ($sections as $section):  $child_type = strtolower($section->child_type); ?>
  <div id="resume_section_<?php echo $section->getIdentity(); ?>" class="resume_profile_body_section">
    <div class="resume_profile_body_section_title">
      <?php echo $this->translate($section->getTitle()); ?>
    </div>
    <div class="resume_profile_body_section_content">
    <?php if ($description = trim($section->getDescription())): ?>
      <div class="resume_body_description resume_profile_body_section_description">
        <?php echo $description; ?>
      </div>
    <?php endif; ?>
    
    <?php if (!$section->isChildTypeText()): $children = $section->getChildItems(); ?>
      <?php if (!empty($children)) :?>
        <ul id="resume_section_children_<?php echo $section->getIdentity(); ?>" class="resume_profile_body_section_children resume_profile_body_section_children_<?php echo $child_type; ?>">
          <?php foreach ($children as $child): ?>
            <li id="resume_section_child_<?php echo $child->getIdentity(); ?>">
              <?php 
                echo $this->partial('section/_view_child_'.$child_type.'.tpl', 'resume', array('child' => $child));
              ?>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    <?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>
  
</div>