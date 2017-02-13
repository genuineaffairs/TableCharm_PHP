<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate('Delete Page Album ?'); ?></h3>
    <p>
      <?php echo $this->translate('Are you sure that you want to delete this page album ? It will not be recoverable after being deleted.'); ?>
    </p>
    <?php if ($this->default_album_value) : ?>
      <br /><div class="tip">
        <span>
          <?php echo $this->translate('Please note that this is the default album of its page. If this album is deleted, then users other than the page admins will not be able to add photos to this page.'); ?>		
        </span>     
      </div>
    <?php endif; ?>  
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->album_id ?>"/>
      <button type='submit'><?php echo $this->translate('Delete'); ?></button>
      <?php echo $this->translate('or') ?> <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
    </p>
  </div>
</form>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>