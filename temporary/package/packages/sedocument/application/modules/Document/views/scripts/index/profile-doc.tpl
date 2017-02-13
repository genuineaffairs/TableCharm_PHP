<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: profile-doc.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class='global_form_popup'>
  <?php if ($this->success): ?>
    <script type="text/javascript">
      parent.$('document-item-<?php echo $this->document_id ?>').destroy();
      setTimeout(function() {
        parent.Smoothbox.close();
      }, 1000 );
    </script>
    <div class="global_form_popup_message">
      <?php echo $this->translate('Done Successfully !'); ?> 
    </div>
  <?php else: ?>
	  <form method="POST" action="<?php echo $this->url() ?>">
	    <div>
				<?php if($this->profile_doc == 0):?>
					<h3><?php echo $this->translate('Make Profile Document ?'); ?></h3>
					<p>
						<?php echo $this->translate('Are you sure you want to make this document your Profile Document? (Note: At any time only one document can be showcased as your Profile Document. Thus, if you have made any document as your profile document currently, then it will be changed to this one.)'); ?>
					</p>
					<p>&nbsp;
					</p>
					<p>
						<input type="hidden" name="document_id" value="<?php echo $this->document_id?>"/>
						<button type='submit'><?php echo $this->translate('Confirm'); ?></button>
						<?php echo $this->translate(' or ')?> <a href="javascript:void(0);" onclick="parent.Smoothbox.close();"><?php echo $this->translate('cancel')?></a>
					</p>
				<?php elseif($this->profile_doc == 1): ?>
					<h3><?php echo $this->translate('Remove as Profile Document'); ?></h3>
					<p>
						<?php echo $this->translate('Are you sure you want to remove this document as your Profile Document?'); ?>
					</p>
					<p>&nbsp;
					</p>
					<p>
						<input type="hidden" name="document_id" value="<?php echo $this->document_id?>"/>
						<button type='submit'><?php echo $this->translate('Remove'); ?></button>
						<?php echo $this->translate(' or ')?> <a href="javascript:void(0);" onclick="parent.Smoothbox.close();"><?php echo $this->translate('cancel')?></a>
					</p>
				<?php endif;?>
	    </div>

	  </form>
  <?php endif; ?>
</div>

<?php if( @$this->closeSmoothbox ): ?>
	<script type="text/javascript">
  	TB_close();
	</script>
<?php endif; ?>