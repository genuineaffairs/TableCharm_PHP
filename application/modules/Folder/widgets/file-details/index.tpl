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

<?php 
//$folder = $this->attachment->getFolder(); 
//$category = $folder->getCategory();
?>

<div class='folder_profile_info'>
  <ul>   
    <li><?php echo $this->translate('Folder: %s', $this->attachment->getFolder()->toString())?></li>
    <li><?php echo $this->translate('Uploaded: %s', $this->timestamp($this->attachment->creation_date))?></li>
    <?php if ($this->folder->creation_date != $this->folder->modified_date): ?>
      <li><?php echo $this->translate('Modified: %s', $this->timestamp($this->folder->modified_date))?></li>
    <?php endif; ?>
    <li><?php echo $this->translate('Size: %s', $this->folderFileSize($this->attachment->getFile()->size));?></li>
    <li><?php echo $this->translate(array('%d download','%d downloads', $this->attachment->download_count), $this->locale()->toNumber($this->attachment->download_count));?></li>
    <li><?php echo $this->translate(array('%s view', '%s views', $this->attachment->view_count), $this->locale()->toNumber($this->attachment->view_count)); ?></li>
    <?php if ($this->attachment->getDescription()): ?>
      <li class="folder_profile_info_description">
        <h6><?php echo $this->translate('Descriptions:')?></h6>
        <div><?php echo $this->viewMore($this->attachment->getDescription()); ?></div>
      </li>
    <?php endif; ?>  
  </ul>
</div>
