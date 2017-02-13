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
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&sensor=true&key=$apiKey");?>

<div id="region" class="seaocore_change_location">
    <?php if($this->showSeperateLink): ?>
        <?php if (isset($this->getMyLocationDetailsCookie['location']) && !empty($this->getMyLocationDetailsCookie['location'])): ?>
            <span><?php echo $this->getMyLocationDetailsCookie['location']; ?></span>
        <?php else: ?>
            <span><?php echo $this->translate("World"); ?></span>
        <?php endif; ?>
           &nbsp;<a href='javascript:void(0)' onclick='changeMyLocation()' class="change_location_link f_small">[<?php echo $this->translate("change my location"); ?>]</a>    
    <?php else: ?>
        <a href='javascript:void(0)' onclick='changeMyLocation()'>
            <?php if (isset($this->getMyLocationDetailsCookie['location']) && !empty($this->getMyLocationDetailsCookie['location'])): ?>
                <span><?php echo $this->getMyLocationDetailsCookie['location']; ?></span>
            <?php else: ?>
                <span><?php echo $this->translate("World"); ?></span>
            <?php endif; ?>
            <i></i>
        </a>
    <?php endif; ?>
</div>

<div style="display:none;" id="changeMyLocation">
  <div class="change_location_form">
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
                              <input type="text" id="changeMyLocationValue" name="changeMyLocationValue" autocomplete="off" onkeypress="unsetLatLng();" value="<?php if (isset($this->getMyLocationDetailsCookie['location']) && !empty($this->getMyLocationDetailsCookie['location'])) { echo $this->getMyLocationDetailsCookie['location']; } ?>">
                              <p id="changeMyLocationValueError" style="display:none; color:red;"><?php echo $this->translate("Please enter the valid location!"); ?></p> 
                              <p id="changeMyLocationValueErrorGeo" style="display:none; color:red;"><?php echo $this->translate("Oops! Something went wrong. Please try again later."); ?></p> 
                          </div>
                      </div>
                      
                      <div class="form-wrapper" id="removeLocation-wrapper">
                        <div id="removeLocation-label" class="form-label">&nbsp;</div>
                        <div class="form-element" id="removeLocation-element">
                            <input type="hidden" value="" name="removeLocation">
                            <input type="checkbox" id="removeLocation" name="removeLocation">
                                <?php echo $this->translate("Remove my location.");?>
                        </div>
                      </div>
                      
                      <input type="hidden" name="latitude" value="" id="latitude" />

                      <input type="hidden" name="longitude" value="" id="longitude" />

                      <div class="form-wrapper" id="buttons-wrapper" style="display: block;margin-bottom: 0;">
                          <div class="form-label" id="buttons-label">&nbsp;</div>
                          <div class="form-element" id="buttons-element">
                              <button type="submit" id="execute" name="execute" onclick="changeLocationSubmitForm($('seaocore_change_my_location'));
      return false;"><?php echo $this->translate("Change Location"); ?></button>
                              or <a href="javascript:void(0)" onclick="parent.Smoothbox.close();"><?php echo $this->translate("cancel"); ?></a>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </form>
    </div>
</div>

<script type="text/javascript">
    
    function unsetLatLng() {
        $('latitude').value = 0;
        $('longitude').value = 0;
    }
    
    function changeMyLocation() {    
        Smoothbox.open('<div id="changeMyLocationHTML">' + $('changeMyLocation').innerHTML + '</div>');
        var autocomplete = new google.maps.places.Autocomplete($('changeMyLocationHTML').getElementById('changeMyLocationValue'));
        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                return;
            }

            $('latitude').value = place.geometry.location.lat();
            $('longitude').value = place.geometry.location.lng();
        });
    } 
   
    function changeLocationSubmitForm(formObject) { 
        
        var removeLocationValue = $('changeMyLocationHTML').getElementById('removeLocation').checked;
        if(removeLocationValue) {
            Cookie.write('seaocore_myLocationDetails', '', {duration: -1, path:en4.core.baseUrl});
        
            parent.Smoothbox.close();
            window.location.reload();
            return false;
        }
        
        var previousLocationValue = '<?php if (isset($this->getMyLocationDetailsCookie['location']) && !empty($this->getMyLocationDetailsCookie['location'])) { echo $this->getMyLocationDetailsCookie['location']; } ?>'
        var newLocationValue = $('changeMyLocationHTML').getElementById('changeMyLocationValue').value;
        
        if(previousLocationValue == newLocationValue && (newLocationValue != '' && newLocationValue != null)) {
            parent.Smoothbox.close();
            return false;
        }
        var request = new Request.JSON({
            url: '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'location', 'action' => 'change-my-location'), "default"); ?>',
            method: 'post',
            data: {
                format: 'json',
                changeMyLocationValue: newLocationValue,
                latitude: $('changeMyLocation').getElementById('latitude').value,
                longitude: $('changeMyLocation').getElementById('longitude').value,
            },
            //responseTree, responseElements, responseHTML, responseJavaScript
            onSuccess: function(responseJSON) { 
                if(responseJSON.error == 2) {
                    $('changeMyLocationHTML').getElementById('changeMyLocationValueErrorGeo').style.display = 'block';
                }   
                else if(responseJSON.error == 1) {
                    $('changeMyLocationHTML').getElementById('changeMyLocationValueError').style.display = 'block';
                }    
                else {
                    
                    var myLocationDetails = JSON.parse(Cookie.read('seaocore_myLocationDetails'));

                    if(myLocationDetails == '') {
                        myLocationDetails = {};
                    }
                    
                   myLocationDetails = $merge(myLocationDetails,{
                        latitude : responseJSON.latitude,
                        longitude: responseJSON.longitude,
                        location:responseJSON.location
                   });
                   
                   if(typeof(myLocationDetails.locationmiles) == 'undefined' || myLocationDetails.locationmiles == null) {
                   myLocationDetails = $merge(myLocationDetails,{
                        locationmiles : <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>
                   });
                   }                  
                    
                    en4.seaocore.locationBased.setLocationCookies(myLocationDetails);
                            
                    parent.Smoothbox.close();
                    window.location.reload();
                }
            }
        });
        request.send();
    }
</script>  