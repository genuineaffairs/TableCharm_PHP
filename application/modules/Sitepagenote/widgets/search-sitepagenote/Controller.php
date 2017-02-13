<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_Widget_SearchSitepagenoteController extends Engine_Content_Widget_Abstract {

  public function indexAction() {


    $this->view->showTabArray = $showTabArray = $this->_getParam("search_column", array("0" => "1", "1" => "2", "2" => "3", "3" => "4","4" => "5", "5" => "6"));

    $sitepage_searching = Zend_Registry::isRegistered('sitepage_searching') ? Zend_Registry::get('sitepage_searching') : null;

    $sitepagetable = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $sitepageName = $sitepagetable->info('name');
    $viewer = Engine_Api::_()->user()->getViewer()->getIdentity();
  
    //FORM CREATION
    $this->view->form = $form = new Sitepagenote_Form_Searchwidget(array('type' => 'sitepage_page'));
    $populateValue = 0;
    if( isset($form->show) ) {
			$sponsorednote = Zend_Controller_Front::getInstance()->getRequest()->getParam('sponsorednote', null);
			if(!empty($sponsorednote)) {
				$populateValue = 1;
				$form->show->setValue("sponsored note");
			}
		
			$featurednote = Zend_Controller_Front::getInstance()->getRequest()->getParam('featurednote', null);
			if(!empty($featurednote)) {
				$populateValue = 1;
				$form->show->setValue("featured");
			}
    }

    $commentednote = Zend_Controller_Front::getInstance()->getRequest()->getParam('commentednote', null);
    if(!empty($commentednote)) {
      $populateValue = 1;
			$form->orderby_browse->setValue("comment_count");
    }

    $likednote = Zend_Controller_Front::getInstance()->getRequest()->getParam('likednote', null);
    if(!empty($likednote)) {
      $populateValue = 1;
			$form->orderby_browse->setValue("like_count");
    } 

    $sitepage_post = Zend_Registry::isRegistered('sitepage_post') ? Zend_Registry::get('sitepage_post') : null;
    if (!empty($sitepage_post)) {
      $this->view->sitepage_post = $sitepage_post;
    }

    $category = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id', null);
    $subcategory = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory_id', null);
    $categoryname = Zend_Controller_Front::getInstance()->getRequest()->getParam('categoryname', null);
    $subcategoryname = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategoryname', null);
    $subsubcategory = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategory_id', null);
    $subsubcategoryname = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategoryname', null);
    $cattemp = Zend_Controller_Front::getInstance()->getRequest()->getParam('category', null);

    if(!empty($cattemp)) 
    {
    	$this->view->category_id  = $_GET['category'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('category');
    	$row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($this->view->category_id);
	    if (!empty($row->category_name)) {
	      $categoryname = $this->view->category_name  = $_GET['categoryname'] = $row->category_name;
	    }
	    
	    $categorynametemp = Zend_Controller_Front::getInstance()->getRequest()->getParam('categoryname', null);
	    $subcategorynametemp = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategoryname', null);
	    if (!empty($categorynametemp)) {
		    $categoryname = $this->view->category_name = $_GET['categoryname'] = $categorynametemp;
	    }
			if (!empty($subcategorynametemp)) {
		    $subcategoryname = $this->view->subcategory_name = $_GET['subcategoryname'] = $subcategorynametemp;
	    }
	  } else {
      if($categoryname)
	    $this->view->category_name = $_GET['categoryname'] =  $categoryname;      
	    if($category) {
	      $this->view->category_id = $_GET['category_id'] =  $category;
        $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($this->view->category_id);
        if (!empty($row->category_name)) {
          $this->view->category_name  = $_GET['categoryname'] = $categoryname = $row->category_name;
        }
      }	    
    }
    
    $subcattemp = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory', null);

    if(!empty($subcattemp)) 
    {
    	$this->view->subcategory_id = $_GET['subcategory_id'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory');
	    $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($this->view->subcategory_id);
	    if (!empty($row->category_name)) {
	      $this->view->subcategory_name = $row->category_name;
	    }
    } else {
        if($subcategoryname)
				  $this->view->subcategory_name = $_GET['subcategoryname'] =  $subcategoryname;        
        if($subcategory) {
          $this->view->subcategory_id = $_GET['subcategory_id'] =  $subcategory;
          $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($this->view->subcategory_id);
          if (!empty($row->category_name)) {
            $this->view->subcategory_name  = $_GET['subcategoryname'] = $subcategoryname = $row->category_name;
          }
       }		   
    }

    $subsubcattemp = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategory', null);

    if(!empty($subsubcattemp))
    {
    	$this->view->subsubcategory_id = $_GET['subsubcategory_id'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('subsubcategory');
	    $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($this->view->subsubcategory_id);
	    if (!empty($row->category_name)) {
	      $this->view->subsubcategory_name = $row->category_name;
	    }
    } else {
        if($subsubcategoryname)
				  $this->view->subsubcategory_name = $_GET['subsubcategoryname'] =  $subsubcategoryname;

        if($subsubcategory) {
          $this->view->subsubcategory_id = $_GET['subsubcategory_id'] =  $subsubcategory;
          $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($this->view->subsubcategory_id);
          if (!empty($row->category_name)) {
            $this->view->subsubcategory_name  = $_GET['subsubcategoryname'] = $subsubcategoryname = $row->category_name;
          }
       }
    }

    if(empty($categoryname)) {
      $_GET['category'] = $this->view->category_id =  0;
			$_GET['subcategory'] = $this->view->subcategory_id = 0;
      $_GET['subsubcategory'] = $this->view->subsubcategory_id = 0;
			$_GET['categoryname'] = $categoryname;
			$_GET['subcategoryname'] = $subcategoryname;
      $_GET['subsubcategoryname'] = $subsubcategoryname;
    }
    
    if ((!isset($_POST['orderby_browse']) || empty($_POST)) && empty($populateValue)) {
      $order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagenote.order', 1);
      if($order == 1) {
				$form->orderby_browse->setValue("creation_date");
      }
    }
    
    $_GET['note_category_id'] = Zend_Controller_Front::getInstance()->getRequest()->getParam('note_category_id', 0);     
    
    if (!empty($_GET))
      $form->populate($_GET);  
      
    if (empty($sitepage_searching)) {
      return $this->setNoRender();
    }
  }
}

?>