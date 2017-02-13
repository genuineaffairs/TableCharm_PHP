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

<div class="tip">
  <span>
    <?php if ($this->error == 'draft'): ?>
      <?php echo $this->translate('This resume is not live yet.');?>
    <?php elseif ($this->error == 'not_approved'): ?>
      <?php echo $this->translate('This resume has not been approved by administrator yet.');?>
    <?php elseif ($this->error = 'expired'): ?>
      <?php echo $this->translate('This resume has been expired.');?>
    <?php else: ?>
      <?php echo $this->translate('Sorry, you cannot view this resume at this time.');?>
    <?php endif; ?>  
  </span>
</div>
