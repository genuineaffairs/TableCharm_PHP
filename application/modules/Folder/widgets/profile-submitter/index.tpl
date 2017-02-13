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

<div class='folder_profile_submitter'>
  <?php echo $this->htmlLink($this->owner->getHref(),
    $this->itemPhoto($this->owner, 'thumb.icon'),
    array('class' => 'folder_profile_submitter_photo')
  )?>
  <?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle(), array('class' => 'folder_profile_submitter_user')) ?>
  <span><?php echo $this->htmlLink(array('route'=>'folder_general', 'action'=>'browse', 'user'=>$this->owner->getIdentity()),
      $this->translate(array('%d folder','%d folders', $this->totalFolders), $this->totalFolders)
    )?></span>
</div>