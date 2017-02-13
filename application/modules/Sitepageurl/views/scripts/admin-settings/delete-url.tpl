<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageurl
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: form.tpl 2010-07-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate('Delete this entry?');?></h3>
    <p> <?php echo $this->translate('Are you sure that you want to delete this entry? It will not be recoverable after being deleted.');?> </p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->id?>"/>
      <button type='submit'><?php echo $this->translate('Delete'); ?></button>
      or <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel');?></a> </p>
  </div>
</form>

<?php if( @$this->closeSmoothbox ): ?>
	<script type="text/javascript">
  		TB_close();
	</script>
<?php endif; ?>