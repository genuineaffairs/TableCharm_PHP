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
if (!empty($this->editFlag)) {
  $resouceTitle = Engine_Api::_()->communityad()->truncation($this->resourceObj->getTitle(), $this->titleLimit);
  $contentTitle = Engine_Api::_()->communityad()->truncation($this->viewer->getTitle(), $this->rootTitleLimit);
  $resourceImage = $this->itemPhoto($this->resourceObj, 'thumb.normal');
  if( !empty($this->storyResourceType) && (($this->storyResourceType == 'blog') || ($this->storyResourceType == 'music')) ){
    $resourceImage = $this->itemPhoto($this->resourceObj, 'thumb.icon');
  }

  $contentTitleWithTooltip = '<span class="cmad_show_tooltip_wrapper"><b> <a href="javascript:void(0);">' . 
      $contentTitle . '</a> </b><div class="cmad_show_tooltip"><img src="'. $this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/tooltip_arrow.png" />' . $this->translate("_sponsored_viewer_title_tooltip") . '</div></span>';
}
?>

<script type="text/javascript">

  //var resource_type = '';
  var storyType_id = '';
  window.addEvent('domready', function() {
    if( $('resource_type').value == 0 ){
      $('resource_type').value = '<?php $this->form->resource_type->getValue(); ?>';
    }

	$('editFlag').value = '<?php echo $this->resource_id; ?>';
	// We are set FLAG value 1, when loggden user will go on next step of sponsored stories.
	if( !$('flag').value ) {
	  // Following we are set the "SELECT CONTENT" dropdown.
	  $('resource_id-wrapper').style.display = 'block';
	  var subcatss = '<?php echo $this->edit_sub_title; ?>' . split("::");
	  addOption($('resource_id')," ", '0');
	  for (var i=0; i < subcatss.length;++i){
		var subcatsss = subcatss[i].split("_");
		addOption($('resource_id'), subcatsss[0], subcatsss[1]);
	  }
	  $('resource_id').value = '<?php echo $this->resource_id ?>';

	  // Calling the function which will set the preview.
	  storyType_id = '<?php echo $this->story_type; ?>';
	  setTimeout("resourceData('<?php echo $this->resource_id; ?>')", 2000);
	  $('flag').value = 1;
	}else {

      $('story_main_title_str').innerHTML = '<?php echo $this->translate('%s likes <a href="javascript:void(0);">%s.</a>', $contentTitleWithTooltip, $resouceTitle); ?>';

      $('story_content_title_div').innerHTML = '<?php echo '<a href="javascript:void(0);" id="story_content_title">' . str_replace(' ', '&nbsp;', $resouceTitle) . '</a><div class="cmad_show_tooltip"><img src="'. $this->layout()->staticBaseUrl . 'application/modules/Communityad/externals/images/tooltip_arrow.png" />' . $this->translate("_sponsored_content_title_tooltip") . '</div>'; ?>';
      $('story_content_photo').innerHTML = '<a id="story_content_title" href="javascript:void(0);"><?php echo $resourceImage; ?></a><div class="cmad_show_tooltip"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/tooltip_arrow.png" /><?php echo $this->translate("_sponsored_content_photo_tooltip");?></div>';
	  $('like_button').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("Like This ")) ?>' + '<?php echo $this->getModTitle; ?>';
	}
  
   <?php if( !empty($this->titileName)) :?>
    $('name').value="<?php echo str_replace('&nbsp;', ' ', $this->titileName) ?>";
   <?php endif; ?>
  });

</script>
<h3 style="margin-bottom:10px;" class="fleft"> 
  <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/ad-icon.png" alt="" class="ad_icon" />
  <?php echo $this->translate("Edit Your Sponsored Story") ?>
