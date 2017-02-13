<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
include_once APPLICATION_PATH . '/application/modules/Document/Api/Scribd.php';
class Document_Plugin_Menus {

	//DOCUMENT VIEW PRIVACY CHECK
  public function canViewDocuments() {

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

		//CHECK DOCUMENT VIEW PRIVACY
    if (!Engine_Api::_()->authorization()->isAllowed('document', $viewer, 'view')) {
      return false;
    }
    return true;
  }

	//DOCUMENT CREATION PRIVACY CHECK
  public function canCreateDocuments() {

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

		//CHECK DOCUMENT CREATION PRIVACY
    if (!Engine_Api::_()->authorization()->isAllowed('document', $viewer, 'create')) {
      return false;
    }
    return true;
  }

	//ADD DOCUMENT LISTING LINK
  public function onMenuInitialize_DocumentGutterList() {

		//RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//GET SUBJECT
    $document = Engine_Api::_()->core()->getSubject();
    if ($document->getType() !== 'document') {
      return false;
    }

    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

    //USER IS ALLOWED TO VIEW OR NOT
    $can_view = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'view');
		if(empty($can_view)) {
			return false;
		}

		//GET OWNER LINKED TITLE
		$owner_title = $document->getOwner()->getTitle();

    return array(
        'class' => 'buttonlink icon_type_document',
        'route' => 'document_list',
				'label' => $owner_title.Zend_Registry::get('Zend_Translate')->_("'s Documents"),
        'params' => array(
            'user_id' => $document->owner_id,
        ),
    );
  }

	//ADD DOCUMENT PUBLISH LINK
  public function onMenuInitialize_DocumentGutterPublish() {

		//RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//GET SUBJECT
    $document = Engine_Api::_()->core()->getSubject();
    if ($document->getType() !== 'document'  || empty($document->draft) || empty($viewer_id)) {
      return false;
    }

    //USER IS ALLOWED FOR PUBLISH OR NOT
    $can_edit = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'document', 'edit');
		if(empty($can_edit) || ($can_edit == 1 && $viewer_id != $document->owner_id)) {
			return false;
		}

    return array(
        'class' => 'buttonlink smoothbox icon_document_publish',
        'route' => 'document_publish',
        'params' => array(
            'document_id' => $document->getIdentity(),
        ),
    );
  }

	//ADD DOCUMENT EDIT LINK
  public function onMenuInitialize_DocumentGutterEdit($row) {

		//RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//GET SUBJECT
    $document = Engine_Api::_()->core()->getSubject();
    if ($document->getType() !== 'document' || empty($viewer_id)) {
      return false;
    }

    //USER IS ALLOWED FOR EDIT OR NOT
    $can_edit = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'document', 'edit');
		if(empty($can_edit) || ($can_edit == 1 && $viewer_id != $document->owner_id)) {
			return false;
		}

    return array(
        'class' => 'buttonlink icon_type_document_edit',
        'route' => 'document_edit',
        'params' => array(
            'document_id' => $document->getIdentity(),
        ),
    );
  }

	//ADD DOCUMENT SHARE LINK
  public function onMenuInitialize_DocumentGutterShare() {

		//RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//GET SUBJECT
    $document = Engine_Api::_()->core()->getSubject();
    if ($document->getType() !== 'document' || empty($viewer_id) || $document->draft == 1 || empty($document->approved) || $document->status != 1) {
      return false;
    }

		//SHARING IS ALLOWED OR NOT
    $can_share = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('document.share', 1);
    if (empty($can_share)) {
      return false;
    }

    return array(
        'class' => 'smoothbox buttonlink seaocore_icon_share',
        'route' => 'default',
        'params' => array(
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'share',
            'type' => $document->getType(),
            'id' => $document->getIdentity(),
            'format' => 'smoothbox',
        ),
    );
  }

	//ADD DOCUMENT DOWNLOAD LINK
  public function onMenuInitialize_DocumentGutterDownload($row) {

		//RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//GET SUBJECT
    $document = Engine_Api::_()->core()->getSubject();
    if ($document->getType() !== 'document' || empty($viewer_id)) {
      return false;
    }

		//DOWNLOAD IS ALLOWED OR NOT FOR THIS LEVEL
		$view_download_allow = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'document', 'view_download');
		if(empty($view_download_allow) || empty($document->download_allow)) {
			return false;
		}

		//GET SCRIBD DETAIL
    $scribd_api_key = Engine_Api::_()->getApi('settings', 'core')->document_api_key;
    $scribd_secret = Engine_Api::_()->getApi('settings', 'core')->document_secret_key;
    $scribd = new Scribd($scribd_api_key, $scribd_secret);

    //SET SCRIBD USER ID
    $scribd->my_user_id = $document->owner_id;

		//DOCUMENT FORMAT FOR THIS LEVEL
		$download_format = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'document', 'download_format');
		$link = '';
		try {
			$link = $scribd->getDownloadUrl($document->doc_id, $download_format);
			$link = trim($link['download_link']);
		} catch (Exception $e) {
			$this->view->excep_message = $message = $e->getMessage();
			$this->view->excep_error = 1;
		}

		if(empty($link)) {
			return false;
		}

    return array(
        'class' => 'buttonlink icon_document_download',
				'uri' => $link,
				'target' => '_blank'
    );
  }

	//ADD DOCUMENT EMAIL LINK
  public function onMenuInitialize_DocumentGutterEmail($row) {

		//RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//GET SUBJECT
    $document = Engine_Api::_()->core()->getSubject();
    if ($document->getType() !== 'document' || empty($viewer_id) || $document->status != 1) {
      return false;
    }

		//EMAIL IS ALLOWED OR NOT FOR THIS LEVEL
		$view_email_allow = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'document', 'view_email');
		if(empty($view_email_allow) || empty($document->email_allow)) {
			return false;
		}

    return array(
        'class' => 'buttonlink smoothbox icon_documents_email',
        'route' => 'default',
        'params' => array(
            'module' => 'document',
            'controller' => 'index',
            'action' => 'email',
            'id' => $document->getIdentity(),
        ),
    );
  }

	//ADD DOCUMENT SUGGESTION LINK	
  public function onMenuInitialize_DocumentGutterSuggest($row) {

		$is_suggestion_enabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
		$is_suggestion_active = Engine_Api::_()->suggestion()->getModSettings('document', 'link');
		if( empty($is_suggestion_enabled) || empty($is_suggestion_active) ) {
			return;
		}
	
		//RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//GET SUBJECT
    $document = Engine_Api::_()->core()->getSubject();
    if ($document->getType() !== 'document' || empty($viewer_id) || $document->status != 1 || empty($document->approved) || $document->draft == 1) {
      return false;
    }

		return array(
				'class' => 'buttonlink  notification_type_document_popup_suggestion smoothbox',
				'route' => 'default',
				'params' => array(
						'module' => 'suggestion',
						'controller' => 'index',
						'action' => 'switch-popup',
						'modName' => 'document',
						'modContentId' => $document->getIdentity(),
						'modError' => 1,
						'format' => 'smoothbox',
				),
		);

  }

	//ADD DOCUMENT REPORT LINK
  public function onMenuInitialize_DocumentGutterReport() {

		//RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//GET SUBJECT
    $document = Engine_Api::_()->core()->getSubject();
    if ($document->getType() !== 'document' || empty($viewer_id)) {
      return false;
    }

		//REPORTING IS ALLOWED OR NOT
    $report = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('document.report', 1);
    if (empty($report)) {
      return false;
    }

    return array(
        'class' => 'smoothbox buttonlink seaocore_icon_report',
        'route' => 'default',
        'params' => array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'subject' => $document->getGuid(),
            'format' => 'smoothbox',
        ),
    );
  }

	//ADD DOCUMENT DELETE LINK
  public function onMenuInitialize_DocumentGutterDelete() {

		//RETURN FALSE IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject()) {
      return false;
    }

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//GET SUBJECT
    $document = Engine_Api::_()->core()->getSubject();
    if ($document->getType() !== 'document' || empty($viewer_id)) {
      return false;
    }

    //USER IS ALLOWED FOR DELETE OR NOT
    $can_delete = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'document', 'delete');
		if(empty($can_delete) || ($can_delete == 1 && $viewer_id != $document->owner_id)) {
			return false;
		}

    return array(
        'class' => 'buttonlink icon_type_document_delete',
        'route' => 'document_delete',
        'params' => array(
            'document_id' => $document->getIdentity(),
        ),
    );
  }
}