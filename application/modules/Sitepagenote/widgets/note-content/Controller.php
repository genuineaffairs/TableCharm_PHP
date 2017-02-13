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
class Sitepagenote_Widget_NoteContentController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

     //GET PLAYLIST ID AND OBJECT
    $note_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('note_id', $this->_getParam('note_id', null));
  //GET LOGGED IN USER INFORMATION
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //SEND TAB ID TO TPL FILE
    $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');

    //GET NOTE ITEM
    $this->view->sitepagenote = $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $note_id);
    if (empty($sitepagenote)) {
      return $this->setNoRender();
    }

    //PAGE ID
    $page_id = $sitepagenote->page_id;

    //GET SITEPAGE ITEM
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //GETTING THE NOTE TAGS
    $this->view->noteTags = $sitepagenote->tags()->getTagMaps();

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sncreate');
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
  
    //MAKE FEATURED OR NOT
    $this->view->canMakeFeatured = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagenote.featured', 1);

    $this->view->allowView = false;
    if (!empty($viewer_id) && $viewer->level_id == 1) {
      $auth = Engine_Api::_()->authorization()->context;
      $this->view->allowView = $auth->isAllowed($sitepage, 'everyone', 'view') === 1 ? true : false ||$auth->isAllowed($sitepage, 'registered', 'view') === 1 ? true : false;
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = $can_edit = 0;
    } else {
      $this->view->can_edit = $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //CHECKING THE USER HAVE THE PERMISSION TO VIEW THE NOTE OR NOT
    if ($viewer_id != $sitepagenote->owner_id && $can_edit != 1 && ($sitepagenote->draft == 1 || $sitepagenote->search != 1)) {
      return $this->setNoRender();
    }

    //SHOW PHOTO
    $this->view->photoNotes = Engine_Api::_()->getDbTable('photos', 'sitepagenote')->getNotePhotos($sitepagenote->note_id);

    //INCREMENT IN NUMBER OF VIEWS
    if (!$sitepagenote->getOwner()->isSelf($viewer)) {
      $sitepagenote->view_count++;
    }
    $sitepagenote->save();

    //GETTING THE TOTAL NOTE IN THE ONE PAGE
    $this->view->count_note = Engine_Api::_()->getDbTable('notes', 'sitepagenote')->getTotalNote($page_id);
    $this->view->limit_sitepagenote = $total_sitepagenotes = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagenote.tag.widgets', 3);

    //START: "SUGGEST TO FRIEND" LINK WORK
    $page_flag = 0;
    $is_suggestion_enabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
    $is_moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage');
    $isSupport = Engine_Api::_()->getApi('suggestion', 'sitepage')->isSupport();

    if (!empty($is_suggestion_enabled)) {
      //HERE WE ARE DELETE THIS NOTE SUGGESTION IF VIEWER HAVE
      if (!empty($is_moduleEnabled)) {
        Engine_Api::_()->getApi('suggestion', 'sitepage')->deleteSuggestion($viewer_id, 'page_note', Zend_Controller_Front::getInstance()->getRequest()->getParam('note_id'), 'page_note_suggestion');
      }

      $SuggVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('suggestion')->version;
      $versionStatus = strcasecmp($SuggVersion, '4.1.7p1');
      if ($versionStatus >= 0) {
        $modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend('sitepagenote', Zend_Controller_Front::getInstance()->getRequest()->getParam('note_id'), 1);
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
        $this->view->noteSuggLink = Engine_Api::_()->suggestion()->getModSettings('sitepage', 'note_sugg_link');
      }
      //END: "SUGGEST TO FRIEND" LINK WORK
    }
  }

}
?>