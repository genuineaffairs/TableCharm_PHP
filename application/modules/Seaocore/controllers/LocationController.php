<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Seaocore
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: LocationController.php 2010-11-18 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Seaocore_LocationController extends Core_Controller_Action_Standard {

    public function changeMyLocationAction() {

        $location = $_POST['changeMyLocationValue'];
        $lat = (isset($_POST['latitude']) && !empty($_POST['latitude'])) ? $_POST['latitude'] : 0;
        $lng = (isset($_POST['longitude']) && !empty($_POST['longitude'])) ? $_POST['longitude'] : 0;
        $overQueryLimit = 0;
        if (!empty($location) && $location !== "world" && (empty($lat) || empty($lng))) {

            $urladdress = str_replace(" ", "+", $location);

            //INITIALIZE DELAY IN GEOCODE SPEED
            $delay = 0;

            //ITERATE THROUGH THE ROWS, GEOCODEDING EACH ADDRESS
            $geocode_pending = true;
            while ($geocode_pending) {
                $key = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
                if (!empty($key)) {
                    $request_url = "https://maps.googleapis.com/maps/api/place/textsearch/json?query=$urladdress&sensor=true&key=$key";
                } else {
                    $request_url = "https://maps.googleapis.com/maps/api/geocode/json?address=$urladdress&sensor=true";
                }
                $ch = curl_init();
                $timeout = 5;
                curl_setopt($ch, CURLOPT_URL, $request_url);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                ob_start();
                curl_exec($ch);
                curl_close($ch);
                $json_resopnse = Zend_Json::decode(ob_get_contents());
                ob_end_clean();
                $status = $json_resopnse['status'];
                if (strcmp($status, "OK") == 0) {
                    $geocode_pending = false;
                    $result = $json_resopnse['results'];
                    //FORMAT: LONGIDUDE, LATITUDE, ALTITUDE
                    $lat = $result[0]['geometry']['location']['lat'];
                    $lng = $result[0]['geometry']['location']['lng'];
                } else if (strcmp($status, "620") == 0) {
                    //sent geocodes too fast
                    $delay += 100000;
                } else {
                    //FAILURE TO GEOCODE
                    $geocode_pending = false;
                    $overQueryLimit = 1;
                    //echo "Address " . $location . " failed to geocoded. ";
                    //echo "Received status " . $status . "\n";
                }
                usleep($delay);
            }
        }

        if($overQueryLimit) {
            $this->view->error = 2;
        }
        elseif (empty($lat) || empty($lng)) {
            $this->view->error = 1;
        }
        else {
            $getMyLocationDetailsCookie = Engine_Api::_()->seaocore()->getMyLocationDetailsCookie();
            $this->view->location = $getMyLocationDetailsCookie['location'] = $location;
            $this->view->latitude = $getMyLocationDetailsCookie['latitude'] = $lat;
            $this->view->longitude = $getMyLocationDetailsCookie['longitude'] = $lng;
            $this->view->error = 0;
        }
    }

}
