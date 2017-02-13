<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
include_once APPLICATION_PATH . '/application/modules/Document/Api/Scribd.php';
class Document_Widget_ProfileDocDocumentsController extends Seaocore_Content_Widget_Abstract
{
  public function indexAction()
  {
		//DON'T RENDER IF SUBJECT IS NOT SET
		if(!Engine_Api::_()->core()->hasSubject()) {
			return $this->setNoRender();
		}

		//GET VIEWER DETAIL
		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //GET LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

		//DON'T RENDER IF SUBJECT IS NOT SET OR CAN NOT VIEW DOCUMENTS
		$can_view = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'view');
		if(empty($can_view)) {
			return $this->setNoRender();
		}

		//GET SUBJECT
		$subject = Engine_Api::_()->core()->getSubject();
		if( !$subject->authorization()->isAllowed($viewer, 'view') || $subject->getType() != 'user') {
			return $this->setNoRender();
		}

		//PROFILE DOC IS ALLOWED FOR THIS PROFILE OWNER OR NOT
		$can_profile_doc = Engine_Api::_()->authorization()->getPermission($subject->level_id, 'document', 'profile_doc');
		if(empty($can_profile_doc)){
			return $this->setNoRender();
		}

		//GET DOCUMENT TABLE
		$tableDocument = Engine_Api::_()->getDbtable('documents', 'document');

		//GET DOCUMENT ID
		$document_id = $tableDocument->getProfileDocId($subject->user_id);

		//SET NO RENDER IF DOCUMENT ID IS EMPTY
		if(empty($document_id)){
			return $this->setNoRender();
		}

		//GET DOCUMNET MODEL
		$this->view->document = $document = Engine_Api::_()->getItem('document', $document_id);
    
    if(Engine_Api::_()->getApi('settings', 'core')->getSetting('document.network', 0) && ($document->owner_id != $viewer_id && $level_id != 1 && $document->networks_privacy != NULL)) {
      $viewerNetworkIds = Engine_Api::_()->getDbtable('membership', 'network')->getMembershipsOfIds($viewer);
      
      if(Engine_Api::_()->getApi('settings', 'core')->getSetting('document.networks.type', 0)) {
        $documentNetworkIds = $document->networks_privacy;
      }
      else {
        $documentNetworkIds = Engine_Api::_()->getDbtable('membership', 'network')->getMembershipsOfIds($document->getOwner());
      }

      $commonNetworks = array_intersect($viewerNetworkIds, $documentNetworkIds);
      
      if(count($commonNetworks) <= 0) {
        return $this->setNoRender();
      }
    }
    
		//SET NO RENDER IF DOCUMENT IS NOT AUTHORIZED
		if(( $viewer_id != $document->owner_id && $can_view != 2) && ( empty($document) ||  $document->status != 1 || $document->approved != 1 || $document->draft == 1)) {
			return $this->setNoRender();
		}

