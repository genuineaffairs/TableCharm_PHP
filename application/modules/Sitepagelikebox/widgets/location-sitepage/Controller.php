<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagelikebox
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-10-10 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagelikebox_Widget_LocationSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $viewer = Engine_Api::_()->user()->getViewer();

    //check location map enable /disable
    $check_location = Engine_Api::_()->sitepage()->enableLocation();
		$likebox_location = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagelikebox.location', null);
    if (!Engine_Api::_()->core()->hasSubject() || !$check_location) {
      return $this->setNoRender();
    }
    // Get subject and check auth
    $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin) || empty($likebox_location)) {
      return $this->setNoRender();
    }
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'map');
    if (empty($isManageAdmin) && empty($likebox_location)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK
    $value['id'] = $sitepage->getIdentity();
    $this->view->location = $location =  Engine_Api::_()->getDbtable('locations', 'sitepage')->getLocation($value);
    if (empty($location) || empty($likebox_location)) {
      return $this->setNoRender();
    }
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
    $this->view->content_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.location-sitepage', $sitepage->page_id, $layout);
    $this->view->showtoptitle = $showtoptitle = Engine_Api::_()->sitepage()->showtoptitle($layout, $sitepage->page_id);
    $this->view->identity_temp = $this->view->identity;
  }
}
?>