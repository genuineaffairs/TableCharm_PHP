<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: email.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/modules/Sitepagedocument/externals/styles/style_sitepagedocument.css')
?>
<?php if ($this->is_error == 1): ?>
  <ul class="form-errors">
    <?php foreach ($this->error_array as $key => $item): ?>
      <li>
        <?php echo $item ?>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php if ($this->smoothbox_error != 1): ?>
    <script type="text/javascript">
      setTimeout("parent.Smoothbox.close();", "2000");
    </script>
  <?php else: ?>
    <script type="text/javascript">
      setTimeout("parent.Smoothbox.close();", "900000000");
    </script>
  <?php endif; ?>
<?php endif; ?>

<?php if ($this->msg != ''): ?>
  <ul class="form-notices">
    <li>
      <?php echo $this->msg ?>
    </li>	
  </ul>
  <script type="text/javascript">
    setTimeout("parent.Smoothbox.close();", "2000");
  </script>
<?php endif; ?>

<?php if ($this->excep_error == 1): ?>
  <ul class="form-errors">
    <li>
      <?php echo $this->excep_message ?>
    </li>
  </ul>
  <script type="text/javascript">
    setTimeout("parent.Smoothbox.close();", "2000");
  </script>
<?php endif; ?>

<?php if ($this->no_form != 1): ?>
  <form method="post">
    <div class="sitepagedocuments_popup">
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
      <div>
        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepagedocument/externals/images/email_attachment.png', '', array('class' => 'icon', 'border' => '0', 'style' => 'vertical-align:middle;')) ?> 
        <?php echo $this->attach; ?>
      </div>
      </p>
      <p>
        <label><?php echo $this->translate('Subject'); ?></label>
        <input type="text" class="text" name="subject" value="<?php echo $this->subject ?>" maxlength="100" />
      </p>
      <p>
        <label><?php echo $this->translate('Message'); ?></label>
        <textarea name="message"  rows="5" ><?php echo $this->message ?></textarea>
      </p>
      <p>
        <label>&nbsp;</label>
        <button type="submit" name="submit" value="Send" ><?php echo $this->translate('Send') ?></button>
        <?php echo $this->translate(' or ') ?> <a href="javascript:void(0);" onclick="parent.Smoothbox.close();"><?php echo $this->translate('cancel') ?></a>
      </p>
    </div>
  </form>
<?php endif; ?>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>