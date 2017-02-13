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
<?php if ($this->paginator->getTotalItemCount()): ?>

  <div class="folder_profile_folders">

    <ul>
    <?php foreach ($this->paginator as $folder): ?>
      <li>
        <?php if ($this->showphoto): ?>
          <div class="folder_photo">
            <?php echo $this->htmlLink($folder->getHref(), $this->itemPhoto($folder, 'thumb.icon'));?>
          </div>
        <?php endif; ?>
        <div class="folder_content">
          <div class="folder_title">
            <?php echo $folder->toString()?>
          </div>
          <?php if ($this->showmeta): ?>
            <div class="folder_meta">
              <?php echo $this->partial('index/_meta.tpl', 'folder', array('folder' => $folder, 'show_date'=>true, 'show_files'=>true, 'show_comments'=>true, 'show_likes'=>true, 'show_views'=>true))?>
            </div>
          <?php endif; ?>         
          <?php if ($this->showdescription && $folder->getDescription()): ?>
            <div class="folder_description">
              <?php echo $this->partial('index/_description.tpl', 'folder', array('folder' => $folder))?>
            </div>
          <?php endif; ?>
        </div>
      </li>
    <?php endforeach; ?>
    </ul>
  </div>

<?php endif; ?>