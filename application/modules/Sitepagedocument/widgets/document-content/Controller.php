<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagedocument_Widget_DocumentContentController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    //SET SCRIBD API AND SCECRET KEY
    $this->scribd_api_key = Engine_Api::_()->getApi('settings', 'core')->sitepagedocument_api_key;
    $this->scribd_secret = Engine_Api::_()->getApi('settings', 'core')->sitepagedocument_secret_key;
    $this->scribdsitepage = new Scribdsitepage($this->scribd_api_key, $this->scribd_secret);

      //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //GET USER LEVEL
    if (!empty($viewer_id)) {
      $this->view->level_id = $level_id = Engine_Api::_()->user()->getViewer()->level_id;
    } else {
      $this->view->level_id = $level_id = 0;
    }

    //GET DOCUMNET MODEL
    $this->view->sitepagedocument = $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', Zend_Controller_Front::getInstance()->getRequest()->getParam('document_id'));
    if (empty($sitepagedocument)) {
      return $this->setNoRender();
    }

    $this->view->page_id = $sitepagedocument->page_id;

    //SET PAGE SUBJECT
    $sitepage_subject = null;
    //if (!Engine_Api::_()->core()->hasSubject()) {
      $page_id = $sitepagedocument->page_id;
      if (null !== $page_id) {
        $page_subject = $sitepage_subject = Engine_Api::_()->getItem('sitepage_page', $page_id);
//         if ($sitepage_subject && $sitepage_subject->getIdentity()) {
//           Engine_Api::_()->core()->setSubject($sitepage_subject);
//         }
      }
    //}
    $this->view->sitepage_subject = $sitepage_subject;
    $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage_subject->package_id, "modules", "sitepagedocument")) {
        return $this->setNoRender();
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage_subject, 'sdcreate');
      if (empty($isPageOwnerAllow)) {
        return $this->setNoRender();
      }
    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
  
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'comment');
    if (empty($isManageAdmin)) {
      $this->view->can_comment = 0;
    } else {
      $this->view->can_comment = 1;
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    $this->view->allowView = false;
    if (!empty($viewer_id) && $viewer->level_id == 1) {
      $auth = Engine_Api::_()->authorization()->context;
      $this->view->allowView = $auth->isAllowed($sitepage_subject, 'everyone', 'view') === 1 ? true : false ||$auth->isAllowed($sitepage_subject, 'registered', 'view') === 1 ? true : false;
    } 

    //END MANAGE-ADMIN CHECK
    //DESTROY THE PREVIOUS SUBJECT
//     Engine_Api::_()->core()->clearSubject();

    if (($can_edit != 1 && $viewer_id != $sitepagedocument->owner_id) && ($sitepagedocument->draft == 1 || $sitepagedocument->status != 1 || $sitepagedocument->approved != 1 || $sitepagedocument->search != 1)) {
      return $this->setNoRender();
    }

		//SSL WORK
		$this->view->https = 0;
		if (!empty($_SERVER["HTTPS"]) && 'on' == strtolower($_SERVER["HTTPS"])) {
			$this->view->https = 1;
    }
		$this->view->manifest_path = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.manifestUrl', "page-documents");

    //CHECK THAT VIEWER CAN RATE THE DOCUMENT OR NOT
    $this->view->can_rate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.rating', 1);

    //GET OWNER INFORMATION
    $this->view->owner = $owner = $sitepagedocument->getOwner();

    //GET CURRENT URL
    $front = Zend_Controller_Front::getInstance();
		if(!empty($this->view->https)) {
			$curr_url = 'https://' . $_SERVER['HTTP_HOST'] . $front->getRequest()->getRequestUri();
		}
		else {
			$curr_url = 'http://' . $_SERVER['HTTP_HOST'] . $front->getRequest()->getRequestUri();
		}
    $this->view->curr_url = urlencode((string) $curr_url);

    //INCREMENT IN NUMBER OF VIEWS
    $owner = $sitepagedocument->getOwner();
    if (!$owner->isSelf($viewer)) {
      $sitepagedocument->views++;
    }

    //SET SCRIBD USER ID
    $this->scribdsitepage->my_user_id = $sitepagedocument->owner_id;
    Engine_Api::_()->sitepagedocument()->setDocumentPackages();

    $stat = null;
    if (!empty($sitepagedocument->doc_id)) {
      try {
        $stat = trim($this->scribdsitepage->getConversionStatus($sitepagedocument->doc_id));
      } catch (Exception $e) {
        $message = $e->getMessage();
        $this->view->excep_error = 1;
        $this->view->excep_message = $message;
      }
    }

    //CHECK VIEWER CAN DOWNLOAD AND EMAIL THIS DOCUMENT OR NOT
    if (!empty($viewer_id)) {
      $download_allow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.download.allow', 1);
      $download_format = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.download.format', 'pdf');
      $this->view->email_allow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.email.allow', 1);
    } else {
      $download_allow = 0;
      $download_format = 0;
      $this->view->email_allow = 0;
    }

    if (!empty($viewer_id)) {
      if ($download_allow && $stat == 'DONE' && $sitepagedocument->download_allow) {
        try {
          $link = $this->scribdsitepage->getDownloadUrl($sitepagedocument->doc_id, $download_format);
        } catch (Exception $e) {
          $message = $e->getMessage();
          $this->view->excep_error = 1;
          $this->view->excep_message = $message;
        }
        $this->view->link = trim($link['download_link']);
        $sitepagedocument_include_full_text = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.include.full.text', 1);
        if ($sitepagedocument_include_full_text == 1) {
          $doc_full_text = $sitepagedocument->fulltext;
          $this->view->doc_full_text = $doc_full_text;
        }
      }
    } else { //WE SHOW FULL TEXT IN CASE OF NONLOGGEDIN USER IF DOCUMENT IS AVAILABLE FOR DOWNLOADING  AND ADMIN HAD ALLOWED FOR FULL TEXT IN GLOBAL SETTINGS
      if ($stat == 'DONE') {
        $sitepagedocument_include_full_text = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.include.full.text', 1);
        $sitepagedocument_visitor_fulltext = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.visitor.fulltext', 1);
        if ($sitepagedocument_include_full_text == 1 && $sitepagedocument_visitor_fulltext == 1 && $sitepagedocument->download_allow) {
          $doc_full_text = $sitepagedocument->fulltext;
          $this->view->doc_full_text = $doc_full_text;
        }
      }
    }

    //IF STAT IS DONE THAN UPDATE DOCUMENT STATUS AND OTHER INFORMATION
    if ($stat == 'DONE') {
      try {
        //GETTING DOCUMENT'S FULL TEXT
        $texturl = $this->scribdsitepage->getDownloadUrl($sitepagedocument->doc_id, 'txt');
        if ($sitepagedocument->status != 1) {
          $texturl = trim($texturl['download_link']);
          $file_contents = file_get_contents($texturl);
          if (empty($file_contents)) {
            $site_url = $texturl;
            $ch = curl_init();
            $timeout = 0; //SET ZERO FOR NO TIMEOUT
            curl_setopt($ch, CURLOPT_URL, $site_url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

            ob_start();
            curl_exec($ch);
            curl_close($ch);
            $file_contents = ob_get_contents();
            ob_end_clean();
          }
          $full_text = $file_contents;

          $setting = $this->scribdsitepage->getSettings($sitepagedocument->doc_id);
          $thumbnail_url = trim($setting['thumbnail_url']);

          //UPDATING DOCUMENT STATUS AND FULL TEXT
          $sitepagedocument->fulltext = $full_text;
          $sitepagedocument->thumbnail = $thumbnail_url;
          $sitepagedocument->status = 1;

          //ADD ACTIVITY ONLY IF DOCUMENT IS PUBLISHED
          if ($sitepagedocument->draft == 0 && $sitepagedocument->approved == 1 && $sitepagedocument->status == 1 && $sitepagedocument->search == 1 && $sitepagedocument->activity_feed == 0) {
            $api = Engine_Api::_()->getDbtable('actions', 'activity');
            $creator = Engine_Api::_()->getItem('user', $sitepagedocument->owner_id);
            $activityFeedType = null;
            if (Engine_Api::_()->sitepage()->isPageOwner($sitepage_subject) && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
              $activityFeedType = 'sitepagedocument_admin_new';
            elseif ($sitepage_subject->all_post || Engine_Api::_()->sitepage()->isPageOwner($sitepage_subject))
              $activityFeedType = 'sitepagedocument_new';

            if ($activityFeedType) {
              $action = $api->addActivity($creator, $sitepage_subject, $activityFeedType);
              Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
            }
            //MAKE SURE ACTION EXISTS BEFORE ATTACHING THE DOUCMENT TO THE ACTIVITY
            if ($action != null) {
              $api->attachActivity($action, $sitepagedocument);
              $sitepagedocument->activity_feed = 1;
              $sitepagedocument->save();
            }

            //PAGE DOCUMENT CREATE NOTIFICATION AND EMAIL WORK
						$sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;
						if ($sitepageVersion >= '4.2.9p3') {
							Engine_Api::_()->sitepage()->sendNotificationEmail($sitepagedocument, $action, 'sitepagedocument_create', 'SITEPAGEDOCUMENT_CREATENOTIFICATION_EMAIL', 'Pageevent Invite');
							
							$isPageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->isPageAdmins($viewer->getIdentity(), $page_id);
							if (!empty($isPageAdmins)) {
								//NOTIFICATION FOR ALL FOLLWERS.
								Engine_Api::_()->sitepage()->sendNotificationToFollowers($sitepagedocument, $action, 'sitepagedocument_create');
							}
						}
          }
        }
      } catch (Exception $e) {
        if ($sitepagedocument->status != 3 && $e->getCode() == 619) {
          $sitepagedocument->status = 3;

          //SEND EMAIL TO DOCUMENT OWNER IF PAGE DOCUMENT HAS BEEN DELETED FROM SCRIBD
          Engine_Api::_()->sitepagedocument()->emailDocumentDelete($sitepagedocument, $sitepage_subject->title, $sitepage_subject->owner_id);
        }
      }
      $sitepagedocument->save();
    } elseif ($stat == 'ERROR') {
      if ($sitepagedocument->status != 2) {
        $sitepagedocument->status = 2;
        $sitepagedocument->save();
      }
    } else {
      $sitepagedocument->save();
    }

    //DELETE DOCUMENT FROM SERVER IF ALLOWED BY ADMIN AND HAS STATUS ONE OR TWO
    $sitepagedocument_save_local_server = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.save.local.server', 1);
    if ($sitepagedocument_save_local_server == 0 && ($sitepagedocument->status == 1 || $sitepagedocument->status == 2)) {
      Engine_Api::_()->sitepagedocument()->deleteServerDocument($sitepagedocument->document_id);
    }

    //SETTING PARAMETERS FOR SECURE DOCUMENT
    if ($viewer->getIdentity() == 0) {
      $uid = mt_rand(1000, 100000);
    } else {
      $uid = $viewer->getIdentity();
    }
    $scribd_secret = Engine_Api::_()->getApi('settings', 'core')->sitepagedocument_secret_key;
    $sessionId = session_id();
    $signature = md5($scribd_secret . 'document_id' . $sitepagedocument->doc_id . 'session_id' . $sessionId . 'user_identifier' . $uid);
    $this->view->uid = $uid;
    $this->view->sessionId = $sessionId;
    $this->view->signature = $signature;
    $this->view->download_allow = $download_allow;

    //RATING WORK
    $this->view->rating_count = Engine_Api::_()->getDbTable('ratings', 'sitepagedocument')->countRating($sitepagedocument->getIdentity());
    $this->view->sitepagedocument_rated = Engine_Api::_()->getDbTable('ratings', 'sitepagedocument')->previousRated($sitepagedocument->getIdentity(), $viewer->getIdentity());

    //CUSTOM FIELD WORK
    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitepagedocument);

    // Start: Show "Suggest to Frind" link.
    $page_flag = 0;
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $is_moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage');
    $is_suggestion_enabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
    $isSupport = Engine_Api::_()->getApi('suggestion', 'sitepage')->isSupport();
    // Here we are delete this documemt suggestion if viewer have.

    if (!empty($is_suggestion_enabled)) {
      if (!empty($is_moduleEnabled)) {
        Engine_Api::_()->getApi('suggestion', 'sitepage')->deleteSuggestion($viewer_id, 'page_document', Zend_Controller_Front::getInstance()->getRequest()->getParam('document_id'), 'page_document_suggestion');
      }

      $SuggVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('suggestion')->version;
      $versionStatus = strcasecmp($SuggVersion, '4.1.7p1');
      if ($versionStatus >= 0) {
        $modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('sitepagedocument', Zend_Controller_Front::getInstance()->getRequest()->getParam('document_id'), 1);
        if (!empty($modContentObj)) {
          $contentCreatePopup = @COUNT($modContentObj);
        }
      }

      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
        if ($page_subject->expiration_date <= date("Y-m-d H:i:s")) {
          $page_flag = 1;
        }
      }
      if (!empty($contentCreatePopup) && !empty($isSupport) && empty($page_subject->closed) && !empty($page_subject->approved) && empty($page_subject->declined) && !empty($page_subject->draft) && empty($page_flag) && !empty($viewer_id) && !empty($is_suggestion_enabled)) {
        $this->view->documentSuggLink = Engine_Api::_()->suggestion()->getModSettings('sitepage', 'document_sugg_link');
      }
    }
    // End: "Suggest to Friend" link work
  }

}
?>