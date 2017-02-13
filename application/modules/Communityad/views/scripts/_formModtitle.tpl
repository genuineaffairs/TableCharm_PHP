<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formModtitle.tpl 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */


	/*
	Id which are using in this file.

		$('is_custom'): Set when packages allow 'custom ad'.
		$('is_module'): Set when packages allow 'content ad'.
		$('create_feature-wrapper'): Set for 'Modules Selection Box'.
		$('title-wrapper'): Set for 'content selection box', Display after selecting any modules.
		$('cads_url-wrapper'): Set for URL Form Element
		$('name'): Set for Name (title) Form Element
		$('profile_address_text'): Set numbering of 'how much is left' in title.
		$('profile_address_text1'):Set numbering of 'how much is left' in body.
		$('ad_title'): Display title with tooltip on preview.
		$('ad_body'): Display body with tooltip on preview.
		$('ad_photo'): Display photo with tooltip on preview.
		$('ad_like'): Display 'like_button' with tooltip on preview.
		$('rw_title'): Display title with tooltip on review.
		$('rw_body'): Display body with tooltip on review.
		$('rw_photo'): Display photo with tooltip on review.
		$('rw_like'): Display 'like_button' with tooltip on review.
		$('image'): Set image field which would be uploaded.
		$('resource_type'): Set resource type which would be selected.
		$('resource_id'): Set resource id which would be selected.
		$('resource_image'): Set resource image which would be selected.
		$('like'): Set like this eould be selected only in the case of module type.
	*/


echo '
<div id="subcategory_backgroundimage"> </div>
<div id="title-wrapper" class="form-wrapper">
	<div id="title-label" class="form-label">
	 <label for="title" class="optional">'. $this->string()->escapeJavascript($this->translate("Select Content")). '</label>
	</div>
	<div id="title-element" class="form-element">
		<select name="title" id="title" onchange="resourceData(this.value)">
			
		</select>
	</div>
</div>';

	$title = $this->translate('Example Ad Title');
	$body = $this->translate('Example ad body text.');
?>

