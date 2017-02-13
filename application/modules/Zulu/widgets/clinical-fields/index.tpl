<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<div class='profile_fields'>
  <?php echo $this->htmlLink($this->subject->getParent()->getHref() . '/print', 'Print Medical Record', array(
    'class' => 'buttonlink icon_zulu_print',
    'alt' => 'Print Medical Record',
    'target' => '_blank'
  )); ?>
</div>

<div class="profile_fields">
  <h3 class="medical_record_heading">Medical Record</h3>
</div>

<div class="medical_sections">
  <div class="profile_fields">
    <h4>OWNER OF MEDICAL RECORD</h4>
    <ul>
      <li>
        <?php echo '<span>Full Name</span> ' . '<span>' . $this->subject->getOwner()->getTitle() . '</span>' ?>
      </li>
      <li>
        <?php echo '<span>Date of Birth</span> ' . '<span>' . $this->subject->getUserFieldValueString('birthdate') . '</span>' ?>
      </li>
    </ul>
  </div>
  <?php echo $this->zuluFieldValueLoop($this->subject, $this->fieldStructure) ?>
</div>
