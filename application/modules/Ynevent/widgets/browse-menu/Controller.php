<?php

class Ynevent_Widget_BrowseMenuController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        // Get navigation
        $this->view->navigation = $navigation = Engine_Api::_()
                ->getApi('menus', 'core')
                ->getNavigation('ynevent_main');

		
		$p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
	    $active = $navigation->findOneBy('active', true);
	    if( empty($active) || $active->getRoute() !== 'event_general' ) {
	      $filter = !empty($p['filter']) ? $p['filter'] : 'future';
	      //if( $filter != 'past' && $filter != 'future' ) $filter = 'future';
	    
	      foreach( $navigation->getPages() as $page ) {
	        if( ($page->label == "Upcoming Events" && $filter == "future") || 
	            ($page->route == "event_past" && $filter == "past")) {
	          $page->active = true;
	        }
	      }
	      
	    }
	        
	    $this->view->parent_type = $p['parent_type'];
		
    }

}
