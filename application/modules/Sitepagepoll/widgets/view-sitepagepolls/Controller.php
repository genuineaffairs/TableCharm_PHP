<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagepoll_Widget_ViewSitepagepollsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //GET SITEPAGE SUBJECT AND PAGE ID
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    $page_id = $sitepage->page_id;

    // PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagepoll")) {
        return $this->setNoRender();
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'splcreate');
      if (empty($isPageOwnerAllow)) {
        return $this->setNoRender();
      }
    }
    // PACKAGE BASE PRIYACY END
    $allowPackages = Zend_Registry::isRegistered('sitepagepoll_allowPackages') ? Zend_Registry::get('sitepagepoll_allowPackages') : null;

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK
    //NUMBER OF POLLS IN LISTING
    $total_sitepagepolls = $this->_getParam('itemCount', 3);
    $values = array();
    $values['page_id'] = $page_id;
    $values['profile_page_widget'] = 1;
    $values['total_sitepagepolls'] = $total_sitepagepolls;
    $this->view->listViewedPolls = $listViewedPolls = Engine_Api::_()->getDbtable('polls', 'sitepagepoll')->getPollListing('Most Viewed', $values);

    //NO RENDER IF NO POLLS
    if (Count($listViewedPolls) <= 0) {
      return $this->setNoRender();
    }

    if (empty($allowPackages)) {
      return $this->setNoRender();
    }
  }

}
?>