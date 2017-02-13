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
class Sitepagedocument_Widget_ViewrecentSitepagedocumentsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $document_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('document_id');
  
    $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', $document_id);

    if (empty($sitepagedocument)) {
      return $this->setNoRender();
    }
    $this->view->page_id = $sitepagedocument->page_id;
     $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');
    //GET SUBJECT AND SITEPAGE ID
    $this->view->sitepage_subject = $sitepage_subject = Engine_Api::_()->getItem('sitepage_page', $sitepagedocument->page_id);

    //PACKAGE BASE PRIYACY START
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

    //SSL WORK
		$this->view->https = 0;
		if (!empty($_SERVER["HTTPS"]) && 'on' == strtolower($_SERVER["HTTPS"])) {
			$this->view->https = 1;
    }

    $this->view->manifest_path = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.manifestUrl', "page-documents");

    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK

		//FETCH DOCUMENTS
		$params = array();
		$params['page_id'] = $sitepage_subject->page_id;
		$params['limit'] = $this->_getParam('itemCount', 3);
    $params['view_action'] = 1;
    $params['document_id'] = $sitepagedocument->document_id;
    $this->view->documentSitepage = $documentSitepage = Engine_Api::_()->getDbtable('documents', 'sitepagedocument')->widgetDocumentsData($params);
    $this->view->documentSitepageTotal = Count($documentSitepage);

    if (Count($documentSitepage) <= 0) {
      return $this->setNoRender();
    }
  }

}
?>