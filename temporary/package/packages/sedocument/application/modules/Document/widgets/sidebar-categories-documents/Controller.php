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
class Document_Widget_SidebarCategoriesDocumentsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//INTIALIZE DOCUMENT OWNER ID
		$document_owner_id = 0;

		//IF WIDGET IS PLACED ON DOCUMENT MAIN VIEW PAGE
		if (Engine_Api::_()->core()->hasSubject()) {
			$document_subject = Engine_Api::_()->core()->getSubject();
			if($document_subject->getType() == 'document') {
				$document_owner_id = $document_subject->owner_id;
			}
		}
	
		//GET DOCUMENT CATEGORY TABLE
    $tableCategory = Engine_Api::_()->getDbTable('categories', 'document');

    $categories = array();
    $category_info = $tableCategory->getCategories(1, $document_owner_id);
    foreach ($category_info as $value) {
      $sub_cat_array = array();
      $category_info2 = $tableCategory->getAllCategories($value['category_id'], 'subcategory_id', 0, 'subcategory_id', 0, 0, 0);
      foreach($category_info2 as $subresults) {
        $treesubarray = array();
        $subcategory_info2 = $tableCategory->getAllCategories($subresults['category_id'], 'subcategory_id', 0, 'subcategory_id', 0, 0, 0);
        $treesubarrays[$subresults->category_id] = array();
        foreach($subcategory_info2 as $subvalues) {
           $treesubarrays[$subresults->category_id][] = $treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
            'tree_sub_cat_name' => $subvalues->category_name,
            'order' => $subvalues->cat_order,
            );
        }

        $tmp_array = array('sub_cat_id' => $subresults->category_id,
            'sub_cat_name' => $subresults->category_name,
            'tree_sub_cat' => $treesubarrays[$subresults->category_id],
            'order' => $subresults->cat_order);
        $sub_cat_array[] = $tmp_array;
      }

      $categories[] = $category_array = array('category_id' => $value->category_id,
          'category_name' => $value->category_name,
          'order' => $value->cat_order,
          'sub_categories' => $sub_cat_array);
    }
    
    $this->view->categories = $categories;
    $this->view->subcategorys = 0;
    $this->view->category = 0;
    $this->view->subsubcategorys = 0;

		$Zend_getParam = Zend_Controller_Front::getInstance()->getRequest();

    $categoryname = $Zend_getParam->getParam('categoryname', null);
    $subcategoryname = $Zend_getParam->getParam('subcategoryname', null);
    $subsubcategoryname = $Zend_getParam->getParam('subsubcategoryname', null);

    if($Zend_getParam->getParam('category') ) {
	    	
	    $categoryidtemp = $Zend_getParam->getParam('category');	
	    $subcategoryidtemp = $Zend_getParam->getParam('subcategory');
      if($Zend_getParam->getParam('subsubcategory')) {
        $subsubcategoryidtemp = $Zend_getParam->getParam('subsubcategory');
      } else {
        $subsubcategoryidtemp = $Zend_getParam->getParam('subsubcategory_id');
      }
	    if(!empty($categoryidtemp)) {
	    	$this->view->category = $categoryidtemp; 
	    	$this->view->subcategorys = $subcategoryidtemp;
        $this->view->subsubcategorys = $subsubcategoryidtemp;
	    }
    } elseif($Zend_getParam->getParam('category_id')) {
	    $categoryid = $Zend_getParam->getParam('category_id');
	    $subcategoryid = $Zend_getParam->getParam('subcategory_id');
      $subsubcategoryid = $Zend_getParam->getParam('subsubcategory_id');
	
	    if (!empty($categoryid)) {
	      $_GET['category_id'] = $this->view->category = $categoryid;
	      $_GET['categoryname'] = $categoryname;
	    } 
	    
	    if (!empty($subcategoryid)) {  
	      $_GET['subcategory_id'] = $this->view->subcategorys = $subcategoryid;
	      $_GET['subcategoryname'] = $subcategoryname;
	    }
      
      if (!empty($subsubcategoryid)) {
	      $_GET['subsubcategory_id'] = $this->view->subsubcategorys = $subsubcategoryid;
	      $_GET['subsubcategoryname'] = $subcategoryname;
	    }

	    if (!empty($_GET)) {
	      if (!empty($_GET['subcategory_id'])) {
	        $this->view->subcategorys = $_GET['subcategory_id'];
	      }
	      if (!empty($_GET['category_id'])) {
	        $this->view->category = $_GET['category_id'];
	      }
        if (!empty($_GET['subsubcategory_id'])) {
	        $this->view->subsubcategorys = $_GET['subsubcategory_id'];
	      }
	    }
    }
    if(empty($categoryname)) {
      $_GET['category'] = $this->view->category_id = $this->view->category = 0;
			$_GET['subcategory'] = $this->view->subcategory_id = 0;
      $_GET['subsubcategory'] = $this->view->subsubcategory_id = 0;
			$_GET['categoryname'] = 0;
			$_GET['subcategoryname'] = 0;
      $_GET['subsubcategoryname'] = 0;
    }
       
    if (!(count($this->view->categories) > 0)) {
      return $this->setNoRender();
    }
  }
}
?>