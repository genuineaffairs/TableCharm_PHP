<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _partialSposoredStoryCreate.tpl  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript"> 
  var TempSposoredStoryCreate = 0;
  window.addEvent('domready', function() {    
    $('name-wrapper').style.display = 'none';
  });
</script>

<div id="resource_id_backgroundimage" style="display: none;"> 
  <div class="form-wrapper"><div class="form-label">&nbsp;</div>
    <div class="form-element"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/spinner.gif" /></div>
  </div>
</div>
<div id="resource_id-wrapper" class="form-wrapper" style="display: none;"> 
  <div id="resource_id-label" class="form-label">
    <label for="resource_id" class="optional" id="resource_id-label-tag"><?php echo $this->string()->escapeJavascript($this->translate("Select Content")) ?></label>
  </div>
  <div id="resource_id-element" class="form-element">
    <select name="resource_id" id="resource_id" onchange="resourceData(this.value)">
	</select>
  </div>
</div>

<script type="text/javascript">

  function getStory( story_type, flag ) {
	if( !flag ) {
	  getResource(0, story_type); // If there are
	}
	storyType_id = story_type;
	if( story_type == 2 ) {
	  $('story_main_title_str').innerHTML = '<b> <a href="javascript:void(0);">' + story_main_title + '</a></b> → <b> <a href="javascript:void(0);">Story Title</a>: </b> Here we are defined the example description, which will be post of content which done by content owner. comming Soon';
	  $('story_content_photo').style.display = 'none';
	  $('story_content_title_div').style.display = 'none';
	  $('cmad_show_post_wrapper').style.display = 'block';
	}else if( story_type == 1 ) {
	  $('story_content_photo').style.display = 'block';
	  $('story_content_title_div').style.display = 'block';

	  $('story_content_photo').innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/blankImage.png" style="width: 48px; height: 48px;" />';
	  $('story_content_title_div').innerHTML = '<span><a href="javascript:void(0);" id="story_content_title">Item Title</a></span>';

	  $('cmad_show_post_wrapper').style.display = 'none';
	  $('story_main_title_str').innerHTML = '<b><a href="javascript:void(0);">' + story_main_title + '</a></b> likes <a href="javascript:void(0);">Item Title</a>';
	}
  }

  var getResource = function( module_type, story_type )
  {
	$('name-wrapper').style.display = 'none';

	var getURL = '';
	// If user select "--Select--" from the drop down then "Preview" should be reset.
	if( module_type == 0 ) {
	  getStory(story_type, 1);
	}
    // story_type: We are using story_type in this function because we will not show content in drop down if they already exist in data base.
	storyType_id = story_type;
    if( $('validation_subtitle') ) {
      $('resource_id-element').removeChild($('validation_subtitle'));
    }
    
    $('resource_id-wrapper').style.display='none';    
    if( module_type != 0 ) {
      $('resource_id_backgroundimage').style.display='';
	  if( $('editFlag') ) {
		getURL = en4.core.baseUrl + 'communityad/display/contenttype?resource_type=' + module_type + '&story_type=' + story_type + '&calling_from=edit&resource_id=' + $('editFlag').value;
	  }else {
		getURL = en4.core.baseUrl + 'communityad/display/contenttype?resource_type=' + module_type + '&story_type=' + story_type;
	  }

      en4.core.request.send(new Request.JSON({
        url : getURL,
        data : {
          format : 'json'
        },
        onSuccess : function(responseJSON) {				
          clear('resource_id');
       
          $('resource_id_backgroundimage').style.display='none';

          $('resource_id-wrapper').style.display='';
        
          var  subcatss = responseJSON.resource_string;         
          
          if( subcatss == '' ) {
            $('resource_id').style.display = 'none';      
            $('resource_id-label').style.display = 'block';
            $('resource_id-label-tag').innerHTML="";
            if($('validation_subtitle')) {
			  $('story_main_photo').style.display = 'none';
			  $('story_main_title_str').style.display = 'none';
			  $('cmad_show_tooltip_wrapper').style.display = 'none';
			  $('story_content_photo').style.display = 'none';
			  $('story_content_title_div').style.display = 'none';
			  $('story_help_and_lernmore').style.display = 'block';
	            	
              //$('validation_subtitle').innerHTML ="<?php //echo $this->string()->escapeJavascript($this->translate('You have not created any content of this type.'));  ?>";
            }else {
			  var main_temp_link = '<?php echo $this->url(array('page_id' => 0), 'communityad_help', true); ?>';
			  var temp_link = '<?php echo $this->url(array('page_id' => 100), 'communityad_help', true); ?>';
			  $('story_main_photo').style.display = 'block';
			  $('story_content_photo').style.display = 'block';

			  $('story_main_title_str').style.display = 'block';
			  $('story_main_title_str').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("Currently we are unable to display the preview for this sponsored story.")) ?>';
			  $('story_content_title_div').style.display = 'block';
			  $('story_content_title_div').innerHTML = '<a href="' + main_temp_link + '"><?php echo $this->string()->escapeJavascript($this->translate("- Help & Learn More")) ?></a> <br /> <a href="' + temp_link + '"><?php echo $this->string()->escapeJavascript($this->translate("- Sponsored Stories FAQ")) ?></a>';
		    
			  $('story_content_photo').innerHTML = '<a id="story_content_title" href="javascript:void(0);"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/blankImage.png" alt="" /></a><div class="cmad_show_tooltip"><img src="./application/modules/Communityad/externals/images/tooltip_arrow.png" /><?php echo $this->translate("_sponsored_content_title_tooltip"); ?></div>';

			  $('cmad_show_tooltip_wrapper').style.display = 'none';
			  $('story_help_and_lernmore').style.display = 'none';
            }
            resetcontent();

	    var div_resource_type = document.getElementById("resource_type-element");
	    var myElement = new Element("p");
	    myElement.innerHTML = "<br /><?php echo $this->string()->escapeJavascript($this->translate("You have not created any ")) ?>" + responseJSON.modTitle +  "<?php echo $this->string()->escapeJavascript($this->translate(" that can be advertised.")) ?>";
	    myElement.addClass("error");
	    myElement.id = "validation_resource_type";
	    div_resource_type.appendChild(myElement);

          }else {
			$('story_main_photo').style.display = 'block';
			$('story_main_title_str').style.display = 'block';
			$('story_content_photo').style.display = 'block';
			$('story_content_title_div').style.display = 'block';
			$('story_help_and_lernmore').style.display = 'none';

            $('resource_id-label-tag').innerHTML="<?php echo $this->string()->escapeJavascript($this->translate("Select Content")) ?>";
            addOption($('resource_id')," ", '0');
            for (i=0; i< subcatss.length; i++) {
              addOption($('resource_id'), subcatss[i]['title'], subcatss[i]['id']);
            }		
          }
	
        }
      }));
    }else{
      resetcontent();
    }
    
  };
  
  function resetcontent()
  {
    $('resource_type').value = 0;
  }
  function clear(ddName)
  {
    for (var i = (document.getElementById(ddName).options.length-1); i >= 0; i--) 
    { 
      document.getElementById(ddName).options[ i ]=null; 
    } 
  }
  
  function addOption( selectbox, text, value )
  {
    var optn = document.createElement("OPTION");
    optn.text = text;
    optn.value = value;
    if( TempSposoredStoryCreate == value ) {
      optn.selected = 'selected';
    }

    if(optn.text != '' && optn.value != '') {
      $('resource_id').style.display = 'block';
      $('resource_id-label').style.display = 'block';
      selectbox.options.add(optn);
    } else {
      $('resource_id').style.display = 'none';
      $('resource_id-label').style.display = 'none';
      selectbox.options.add(optn);
    }
  }



  var resourceData = function( resource_id )
  {
	$('cmad_show_tooltip_wrapper').style.display = 'block';
	$('name-wrapper').style.display = 'none';  
	// Condition: If selection from 'Select Content' drop down is empty then reset all values.
	if( resource_id != 0 )
	{
	  // When select any content from drop down then show loder image in preview side.
	  if( $('story_content_title') ) {
		$('story_content_title').innerHTML='<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/loader.gif" alt=""></center>';
	  }
	  if( storyType_id != 2 ) {
		$('story_content_photo').innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/blankImage.png" alt=" " />';
		$('story_content_photo').style.display='block';
	  }
    
	  // 			$('remove_image_link').style.display='block';
	  var resource_type = $('resource_type').value;
	  en4.core.request.send(new Request.JSON({
		url : en4.core.baseUrl + 'communityad/display/resourcecontent?resource_type=' + resource_type + '&resource_id=' + resource_id + '&is_spocerdStory=' + storyType_id,
		data : {
		  format : 'json'
		},
		onSuccess : function(responseJSON) {

		  $('story_main_title_str').innerHTML = responseJSON.main_div_title;
		  $('like_button').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("Like This ")) ?>' + responseJSON.modTitle;

		  if( responseJSON.preview_title && responseJSON.photo ) {
			if( $('story_content_title') ) {
			  $('story_content_title').style.display = 'block';
			}
			$('story_content_photo').style.display = 'block';
			$('name-wrapper').style.display = 'block';
			if( $('story_content_title') ){ $('story_content_title').innerHTML = responseJSON.preview_title; // set responce title for preview title.
			}else {
			  $('story_content_title_div').innerHTML =  '<a href="javascript:void(0);" id="story_content_title">' + responseJSON.preview_title + '</a><div class="cmad_show_tooltip"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/tooltip_arrow.png" /><?php echo $this->translate("_sponsored_content_title_tooltip"); ?></div>';
			}
			$('story_content_photo').innerHTML = responseJSON.photo; // set responce photo for preview photo.
			if( $('editTitle').value != ''  ){
			  $('name').value = $('editTitle').value;
			  $('editTitle').value = '';
			}else {
			  $('name').value = responseJSON.temp_pre_title;
			  if($('validation_name')){
				document.getElementById("name-element").removeChild($('validation_name'));
			  }
			}
			if( $('profile_address') ) {
			  $('profile_address').innerHTML = responseJSON.remaning_title_text;
			}
		  }else {
			$('story_content_title').style.display = 'none';
			$('story_content_photo').style.display = 'none';
			$('name').value = '';
		  }

		  if( responseJSON.footer_comment ) {
			$('cmad_show_post_wrapper').style.display = 'block';
			$('story_content_title').innerHTML = responseJSON.footer_comment;
		  }else {
			$('cmad_show_post_wrapper').style.display = 'none';
		  }
		}
	  }));
	}else {
	  resetcontent();
	}
  }
</script>