<?php

class Ynevent_Widget_ProfileMapController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
			// Prepare data
          	$this->view->event = $event = Engine_Api::_()->core()->getSubject();
          	$st_address = "";
			if ($event->address != '')
				$st_address .= $event->address;
		
			if ($event->city != '')
				$st_address .= ", " . $event->city;
				
			if ($event->country != '')
				$st_address .= ", " . $event->country;
	
			if ($event->zip_code != '')
				$st_address .= ", " . $event->zip_code;
	
			$pos = strpos($st_address, ",");
			if ($pos === 0)
				$st_address = substr($st_address, 1);
			
			$this->view->fullAddress = $st_address;
	}
}