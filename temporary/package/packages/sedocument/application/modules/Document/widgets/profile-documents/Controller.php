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
class Document_Widget_ProfileDocumentsController extends Seaocore_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
		//GET VIEWER DETAIL
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();
		$profile_view = Zend_Registry::get('document_profile_view');

    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = Engine_Api::_()->user()->getViewer()->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

		//WHO CAN VIEW THE DOCUMENTS
		$can_view = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'view');
		if(empty($can_view)) {
			return $this->setNoRender();
		}

		//DON'T RENDER IF SUBJECT IS NOT SET
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

		//GET SUBJECT
    $subject = Engine_Api::_()->core()->getSubject();
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

		if (empty($profile_view)) {
			return $this->setNoRender();
		}
    
    //NUMBER OF DOCUMENTS IN LISTING
		$this->view->items_per_page = $total_documents = $this->_getParam('itemCountPerPage', 10);
		
    //CHECK THAT RATING IS VIEABLE OR NOT
    $this->view->show_rate = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.rating', 1);

		//DOCUMENT RATING
		$this->view->document_rating = Zend_Registry::get('document_rating');
    
    //OWNER AND SUPER ADMIN CAN VIEW ALL PROFILE DOCUMENTS 
    $profile_owner_id = Engine_Api::_()->core()->getSubject()->getIdentity();
    $network_based_content = 1;
    if($viewer_id == $profile_owner_id || $level_id == 1) {
      $network_based_content = 0;
    }

    //FETCH DOCUMENTS
    $this->view->paginator = Engine_Api::_()->getDbtable('documents', 'document')->getDocumentsPaginator(array(
      'orderby' => 'document_id',
      'draft'  => '0',
      'approved' => '1',
      'status' => '1',
      'owner_id' =>  $profile_owner_id,
      'network_based_content' => $network_based_content  
    ));

    $this->view->paginator->setItemCountPerPage($total_documents);
    $this->view->paginator->setCurrentPageNumber($this->_getParam('page', 1));

    if( $this->view->paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

    if($this->_getParam('titleCount', false)) {
      $this->_childCount = $this->view->paginator->getTotalItemCount();
    } 
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}
?>