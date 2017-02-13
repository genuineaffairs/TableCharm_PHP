<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Document_AdminManageController extends Core_Controller_Action_Admin
{
	//ACTION FOR MANAGING THE DOCUMENTS
  public function indexAction()
  {
		//GET NAVIGATION
		$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
				->getNavigation('document_admin_main', array(), 'document_admin_main_manage');

		$document_tab = 'view_document';

		//GET PAGE
		$page = $this->_getParam('page',1);
		include_once APPLICATION_PATH . '/application/modules/Document/controllers/license/license2.php';

		//MAKE QUERY
    $tableUser = Engine_Api::_()->getItemTable('user')->info('name');

		//GET DOCUMENT TABLE
    $tableDocument = Engine_Api::_()->getDbtable('documents', 'document');
    $tableDocumentName = $tableDocument->info('name');

		//GET CATEGORY TABLE
		$tableCategory = Engine_Api::_()->getDbtable('categories', 'document');
		$tableCategoryName = $tableCategory->info('name');

		//MAKE QUERY
    $select = $tableDocument->select()
            ->setIntegrityCheck(false)
            ->from($tableDocumentName, array('document_id', 'owner_id', 'document_title', 'document_description', 'doc_id', 'creation_date', 'rating', 'comment_count', 'views', 'like_count', 'featured', 'featured', 'approved', 'sponsored', 'status', 'category_id'))
            ->joinLeft($tableUser, "$tableDocumentName.owner_id = $tableUser.user_id", 'username')
						->joinLeft($tableCategoryName, "$tableDocumentName.category_id = $tableCategoryName.category_id", 'category_name');

    $values = $_GET;//array();

		if(!empty($_POST['owner'])) { $user_name = $_POST['owner']; } elseif(!empty($_GET['owner'])) { $user_name = $_GET['owner']; }  else { $user_name = '';}

		if(!empty($_POST['document_title'])) { $document_title = $_POST['document_title']; } elseif(!empty($_GET['document_title'])) { $document_title = $_GET['document_title']; } else { $document_title = '';}

		if(!empty($_POST['featured'])) { $featured = $_POST['featured']; } elseif(!empty($_GET['featured'])) { $featured = $_GET['featured']; } else { $featured = '';}

		if(!empty($_POST['sponsored'])) { $sponsored = $_POST['sponsored']; } elseif(!empty($_GET['sponsored'])) { $sponsored = $_GET['sponsored']; } else { $sponsored = '';}

		if(!empty($_POST['approved'])) { $approved = $_POST['approved']; } elseif(!empty($_GET['approved'])) { $approved = $_GET['approved']; } else { $approved = '';}

		if(!empty($_POST['document_browse'])) { $document_browse = $_POST['document_browse']; } elseif(!empty($_GET['document_browse'])) { $document_browse = $_GET['document_browse']; } else { $document_browse = '';}

		if(!empty($_POST['category_id'])) { $category_id = $_POST['category_id']; } elseif(!empty($_GET['category_id'])) { $category_id = $_GET['category_id']; } else { $category_id = '';}

		if(!empty($_POST['subcategory_id'])) { $subcategory_id = $_POST['subcategory_id']; } elseif(!empty($_GET['subcategory_id'])) { $subcategory_id = $_GET['subcategory_id']; } else { $subcategory_id = '';}

		if(!empty($_POST['subsubcategory_id'])) { $subsubcategory_id = $_POST['subsubcategory_id']; } elseif(!empty($_GET['subsubcategory_id'])) { $subsubcategory_id = $_GET['subsubcategory_id']; } else { $subsubcategory_id = '';}

		//SEARCHING
		$this->view->owner = $values['owner'] = $user_name;
		$this->view->document_title = $values['document_title'] = $document_title; 
		$this->view->sponsored = $values['sponsored'] = $sponsored;
		$this->view->approved = $values['approved'] = $approved;
		$this->view->featured = $values['featured'] = $featured;
		$this->view->document_browse = $values['document_browse'] = $document_browse;
		$this->view->category_id = $values['category_id'] = $category_id;
		$this->view->subcategory_id = $values['subcategory_id'] = $subcategory_id;
		$this->view->subsubcategory_id = $values['subsubcategory_id'] = $subsubcategory_id;

		if (!empty($document_title)) {
			$select->where($tableDocumentName . '.document_title  LIKE ?', '%' . $document_title . '%');
		}    

		if (!empty($user_name)) {
			$select->where($tableUser . '.username  LIKE ?', '%' . $user_name . '%');
		}

		if (!empty($sponsored)) {
			$select->where($tableDocumentName . '.sponsored = ? ', $sponsored-1);
		}

		if (!empty($featured)) {
			$select->where($tableDocumentName . '.featured = ? ', $featured-1);
		}

		if (!empty($approved)) {
			$select->where($tableDocumentName . '.approved = ? ', $approved-1);
		}

		if (!empty($category_id) && empty($subcategory_id) && empty($subsubcategory_id)) {
			$select->where($tableDocumentName . '.category_id = ? ', $category_id);
		} 
		elseif (!empty($category_id) && !empty($subcategory_id) && empty($subsubcategory_id)) {

			$subcategory = $tableCategory->getCategory($subcategory_id);
			if (!empty($subcategory->category_name)) {
				$this->view->subcategory_name = $subcategory->category_name;
			}

			$select->where($tableDocumentName . '.category_id = ? ', $category_id)
							->where($tableDocumentName . '.subcategory_id = ? ', $subcategory_id);
		}
		elseif (!empty($category_id) && !empty($subcategory_id) && !empty($subsubcategory_id)) {
			
			$subcategory = $tableCategory->getCategory($subcategory_id);
			if (!empty($subcategory->category_name)) {
				$this->view->subcategory_name = $subcategory->category_name;
			}

			$subsubcategory = $tableCategory->getCategory($subsubcategory_id);
			if (!empty($subsubcategory->category_name)) {
				$this->view->subsubcategory_name = $subsubcategory->category_name;
			}

			$select->where($tableDocumentName . '.category_id = ? ', $category_id)
							->where($tableDocumentName . '.subcategory_id = ? ', $subcategory_id)
							->where($tableDocumentName . '.subsubcategory_id = ? ', $subsubcategory_id);
		}

    $this->view->formValues = array_filter($values);

		if(!empty($document_browse)) {
			$select->order($tableDocumentName . ".$document_browse DESC");
		}

		if(isset($values['order']) && !empty($values['order'])) {
			$select->order($values['order'] . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
		}
		
		if($document_browse != 'document_id' && (isset($values['order']) && $values['order'] != 'document_id')) {
			$select->order('document_id ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
		}

    //MAKE PAGINATOR
    $this->view->paginator = Zend_Paginator::factory($select);
    $this->view->paginator->setItemCountPerPage(100);
    $this->view->paginator->setCurrentPageNumber($page);
  }

	//ACTION FOR MAKE THE DOCUMENT FEATURED/UNFEATURED
  public function featuredAction() {

		//GET DOCUMENT ID
  	$document_id = $this->_getParam('document_id');

		//BEGIN TRANSCATION
   	$db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {

			//GET DOCUMENT OBJECT
     	$document = Engine_Api::_()->getItem('document', $document_id);
     	if($document->featured == 0) {
   		  $document->featured = 1;	
   		}
   		else {
   			$document->featured = 0;
   		}

			//SAVE CHANGES AND COMMIT
   		$document->save();
 			$db->commit();
	 	}
   	catch( Exception $e ){
     $db->rollBack();
     throw $e;
   	}

		//REDIRECT
  	$this->_redirect('admin/document/manage');   
 	}

	//ACTION FOR MAKE THE DOCUMENT SPONSORED/UNSPONSORED
  public function sponsoredAction() {

		//GET DOCUMENT ID
  	$document_id = $this->_getParam('document_id');

		//BEGIN TRANSCATION
   	$db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {

			//GET DOCUMENT OBJECT
     	$document = Engine_Api::_()->getItem('document', $document_id);
     	if($document->sponsored == 0) {
   		  $document->sponsored = 1;	
   		}
   		else {
   			$document->sponsored = 0;
   		}

			//SAVE CHANGES AND COMMIT
   		$document->save();
 			$db->commit();
	 	}
   	catch( Exception $e ){
     $db->rollBack();
     throw $e;
   	}

		//REDIRECT
  	$this->_redirect('admin/document/manage');   
 	}
  
 	//ACTION FOR MAKE DOCUMENT APPROVE/DIS-APPROVE
  public function approvedAction() 
  {
		//GET DOCUMENT ID
  	$document_id = $this->_getParam('document_id');

		//BEGIN TRANSCATION
   	$db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {

			//GET DOCUMENT OBJECT
    	$document = Engine_Api::_()->getItem('document', $document_id);
     	if($document->approved == 0){
   		  $document->approved = 1;	
   		}
   		else {
   			$document->approved = 0;
   		}

			//SAVE CHANGES AND COMMIT
   		$document->save();
 			$db->commit();
  	}
    catch( Exception $e ) {
    	$db->rollBack();
      throw $e;
    }

		//REDIRECT
    $this->_redirect('admin/document/manage');   
  }
   
  //ACTION FOR DELETE THE DOCUMENT
  public function deleteAction()
  {
		//SET LAYOUT
		$this->_helper->layout->setLayout('admin-simple');

		//GET DOCUMENT ID
		$this->view->document_id = $document_id = $this->_getParam('document_id');

		if( $this->getRequest()->isPost()){

			//DELETE DOCUMENT BELONGINGS
			Engine_Api::_()->document()->deleteContent($document_id);	

			$this->_forward('success', 'utility', 'core', array(
			   'smoothboxClose' => 10,
			   'parentRefresh'=> 10,
			   'messages' => array('')
			));
   	}
		$this->renderScript('admin-manage/delete.tpl');
	}
 
  //ACTION FOR MULTI DELETE DOCUMENTS
  public function multiDeleteAction()
  {
    if ($this->getRequest()->isPost()) {

			//GET FORM VALUES
      $values = $this->getRequest()->getPost();
      foreach ($values as $key=>$value) {
        if ($key == 'delete_' . $value) {

        	//GET DOCUMENT ID
          $document_id = (int)$value;

					//DELETE DOCUMENT BELONGINGS
					Engine_Api::_()->document()->deleteContent($document_id);
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }
}