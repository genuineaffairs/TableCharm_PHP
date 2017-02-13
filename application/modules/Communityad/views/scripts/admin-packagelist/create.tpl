<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
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
  <div>
    <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'communityad', 'controller' => 'packagelist', 'action' => 'index'), $this->translate('Back to Manage Ad Packages'), array('class' => 'cmad_icon_back buttonlink')) ?>
  </div>
  <br />
<?php endif; ?>
<script type="text/javascript">
  function setModelDetail(model_id){ 
    switch(model_id){  
      case "Pay/click":
        $('model_click-wrapper').setStyle('display', 'block');
        $('model_view-wrapper').setStyle('display', 'none');
        $('model_period-wrapper').setStyle('display', 'none');
        $("renew_before_msg").innerHTML='clicks';
        break;
      case "Pay/view":
        $('model_click-wrapper').setStyle('display', 'none');
        $('model_view-wrapper').setStyle('display', 'block');
        $('model_period-wrapper').setStyle('display', 'none');
        $("renew_before_msg").innerHTML='views';
        break;
      case "Pay/period":
        $('model_click-wrapper').setStyle('display', 'none');
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
    setModelDetail(model_id);
       
    setRenewBefore();
  });
  function setTypeBaseContent(value){
    if(value=='default'){
//      $('placement-wrapper').style.display=''; 
      $('sponsored-wrapper').style.display='';      
      $('featured-wrapper').style.display='';        
      $('network-wrapper').style.display='';       
      $('public-wrapper').style.display=''; 
      $('network').value=1;
      $('public').value=1;
      insertOptionBefore();
     // $('urloption').options['0'].style.display='';
      $('urloption_description').innerHTML='<?php echo $this->string()->escapeJavascript($this->translate('Select the content types that you want to be advertised in this package. Choosing “Custom Ad” will enable the advertiser to create a custom ad. (Press Ctrl and click to select multiple types.)')); ?>';
       
    }else if(value=='sponsored_stories'){ 
//      $('placement-wrapper').style.display='none'; 
      $('sponsored-wrapper').style.display='none';
      $('sponsored').value=0;
      $('featured-wrapper').style.display='none';
      $('featured').value=0;
    /*  $('network-wrapper').style.display='none';
      $('network').value=0;*/
      $('public-wrapper').style.display='none';
      $('public').value=0;
      $('urloption').remove(0);
     
    //  $('urloption').options['0'].style.display='none';
    //  $('urloption').options['0'].selected='';    
      $('urloption').options['0'].selected='selected';    
      $('urloption_description').innerHTML='<?php echo $this->string()->escapeJavascript($this->translate('Select the content types that you want to be advertised in this package.(Press Ctrl and click to select multiple types.)')); ?>';
    }
  }
  
function insertOptionBefore()
{
  var elSel = document.getElementById('urloption');

    var elOptNew = document.createElement('option');
    elOptNew.text = '<?php echo $this->string()->escapeJavascript($this->translate('Custom Ad'))?>';
    elOptNew.value = 'website';
    var elOptOld = elSel.options[0];  
    try {
      elSel.add(elOptNew, elOptOld); // standards compliant; doesn't work in IE
    }
    catch(ex) {
      elSel.add(elOptNew, 0); // IE only
    }
  
  var i;
  for (i = elSel.length - 1; i>=0; i--) {
   elSel.options[i].selected='';
  }
  $('urloption').options['0'].selected='selected';
}
</script>
<div class='clear'>
  <div class='settings' id="myFormId">
    <?php echo $this->form->render($this) ?>
  </div>
</div>
