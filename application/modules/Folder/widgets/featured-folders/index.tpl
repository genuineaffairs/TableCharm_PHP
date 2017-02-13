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

  <?php $this->headScript()->appendFile('application/modules/Folder/externals/scripts/slideshow.js') ?>
  
  <div class="folder_featured_folders">
    <div class="folder_featured_mask">
      <div id="<?php echo $this->widget_name?>" class="folder_featured_slides">
        <?php foreach ($this->paginator as $folder): ?>
          <div class="folder_featured_slide folder_record_<?php echo $folder->getIdentity();?>">
            <?php if ($this->showphoto && $folder->photo_id): ?>
              <div class="folder_photo">
                <?php echo $this->htmlLink($folder->getHref(), $this->itemPhoto($folder, 'thumb.normal'));?>
              </div>   
            <?php endif; ?>         
            <div class="folder_content">
              <div class="folder_title">
                <?php echo $this->partial('index/_title.tpl', 'folder', array('folder' => $folder, 'max_title_length' => 42))?>
              </div>
              <?php if ($this->showdescription && $folder->getDescription()): ?>
                <div class="folder_description">
                  <?php echo $this->radcodes()->text()->truncate($folder->getDescription(), 255); ?>
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
          </div>  
        <?php endforeach; ?>  
      </div>
    </div>
    <?php if ($this->use_slideshow): ?>
      <p class="folder_featured_slideshow_buttons" id="<?php echo $this->widget_name?>_buttons">
        <span id="<?php echo $this->widget_name?>_prev" class="folder_slideshow_button_prev"><span><?php echo $this->translate('&lt;&lt; Previous'); ?></span></span>
        <span id="<?php echo $this->widget_name?>_play" class="folder_slideshow_button_play"><span><?php echo $this->translate('Play &gt;'); ?></span></span>
        <span id="<?php echo $this->widget_name?>_stop" class="folder_slideshow_button_stop"><span><?php echo $this->translate('Stop'); ?></span></span>
        <span id="<?php echo $this->widget_name?>_next" class="folder_slideshow_button_next"><span><?php echo $this->translate('Next &gt;&gt;'); ?></span></span>
      </p>
    <?php endif; ?>
  </div>
  <?php if ($this->use_slideshow): ?>
    <script type="text/javascript">
    en4.core.runonce.add(function(){
      var <?php echo $this->widget_name?>_width = $('<?php echo $this->widget_name?>').getSize().x;
      
      $$('#<?php echo $this->widget_name?> div.folder_featured_slide').each(function(el){
        el.setStyle('width', <?php echo $this->widget_name?>_width - 30);
      });
    	var <?php echo $this->widget_name?> = new radcodesFolderNoobSlide({
    		box: $('<?php echo $this->widget_name?>'),
    		items: $$('#<?php echo $this->widget_name?> div.folder_featured_slide'),
    		size: <?php echo $this->widget_name?>_width,
    		autoPlay: true,
    		interval: 8000,
    		addButtons: {
    			previous: $('<?php echo $this->widget_name?>_prev'),
    			play: $('<?php echo $this->widget_name?>_play'),
    			stop: $('<?php echo $this->widget_name?>_stop'),
    			next: $('<?php echo $this->widget_name?>_next')
    		},
    		onWalk: function(currentItem,currentHandle){
    		}
    	});
    });
    </script>
  <?php endif; ?>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no featured folders.');?>
    </span>
  </div>
<?php endif; ?>