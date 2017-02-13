<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  var infoBubbles;
</script>

<?php   //GET API KEY
  $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$infobubbleJS = "https://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobubble/src/infobubble.js";
?>
<?php 
$markerclustererJS = "https://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/src/markerclusterer.js";
?>
<?php
$mapjs = "https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&key=$apiKey";
?>
<script src="<?php echo $infobubbleJS;?>" type="text/javascript"></script>
<script src="<?php echo $markerclustererJS;?>" type="text/javascript"></script>
<?php
   $moduleCore = Engine_Api::_()->getDbtable('modules', 'core');
	 $getEnableModuleEvent = $moduleCore->isModuleEnabled('event');
	 $getEnableModuleYnEvent = $moduleCore->isModuleEnabled('ynevent');
	 $getEnableModulePageEvent = $moduleCore->isModuleEnabled('sitepageevent');
	 $getEnableModuleBusinessEvent = $moduleCore->isModuleEnabled('sitebusinessevent');
   $getEnableModuleGroupEvent = $moduleCore->isModuleEnabled('sitegroupevent');
   $this->headLink()
?>
<div class="stcheckin_profile_header" id="sitetag_checkin_profile_header">
	<?php if($this->subject->user_id == $this->viewer_id) :?>
		<div class="sm-ui-profile-map-link-left" id="stcheckin_feeds_buttton">
			<?php
				$url = $this->url(array('action' => 'check-in', 'module' => 'sitetagcheckin', 'controller' => 'checkin', 'resource_type' => $this->subject->getType(), 'resource_id' => $this->subject->getIdentity(), 'checkin_use' => 1, 'checkin_verb' => "Post",  'checkedinto_verb' => $this->checkedinto_verb, 'tab' => $this->identity), 'default', true);
			?>
		<?php $moduleCore = Engine_Api::_()->getDbtable('modules', 'core');
    $getEnableModulealbum = $moduleCore->isModuleEnabled('album');
    $getEnableModuleAdvalbum = $moduleCore->isModuleEnabled('advalbum');  ?>
        
				<a href="<?php echo $url; ?>" id="stckeckin_icon_checkin"  data-role="button" data-icon="plus" data-iconpos="left" data-inset="false" data-mini="true" data-corners="true" data-shadow="true" data-inline="true" data-ajax="true">
					<?php echo $this->translate("Add Locations on Map"); ?>
				</a>
				
		</div>
		<div id="background-photo-location-image" class="fleft" style="display:none;">
			<img src="./application/modules/Sitetagcheckin/externals/images/loading.gif" />
		</div>
	<?php endif;?>
	<div class="stcheckin_profile_header_right" style="display:none;">
		<div onclick="showFeeds();" id="display_feedlinks" class="stcheckin_profile_buttons stcheckin_tip">
			<div class="stcheckin_tip_content"><?php echo $this->translate("View Feeds");?></div>
			<img src="./application/modules/Sitetagcheckin/externals/images/list-view.png" alt="" />
		</div>
		<div onclick="showMap();" id="display_maplinks" class="stcheckin_profile_buttons stcheckin_tip">
			<div class="stcheckin_tip_content"><?php echo $this->translate("View Map");?></div>
			<img src="./application/modules/Sitetagcheckin/externals/images/map-view.png" alt="" />
		</div>
	</div>	
</div>

<?php if($this->subject->user_id == $this->viewer_id) :?>
	<div id="map_add_photo">
		<div id="show-photo-strip"></div>
	</div>
<?php endif;?>

