<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @package   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
?>

<div class="global_form_popup">
  <h3><?php echo $this->translate($this->package->getTitle()); ?></h3>
<?php if ($this->package->photo_id): ?>
  <p>thumb.mini</p>
  <?php echo $this->itemPhoto($this->package, 'thumb.mini') ?>
  <p>thumb.icon</p>
  <?php echo $this->itemPhoto($this->package, 'thumb.icon') ?>
  <p>thumb.normal</p>
  <?php echo $this->itemPhoto($this->package, 'thumb.normal') ?>
  <p>thumb.profile</p>
  <?php echo $this->itemPhoto($this->package, 'thumb.profile') ?>
  <p>
    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'resume', 'controller' => 'packages', 'action' => 'delete-photo', 'package_id' => $this->package->getIdentity()),
      $this->translate("delete package's photo"), array('target' => '_top')
    )?>
  </p>
<?php else: ?>
  <?php echo $this->translate('This package has no photo.')?>
<?php endif; ?>  
</div>