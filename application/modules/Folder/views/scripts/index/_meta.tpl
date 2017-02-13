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
<?php $folder = $this->folder; ?>
<ul>
  <?php if ($this->show_parent): ?>
    <li class="folder_meta_type_parent folder_item_icon_<?php echo $folder->parent_type; ?>">
      <?php 
        $type_link = $this->htmlLink($folder->getParentTypeHref(), $folder->getParentTypeText());
        $parent_folder_link = $this->htmlLink($folder->getParentFoldersHref(), $this->translate('Folders'));
        
        //echo $this->translate('%1$s / %2$s&#39;s %3$s', $type_link, $folder->getParent()->toString(), $parent_folder_link);
        // echo $this->translate('Type: %s', $type_link);
        echo $this->translate('In %1$s / <strong>%2$s</strong>&#39;s %3$s', $folder->getParentTypeText(), $folder->getParent()->toString(), $parent_folder_link);
      ?>
    </li>
  <?php endif; ?>
  <?php if ($this->show_owner): ?>
    <li class="folder_meta_owner"><?php echo $this->translate('by %s', $folder->getOwner()->toString()); ?></li>
  <?php endif; ?>
  <?php if ($this->show_date): ?>
    <li class="folder_meta_date"><?php echo $this->timestamp($folder->creation_date); ?></li>
  <?php endif; ?>
  <?php if ($this->show_files): $total_files = count($this->folder); ?>
    <li class="folder_meta_files"><?php echo $this->translate(array('%d file', '%d files', $total_files), $this->locale()->toNumber($total_files)); ?></li>
  <?php endif; ?>
  <?php if ($this->show_comments): ?>
    <!--<li class="folder_meta_comments"><?php echo $this->translate(array("%s comment", "%s comments", $folder->comment_count), $this->locale()->toNumber($folder->comment_count)); ?></li>-->
  <?php endif; ?>
  <?php if ($this->show_likes): ?>
    <!--<li class="folder_meta_likes"><?php echo $this->translate(array('%1$s like', '%1$s likes', $folder->like_count), $this->locale()->toNumber($folder->like_count)); ?></li>-->
  <?php endif; ?>
  <?php if ($this->show_views): ?>
    <li class="folder_meta_views"><?php echo $this->translate(array('%s view', '%s views', $folder->view_count), $this->locale()->toNumber($folder->view_count)); ?></li>
  <?php endif; ?>
</ul>