<script type="text/javascript">
sm4.core.runonce.add(function() {
  
 setTimeout(function(){
                            showMap();
                            infoBubbles.close();
                            google.maps.event.trigger(mapCheckin, 'resize');
                            setMapCenterZoomPoint(<?php echo json_encode(Engine_Api::_()->seaocore()->getProfileMapBounds($this->locations));?>,mapCheckin);
                            showMap();
                        }, 50);
		
  
});
function filterMap(category) {
 $.mobile.activePage.find('.seaocheckinmapfilters').removeClass('active');
  clearOverlays();
  gmarkers = [];
  
  var lng_radius = 0.0003,         // degrees of longitude separation
        lat_to_lng = 111.23 / 71.7,  // lat to long proportion in Warsaw
        angle = 0.5,                 // starting angle, in radians
        loclen = 13,
        step = 2 * Math.PI / loclen,
        i,
        loc,
        lat_radius = lng_radius / lat_to_lng; 
  if(category == 1) {	
		$.mobile.activePage.find('#location_filter').addClass('active'); //.className="seaocheckinmapfilters active";
    <?php if($this->locations):?> 
			<?php foreach ($this->locations as $location) : ?>
				lat= <?php echo $location['latitude'] ?> + (Math.sin(angle) * lat_radius);
				lng = <?php echo $location['longitude'] ?> + (Math.sin(angle) * lng_radius);
				point = new google.maps.LatLng(lat,lng);
				<?php for($i = 1; $i<= $location['count']; $i++): ?>
					createMarker(point, "<?php echo $location['location'] ?>", "<?php echo $location['count'] ?>", "<?php echo $location['location_id'] ?>", category);
				<?php endfor;?>
				angle += step;
			<?php endforeach; ?>
    <?php endif;?>
		setMapCenterZoomPoint(<?php echo json_encode(Engine_Api::_()->seaocore()->getProfileMapBounds($this->locations));?>,mapCheckin);
  } 
  else if(category == 2) {
		$.mobile.activePage.find('#checkin_filter').addClass('active'); //.className="seaocheckinmapfilters active";
    <?php if($this->checkins):?>  
			<?php foreach ($this->checkins as $location) : ?>
				lat= <?php echo $location['latitude'] ?> + (Math.sin(angle) * lat_radius);
				lng = <?php echo $location['longitude'] ?> + (Math.sin(angle) * lng_radius);
				point = new google.maps.LatLng(lat,lng);
				<?php for($i = 1; $i<= $location['count']; $i++): ?>
					createMarker(point, "<?php echo $location['location'] ?>", "<?php echo $location['count'] ?>", "<?php echo $location['location_id'] ?>", category, 0);
				<?php endfor;?>
				angle += step;
			<?php endforeach; ?>
    <?php endif;?>
		setMapCenterZoomPoint(<?php echo json_encode(Engine_Api::_()->seaocore()->getProfileMapBounds($this->checkins));?>,mapCheckin);
  }
  else if(category == 3) { 
		$.mobile.activePage.find('#phototag_filter').addClass('active'); //.className="seaocheckinmapfilters active";
		<?php if($this->taggedPhotos):?>
			<?php foreach ($this->taggedPhotos as $location) : ?>
				lat= <?php echo $location['latitude'] ?> + (Math.sin(angle) * lat_radius);
				lng = <?php echo $location['longitude'] ?> + (Math.sin(angle) * lng_radius);
				point = new google.maps.LatLng(lat,lng);
				<?php for($i = 1; $i<= $location['count']; $i++): ?>
					createMarker(point, "<?php echo $location['location'] ?>", "<?php echo $location['count'] ?>", "<?php echo $location['location_id'] ?>", category, 0);
				<?php endfor;?>
					angle += step;
			<?php endforeach; ?>
    <?php endif;?>
		setMapCenterZoomPoint(<?php echo json_encode(Engine_Api::_()->seaocore()->getProfileMapBounds($this->taggedPhotos));?>,mapCheckin);
  } 
  else if(category == 4) {
		$.mobile.activePage.find('#updates_filter').addClass('active'); //.className="seaocheckinmapfilters active";
    <?php if($this->updates):?>
			<?php foreach ($this->updates as $location) : ?>
				lat= <?php echo $location['latitude'] ?> + (Math.sin(angle) * lat_radius);
				lng = <?php echo $location['longitude'] ?> + (Math.sin(angle) * lng_radius);
				point = new google.maps.LatLng(lat,lng);
				<?php for($i = 1; $i<= $location['count']; $i++): ?>
					createMarker(point, "<?php echo $location['location'] ?>", "<?php echo $location['count'] ?>", "<?php echo $location['location_id'] ?>", category, 0);
				<?php endfor;?>
				angle += step;
			<?php endforeach; ?>	
    <?php endif;?>
		setMapCenterZoomPoint(<?php echo json_encode(Engine_Api::_()->seaocore()->getProfileMapBounds($this->updates));?>,mapCheckin);
  } 
  else if(category == 5) {
		$.mobile.activePage.find('#eventtag_filter').addClass('active'); //.className="seaocheckinmapfilters active";
    <?php if($this->eventLocationsArray):?>
			<?php foreach ($this->eventLocationsArray as $location) : ?>
				lat= <?php echo $location['latitude'] ?> + (Math.sin(angle) * lat_radius);
				lng = <?php echo $location['longitude'] ?> + (Math.sin(angle) * lng_radius);
				point = new google.maps.LatLng(lat,lng);
				<?php for($i = 1; $i<= $location['count']; $i++): ?>
					createMarker(point, "<?php echo $location['location'] ?>", "<?php echo $location['count'] ?>", "<?php echo $location['location_id'] ?>", category);
				<?php endfor;?>
				angle += step;
			<?php endforeach; ?>	
    <?php endif;?>
		setMapCenterZoomPoint(<?php echo json_encode(Engine_Api::_()->seaocore()->getProfileMapBounds($this->eventLocationsArray));?>,mapCheckin);
  }

	markerClusterer = new MarkerClusterer(mapCheckin, gmarkers, {
   zoomOnClick: true
	});

	google.maps.event.addListener(markerClusterer, 'clusterclick', function(cluster) {
   	var info = new google.maps.MVCObject;
   	info.set('position', cluster.center_);
    var markers = cluster.getMarkers();
  
    for(var i = 1; i < markers.length; i++) {
      if(info.position != markers[i].position) {
        return;
      }
    }

    infoBubbles.close();
 		contentString = '<div class="sitetag_checkin_map_tip_loading"><center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitetagcheckin/externals/images/loading.gif" style="margin:10px 0;"/></center></div>';
		infoBubbles.setContent(contentString);
		infoBubbles.open(mapCheckin,info);

    var i = 0;
	  getPhotoPagination(markers[i].location_id, markerCheckin, mapCheckin, markers[i].getTitle(), category);

	});

	if(markerCheckinLocation) {
		markerCheckinLocation.setMap(null);
    markerCheckinLocation=null;
	}

}

