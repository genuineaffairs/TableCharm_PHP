<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: WidgetsController.php 6590 2010-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_WidgetController extends Core_Controller_Action_Standard {

  //ACTION FOR RSVP
  public function profileRsvpAction() {

    //MAKE FORM
    $this->view->form = new Sitepageevent_Form_Rsvp();

    //GET THE SITEPAGEEVENT SUBJECT
    $sitepageevent = Engine_Api::_()->core()->getSubject();

    // PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $page_id = $sitepageevent->page_id;
      if (!empty($page_id)) {
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageevent")) {
          return $this->_forward('requireauth', 'error', 'core');
        }
      }
    } else {
      $page_id = $sitepageevent->page_id;
      if (!empty($page_id)) {
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'secreate');
      }
      if (empty($isPageOwnerAllow)) {
        return $this->_forward('requireauth', 'error', 'core');
      }
    }
    // PACKAGE BASE PRIYACY END
    
    // GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();

    // IF VIEWER IS NOT MEMBER OF EVENT, RETURN
    if (!$sitepageevent->membership()->isMember($viewer, true)) {
      return;
    }

    //GETTING ROWS
    $row = $sitepageevent->membership()->getRow($viewer);
    if ($row) {
      $this->view->rsvp = $row->rsvp;
    } else {
      return $this->_helper->viewRenderer->setNoRender(true);
    }

    // IF NOT POST OR FORM NOT VALID, RETURN
    if ($this->getRequest()->isPost()) {
      $option_id = $this->getRequest()->getParam('option_id');
      //SAVE RSVP
      $row->rsvp = $option_id;
      $row->save();
    }
  }

  //ACTION FOR PROFILE INFO
  public function profileInfoAction() {
  	
    // DON'T RENDER THIS IF NOT AUTHORIZED
    if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'view')->isValid())
      return $this->_helper->viewRenderer->setNoRender(true);
  }

  //ACTION FOR EVENT NOTIFICATION
  public function requestSitepageeventAction() {
  	
    $this->view->notification = $notification = $this->_getParam('notification');
  }

}

?>