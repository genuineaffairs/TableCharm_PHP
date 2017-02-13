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
class Document_Widget_DocumentOwnerDocumentsController extends Engine_Content_Widget_Abstract
{ 
	public function indexAction()
  {
    //SET NO RENDER IF NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //SET NO RENDER IF SUBJECT OF DOCUMENT IS NOT FIND
    $document = Engine_Api::_()->core()->getSubject('document');
		if(empty($document)) {
			return $this->setNoRender();
		}

    //GET OWNER INFORMATION
    $this->view->owner = $document->getOwner();

    //FETCH DOCUMENTS
    $params = array();
    $params['orderby'] = 'document_id DESC';
    $params['owner_id'] = $document->owner_id;
		$params['document_id'] = $document->document_id;
    $params['limit'] = $this->_getParam('itemCount', 3);
    $this->view->paginator = Engine_Api::_()->getDbtable('documents', 'document')->widgetDocumentsData($params);

		//SET NO RENDER IF DATA IS EMPTY
    if (Count($this->view->paginator) <= 0) {
      return $this->setNoRender();
    }
  }
}
?>