var is_active_feed_req=false;
function showFeeds() {
  if($.mobile.activePage.find('#mapsitetagcheckin_map'))
  $.mobile.activePage.find('#mapsitetagcheckin_map').css('display', 'none');
  if($.mobile.activePage.find('#sitetagcheckin_autosuggest_tooltiplocations'))
  $.mobile.activePage.find('#sitetagcheckin_autosuggest_tooltiplocations').css('display', 'none');
  if($.mobile.activePage.find('#setlocation_photo_suggest_tip'))
  $.mobile.activePage.find('#setlocation_photo_suggest_tip').css('display', 'none');

  if($.mobile.activePage.find('#status_update_location'))
  $.mobile.activePage.find('#status_update_location').css('display', 'none');
  if($.mobile.activePage.find('#stcheckin_feeds_buttton'))
  $.mobile.activePage.find('#stcheckin_feeds_buttton').css('display', 'none'); 
	if($.mobile.activePage.find('#map_add_photo'))
  $.mobile.activePage.find('#map_add_photo').css('display', 'none');
  $.mobile.activePage.find('#feed_items').html('<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/loading.gif" alt="" style="margin:50px 0;" /></center>');
  is_active_feed_req = true; 
 
	$.ajax({
        type: "GET", 
        dataType: "html", 
        url: '<?php echo $this->url(array('action' => 'get-feed-items'), 'sitetagcheckin_general', true);?>',
        data : {
          format : 'html',
          subject:  sm4.core.subject.guid,
          show_map: 0,
          is_ajax: 0
        },
        success:function( responseHTML, textStatus, xhr ) { 
          is_active_feed_req=false;
			   
          $.mobile.activePage.find('#feed_items').html(responseHTML);
          sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
        }
      });
}

