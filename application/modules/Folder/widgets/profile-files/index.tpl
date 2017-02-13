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


<?php if ($this->is_locked): ?>
  <?php echo $this->form->render($this);?>
  <?php return; ?>
<?php endif; ?>

<?php $folder = $this->folder; ?>
  
  <div class="folder_attachments_back">
    <?php echo $this->htmlLink($folder->getParentFoldersHref(), $this->translate('Back to Folders')); ?>
  </div>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    
    <ul class="folder_attachments">
    <?php foreach ($this->paginator as $attachment): ?>
      <li>
      <?php /*
        <div class="folder_attachments_options">
          <?php echo $this->htmlLink($attachment->getActionHref('download'), $this->translate('Download'), array('class'=>'folder_attachments_download')); ?>
        </div>
        */
      ?>
        <div class="folder_attachments_info">
          <div class="folder_attachments_title">
            <?php echo $attachment->toString(); ?>
          </div>
          <?php if ($attachment->getDescription()): ?>
            <div class="folder_attachments_description"><?php echo $this->viewMore($attachment->getDescription()); ?></div>
          <?php endif;?>
          <div class="folder_attachment_meta">
            <ul>
              <li><?php echo $this->locale()->toDate($attachment->creation_date)?></li>
              <li><?php echo $this->folderFileSize($attachment->getFile()->size);?></li>
              <li><?php echo $this->translate(array('%s view', '%s views', $attachment->view_count), $this->locale()->toNumber($attachment->view_count)); ?></li>
              <li><?php echo $this->translate(array('%d download', '%d downloads', $attachment->download_count), $this->locale()->toNumber($attachment->download_count)); ?></li>
              <?php if ($attachment->creation_date != $attachment->modified_date): ?>
                <li><?php echo $this->translate('modified %s', $this->locale()->toDate($attachment->modified_date))?></li>
              <?php endif; ?>  
            </ul>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <br />
    <div class="tip">
      <span>
        <?php echo $this->translate('This folder does not have any files.');?>
        <?php if ($this->can_upload): ?>
          <?php echo $this->htmlLink($folder->getActionHref('upload'), $this->translate('Upload files now!'))?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>
  
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