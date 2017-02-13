<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: publish.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class='global_form_popup'>
  <?php if ($this->success): ?>
    <script type="text/javascript">
      //      parent.$('sitepagedocument-item-<?php echo $this->document_id ?>').destroy();
      //      setTimeout(function() {
      //        parent.Smoothbox.close();
      //      }, 1000 );
    </script>
    <div class="global_form_popup_message">
      <?php echo $this->translate('Your document has been published.'); ?> 
    </div>
  <?php else: ?>
    <form method="POST" action="<?php echo $this->url() ?>">
      <div>
        <h3><?php echo $this->translate('Publish Page Document ?'); ?></h3>
        <p>
          <?php echo $this->translate('Are you sure that you want to publish this Page document ?'); ?>
        </p>
       
        <br/>
       
          <input type="hidden" name="document_id" value="<?php echo $this->document_id ?>"/>
          <button type='submit' data-theme="b"><?php echo $this->translate('Publish'); ?></button>
           <?php echo $this->translate('or'); ?>
        <a href="#" data-rel="back" data-role="button">
          <?php echo $this->translate('Cancel') ?>
        </a>

      </div>
    </form>
  <?php endif; ?>
</div>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    //  TB_close();
  </script>
<?php endif; ?>