</h3>
<div style="height:0px;" class="clr"></div>
<div class="layout_middle">
  <div id="create_sponsoredstory_div">
	<fieldset>
	  <form id="create_sponsoredstory_form" method="post" >
		<div id="create_sponsoredstory" class="cmad_ad_steps">
		  <h4 class="fleft"> <?php echo $this->translate("2.") . " " . $this->form->getTitle(); ?></h4>

		    <?php if(!empty($this->design_faq)) : ?>
			    <h4 class="fright"><a href='<?php echo $this->url(array('page_id' => 100), 'communityad_help_and_learnmore', true) ?>' target='_blank'><?php echo $this->translate('Design Your Sponsored Story FAQ')?></a></h4>
		    <?php endif; ?>

		  <div class="global_form">
			<div>
			  <div>
				<div class="cmad_form_left">
				  <div class="form-elements">
					<?php
					$elementTitleForm = $this->form->getElements();
					foreach ($elementTitleForm as $key => $value) {
					  if ($key != 'preview')
						echo $this->form->$key;
					}
					?>
				  </div>
				</div>
				<!--Ad Preview Start here-->
				<?php echo $this->form->preview; ?>
					<!--Ad Preview End here-->
				  </div>
				</div>
			  </div>
			</div>
			<div id="list"></div>
			 <div id = "target_sponsoredstory" class="slide cmad_ad_steps">
		  <?php
					  /* Include the common user-end field switching javascript */
					  echo $this->partial('_jsSwitch.tpl', 'fields', array(
						  'topLevelId' => (int) @$this->topLevelId,
						  'topLevelValue' => (int) @$this->topLevelValue
					  ));
		  ?>

          <?php if($this->showTargetingTitle): ?>
          <h4 class="fleft">
            <?php echo $this->translate("3.")." ".$this->formField->getTitle()." ".$this->translate("and Scheduling"); ?> 
          </h4>
        <?php if(!empty($this->target_faq)) : ?>
          <h4 class="fright">
            <a href='<?php echo $this->url(array('display_faq' => 3), 'communityad_help_and_learnmore', true) ?>' target='_blank'><?php echo $this->translate('Ad Targeting FAQ')?></a>
          </h4>
        <?php endif; ?>
             <?php else: ?>
              <h4 > <?php echo $this->translate("3. Scheduling"); ?> </h4>
          <?php endif; ?>
			  		  <div class="global_form">
			  			<div>
			  			  <div>
			  				  <?php if($this->showTargetingTitle): ?>
                   <h5 > <?php echo $this->formField->getTitle(); ?> </h5>
                   <?php endif; ?>
			  				<div class="form-elements">
                  <?php
                    $subforms = $this->formField->getSubForms();
                    foreach ($subforms as $formField) :
                      echo $formField->render($this);
                    endforeach;
                    $elementTargetForm = $this->formField->getElements();
                    foreach ($elementTargetForm as $key => $value):
                         if($key != "cads_start_date" && $key != "cads_end_date" && $key != "enable_end_date" &&  $key != "continue_review" &&  $key !="continue" )
                            echo $this->formField->$key;
                    endforeach;
                  ?>
	               </div>
                 <?php if($this->showTargetingTitle): ?>
                <h5 style="padding-top:10px;margin-top:5px;"> <?php echo $this->translate("Scheduling"); ?> </h5>
                <?php endif; ?>
                  <div class="form-elements">
                  <?php
                    echo $this->formField->cads_start_date;
                    echo $this->formField->enable_end_date;
                    echo $this->formField->cads_end_date;                   
                  ?>
                </div>
	             </div>
	          </div>
	        </div>
	       </div>	
         <br />
        <div style="margin-left:20px;" id="create_sponsoredstory_button">
            <button type="button" id="continue_next"> <?php echo $this->translate("Continue") ?></button>
