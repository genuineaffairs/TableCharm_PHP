<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_PageCoverInformationSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    if (!Engine_Api::_()->core()->hasSubject() || !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum')) {
      return $this->setNoRender();
    }

    //GET SETTING
    $statisticsElement = $this->_getParam('showContent', array("mainPhoto", "title", "followButton", "likeButton", "followCount", "likeCount"));
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
			$statisticsElement['']="memberCount";
			$statisticsElement[''] = 'addButton';
			$statisticsElement[''] = 'joinButton';
			$statisticsElement[''] = 'leaveButton';
		} 
		$this->view->showContent  = $statisticsElement;
		
    if(empty($this->view->showContent)) {
      $this->view->showContent = array();
    }

    //GET VIEWER ID
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
    
    //GET SITEPAGE SUBJECT
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    /* @var $membershipTable Sitepage_Model_DbTable_Membership */
    $membershipTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
    // We need to do this because the current member_count attribute of the sitepage object is inaccurate
    $totalMemberCount = $membershipTable->getTotalMemberCount($sitepage->page_id);
    if ($totalMemberCount != $sitepage->member_count) {
      $sitepage->member_count = $totalMemberCount;
      $sitepage->save();
    }
    $this->view->sitepage = $sitepage;

    $this->view->photo = $photo = Engine_Api::_()->getItem('sitepage_photo', $sitepage->page_cover);
    $this->view->columnHeight = $this->_getParam('columnHeight', '300');
    //START MANAGE-ADMIN CHECK
    $this->view->can_edit = $can_edit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
			$this->view->allowPage = Engine_Api::_()->sitepage()->allowInThisPage($sitepage, "sitepagemember", 'smecreate');
    }

    $this->view->cover_params = array('top' => 0, 'left' => 0);
    if($sitepage->page_cover) {
			$tableAlbum = Engine_Api::_()->getDbtable('albums', 'sitepage');
			$album = $tableAlbum->getSpecialAlbum($sitepage, 'cover');
			
			if($album->cover_params)
				$this->view->cover_params = $album->cover_params;
		}
  }

}

?>