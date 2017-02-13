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
class Document_Widget_SearchDocumentsController extends Engine_Content_Widget_Abstract
{ 
  public function indexAction()
  {
		//GET VIEWER ID
		$viewer = Engine_Api::_()->user()->getViewer();
		$viewer_id = $viewer->getIdentity();

    //GET USER LEVEL ID
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
    }

		//WHO CAN VIEW THE DOCUMENTS
		$can_view = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'view');
		if(empty($can_view)) {
			return $this->setNoRender();
		}

  	//GENERATE SEARCH FORM
		$this->view->form = $form = new Document_Form_Search(array('type' => 'document'));
		$form->removeElement('draft');

    if (empty($viewer_id)) {
      $form->removeElement('show');
    }

		$Zend_getParam = Zend_Controller_Front::getInstance()->getRequest();

    $category = $Zend_getParam->getParam('category_id', null);
    $subcategory = $Zend_getParam->getParam('subcategory_id', null);
    $categoryname = $Zend_getParam->getParam('categoryname', null);
    $subcategoryname = $Zend_getParam->getParam('subcategoryname', null);
    $subsubcategory = $Zend_getParam->getParam('subsubcategory_id', null);
    $subsubcategoryname = $Zend_getParam->getParam('subsubcategoryname', null);
    $cattemp = $Zend_getParam->getParam('category', null);

		//GET CATEGORY TABLE
		$tableCategory = Engine_Api::_()->getDbTable('categories', 'document');

    if(!empty($cattemp)) 
    {
    	$this->view->category_id = $_GET['category'] = $Zend_getParam->getParam('category');
    	$row = $tableCategory->getCategory($this->view->category_id);
	    if (!empty($row->category_name)) {
	      $categoryname = $this->view->category_name = $_GET['categoryname'] = $row->category_name;
	    }
	    
	    $categorynametemp = $Zend_getParam->getParam('categoryname', null);
	    $subcategorynametemp = $Zend_getParam->getParam('subcategoryname', null);
	    if (!empty($categorynametemp)) {
		    $categoryname = $this->view->category_name = $_GET['categoryname'] = $categorynametemp;
	    }
			if (!empty($subcategorynametemp)) {
		    $subcategoryname = $this->view->subcategory_name = $_GET['subcategoryname'] = $subcategorynametemp;
	    }
	  } else {
      if($categoryname)
	    $this->view->category_name = $_GET['categoryname'] = $categoryname;      
	    if($category) {
	      $this->view->category_id = $_GET['category_id'] = $category;
        $row = $tableCategory->getCategory($this->view->category_id);
        if (!empty($row->category_name)) {
          $this->view->category_name = $_GET['categoryname'] = $categoryname = $row->category_name;
        }
      }	    
    }
    
    $subcattemp = $Zend_getParam->getParam('subcategory', null);

    if(!empty($subcattemp)) 
    {
    	$this->view->subcategory_id = $_GET['subcategory_id'] = $Zend_getParam->getParam('subcategory');
	    $row = $tableCategory->getCategory($this->view->subcategory_id);
	    if (!empty($row->category_name)) {
	      $this->view->subcategory_name = $row->category_name;
	    }
    } else {
        if($subcategoryname)
				  $this->view->subcategory_name = $_GET['subcategoryname'] = $subcategoryname;        
        if($subcategory) {
          $this->view->subcategory_id = $_GET['subcategory_id'] = $subcategory;
          $row = $tableCategory->getCategory($this->view->subcategory_id);
          if (!empty($row->category_name)) {
            $this->view->subcategory_name = $_GET['subcategoryname'] = $subcategoryname = $row->category_name;
          }
       }		   
    }

    $subsubcattemp = $Zend_getParam->getParam('subsubcategory', null);

    if(!empty($subsubcattemp))
    {
    	$this->view->subsubcategory_id = $_GET['subsubcategory_id'] = $Zend_getParam->getParam('subsubcategory');
	    $row = $tableCategory->getCategory($this->view->subsubcategory_id);
	    if (!empty($row->category_name)) {
	      $this->view->subsubcategory_name = $row->category_name;
	    }
    } else {
        if($subsubcategoryname)
				  $this->view->subsubcategory_name = $_GET['subsubcategoryname'] = $subsubcategoryname;

        if($subsubcategory) {
          $this->view->subsubcategory_id = $_GET['subsubcategory_id'] = $subsubcategory;
          $row = $tableCategory->getCategory($this->view->subsubcategory_id);
          if (!empty($row->category_name)) {
            $this->view->subsubcategory_name = $_GET['subsubcategoryname'] = $subsubcategoryname = $row->category_name;
          }
       }
    }

    if(empty($categoryname)) {
      $_GET['category'] = $this->view->category_id = 0;
			$_GET['subcategory'] = $this->view->subcategory_id = 0;
      $_GET['subsubcategory'] = $this->view->subsubcategory_id = 0;
			$_GET['categoryname'] = $categoryname;
			$_GET['subcategoryname'] = $subcategoryname;
      $_GET['subsubcategoryname'] = $subsubcategoryname;
    }

		$action = $Zend_getParam->getActionName();
		if(!isset($_GET['orderby']) && $action == 'browse') {
			$orderBy = Engine_Api::_()->document()->showSelectedBrowseBy();
			if(!empty($orderBy)) {
				$_GET['orderby'] = $orderBy;
			}
		}

		$prefield_data = array_merge($_GET, $_POST);

		//POPULATE SEARCH FORM
		$form->populate($prefield_data);

		//SHOW PROFILE FIELDS ON DOME READY
    $category_search = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('document', 'category_id');
    if (!empty($category_search) && !empty($category_search->display) && !empty($_GET['category'])) {
			//GET PROFILE MAPPING ID
			$this->view->profileType = Engine_Api::_()->getDbTable('profilemaps', 'document')->getProfileType($_GET['category']);
		}
  }
}