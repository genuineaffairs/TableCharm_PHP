<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate('Delete Classified ?'); ?></h3>
    <p>
      <?php echo $this->translate('Are you sure that you want to delete this Classified ? It will not be recoverable after being deleted.'); ?>
    </p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->classified_id?>"/>
      <button type='submit'><?php echo $this->translate('Delete'); ?></button>
      or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel'); ?></a>
    </p>
  </div>
</form>
<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>