<?php echo $this->translate(" or ") ?><a href="javascript:void(0);" onclick= "cancel()"><?php echo $this->translate("Cancel") ?></a> </div>
        </div>
			 </form>
		</fieldset>
	</div>
 <div id="preview_sponsoredstory" class="global_form cmad_ad_steps">
			      <h4 class="fleft"><?php echo  "4. "  . $this->translate("Review Your Sponsored Story") ?></a></h4>
			      <h4 class="fright"><a href='<?php echo $this->url(array(), 'communityad_help_and_learnmore', true) ?>' target='_blank'><?php echo $this->translate('Help Center') ?></a></h4>
			      <div>
			        <div>
			          <input type="hidden" name="name" value="value1" >
			          <h3></h3>
			          <p>
		  <?php echo $this->translate("Please check your Sponsored Story for accuracy before placing your order.") ?>
			          </p>
			          <div class="form-elements">
			            <div class="cmaddis_preview_wrapper">
			              <div class="form-label">
			                <label><?php echo $this->translate("Sponsored Story Preview:"); ?></label>
			              </div>
			              <div class="form-element">
			  			  <div class="cadcp_preview" id="preview">
			  			  </div>
			              </div>
			            </div>
			          </div>
			          <div class="form-elements" >
			            <div class="form-wrapper">
			              <div class="form-label">
			                <label><?php echo $this->form->package_name->getLabel() . ":"; ?></label>
			              </div>
			              <div class="form-element">
			  <?php echo $this->form->package_name->getDescription(); ?>
		              </div>
		            </div>

		            <div class="form-wrapper">
		  			<div class="form-label">
		  			  <label><?php echo $this->translate('Price:'); ?></label>
		  			</div>
		  			<div class="form-element">
			  <?php if (!$this->package->isFree()):echo $this->locale()->toCurrency($this->package->price, $currency);
						else: echo $this->translate('FREE');
						endif; ?>
		  			</div>
		  		  </div>
		  		  <div class="form-wrapper">
		  			<div class="form-label">
		  			  <label><?php echo $this->translate('Quantity:'); ?></label>
		  			</div>
		  			<div class="form-element">
			  <?php
						switch ($this->package->price_model):
						  case "Pay/view":
							if ($this->package->model_detail != -1):echo $this->translate(array('%s View', '%s Views', $this->package->model_detail), $this->locale()->toNumber($this->package->model_detail));
							else: echo $this->translate('UNLIMITED Views');
							endif;

							break;

						  case "Pay/click":
							if ($this->package->model_detail != -1):echo $this->translate(array('%s Click', '%s Clicks', $this->package->model_detail), $this->locale()->toNumber($this->package->model_detail));
							else: echo $this->translate('UNLIMITED Clicks');
							endif;

							break;

						  case "Pay/period":
							if ($this->package->model_detail != -1):echo $this->translate(array('%s Day', '%s Days', $this->package->model_detail), $this->locale()->toNumber($this->package->model_detail));
							else: echo $this->translate('UNLIMITED  Days');
							endif;

							break;
						endswitch;
			  ?>
		  			</div>
		  		  </div>


		            <div class="form-wrapper">
		              <div class="form-label">
		                <label><?php echo $this->form->campaign_name->getLabel() . ":"; ?></label>
		              </div>
		              <div class="form-element" id="campaign_name_preview">
		              </div>
		            </div>
		            <div class="form-wrapper">
		              <div class="form-label">
		                <label><?php echo $this->form->resource_type->getLabel() . ":"; ?></label>
		              </div>
		              <div class="form-element" id="resource_type_preview">
		              </div>
		            </div>

		  		  <div class="form-wrapper">
		              <div class="form-label">
		                <label><?php echo $this->translate('Name:'); ?></label>
			              </div>
			              <div class="form-element" id="resource_id_content">
            <?php if (!empty($this->resourceObj)):
						  echo $this->resourceObj->getTitle();
						endif; ?>
			              </div>
			            </div>
            <div class="form-wrapper">
						  <div class="form-label">
						    <label><?php echo $this->formField->cads_start_date->getLabel().":"; ?></label>
						  </div>
						  <div class="form-element">
						  	<span id = "ad_start_date"></span>      
						  </div>
						</div>
	          <div class="form-wrapper">
						  <div class="form-label">
						    <label><?php echo $this->formField->cads_end_date->getLabel().":"; ?></label>
						  </div>
						  <div class="form-element">
						  	<span id = "ad_end_date"></span>      
						  </div>
						</div>

<?php if (!empty($this->showTargetingTitle)): ?>
						  <span id="targetDetails_hedding" style="display:none;">
							<br /><br />
							<h5><?php echo $this->translate('Targeting') ?></h5></span>
<?php endif; ?>
						  <p style="width:100%;max-width: 100%;"><div id="tdata"></div>
<?php if (!empty($this->birthday_enable)): ?>
			  		  <div>
			  			<div id="birthday_enable_span"></div>
			  		  </div>
				  <?php endif; ?></p>

		            <div class="form-wrapper">
		              <div class="form-label">
		                <label>&nbsp;</label>
		              </div>
		              <div class="form-element">
                  <?php if (!empty($this->storyResourceType) && !empty($this->storyResourceId)): ?>
                                        <button name = "saveOrder" onclick="submitForm(0, '<?php echo $this->storyResourceType; ?>', <?php echo $this->storyResourceId; ?>)">
                          <?php else: ?> <button name = "saveOrder" onclick="submitForm(0, '', 0)">
                  <?php endif; ?>
                  <?php echo $this->translate("Save Changes"); ?></button>
                  <?php if (!empty($this->storyResourceType) && !empty($this->storyResourceId) && !empty($this->story_type)) { ?>
                  <button name = "editad"  onclick= "slideContent('<?php echo $this->storyResourceType; ?>', <?php echo $this->storyResourceId; ?>, <?php echo $this->story_type; ?>)" ><?php echo $this->translate("Edit Sponsored Story") ?></button>

                  <?php } else { ?>

                  <button name = "editad"  onclick= "createSlideContent()" ><?php echo $this->translate("Edit Sponsored Story") ?></button>

                  <?php } ?>
                 <button name = "editad"  onclick= "cancel()" ><?php echo $this->translate("Cancel") ?></button>
				            </div>
				          </div>
				        </div>
				      </div>
					</div>
				  </div>
				</div>
