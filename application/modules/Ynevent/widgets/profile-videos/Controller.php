<?php

class Ynevent_Widget_ProfileVideosController extends Engine_Content_Widget_Abstract
{
	protected $_childCount;
	
	public function indexAction() {
	// Don't render this if not authorized
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!Engine_Api::_()->core()->hasSubject()) {
			return $this->setNoRender();
		}
		
		// Get subject and check auth
		$subject = Engine_Api::_()->core()->getSubject('event');
		if (!$subject->authorization()->isAllowed($viewer, 'view')) {
			return $this->setNoRender();
		}
		
		// Prepare data
		$this->view->event = $event = $subject;
			
	    //Get viewer, event, search form
	   	$viewer = Engine_Api::_() -> user() -> getViewer();
	   	$this -> view -> event = $event = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> form = $form = new Ynevent_Form_Video_Search;
	
		if ( !$event->authorization()->isAllowed($viewer, 'view') ) { return; }
	   
	    // Check create video authorization
		$canCreate = $event -> authorization() -> isAllowed($viewer, 'video');
		$levelCreate = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('event', $viewer, 'video');

		//print_r($canCreate); exit;
		
		if ($canCreate && $levelCreate) {
			$this -> view -> canCreate = true;
		} else {
			$this -> view -> canCreate = false;
		}
	
	    //Prepare data filer
	    $params = array();
	    $params = $this->_getAllParams();
	    $params['title'] = '';
	    $params['parent_type'] = 'event';
	    $params['parent_id'] = $event->getIdentity();
	    $params['search'] = 1;
	    $params['limit'] = 12;
	    $form->populate($params);
	    $this->view->formValues = $form->getValues();
	    //Get data
	    //print_r($params); exit;
	    $this -> view -> paginator = $paginator = Engine_Api::_() -> ynvideo() -> getVideosPaginator($params);
	    if(!empty($params['orderby'])){
	      switch($params['orderby']){
	        case 'most_liked':
	          $this->view->infoCol = 'like';
	          break;
	        case 'most_commented':
	          $this->view->infoCol = 'comment';
	          break;
	        default:
	          $this->view->infoCol = 'view';
	          break;
	      }
	    }
	    
		// Add count to title if configured
	    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
	      $this->_childCount = $paginator->getTotalItemCount();
	    }
	}
	
	public function getChildCount()
	{
	    return $this->_childCount;
	}
}