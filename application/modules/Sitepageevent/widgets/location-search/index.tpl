<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitepageevent/externals/styles/style_sitepageevent.css');

//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&sensor=true&key=$apiKey");
?>
<?php if( $this->form ): ?>
  <?php echo $this->form->setAttrib('class', 'global_form_box eventlocation_advanced_search_form')->render($this) ?>
  <div class="" id="eventlocation_location_pops_loding_image" style="display: none;">
	<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' />
	<?php //echo $this->translate("Loading ...") ?>
</div>
<?php endif ?>

<script type="text/javascript">
  var flag = '<?php echo $this->advanced_search; ?>';
  var mapGetDirection;
  var myLatlng;
	window.addEvent('domready', function() {
	
	  if(document.getElementById('sitepage_location').value == '') {
			submiForm();
		}
		
		if ($$('.browse-separator-wrapper')) {
			$$('.browse-separator-wrapper').setStyle("display",'none');
		}
	
	  $('eventlocation_location_pops_loding_image').injectAfter($('done-element'));
		new google.maps.places.Autocomplete(document.getElementById('sitepage_location'));
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position){
			var lat = position.coords.latitude;
			var lng = position.coords.longitude;
			
			var myLatlng = new google.maps.LatLng(lat,lng);
			
			var myOptions = {
				zoom: 8 ,
				center: myLatlng,
				navigationControl: true,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}

			mapGetDirection = new google.maps.Map(document.getElementById("eventlocation_location_map_none"), myOptions);
    
        if(!position.address) {
          var service = new google.maps.places.PlacesService(mapGetDirection);
          var request = {
            location: new google.maps.LatLng(lat,lng), 
            radius: 500
          };
          
          service.search(request, function(results, status) { 
            if (status  ==  'OK') {
              var index = 0;
              var radian = 3.141592653589793/ 180;
              var my_distance = 1000; 
              for (var i = 0; i < results.length; i++){
              var R = 6371; // km
              var lat2 = results[i].geometry.location.lat();
              var lon2 = results[i].geometry.location.lng(); 
              var dLat = (lat2-lat) * radian;
              var dLon = (lon2-lng) * radian;
              var lat1 = lat * radian;
              var lat2 = lat2 * radian;

              var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(lat1) * Math.cos(lat2); 
              var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
              var d = R * c;
              if(d < my_distance) {
                index = i;
                my_distance = d;
              }
            }      
           
              document.getElementById('sitepage_location').value = (results[index].vicinity) ? results[index].vicinity :'';
              document.getElementById('Latitude').value = lat;
              document.getElementById('Longitude').value = lng;
              document.getElementById('locationmiles').value = 1000;
              
              //form submit by ajax
              submiForm();
            } 
          });
        } else {
          var delimiter = (position.address && position.address.street !=  '' && position.address.city !=  '') ? ', ' : '';
          var location = (position.address) ? (position.address.street + delimiter + position.address.city) : '';
          document.getElementById('sitepage_location').value = location;
					document.getElementById('Latitude').value = lat;
					document.getElementById('Longitude').value = lng;
					document.getElementById('locationmiles').value = 1000;
          //form submit by ajax
          submiForm();
        }
      });
    } else {
			submiForm();
		}

		advancedSearchEvents(flag);
		
	});

	function submiForm() {
	
  	if ($('category_id').options[$('category_id').selectedIndex].value == 0) { 
			$('category_id').value = 0;
		}
		var  formElements = document.getElementById('filter_form');
		var url = en4.core.baseUrl + 'widget/index/mod/sitepageevent/name/bylocation-event';
		var parms = formElements.toQueryString(); 

		var param = (parms ? parms + '&' : '') + 'is_ajax=1&format=html';
		document.getElementById('eventlocation_location_pops_loding_image').style.display ='';
		en4.core.request.send(new Request.HTML({
			method : 'post',
			'url' : url,
			'data' : param,
			onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
				document.getElementById('eventlocation_location_pops_loding_image').style.display ='none';
				$('eventlocation_map_container_topbar').style.display ='block';
				document.getElementById('eventlocation_location_map_anchor').getParent().innerHTML = responseHTML;
				setMarker();
			  en4.core.runonce.trigger();
				$('eventlocation_map_container').style.visibility = 'visible'; 
				if ($('seaocore_browse_list')) {
					var elementStartY = $('eventlocation_map').getPosition().x ;
					var offsetWidth = $('eventlocation_map_container').offsetWidth;
					var actualRightPostion = window.getSize().x - (elementStartY + offsetWidth);
				}
			}
		}), {
         "force":true
    });
	}

	function locationPage() {
		var  sitepage_location = document.getElementById('sitepage_location');

		if (document.getElementById('Latitude').value) {
			document.getElementById('Latitude').value = 0;
		}
		
		if(document.getElementById('Longitude').value) {
			document.getElementById('Longitude').value = 0;
		}
	}
	
	function locationSearch() {

	  var  formElements = document.getElementById('filter_form');
    formElements.addEvent('submit', function(event) { 
      event.stop();
      submiForm();
    });
  }

	function advancedSearchEvents() {
	
		if (flag == 0) {
				
			if ($('fieldset-grp1'))
				$('fieldset-grp1').style.display = 'none';

			flag = 1;
			$('advanced_search').value = 0;
			if ($('order'))
			$('order').value = '';
		} else {
			if ($('fieldset-grp1'))
				$('fieldset-grp1').style.display = 'block';
				
			flag = 0;
			$('advanced_search').value = 1;
		}
  }
