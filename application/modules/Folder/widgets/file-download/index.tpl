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

<div class="folder_file_download">
  <h2><?php echo $this->attachment->getTitle(); ?></h2>
  <?php if ($this->attachment->getTitle() != $this->attachment->getFile()->name): ?>
    <div class="folder_file_download_filename"><?php echo $this->attachment->getFile()->name; ?></div>
  <?php endif; ?>
  <div class="folder_file_download_size">(<?php echo $this->folderFileSize($this->attachment->getFile()->size) ?>)</div>

  <?php if ($this->is_locked): ?>
  
    <?php echo $this->form->render($this);?>
  
  <?php else: ?>

    <div class="folder_file_download_button">
      <?php echo $this->htmlLink($this->attachment->getActionHref('download'), $this->translate('Download This File')); ?>
    </div>
 
  <?php endif; ?>
</div>