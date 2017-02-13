<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div id='sitepageevent_stats'>
  <ul>
    <?php if (!empty($this->subject()->description)): ?>
      <li>
        <?php echo nl2br($this->subject()->description) ?>
      </li>
    <?php endif ?>
    <li class="sitepageevent_date">
      <?php
      // Convert the dates for the viewer
      $startDateObject = new Zend_Date(strtotime($this->subject()->starttime));
      $endDateObject = new Zend_Date(strtotime($this->subject()->endtime));
      if ($this->viewer() && $this->viewer()->getIdentity()) {
        $tz = $this->viewer()->timezone;
        $startDateObject->setTimezone($tz);
        $endDateObject->setTimezone($tz);
      }
      ?>
      <?php if ($this->subject()->starttime == $this->subject()->endtime): ?>
        <div class="label">
          <?php echo $this->translate('Date') ?>
        </div>
        <div class="sitepageevent_stats_content">
          <?php echo $this->locale()->toDate($startDateObject) ?>
        </div>

        <div class="label">
          <?php echo $this->translate('Time') ?>
        </div>
        <div class="sitepageevent_stats_content">
          <?php echo $this->locale()->toTime($startDateObject) ?>
        </div>

      <?php elseif ($startDateObject->toString('y-MM-dd') == $endDateObject->toString('y-MM-dd')): ?>
        <div class="label">
          <?php echo $this->translate('Date') ?>
        </div>
        <div class="sitepageevent_stats_content">
          <?php echo $this->locale()->toDate($startDateObject) ?>
        </div>

        <div class="label">
          <?php echo $this->translate('Time') ?>
        </div>
        <div class="sitepageevent_stats_content">
          <?php echo $this->locale()->toTime($startDateObject) ?>
          -
          <?php echo $this->locale()->toTime($endDateObject) ?>
        </div>
      <?php else: ?>  
        <div class="sitepageevent_stats_content">
          <?php
          echo $this->translate('%1$s at %2$s', $this->locale()->toDate($startDateObject), $this->locale()->toTime($startDateObject)
          )
          ?>
          - <br />
          <?php
          echo $this->translate('%1$s at %2$s', $this->locale()->toDate($endDateObject), $this->locale()->toTime($endDateObject)
          )
          ?>
        </div>
      <?php endif ?>
    </li>    
    <?php if (!empty($this->subject()->location)): ?>
      <li>
        <div class="label"><?php echo $this->translate('Where') ?></div>
        <div class="sitepageevent_stats_content"><?php echo $this->subject()->location; ?> <?php echo $this->htmlLink('http://maps.google.com/?q=' . urlencode($this->subject()->location), $this->translate('Map'), array('target' => 'blank')) ?></div>
      </li>
    <?php endif; ?>

    <?php if (!empty($this->subject()->host)): ?>
      <?php if ($this->subject()->host != $this->subject()->getParent()->getTitle()): ?>
        <li>
          <div class="label"><?php echo $this->translate('Host'); ?></div>
          <div class="sitepageevent_stats_content"><?php echo $this->subject()->host; ?></div>
        </li>
      <?php endif; ?>
      <li>
        <div class="label"><?php echo $this->translate('Led by'); ?></div>
        <div class="sitepageevent_stats_content"><?php echo $this->subject()->getParent()->__toString(); ?></div>
      </li>
    <?php endif; ?>  
      
    <?php if( !empty($this->subject()->category_id) ): ?>
      <li>
        <div class="label"><?php echo $this->translate('Category')?></div>
        <div class="sitepageevent_stats_content">
          <?php echo $this->htmlLink(array(
            'route' => 'sitepageevent_browse',
            'event_category_id' => $this->subject()->category_id,
          ), $this->translate((string)$this->subject()->categoryName())) ?>
        </div>
      </li>
    <?php endif ?>      
      
    <li class="sitepageevent_stats_info">
      <div class="label"><?php echo $this->translate('RSVPs'); ?></div>
      <div class="sitepageevent_stats_content">
        <ul>
          <li>
            <?php echo $this->locale()->toNumber($this->subject()->getAttendingCount()) ?>
            <span><?php echo $this->translate('attending'); ?></span>
          </li>
          <li>
            <?php echo $this->locale()->toNumber($this->subject()->getMaybeCount()) ?>
            <span><?php echo $this->translate('maybe attending'); ?></span>
          </li>
          <li>
            <?php echo $this->locale()->toNumber($this->subject()->getNotAttendingCount()) ?>
            <span><?php echo $this->translate('not attending'); ?></span>
          </li>
          <li>
            <?php echo $this->locale()->toNumber($this->subject()->getAwaitingReplyCount()) ?>
            <span><?php echo $this->translate('awaiting reply'); ?></span>
          </li>
        </ul>
      </div>
    </li>
  </ul>
</div>