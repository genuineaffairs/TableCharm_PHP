<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: editcamp.tpl  2011-02-16 9:40:21Z SocialEngineAddOns $
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
<form method="post" class="global_form">
  <div>
    <div>
      <h3><?php echo $this->translate("Edit Campaign Title") ?></h3>
      <br />
      <p>
        <input type="hidden" name="confirm" value="<?php echo $this->adcampaign_id ?>"/>

        <input type="text" name="name" maxlength="100" value= '<?php echo $this->camp_title ?>' /><br /><br />

        <button type='submit'><?php echo $this->translate("Save Title") ?></button>
        <?php echo Zend_Registry::get('Zend_Translate')->_(' or ') ?>
        <a href='<?php echo $this->url(array(), 'communityad_campaigns', true) ?>'>
          <?php echo $this->translate("cancel") ?></a>
      </p>
    </div>
  </div>
</form>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>            
<?php endif; ?>