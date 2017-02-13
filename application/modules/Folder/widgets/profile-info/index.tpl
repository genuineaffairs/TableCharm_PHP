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
$folder = $this->folder;
$category = $folder->getCategory();
?>

<div class='folder_profile_info'>
  <ul>   
    <!--<li><?php echo $this->translate('Type: %s', $this->htmlLink($folder->getParentTypeHref(), $folder->getParentTypeText()))?></li>-->
    <!--<li><?php echo $this->translate('Item: %s', $folder->getParent()->toString())?></li>-->
    <li><?php echo $this->translate('Category: %s', $this->htmlLink($category->getHref(), $this->translate($category->getTitle())))?></li>
    <?php if (count($tagMaps = $folder->tags()->getTagMaps()) > 0): ?>
      <?php $tagFolders = array(); ?>
      <?php foreach ($tagMaps as $tagMap): $tag = $tagMap->getTag()?>
        <?php if (!empty($tag->text)):?>
          <?php $tagFolders[] = '#'.$this->htmlLink(array('route'=>'folder_general', 'action'=>'browse', 'tag'=>$tag->tag_id), $tag->text); ?>
        <?php endif; ?>
      <?php endforeach; ?>
      <li class="folder_profile_info_tags">
        <h6><?php echo $this->translate('Tags:')?></h6>
        <div><?php echo join(" ", $tagFolders); ?></div>
      </li>
    <?php endif; ?>
    
    <?php if ($folder->getDescription()): ?>
      <li class="folder_profile_info_description">
        <h6><?php echo $this->translate('Descriptions:')?></h6>
        <div><?php echo $this->viewMore($folder->getDescription()); ?></div>
      </li>
    <?php endif; ?>
  </ul>
</div>