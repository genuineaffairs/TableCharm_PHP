<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Mgsl
 * @copyright  Copyright 2001-2013 Technobd Web Solution (Pvt.) Limited.
 * @license    http://www.socialengine-expert.com/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z aditya $
 * @author     Aditya
 */
?>

<div class="profile-picture-container"><?php echo $this->itemPhoto($this->subject(), 'thumb.profile') ?></div>
<h2><?php echo $this->subject()->getTitle() ?></h2>
<?php if (!Engine_Api::_()->user()->isSiteAdmin($this->subject())) : ?>
  <div class="summary-info">
      <?php if ($this->primary_sport != null): ?>
          <div class="resides-in">
              <p><?php echo $this->translate('Primary Sport'); ?> <strong><?php echo $this->translate($this->primary_sport); ?></strong></p>
          </div>
      <?php endif ?>
      <?php if ($this->participation_level != null): ?>
          <div class="resides-in">
              <p><?php echo $this->translate('Participation Level'); ?> <strong><?php echo $this->participation_level; ?></strong></p>
          </div>
      <?php endif ?>
      <?php if ($this->residence!= null): ?>
          <div class="resides-in">
              <p><?php echo $this->translate('Currently resides in'); ?> <strong><?php echo $this->residence; ?></strong></p>
          </div>
      <?php endif ?>
  </div>
<?php endif; ?>