<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagelikebox
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-10-10 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
  $this->headLink()
  	->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Sitepage/externals/styles/sitepage-tooltip.css');
?>
<script type="text/javascript">

	    var contentinformtion;
      var page_showtitle;
  if(contentinformtion == 0) {
		if($('global_content').getElement('.layout_activity_feed')) {
			$('global_content').getElement('.layout_activity_feed').style.display = 'none';
		}
		if($('global_content').getElement('.layout_core_profile_links')) {
			$('global_content').getElement('.layout_core_profile_links').style.display = 'none';
		}
		if($('global_content').getElement('.layout_sitepage_info_sitepage')) {
			$('global_content').getElement('.layout_sitepage_info_sitepage').style.display = 'none';
		}

		if($('global_content').getElement('.layout_sitepage_location_sitepage')) {
			$('global_content').getElement('.layout_sitepage_location_sitepage').style.display = 'none';
		}
  }
</script>

<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>

<?php $sitepage=$this->sitepage;  ?>
<script type="text/javascript">
  var myLatlng;
  function initialize() {
    var myLatlng = new google.maps.LatLng(<?php echo $this->location->latitude; ?>,<?php echo $this->location->longitude; ?>);
    var myOptions = {
      zoom: <?php echo $this->location->zoom; ?> ,
      center: myLatlng,
      navigationControl: true,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

    var contentString = '<div id="content">'+
      '<div id="siteNotice">'+
      '</div>'+''+
				'<div class="splb_mtt">'+
		    	"<?php echo $this->string()->escapeJavascript($sitepage->getTitle())?>"+
	      '</div>'+
				'<div class="splb_mtl">'+
					"<?php echo $this->string()->escapeJavascript( $this->location->location); ?>"+
		     '</div>'+
      '</div>';


    var infowindow = new google.maps.InfoWindow({
      content: contentString ,
      size: new google.maps.Size(250,50)

    });

    var marker = new google.maps.Marker({
      position: myLatlng,
      map: map,
      title: "<?php echo str_replace('"', ' ',$sitepage->getTitle())?>"
    });
    google.maps.event.addListener(marker, 'click', function() {

      infowindow.open(map,marker);
    });


      $('map_likebox_active').addEvent('click', function() {  
      google.maps.event.trigger(map, 'resize');
      map.setZoom(<?php echo $this->location->zoom; ?> );
      map.setCenter(myLatlng);
    });
  

    google.maps.event.addListener(map, 'click', function() {

      infowindow.close();
			google.maps.event.trigger(map, 'resize');
      map.setZoom(<?php echo $this->location->zoom; ?> );
      map.setCenter(myLatlng);
    });


  }

</script>
<div id="map_canvas"></div>


<style type="text/css">
  #map_canvas {
    width: 100%;
    height: 300px;
    margin:0 auto;
  }
  #map_canvas > div{
    position: static !important;
    height: 300px;
  }
  #infoPanel {
    float: left;
    margin-left: 10px;
  }
  #infoPanel div {
    margin-bottom: 5px;
  }
</style>
<script type="text/javascript">
  window.addEvent('domready',function(){
    initialize();
  });
</script>


	<script type="text/javascript">
	 //prev_tab_id = '<?php //echo $this->content_id; ?>';
	 $$('.tab_<?php echo $this->identity_temp; ?>').addEvent('click', function()
	 {
	    if(page_showtitle != 0) {
	    	if($('profile_status')) {
			  	$('profile_status').innerHTML = "<h2><?php echo $this->string()->escapeJavascript($this->sitepage->getTitle())?><?php echo $this->translate(' &raquo; Map ');?></h2>";
	    	}
				if($('layout_map')) {
				  $('layout_map').style.display = 'block';
				}
	  	}
	  	if($('global_content').getElement('.layout_sitepage_photorecent_sitepage')) {
				$('global_content').getElement('.layout_sitepage_photorecent_sitepage').style.display = 'none';
			}
	    if($('global_content').getElement('.layout_sitepage_location_sitepage')) {
					 $('global_content').getElement('.layout_sitepage_location_sitepage').style.display = 'block';
			}
			if($('global_content').getElement('.layout_activity_feed')) {
				$('global_content').getElement('.layout_activity_feed').style.display = 'none';
			}
			if($('global_content').getElement('.layout_core_profile_links')) {
				$('global_content').getElement('.layout_core_profile_links').style.display = 'none';
			}
			if($('global_content').getElement('.layout_sitepage_info_sitepage')) {
				$('global_content').getElement('.layout_sitepage_info_sitepage').style.display = 'none';
			}
			if($('global_content').getElement('.layout_sitepage_overview_sitepage')) {
				$('global_content').getElement('.layout_sitepage_overview_sitepage').style.display = 'none';
		  }
	  	$('id_' + <?php echo $this->content_id ?>).style.display = "block";
	    if ($('id_' + prev_tab_id) != null && prev_tab_id != 0 && prev_tab_id != '<?php echo $this->content_id; ?>') {
	      $('global_content').getElement('.'+ prev_tab_class).style.display = 'none';
	    }
	    prev_tab_id = '<?php echo $this->content_id; ?>';
	  	prev_tab_class = 'layout_sitepage_location_sitepage';

	    if(page_showtitle == 1 ) {
setLeftLayoutForPage(); 
	    } else if(page_showtitle == 0 ) {
		    if ($$('.layout_left')){
		      $$('.layout_left').setStyle('display', 'none');
		      if($('thumb_icon')) {
		       $('thumb_icon').style.display = 'block';
		      }
		    }		if ($$('.layout_right')){
      $$('.layout_right').setStyle('display', 'none');
      if($('thumb_icon')) {
       $('thumb_icon').style.display = 'block';
      }
    }		

	    }
	  });
	</script>