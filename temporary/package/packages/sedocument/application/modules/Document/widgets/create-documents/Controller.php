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
class Document_Widget_CreateDocumentsController extends Engine_Content_Widget_Abstract
{ 
  public function indexAction()
  {
		//GET VIEWER DETAILS
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();
		if(!empty($viewer_id)) {
			$level_id = $viewer->level_id;
		}
		else {
			$level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
		}

		$create_permition = Zend_Registry::get('document_create_per');

		//IF CAN NOT VIEW AND CREATE DOCUMENTS THEN DON'T RENDER THE WIDGET
		$can_view = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'view');
		$can_create = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'create');
		if(empty($can_view) || empty($can_create) || empty($viewer_id)) {
			return $this->setNoRender();
		}

    //CHECK THAT VIEWER CAN CREATE NEW DOCUMENT
		if(empty($create_permition)) {
			return $this->setNoRender();
		}
  }
}
?>