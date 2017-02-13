<?php


/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Folder
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
?>

<div class="global_form_popup">
  <h3><?php echo $this->translate($this->category->getTitle()); ?></h3>
<?php if ($this->category->photo_id): ?>
  <p>thumb.mini</p>
  <?php echo $this->itemPhoto($this->category, 'thumb.mini') ?>
  <p>thumb.icon</p>
  <?php echo $this->itemPhoto($this->category, 'thumb.icon') ?>
  <p>thumb.normal</p>
  <?php echo $this->itemPhoto($this->category, 'thumb.normal') ?>
  <p>thumb.profile</p>
  <?php echo $this->itemPhoto($this->category, 'thumb.profile') ?>
  <p>
    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'folder', 'controller' => 'categories', 'action' => 'delete-photo', 'category_id' => $this->category->getIdentity()),
      $this->translate("delete category's photo"), array('target' => '_top')
    )?>
  </p>
<?php else: ?>
  <?php echo $this->translate('This category has no photo.')?>
<?php endif; ?>  
</div>