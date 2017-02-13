<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagedocument_Plugin_Menus {

  public function canViewDocuments() {

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.document.show.menu', 1)) {
      return false;
    }

    $table = Engine_Api::_()->getDbtable('documents', 'sitepagedocument');
    $rName = $table->info('name');
    $table_pages = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $rName_pages = $table_pages->info('name');
    $select = $table->select()
                    ->setIntegrityCheck(false)
                    ->from($rName_pages, array(''))
                    ->join($rName, $rName . '.page_id = ' . $rName_pages . '.page_id')
                    ->where($rName .'.approved = ?', 1)
										->where($rName .'.draft = ?', 0)
										->where($rName .'.status = ?', 1)
										->where($rName .'.search = ?', 1);
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

   //ADD PAGE DOCUMENT LISTING LINK
  public function onMenuInitialize_SitepagedocumentGutterPage() {

		//GETTING THE VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GETTING THE TAB ID
    $tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);

    //GETTING THE SITEPAGEDOCUMENT SUBJECT
    $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', Zend_Controller_Front::getInstance()->getRequest()->getParam('document_id'));
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagedocument->page_id);   

    if ($sitepagedocument->getType() !== 'sitepagedocument_document') {
      return false;
    }

   $page_url = $sitepage->getHref(array('tab' => $tab_selected_id));

    //RETURN BACK TO PAGE LINK
    return array(
        'class' => 'buttonlink  icon_sitepagedocument_back',
        'uri' =>  $page_url
        
    );
  }

	//ADD PAGE DOCUMENT PUBLISH LINK
  public function onMenuInitialize_SitepagedocumentGutterPublish() {

		//GETTING THE VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();    
 
    //GETTING THE TAB ID
    $tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);

    //GETTING THE SITEPAGEDOCUMENT SUBJECT
    $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', Zend_Controller_Front::getInstance()->getRequest()->getParam('document_id'));
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagedocument->page_id);   

    //START MANAGE-ADMIN CHECK
		$isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK

    if ($sitepagedocument->getType() !== 'sitepagedocument_document' || empty($viewer_id) || empty($sitepagedocument->draft)) {
      return false;
    }

    //USER IS ALLOWED FOR DELETE OR NOT
		if(empty($can_edit) || ($viewer_id != $sitepagedocument->owner_id)) {
			return false;
		}

    return array(
        'class' => 'buttonlink smoothbox icon_sitepagedocument_publish',
        'route' => 'sitepagedocument_publish',
        'params' => array(
            'document_id' => $sitepagedocument->getIdentity(),
						'tab' => $tab_selected_id
        ),
    );
  }

	//ADD PAGE DOCUMENT EDIT LINK
  public function onMenuInitialize_SitepagedocumentGutterEdit($row) {

		 //GETTING THE VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();    
   
    $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', Zend_Controller_Front::getInstance()->getRequest()->getParam('document_id'));

    //GETTING THE TAB ID
    $tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);

    //GETTING THE SITEPAG SUBJECT
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagedocument->page_id);   

    //START MANAGE-ADMIN CHECK
		$isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK

    if ($sitepagedocument->getType() !== 'sitepagedocument_document' || empty($viewer_id)) {
      return false;
    }

    //USER IS ALLOWED FOR DELETE OR NOT
		if(empty($can_edit) && ($viewer_id != $sitepagedocument->owner_id)) {
			return false;
		}

    return array(
        'class' => 'buttonlink icon_sitepagedocument_edit',
        'route' => 'sitepagedocument_edit',
        'params' => array(
						'document_id' => $sitepagedocument->getIdentity(),
						'page_id' => $sitepagedocument->page_id,
						'tab' => $tab_selected_id
        ),
    );
  }


