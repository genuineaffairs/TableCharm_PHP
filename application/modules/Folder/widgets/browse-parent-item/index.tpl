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

<div class="folder_browse_parent_item">
  <div class="folder_browse_parent_item_photo">
    <?php echo $this->htmlLink($this->parentObject->getHref(), $this->itemPhoto($this->parentObject, 'thumb.icon')); ?>
  </div>
  <div class="folder_browse_parent_item_content">
    <?php echo $this->htmlLink($this->parentObject->getHref(), $this->radcodes()->text()->truncate($this->parentObject->getTitle(), 32)); ?>
    <span><?php echo $this->translate(strtoupper('ITEM_TYPE_' . $this->parentObject->getType()))?></span>
  </div>
</div>
