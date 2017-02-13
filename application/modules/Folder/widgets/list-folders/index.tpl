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
  
  <?php if ($this->display_style == 'narrow'): ?>
    <ul class='folders_list'>
      <?php foreach ($this->paginator as $folder): ?>
        <?php 
          try {
            $parent = $folder->getParent();
          }
          catch (Core_Model_Item_Exception $ex)
          {
            continue;
          }
        ?>      
        <li>
          <?php if ($this->showphoto): ?>
            <div class="folder_photo">
              <?php echo $this->htmlLink($folder->getHref(), $this->itemPhoto($folder, 'thumb.icon'));?>
            </div>
          <?php endif; ?>
          <div class="folder_content">
            <div class="folder_title">
              <?php echo $this->htmlLink($folder->getHref(), $this->radcodes()->text()->truncate($folder->getTitle(), 24))?>
            </div>
            <?php if ($this->showdescription && $folder->getDescription()): ?>
              <div class="folder_description">
                <?php echo $this->partial('index/_description.tpl', 'folder', array('folder' => $folder))?>
              </div>
            <?php endif; ?>
            <?php if ($this->showdetails): ?>
              <div class="folder_details">
                <?php echo $this->partial('index/_meta.tpl', 'folder', array('folder' => $folder, 'show_parent'=>true))?>
              </div>
            <?php endif; ?>             
            <?php if ($this->showmeta): ?>
              <div class="folder_meta">
                <?php 
                  $meta_options = array('folder' => $folder,
                    'show_files' => true,
                  );
                  if ($this->order == 'mostcommented') {
                    $meta_options['show_comments'] = true;
                  }
                  else if ($this->order == 'mostviewed') {
                    $meta_options['show_viewed'] = true;
                  }
                  else if ($this->order == 'mostliked') {
                    $meta_options['show_liked'] = true;
                  }
                  else {
                    $meta_options['show_date'] = true;
                  }
                ?>
                <?php echo $this->partial('index/_meta.tpl', 'folder', $meta_options)?>
              </div>
            <?php endif; ?>
          </div>
        </li>
      <?php endforeach; ?>  
    </ul>

  <?php else: ?>
    <ul class="folders_rows">
	    <?php foreach( $this->paginator as $folder ): $user = $folder->getOwner(); ?>
          <?php 
            try {
              $parent = $folder->getParent();
            }
            catch (Core_Model_Item_Exception $ex)
            {
              continue;
            }
          ?>      
	      <li>
          <?php if ($this->showphoto): ?>
            <div class="folder_photo">
              <?php echo $this->htmlLink($folder->getHref(), $this->itemPhoto($folder, 'thumb.normal'));?>
            </div>
          <?php endif; ?>
          <div class="folder_content">
            <div class="folder_title">
              <?php echo $this->partial('index/_title.tpl', 'folder', array('folder' => $folder))?>
            </div>
            <?php if ($this->showdescription && $folder->getDescription()): ?>
              <div class="folder_description">
                <?php echo $this->partial('index/_description.tpl', 'folder', array('folder' => $folder))?>
              </div>
            <?php endif; ?>
            <?php if ($this->showdetails): ?>
              <div class="folder_details">
                <?php echo $this->partial('index/_meta.tpl', 'folder', array('folder' => $folder, 'show_parent'=>true))?>
              </div>
            <?php endif; ?>    
            <?php if ($this->showmeta): ?>
              <div class="folder_meta">
                <?php echo $this->partial('index/_meta.tpl', 'folder', array('folder' => $folder, 'show_date'=>true, 'show_files'=>true, 'show_comments'=>true, 'show_likes'=>true, 'show_views'=>true))?>
              </div>
            <?php endif; ?>
          </div>
	      </li>
	    <?php endforeach; ?>
    </ul>
  <?php endif;?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no folders posted yet.');?>
    </span>
  </div>  
<?php endif; ?>