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
class Document_Widget_NavigationDocumentsController extends Engine_Content_Widget_Abstract
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

		//GET ACTION NAME
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$action = '';
		if (!empty($request)) {
			$action = $request->getActionName();
		}

		//GET NAVIGATION
		if($action == 'home' || $action == 'mobi-home') {
			$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('document_main', array(), 'document_main_home');
		}
		else {
			$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('document_main', array(), 'document_main_browse');
		}
  }
}
?>