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
class Sitepageevent_Widget_ProfileEventsController extends Engine_Content_Widget_Abstract {

  //ACTION FOR SHOWING THE UPCOMING EVENTS ON PAGE PROFILE PAGE 
  public function indexAction() {

    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SUBJECT AND SITEPAGE ID
    $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    $sitepageevent_profileEvent = Zend_Registry::isRegistered('sitepageevent_profileEvent') ? Zend_Registry::get('sitepageevent_profileEvent') : null;
    $page_id = $sitepage->page_id;

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageevent")) {
        return $this->setNoRender();
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'secreate');
      if (empty($isPageOwnerAllow)) {
        return $this->setNoRender();
      }
    }
    //PACKAGE BASE PRIYACY END
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK
    
    //NO RENDER
    if (empty($sitepageevent_profileEvent)) {
      return $this->setNoRender();
    }

    //GET TAB ID
    $this->view->tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepageevent.profile-sitepageevents', $page_id, Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0));

    //SEARCH PARAMETER
    $params = array();
    $params['page_id'] = $page_id;
   // $params['view_action'] = 1;
    $params['limit'] = $this->_getParam('itemCount', 3);

    //MAKE PAGINATOR
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('events', 'sitepageevent')->widgetEventsData($params);

    //SET NO RENDER
    if (Count($paginator) <= 0) {
      return $this->setNoRender();
    }
  }

}

?>