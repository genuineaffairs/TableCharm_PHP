<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: profile-info.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div id='sitepageevent_stats'>
  <ul>
    <li class="sitepageevent_stats_title">
      <?php echo $this->subject()->getTitle(); ?>      
    </li>
    <li>
      <?php echo $this->subject()->description; ?>
    </li>
    <li>
      <?php echo $this->translate('Starts:'); ?> <?php echo $this->dateTime($this->subject()->starttime); ?>
      <br /><?php echo $this->translate('Ends:'); ?> <?php echo $this->dateTime($this->subject()->endtime); ?>
    </li>
    <?php if (!empty($this->subject()->host)): ?>
      <li>
        <?php $this->translate('Hosted by'); ?> <?php echo $this->subject()->host; ?>
      </li>
      <li>
        <?php $this->translate('Created by'); ?> <?php echo $this->subject()->getParent()->__toString(); ?>
      </li>
    <?php endif; ?>
    <?php if (!empty($this->subject()->location)): ?>
      <li>
        <?php $this->translate('Location:'); ?> <?php echo $this->subject()->location; ?> [<?php echo $this->htmlLink('http://maps.google.com/?q=' . urlencode($this->subject()->location), $this->translate('map'), array('target' => 'blank')) ?>]
      </li>
    <?php endif; ?>
    <li class="sitepageevent_stats_info">
      <ul>
        <li><?php echo $this->translate(array('%s person', '%s people', $this->subject()->getAttendingCount()), $this->locale()->toNumber($this->subject()->getAttendingCount())) ?> <?php echo $this->translate('attending'); ?></li>
        <li><?php echo $this->translate(array('%s person', '%s people', $this->subject()->getMaybeCount()), $this->locale()->toNumber($this->subject()->getMaybeCount())) ?> <?php echo $this->translate('maybe attending'); ?></li>
        <li><?php echo $this->translate(array('%s person', '%s people', $this->subject()->getNotAttendingCount()), $this->locale()->toNumber($this->subject()->getNotAttendingCount())) ?> <?php echo $this->translate('not attending'); ?></li>
      </ul>
    </li>
  </ul>
</div>