function showMap() {
  if(is_active_feed_req) return;

	if(markerCheckinLocation) {
		markerCheckinLocation.setMap(null);
    markerCheckinLocation=null;
	}
  if($.mobile.activePage.find('#status_update_location'))
  $.mobile.activePage.find('#status_update_location').css('display', 'block');
  if($.mobile.activePage.find('#mapsitetagcheckin_map'))
  $.mobile.activePage.find('#mapsitetagcheckin_map').css('display', 'block');
  if($.mobile.activePage.find('#map_add_photo'))
  $.mobile.activePage.find('#map_add_photo').css('display', 'block');
  if($.mobile.activePage.find('#stcheckin_feeds_buttton'))
  $.mobile.activePage.find('#stcheckin_feeds_buttton').css('display', 'block'); 
  $.mobile.activePage.find('#feed_items').empty();
}

function showPhotoStrip(option) {
  if($.mobile.activePage.find('#filter'))
  $.mobile.activePage.find('#filter').css('display', 'none');
	if($.mobile.activePage.find('#stckeckin_icon_checkin'))
	$.mobile.activePage.find('#stckeckin_icon_checkin').css('display', 'none');
  if($.mobile.activePage.find('#done_photo_adding'))
  $.mobile.activePage.find('#done_photo_adding').css('display', 'block');
  if($.mobile.activePage.find('#add_photos_to_map'))
  $.mobile.activePage.find('#add_photos_to_map').css('display', 'none');
	if($.mobile.activePage.find('#background-photo-location-image') && option == 1)
	$.mobile.activePage.find('#background-photo-location-image').css('display', 'block');
	sm4.core.request.send({
		url : '<?php echo $this->url(array('action' => 'get-albums-photos'), 'sitetagcheckin_general', true);?>',
    type: "GET", 
    dataType: "html", 
		data : {
			format : 'html',
			subject:  sm4.core.subject.guid,
      show_map: 1,
      is_ajax: 0,
      photoCount: '<?php echo $this->countPhoto ?>'
		},
		success : function( responseHTML, textStatus, xhr ) {
			$.mobile.activePage.find('#show-photo-strip').html(responseHTML);
      if($.mobile.activePage.find('#background-photo-location-image'))
      $.mobile.activePage.find('#background-photo-location-image').css('display', 'none');
			
			sm4.core.dloader.refreshPage();
      sm4.core.runonce.trigger();
		}
	});

}

function showAddToMap() {
  $.mobile.activePage.find('#done_photo_adding').css('display', 'none');

  if($.mobile.activePage.find('#sitetagcheckin_autosuggest_tooltiplocations'))
  $.mobile.activePage.find('#sitetagcheckin_autosuggest_tooltiplocations').css('display', 'none');
  $.mobile.activePage.find('#add_photos_to_map').css('display', 'block');
  if($.mobile.activePage.find('#filter'))
  $.mobile.activePage.find('#filter').css('display', 'block');

  if($.mobile.activePage.find('#location_photo_image_recent'))
  $.mobile.activePage.find('#location_photo_image_recent').css('display', 'none');
  <?php if($this->profileAddress) :?>
    location.href = '<?php echo $this->url(array("id" => $this->profileAddress, "tab" => $this->identity), "user_profile");?>';
  <?php endif;?>
}

</script>



