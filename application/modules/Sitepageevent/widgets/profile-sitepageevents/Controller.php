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
class Sitepageevent_Widget_ProfileSitepageeventsController extends Engine_Content_Widget_Abstract {

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

    $get_event = Engine_Api::_()->getItemTable('sitepageevent_event')->getEventUserType();
    if (empty($get_event)) {
      return $this->setNoRender();
    }

    $this->view->allowView = false;
    if (!empty($viewer_id) && $viewer->level_id == 1) {
      $auth = Engine_Api::_()->authorization()->context;
      $this->view->allowView = true;
    }    

    //GET LAYOUT
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);

    //GET THE THIRD TYPE LAYOUT
    $this->view->widgets = $widgets = Engine_Api::_()->sitepage()->getwidget($layout, $page_id);

    //GET THE CURRENT TAB ID
    $this->view->content_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepageevent.profile-sitepageevents', $page_id, $layout);
    $sitepageevent_profilePageType = Zend_Registry::isRegistered('sitepageevent_profilePageType') ? Zend_Registry::get('sitepageevent_profilePageType') : null;
    if (empty($sitepageevent_profilePageType)) {
      return $this->setNoRender();
    }
    
    //WHICH LINK HAS BEEN CLICKED
    $this->view->clicked = $clicked = $this->_getParam('clicked_event', 'upcomingevent');

    //REQUEST TYPE IS AJAX OR NOT
    $this->view->isajax = $isajax = $this->_getParam('isajax', null);

    //GET THE TAB ID
    $this->view->module_tabid = $currenttabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    $this->view->getIsEvent = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepageevent');

    //SHOWING THE TOP TITLE IN CASE OF NON TABBED LAYOUT
    $this->view->showtoptitle = $showtoptitle = Engine_Api::_()->sitepage()->showtoptitle($layout, $page_id);

    //SHOWING THE EVENTS
    if (!empty($isajax) || ($currenttabid == $this->view->identity) || ($widgets == 0)) {
      //GET THE TAB ID
      $this->view->identity_temp = Zend_Controller_Front::getInstance()->getRequest()->getParam('identity_temp', $currenttabid);
      $this->view->show_content = true;

      //GET SEARCHING PARAMETERS
      $this->view->page = $page = $this->_getParam('page', 1);
      $this->view->search = $search = $this->_getParam('search');
      $this->view->selectbox = $selectbox = $this->_getParam('selectbox');
      $this->view->checkbox = $checkbox = $this->_getParam('checkbox');

      //MAKING THE SEACHING PARAMATER ARRAY
      $values = array();
      if (!empty($search)) {
        $values['search'] = $search;
      }

      if ($clicked == 'upcomingevent') {
        if ($selectbox == 'allmyevent' || $selectbox == 'eventilead') {
          if (empty($values['orderby'])) {
            $values['orderby'] = 'starttime';
            $selectbox = 'starttime';
          } else {
            $values['orderby'] = $selectbox;
          }
        }
        $values['orderby'] = $selectbox;
        $values['clicked'] = $clicked;
        $params['selectedbox'] = '';
      } else if ($clicked == 'pastevent') {
        $values['clicked'] = $clicked;
        $values['orderby'] = 'endtime';
        $params['selectedbox'] = '';
      } else if ($clicked == 'myevent') {
        $values['selectedbox'] = 'allmyevent';
        if ($selectbox == 'allmyevent') {
          $values['selectedbox'] = $selectbox;
        } elseif ($selectbox == 'eventilead') {
          $values['selectedbox'] = $selectbox;
          $values['user_id'] = $viewer_id;
        }
      }

      if (empty($selectbox)) {
        $values['orderby'] = 'starttime';
      }

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
      $paginator->setItemCountPerPage(10);
      $this->view->paginator->setCurrentPageNumber($page);

      //ADD COUNT TO TITLE IF CONFIGURED
      if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0) {
        $this->_childCount = $paginator->getTotalItemCount();
      }

      // MAKE PAGINATOR
      $currentPageNumber = $this->_getParam('page', 1);
      $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('events', 'sitepageevent')->getSitepageeventsPaginator($values);

      $this->view->myeventmessage = 0;
      $this->view->pastmessage = 0;
      $this->view->upcomingmessage = 0;
      
      if ($paginator->getTotalItemCount() == 0) {
        if ($clicked == 'myevent' && empty($search)) {
          $this->view->selectbox = '';
          $this->view->myeventmessage = 1;
        } elseif ($clicked == 'pastevent' && empty($search)) {
          $this->view->selectbox = '';
          $this->view->pastmessage = 1;
        } elseif ($clicked == 'upcomingevent' && empty($search)) {
          $this->view->selectbox = '';
          $this->view->upcomingmessage = 1;
        }
      }
      $paginator->setItemCountPerPage(10)->setCurrentPageNumber($currentPageNumber);
    } else {
      $this->view->show_content = false;
      $title_count = $this->_getParam('titleCount', false);
      $this->view->identity_temp = $this->view->identity;

      $values = array();
      $values['orderby'] = 'starttime';
      $values['page_id'] = $page_id;
      $values['show_count'] = 1;
      if ($can_edit) {
        $values['show_event'] = 0;
        $paginator = Engine_Api::_()->getDbTable('events', 'sitepageevent')->getSitepageeventsPaginator($values);
      } else {
        $values['show_event'] = 1;
        $values['event_owner_id'] = $viewer_id;
        $paginator = Engine_Api::_()->getDbTable('events', 'sitepageevent')->getSitepageeventsPaginator($values);
      }
    }
    $this->_childCount = $paginator->getTotalItemCount();
    
    // If we cannot find clicked_event param, set page load calendar.
    $real_clicked_event = $this->_getParam('clicked_event', null);
    $this->view->isCalendar = $calendar = $this->_getParam('calendar') || empty($real_clicked_event);
  }

  public function getChildCount() {
  	
    return $this->_childCount;
  }

}

?>