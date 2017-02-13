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
class Document_Widget_ZeroDocumentsController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

		//GET VIEWER DETAIL
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//GET LEVEL ID
		if(!empty($viewer_id)) {
			$level_id = $viewer->level_id;
		}
		else {
			$level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
		}

    //FETCH DOCUMENTS
    $params = array();
    $params['limit'] = 1;
    $paginator = Engine_Api::_()->getDbtable('documents', 'document')->widgetDocumentsData($params);
		$this->view->total_results = Count($paginator);

		//DOCUMENT VIEW PRIVACY
		$can_view = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'view');

		//DON'T RENDER IF CAN'T VIEW AND TOTAL RESULTS ARE ZERO
		if(empty($can_view) || $this->view->total_results >= 1) {
			return $this->setNoRender();
		}

		//DOCUMENT CREATION PRIVACY
		$this->view->can_create = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'create');
  }
}
?>