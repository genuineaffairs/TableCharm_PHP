<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: target.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<style type="text/css">

.settings .form-wrapper{
	margin-top:15px;
}
#profile_base_targeting-wrapper .form-label{
	width:300px;
}
#target_birthday-wrapper .form-label
{
	display:none;
}
#profile_base_targeting_others-element{
	max-width:100% !important;
}
</style>
<h2><?php echo $this->translate("Community Ads Plugin") ?></h2>

<script type="text/javascript">
//  var fetchLevelSettings =function(level_id){
//    window.location.href = en4.core.baseUrl + 'admin/communityad/target/' + level_id;
//  }

</script>





<?php if (count($this->navigation)): ?>
  <div class='communityad_admin_tabs'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>

<?php endif; ?>
 
  <div class='seaocore_settings_form'>
    <div class='settings' id="myFormId">
    <?php echo $this->form->render($this) ?>
  </div>
</div>