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
class Document_Widget_DayItemDocumentController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//GET DOCUMENT ID
		$document_id = Engine_Api::_()->getDbtable('documents', 'document')->getItemOfDay();

    //DONT RENDER IF DOCUMENT ID IS ZERO
    if (empty($document_id)) {
      return $this->setNoRender();
    }
	
		//GET DOCUMENT OF THE DAY DATA
		$this->view->dayitem = Engine_Api::_()->getItem('document', $document_id);
  }
}
?>