<div id="feed_items"></div>
<div id="mapsitetagcheckin_map" class="seaocheckinmaparea">
	<div class="sm-ui-map-wrapper">
		<div class="sm-ui-map" id="mapsitetagcheckin_browse_map_canvas_<?php echo $this->Guid;?>"></div>
		<?php $siteTitle = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title; ?>
		<?php if (!empty($siteTitle)) : ?>
			<div class="sm-ui-map-info"><?php echo "Locations on "; ?><a href="" target="_blank"><?php echo $siteTitle; ?></a></div>
		<?php endif; ?>
	</div>

	<div id="filter" class="sm-profile-map-filters">
		<div class="ui-grid-d ui-responsive">
			<div class="ui-block-a" id="location_filter" class="seaocheckinmapfilters " onclick="filterMap(1);">
				<div class="ui-bar ui-bar-c">
					<?php echo $this->totalMapLocation;?> <?php echo $this->translate("All");?>
				</div>
			</div>
			<div class="ui-block-b" id="updates_filter" onclick="filterMap(4);">
				<div class="ui-bar ui-bar-c">
					<?php echo $this->totalUpdates;?> <?php echo $this->translate(array('Update', 'Updates', $this->totalUpdates),  $this->locale()->toNumber($this->totalUpdates)) ?>
				</div>
			</div>
			<div class="ui-block-c"  id="checkin_filter" onclick="filterMap(2);">
				<div class="ui-bar ui-bar-c">
					<?php echo $this->totalCheckins;?> <?php echo $this->translate(array('Check-in', 'Check-ins', $this->totalCheckins),  $this->locale()->toNumber($this->totalCheckins)) ?>
				</div>
			</div>
			<div class="ui-block-d"  id="phototag_filter" onclick="filterMap(3);">
				<div class="ui-bar ui-bar-c">
					<?php echo $this->totalTaggedPhotos;?> <?php echo $this->translate(array('PHOTO_1', 'PHOTOS_1', $this->totalTaggedPhotos),  $this->locale()->toNumber($this->totalTaggedPhotos)); ?>
				</div>
			</div>
			<?php if($getEnableModuleEvent || $getEnableModuleYnEvent || $getEnableModulePageEvent || $getEnableModuleBusinessEvent || $getEnableModuleGroupEvent) :?>
				<div class="ui-block-e" id="eventtag_filter" onclick="filterMap(5);">
					<div class="ui-bar ui-bar-c">
						<?php echo $this->totalEventCount;?> <?php echo $this->translate(array('Event Attended', 'Events Attended', $this->totalEventCount),  $this->locale()->toNumber($this->totalEventCount)); ?>
					</div>
				</div>
			<?php endif;?>
		</div>
	</div>
</div>

