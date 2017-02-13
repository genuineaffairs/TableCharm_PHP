 <?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
class Sitepageevent_Widget_SitemobileProfileSitepageeventsController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  //ACTION FOR SHOWING THE EVENTS ON PAGE PROFILE PAGE
  public function indexAction() {

    // SET NO RENDER IF NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }
    
    // GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    
    //GET SUBJECT
    $this->view->sitepage_subject = $sitepage_subject = Engine_Api::_()->core()->getSubject('sitepage_page');

    //GET PAGE ID
    $page_id = $sitepage_subject->page_id;

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage_subject->package_id, "modules", "sitepageevent")) {
        return $this->setNoRender();
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage_subject, 'secreate');
      if (empty($isPageOwnerAllow)) {
        return $this->setNoRender();
      }
    }
    //PACKAGE BASE PRIYACY END
    
    //TOTAL EVENT
    $this->view->eventCount = Engine_Api::_()->sitepage()->getTotalCount($page_id, 'sitepageevent', 'events');     
    $eventCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'secreate');
       
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = $can_edit = 0;
    } else {
      $this->view->can_edit = $can_edit = 1;
    }

    if (empty($eventCreate) && empty($this->view->eventCount) && empty($can_edit) && !(Engine_Api::_()->sitepage()->showTabsWithoutContent())) {
      return $this->setNoRender();
    } 
    //END MANAGE-ADMIN CHECK
    
    //GET VIEWER INFORMATION
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    $this->view->allowView = false;
    if (!empty($viewer_id) && $viewer->level_id == 1) {
      $auth = Engine_Api::_()->authorization()->context;
      $this->view->allowView = true;
    }    

		//MAKING THE SEACHING PARAMATER ARRAY
		$values = array();
		$values['orderby'] = 'starttime';

		//START MANAGE-ADMIN CHECK
		$isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'secreate');
		if (empty($isManageAdmin) && empty($can_edit)) {
			$this->view->can_create = 0;
		} else {
			$this->view->can_create = 1;
		}
		//END MANAGE-ADMIN CHECK

		$values['page_id'] = $page_id;
		if ($can_edit) {
			$values['show_event'] = 0;
			$this->view->paginator = $paginator = Engine_Api::_()->getDbTable('events', 'sitepageevent')->getSitepageeventsPaginator($values);
		} else {
			$values['show_event'] = 1;
			$values['event_owner_id'] = $viewer_id;
			$this->view->paginator = $paginator = Engine_Api::_()->getDbTable('events', 'sitepageevent')->getSitepageeventsPaginator($values);
		}

		//EVENTS PER PAGE
		$this->view->paginator->setItemCountPerPage(10)->setCurrentPageNumber($this->_getParam('page', 1));
		$this->_childCount = $paginator->getTotalItemCount();
  }

  public function getChildCount() {
  	
    return $this->_childCount;
  }

}