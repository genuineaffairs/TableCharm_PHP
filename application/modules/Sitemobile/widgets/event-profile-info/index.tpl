<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<?php if (!empty($this->event_info_collapsible)) : ?>
  <div class="sm_ui_item_profile_details" data-role="collapsible" <?php if(!empty($this->event_info_collapsible_default)):?> data-collapsed='false' <?php else:?> data-collapsed='true' <?php endif;?> id="collapsibles" data-mini="true">
    <h3><?php echo $this->translate('Event Details'); ?></h3>
  <?php else: ?>
    <div class="sm_ui_item_profile_details">
    <?php endif; ?>
    <table>
      <tbody>
        <?php if (!empty($this->subject->description)): ?>
          <tr valign="top">
            <td class="label"><div><?php echo $this->translate('Details') ?></div></td>
            <td><?php echo nl2br($this->subject->description) ?></td>
          </tr>
        <?php endif ?>

        <?php
        // Convert the dates for the viewer
        $startDateObject = new Zend_Date(strtotime($this->subject->starttime));
        $endDateObject = new Zend_Date(strtotime($this->subject->endtime));
        if ($this->viewer() && $this->viewer()->getIdentity()) {
          $tz = $this->viewer()->timezone;
          $startDateObject->setTimezone($tz);
          $endDateObject->setTimezone($tz);
        }
        ?>
        <?php if ($this->subject->starttime == $this->subject->endtime): ?>
          <tr valign="top">
            <td class="label"><div><?php echo $this->translate('Date') ?></div></td>
            <td>
              <?php echo $this->locale()->toDate($startDateObject) ?>
            </td>
          </tr> 

          <tr valign="top">
            <td class="label"><div><?php echo $this->translate('Time') ?></div></td>
            <td>
              <?php echo $this->locale()->toTime($startDateObject) ?>
            </td>
          </tr> 
        <?php elseif ($startDateObject->toString('y-MM-dd') == $endDateObject->toString('y-MM-dd')): ?>
          <tr valign="top">
            <td class="label"><div><?php echo $this->translate('Date') ?></div></td>
            <td>
              <?php echo $this->locale()->toDate($startDateObject) ?>
            </td>
          </tr> 
          <tr valign="top">
            <td class="label"><div><?php echo $this->translate('Time') ?></div></td>
            <td>
              <?php echo $this->locale()->toTime($startDateObject) ?>
              -
              <?php echo $this->locale()->toTime($endDateObject) ?>
            </td>
          </tr>
        <?php else: ?>  
          <tr valign="top">
            <td class="label"><div><?php echo $this->translate('When') ?></div></td>
            <td>
              <div class="event_stats_content">
                <?php
                echo $this->translate('%1$s at %2$s', $this->locale()->toDate($startDateObject), $this->locale()->toTime($startDateObject)
                )
                ?>
                - 
                <?php
                echo $this->translate('%1$s at %2$s', $this->locale()->toDate($endDateObject), $this->locale()->toTime($endDateObject)
                )
                ?>
              </div>
            </td>
          </tr>
        <?php endif ?>

        <?php if (!empty($this->subject->location)): ?>
          <tr valign="top">
            <td class="label"><div><?php echo $this->translate('Where') ?></div></td>
            <td><?php echo $this->subject->location; ?> <?php echo $this->htmlLink('http://maps.google.com/?q=' . urlencode($this->subject->location), $this->translate('Map'), array('target' => 'blank')) ?></td>
          </tr>
        <?php endif ?>

        <?php if (!empty($this->subject->host)): ?>
          <?php if ($this->subject->host != $this->subject->getParent()->getTitle()): ?>
            <tr valign="top">
              <td class="label"><div><?php echo $this->translate('Host') ?></div></td>
              <td><?php echo $this->subject->host ?></td>
            </tr>
          <?php endif ?>
          <tr valign="top">
            <td class="label"><div><?php echo $this->translate('Led by') ?></div></td>
            <td><?php echo $this->subject->getParent()->__toString() ?></td>
          </tr>
        <?php endif ?>

        <?php if (!empty($this->subject->category_id)): ?>
          <tr valign="top">
            <td class="label"><div><?php echo $this->translate('Category') ?></div></td>
            <td>
              <?php
              echo $this->htmlLink(array(
                  'route' => 'event_general',
                  'action' => 'browse',
                  'category_id' => $this->subject->category_id,
                      ), $this->translate((string) $this->subject->categoryName()))
              ?>
            </td>
          </tr>
<?php endif ?>

        <tr valign="top">
          <td class="label"><div><?php echo $this->translate('RSVPs'); ?></div></td>
          <td>
            <ul>
              <li>
                <strong><?php echo $this->locale()->toNumber($this->subject->getAttendingCount()) ?></strong>
                <span><?php echo $this->translate('attending'); ?></span>
              </li>
              <li>
                <strong><?php echo $this->locale()->toNumber($this->subject->getMaybeCount()) ?></strong>
                <span><?php echo $this->translate('maybe attending'); ?></span>
              </li>
              <li>
                <strong><?php echo $this->locale()->toNumber($this->subject->getNotAttendingCount()) ?></strong>
                <span><?php echo $this->translate('not attending'); ?></span>
              </li>
              <li>
                <strong><?php echo $this->locale()->toNumber($this->subject->getAwaitingReplyCount()) ?></strong>
                <span><?php echo $this->translate('awaiting reply'); ?></span>
              </li>
            </ul>
          </td>
        </tr>
      </tbody>
    </table>
  </div>