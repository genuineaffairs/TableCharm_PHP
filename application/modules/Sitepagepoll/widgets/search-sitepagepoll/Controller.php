<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagepoll_Widget_SearchSitepagepollController extends Engine_Content_Widget_Abstract {

  public function indexAction() {


    $this->view->showTabArray = $showTabArray = $this->_getParam("search_column", array("0" => "1", "1" => "2", "2" => "3", "3" => "4","4" => "5"));

    $sitepage_searching = Zend_Registry::isRegistered('sitepage_searching') ? Zend_Registry::get('sitepage_searching') : null;

    $sitepagetable = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $sitepageName = $sitepagetable->info('name');
    $viewer = Engine_Api::_()->user()->getViewer()->getIdentity();
  
    //FORM CREATION
    $this->view->form = $form = new Sitepagepoll_Form_Searchwidget(array('type' => 'sitepage_page'));

    $sponsoredpoll = Zend_Controller_Front::getInstance()->getRequest()->getParam('sponsoredpoll', null);
    $populateValue = 0;
    if( isset($form->show) ) {
			if(!empty($sponsoredpoll)) {
				$populateValue = 1;
				$form->show->setValue("sponsored poll");
			}
    }
    $commentedpoll = Zend_Controller_Front::getInstance()->getRequest()->getParam('commentedpoll', null);
    if(!empty($commentedpoll)) {
      $populateValue = 1;
			$form->orderby->setValue("comment_count");
    }

    $viewedpoll = Zend_Controller_Front::getInstance()->getRequest()->getParam('viewedpoll', null);
    if(!empty($viewedpoll)) {
      $populateValue = 1;
			$form->orderby->setValue("view_count");
    }
 
    $likedpoll = Zend_Controller_Front::getInstance()->getRequest()->getParam('likedpoll', null);
    if(!empty($likedpoll)) {
      $populateValue = 1;
			$form->orderby->setValue("like_count");
    }

    $votedpoll = Zend_Controller_Front::getInstance()->getRequest()->getParam('votedpoll', null);
    if(!empty($votedpoll)) {
      $populateValue = 1;
			$form->orderby->setValue("vote_count");
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
    
		if ((!isset($_POST['orderby']) || empty($_POST)) && empty($populateValue)) {
      $order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.order', 1);
      if($order == 1) {
				$form->orderby->setValue("creation_date");
      }
    }
    if (!empty($_GET))
      $form->populate($_GET);  
      
    if (empty($sitepage_searching)) {
      return $this->setNoRender();
    }
  }
}

?>