<script type="text/javascript">
  var updateTextFields = function()
  {
    var adcampaign = document.getElementById("campaign_id");
    var namewrapper = document.getElementById("campaign_name-wrapper");
    var name = document.getElementById("campaign_name");

    if (adcampaign.value == 0)
    {
      namewrapper.style.display = "block";
    }
    else
    {
      namewrapper.style.display = "none";
      var camText='';
      for(var i=(adcampaign.options.length-1);i>=0;i--)
      {
        if(adcampaign.options[i].value==adcampaign.value){
          camText=adcampaign.options[i].text;
          break;
        }
      }

      name.value=camText;
    }
  }
  en4.core.runonce.add(updateTextFields);
  var slidePreviewDiv=null;
  var slideCreateDiv=null;


  var slideCreateTargetDiv=null;
  window.addEvent('domready', function() {

    slideCreateDiv = new Fx.Slide($('create_sponsoredstory_div'), {
      duration: 600,
      resetHeight:true
    });

    slidePreviewDiv = new Fx.Slide($('preview_sponsoredstory'), {
      duration: 600,
      resetHeight:true
    });


      profileFields(<?php echo $this->profileSelect_id ?>);
<?php if (!$this->mode): ?>

        slideCreateDiv.hide();
        showPreview();
<?php else: ?>     
        slidePreviewDiv.hide();
<?php endif; ?>

      $('continue_next').addEvent('click', function(){
        var flage=checkValidation();
        if(!flage)
          return flage;
        
         if ($('cads_end_date-date').value == "" && !$m("enable_end_date").checked )
      {
        if(!$('validation_cads_end_date-element')){
          var div_cads_end_date = document.getElementById("cads_end_date-element");
          var myElement = new Element("p");
          myElement.innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("End Date not selected.")) ?>';
          myElement.addClass("error");
          myElement.id = "validation_cads_end_date-element";
         div_cads_end_date.appendChild(myElement);
        }
         flage=false;
      }

        if(!flage)
          return flage;
        
        submitForm(1, '', 0);

      });

    $('resource_type').addEvent('change', function()
    {
      if($('validation_resource_type')){
        document.getElementById("resource_type-element").removeChild($('validation_resource_type'));
      }

    });

    $('resource_id').addEvent('change', function()
    {
      if($('validation_resource_id')){
        document.getElementById("resource_id-element").removeChild($('validation_resource_id'));
      }

    });

     $('enable_end_date').addEvent('click', function()
     {
         enableEndDate();
     });

    $('name').addEvent('keyup', function()
    {
      if($('validation_name')){
        document.getElementById("name-element").removeChild($('validation_name'));
      }

    }); 
  });

  function showPreview(){


    $('preview').innerHTML=$('createPrivew').innerHTML;
    $('campaign_name_preview').innerHTML=$('campaign_name').value;
    $('resource_id_content').innerHTML = $('name').value;
    var resource_type_text='';
    for(var i=0; i< $('resource_type').options.length;i++){
      if($('resource_type').options[i].value == $('resource_type').value){
        resource_type_text= $('resource_type').options[i].text;
        break;
      }

    }
    $('resource_type_preview').innerHTML=resource_type_text;

    var resource_id_text='';
    for(var i=0; i< $('resource_id').options.length;i++){
      if($('resource_id').options[i].value == $('resource_id').value){
        resource_id_text= $('resource_id').options[i].text;
        break;
      }

    }
    if($('resource_id_preview')) {
      $('resource_id_preview').innerHTML=resource_id_text;
    }
    
    $('ad_start_date').innerHTML =' <?php   $labelDate = new Zend_Date();
						  $oldTz = date_default_timezone_get();
             date_default_timezone_set($this->viewer()->timezone);
             $start_date=strtotime($this->formField->cads_start_date->getValue());
             date_default_timezone_set($oldTz);
						$start_date= $this->locale()->toDateTime($labelDate->set($start_date), array('size' => 'long'));
						$start_date_array=explode(' ',$start_date);
						unset($start_date_array[count($start_date_array) -1]);
						echo implode(" ",$start_date_array);
//echo date('M d, Y h:i A',strtotime($this->formField->cads_start_date->getValue())) ?>';
  $('ad_end_date').innerHTML = '<?php $enableEndDate=$this->formField->enable_end_date->getValue(); if(empty($enableEndDate)): 
		$labelDate = new Zend_Date();
		 $oldTz = date_default_timezone_get();
              date_default_timezone_set($this->viewer()->timezone);
              $end_date=strtotime($this->formField->cads_end_date->getValue());
             date_default_timezone_set($oldTz);
		$end_date= $this->locale()->toDateTime($labelDate->set($end_date), array('size' => 'long'));
		$end_date_array=explode(' ',$end_date);
							unset($end_date_array[count($end_date_array)-1]);
						echo implode(" ",$end_date_array);
		//echo date('M d, Y h:i A',strtotime($this->formField->cads_end_date->getValue())); 
		else: echo $this->string()->escapeJavascript($this->translate("This ad will run continuously from starting date till it expires.")); endif; ?>';
    
    setTargetPrivew();
  }


  function createSlideContent() {
    slideContent( $('resource_type').value, $('resource_id').value, 1 )
  }


  function slideContent(resource_type, resource_id, storyType){

    slideCreateDiv.slideIn();
    slidePreviewDiv.slideOut();

    if( resource_type == '' ) {
      module_type = $('resource_type').value;
    }else {
      module_type = resource_type;
    }

    if( resource_id == '' ) {
      module_id = $('resource_id').value;
    }else {
      module_id = resource_id;
    }

   // if( !$('resource_type').value && ! $('resource_id').value ) {
      en4.core.request.send(new Request.JSON({
        url : en4.core.baseUrl + 'communityad/display/contenttype?resource_type=' + module_type + '&story_type=' + storyType + '&calling_from=edit&resource_id=' + '<?php echo $this->resource_id; ?>',
        data : {
          format : 'json'
        },
        onSuccess : function(responseJSON) {
          storyType_id = storyType;
          var  subcatss = responseJSON.resource_string;
          addOption($('resource_id')," ", '0');
          for (i=0; i< subcatss.length; i++) {
            addOption($('resource_id'), subcatss[i]['title'], subcatss[i]['id']);
          }
          $('resource_id').value = module_id;
          $('resource_id-wrapper').style.display = 'block';
	   $('name-wrapper').style.display = 'block';
        }
      }));
    //}
  }

  function submitForm(mode, resource_type, resource_id){
    $('mode').value=mode;
    if( resource_type != '' && resource_id != 0 ) {
      $('temp_resource_type').value = resource_type;
      $('temp_resource_id').value = resource_id;
    }
    $('create_sponsoredstory_form').submit();
  }

  function checkValidation(){

    var validationFlage=0;


    if ($('resource_type').value == 0 )
    {
      if(!$('validation_resource_type')){
        var div_resource_type = document.getElementById("resource_type-element");
        var myElement = new Element("p");
        myElement.innerHTML = "<?php echo $this->string()->escapeJavascript($this->translate("Please choose a content type.")) ?>";
        myElement.addClass("error");
        myElement.id = "validation_resource_type";
        div_resource_type.appendChild(myElement);

      }
      validationFlage=1;
    }

    if ($('resource_type').value != 0 && $('resource_id').value == 0 )
    {
      if(!$('validation_resource_id')){
        var div_resource_id = document.getElementById("resource_id-element");
        var myElement = new Element("p");
        myElement.innerHTML = "<?php echo $this->string()->escapeJavascript($this->translate("Please choose a content.")) ?>";
        myElement.addClass("error");
        myElement.id = "validation_resource_id";
        div_resource_id.appendChild(myElement);

      }
      validationFlage=1;
    }
    
    if($('name').value==''){
      if(!$('validation_name')){
        var div_name = document.getElementById("name-element");
        var myElement = new Element("p");
        myElement.innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("Please enter a title for your sponsored story.")) ?>';
        myElement.addClass("error");
        myElement.id = "validation_name";
        div_name.appendChild(myElement);
      }
    validationFlage=1;

    }  

    if(validationFlage==1){
      return false;
    }

    return true;

  }

</script>