		//WHO CAN EDIT THE DOCUMENT
    $this->view->can_edit = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'edit');

		//GET PROFILE OWNER OBJECT
		$this->view->user = Engine_Api::_()->getItem('user', $document->owner_id);

		//GET WIDGET SETTINGS
		$this->view->documentViewerHeight = $this->_getParam('documentViewerHeight', 600);
		$this->view->documentViewerWidth = $this->_getParam('documentViewerWidth', 730);

    //SET SCRIBD API AND SCECRET KEY
    $scribd_api_key = Engine_Api::_()->getApi('settings', 'core')->document_api_key;
    $scribd_secret = Engine_Api::_()->getApi('settings', 'core')->document_secret_key;
    $scribd = new Scribd($scribd_api_key, $scribd_secret);
		
    //CHECK THAT VIEWER CAN RATE THE DOCUMENT OR NOT
    $this->view->can_rate = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.rating', 1);

    //WHICH TYPE OF DOCUMENT READER WE HAVE TO SHOW TO USER
    $this->view->document_viewer = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.viewer', 1);

    //CHECK THAT VIEWER CAN SHARE THE DOCUMENT OR NOT
		$this->view->can_share = $this->_getParam('share', 1);
		if(!empty($this->view->can_share)) {
			$this->view->can_share = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.share', 1);
		}

    //CHECK THAT USER CAN REPORT THE DOCUMENT OR NOT
		$this->view->can_report = $this->_getParam('report', 1);
		if(!empty($this->view->can_report)) {
			$this->view->can_report = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.report', 1);
		}

    //CHECK THAT WE HAVE TO SHOW SOCIAL SHARE BUTTON OR NOT
		$this->view->code = $this->_getParam('code', '');
		$this->view->can_social_share = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.button.share', 1);
		
    //CHECK THAT WE HAVE TO SHOW COMMENT AND LIKE BOX OR NOT
		$this->view->can_comment_like = $this->_getParam('comment_like', 1);

    //CHECK THAT WE HAVE TO SHOW EMAIL LINK OR NOT
		$this->view->view_email = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'view_email');
		$this->view->can_email = $this->_getParam('email', 1);

    //CHECK THAT WE HAVE TO SHOW EMAIL LINK OR NOT
		$this->view->view_download = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'view_download');
		$this->view->can_download = $this->_getParam('download', 1);

    //GET CURRENT URL
    $front = Zend_Controller_Front::getInstance();
    $curr_url = 'http://' . $_SERVER['HTTP_HOST'] . $front->getRequest()->getRequestUri();
    $this->view->curr_url = urlencode((string) $curr_url);

    //GET CATEGORY TABLE
		$categoryTable = Engine_Api::_()->getDbtable('categories', 'document');

		//GET CATEGORIES DETAIL
		$this->view->category_name = $this->view->subcategory_name = $this->view->subsubcategory_name = '';
    $categoriesNmae = $categoryTable->getCategory($document->category_id);
    if (!empty($categoriesNmae->category_name)) {
      $this->view->category_name = $categoriesNmae->category_name;
    }
    $subcategory_name = $categoryTable->getCategory($document->subcategory_id);
    if (!empty($subcategory_name->category_name)) {
      $this->view->subcategory_name = $subcategory_name->category_name;
    }
    //GET SUB-SUB-CATEGORY
    $subsubcategory_name = $categoryTable->getCategory($document->subsubcategory_id);
    if (!empty($subsubcategory_name->category_name)) {
      $this->view->subsubcategory_name = $subsubcategory_name->category_name;
    }

    //SET SCRIBD USER ID
    $scribd->my_user_id = $document->owner_id;

		//CHECK DOWNLOAD AND EMAIL OPTIONS
    if (!empty($viewer_id)) {
      $download_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'download_allow');
      $download_format = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'download_format');
    } else {
      $download_allow = 0;
      $download_format = 0;
    }

		$link = array();
		$this->view->link = '';
		if(!empty($viewer_id)) {
			if ($download_allow && $document->download_allow) {
				try {
					$link = $scribd->getDownloadUrl($document->doc_id, $download_format);
				} catch (Exception $e) {
					$this->view->excep_message = $message = $e->getMessage();
					$this->view->excep_error = 1;
				}
				if(!empty($link)) {
					$this->view->link = trim($link['download_link']);
				}
			}
		}

    //SETTING PARAMETERS FOR SECURE DOCUMENT
    if ($viewer_id == 0) {
      $uid = mt_rand(1000, 100000);
    } else {
      $uid = $viewer_id;
    }
    $scribd_secret = Engine_Api::_()->getApi('settings', 'core')->document_secret_key;
    $sessionId = session_id();
    $signature = md5($scribd_secret . 'document_id' . $document->doc_id . 'session_id' . $sessionId . 'user_identifier' . $uid);
    $this->view->uid = $uid;
    $this->view->sessionId = $sessionId;
    $this->view->signature = $signature;
    $this->view->download_allow = $download_allow;

    //SHOW ARCHIVES
		$this->view->owner = $owner = $document->getOwner();
		
    //FIND DOCUMENT OWNER TAGS
    $this->view->documentTags = $document->tags()->getTagMaps();

		//GET RATING TABLE
		$tableRating = Engine_Api::_()->getDbTable('ratings', 'document');
    $this->view->rating_count = $tableRating->countRating($document->getIdentity());
    $this->view->document_rated = $tableRating->previousRated($document->getIdentity(), $viewer_id);

    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    $view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($document);
  }
}
?>