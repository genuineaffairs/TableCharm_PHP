<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Document_Widget_SponsoredDocumentsController extends Engine_Content_Widget_Abstract {

	public function indexAction() {

		//FETCH DOCUMENTS DATA
		$params = array();
		$params['sponsored'] = 1;
		$this->view->category_id = $params['category_id'] = $this->_getParam('category_id', 0);
		$this->view->interval = $this->_getParam('interval', 300);
		$this->view->titletruncation = $this->_getParam('truncation', 18);

		//GET DOCUMENT TABLE
		$documentTable = Engine_Api::_()->getDbtable('documents', 'document');

		//GET SPONSORED DOCUMENTS COUNT
		$totalDocument = $documentTable->widgetDocumentsData($params);

		//DOCUMENTS LIMIT
		$this->view->limit = $params['limit'] = $this->_getParam('itemCount', 4);

		//NO RENDER IF SPONSORED DOCUMENTS ARE ZERO
		$this->view->totalSponsoredDocuments = Count($totalDocument);
		if (($this->view->totalSponsoredDocuments <= 0)) {
			return $this->setNoRender();
		}

    //SEND DOCUMENT DATA TO TPL
    $this->view->documents = $documentTable->widgetDocumentsData($params);
  }
}
?>