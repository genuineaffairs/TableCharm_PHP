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
<?php include_once APPLICATION_PATH . '/application/modules/Communityad/views/scripts/_partialCreate.tpl'; ?>
<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'externals/calendar/styles.css');
?>
<script type="text/javascript">
function cancel(){
   url='<?php echo $this->url(array('ad_id' => $this->userAds_id), 'communityad_userad', true) ?>';

  parent.window.location.href=url;
}
</script>
<?php if($this->package->type=='sponsored_stories'):?>
<?php include_once APPLICATION_PATH . '/application/modules/Communityad/views/scripts/sponsored-story/edit.tpl'; ?>
<?php else: ?>
<?php  if( !empty($this->is_customAs_enabled) && !empty($this->is_moduleAds_enabled) ) { ?>
	<style type="text/css">
		#create_feature-wrapper,
		#cads_url-wrapper
		{
			overflow:visible;
			margin-top:20px;
		}
		#create_feature-wrapper .form-element,
		#cads_url-wrapper .form-element
		{
			margin-top:-20px;
			overflow:auto;
		}
		#create_feature-wrapper .form-element a,
		#cads_url-wrapper .form-element a
		{
			margin-bottom:10px;
			float:left;
		}
	</style>
<?php } ?>

<script type="text/javascript">

 <?php if(empty($this->photoDisplay)):?>
         var is_image  = '<?php echo $this->itemPhoto($this->userAds, '', ''); ?>';
      <?php else:?>
    var is_image  = '<?php echo $this->photoDisplay ?>';
      <?php endif; ?>
var is_edit_image =  '<?php echo $this->is_photo_id; ?>';
function contenttype(contentKey) {
}

