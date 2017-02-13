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
  <div class="folder_edit_gutter"> 
    <?php echo $this->partial('/folder/_info.tpl', 'folder', array('folder' => $this->folder, 'dashboardNavigation' => $this->dashboardNavigation));?>
  </div>  
</div>
<div class='layout_middle'>
  
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <table class="folder_manage_attachments">
      <thead>
        <tr>
          <th><?php echo $this->translate('%s / Files', $this->folder->toString());?></th>
          <?php /*
          <th><?php echo $this->translate('Views')?></th>
          <th><?php echo $this->translate('Downloads');?></th>
          */ ?>
          <th style="width: 100px"><?php echo $this->translate('Options')?></th>
        </tr>
      </thead>
      <tbody>
    <?php foreach ($this->paginator as $attachment): ?>
      <tr>
        <th>
          <div class="folder_attachment_title">
            <?php echo $attachment->toString(); ?>
          </div>
          <?php if ($attachment->getTitle() != $attachment->getFile()->name): ?>
            <div class="folder_attachment_filename"><?php echo $attachment->getFile()->name; ?></div>
          <?php endif; ?>
          <?php if ($attachment->getDescription()): ?>
            <div class="folder_attachment_description"><?php echo $this->viewMore($attachment->getDescription()); ?></div>
          <?php endif;?>
          <div class="folder_attachment_meta">
            <ul>
              <li><?php echo $this->translate(array('%s view', '%s views', $attachment->view_count), $this->locale()->toNumber($attachment->view_count)); ?></li>
              <li><?php echo $this->translate(array('%d download', '%d downloads', $attachment->download_count), $this->locale()->toNumber($attachment->download_count)); ?></li>
              <li><?php echo $this->translate('created %s', $this->locale()->toDate($attachment->creation_date))?></li>
              <?php if ($attachment->creation_date != $attachment->modified_date): ?>
                <li><?php echo $this->translate('modified %s', $this->locale()->toDate($attachment->modified_date))?></li>
              <?php endif; ?>  
            </ul>
          </div>  
        </th>
        <td>
          <?php echo $this->htmlLink($attachment->getActionHref('edit'), $this->translate('edit'), array('class'=>'smoothbox')); ?>
          |
          <?php echo $this->htmlLink($attachment->getActionHref('delete'), $this->translate('delete'), array('class'=>'smoothbox')); ?>
        </td>
      </tr>
    <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('This folder does not have any files.');?>
          <?php echo $this->translate('Get started by <a href=\'%1$s\'>uploading</a> new files.', $this->folder->getActionHref('upload'));?>
      </span>
    </div>
  <?php endif; ?>
  
  <div class="wrapper_padding">
    <?php
    $this->headLink()->appendStylesheet(
            $this->layout()->staticBaseUrl . 'application/modules/Resume/externals/styles/resume.css');
    if ($this->folder->getParent()->getType() == 'resume') {
      $tab_selected_id = Engine_Api::_()->resume()->getDocumentTabId();
    } else {
      $tab_selected_id = null;
    }
    ?>
    <a class="resume_button_link" href="<?php echo $this->folder->getParent()->getHref(array('tab'=>$tab_selected_id))?>"><?php echo $this->translate("Go back to " . $this->folder->getParentTypeText()) ?></a>
  </div>
</div>

<?php /*
<script language="Javascript">

en4.core.runonce.add(function(){
  
  var ListFolderAttachmentTip = new Tips($$('.FolderAttachmentTip'), {
    initialize:function(){
      this.fx = new Fx.Style(this.toolTip, 'opacity', {duration: 500, wait: false}).set(0);
    },
    text : 'rel',
    className: 'folder_bubble',
    showDelay: 400,
    hideDelay: 400
  });
  
});
</script>  
*/
?>
