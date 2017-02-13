<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if( count($this->navigation) ): ?>
	<div class="headline">
		<h2><?php echo $this->translate('Documents'); ?></h2>
		<div class='tabs'>
			<?php echo $this->navigation()->setContainer($this->navigation)->menu()->render() ?>
		</div>
	</div>  
<?php endif; ?>

<div class='global_form'>
  <form method="post" class="global_form">
  	<div>
      <div>
      	<h3><?php echo $this->translate('Delete Document ?');?></h3>
      	<p>
        	<?php echo $this->translate('Are you sure that you want to delete the document titled "%1$s" ? It will not be recoverable after being deleted.', $this->document->document_title); ?>
      	</p>
      	<br />
      	<p>
        	<input type="hidden" name="confirm" value="true"/>
        	<button type='submit' style="color:#D12F19;"><?php echo $this->translate('Delete');?></button>
        	<?php echo $this->translate('or');?> <a href='<?php echo $this->url(array(), 'document_manage', true) ?>'><?php echo $this->translate('cancel');?></a>
      	</p>
    	</div>
    </div>
  </form>
</div>