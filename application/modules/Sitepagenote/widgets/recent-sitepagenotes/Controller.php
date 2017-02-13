<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_Widget_RecentSitepagenotesController extends Engine_Content_Widget_Abstract {

	//ACTION FOR SHOWING THE MOST RECENT NOTES ON PAGE PROFILE PAGE
  public function indexAction() {
  	
    //GET SITEPAGE SUBJECT
    $this->view->sitepage_subject = $sitepage_subject = Engine_Api::_()->core()->getSubject('sitepage_page');

    $sitepagenote_recentContent = Zend_Registry::isRegistered('sitepagenote_recentContent') ? Zend_Registry::get('sitepagenote_recentContent') : null;

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage_subject->package_id, "modules", "sitepagenote")) {
        return $this->setNoRender();
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage_subject, 'sncreate');
      if (empty($isPageOwnerAllow)) {
        return $this->setNoRender();
      }
    }
    //PACKAGE BASE PRIYACY END    
    
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK

    $getLevelAuth = Engine_Api::_()->sitepagenote()->getLevelAuth();
    if (empty($getLevelAuth)) {
      return $this->setNoRender();
    }

    //NO RENDER
    if (empty($sitepagenote_recentContent)) {
      return $this->setNoRender();
    }

    //SEARCH PARAMETER
    $params = array();
		$params['page_id'] = $sitepage_subject->page_id;
    $params['profile_page_widget'] = 1;
		$params['limit'] = $this->_getParam('itemCount', 3);

    //MAKE PAGINATOR
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('notes', 'sitepagenote')->widgetNotesData($params);
    
    //NO RENDER
    if (Count($paginator) <= 0) {
      return $this->setNoRender();
    }
  }

}

?>