<script type="text/javascript">

  //ARRAYS TO HOLD COPIES OF THE MARKERS AND HTML USED BY THE SIDE_BAR
  //BECAUSE THE FUNCTION CLOSURE TRICK DOESNT WORK THERE
  var gmarkers = [];
  var infoBubbles;
  var markerClusterer = null;
  //GLOBAL "MAP" VARIABLE
  var mapCheckin = null;
  var markerCheckin=null;
  var markerCheckinLocation=null;
  //A FUNCTION TO CREATE THE MARKER AND SET UP THE EVENT WINDOW FUNCTION
  function createMarker(latlng, location, count, location_id, category) {
		var image = 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chco=FFFFFF,008CFF,000000&chld='+count + '|008CFF|000000';
		var markerCheckin = new google.maps.Marker({
			position: latlng,
			map: mapCheckin,
			title: location,
			icon: image,
      count: count,
      location_id: location_id
		});
		gmarkers.push(markerCheckin);
		google.maps.event.addListener(markerCheckin, 'click', function() {
			//google.maps.event.trigger(mapCheckin, 'resize');
			mapCheckin.setCenter(markerCheckin.position);
			contentString = '<div class="sitetag_checkin_map_tip_loading"><center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitetagcheckin/externals/images/loading.gif" style="margin:10px 0;"/></center></div>';
			infoBubbles.setContent(contentString);
			infoBubbles.open(mapCheckin,markerCheckin);
			getPhotoPagination(location_id, markerCheckin, mapCheckin, location, category);
		});

  }

	function getPhotoPagination(location_id,markerCheckin,mapCheckin,location, category) {
    $.ajax({
			url : '<?php echo $this->url(array('action' => 'get-location-photos'), 'sitetagcheckin_general', true);?>',
      type: "GET", 
      dataType: "html", 
			data : {
				format : 'html',
				location_id : location_id,
				location : location,
        subject:  '<?php echo $this->subject->getGuid();?>',
        category: category,
        isajax:0
			},     
			success : function(responseHTML, textStatus, xhr) {
				google.maps.event.trigger(mapCheckin, 'resize');
				contentString ="<div class='sitetag_checkin_map_tip_content'>" +responseHTML +"</div>";
				infoBubbles.open(mapCheckin,markerCheckin);
				infoBubbles.setContent(contentString);
				setTimeout(function() {
          $.mobile.activePage.find('.sitetag_checkin_map_tip_content').find('a').on('click',function(event){
            var link=$(event.target)[0];
            if(link.tagName!='A'){
              link=$(link).closest('a');
            }
            link = $(link);
            //CHECK IF THE HREF CONTAIN # OR JAVASCRIPT VOID(0);
            
           if (link.attr('href').match(/javascript/) == null && link.attr('href').match(/#/) == null)            {
                event.preventDefault();
                $.mobile.changePage(link.attr('href'),{link:link});
           }
   
          });
				  sm4.core.dloader.refreshPage();
          sm4.core.runonce.trigger();
        }, 500);
        
			}
		});
   
	}

	function clearOverlays() {
		infoBubbles.close();
		google.maps.event.trigger(mapCheckin, 'resize');

		if (gmarkers) {
			for (var i = 0; i < gmarkers.length; i++ ) {
				gmarkers[i].setMap(null);
			}
		}

    if (markerClusterer) {
		  markerClusterer.clearMarkers();
		}
	}

  function initializeMap() {
    if($.mobile.getScreenHeight()< 470){
       $.mobile.activePage.find("#mapsitetagcheckin_browse_map_canvas_<?php echo $this->Guid; ?>").closest('.sm-ui-map-wrapper').css('height',($.mobile.getScreenHeight()- 60));
    } 
    var mylatlng = new google.maps.LatLng(0,0);
    //CREATE THE MAP
    var myOptions = {
      zoom: 2,
      center: mylatlng,
      navigationControl: true,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    mapCheckin = new google.maps.Map(document.getElementById("mapsitetagcheckin_browse_map_canvas_<?php echo $this->Guid;?>" ),
    myOptions);

		google.maps.event.addListener(mapCheckin, 'click', function() {
			infoBubbles.close();
		});
    $.mobile.activePage.find('#display_feedlinks').click(function() {
        infoBubbles.close();
      });
    
		$.mobile.activePage.find('.tab_layout_sitemobile_sitetagcheckin_map').find('h3').click(function() {
   
        
        setTimeout(function(){
                            showMap();
                            infoBubbles.close();
                            google.maps.event.trigger(mapCheckin, 'resize');
                            setMapCenterZoomPoint(<?php echo json_encode(Engine_Api::_()->seaocore()->getProfileMapBounds($this->locations));?>,mapCheckin);
                            showMap();
                        }, 50);
			});
			filterMap('<?php echo $this->show_map_photo;?>');
  }

	infoBubbles = new InfoBubble({
		maxWidth: 400,
		maxHeight: 400,
		shadowStyle: 1,
		padding: 0,
		backgroundColor: '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.tooltip.bgcolor', '#ffffff');?>',
		borderRadius: 5,
		arrowSize: 10,
		borderWidth: 1,
		borderColor: '#2c2c2c',
		disableAutoPan: true,
		hideCloseButton: false,
		arrowPosition: 50,
		backgroundClassName: 'sitetag_checkin_map_tip',
		arrowStyle: 0
	});

  $(document).ready(function() {
    initializeMap();
  });

  function setMapCenterZoomPoint(bounds, mapcheckin) {
    if(bounds == '')
    return;
    if (bounds && bounds.min_lat && bounds.min_lng && bounds.max_lat && bounds.max_lng) {
      var bds = new google.maps.LatLngBounds(new google.maps.LatLng(bounds.min_lat, bounds.min_lng), new google.maps.LatLng(bounds.max_lat, bounds.max_lng));
    }
    if (bounds &&  bounds.center_lat &&  bounds.center_lng) {
      mapcheckin.setCenter(new google.maps.LatLng( bounds.center_lat,  bounds.center_lng), 4);
    } else {
      mapcheckin.setCenter(new google.maps.LatLng(lat, lng), 4);
    }
    if (bds) {
      mapcheckin.setCenter(bds.getCenter());
      mapcheckin.fitBounds(bds);
    }
  }

	var sitetagcheckin_location_flag = false;
	$(document.body).click(function(event) { 
    var el = $(event.target); 
		if(el.get('for')=='location_stcheckin_photo_tooltip'){
			sitetagcheckin_location_flag = false;
			return;
		}
		if(sitetagcheckin_location_flag == false) {
			if($.mobile.activePage.find('#setlocation_photo_suggest_tip'))
			  $.mobile.activePage.find('#setlocation_photo_suggest_tip').remove(); 
		}
    sitetagcheckin_location_flag = false;
	});

</script>