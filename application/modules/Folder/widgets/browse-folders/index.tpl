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

<div class='inner_wrapper'>

<?php if ($this->paginator->getTotalItemCount()): ?> 

  <?php if( $this->tag || $this->keyword || $this->user || $this->parent || $this->parent_type):?>
    <div class="folders_result_filter_details">
      <?php echo $this->translate('Showing folders'); ?>
      
      <?php if ($this->parent_type): ?>
        <?php $type_link = $this->htmlLink(array('route'=>'folder_general','action'=>'browse','parent_type'=>$this->parent_type), $this->translate('ITEM_TYPE_'.strtoupper($this->parent_type)));
          echo $this->translate('with %s type', $type_link);
        ?>
      <?php endif;?>
      <?php if ($this->parent):?>
        <?php if ($this->parentObject): ?>
          <?php echo $this->translate('for %s', $this->parentObject->toString()); ?>
        <?php else: ?>
          <?php echo $this->translate('for %s', $this->parent); ?>
        <?php endif;?>
      <?php endif;?>
      <?php if ($this->user): ?>
        <?php if ($this->userObject): ?>
          <?php echo $this->translate('by %s', $this->userObject->toString()); ?>
        <?php else: ?>
          <?php echo $this->translate('by user #%s', $this->user); ?>
        <?php endif; ?>
      <?php endif;?>
      <?php if ($this->tag): ?>
        <?php echo $this->translate('tagging #%s', $this->htmlLink(
          $this->url(array('action'=>'browse', 'tag'=>$this->tag), 'folder_general', true),
          $this->tagObject ? $this->tagObject->text : $this->tag
        ));?>
      <?php endif; ?>
      <?php if ($this->keyword): ?>
        <?php echo $this->translate('with %s keyword', $this->htmlLink(
          $this->url(array('action'=>'browse', 'keyword'=>$this->keyword), 'folder_general', true),
          $this->keyword
        ));?>
      <?php endif; ?>         
      <?php echo $this->htmlLink(array('action'=>'browse', 'route'=>'folder_general'), $this->translate('(x)'))?>
    </div>
  <?php endif; ?>
  
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
        <li class="folders_rows_featured_<?php echo $folder->featured ? 'yes' : 'no'; ?> folders_rows_sponsored_<?php echo $folder->sponsored ? 'yes' : 'no'; ?>">
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
    
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues
    )); ?>  
       
<?php elseif ( $this->tag || $this->keyword || $this->user || $this->parent || $this->parent_type || $this->category): ?>       
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has posted any folders with that criteria.');?>
    </span>
  </div>
<?php else: ?>    
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has posted any folders yet.');?>
    </span>
  </div>
<?php endif; ?>

</div>

<div class="wrapper_padding">
  <?php
  $this->headLink()->appendStylesheet(
          $this->layout()->staticBaseUrl . 'application/modules/Resume/externals/styles/resume.css');
  if ($folder->getParent()->getType() == 'resume') {
    $tab_selected_id = Engine_Api::_()->resume()->getDocumentTabId();
  } else {
    $tab_selected_id = null;
  }
  ?>
  <a class="resume_button_link" href="<?php echo $folder->getParent()->getHref(array('tab'=>$tab_selected_id))?>"><?php echo $this->translate("Go back to " . $folder->getParentTypeText()) ?></a>
</div>