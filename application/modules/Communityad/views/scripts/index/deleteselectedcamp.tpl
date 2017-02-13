<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: deleteselectedcamp.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="headline">
  <h2>
    <?php echo $this->translate('Advertising'); ?>
  </h2>
  <div class='tabs'>
    <?php echo $this->navigation($this->navigation)->render() ?>
  </div>
</div>
<div class="settings">
  <?php if ($this->ids): ?>
    <form method="post" class="global_form">
      <div>
        <div>
          <h3><?php echo $this->translate("Delete the selected Campaigns?") ?></h3>
          <p>
          <?php echo $this->translate("Are you sure that you want to delete the %d campaigns? It will not be recoverable after being deleted.", $this->count) ?>
        </p>
        <br />
        <p>
          <input type="hidden" name="confirm" value='true'/>
          <input type="hidden" name="ids" value="<?php echo $this->ids ?>"/>

          <button type='submit'><?php echo $this->translate("Delete") ?></button>
          <?php echo Zend_Registry::get('Zend_Translate')->_(' or ') ?>
          <a href='<?php echo $this->url(array(), 'communityad_campaigns', true) ?>'>
            <?php echo $this->translate("cancel") ?></a>
        </p>
      </div>
    </div>
  </form>
  <?php else: ?>
              <div class="tip"><span><?php echo $this->translate("Please select a campaign to delete.") ?></span></div>
              <a href="<?php echo $this->url(array(), 'communityad_campaigns', true) ?>" class="buttonlink icon_back">
    <?php echo $this->translate("Go Back") ?>
            </a>
  <?php endif; ?>
            </div>         
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>