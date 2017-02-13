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
<div class='resume_profile_details'>
  <div class="profile_fields">

    <?php $label = 'Gender'; ?>
    <?php $valueString = $resume->getFieldValueString($label); ?>
    <?php if($valueString) : ?>
    <div class="resume_summary_topfields">
      <span><?php echo $label ?></span>
      <span><?php echo $valueString; ?></span>
    </div>
    <?php endif; ?>

    <?php $label = 'Date of Birth'; ?>
    <?php $valueString = $resume->getFieldValueString($label); ?>
    <?php if($valueString) : ?>
    <div class="resume_summary_topfields">
      <span><?php echo $label ?></span>
      <span><?php echo $valueString; ?></span>
    </div>
    <?php endif; ?>

    <div class="resume_summary_participation_level">
      <span>Participation Level</span>
      <span><?php echo Engine_Api::_()->getItem('resume_category', $resume->category_id)->category_name; ?></span>
    </div>
    <?php echo $this->fieldValues; ?>
  </div>  
</div>
