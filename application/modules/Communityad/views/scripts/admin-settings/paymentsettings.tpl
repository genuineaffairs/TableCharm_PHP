<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: paymentsettings.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo $this->translate("Community Ads Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
<div class='communityad_admin_tabs'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>
<div class='clear'>
  <div class='settings'>
   <?php echo $this->form->render($this); ?>
	</div>
</div>
<script type="text/javascript">
  $$('input[type=select]:([name=mode])').addEvent('click', function(e){
    $(this).getParent('.form-wrapper').getAllNext(':([id^=mode])').setStyle('display', ($(this).get('value')>0?'none':'none'));
  });
</script>