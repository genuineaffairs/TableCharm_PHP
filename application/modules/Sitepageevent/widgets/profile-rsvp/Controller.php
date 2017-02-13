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
class Sitepageevent_Widget_ProfileRsvpController extends Seaocore_Content_Widget_Abstract {

  //ACTION FOR SHOWING THE EVENT RSVP ON EVENT VIEW PAGE
  public function indexAction() {

    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GETTING LOGGED IN USER INFO
    $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET SUBJECT OF SITEPAGEEVENT
    $sitepageevent_subject = Engine_Api::_()->core()->getSubject('sitepageevent_event');

    //GET ITEM OF SITEPAGE
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepageevent_subject->page_id);

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

    //GETTING ROWS
    $row = $sitepageevent_subject->membership()->getRow(Engine_Api::_()->user()->getViewer());
    if (!$row) {
      return $this->setNoRender();
    }
    $this->view->rsvp = $row->rsvp;
  }

}