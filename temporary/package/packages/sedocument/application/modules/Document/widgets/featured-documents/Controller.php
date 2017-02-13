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
class Document_Widget_FeaturedDocumentsController extends Engine_Content_Widget_Abstract
{ 
  public function indexAction()
  { 
		//GET VIEWER DETAILS
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

    //GET LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

		//WHO CAN VIEW THE DOCUMENTS
		$can_view = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'view');
		if(empty($can_view)) {
			return $this->setNoRender();
		}

		//CHECK THAT RATING IS VIEABLE OR NOT
		$this->view->show_rate = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.rating', 1);

    //FETCH DOCUMENTS
    $params = array();
    $params['orderby'] = 'featured DESC';
		$params['featured'] = 1;
    $params['limit'] = $this->_getParam('itemCount', 15);
    $this->view->paginator = Engine_Api::_()->getDbtable('documents', 'document')->widgetDocumentsData($params);

		//SET NO RENDER IF DATA IS EMPTY
    if (Count($this->view->paginator) <= 0) {
      return $this->setNoRender();
    }
  }
}
?>