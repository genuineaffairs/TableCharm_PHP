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
include_once APPLICATION_PATH . '/application/modules/Sitepagedocument/Api/Scribdsitepage.php';

class Sitepagedocument_Widget_ProfileSitepagedocumentsController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //SET NO RENDER IF NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET VIEWER INFORMATION
    if (!empty($viewer_id)) {
      $this->view->level_id = $level_id = $viewer->level_id;
    } else {
      $this->view->level_id = $level_id = 0;
    }

    //GET SUBJECT
    if(Engine_Api::_()->core()->getSubject()->getType() == 'sitepage_page') {
    	$this->view->sitepage_subject = $sitepage_subject = Engine_Api::_()->core()->getSubject('sitepage_page');
    }
    else {
      $this->view->sitepage_subject = $sitepage_subject = Engine_Api::_()->core()->getSubject()->getParent();
    }

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
    //TOTAL DOCUMENT
    $documentCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage_subject->page_id, 'sitepagedocument', 'documents');
    $documentCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'sdcreate');

//    //START MANAGE-ADMIN CHECK
//    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'view');
//    if (empty($isManageAdmin)) {
//      return $this->setNoRender();
//    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = $can_edit = 0;
    } else {
      $this->view->can_edit = $can_edit = 1;
    }

    if (empty($documentCreate) && empty($documentCount) && empty($can_edit) && !(Engine_Api::_()->sitepage()->showTabsWithoutContent())) {
      return $this->setNoRender();
    }

    $sitepagedocument_feedtype = Zend_Registry::isRegistered('sitepagedocument_feedtype') ? Zend_Registry::get('sitepagedocument_feedtype') : null;
    if (empty($sitepagedocument_feedtype)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK

    //GET DOCUMENT TABLE
    $documentTable = Engine_Api::_()->getDbtable('documents', 'sitepagedocument');

    //Getting the tab id from the content table.
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
    $this->view->widgets = $widgets = Engine_Api::_()->sitepage()->getwidget($layout, $sitepage_subject->page_id);
    $this->view->content_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagedocument.profile-sitepagedocuments', $sitepage_subject->page_id, $layout);
    $this->view->module_tabid = $currenttabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    $isajax = $this->_getParam('isajax', null);
    $this->view->isajax = $isajax;
    $this->view->showtoptitle = $showtoptitle = Engine_Api::_()->sitepage()->showtoptitle($layout, $sitepage_subject->page_id);
    if (!empty($isajax) || ($currenttabid == $this->view->identity) || ($widgets == 0)) {

      $this->view->identity_temp = Zend_Controller_Front::getInstance()->getRequest()->getParam('identity_temp', $currenttabid);
      $this->view->show_content = true;

      //GET SEARCHING PARAMETERS
      $this->view->page = $page = $this->_getParam('page', 1);
      $this->view->search = $search = $this->_getParam('search');
      $this->view->selectbox = $selectbox = $this->_getParam('selectbox');
      $this->view->checkbox = $checkbox = $this->_getParam('checkbox');
      
      $values = array();
      $values['orderby'] = '';
      if (!empty($search)) {
        $values['search'] = $search;
      }
      if (!empty($selectbox) && $selectbox == 'featured') {
        $values['featured'] = 1;
        $values['orderby'] = 'document_id';
      } elseif (!empty($selectbox) && $selectbox == 'highlighted') {
        $values['highlighted'] = 1;
        $values['orderby'] = 'document_id';
      } elseif (!empty($selectbox)) {
        $values['orderby'] = $selectbox;
      }
      if (!empty($checkbox) && $checkbox == 1) {
        $values['owner_id'] = $viewer_id;
      }
   
      //START MANAGE-ADMIN CHECK
      $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage_subject, 'sdcreate');
      if (empty($isManageAdmin) && empty($can_edit)) {
        $this->view->can_create = 0;
      } else {
        $this->view->can_create = 1;
      }
      //END MANAGE-ADMIN CHECK
      //MAKE FEATURED OR NOT
      $this->view->canMakeHighlighted = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.featured', 1);

      //START CAROUSEL WORK
      //CHECK THAT CAROUSEL IS VIEABLE OR NOT
      $this->view->show_carousel = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.carousel', 1);
      if (!empty($this->view->show_carousel)) {

        //FETCH DOCUMENTS
        $params = array();
        $params['page_id'] = $sitepage_subject->page_id;
        $params['highlighted'] = 1;
        $params['limit'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.number.carousel', 15);
        $this->view->highlightedDocuments = $highlightDocuments = $documentTable->widgetDocumentsData($params);

        $this->view->total_highlightedDocuments = Count($highlightDocuments);
      }
      //END CAROUSEL WORK
      //BLOCK FOR UPDATING CONVERSION STATUS OF THE DOCUMENT
      $this->scribd_api_key = Engine_Api::_()->getApi('settings', 'core')->sitepagedocument_api_key;
      $this->scribd_secret = Engine_Api::_()->getApi('settings', 'core')->sitepagedocument_secret_key;
      $this->scribdsitepage = new Scribdsitepage($this->scribd_api_key, $this->scribd_secret);

      //FETCH DOCUMENTS
      $params = array();
      $params['page_id'] = $sitepage_subject->page_id;
      $params['profile_page_widget'] = 1;
      $doc_forUpdate = $documentTable->widgetDocumentsData($params);
      $stat="";
      foreach ($doc_forUpdate as $value) {

        if (empty($value->doc_id)) {
          continue;
        }

        $this->scribdsitepage->my_user_id = $value->owner_id;

        try {
          $stat = trim($this->scribdsitepage->getConversionStatus($value->doc_id));
        } catch (Exception $e) {
          $message = $e->getMessage();
        }

        if ($stat == 'DONE') {
          try {
            //GETTING DOCUMENT'S FULL TEXT
            $texturl = $this->scribdsitepage->getDownloadUrl($value->doc_id, 'txt');
            //for some reason, the URL comes back with leading and trailing spaces
            $texturl = trim($texturl['download_link']);

            $file_contents = file_get_contents($texturl);
            if (empty($file_contents)) {
              $site_url = $texturl;
              $ch = curl_init();
              $timeout = 0; // set to zero for no timeout
              curl_setopt($ch, CURLOPT_URL, $site_url);
              curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

              ob_start();
              curl_exec($ch);
              curl_close($ch);
              $file_contents = ob_get_contents();
              ob_end_clean();
            }
            $full_text = $file_contents;

            $setting = $this->scribdsitepage->getSettings($value->doc_id);
            $thumbnail_url = trim($setting['thumbnail_url']);

            //UPDATING DOCUMENT STATUS AND FULL TEXT
            $value->fulltext = $full_text;
            $value->thumbnail = $thumbnail_url;
            $value->status = 1;
            $value->save();
          } catch (Exception $e) {
            if ($e->getCode() == 619) {
              $value->status = 3;
              $value->save();

              //SEND EMAIL TO DOCUMENT OWNER IF PAGE DOCUMENT HAS BEEN DELETED FROM SCRIBD
              Engine_Api::_()->sitepagedocument()->emailDocumentDelete($value, $sitepage_subject->title, $sitepage_subject->owner_id);
            }
          }

          //ADD ACTIVITY ONLY IF DOCUMENT IS PUBLISHED
          $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', $value->document_id);
          if ($sitepagedocument->draft == 0 && $sitepagedocument->approved == 1 && $sitepagedocument->status == 1 && $sitepagedocument->search == 1 && $sitepagedocument->activity_feed == 0) {
            $api = Engine_Api::_()->getDbtable('actions', 'activity');
            $creator = Engine_Api::_()->getItem('user', $sitepagedocument->owner_id);
           // $action = $api->addActivity($creator, $sitepage_subject, 'sitepagedocument_new');
            $activityFeedType = null;
            if (Engine_Api::_()->sitepage()->isPageOwner($sitepage_subject,$creator) && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
              $activityFeedType = 'sitepagedocument_admin_new';
            elseif ($sitepage_subject->all_post || Engine_Api::_()->sitepage()->isPageOwner($sitepage_subject,$creator))
              $activityFeedType = 'sitepagedocument_new';

            if ($activityFeedType) {
              $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($creator, $sitepage_subject, $activityFeedType);
              Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
            }
            //MAKE SURE ACTION EXISTS BEFORE ATTACHING THE DOUCMENT TO THE ACTIVITY
            if ($action != null) {
              $api->attachActivity($action, $sitepagedocument);
              $sitepagedocument->activity_feed = 1;
              $sitepagedocument->save();
            }
          }
        } elseif ($stat == 'ERROR') {
          $value->status = 2;
          $value->save();
        }

        //DELETE DOCUMENT FROM SERVER IF ALLOWED BY ADMIN AND HAS STATUS ONE OR TWO
        $sitepagedocument_save_local_server = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.save.local.server', 1);
        if ($sitepagedocument_save_local_server == 0 && ($value->status == 1 || $value->status == 2)) {
          Engine_Api::_()->sitepagedocument()->deleteServerDocument($value->document_id);
        }
      }

      //CHECK THAT RATING IS VIEABLE OR NOT
      $this->view->show_rate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.rating', 1);

      //FETCH DOCUMENTS
      $values['page_id'] = $sitepage_subject->page_id;
      if ($can_edit == 1) {
        $values['show_document'] = 0;
        $this->view->paginator = $paginator = $documentTable->getSitepagedocumentsPaginator($values);
      } else {
        $values['show_document'] = 1;
        $values['document_owner_id'] = $viewer_id;
        $this->view->paginator = $paginator = $documentTable->getSitepagedocumentsPaginator($values);
      }

      //10 DOCUMENTS PER PAGE
      $paginator->setItemCountPerPage(10);
      $this->view->paginator->setCurrentPageNumber($page);

      //ADD NUMBER OF DOCUMENTS IN TAB
      if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0) {
        $this->_childCount = $paginator->getTotalItemCount();
      }
    } else {
      $this->view->show_content = false;
      $this->view->identity_temp = $this->view->identity;

      $values = array();
      //$values['orderby'] = 'document_id';
      $values['page_id'] = $sitepage_subject->page_id;
      $values['show_count'] = 1;
      if ($can_edit == 1) {
        $values['show_document'] = 0;
        $paginator = $documentTable->getSitepagedocumentsPaginator($values);
      } else {
        $values['show_document'] = 1;
        $values['document_owner_id'] = $viewer_id;
        $paginator = $documentTable->getSitepagedocumentsPaginator($values);
      }

      $this->_childCount = $paginator->getTotalItemCount();
    }
  }

  public function getChildCount() {
    return $this->_childCount;
  }

}

?>