<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MobiController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_MobiController extends Core_Controller_Action_Standard {

  public function init() {
  	
    //GET PAGE ID
    $page_id = $this->_getParam('page_id');

    //PACKAGE BASE PRIYACY START
    if (!empty($page_id)) {
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageevent")) {
          return $this->_forward('requireauth', 'error', 'core');
        }
      } else {
        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'secreate');
        if (empty($isPageOwnerAllow)) {
          return $this->_forward('requireauth', 'error', 'core');
        }
      }
    }
    //PACKAGE BASE PRIYACY END
    else {
      if ($this->_getParam('event_id') != null) {
        $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', $this->_getParam('event_id'));
        $page_id = $sitepageevent->page_id;
      }
    }
    
    //GET EVENT ID
    $event_id = $this->_getParam('event_id');
    if ($event_id) {
      $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', $event_id);
      if ($sitepageevent) {
        Engine_Api::_()->core()->setSubject($sitepageevent);
      }
    }
  }

  //ACTION FOR VIEW THE EVENT
  public function viewAction() {
    
    //IF SITEPAGEEVENT SUBJECT IS NOT THEN RETURN
    if (!$this->_helper->requireSubject('sitepageevent_event')->isValid())
      return;

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET EVENT ITEM
    $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', $this->getRequest()->getParam('event_id'));

    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepageevent->page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forward('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK

    //CHECKING THE USER HAVE THE PERMISSION TO VIEW THE EVENT OR NOT
    if ($viewer_id != $sitepageevent->user_id && $can_edit != 1 && $sitepageevent->search != 1) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    //INCREMENT IN NUMBER OF VIEWS
    $owner = $sitepageevent->getOwner();
    if (!$owner->isSelf($viewer)) {
      $sitepageevent->view_count++;
    }

    //SAVE VALUES
    $sitepageevent->save();

    //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }
  }
  
}

?>