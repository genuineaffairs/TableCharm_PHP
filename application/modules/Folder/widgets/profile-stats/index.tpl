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
<?php $folder = $this->folder; $total_files = count($folder); ?>
<div class='folder_profile_stats'>
  <ul>
    <li><?php echo $this->translate('Created By: %s', $this->folder->getOwner()->toString())?></li>
    <li><?php echo $this->translate('Posted: %s', $this->timestamp($this->folder->creation_date))?></li>
    <?php if ($this->folder->creation_date != $this->folder->modified_date): ?>
      <li><?php echo $this->translate('Updated: %s', $this->timestamp($this->folder->modified_date))?></li>
    <?php endif; ?>
    <li><?php echo $this->translate(array('%d file','%d files', $total_files), $this->locale()->toNumber($total_files));?></li>
    <!--<li><?php echo $this->translate(array("%s comment", "%s comments", $this->folder->comment_count), $this->locale()->toNumber($this->folder->comment_count)); ?></li>-->
    <!--<li><?php echo $this->translate(array('%1$s like', '%1$s likes', $this->folder->like_count), $this->locale()->toNumber($this->folder->like_count)); ?></li>-->
    <li><?php echo $this->translate(array('%s view', '%s views', $this->folder->view_count), $this->locale()->toNumber($this->folder->view_count)); ?></li>
  </ul>
</div>