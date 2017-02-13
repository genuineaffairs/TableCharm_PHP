<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php ?>
<script type="text/javascript">
  function setModelDetail(model_id,place){
    switch(model_id){
      case "Pay/click":
        if(place=='set')
          $('model_click').value = $('model_detail').value;        
        $('model_click-wrapper').setStyle('display', 'block');        
        $('model_view-wrapper').setStyle('display', 'none');
        $('model_period-wrapper').setStyle('display', 'none');
         $("renew_before_msg").innerHTML='clicks';
        break;
      case "Pay/view":
        $('model_click-wrapper').setStyle('display', 'none');
        if(place=='set')
          $('model_view').value = $('model_detail').value;
        $('model_view-wrapper').setStyle('display', 'block');
        $('model_period-wrapper').setStyle('display', 'none');
         $("renew_before_msg").innerHTML='views';
        break;
      case "Pay/period":
        $('model_click-wrapper').setStyle('display', 'none');
        if(place=='set')
          $('model_period').value = $('model_detail').value;
        $('model_view-wrapper').setStyle('display', 'none');
        $('model_period-wrapper').setStyle('display', 'block');
         $("renew_before_msg").innerHTML='days';
        break;
    }
  }

 function setRenewBefore(){ 
     if($('renew').checked)
       $('renew_before-wrapper').setStyle('display', 'block');
     else
       $('renew_before-wrapper').setStyle('display', 'none');
 }

  window.addEvent('domready', function() {
    var model_id =  $('price_model').value;
    setModelDetail(model_id,'set');
      setRenewBefore();
  });
</script>
<style type="text/css">
.settings form{
	float:none;
}
.settings .form-element{
	width:650px;
}
.settings .form-element .description{
	max-width: 650px;
}
input[type="checkbox"]{
	float:left;
}
label.optional{
	float:left;
	max-width:600px;
}
</style>
<h2><?php echo $this->translate("Community Ads Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='communityad_admin_tabs'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>
  <div>
  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'packagelist', 'action' => 'index'), $this->translate('Back to Manage Ad Packages'), array('class'=>'cmad_icon_back buttonlink')) ?>
</div>
<br />
<div class='clear'>
  <div class='settings'>

    <?php echo $this->form->render($this); ?>
  </div>
</div>
<?php if (@$this->closeSmoothbox): ?>
      <script type="text/javascript">
        TB_close();
      </script>
<?php endif; ?>