//ADD PAGE DOCUMENT SUGGESTION LINK	
  public function onMenuInitialize_SitepagedocumentGutterSuggest($row) {

		$sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', Zend_Controller_Front::getInstance()->getRequest()->getParam('document_id'));

    $page_subject = Engine_Api::_()->getItem('sitepage_page', $sitepagedocument->page_id);      

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
        $documentSuggLink = Engine_Api::_()->suggestion()->getModSettings('sitepage', 'document_sugg_link');
      }
    }
    else {
     return false;   
  
    }
 
    $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', Zend_Controller_Front::getInstance()->getRequest()->getParam('document_id'));

    if( empty($documentSuggLink) && !empty($sitepagedocument->draft) && empty($sitepagedocument->approved) && empty($sitepagedocument->status) )  {
      return false;
    }
 
    // End: "Suggest to Friend" link work

   return array(
				'class' => 'buttonlink icon_page_friend_suggestion smoothbox',
				'route' => 'default',
				'params' => array(
						'module' => 'suggestion',
						'controller' => 'index',
						'action' => 'switch-popup',
						'modContentId' => $sitepagedocument->document_id,
						'modName' => 'page_document',
						'format' => 'smoothbox',
				),
    );
  }


//ADD DOCUMENT DELETE LINK
  public function onMenuInitialize_SitepagedocumentGutterDelete() {

    //GETTING THE VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();    
 
    //GETTING THE TAB ID
    $tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    
    $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', Zend_Controller_Front::getInstance()->getRequest()->getParam('document_id'));

    //GETTING THE SITEPAGE SUBJECT
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagedocument->page_id);   

    //START MANAGE-ADMIN CHECK
		$isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK

    if ($sitepagedocument->getType() !== 'sitepagedocument_document' || empty($viewer_id)) {
      return false;
    }

    //USER IS ALLOWED FOR DELETE OR NOT
		if(empty($can_edit) && ($viewer_id != $sitepagedocument->owner_id)) {
			return false;
		}

    return array(
        'class' => 'buttonlink  icon_sitepagedocument_delete',
        'route' => 'sitepagedocument_delete',
        'params' => array(
								'document_id' => $sitepagedocument->getIdentity(),
								'page_id' => $sitepagedocument->page_id,
								'tab' => $tab_selected_id
        ),
    );
  }


  //ADD DOCUMENT OF THE DAY LINK
  public function onMenuInitialize_SitepagedocumentGutterDocumentofday() {

    //GETTING THE VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();    
 
    //GETTING THE TAB ID
    $tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    
    $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', Zend_Controller_Front::getInstance()->getRequest()->getParam('document_id'));

    //GETTING THE SITEPAGE SUBJECT
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagedocument->page_id);   

    $allowView = false;
    if (!empty($viewer_id) && $viewer->level_id == 1) {
      $auth = Engine_Api::_()->authorization()->context;
      $allowView = $auth->isAllowed($sitepage, 'everyone', 'view') === 1 ? true : false ||$auth->isAllowed($sitepage, 'registered', 'view') === 1 ? true : false;
    } 

    if(empty($allowView)) {
      return false;
   }

    return array(
				'class' => 'buttonlink item_icon_sitepagedocument_detail smoothbox',
				'route' => 'default',
				'params' => array(
						'module' => 'sitepagedocument',
						'controller' => 'index',
						'action' => 'add-document-of-day',
						'document_id' => $sitepagedocument->document_id,
						'format' => 'smoothbox',
				),
    );
  }


  //ADD PAGE DOCUMENT ADD LINK
  public function onMenuInitialize_SitepagedocumentGutterAdd() {
   
    //GETTING THE VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();      
    $tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);  
		$sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', Zend_Controller_Front::getInstance()->getRequest()->getParam('document_id'));

    $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepagedocument->page_id);  
 
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdcreate');
    if (empty($isManageAdmin)) {
      $can_create = 0;
    } else {
      $can_create = 1;
    }
    //END MANAGE-ADMIN CHECK

    if ($sitepagedocument->getType() !== 'sitepagedocument_document' || empty($viewer_id)) {
      return false;
    }

    //USER IS ALLOWED FOR DELETE OR NOT
		if(empty($can_create)) {
			return false;
		}

    return array(
        'class' => 'buttonlink icon_sitepagedocument_new',
        'route' => 'sitepagedocument_create',
        'params' => array(
								'page_id' => $sitepagedocument->page_id,
								'tab' => $tab_selected_id
        ),
    );
  }


	//ADD DOCUMENT SHARE LINK
  public function onMenuInitialize_SitepagedocumentGutterShare() {

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//GET OBJECT
		$document_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('document_id');
    $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', $document_id);

		//SHARING IS ALLOWED OR NOT
    $can_share = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.share', 1);

		//CAN SHARE OR NOT
    if (empty($sitepagedocument) || empty($viewer_id) || $sitepagedocument->draft == 1 || empty($sitepagedocument->approved) || $sitepagedocument->status != 1 || empty($can_share)) {
      return false;
    }

    return array(
        'class' => 'smoothbox buttonlink seaocore_icon_share',
        'route' => 'default',
        'params' => array(
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'share',
            'type' => 'sitepagedocument_document',
            'id' => $sitepagedocument->getIdentity(),
            'format' => 'smoothbox',
        ),
    );
  }

	//ADD DOCUMENT DOWNLOAD LINK
  public function onMenuInitialize_SitepagedocumentGutterDownload($row) {

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//GET OBJECT
		$document_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('document_id');
    $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', $document_id);

		//AUTORIZATION CHECKS
    if (empty($sitepagedocument) || empty($viewer_id)) {
      return false;
    }

		//DOWNLOAD IS ALLOWED OR NOT FOR THIS LEVEL
		$view_download_allow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.download.allow', 1);
		if(empty($view_download_allow) || empty($sitepagedocument->download_allow)) {
			return false;
		}

		//GET SCRIBD DETAIL
    $scribd_api_key = Engine_Api::_()->getApi('settings', 'core')->sitepagedocument_api_key;
    $scribd_secret = Engine_Api::_()->getApi('settings', 'core')->sitepagedocument_secret_key;
    $scribd = new Scribdsitepage($scribd_api_key, $scribd_secret);

    //SET SCRIBD USER ID
    $scribd->my_user_id = $sitepagedocument->owner_id;

		//DOCUMENT FORMAT FOR THIS LEVEL
		$download_format = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.download.format', 'pdf');
		$link = '';
		try {
			$link = $scribd->getDownloadUrl($sitepagedocument->doc_id, $download_format);
			$link = trim($link['download_link']);
		} catch (Exception $e) {
			$this->view->excep_message = $message = $e->getMessage();
			$this->view->excep_error = 1;
		}

		if(empty($link)) {
			return false;
		}

    return array(
        'class' => 'buttonlink icon_sitepagedocument_download',
				'uri' => $link,
				'target' => '_blank'
    );
  }

	//ADD DOCUMENT EMAIL LINK
  public function onMenuInitialize_SitepagedocumentGutterEmail($row) {

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//GET OBJECT
		$document_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('document_id');
    $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', $document_id);

		//AUTORIZATION CHECKS
    if (empty($sitepagedocument) || empty($viewer_id) || $sitepagedocument->status != 1) {
      return false;
    }

		//EMAIL IS ALLOWED OR NOT
		$view_email_allow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.email.allow', 1);
		if(empty($view_email_allow) || empty($sitepagedocument->email_allow)) {
			return false;
		}

    return array(
        'class' => 'buttonlink smoothbox icon_sitepagedocuments_email',
        'route' => 'default',
        'params' => array(
            'module' => 'sitepagedocument',
            'controller' => 'index',
            'action' => 'email',
            'id' => $sitepagedocument->getIdentity(),
        ),
    );
  }


	//ADD DOCUMENT REPORT LINK
  public function onMenuInitialize_SitepagedocumentGutterReport() {

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

		//GET OBJECT
		$document_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('document_id');
    $sitepagedocument = Engine_Api::_()->getItem('sitepagedocument_document', $document_id);

		//CAN REPORT OR NOT
    if (empty($viewer_id) || empty($sitepagedocument)) {
      return false;
    }

		//REPORTING IS ALLOWED OR NOT
    $report = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.report', 1);
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
            'subject' => $sitepagedocument->getGuid(),
            'format' => 'smoothbox',
        ),
    );
  }

}
?>