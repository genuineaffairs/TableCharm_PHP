<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenotes
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editphoto.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/css.php?request=/application/modules/Sitepagenote/externals/styles/style_sitepagenote.css')
?>
<?php if (!$this->format): ?>
	<?php 
	  include APPLICATION_PATH . '/application/modules/Sitepagenote/views/scripts/pageNoteHeader.tpl';
	?>
  <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnoteeditphoto', 3) && $page_communityad_integration && Engine_Api::_()->sitepage()->showAdWithPackage($this->sitepage)): ?>
    <div class="layout_right" id="communityad_noteeditphoto">
      <?php echo $this->content()->renderWidget("sitepage.page-ads", array('limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnoteeditphoto', 3), 'tab' => 'noteeditphoto', 'communityadid' => 'communityad_noteeditphoto', 'isajax' => 0)); ?>
    </div>
  <?php endif; ?>
  <div class="layout_middle">
    <form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" class="global_form sitepagenotes_browse_filters">
      <div style="float:none;">
        <div>
          <h3><?php echo $this->translate('Edit Note Photos') ?></h3>
          <p><?php echo $this->translate("Edit and manage the photos of your Page's note below.") ?></p>
          <ul class='sitepagenotes_editphotos'>        
            <?php foreach ($this->paginator as $photo): ?>
              <li>    
                <div class="sitepagenotes_editphotos_photo">
                  <?php echo $this->itemPhoto($photo, 'thumb.normal') ?>
                </div>     
                <div class="sitepagenotes_editphotos_info">
                  <?php
                  $key = $photo->getGuid();
                  echo $this->form->getSubForm($key)->render($this);
                  ?>
                  <div class="sitepagenotes_editphotos_cover">
                    <input type="radio" name="cover" value="<?php echo $photo->getIdentity() ?>" <?php if ($this->sitepagenote->photo_id == $photo->file_id): ?> checked="checked"<?php endif; ?> />
                  </div>
                  <div class="sitepagenotes_editphotos_label">
                    <label><?php echo $this->translate('Main Photo'); ?></label>
                  </div>
                </div>
              </li>    
            <?php endforeach; ?>
            <?php if ($this->format == 'smoothbox'): ?>
              <input type="hidden" name='format' value='<?php echo $this->format; ?>'>
            <?php endif; ?>
            <?php echo $this->form->execute->render(); ?>
            <?php echo $this->translate('or'); ?>
            <?php if ($this->format != 'smoothbox') : ?>
              <?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Cancel')) ?>
            <?php else : ?>
              <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </form>
  </div>

<?php else: ?>
  <form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" class="global_form">
    <?php echo $this->translate('Edit Photo') ?>
    <ul class='sitepagenotes_editphotos'>        
      <?php foreach ($this->paginator as $photo): ?>
        <li>     
          <div class="sitepagenotes_editphotos_info">
            <?php
            $key = $photo->getGuid();
            echo $this->form->getSubForm($key)->render($this);
            ?>
            <div class="sitepagenotes_editphotos_cover">
              <input type="radio" name="cover" value="<?php echo $photo->getIdentity() ?>" <?php if ($this->sitepagenote->photo_id == $photo->file_id): ?> checked="checked"<?php endif; ?> />
            </div>
            <div class="sitepagenotes_editphotos_label">
              <label><?php echo $this->translate('Main Photo'); ?></label>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
      <?php if ($this->format == 'smoothbox'): ?>
        <input type="hidden" name='format' value='<?php echo $this->format; ?>'>
      <?php endif; ?>
      <?php echo $this->form->execute->render(); ?>
      <?php echo $this->translate('or'); ?>
      <?php if ($this->format != 'smoothbox') : ?>	
        <a href='<?php echo $this->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($this->sitepage->page_id), 'tab' => $this->tab_selected_id), 'sitepage_entry_view', true) ?>'><?php echo $this->translate('cancel'); ?></a>
      <?php else : ?>
        <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
      <?php endif; ?>
    </ul>
  </form>
<?php endif; ?>