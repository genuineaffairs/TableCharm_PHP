<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: email.tpl 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if($this->is_error == 1):?>
	<ul class="form-errors">
		<?php foreach($this->error_array as $key => $item): ?>
	  	<li style="font-size:12px;">
	    	<?php echo $item ?>
	   	</li>
	  <?php endforeach; ?>
	</ul>
	<script type="text/javascript">
  	setTimeout("parent.Smoothbox.close();", "2000");
	</script>
<?php endif; ?>

<?php if($this->msg != ''): ?>
	<ul class="form-notices">
		<li>
			<?php echo $this->msg ?>
		</li>	
	</ul>
	<script type="text/javascript">
  	setTimeout("parent.Smoothbox.close();", "2000");
	</script>
<?php endif;?>

<?php if($this->excep_error == 1): ?>
	<ul class="form-errors">
		<li style="font-size:12px;">
			<?php echo $this->excep_message ?>
		</li>
	</ul>
	<script type="text/javascript">
  	setTimeout("parent.Smoothbox.close();", "2000");
	</script>
<?php endif; ?>

<?php if ($this->no_form != 1): ?>
	<form method="post">
		<div class="documents_popup">
			<h3><?php echo $this->translate("Email Document as Attachment");?></h3>
			<p>
				<label>
				<?php echo $this->translate('To'); ?>
				</label>
				<input type="text" class="text" name="to" value="<?php echo $this->to ?>" maxlength="100" />
			</p>
			<p>
				<label>
					<?php echo $this->translate('Attachment'); ?>
				</label>
				<span>
					<?php echo $this->htmlImage('application/modules/Document/externals/images/email_attachment.png', '', array('class'=>'icon', 'border'=>'0', 'style'=>'vertical-align:middle;')) ?> 
					<?php echo $this->attach; ?>
				</span>
			</p>
			<p>
				<label><?php echo $this->translate('Subject'); ?></label>
				<input type="text" class="text" name="subject" value="<?php echo $this->subject ?>" maxlength="100" />
			</p>
			<p>
				<label><?php echo $this->translate('Message'); ?></label>
				<textarea name="message"  style="width: 350px;" rows="5" ><?php echo $this->message ?></textarea>
			</p>
			<p>
				<label>&nbsp;</label>
				<button type="submit" name="submit" value="Send" ><?php echo $this->translate('Send') ?></button>
        <?php echo $this->translate(' or ') ?> <a href="javascript:void(0);" onclick="parent.Smoothbox.close();"><?php echo $this->translate('cancel') ?></a>
			</p>
		</div>
	</form>
<?php endif; ?>