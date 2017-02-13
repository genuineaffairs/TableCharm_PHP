<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagepoll_IndexController extends Seaocore_Controller_Action_Standard {

  //ACTION FOR CREATING POLL
  public function createAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //NUMBER OF OPTIONS WHICH WILL BE SHOW AT THE TIME OF POLL CREATION
    $this->view->maxOptions = $max_options = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.maxoptions', 15);

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

    $getPollCreate = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepagepoll');

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    $sitepagePollHostName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));

    //GET PAGE ID
    $page_id = $this->_getParam('page_id');
    if (empty($page_id)) {
      return;
    }

    //GET PAGE OBJECT
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if ((!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagepoll")) || empty($getPollCreate)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'splcreate');
      if (empty($isPageOwnerAllow) || empty($getPollCreate)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }

    //IS PICCHART ALLOW OR NOT.
    $isPieChart = Engine_Api::_()->sitepagepoll()->isPieChart();

    $maxLenght = Zend_Registry::isRegistered('sitepagepoll_maxLenght') ? Zend_Registry::get('sitepagepoll_maxLenght') : null;
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = $can_edit = 0;
    } else {
      $this->view->can_edit = $can_edit = 1;
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'splcreate');
    if (empty($isManageAdmin) && empty($can_edit)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //SEND TAB ID TO TPL
    $this->view->tab_selected_id = $this->_getParam('tab');

    //GENERATE FORM
    $this->view->form = $form = new Sitepagepoll_Form_Create();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $translate = Zend_Registry::get('Zend_Translate');

      //CHECK OPTIONS
      $options = (array) $this->_getParam('optionsArray');
      $options = array_filter(array_map('trim', $options));
      $options = array_slice($options, 0, $max_options);
      $this->view->options = $options;
      if (empty($options) || !is_array($options) || count($options) < 2) {
        $error = Zend_Registry::get('Zend_Translate')->_('You must provide at least two possible answers.');
        return $form->addError($error);
      }
      foreach ($options as $index => $option) {
        if (strlen($option) > 80) {
          $options[$index] = Engine_String::substr($option, 0, 80);
        }
      }

      if (empty($isPieChart)) {
        return;
      }

      $isModType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.set.type', 0);
      if (empty($isModType)) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepagepoll.type', convert_uuencode($sitepagePollHostName));
      }

      //CONNET WITH POLL TABLE
      $sitepagepollTable = Engine_Api::_()->getItemTable('sitepagepoll_poll');
      $sitepagepollOptionsTable = Engine_Api::_()->getDbtable('options', 'sitepagepoll');
      $db = $sitepagepollTable->getAdapter();
      $db->beginTransaction();

      //GET POSTED VALUES FROM CREATE FORM
      $values = $form->getValues();
      if ($values['end_settings'] == 1 && $values['end_time'] == 0) {
        $error = $message . $this->view->translate('Please select end-date and time from calendar !');
        $error = Zend_Registry::get('Zend_Translate')->_($error);
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }

      if (empty($maxLenght)) {
        return;
      }

      //POLL CREATION CODE
      try {
        $values = array_merge($form->getValues(), array(
            'owner_type' => $viewer->getType(),
            'owner_id' => $viewer_id,
                ));

        $this->view->is_error = 0;
        $this->view->excep_error = 0;

        //CREATE THE MAIN POLL
        $sitePagePollRow = $sitepagepollTable->createRow();
        $sitePagePollRow->setFromArray($values);
        $sitePagePollRow->page_id = $page_id;
        $sitePagePollRow->approved = 1;
        $sitePagePollRow->parent_type = 'sitepage_page';
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        $sitePagePollRow->save();

        //CREATE OPTIONS
        $censor = new Engine_Filter_Censor();
        foreach ($options as $option) {
          $sitepagepollOptionsTable->insert(array(
              'poll_id' => $sitePagePollRow->poll_id,
              'sitepagepoll_option' => $censor->filter($option),
          ));
        }

        //COMMENT PRIVACY
        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');
        $auth_comment = "everyone";
        $commentMax = array_search($auth_comment, $roles);
        foreach ($roles as $i => $role) {
          $auth->setAllowed($sitePagePollRow, $role, 'comment', ($i <= $commentMax));
        }

        //ACTIVITY FEED
        if ($sitePagePollRow->search == 1) {
          $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
          $activityFeedType = null;
          if (Engine_Api::_()->sitepage()->isPageOwner($sitepage) && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
            $activityFeedType = 'sitepagepoll_admin_new';
          elseif ($sitepage->all_post || Engine_Api::_()->sitepage()->isPageOwner($sitepage))
            $activityFeedType = 'sitepagepoll_new';
          if ($activityFeedType) {
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $sitepage, $activityFeedType);
            Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
          }
          if ($action != null) {
            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $sitePagePollRow);
          }

          //SENDING ACTIVITY FEED TO FACEBOOK.
          $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
          if (!empty($enable_Facebooksefeed)) {

            $poll_array = array();
            $poll_array['type'] = 'sitepagepoll_new';
            $poll_array['object'] = $sitePagePollRow;

            Engine_Api::_()->facebooksefeed()->sendFacebookFeed($poll_array);
          }

					//PAGE POLL CREATE NOTIFICATION AND EMAIL WORK
					$sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;
					if(!empty($action)) {
						if ($sitepageVersion >= '4.3.0p1') {
							Engine_Api::_()->sitepage()->sendNotificationEmail($sitePagePollRow, $action, 'sitepagepoll_create', 'SITEPAGEPOLL_CREATENOTIFICATION_EMAIL');
							$isPageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->isPageAdmins($viewer->getIdentity(), $page_id);
							if (!empty($isPageAdmins)) {
								//NOTIFICATION FOR ALL FOLLWERS.
								Engine_Api::_()->sitepage()->sendNotificationToFollowers($sitePagePollRow, $action, 'sitepagepoll_create');
							}
						}
					}
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      //GO TO SITEPAGE PROFILE PAGE WITH SITEPAGE POLL SELECTED TAB
      return $this->_redirectCustom($sitePagePollRow->getHref(), array('prependBase' => false));
    }
  }

  //ACTION FOR VIEW THE POLL
  public function viewAction() {

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET POLL ITEM
    $sitepagepoll = Engine_Api::_()->getItem('sitepagepoll_poll', $this->getRequest()->getParam('poll_id'));

		if ($sitepagepoll) {
			Engine_Api::_()->core()->setSubject($sitepagepoll);
		}

    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagepoll->page_id);

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagepoll")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'splcreate');
      if (empty($isPageOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'splcreate');
    if (empty($isManageAdmin)) {
      $this->view->can_create = 0;
    } else {
      $this->view->can_create = 1;
    }
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //CHECKING THE USER HAVE THE PERMISSION TO VIEW THE POLL OR NOT
    if ($sitepagepoll->owner_id != $viewer_id && $can_edit != 1 && ( empty($sitepagepoll->approved) || empty($sitepagepoll->search))) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //NAVIGATION WORK FOR FOOTER.(DO NOT DISPLAY NAVIGATION IN FOOTER ON VIEW PAGE.)
    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
         if(!Zend_Registry::isRegistered('sitemobileNavigationName')){
         Zend_Registry::set('sitemobileNavigationName','setNoRender');
         }
    }
    
   //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }
  }

  //ACTION FOR DELETE POLL
  public function deleteAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET LOGGED IN INFORMATION
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    //GET TAB ID OF POLL ON THE PAGE PROFILE PAGE
    $this->view->tab_selected_id = $tab_selected_id = $this->_getParam('tab');
    $page_id = $this->_getParam('page_id');

    //GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_main');

    //RETURN IF PAGE ID IS EMPTY
    if (empty($page_id)) {
      return;
    }

    //GET SITEPAGE OBJECT
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //GET PAGEPOLL OBJECT
    $this->view->sitepagepoll = $sitepagepoll = Engine_Api::_()->getItem('sitepagepoll_poll', $this->_getParam('poll_id'));

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagepoll")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'splcreate');
      if (empty($isPageOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //POLL OWNER AND SITEPAGE OWNER CAN DELETE POLL
    if ($viewer_id != $sitepagepoll->owner_id && $can_edit != 1) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //DELETE POLL FROM DATATBASE AND SCRIBD AFTER CONFIRMATION
    if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {

      //FINALLY DELETE PAGE POLL
      $sitepagepoll->delete();

			return $this->_gotoRouteCustom(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id), 'tab' => $this->view->tab_selected_id), 'sitepage_entry_view', true);
    }
  }

  public function voteAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid()) {
      return;
    }

    //GET SUBJECT
    $sitepagepoll = null;
    if (null !== ($sitepagepollIdentity = $this->_getParam('poll_id'))) {
      $sitepagepoll = Engine_Api::_()->getItem('sitepagepoll_poll', $sitepagepollIdentity);
      if (null !== $sitepagepoll) {
        Engine_Api::_()->core()->setSubject($sitepagepoll);
      }
    }

    //CHECK METHOD
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //GET OPTION ID
    $this->view->option_id = $option_id = $this->_getParam('option_id');

    $getPackagepollvote = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepagepoll');

    //CAN CHANGE VOTE OR NOT
    $canChangeVote = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.canchangevote', false);

    //GET VIEWER INFO
    $viewer = Engine_Api::_()->user()->getViewer();
    if (empty($getPackagepollvote)) {
      return;
    }

    if (!$sitepagepoll) {
      $this->view->success = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('This poll does not seem to exist anymore.');
      return;
    }

    //CHECK FOR THE USER THAT CAN CHANGE VOTE OR NOT.
    if ($sitepagepoll->hasVoted($viewer) && !$canChangeVote) {
      $this->view->success = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('You have already voted on this poll, and are not permitted to change your vote.');
      return;
    }

    //MAKE VOTE ENTRY IN DATATBASE
    $db = Engine_Api::_()->getDbtable('polls', 'sitepagepoll')->getAdapter();
    $db->beginTransaction();
    try {
      $sitepagepoll->vote($viewer, $option_id);
      $db->commit();
    } catch (Exception $e) {
      $db->rollback();
      $this->view->success = false;
      throw $e;
    }

    $this->view->success = true;
    $sitepagepollOptions = array();
    foreach ($sitepagepoll->getOptions()->toArray() as $option) {
      $option['votesTranslated'] = $this->view->translate(array('%s vote', '%s votes', $option['votes']), $this->view->locale()->toNumber($option['votes']));
      $sitepagepollOptions[] = $option;
    }
    $this->view->sitepagepollOptions = $sitepagepollOptions;
    $this->view->votes_total = $sitepagepoll->vote_count;
  }

  public function closeAction() {
    if (!$this->_helper->requireUser()->isValid())
      return;

    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $sitepagepoll = Engine_Api::_()->getItem('sitepagepoll_poll', $this->_getParam('poll_id'));
    $page_id = $this->_getParam('page_id');
    $tab_selected_id = $this->_getParam('tab');

    //GET SITEPAGE OBJECT
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagepoll")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'splcreate');
      if (empty($isPageOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //POLL OWNER AND SITEPAGE OWNER CAN DELETE POLL
    if ($viewer_id != $sitepagepoll->owner_id && $can_edit != 1) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $sitepagepollTable = $sitepagepoll->getTable();
    $db = $sitepagepollTable->getAdapter();
    $db->beginTransaction();

    try {
      $sitepagepoll->closed = (bool) $this->_getParam('closed');
      $sitepagepoll->save();

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_gotoRouteCusotm(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id), 'tab' => $tab_selected_id), 'sitepage_entry_view', true);
  }

   public function browseAction() {

   //CHECK VIEW PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'view')->isValid())
      return;

   //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }
  }

}