<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevents
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editphoto.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (!$this->format): ?>


  <div class="layout_middle">
    <form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" class="global_form sitepageevents_browse_filters">
      <div style="float:none;">
        <div>
          <h3><?php echo $this->translate('Edit Event Photos') ?></h3>
          <p><?php echo $this->translate("Edit and manage the photos of your Page's event below.") ?></p>
          <ul class='sitepageevents_editphotos'>        
            <?php foreach ($this->paginator as $photo): ?>
              <li>    
                <div class="sitepageevents_editphotos_photo">
                  <?php echo $this->itemPhoto($photo, 'thumb.normal') ?>
                </div>     
                <div class="sitepageevents_editphotos_info">
                  <?php
                  $key = $photo->getGuid();
                  echo $this->form->getSubForm($key)->render($this);
                  ?>
                  <div class="sitepageevents_editphotos_cover">
                    <input type="radio" name="cover" value="<?php echo $photo->getIdentity() ?>" <?php if ($this->sitepageevent->photo_id == $photo->file_id): ?> checked="checked"<?php endif; ?> />
                  </div>
                  <div class="sitepageevents_editphotos_label">
                    <label><?php echo $this->translate('Main Photo'); ?></label>
                  </div>
                </div>
              </li>    
            <?php endforeach; ?>
              <input type="hidden" name='format' value='<?php echo $this->format; ?>'>
             <div style="text-align: center"><?php echo $this->translate('or'); ?> </div>
          <a href="#" data-rel="back" data-role="button">
            <?php echo $this->translate('Cancel') ?>
          </a>
          </ul>
        </div>
      </div>
    </form>
  </div>

<?php else: ?>
  <form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>" class="global_form">
    <?php echo $this->translate('Edit Photo') ?>
    <ul class='sitepageevents_editphotos'>        
      <?php foreach ($this->paginator as $photo): ?>
        <li>     
          <div class="sitepageevents_editphotos_info">
            <?php
            $key = $photo->getGuid();
            echo $this->form->getSubForm($key)->render($this);
            ?>
            <div class="sitepageevents_editphotos_cover">
              <input type="radio" name="cover" value="<?php echo $photo->getIdentity() ?>" <?php if ($this->sitepageevent->photo_id == $photo->file_id): ?> checked="checked"<?php endif; ?> />
            </div>
            <div class="sitepageevents_editphotos_label">
              <label><?php echo $this->translate('Main Photo'); ?></label>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
      <?php if ($this->format == 'smoothbox'): ?>
        <input type="hidden" name='format' value='<?php echo $this->format; ?>'>
      <?php endif; ?>
      <?php echo $this->form->execute->render(); ?>
      <div style="text-align: center"><?php echo $this->translate('or'); ?> </div>
          <a href="#" data-rel="back" data-role="button">
            <?php echo $this->translate('Cancel') ?>
          </a>
    </ul>
  </form>
<?php endif; ?>