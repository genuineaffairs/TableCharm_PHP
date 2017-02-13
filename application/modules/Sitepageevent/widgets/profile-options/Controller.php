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
class Sitepageevent_Widget_ProfileOptionsController extends Engine_Content_Widget_Abstract {

  //ACTION FOR SHOWING THE OPTIONS OF THE EVENT ON EVENT VIEW PAGE
  public function indexAction() {

    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    $sitepageevent_profileOption = Zend_Registry::isRegistered('sitepageevent_profileOption') ? Zend_Registry::get('sitepageevent_profileOption') : null;

    //GET SITEPAGE SUBJECT
    $sitepage = Engine_Api::_()->getItem('sitepage_page', Engine_Api::_()->core()->getSubject('sitepageevent_event')->page_id);

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
    if (empty($sitepageevent_profileOption)) {
      return $this->setNoRender();
    }

    //GET NAVIGATION
    $this->view->gutterNavigation = $gutterNavigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepageevent_gutter');
    
    foreach ($gutterNavigation->getPages() as $page) {
      if (method_exists($page, 'getParams') && $page->getParams()['action'] == 'share') {
        $gutterNavigation->removePage($page);
      }
    }
  }

}

?>