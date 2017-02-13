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
class Sitepagepoll_Widget_SitepagepollContentController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

     //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //GET THE TAB ID OF POLL ON PAGE PROFILE PAGE
    $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');
  
    //GET PAGEPOLL MODEL
    $this->view->sitepagepoll = $sitepagepoll = Engine_Api::_()->core()->getSubject('sitepagepoll_poll');
    if (empty($sitepagepoll)) {
      return $this->setNoRender();
    }

    //GET THE PAGE ID
    $this->view->page_id = $page_id = $sitepagepoll->page_id;

    //GET SITEPAGE OBJECT
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //PACKAGE BASE PRIYACY START
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
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'splcreate');
    if (empty($isManageAdmin)) {
      $this->view->can_create = 0;
    } else {
      $this->view->can_create = 1;
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'comment');
    if (empty($isManageAdmin)) {
      $this->view->can_comment = 0;
    } else {
      $this->view->can_comment = 1;
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = $can_edit = 0;
    } else {
      $this->view->can_edit = $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //DIS-APPROVED POLL WILL BE VISIBLE ONLY TO POLL-OWNER, SITEPAGE-OWNER AND MANAGE-ADMIN
    if ($sitepagepoll->owner_id != $viewer_id && $can_edit != 1 && ( empty($sitepagepoll->approved) || empty($sitepagepoll->search))) {
      return $this->setNoRender();
    }

    //WHO CAN VOTE
    if (!empty($viewer_id) && $sitepagepoll->approved == 1 && $sitepagepoll->search == 1) {
      $this->view->can_vote = $can_vote = 1;
    } else {
      $this->view->can_vote = $can_vote = 0;
    }
    $getPackagepollview = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepagepoll');

    //GET OWNER INFORMATION
    Engine_Api::_()->sitepagepoll()->setPollPackages();
    $owner = $sitepagepoll->getOwner();

    //REPORT CODE
//     if (!empty($viewer_id)) {
//       $report = $sitepagepoll;
//       if (!empty($report)) {
//         Engine_Api::_()->core()->setSubject($report);
//       }
// //       if (!$this->_helper->requireSubject()->isValid())
// //         return;
//     }

    $this->view->sitepagepollOptions = empty($getPackagepollview) ? null : $sitepagepoll->getOptions();
    $this->view->hasVoted = $sitepagepoll->viewerVoted();
    $this->view->showPieChart = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.showPieChart', false);
    $this->view->canVote = $can_vote;
    $this->view->canChangeVote = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.canchangevote', false);

    //INCREMENT IN NUMBER OF VIEWS
    if (!$owner->isSelf($viewer)) {
      $sitepagepoll->views++;
    }

    $sitepagepoll->save();

    // START: "SUGGEST TO FRIENDS" LINK WORK.
    $page_flag = 0;
    $is_suggestion_enabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
    $is_moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage');
    $isSupport = Engine_Api::_()->getApi('suggestion', 'sitepage')->isSupport();
    if (!empty($is_suggestion_enabled)) {
      // HERE WE ARE DELETE THIS POLL SUGGESTION IF VIEWER HAVE.
      if (!empty($is_moduleEnabled)) {
        Engine_Api::_()->getApi('suggestion', 'sitepage')->deleteSuggestion($viewer_id, 'page_poll', Zend_Controller_Front::getInstance()->getRequest()->getParam('poll_id'), 'page_poll_suggestion');
      }

      $SuggVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('suggestion')->version;
      $versionStatus = strcasecmp($SuggVersion, '4.1.7p1');
      if ($versionStatus >= 0) {
        $modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('sitepagepoll', Zend_Controller_Front::getInstance()->getRequest()->getParam('poll_id'), 1);
        if (!empty($modContentObj)) {
          $contentCreatePopup = @COUNT($modContentObj);
        }
      }

      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.enable', 1)) {
        if ($sitepage->expiration_date <= date("Y-m-d H:i:s")) {
          $page_flag = 1;
        }
      }
      if (!empty($contentCreatePopup) && !empty($isSupport) && empty($sitepage->closed) && !empty($sitepage->approved) && empty($sitepage->declined) && !empty($sitepage->draft) && empty($page_flag) && !empty($viewer_id) && !empty($is_suggestion_enabled)) {
        $this->view->pollSuggLink = Engine_Api::_()->suggestion()->getModSettings('sitepage', 'poll_sugg_link');
      }
    }
    // END: "SUGGEST TO FRIENDS" LINE WORK.
  }

}
?>