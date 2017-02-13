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

<?php echo $this->partial('index/_js_fields_search.tpl', 'folder', array())?>

<div class="headline">
  <h2>
    <?php echo $this->translate('Folders');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<div class='layout_right'>
  <?php echo $this->form->render($this) ?>
  <?php if( count($this->quickNavigation) > 0 ): ?>
    <div class="quicklinks">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->quickNavigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>

<div class='layout_middle folder_layout_middle'>

  <?php if( $this->tag || $this->keyword || $this->parent_type):?>
    <div class="folders_result_filter_details">
      <?php echo $this->translate('Showing folders posted'); ?>
      <?php if ($this->parent_type): ?>
        <?php $type_link = $this->htmlLink(array('route'=>'folder_general','action'=>'manage','parent_type'=>$this->parent_type), $this->translate('ITEM_TYPE_'.strtoupper($this->parent_type)));
          echo $this->translate('with %s type', $type_link);
        ?>
      <?php endif;?>
      <?php if ($this->tag): ?>
        <?php echo $this->translate('tagging #%s', $this->htmlLink(
          $this->url(array('action'=>'manage', 'tag'=>$this->tag), 'folder_general', true),
          $this->tagObject ? $this->tagObject->text : $this->tag
        ));?>
      <?php endif; ?>
      <?php if ($this->keyword): ?>
        <?php echo $this->translate('with %s keyword', $this->htmlLink(
          $this->url(array('action'=>'manage', 'keyword'=>$this->keyword), 'folder_general', true),
          $this->keyword
        ));?>
      <?php endif; ?>        
      <?php echo $this->htmlLink(array('action'=>'manage', 'route'=>'folder_general'), $this->translate('(x)'))?>
    </div>
  <?php endif; ?>
  <h3 class="sep">
    <span>
      <?php if ($this->categoryObject instanceof Folder_Model_Category): ?>
        <?php echo $this->translate('My %s Folders', $this->translate($this->categoryObject->getTitle())); ?>
      <?php else: ?>  
        <?php echo $this->translate('My Folders'); ?>
      <?php endif; ?>
    </span>
  </h3>  
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  
      <ul class='folders_rows'>
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
            <div class="folder_photo">
              <?php echo $this->htmlLink($folder->getHref(), $this->itemPhoto($folder, 'thumb.normal'));?>
            </div>
            <div class="folder_options">
              <?php if ($folder->authorization()->isAllowed($this->viewer(), 'edit')): ?>
                <?php echo $this->htmlLink($folder->getActionHref('edit'), $this->translate('Edit Folder'), array('class'=>'buttonlink icon_folder_edit'))?>
              <?php endif; ?>
              <?php if ($folder->authorization()->isAllowed($this->viewer(), 'edit')): ?>
                <?php echo $this->htmlLink($folder->getActionHref('manage'), $this->translate('Manage Files'), array('class'=>'buttonlink icon_folder_manage'))?>
              <?php endif; ?>
              <?php if ($folder->authorization()->isAllowed($this->viewer(), 'edit')): ?>
                <?php echo $this->htmlLink($folder->getActionHref('upload'), $this->translate('Upload Files'), array('class'=>'buttonlink icon_folder_upload'))?>
              <?php endif; ?>
              <?php if ($folder->authorization()->isAllowed($this->viewer(), 'delete')): ?>
                <?php echo $this->htmlLink($folder->getActionHref('delete'), $this->translate('Delete Folder'), array('class'=>'buttonlink icon_folder_delete'))?>
              <?php endif; ?>
            </div>
            <div class="folder_content">
              <div class="folder_title">
                <?php echo $this->partial('index/_title.tpl', 'folder', array('folder' => $folder))?>
              </div>
              <div class="folder_description">
                <?php echo $this->partial('index/_description.tpl', 'folder', array('folder' => $folder))?>
              </div>
              <div class="folder_details">
                <?php echo $this->partial('index/_meta.tpl', 'folder', array('folder' => $folder, 'show_parent' => true))?>
              </div>
              <div class="folder_meta">
                <?php echo $this->partial('index/_meta.tpl', 'folder', array('folder' => $folder, 'show_date'=>true, 'show_files'=>true, 'show_comments'=>true, 'show_likes'=>true, 'show_views'=>true))?>
              </div>
            </div>
          </li>
        <?php endforeach; ?>  
      </ul>

  <?php elseif( $this->tag || $this->keyword || $this->parent_type || $this->categoryObject): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any folders that match your search criteria.');?>
      </span>
    </div>
  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any folders.');?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('Get started by <a href=\'%1$s\'>posting</a> a new folder.', $this->url(array('action'=>'create'), 'folder_general'));?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>

    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues
    )); ?>    

</div>
