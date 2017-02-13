<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: highlighted.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class='global_form_popup'>
  <?php if ($this->success): ?>
    <script type="text/javascript">
      parent.$('sitepagevideo-item-<?php echo $this->video_id ?>').destroy();
      setTimeout(function() {
        parent.Smoothbox.close();
      }, 1000 );
    </script>
    <div class="global_form_popup_message">
      <?php echo $this->translate('Your video has been highlighted.'); ?> 
    </div>
  <?php else: ?>
    <form method="POST" action="<?php echo $this->url() ?>">
      <div>
        <?php if ($this->highlighted == 0): ?>
          <h3><?php echo $this->translate('Make Video Highlighted ?'); ?></h3>
          <p>
            <?php echo $this->translate('Are you sure that you want to make this Page Video highlighted?'); ?>
          </p>
          <p>&nbsp;
          </p>
          <p>
            <input type="hidden" name="video_id" value="<?php echo $this->video_id ?>"/>
            <button type='submit'><?php echo $this->translate('Make Highlighted'); ?></button>
            <?php echo $this->translate(' or ') ?> <a href="javascript:void(0);" onclick="parent.Smoothbox.close();"><?php echo $this->translate('cancel') ?></a>
          </p>
        <?php elseif ($this->highlighted == 1): ?>
          <h3><?php echo $this->translate('Make Video Un-highlighted ?'); ?></h3>
          <p>
            <?php echo $this->translate('Are you sure that you want to make this Page Video un-highlighted ?'); ?>
          </p>
          <p>&nbsp;
          </p>
          <p>
            <input type="hidden" name="video_id" value="<?php echo $this->video_id ?>"/>
            <button type='submit'><?php echo $this->translate('Make Un-highlighted'); ?></button>
            <?php echo $this->translate(' or ') ?> <a href="javascript:void(0);" onclick="parent.Smoothbox.close();"><?php echo $this->translate('cancel') ?></a>
          </p>
        <?php endif; ?>
      </div>

    </form>
  <?php endif; ?>
</div>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>