<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_Widget_SitemobileProfileSitepagenotesController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  //ACTION FOR SHOWING NOTES ON PAGE PROFILE PAGE
  public function indexAction() {
  	
    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //SET NO RENDER IF NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET VIEWER INFORMATION
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //GETTING LEVEL
    if (!empty($viewer_id)) {
      $level_id = $this->view->level_id = $viewer->level_id;
    } else {
      $level_id = $this->view->level_id = 0;
    }

    //GET SUBJECT AND PAGE ID AND PAGE OWNER ID
    $this->view->sitepageSubject = $sitepageSubject = Engine_Api::_()->core()->getSubject('sitepage_page');
    $page_id = $sitepageSubject->page_id;

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepageSubject->package_id, "modules", "sitepagenote")) {
        return $this->setNoRender();
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepageSubject, 'sncreate');
      if (empty($isPageOwnerAllow)) {
        return $this->setNoRender();
      }
    }
    //PACKAGE BASE PRIYACY END
    
    //TOTAL NOTE
    $noteCount = Engine_Api::_()->sitepage()->getTotalCount($page_id, 'sitepagenote', 'notes'); 
    $noteCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepageSubject, 'sncreate');
            
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepageSubject, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    $this->view->allowView = false;
    if (!empty($viewer_id) && $viewer->level_id == 1) {
      $this->view->allowView = true;
    } 

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepageSubject, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = $can_edit = 0;
    } else {
      $this->view->can_edit = $can_edit = 1;
    }
   
    if (empty($noteCreate) && empty($noteCount) && empty($can_edit) && !(Engine_Api::_()->sitepage()->showTabsWithoutContent())) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK

		//MAKING THE SEACHING PARAMATER ARRAY
		$values = array();
		
		//START MANAGE-ADMIN CHECK
		$isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepageSubject, 'sncreate');
		if (empty($isManageAdmin) && empty($can_edit)) {
			$this->view->can_create = 0;
		} else {
			$this->view->can_create = 1;
		}
		//END MANAGE-ADMIN CHECK
		
		//FETCH NOTES
		$values['page_id'] = $page_id;
    $values['orderby'] = 'creation_date';
		if ($can_edit == 1) {
			$values['show_pagenotes'] = 0;
			$this->view->paginator = $paginator = Engine_Api::_()->getDbTable('notes', 'sitepagenote')->getSitepagenotesPaginator($values);
		} else {
			$values['show_pagenotes'] = 1;
			$values['note_owner_id'] = $viewer_id;
			$this->view->paginator = $paginator = Engine_Api::_()->getDbTable('notes', 'sitepagenote')->getSitepagenotesPaginator($values);
		}

		$this->view->paginator->setItemCountPerPage(10)->setCurrentPageNumber($this->_getParam('page', 1));
    $this->_childCount = $paginator->getTotalItemCount();
  }

  //RETURN THE COUNT OF THE NOTE
  public function getChildCount() {
    return $this->_childCount;
  }

}