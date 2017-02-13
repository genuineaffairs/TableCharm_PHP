<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagepoll_Widget_SitemobileProfileSitepagepollsController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    //DONT RENDER THIS IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET VIEWER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    if (!empty($viewer_id)) {
      $this->view->level_id = $level_id = $viewer->level_id;
    } else {
      $this->view->level_id = $level_id = 0;
    }

    //GET SITEPAGE SUBJECT
    $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

    //GET PAGE ID
    $this->view->page_id = $page_id = $sitepage->page_id;

    //IF PIECHART IS DISABLE THEN SET NO RENDER
    $isPieChart = Engine_Api::_()->sitepagepoll()->isPieChart();
    if (empty($isPieChart)) {
      return $this->setNoRender();
    }

    // PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagepoll")) {
        return $this->setNoRender();
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'splcreate');
      if (empty($isPageOwnerAllow)) {
        return $this->setNoRender();
      }
    }
    // PACKAGE BASE PRIYACY END
    
    //TOTAL POLL
    $pollCount = Engine_Api::_()->sitepage()->getTotalCount($page_id, 'sitepagepoll', 'polls');   
    $pollCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'splcreate');
           
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = $can_edit = 0;
    } else {
      $this->view->can_edit = $can_edit = 1;
    }

    if (empty($pollCreate) && empty($pollCount) && empty($can_edit) && !(Engine_Api::_()->sitepage()->showTabsWithoutContent())) {
      return $this->setNoRender();
    } 
    //END MANAGE-ADMIN CHECK

		//START MANAGE-ADMIN CHECK
		$isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'splcreate');
		if (empty($isManageAdmin) && empty($can_edit)) {
			$this->view->can_create = 0;
		} else {
			$this->view->can_create = 1;
		}
		//END MANAGE-ADMIN CHECK

		//FETCH POLLS
		$values['page_id'] = $page_id;
    $values['orderby'] = 'creation_date';
		if ($can_edit) {
			$values['show_poll'] = 0;
			$this->view->paginator = $paginator = Engine_Api::_()->getItemTable('sitepagepoll_poll')->getSitepagepollsPaginator($values);
		} else {
			$values['show_poll'] = 1;
			$values['poll_owner_id'] = $viewer_id;
			$this->view->paginator = $paginator = Engine_Api::_()->getItemTable('sitepagepoll_poll')->getSitepagepollsPaginator($values);
		}

		//10 POLLS PER PAGE
		$this->view->paginator->setItemCountPerPage(10)->setCurrentPageNumber($this->_getParam('page', 1));
		$this->_childCount = $paginator->getTotalItemCount();
      
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}