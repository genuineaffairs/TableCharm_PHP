<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div id="region" class="seaocore_change_location">
  <?php if ($this->showSeperateLink): ?>
    <?php if (isset($this->getMyLocationDetailsCookie['location']) && !empty($this->getMyLocationDetailsCookie['location'])): ?>
      <span id="location_span" ><?php echo $this->getMyLocationDetailsCookie['location']; ?></span>
    <?php else: ?>
      <span id="location_span"><?php echo $this->translate("World"); ?></span>
    <?php endif; ?>
      &nbsp;<a href="#popupBasic" data-rel="popup" data-position-to="window" data-transition="pop" onclick='changeMyLocation()' class="change_location_link f_small">[<?php echo $this->translate("change my location"); ?>]</a> 
  <?php else: ?>
    <a class ="location_a_tag" href="#popupBasic" data-rel="popup" data-position-to="window" data-transition="pop" onclick='changeMyLocation()'>
      <?php if (isset($this->getMyLocationDetailsCookie['location']) && !empty($this->getMyLocationDetailsCookie['location'])): ?>
        <span id="location_span"><?php echo $this->getMyLocationDetailsCookie['location']; ?></span>
      <?php else: ?>
        <span id="location_span"><?php echo $this->translate("World"); ?></span>
      <?php endif; ?>
      <i></i>
    </a>
  <?php endif; ?>
</div>

<div data-role="popup" id="popupBasic" data-position-to="window" class="ui-content" data-overlay-theme="a" class="ui-corner-all">
  <a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a>

  <div class="change_location_form" id="dialog-form">
    <form method="post" action="" class="global_form" enctype="application/x-www-form-urlencoded" id="seaocore_change_my_location">
      <div>
        <div>
          <h3><?php echo $this->translate("Change My Location"); ?></h3>
          <p class="form-description"><?php echo $this->translate("Enter your location in the auto-suggest box. (e.g., CA or 94131, San Francisco)"); ?></p>
          <div class="form-elements">
            <div class="form-wrapper" id="changeMyLocationValue-wrapper">
              <div class="form-label" id="changeMyLocationValue-label"><label class="required" for="changeMyLocationValue"><?php echo $this->translate("Location: "); ?></label>

              </div>
              <div class="form-element" id="changeMyLocationValue-element">
                <input type="text" id="changeMyLocationValue" name="changeMyLocationValue" autocomplete="off" onkeypress="unsetLatLng();" value="<?php if (isset($this->getMyLocationDetailsCookie['location']) && !empty($this->getMyLocationDetailsCookie['location'])) {
    echo $this->getMyLocationDetailsCookie['location'];
  } ?>">
                <p id="changeMyLocationValueError" style="display:none; color:red;"><?php echo $this->translate("Please enter the valid location!"); ?></p> 
                <p id="changeMyLocationValueErrorGeo" style="display:none; color:red;"><?php echo $this->translate("Oops! Something went wrong. Please try again later."); ?></p> 
              </div>
            </div>

            <div class="form-wrapper" id="removeLocation-wrapper">
              <div id="removeLocation-label" class="form-label">&nbsp;</div>
              <div class="form-element" id="removeLocation-element">
                <input type="hidden" value="" name="removeLocation" >
                <input type="checkbox" id="removeLocation" name="removeLocation" data-role="none">
                <?php echo $this->translate("Remove my location."); ?>
              </div>
            </div>

            <input type="hidden" name="latitude" value="" id="latitude" />

            <input type="hidden" name="longitude" value="" id="longitude" />

            <div class="form-wrapper" id="buttons-wrapper" style="display: block;margin-bottom: 0;">
              <div class="form-label" id="buttons-label">&nbsp;</div>
              <div class="form-element" id="buttons-element">
                <button type="submit" id="execute" name="execute" data-theme="b" onclick="changeLocationSubmitForm();
                    return false;"><?php echo $this->translate("Change Location"); ?></button>
                                              or <a href="#" data-rel="back" data-role="button">
<?php echo $this->translate('Cancel')  ?>
                </a>
                
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
<br/>
<script type="text/javascript">

  function unsetLatLng() {
    $('#latitude').val('0');
    $('#longitude').val('0');
  }

  function changeMyLocation() {
    var autocomplete = new google.maps.places.Autocomplete($('#changeMyLocationValue').get(0));
    google.maps.event.addListener(autocomplete, 'place_changed', function() {
      var place = autocomplete.getPlace();
      if (!place.geometry) {
        return;
      }

      $.mobile.activePage.find('#latitude').val(place.geometry.location.lat());
      $.mobile.activePage.find('#longitude').val(place.geometry.location.lng());
    });
  }

  function changeLocationSubmitForm() {

    var removeLocationValue = $('#removeLocation').is(':checked');
    if (removeLocationValue) {
      $("div.seaocore_change_location").find("#location_span").html("World");
      $.cookie('seaocore_myLocationDetails', '', {expire: -1, path: sm4.core.baseUrl});

      $("#popupBasic").popup("close");
      return false;
    }

    var previousLocationValue = '<?php if (isset($this->getMyLocationDetailsCookie['location']) && !empty($this->getMyLocationDetailsCookie['location'])) {
  echo $this->getMyLocationDetailsCookie['location'];
} ?>'
    var newLocationValue = $.mobile.activePage.find('#changeMyLocationValue').val();

    if (previousLocationValue == newLocationValue && (newLocationValue != '' && newLocationValue != null)) {
    $("#popupBasic").popup("close");
      return false;
    }
    $.ajax({
      url: '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'location', 'action' => 'change-my-location'), "default"); ?>',
      method: 'post',
      data: {
        format: 'json',
        changeMyLocationValue: newLocationValue,
        latitude: $('#latitude').val(),
        longitude: $('#longitude').val(),
      },
      success: function(responseHTML) {
        if (responseHTML.error == 2) {
          $.mobile.activePage.find('#changeMyLocationValueErrorGeo').css('display', 'block');
        }
        else if (responseHTML.error == 1) {
          $.mobile.activePage.find('#changeMyLocationValueError').css('display', 'block');
        }
        else {
          if(typeof($.cookie('seaocore_myLocationDetails')) != 'undefined' && $.cookie('seaocore_myLocationDetails') != '')
             var myLocationDetails = jQuery.parseJSON($.cookie("seaocore_myLocationDetails"));

          if (myLocationDetails == '') {
            myLocationDetails = {};
          }

          myLocationDetails = $.extend(myLocationDetails, {
            latitude: responseHTML.latitude,
            longitude: responseHTML.longitude,
            location: responseHTML.location
          });

          if (typeof (myLocationDetails.locationmiles) == 'undefined' || myLocationDetails.locationmiles == null) {
            myLocationDetails = $.extend(myLocationDetails, {
              locationmiles: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>
            });
          }
          $("div.seaocore_change_location").find("#location_span").html(responseHTML.location);
          sm4.core.locationBased.setLocationCookies(myLocationDetails);
                             
          $("#popupBasic").popup("close");
        }
      }
    });
  }
</script> 
<style type="text/css">
  .pac-container{
    z-index:100000;
  }
</style>
  