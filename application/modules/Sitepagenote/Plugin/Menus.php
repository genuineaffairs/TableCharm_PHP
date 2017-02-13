<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_Plugin_Menus {

  public function canViewNotes() {

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagenote.note.show.menu', 1)) {
      return false;
    }

    $table = Engine_Api::_()->getDbTable('notes', 'sitepagenote');
    $rName = $table->info('name');
    $table_pages = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $rName_pages = $table_pages->info('name');
    $select = $table->select()
                    ->setIntegrityCheck(false)
                    ->from($rName_pages, array('photo_id', 'title as sitepage_title'))
                    ->join($rName, $rName . '.page_id = ' . $rName_pages . '.page_id');

    $select = $select
                    ->where($rName_pages . '.closed = ?', '0')
                    ->where($rName_pages . '.approved = ?', '1')
                    ->where($rName_pages . '.search = ?', '1')
                    ->where($rName_pages . '.declined = ?', '0')
                    ->where($rName_pages . '.draft = ?', '1');
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $select->where($rName_pages . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
    $row = $table->fetchAll($select);
    $count = count($row);
    if (empty($count)) {
      return false;
    }
    return true;
  }


  //SITEMOBILE PAGE NOTE MENUS
  public function onMenuInitialize_SitepagenoteWrite($row) {

    $subject = Engine_Api::_()->core()->getSubject();

    $sitepagenote = $this->getSitepageNoteObject();

    if (empty($sitepagenote)) {
      return false;
    }
    //PAGE ID
    $page_id = $sitepagenote->page_id;

    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sncreate');
    if (empty($isManageAdmin)) {
      $can_create = 0;
    } else {
      $can_create = 1;
    }

    //CHECKS FOR WRITE
    if (empty($can_create)) {
      return false;
    }

    return array(
        'label' => 'Write a Note',
        'route' => 'sitepagenote_create',
        'class' => 'ui-btn-action',
        'params' => array(
            'page_id' => $page_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitepagenotePublish($row) {
    $subject = Engine_Api::_()->core()->getSubject();

    $check = $this->commonChecks();

    if (empty($check)) {
      return false;
    }
    $sitepagenote = $this->getSitepageNoteObject();

    if (empty($sitepagenote)) {
      return false;
    }
    if ($sitepagenote->draft != 1) {
      return false;
    }
    return array(
        'label' => 'Publish Note',
        'route' => 'sitepagenote_publish',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
            'note_id' => $subject->getIdentity(),
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitepagenoteEdit($row) {
    $subject = Engine_Api::_()->core()->getSubject();
    $sitepagenote = $this->getSitepageNoteObject();

    if (empty($sitepagenote)) {
      return false;
    }
    //PAGE ID
    $page_id = $sitepagenote->page_id;

    $check = $this->commonChecks();

    if (empty($check)) {
      return false;
    }

    return array(
        'label' => 'Edit Note',
        'route' => 'sitepagenote_edit',
        'class' => 'ui-btn-action',
        'params' => array(
            'note_id' => $subject->getIdentity(),
            'page_id' => $page_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitepagenoteDelete($row) {
    $subject = Engine_Api::_()->core()->getSubject();
    $sitepagenote = $this->getSitepageNoteObject();

    if (empty($sitepagenote)) {
      return false;
    }
    //PAGE ID
    $page_id = $sitepagenote->page_id;

    $check = $this->commonChecks();

    if (empty($check)) {
      return false;
    }

    return array(
        'label' => 'Delete Note',
        'route' => 'sitepagenote_delete',
        'class' => 'ui-btn-danger',
        'params' => array(
            'note_id' => $subject->getIdentity(),
            'page_id' => $page_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitepagenoteAdd($row) {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    $sitepagenote = $this->getSitepageNoteObject();

    if (empty($sitepagenote)) {
      return false;
    }
    //PAGE ID
    $page_id = $sitepagenote->page_id;

    $owner_id = $sitepagenote->owner_id;

    $viewer_id = $viewer->getIdentity();

    $allow_image = Engine_Api::_()->getApi('settings', 'core')->sitepagenote_allow_image;

    //CHECKS FOR ADD PHOTOS
    if ($owner_id != $viewer_id || empty($allow_image)) {
      return false;
    }

    if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
			return array(
					'label' => 'Add Photos',
					'route' => 'sitepagenote_photoupload',
					'class' => 'ui-btn-action',
					'params' => array(
							'note_id' => $subject->getIdentity(),
							'page_id' => $page_id,
              'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
					)
			);
    } else {
			return array(
					'label' => 'Add Photos',
					'route' => 'sitepagenote_sitemobilephotoupload',
					'class' => 'ui-btn-action',
					'params' => array(
							'note_id' => $subject->getIdentity(),
							'page_id' => $page_id,
              'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
					)
			);
    }
  }

  //PHOTO VIEW PAGE OPTIONS
  public function onMenuInitialize_SitepagenotePhotoEdit($row) {

     //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $subject = Engine_Api::_()->core()->getSubject();
    //GET ALBUM ID
    $note_id = $subject->note_id;
    
    $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $note_id);
    
    //GET PAGE ID
    $page_id = $sitepagenote->page_id;

    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    
    //PHOTO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT PHOTO
    if ($viewer_id != $sitepagenote->owner_id && $can_edit != 1) {
      return false;
    }


    return array(
        'label' => 'Edit',
        'route' => 'sitepagenote_photoedit',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
           'action' => 'photoedit',
            'photo_id' => $subject->photo_id,
            'note_id' => $note_id,
            'page_id' => $page_id,
             'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitepagenotePhotoDelete($row) {


     //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $subject = Engine_Api::_()->core()->getSubject();
    //GET ALBUM ID
    $note_id = $subject->note_id;
    
    $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $note_id);
    
    //GET PAGE ID
    $page_id = $sitepagenote->page_id;

    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    
    //PHOTO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT PHOTO
    if ($viewer_id != $sitepagenote->owner_id && $can_edit != 1) {
      return false;
    }

    return array(
        'label' => 'Delete',
        'route' => 'sitepagenote_removeimage',
        'class' => 'ui-btn-danger',
        'params' => array(
            'action' => 'remove',
            'photo_id' => $subject->photo_id,
            'note_id' => $note_id,
            'page_id' => $page_id,
            'owner_id' => $subject->user_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitepagenotePhotoShare($row) {
    $subject = Engine_Api::_()->core()->getSubject();
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    if(!$viewer_id){
      return false;
    }
    if (!SEA_PHOTOLIGHTBOX_SHARE) {
      return false;
    }
    return array(
        'label' => 'Share',
        'class' => 'ui-btn-action smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'activity',
            'action' => 'share',
            'type' => $subject->getType(),
            'id' => $subject->getIdentity(),
        )
    );
  }

  public function onMenuInitialize_SitepagenotePhotoReport($row) {
    $subject = Engine_Api::_()->core()->getSubject();
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    if(!$viewer_id){
      return false;
    }
    if (!SEA_PHOTOLIGHTBOX_REPORT) {
      return false;
    }
    return array(
        'label' => 'Report',
        'class' => 'ui-btn-action smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'subject' => $subject->getGuid(),
        )
    );
  }

   //COMMON FUNCTION
  public function commonChecks() {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    $sitepagenote = $this->getSitepageNoteObject();

    if (empty($sitepagenote)) {
      return false;
    }
    //PAGE ID
    $page_id = $sitepagenote->page_id;
    $owner_id = $sitepagenote->owner_id;

    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    $viewer_id = $viewer->getIdentity();

    //EDIT
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }

    //CHECKS FOR DELETE
    if ($owner_id != $viewer_id && empty($can_edit)) {
      return false;
    } else {
      return true;
    }
  }

  public function getSitepageNoteObject() {
    $subject = Engine_Api::_()->core()->getSubject();
    //GET NOTE ID
    $note_id = $subject->getIdentity();
    //GET NOTE ITEM
    $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $note_id);
    //ASK
    if (empty($sitepagenote)) {
      return false;
    }
    return $sitepagenote;
  }

}

?>