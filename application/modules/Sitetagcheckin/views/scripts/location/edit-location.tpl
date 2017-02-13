<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit-location.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
if ($this->resource_type == 'group') :
	$itemId = 'group_id';
	$route = 'sitetagcheckin_groupspecific';
elseif ($this->resource_type == 'event') :
	$itemId = 'event_id';
	$route = 'sitetagcheckin_specific';
elseif ($this->resource_type == 'user') :
	$itemId = 'user_id';
	$route = 'sitetagcheckin_userspecific';
endif;
?>
<?php if (empty($this->seao_locationid)) : ?>
<script type="text/javascript">
window.addEvent('domready', function() {
	var url = '<?php echo Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'edit-address', $itemId => $this->resource_id, 'resource_type' => $this->resource_type), "$route", true) ?>';
Smoothbox.open(url);
	});
</script>
<?php endif; ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitetagcheckin/externals/styles/style_sitetagcheckin.css'); ?>

<div class='layout_middle'>
	<div class="stchecking_pages_breadcrumbs">
		<?php echo $this->htmlLink($this->resource->getHref(), $this->itemPhoto($this->resource, 'thumb.icon', '', array('align' => 'left'))) ?>
		<h2>
			<?php echo $this->resource->__toString() ?>	
		</h2>
	</div>
	<?php
	$this->headScript()
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
	    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
	?>
  <div class="sitetagcheckin_edit_content">
  <div id="show_tab_content">
  	<div class="sitetagcheckin_editlocation_wrapper">
			<?php if (!empty($this->location)): ?>
				<h4><?php echo $this->translate('Edit Location') ?></h4>
				<p>
					<?php echo $this->translate('Edit location by clicking on "Edit Location" below. You can also accurately mark the location on the map by dragging-and-dropping the marker (shown in red color) on the map at the right position, and then click on "Save Changes" to save the position.') ?>
				</p>
				<br />
				<div class="edit_form">
					<div class="global_form_box">
						<div>
							<div class="formlocation_edit_label"><?php echo $this->translate('Location: ');?></div>
							<?php
								echo $this->htmlLink(array(
								'route' => $route,
								"$itemId" => $this->resource_id,
								'seao_locationid' => $this->seao_locationid,
								'action' => 'edit-address',
								'resource_type' => $this->resource_type,
								), $this->translate('Edit Location'), array(
								
								'class' => 'smoothbox stcheckin_icon_map_edit buttonlink fright',
								));
							?>
							<div class="formlocation_add"><?php echo $this->location->location ?></div>
	
						</div>
					</div>
				</div>
				<div class="edit_form">
					<?php echo $this->form->render($this); ?>
				</div>	
				<div class="global_form">
						<div>
							<div class="seaocore_map" style="padding:0px;">
								<div id="mapCanvas"></div>
								<?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
								<?php if (!empty($siteTitle)) : ?>
								<div class="seaocore_map_info"><?php echo "Locations on "; ?><a href="" target="_blank"><?php echo $siteTitle; ?></a></div>
								<?php endif; ?>
							</div>	
						</div>
				</div>
			<?php else: ?>
				<div class="tip">
					<span>
					<?php echo $this->translate('No location has been added. Please '); ?>
											<a  onclick="javascript:Smoothbox.open('<?php echo Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'edit-address', $itemId => $this->resource_id, 'resource_type' => $this->resource_type), "$route", true) ?>');"
													href="javascript:void(0);"><?php echo $this->translate('click here'); ?></a>
							<?php echo $this->translate(' to add a location.'); ?>
					</span>
				</div>
			<?php endif; ?>
			</div>
		</div>
  </div>
  </div>
<?php if (!empty($this->location)): ?>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
  var map;
  var geocoder = new google.maps.Geocoder();
  var tresponses;
  function geocodePosition(pos) {
    geocoder.geocode({
      latLng: pos
    }, function(responses) {
      if (responses && responses.length > 0) {
        updateMarkerAddress(responses[0].formatted_address);
        //	tresponses=responses;
        var len_add = responses[0].address_components.length;

        document.getElementById('address').value='';
        document.getElementById('country').value='';
        document.getElementById('state').value ='';
        document.getElementById('city').value ='';
        document.getElementById('zipcode').value ='';
        for (i=0; i< len_add; i++) {


          var types_location = responses[0].address_components[i].types;

          if(types_location=='country,political'){

            document.getElementById('country').value = responses[0].address_components[i].long_name;
          }else if(types_location=='administrative_area_level_1,political')
          {
            document.getElementById('state').value = responses[0].address_components[i].long_name;
          }else if(types_location=='administrative_area_level_2,political')
          {
            document.getElementById('city').value = responses[0].address_components[i].long_name;
          }else if(types_location=='postal_code')
          {
            document.getElementById('zipcode').value = responses[0].address_components[i].long_name;
          }
          else if(types_location=='street_address')
          {
            if(document.getElementById('address').value=='')
              document.getElementById('address').value = responses[0].address_components[i].long_name;
            else
              document.getElementById('address').value = document.getElementById('address').value+','+responses[0].address_components[i].long_name;

          }else if(types_location=='locality,political')
          {  if(document.getElementById('address').value=='')
              document.getElementById('address').value = responses[0].address_components[i].long_name;
            else
              document.getElementById('address').value = document.getElementById('address').value+','+responses[0].address_components[i].long_name;
          }else if(types_location=='route')
          {
            if(document.getElementById('address').value=='')
              document.getElementById('address').value = responses[0].address_components[i].long_name;
            else
              document.getElementById('address').value = document.getElementById('address').value+','+responses[0].address_components[i].long_name;
          }else if(types_location=='sublocality,political')
          {
            if(document.getElementById('address').value=='')
              document.getElementById('address').value = responses[0].address_components[i].long_name;
            else
              document.getElementById('address').value = document.getElementById('address').value+','+responses[0].address_components[i].long_name;

          }

        }

        document.getElementById('zoom').value=map.getZoom();
      } else {
        document.getElementById('address').value='';
        document.getElementById('country').value='';
        document.getElementById('state').value ='';
        document.getElementById('city').value ='';
        updateMarkerAddress('Cannot determine address at this location.');
      }
    });
  }


  function updateMarkerPosition(latLng) {
    document.getElementById('latitude').value = latLng.lat();
    document.getElementById('longitude').value = latLng.lng();
  }

  function updateMarkerAddress(str) {
    document.getElementById('formatted_address').value = str;
  }

  function initialize() {
    var latLng = new google.maps.LatLng(<?php echo $this->location->latitude; ?>,<?php echo $this->location->longitude; ?>);
    map = new google.maps.Map(document.getElementById('mapCanvas'), {
      zoom: <?php echo $this->location->zoom; ?>,
      center: latLng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    var marker = new google.maps.Marker({
      position: latLng,
      title: 'Point Location',

      map: map,
      draggable: true
    });

   
    // Add dragging event listeners.
    google.maps.event.addListener(marker, 'dragstart', function() {
      updateMarkerAddress('Dragging...');
    });

    google.maps.event.addListener(marker, 'drag', function() {
      // updateMarkerStatus('Dragging...');
      updateMarkerPosition(marker.getPosition());
    });

    google.maps.event.addListener(marker, 'dragend', function() {
      //  updateMarkerStatus('Drag ended');



      geocodePosition(marker.getPosition());
    });
  }

  // Onload handler to fire off the app.
  google.maps.event.addDomListener(window, 'load', initialize);
</script>
<?php endif; ?>

<style types="text/css">
	.edit_form .global_form_box > div .form-elements > div input[type="text"]{width:160px;}
</style>