<script type="text/javascript">

	window.addEvent('domready', function() {

 		var is_custom = $('is_custom').value; // Set this variable if selected ad package allow 'custom ad'.
		var is_module = $('is_module').value; // Set this variable if selected ad package allow 'content ad'.

		// Condition: If set only 'custom' ad allow.
		if( is_custom != 0 && is_module == 0 ) {
			$('create_feature-wrapper').style.display = 'none';
			$('title-wrapper').style.display = 'none';
		}
		
		// Condition: If set only 'content ad' allow.
		if( is_custom == 0 && is_module != 0 ) {
			//$('change_module_1').innerHTML = '';
			$('cads_url-wrapper').style.display = 'none'; 
			$('title-wrapper').style.display = 'none';
			$('name').disabled = 'disabled';
			//$('name-element').getElement('.description').innerHTML = '';
			$('ad_like').style.display = 'block';
		}

		// Condition: If set 'custom ad' & 'content ad' both are allow.
		if( is_custom != 0 && is_module != 0 ) {
			$('create_feature-wrapper').style.display = 'none';
			$('title-wrapper').style.display = 'none';
       if( $('like').value == 0 ) {
				$('name').disabled = '';
      }else {
				$('name').disabled = 'disabled';
			}
		}

		// Condition: 'is_edit' variable set only when ad is edit
		if( $('is_edit').value != 0 ) {
			$('title-wrapper').style.display = 'block';
			$('create_feature-wrapper').style.display = 'block';
			$('cads_url-wrapper').style.display = 'none';
			// Else case apply only for 'Edit Ad'.
			if( $('resource_type') && $('resource_id') ) {
				$('create_feature').value = $('resource_type').value;
			}
			$('ad_title').innerHTML = '' + $('name').value + '<div class="cmaddis_adinfo"><a href="javascript:void(0);"> ' + $('is_edit').value + ' </a></div>' ;

			if( $('like').value != 0 ) {
				$('ad_like').style.display = 'block';
				$('rw_like').style.display = 'block';
			}else {
				$('cads_url-wrapper').style.display = 'block';
			}
		}

		if( is_module == 0 && $('like').value == 0 ) {
			if( $('title-wrapper') ) {
				$('title-wrapper').style.display = 'none';
			}
			if( $('create_feature-wrapper') ) {
				$('create_feature-wrapper').style.display = 'none';
			}
		}
	});

	function changOption(changeFor)
	{
		var title_limit = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.title', 25);?>';
		var body_limit = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.body', 135);?>';

		if( changeFor == 0 ) {
			$('name').addClass('disabled_title');
			$('imageenable').value = 0;
			$('imageName').value = '';
			$('profile_address_text1').innerHTML = body_limit;
			$('profile_address_text').innerHTML = title_limit;
			$('ad_like').style.display = 'block';
			$('rw_like').style.display = 'block';
			$('cads_url-wrapper').style.display = 'none';
			$('create_feature-wrapper').style.display = 'block';
			$('title-wrapper').style.display = 'block';
			$('name').disabled = 'disabled';
			$('create_feature').value = 0;
			$('title-wrapper').style.display = 'none';
			$('cads_body').value = ''; // Set responce description for form description.
			$('name').value = ''; // set responce label for form submit title.
			$('cads_url').value = 'http://'; // set responce page url for form submit url.
			$('ad_title').innerHTML = '<a href="javascript:void(0);">' + '<?php echo $this->string()->escapeJavascript($title) ?>' + '</a>'; // set responce title for preview title.
			$('ad_body').innerHTML = '<?php echo $this->string()->escapeJavascript($body) ?>'; // set responce description for preview body.
			$('ad_photo').innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/blankImage.png" alt=" " />'; // set responce photo for preview photo.
			$('resource_image').value = ''; // set responce for Hidden element - image id
			$('resource_type').value = ''; // set responce for Hidden element - resource_type
			$('resource_id').value = ''; // set responce for Hidden element - resource_id
			$('image').value = '';
			$('remove_image_link').style.display = 'none';
		} else {
			$('name').removeClass('disabled_title');
			$('profile_address_text1').innerHTML = body_limit;
			$('profile_address_text').innerHTML = title_limit;
			$('ad_like').style.display = 'none';
			$('rw_like').style.display = 'none';
			$('preview_title').value = '';
			$('preview_body').value = '';
			$('cads_url-wrapper').style.display = 'block';
			$('name-wrapper').style.display = 'block';
			$('create_feature-wrapper').style.display = 'none';
			$('title-wrapper').style.display = 'none';
			$('name').disabled = '';
			$('like').value = 0;
			$('cads_body').value = ''; // Set responce description for form description.
			$('name').value = ''; // set responce label for form submit title.
			$('cads_url').value = 'http://'; // set responce page url for form submit url.
			$('ad_title').innerHTML = '<a href="javascript:void(0);">' + '<?php echo $this->string()->escapeJavascript($title) ?>' + '</a>'; // set responce title for preview title.
			$('ad_body').innerHTML = '<?php echo $this->string()->escapeJavascript($body) ?>'; // set responce description for preview body.
			$('ad_photo').innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/blankImage.png" alt=" " />'; // set responce photo for preview photo.
			$('resource_image').value = ''; // set responce for Hidden element - image id
			$('resource_type').value = ''; // set responce for Hidden element - resource_type
			$('resource_id').value = ''; // set responce for Hidden element - resource_id
			$('image').value = '';
			$('remove_image_link').style.display = 'none';
		}
	}

	var subcontent = function( module_type )
	{
		if( $('validation_subtitle') ) {
			$('title-element').removeChild($('validation_subtitle'));
		}

		if($('validation_subtitle')) {
			$('validation_subtitle').innerHTML = '';
		}
		$('title-wrapper').style.display = 'none';
		if( module_type != 0 ) {
			$('title-wrapper').style.display = 'block';
			$('subcategory_backgroundimage').style.display = 'block';
			$('title').style.display = 'none';
			$('title-label').style.display = 'none';
			$('subcategory_backgroundimage').innerHTML = '<div class="form-wrapper"><div class="form-label">&nbsp;</div><div class="form-element"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif" /></div></div>';

			en4.core.request.send(new Request.JSON({
				url : en4.core.baseUrl + 'communityad/display/contenttype?resource_type=' + module_type,
				data : {
					format : 'json'
				},
				onSuccess : function(responseJSON) {
					$('subcategory_backgroundimage').style.display = 'none';
					clear('title');

					var  subcatss = responseJSON.resource_string;
					if( subcatss == '' ) {
						$('title-label').style.display = 'block';
						if($('validation_subtitle')) {					
							$('validation_subtitle').innerHTML ="<?php echo $this->string()->escapeJavascript($this->translate('You have not created any content of this type.')); ?>";
						}else {
							var div_cads_body = document.getElementById("title-element");
							var myElement = new Element("p");
							myElement.innerHTML = "<?php echo $this->string()->escapeJavascript($this->translate("You have not created any content of this type.")); ?>";
							myElement.addClass("error");
							myElement.id = "validation_subtitle";
							div_cads_body.appendChild(myElement);
						}
						resetcontent();
					}else {
						if($('validation_subtitle')) {
							$('validation_subtitle').innerHTML = '';
						}
						$('subtitle_string').value = subcatss;
						addOption($('title')," ", '0');
						for (i=0; i< subcatss.length; i++) {
							addOption($('title'), subcatss[i]['title'], subcatss[i]['id']);
						}
					}
					if( $('module_id').value != 0 ) {
						$('title').value = $('module_id').value;
						setTimeout("resourceData($('module_id').value)", 5);						
					}
				}
			}));
		}else {
			$('title-wrapper').style.display = 'none';
			resetcontent();
		}
	};

	function resetcontent()
	{
		var title_limit = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.title', 25);?>';
		var body_limit = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.body', 135);?>';
		$('cads_body').value = ''; // Set responce description for form description.
		$('name').value = ''; // set responce label for form submit title.
		$('cads_url').value = 'http://'; // set responce page url for form submit url.
		$('ad_title').innerHTML = '<a href="">' + '<?php echo $this->string()->escapeJavascript($title) ?>' + '</a>'; // set responce title for preview title.
		$('ad_body').innerHTML = '<?php echo $this->string()->escapeJavascript($body) ?>'; // set responce description for preview body.
		$('ad_photo').innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/blankImage.png" alt=" " />'; // set responce photo for preview photo.
		$('resource_image').value = ''; // set responce for Hidden element - image id
		$('resource_type').value = ''; // set responce for Hidden element - resource_type
		$('resource_id').value = ''; // set responce for Hidden element - resource_id
		$('profile_address_text').innerHTML = title_limit;
		$('profile_address_text1').innerHTML = body_limit;
	}

	var resourceData = function( resource_id )
	{
		// Condition: If selection from 'Select Content' drop down is empty then reset all values. 
		if( resource_id != 0 )
		{
			// When select any content from drop down then show loder image in preview side.
			$('ad_title').innerHTML='<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/loader.gif" alt=""></center>';
			$('ad_body').innerHTML = '';
			$('ad_photo').innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Communityad/externals/images/blankImage.png" alt=" " />';
			$('ad_photo').style.display='block';
			$('remove_image_link').style.display='block';
			var resource_type = $('create_feature').value;
			en4.core.request.send(new Request.JSON({     	
				url : en4.core.baseUrl + 'communityad/display/resourcecontent?resource_type=' + resource_type + '&resource_id=' + resource_id,
				data : {
					format : 'json'
				},
				onSuccess : function(responseJSON) {
					$('name').addClass('disabled_title');
					$('cads_body').value = responseJSON.des; // Set responce description for form description.
					$('name').value = responseJSON.title; // set responce label for form submit title.
					$('cads_url').value = responseJSON.page_url;// set responce page url for form submit url.
					$('ad_title').innerHTML = responseJSON.preview_title; // set responce title for preview title.
					$('content_title').value = responseJSON.title;
					$('ad_body').innerHTML = responseJSON.des;//responseJSON.like_button; // set responce description for preview body.
					$('ad_photo').innerHTML = responseJSON.photo; // set responce photo for preview photo.
					$('preview_title').value = responseJSON.preview_title; // set responce title for preview title.
					$('preview_body').value = responseJSON.des ; // set responce description for preview body.
					$('resource_image').value = responseJSON.photo; // set responce for Hidden element - image id
					$('resource_type').value = responseJSON.resource_type; // set responce for Hidden element - resource_type
					$('resource_id').value = responseJSON.id; // set responce for Hidden element - resource_id
					$('like').value = 1;
					$('imageenable').value = 1;
					if( $('profile_address_text1') ) {
						$('profile_address_text1').innerHTML = responseJSON.remaning_body_text;
					}
					$('photo_id_filepath').value = responseJSON.photo_id_filepath;
					if( responseJSON.photo.search('application/') > 0 ){
						removeImage();
						$('remove_image_link').style.display = 'none';
					}
					if( $('profile_address_text') ) {
						$('profile_address_text').innerHTML = responseJSON.remaning_title_text;
					}

					if( $('validation_name') ) {
						$('name-element').removeChild($('validation_name'));
					}
					if( $('validation_cads_body') ) {
						$('cads_body-element').removeChild($('validation_cads_body'));
					}
					if( $('validation_image') ) {
						$('image-element').removeChild($('validation_image'));
					}
				}
			}));
		}else {
			resetcontent();
		}
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

		if(optn.text != '' && optn.value != '') {
			$('title').style.display = 'block';
			$('title-label').style.display = 'block';
			selectbox.options.add(optn);
		} else {
				$('title').style.display = 'none';
				$('title-label').style.display = 'none';
				selectbox.options.add(optn);
		}
	}
</script>