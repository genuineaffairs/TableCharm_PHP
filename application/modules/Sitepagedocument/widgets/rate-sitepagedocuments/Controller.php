<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagedocument_Widget_RateSitepagedocumentsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //GET SUBJECT AND SITEPAGE ID
    $sitepage_subject = Engine_Api::_()->core()->getSubject('sitepage_page');

    // PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage_subject->package_id, "modules", "sitepagedocument")) {
        return $this->setNoRender();
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage_subject, 'sdcreate');
      if (empty($isPageOwnerAllow)) {
        return $this->setNoRender();
      }
    }
    // PACKAGE BASE PRIYACY END

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK

    $sitepagedocument_ratetype = Zend_Registry::isRegistered('sitepagedocument_ratetype') ? Zend_Registry::get('sitepagedocument_ratetype') : null;
    if (empty($sitepagedocument_ratetype)) {
      return $this->setNoRender();
    }

    //CHECK THAT RATING IS VIEABLE OR NOT
    $this->view->show_rate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.rating', 1);

    if (empty($this->view->show_rate)) {
      return $this->setNoRender();
    }

		//FETCH DOCUMENTS
		$params = array();
		$params['page_id'] = $sitepage_subject->page_id;
		$params['orderby'] = 'rating DESC';
		$params['zero_count'] = 'rating';
		$params['limit'] = $this->_getParam('itemCount', 3);
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('documents', 'sitepagedocument')->widgetDocumentsData($params);

    if (Count($paginator) <= 0) {
      return $this->setNoRender();
    }
  }

}
?>