</script>
<script type="text/javascript">
  var form;

  var location_subcategoryies = function(category_id, sub, subcatname, subsubcat) {

	  if($('filter_form')) {
	    form=document.getElementById('filter_form');
	  } else if($('filter_form_category')){
			form=$('filter_form_category');
		}

    if($('category_id') && form.elements['category_id']){
      form.elements['category_id'].value = '<?php echo $this->category_id;?>';
    }

    if($('subcategory_id') && form.elements['subcategory_id']){
      form.elements['subcategory_id'].value = '<?php echo $this->subcategory_id;?>';
    }

    if($('subsubcategory_id') && form.elements['subsubcategory_id']){
      form.elements['subsubcategory_id'].value = '<?php echo $this->subsubcategory_id;?>';
    }
    
    if(category_id != '' && form.elements['category_id']){
      form.elements['category_id'].value = category_id;
    }

    if(category_id != 0) {
      if(sub == '') {
				sub=0;
				subsubcat = 0;
      }
      changesubcategory(sub, subsubcat, subcatname);
    }

  	var url = '<?php echo $this->url(array('action' => 'subcategory'), 'sitepage_general', true);?>';
    en4.core.request.send(new Request.JSON({      	
      url : url,
      data : {
        format : 'json',
        category_id_temp : category_id
      },
      onSuccess : function(responseJSON) {
      	clear('subcategory_id');
        var  subcatss = responseJSON.subcats;        
        addOption($('subcategory_id')," ", '0');
        for (i=0; i< subcatss.length; i++) {
          addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);  
          //$('subcategory_id').value = sub;
          //form.elements['subcategory'].value = sub;
        	form.elements['categoryname'].value = subcatss[i]['categoryname_temp'];
          form.elements['category'].value = category_id;
          form.elements['subcategory_id'].value = sub;
          //form.elements['subcategory'].value = sub;
//           if(form.elements['subsubcategory'])
//           form.elements['subsubcategory'].value = subsubcat;
//           if(form.elements['subsubcategory_id'])
//           form.elements['subsubcategory_id'].value = subsubcat;
        }

        if(subcatss.length == 0) {
	      	form.elements['categoryname'].value = 0;
        }

        if(category_id == 0) {
          clear('subcategory_id');
          clear('subsubcategory_id');
          $('subcategory_id').style.display = 'none';
          $('subcategory_id-label').style.display = 'none';
          $('subsubcategory_id').style.display = 'none';
          $('subsubcategory_id-label').style.display = 'none';
        }
      }
    }));
  };

  function clear(ddName) {
    for (var i = (document.getElementById(ddName).options.length-1); i >= 0; i--) {
      document.getElementById(ddName).options[ i ]=null; 	      
    }
  }

  function addOption(selectbox,text,value ) {
    var optn = document.createElement("OPTION");
    optn.text = text;
    optn.value = value;

    if(optn.text != '' && optn.value != '') {
      $('subcategory_id').style.display = 'inline-block';
      $('subcategory_id-label').style.display = 'inline-block';
      selectbox.options.add(optn);
    }
    else {
      $('subcategory_id').style.display = 'none';
      $('subcategory_id-label').style.display = 'none';
      selectbox.options.add(optn);
    }
  }
  
	var changesubcategory = function(subcatid, subsubcat, subcatname) {
	
		var url = '<?php echo $this->url(array('action' => 'subsubcategory'), 'sitepage_general', true);?>';
		var request = new Request.JSON({
			url : url,
			data : {
				format : 'json',
				subcategory_id_temp : subcatid
			},
			onSuccess : function(responseJSON) {
				clear('subsubcategory_id');
				var  subsubcatss = responseJSON.subsubcats;
				addSubOption($('subsubcategory_id')," ", '0');
				for (i=0; i< subsubcatss.length; i++) {
					addSubOption($('subsubcategory_id'), subsubcatss[i]['category_name'], subsubcatss[i]['category_id']);
					if(form.elements['subsubcategory_id'])
					form.elements['subsubcategory_id'].value = subsubcat;
					if(form.elements['subsubcategory'])
					form.elements['subsubcategory'].value = subsubcat;
					if($('subsubcategory_id')) {
						$('subsubcategory_id').value = subsubcat;
					}
				}
				form.elements['subcategory'].value = subcatid;
				form.elements['subcategoryname'].value = subcatname;

				if(subcatid == 0) {
					clear('subsubcategory_id');
					if($('subsubcategory_id-label'))
					$('subsubcategory_id-label').style.display = 'none';
				}
			}
		});
		request.send();
	};

	function addSubOption(selectbox,text,value ) {
		var optn = document.createElement("OPTION");
		optn.text = text;
		optn.value = value;
		
		if(optn.text != '' && optn.value != '') {
			$('subsubcategory_id').style.display = 'block';
				if($('subsubcategory_id-wrapper'))
				$('subsubcategory_id-wrapper').style.display = 'inline-block';
				if($('subsubcategory_id-label'))
				$('subsubcategory_id-label').style.display = 'inline-block';
			selectbox.options.add(optn);
		} 
		else {
			$('subsubcategory_id').style.display = 'none';
				if($('subsubcategory_id-wrapper'))
				$('subsubcategory_id-wrapper').style.display = 'none';
				if($('subsubcategory_id-label'))
				$('subsubcategory_id-label').style.display = 'none';
			selectbox.options.add(optn);
		}
	}

  var cat = '<?php echo $this->category_id ?>';

  if(cat != '') {
    var sub = '<?php echo $this->subcategory_id; ?>';
    var subcatname = '<?php echo $this->subcategory_name; ?>';
    var subsubcat = '<?php echo $this->subsubcategory_id; ?>';

    location_subcategoryies(cat, sub, subcatname,subsubcat);
  }