window.addEvent('domready', function() {
		var userStr;
		var titleStr;
		titleStr = "<?php echo $this->viewer->getTitle(); ?>";
		titleStr = titleStr.replace("'", "\'");
		userStr = '<?php echo '<div class="cmaddis_cont"><a href="javascript:void(0);" class="cmad_like_button"><i class="like_thumbup_icon"></i><span>'. $this->string()->escapeJavascript($this->translate('Like')). '</span></a><span class="cmad_like_un">&nbsp;&middot;&nbsp;<a href="javascript:void(0);">' ?>' + titleStr + '<?php echo '</a>' .$this->string()->escapeJavascript( $this->translate(' likes this.') ). '</span></div>' ?>';
		$('rw_like').innerHTML = userStr;
		$('ad_like').innerHTML = $('rw_like').innerHTML ;
		$('ad_like').style.display = 'none';
		$('rw_like').style.display = 'none';
    //var subcatss = Array();
		if( $('like').value != 0 ) {
			$('name').addClass('disabled_title');
			var subcatss = '<?php echo $this->edit_sub_title; ?>' . split("::");
			addOption($('title')," ", '0');
				for (var i=0; i < subcatss.length;++i){
					var subcatsss = subcatss[i].split("_");
					addOption($('title'), subcatsss[0], subcatsss[1]);
				}
				$('title').value = '<?php echo $this->resource_id ?>';
			}
			
	});


  function imposeMaxLength(Object, MaxLen)
  { 
    return (Object.value.length <= MaxLen);
  }

 function removeImage(){
    $m('ad_photo').innerHTML='<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/blankImage.png" />';
    $('imageName').value='';
    $('image').value='';
    $('imageenable').value=0;
    $("remove_image_link").style.display='none';
  }

  window.addEvent('domready', function()
  {
    var title_count;
    var body_count;
     $("remove_image_link").style.display='block';
    //for title
		if( $('profile_address') ) {
			$('profile_address').innerHTML = $('profile_address').innerHTML= '<span id="profile_address_text"><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.title', 25)); ?></span>';
		}
    //for body
    $('profile_address1').innerHTML = $('profile_address1').innerHTML = '<span id="profile_address_text1"><?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.body', 135)); ?></span>';

    //for title
    $('name').addEvent('keyup', function()
    {
     nameTextLimt(this);
    });

    $('name').addEvent('blur', function()
    {
     nameTextLimt(this);
    });


   

      $('campaign_name').addEvent('keyup', function()
     {
      if($('validation_campaign_name')){
         document.getElementById("campaign_name-element").removeChild($('validation_campaign_name'));
      }

    });

    $('enable_end_date').addEvent('click', function()
    {
     enableEndDate();
    });
     $('cads_url').addEvent('keyup', function()
    {
      if($('validation_cads_url')){
         document.getElementById("cads_url-element").removeChild($('validation_cads_url'));
      }

    });

    //  trigger on page-load
    if ($('name').value.length)
      $('name').fireEvent('keyup');
  
    //for body
    $('cads_body').addEvent('keyup', function()
    {
      var maxSize= <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.body', 135); ?>;
      var   text = this.value;
        body_count=maxSize-text.length;
       
      if(body_count>=0)
      {
        $('profile_address_text1').innerHTML = body_count;
      }
      else
      {
        $('profile_address_text1').innerHTML="0";
      }
    });

    $('cads_body').addEvent('blur', function()
    {
      var maxSize= <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.body', 135); ?>;
      var   text = this.value;
      body_count=maxSize-text.length;

      if(body_count>=0)
      {
        $('profile_address_text1').innerHTML = body_count;
      }
      else
      {
        $('profile_address_text1').innerHTML="0";
      }
    });
    // trigger on page-load
    if ($('cads_body').value.length)
      $('cads_body').fireEvent('keyup');
  }
);

 function nameTextLimt(thisName){
      var maxTitle=<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.title', 25) ?>;
      var text = thisName.value;
      title_count=maxTitle-text.length;
			if( $('profile_address_text') ) {
				if(title_count>=0)
				{
					$('profile_address_text').innerHTML = title_count;
				}
				else
				{
					$('profile_address_text').innerHTML="0";
				}
			}
    }
  window.addEvent('domready', function() {

    <?php if(empty ($this->copy )):?>
     $('campaign_id').set('class', 'disabled_title');
     <?php endif; ?>

    $('imageName').value='<?php echo $this->photoName ?>';
    var targetDivElement = $('targetdiv');
    var slideTargetDiv = new Fx.Slide(targetDivElement, {
      duration: 600,
      resetHeight:true
    });
    var titleBodyDivElement = $('titlebodydiv');
    var slideTitleBodyDiv = new Fx.Slide(titleBodyDivElement, {
      duration: 600,
       resetHeight:true
    });

    var reviewDivElement = $('reviewdiv');
    var slideReviewDiv = new Fx.Slide(reviewDivElement, {
      duration: 600,
      resetHeight:true
    });

    $('continue_target').addEvent('click', function(){ 

   var flage=checkValidation();

      if(!flage)
        return flage;
      profileFields(<?php echo  $this->profileSelect_id ?>);
      slideTargetDiv.slideIn();
    });

     $('continue_1').style.display='block';

     $('continue').addEvent('click', function(){
        var flage=checkValidation();

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

        $('wholeform').submit();

      });
      
   
    <?php if (!$this->mode): ?>
      $('continue_1').style.display='none';     
      slideTargetDiv.hide();
      slideTitleBodyDiv.hide();
      setReviewData();
      //slideReviewDiv.slideIn();

      <?php else:?>
        slideReviewDiv.hide();
       $('imageenable').value=1;
      if( is_edit_image != 0 ) {
        $('ad_photo').innerHTML = '<?php echo $this->itemPhoto($this->userAds, '', '' , array()); ?>';
      } else {
        $('ad_photo').innerHTML = $('resource_image').value;
      }
   
      $('ad_title').innerHTML ='<a href="javascript:void(0);">'+$('name').value+'</a>';
      $('ad_body').innerHTML = $('cads_body').value;
      $("remove_image_link").style.display='block';
    <?php endif; ?>

 
      $('continue_review').addEvent('click', function(){

      var flage=checkValidation();

      if ($('cads_end_date-date').value == "" && !$m("enable_end_date").checked )
      {
      if(!$('validation_image')){
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

        $('wholeform').submit();

      });

   
    if( $('like').value == 0 ) {
			$('cads_url-wrapper').style.display = 'block';
			$('create_feature-wrapper').style.display = 'none';
			$('title-wrapper').style.display = 'none';
			$('name').disabled = '';
		}else {
			$('name').disabled = 'disabled';
		}
    profileFields(<?php echo  $this->profileSelect_id ?>);
   $('continue_target-wrapper').style.display='none';
   $('continue_review-wrapper').style.display='none';
});

function setReviewData() {
 $('reviewdiv').style.display='block';
 $('continue_1').style.display='none';
 $('creatediv').set('class', "cmad_crbn");

	// Condition: value would be 0 only if currect ad is 'custom type'
	if( $('preview_body').value == 0 ) {
		$('rw_body').innerHTML = '<a href="javascript:void(0);" >' + $('cads_body').value + '</a>';
	}else {
		$('rw_body').innerHTML = $('preview_body').value;
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

	// Condition: value would be 0 only if currect ad is 'custom type'
	if( $('preview_title').value == 0 ) {
		$('rw_title').innerHTML = '<a href="javascript:void(0);" >' + $('name').value + '</a>';
	}else {
		$('ad_like').style.display = 'block';
		$('rw_like').style.display = 'block';
    if($('preview_title').value == '')
    $('rw_title').innerHTML = $('name').value;
      else
    $('rw_title').innerHTML = $('preview_title').value;
	}

   if( $('like').value == 0 ) {
			$('cads_url-wrapper').style.display = 'block';
			$('create_feature-wrapper').style.display = 'none';
			$('title-wrapper').style.display = 'none';
			$('name').disabled = '';
		}else {
			$('name').disabled = 'disabled';
			$('name').addClass('disabled_title');
			$('ad_body').innerHTML = $('cads_body').innerHTML;
			$('rw_body').innerHTML = $('cads_body').innerHTML;
		}

 
    <?php if (!$this->mode): ?>		
					var ad_upload_image = ''; 
					if($('resource_image').value == '') {
						ad_upload_image = is_image;
					} else {
             var is_image_upload  = '<?php echo $this->photoDisplay ?>';
            if(is_image_upload=='')
						ad_upload_image = $('resource_image').value;
            else
             ad_upload_image = is_image_upload;
					}
					$('rw_photo').innerHTML = ad_upload_image;
         
         $('mode').value=0;
          setTargetPrivew();
    <?php endif; ?>
}

 function placeOrder(){

   $('wholeform').submit();
 }

function editOrder(){
      profileFields(<?php echo  $this->profileSelect_id ?>);
if( $('like').value != 0 ) {
	$('name').addClass('disabled_title');
	$('create_feature-wrapper').style.display = 'block';	
	var module_type = $('resource_type').value;
	var module_id = $('resource_id').value;
	en4.core.request.send(new Request.JSON({
		url : en4.core.baseUrl + 'communityad/display/contenttype?resource_type=' + module_type,
		data : {
			format : 'json'
		},
		onSuccess : function(responseJSON) {
			$('subcategory_backgroundimage').style.display = 'none';
			clear('title');
			var  subcatss = responseJSON.resource_string;
			addOption($('title')," ", '0');
			for (i=0; i< subcatss.length; i++) {
				addOption($('title'), subcatss[i]['title'], subcatss[i]['id']);
			}
			$('title').value = module_id;
			$('title-wrapper').style.display = 'block';
		}		
	}));
	$('ad_body').innerHTML = $('cads_body').innerHTML;
	$('rw_body').innerHTML = $('cads_body').innerHTML;
}else {
	$('create_feature-wrapper').style.display = 'none';
	$('title-wrapper').style.display = 'none';
}

    if($('like').value != 0) {

      $('name').disabled = 'disabled';
			if($('resource_image').value == "") {
				$('ad_photo').innerHTML = is_image;
			}else {
          var is_image_upload  = '<?php echo $this->photoDisplay ?>';
            if(is_image_upload=='')
						$('ad_photo').innerHTML= $('resource_image').value;
            else
            $('ad_photo').innerHTML = is_image_upload;
				
			}
      $('cads_url-wrapper').style.display = 'none';
			$('rw_like').style.display = 'block';
			$('ad_like').style.display = 'block';
    }	else {
      $('ad_photo').innerHTML = is_image;
			if( $('create_feature-wrapper') ) {
				$('create_feature-wrapper').style.display = 'none';
			}
			if( $('title-wrapper-wrapper') ) {
				$('title-wrapper').style.display = 'none';
			}
    }
		// Condition: value would be 0 only if currect ad is 'custom type'
		if( $('preview_title').value == 0 ) {
			$('ad_title').innerHTML = '<a href="javascript:void(0);" >' + $('name').value + '</a>';
		}else {
			$('ad_title').innerHTML = $('preview_title').value;
		}
		// Condition: value would be 0 only if currect ad is 'custom type'
		if( $('preview_body').value == 0 ) {
			$('ad_body').innerHTML = '<a href="javascript:void(0);" >' + $('cads_body').value + '</a>';
		}
    $m('creatediv').set('class', "cmad_crb");

    $("remove_image_link").style.display='block';
    var targetDivElement = $('targetdiv');

    var slideTargetDiv = new Fx.Slide(targetDivElement, {
    duration: 600
    }).hide();

    var titleBodyDivElement = $('titlebodydiv');
    var slideTitleBodyDiv = new Fx.Slide(titleBodyDivElement, {
    duration: 600
    });

    var reviewDivElement = $('reviewdiv');
    var slideReviewDiv = new Fx.Slide(reviewDivElement, {
    duration: 600
    }).hide();
  
   
    slideTitleBodyDiv.slideIn();
    slideTargetDiv.slideIn();
    profileFields(<?php echo  $this->profileSelect_id ?>);
    slideReviewDiv.slideOut();


    $('mode').value=1;

    $('continue_1').style.display='block';
    $('continue_target-wrapper').style.display='none';
    $('continue_review-wrapper').style.display='none';
    
}
</script>

<?php if( empty($this->communityad_package_info) ){ return; } ?>
<?php if(!$this->copy):?>
	<h3 style="margin-bottom:10px;"> 
	<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/ad-icon.png" alt="" class="ad_icon" />
<?php  echo $this->translate("Edit Your Ad") ?></h3>
<?php else:?>
	<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/ad-icon.png" alt="" class="ad_icon" /> 
	<h3 style="margin-bottom:6px;"> <?php  echo $this->translate("Advertise on "). Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title') ?></h3>
<?php endif;?>

    <?php if(!$this->notshowapprovedMessage && !$this->copy):?>
<div class="cmad_tip tip">
  <span>
    <?php echo $this->translate(" 
Design, targeting, scheduling and package field are pre-populated with your existing ad settings.  After submitting your changes, your ad will stop running untill it has been approved by Administrator."); ?>
    </span>
</div>
    <?php endif; ?>
 
 
<div class="layout_middle">
  <div id="creatediv" class="create cmad_create" >
	   <fieldset>
      <form id="wholeform" method="post" >
		     <div id ="titlebodydiv" class="cmad_ad_steps">
          <h4 class="fleft"><?php echo $this->translate("2.")." ".$this->form->getTitle(); ?> </h4>
				<?php if(!empty($this->design_faq)) : ?>
					<h4 class="fright"><a href='<?php echo $this->url(array('display_faq' => 2), 'communityad_help_and_learnmore', true) ?>' target='_blank'><?php echo $this->translate('Design Your Ad FAQ')?></a></h4>
				<?php endif; ?>
         <div class="global_form" >
           <div>
             <div>
              <div class="cmad_form_left">

                <div class="form-elements">
                  <?php
                  $elementTitleForm = $this->form->getElements();
                  foreach ($elementTitleForm as $key => $value) {
                    if ($key != 'current')
                      echo $this->form->$key;
                  }
                  ?>
                </div>
            </div>
            <!--Ad Preview Start here-->
            <?php echo $this->form->current; ?>
                <!--Ad Preview End here-->

             </div>
            </div>
          </div>
         </div>
        <div id="list"></div>
        <div id = "targetdiv" class="slide cmad_ad_steps">
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
                  //    echo $this->formField->render($this);
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
                    echo $this->formField->continue_review;
                  ?>
                </div>
              </div>
            </div>
          </div>       
          <div id="continue_1" style="margin-left:20px;" > <div style="float: left;"><?php echo $this->formField->continue; ?></div>
          <div style="margin-top: 25px;margin-left: 5px; float: left;" > <?php echo $this->translate(" or ") ?><a href="javascript:void(0);" onclick= "cancel()"><?php echo $this->translate("Cancel") ?></a> </div>
          </div>
       </div>
      </form>      
    </fieldset>
  </div>

	<!--Review Ad Content Start here-->
	<div class="cmad_acrad_wrapper">
    <div id="reviewdiv" class ="slide cmad_ad_steps" style="display: none;">
  	<h4 class="fleft"><?php echo "4. ". $this->translate("Review Your Ad") ?></h4>
  	<h4 class="fright"><a href='<?php echo $this->url(array(), 'communityad_help_and_learnmore', true) ?>' target='_blank'><?php echo $this->translate('Help Center')?></a></h4>
    <div class="global_form">
			<div>
				<div>
		    	<input type="hidden" name="name" value="value1" />
		    	<h3></h3>         
          <p>
             <?php echo $this->translate("Please check your ad for accuracy before placing your order.")?>
          </p>
          <div class ="form-elements">
	          <div class="form-wrapper">
						  <div class="form-label">
						    <label><?php echo $this->translate("Ad Preview:"); ?></label>
						  </div>
						  <div class="form-element">
								<!--Ad Preview Start here-->
								<div class="cadcp_preview" id="cadcp_preview">
					       	<div class="cmaddis">
								 		<div class="cmad_addis">
								    	<div class="cmad_show_tooltip_wrapper">
									 			<div class='cmaddis_title' id ="rw_title">
													
												</div>
									    	<div class="cmad_show_tooltip">
													<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/tooltip_arrow.png" />
									      	<?php echo $this->translate("Ad title linked to the ad destination URL.");?>
									      </div>
											</div>
								      <div class="cmad_show_tooltip_wrapper">
												<div class="cmaddis_image" id="rw_photo">
												</div>
								      	<div class="cmad_show_tooltip">
								      		<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/tooltip_arrow.png" />
								      		<?php echo $this->translate("Ad image linked to the ad destination URL.");?>
								      	</div>
				              </div>
											</div>
								      <div class="cmad_show_tooltip_wrapper">
									 			<div class="cmaddis_body" id = "rw_body">
									 				
									 			</div>
								      	<div class="cmad_show_tooltip">
													<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/tooltip_arrow.png" />
								      		<?php echo $this->translate("Ad body text linked to the ad destination URL.");?>
								      	</div>
									 		</div>
				              <div class="cmad_show_tooltip_wrapper">
												<div id="rw_like"> </div>
												<div class="cmad_show_tooltip">
													<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/tooltip_arrow.png" />
								      		<?php echo $this->translate("Viewers will be able to like this ad and its content. They will also be able to see how many people like this ad, and which friends like this ad.");?>
								      	</div>
								      </div>
										</div>
									</div>
								</div>		
								<!--Ad Preview End here-->    
						  </div>
						</div>
          
	          <div class="form-wrapper">
						  <div class="form-label">
						    <label><?php echo $this->form->package_name->getLabel().":"; ?></label>
						  </div>
						  <div class="form-element">
						  	<?php echo $this->form->package_name->getDescription(); ?>
						  </div>
						</div>
	         <div class="form-wrapper">
              <div class="form-label">
                <label><?php echo $this->translate('Quantity:'); ?></label>
              </div>
              <div class="form-element">
                 <?php  switch ($this->package->price_model):
	                 case "Pay/view":
                     if($this->package->model_detail != -1):echo $this->translate(array('%s View', '%s Views', $this->package->model_detail), $this->locale()->toNumber($this->package->model_detail)); else: echo $this->translate('UNLIMITED Views'); endif ;

                     break;

                  case "Pay/click":
                     if($this->package->model_detail != -1):echo $this->translate(array('%s Click', '%s Clicks', $this->package->model_detail), $this->locale()->toNumber($this->package->model_detail)); else: echo $this->translate('UNLIMITED Clicks'); endif ;

                     break;

                  case "Pay/period":
                     if($this->package->model_detail != -1):echo $this->translate(array('%s Day', '%s Days', $this->package->model_detail), $this->locale()->toNumber($this->package->model_detail)); else: echo $this->translate('UNLIMITED  Days'); endif ;

                  break;
                 endswitch;?>
              </div>
            </div>
	          <div class="form-wrapper">
						  <div class="form-label">
						    <label><?php echo $this->form->campaign_name->getLabel().":"; ?></label>
						  </div>
						  <div class="form-element">
						  	<?php echo $this->form->campaign_name->getValue(); ?>      
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
	        
	          <?php if(!empty($this->enableTarget)): ?>
	          <span id="targetDetails_hedding" style="display:none;">
	            <h5><?php echo $this->translate('Targeting') ?></h5></span>
	          <?php endif;?>
	          <p style="width:100%;max-width: 100%;"><div id="tdata"></div>
            <?php if(!empty($this->birthday_enable)):?>
            <div>
            <div id="birthday_enable_span"></div>
            </div>
            <?php endif;?></p>
	          <div class="form-wrapper">
						  <div class="form-label">
						    <label>&nbsp;</label>
						  </div>
						  <div class="form-element">
                <?php if(empty($this->copy)):?>
						  	  <button name = "saveOrder" onclick="placeOrder()"><?php echo $this->translate("Save Changes") ?></button>
                  <?php else: ?>
                    <button name = "saveOrder" onclick="placeOrder()"><?php if(!$this->package->isFree()): echo $this->translate("Place Order");  else: echo $this->translate('Create Ad'); endif; ?></button>
                  <?php endif; ?>
	    						<button name = "editad"  onclick= "editOrder()" ><?php echo $this->translate("Edit Ad") ?></button>
                 <button name = "editad"  onclick= "cancel()" ><?php echo $this->translate("Cancel") ?></button>
						  </div>
						</div>
					</div>		
				</div>
			</div>	
	  </div>
	</div>
	</div>
	<!--Review Ad Content End here-->
	

<script type="text/javascript">
var imageName='';

  function imageupload()
  {
     $('imageName').value='';
     $m('photo_id_filepath').value='';
     $('imageenable').value=0;
     $m('ad_photo').innerHTML='';
     $("remove_image_link").style.display='none';
    if($('validation_image')){
       document.getElementById("image-element").removeChild($('validation_image'));
    }
    form = $m('wholeform');
   
    var  url_action= '<?php echo $this->url(array('module' => 'communityad', 'controller' => 'index', 'action' => 'upload'), 'default', true) ?>';

    ajaxUpload(form,
    url_action,
    'ad_photo','<center><img src=\"<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/loader.gif\" border=\'0\' />','');
    $m("loading_image").style.display="block";
        $m("loading_image").innerHTML='<br /><img src=\"<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/loader.gif\" border=\'0\' /> ' + '<?php echo $this->string()->escapeJavascript($this->translate("Uploading image...")) ?>';
   $m("image").style.visibility="Hidden";
    
    return false;
  }
 
  

</script>

<script type="text/javascript">

 function checkValidation(){

    var validationFlage=0;

    var adcampaign = document.getElementById("campaign_id");
    var namewrapper = document.getElementById("campaign_name-wrapper");
    var name = document.getElementById("campaign_name");

    if (adcampaign.value == 0 && name.value=='')
    {
       if(!$('validation_campaign_name')){
      var div_campaign_name = document.getElementById("campaign_name-element");
      var myElement = new Element("p");
      myElement.innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("Please enter a campaign name.")) ?>';
      myElement.addClass("error");
      myElement.id = "validation_campaign_name";
      div_campaign_name.appendChild(myElement);
       }
    validationFlage=1;
    }


     if($('cads_url').value==''){
       if(!$('validation_cads_url')){
     var div_cads_url = document.getElementById("cads_url-element");
      var myElement = new Element("p");
      myElement.innerHTML = "<?php echo $this->string()->escapeJavascript($this->translate('Please enter a valid web page URL for users to visit when clicking on your ad.')) ?>";
      myElement.addClass("error");
      myElement.id = "validation_cads_url";
      div_cads_url.appendChild(myElement);
     }
    validationFlage=1;
     }
    
      if($('like').value == 0 && !isUrl($('cads_url').value) ){
       if(!$('validation_cads_url')){
     var div_cads_url = document.getElementById("cads_url-element");
      var myElement = new Element("p");
      myElement.innerHTML = "<?php echo $this->string()->escapeJavascript($this->translate("Destination URL must be a valid web page.")) ?>";
      myElement.addClass("error");
      myElement.id = "validation_cads_url";
      div_cads_url.appendChild(myElement);
     }
    validationFlage=1;
     }
     if($('name').value==''){
      if(!$('validation_name')){
      var div_name = document.getElementById("name-element");
      var myElement = new Element("p");
      myElement.innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("Please enter a title for your ad.")) ?>';
      myElement.addClass("error");
      myElement.id = "validation_name";
      div_name.appendChild(myElement);
     }
       validationFlage=1;

     }
     if($('cads_body').value==''){
       if(!$('validation_cads_body')){
      var div_cads_body = document.getElementById("cads_body-element");
      var myElement = new Element("p");
      myElement.innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("Please enter a body for your ad.")) ?>';
      myElement.addClass("error");
      myElement.id = "validation_cads_body";
      div_cads_body.appendChild(myElement);
       }
       validationFlage=1;
       
     }

     if ($('imageenable').value == 0 )
    {
        if(!$('validation_image')){
        var div_image = document.getElementById("image-element");
        var myElement = new Element("p");
        myElement.innerHTML = "<?php echo $this->string()->escapeJavascript($this->translate("Please choose an image for your ad.")) ?>";
        myElement.addClass("error");
        myElement.id = "validation_image";
        div_image.appendChild(myElement);
       
      }
      validationFlage=1;
    }

     if(validationFlage==1){
       return false;
     }

      return true;

 }
function isUrl(s) {
        var regexp = /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/

        return regexp.test(s);
}
</script>

<script type="text/javascript">

function copyredirect(){

  if($("copy_ads_list")){
    var url='<?php echo  $this->url(array("copy"=>"copy"),"communityad_copyad",true); ?>'+'/id/'+$("copy_ads_list").value;
    window.location.href=url;
  }
}

</script>
<?php endif; ?>