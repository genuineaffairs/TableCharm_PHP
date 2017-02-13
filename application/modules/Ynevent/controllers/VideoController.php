<?php
class Ynevent_VideoController extends Core_Controller_Action_Standard {
  
	public function init(){
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			if (0 !== ($video_id = (int)$this -> _getParam('video_id')) && null !== ($video = Engine_Api::_() -> getItem('ynevent_video', $album_id))) {
				Engine_Api::_() -> core() -> setSubject($video);
			} else if (0 !== ($event_id = (int)$this -> _getParam('even_id')) && null !== ($event = Engine_Api::_() -> getItem('even', $event_id))) {
				Engine_Api::_() -> core() -> setSubject($event);
			}
		}
		if (!Engine_Api::_() -> core() -> hasSubject()){
	     return $this->_helper->requireSubject->forward();
		}
 	}

	public function listAction(){
	   //Checking Ynvideo Plugin - View privacy
	   $video_enable = Engine_Api::_()->ynevent()->checkYouNetPlugin('ynvideo');
	   if(!$video_enable){
	    	return $this->_helper->requireSubject->forward();
	   }
	
	    //Get viewer, event, search form
	   $viewer = Engine_Api::_() -> user() -> getViewer();
	   $this -> view -> event = $event = Engine_Api::_() -> core() -> getSubject();
		 $this -> view -> form = $form = new Ynevent_Form_Video_Search;
			
	   if (!$this -> _helper -> requireAuth() -> setAuthParams($event, null, 'view') -> isValid()) {
				return;
			}
	    // Check create video authorization
	   $canCreate = $event -> authorization() -> isAllowed($viewer, 'video');
	   $levelCreate = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('event', $viewer, 'video');
	   
	      if ($canCreate && $levelCreate) {
	        $this -> view -> canCreate = true;
	      } else {
	        $this -> view -> canCreate = false;
	      }
	
	    //Prepare data filer
	    $params = array();
	    $params = $this->_getAllParams();
	    $params['parent_type'] = 'event';
	    $params['parent_id'] = $event->getIdentity();
	    $params['search'] = 1;
	    $params['limit'] = 12;
	    $form->populate($params);
	    $this->view->formValues = $form->getValues();
	    //Get data
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
	}

	public function manageAction(){
		//Checking Ynvideo Plugin - Viewer required -View privacy
		$video_enable = Engine_Api::_()->ynevent()->checkYouNetPlugin('ynvideo');
		if(!$video_enable){
			return $this->_helper->requireSubject->forward();
		}
		if (!$this -> _helper -> requireUser() -> isValid()) {
			return;
		}
		if (!$this -> _helper -> requireAuth() -> setAuthParams($event, null, 'view') -> isValid()) {
			return;
		}
    
		//Get viewer, event, search form
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> event = $event = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> form = $form = new Ynevent_Form_Video_Search;

	   // Check create video authorization
		$canCreate = $event -> authorization() -> isAllowed(null, 'video');
		$levelCreate = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('event', $viewer, 'video');

		if ($canCreate && $levelCreate) {
			$this -> view -> canCreate = true;
		} else {
			$this -> view -> canCreate = false;
		}

		//Prepare data filer
		$params = array();
		$params = $this->_getAllParams();
		$params['parent_type'] = 'event';
		$params['parent_id'] = $event->getIdentity();
		$params['user_id'] = $viewer->getIdentity();
		$params['limit'] = 12;
		$form->populate($params);
		$this->view->formValues = $form->getValues();
 		//Get data
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
 	}
}

?>