</script>

<div id="eventlocation_location_map_none" style="display: none;"></div>

<script type="text/javascript">
  var cal_starttime_onHideStart = function(){
    // check end date and make it the same date if it's too
    cal_endtime.calendars[0].start = new Date( $('starttime-date').value );
    // redraw calendar
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);
  }
  var cal_endtime_onHideStart = function(){
    // check start date and make it the same date if it's too
    cal_starttime.calendars[0].end = new Date( $('endtime-date').value );
    // redraw calendar
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', -1);
  }
    window.addEvent('domready', function() {
    if($('endtime-minute')) {
      $('endtime-minute').style.display= 'none';
    }
    
    if($('endtime-ampm')) {
      $('endtime-ampm').style.display= 'none';
    }
    
    if($('endtime-hour')) {
      $('endtime-hour').style.display= 'none';
    }
    
    if($('starttime-minute')) {
      $('starttime-minute').style.display= 'none';
    }
    
    if($('starttime-ampm')) {
      $('starttime-ampm').style.display= 'none';
    }
    
    if($('starttime-hour')) {
      $('starttime-hour').style.display= 'none';
    }
  });
  
	if($('endtime-minute')) {
		$('endtime-minute').style.display= 'none';
	}
	
	if($('endtime-ampm')) {
		$('endtime-ampm').style.display= 'none';
	}
	
	if($('endtime-hour')) {
		$('endtime-hour').style.display= 'none';
	}
	
	if($('starttime-minute')) {
		$('starttime-minute').style.display= 'none';
	}
	
	if($('starttime-ampm')) {
		$('starttime-ampm').style.display= 'none';
	}
	
	if($('starttime-hour')) {
		$('starttime-hour').style.display= 'none';
	}
</script>