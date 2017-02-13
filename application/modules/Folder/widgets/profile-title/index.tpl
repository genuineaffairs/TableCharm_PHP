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
<div class="folder_profile_title folder_profile_title_featured_<?php echo $folder->featured ? 'yes' : 'no'?> folder_profile_title_sponsored_<?php echo $folder->sponsored ? 'yes' : 'no'?>">
  <div class="folder_profile_title_photo"><?php echo $this->htmlLink($folder->getParent()->getHref(), $this->itemPhoto($folder->getParent(), 'thumb.icon')) ?></div>
  <h3><?php echo $folder->getTitle(); ?></h3>
  <div class="folder_profile_title_navigator">
    <span class="folder_profile_title_navigator_type"><?php echo $this->htmlLink(array('route'=>'folder_general', 'action'=>'browse', 'parent_type'=>$folder->parent_type), $folder->getParentTypeText());?></span>
    ::
    <span class="folder_profile_title_navigator_parent"><?php echo $this->translate("%s's Shared Files", $folder->getParent()->toString() )?></span>
    <span class="folder_profile_title_navigator_total">(<?php $total_link = $this->translate(array('%d folder','%d folders', $this->total_folders), $this->total_folders);
      echo $this->htmlLink(array('route'=>'folder_general', 'action'=>'browse', 'parent' => $folder->getParent()->getGuid()), $total_link)
    ?>)</span>
  </div>
</div>