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

  <?php $this->headScript()->appendFile('application/modules/Folder/externals/scripts/ticker.js') ?>
  
  <div class="folder_sponsored_folders">
    <ul id="<?php echo $this->widget_name?>" class="folder_sponsored_slides">
      <?php foreach ($this->paginator as $folder): ?>
        <li>
          <?php if ($this->showphoto): ?>
            <div class="folder_photo">
              <?php echo $this->htmlLink($folder->getHref(), $this->itemPhoto($folder, 'thumb.normal'));?>
            </div>   
          <?php endif; ?>         
          <div class="folder_content">
            <div class="folder_title">
              <?php echo $this->partial('index/_title.tpl', 'folder', array('folder' => $folder, 'max_title_length' => 26))?>
            </div>
            
            <?php if ($this->showdescription && $folder->getDescription()): ?>
              <div class="folder_description">
                <?php echo $this->radcodes()->text()->truncate($folder->getDescription(), 128); ?>
              </div>
            <?php endif; ?>  
            <?php if ($this->showdetails): ?>
              <div class="folder_details">
                <?php echo $this->partial('index/_meta.tpl', 'folder', array('folder' => $folder, 'show_parent'=>true))?>
              </div>
            <?php endif; ?> 
            <?php if ($this->showmeta): ?>
              <div class="folder_meta">
                <?php echo $this->partial('index/_meta.tpl', 'folder', array('folder' => $folder, 'show_files' => true, 'show_date'=>true))?>
              </div>
            <?php endif; ?>
          </div>
        </li>  
      <?php endforeach; ?>  
    </ul>
    
  </div>

  <?php if ($this->use_slideshow): ?>
    <script type="text/javascript">
    en4.core.runonce.add(function(){
    	<?php echo $this->widget_name?>Ticker = new radcodesFolderNewsTicker('<?php echo $this->widget_name?>', {speed:1000,delay:15000,direction:'vertical'});
    });
    </script>
    <div class="folder_sponsored_folders_action">
      <a href="javascript: void(0);" onclick="<?php echo $this->widget_name?>Ticker.next(); return false;"><?php echo $this->translate("Next &raquo;")?></a>
    </div>    
  <?php endif; ?>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no sponsored folders.');?>
    </span>
  </div>
<?php endif; ?>