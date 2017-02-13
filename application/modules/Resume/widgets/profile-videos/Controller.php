<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Resume_video
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Resume_Widget_ProfileVideosController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {
    
    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SUBJECT
    if (Engine_Api::_()->core()->getSubject()->getType() == 'resume') {
      $this->view->resume = $resume = Engine_Api::_()->core()->getSubject('resume');
    } else {
      $this->view->resume = $resume = Engine_Api::_()->core()->getSubject()->getParent();
    }

    //GET VIEWER DETAIL
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //GET LEVEL INFO
    if (!empty($viewer_id)) {
      $this->view->level_id = $level_id = $viewer->level_id;
    } else {
      $this->view->level_id = $level_id = 0;
    }

    //PACKAGE BASE PRIYACY START
//    if (Engine_Api::_()->resume()->hasPackageEnable()) {
//      if (!Engine_Api::_()->resume()->allowPackageContent($resume->package_id, "modules", "sitepagevideo")) {
//        return $this->setNoRender();
//      }
//    } else {
//      $isPageOwnerAllow = Engine_Api::_()->resume()->isPageOwnerAllow($resume, 'svcreate');
//      if (empty($isPageOwnerAllow)) {
//        return $this->setNoRender();
//      }
//    }
    //PACKAGE BASE PRIYACY END
//    $videoCreate = Engine_Api::_()->resume()->isManageAdmin($resume, 'svcreate');
//    //START MANAGE-ADMIN CHECK
//    $isManageAdmin = Engine_Api::_()->resume()->isManageAdmin($sitepage, 'view');
//    if (empty($isManageAdmin)) {
//      return $this->setNoRender();
//    }
//    $isManageAdmin = Engine_Api::_()->resume()->isManageAdmin($resume, 'edit');
//    if (empty($isManageAdmin)) {
//      $this->view->can_edit = $can_edit = 0;
//    } else {
//      $this->view->can_edit = $can_edit = 1;
//    }
//    if (empty($videoCount) && empty($videoCreate) && empty($can_edit) && !(Engine_Api::_()->resume()->showTabsWithoutContent())) {
//      return $this->setNoRender();
//    }
    //END MANAGE-ADMIN CHECK
    
    $this->view->canUpload = $canUpload = $resume->authorization()->isAllowed(null, 'video');
    
    $paginator_params = array(
      'owner_id' => $resume->user_id,
      'status' => 1,
//        'search' => 1,
      'resume_id' => $resume->resume_id
    );
    
    if(!$canUpload) {
      $paginator_params['search'] = 1;
    }
    
    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->resume()->getVideosPaginator($paginator_params);

    $this->view->tab = $this->_getVideoTabId();
    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 8));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Do not render if nothing to show
    if ($paginator->getTotalItemCount() <= 0 && !$canUpload) {
      return $this->setNoRender();
    }
    
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    if($view) {
      $view->headLink()->appendStylesheet(
        $view->layout()->staticBaseUrl . 'application/modules/Resume/externals/styles/resume.css'
      );
    }
    
    $this->view->isAjax = $this->_getParam('ajax', 0);

    $this->_childCount = $paginator->getTotalItemCount();
  }

  public function getChildCount() {
    return $this->_childCount;
  }

  protected function _getVideoTabId() {
    $db = Engine_Db_Table::getDefaultAdapter();
    
    $resumeProfilePageId = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('`name` = ?', 'resume_profile_index')
            ->limit(1)
            ->query()
            ->fetchColumn()
            ;
    
    $videoTabContentId = $db->select()
            ->from('engine4_core_content', 'content_id')
            ->where('`name` = ?', 'resume.profile-videos')
            ->where('`page_id` = ?', $resumeProfilePageId)
            ->limit(1)
            ->query()
            ->fetchColumn()
            ;
    
    return $videoTabContentId;
  }

}

?>