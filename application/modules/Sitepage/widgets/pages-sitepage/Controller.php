<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_PagesSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->sitepage_browse = $sitepage_browse = Zend_Registry::isRegistered('sitepage_browse') ? Zend_Registry::get('sitepage_browse') : null;
    $ShowViewArray = $this->_getParam('layouts_views', array("0" => "1", "1" => "2", "2" => "3"));

    $defaultOrder = $this->_getParam('layouts_oder', 1);
    $this->view->columnWidth = $this->_getParam('columnWidth', 188);
    $this->view->columnHeight = $this->_getParam('columnHeight', 350);
    $this->view->showlikebutton = $this->_getParam('showlikebutton', 1);
    $this->view->showfeaturedLable = $this->_getParam('showfeaturedLable', 1);
    $this->view->showsponsoredLable = $this->_getParam('showsponsoredLable', 1);
    $this->view->showlocation = $this->_getParam('showlocation', 1);
    $this->view->showprice = $this->_getParam('showprice', 1);
    $this->view->showpostedBy = $this->_getParam('showpostedBy', 1);
    $this->view->showdate = $this->_getParam('showdate', 1);
    $this->view->turncation = $this->_getParam('turncation', 15);
    $this->view->showContactDetails = $this->_getParam('showContactDetails', 1);
    $this->view->showgetdirection = $this->_getParam('showgetdirection', 1);
    
    $this->view->showProfileField = $this->_getParam('showProfileField', 0);
    $this->view->customFieldCount = $this->_getParam('customFieldCount', 2);
    $this->view->custom_field_title = $this->_getParam('custom_field_title', 0);
    $this->view->custom_field_heading = $this->_getParam('custom_field_heading', 0);
    
    $statisticsElement = array("likeCount" , "followCount", "viewCount" , "commentCount");
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
			$statisticsElement['']="reviewCount";
		}
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
			$statisticsElement['']="memberCount";
			$this->view->membercalled = $this->_getParam('membercalled', 1);
		}
    $this->view->statistics = $this->_getParam('statistics', $statisticsElement);

    $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.price.field', 1);
    $this->view->list_view = 0;
    $this->view->grid_view = 0;
    $this->view->map_view = 0;
    $this->view->defaultView = -1;
    if ($ShowViewArray && in_array("1", $ShowViewArray)) {
      $this->view->list_view = 1;
      if ($this->view->defaultView == -1 || $defaultOrder == 1)
        $this->view->defaultView = 0;
    }
    if ($ShowViewArray && in_array("2", $ShowViewArray)) {
      $this->view->grid_view = 1;
      if ($this->view->defaultView == -1 || $defaultOrder == 2)
        $this->view->defaultView = 1;
    }
    if ($ShowViewArray && in_array("3", $ShowViewArray)) {
      $this->view->map_view = 1;
      if ($this->view->defaultView == -1 || $defaultOrder == 3)
        $this->view->defaultView = 2;
    }

    if ($this->view->defaultView == -1) {
      return $this->setNoRender();
    }
    $customFieldValues = array();
    $values = array();
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $select_category = $this->_getParam('category_id',0);
    if(!empty($select_category) && empty($_GET['category'])) {
      $category = $select_category;
      $category_id = $select_category;
    }
    else {
			$category = $request->getParam('category_id', null);
      $category_id = $request->getParam('category', null);
    }
    $subcategory = $request->getParam('subcategory_id', null);
    $subcategory_id = $request->getParam('subcategory', null);
    $categoryname = $request->getParam('categoryname', null);
    $subcategoryname = $request->getParam('subcategoryname', null);
    $subsubcategory = $request->getParam('subsubcategory_id', null);
    $subsubcategory_id = $request->getParam('subsubcategory', null);
    $subsubcategoryname = $request->getParam('subsubcategoryname', null);

    if ($category)
      $this->view->category = $_GET['category'] = $category;
    if ($subcategory)
      $this->view->subcategory = $_GET['subcategory'] = $subcategory;
    if ($categoryname)
      $this->view->categoryname = $_GET['categoryname'] = $categoryname;
    if ($subcategoryname)
      $this->view->subcategoryname = $_GET['subcategoryname'] = $subcategoryname;

    if ($subsubcategory)
       $this->view->subsubcategory = $_GET['subsubcategory'] = $subsubcategory;
    if ($subcategoryname)
     $this->view->subsubcategoryname =  $_GET['subsubcategoryname'] = $subsubcategoryname;

    if ($category_id)
      $this->view->category =  $_GET['category'] = $values['category'] = $category_id;
    if ($subcategory_id)
      $this->view->subcategory = $_GET['subcategory'] = $values['subcategory'] = $subcategory_id;
    if ($subsubcategory_id)
     $this->view->subsubcategory = $_GET['subsubcategory'] = $values['subsubcategory'] = $subsubcategory_id;
    $this->view->tag = $values['tag'] = $request->getParam('tag', null);
    if (!empty($values['tag']))
     $this->view->tag = $_GET['tag'] = $values['tag'];
    
    if (isset($_GET['tag']) && !empty($_GET['tag'])) {
      $this->view->tag =$tag = $_GET['tag'];
      $page = 1;
      if (isset($_GET['page']) && !empty($_GET['page'])) {
        $page = $_GET['page'];
      }
      unset($_GET);
      $this->view->tag = $_GET['tag'] = $tag;
      $_GET['page'] = $page;
    }

		$this->view->sitepage_location = $values['sitepage_location'] = $request->getParam('sitepage_location', null);
    if (!empty($values['sitepage_location']))
     $this->view->sitepage_location = $_GET['sitepage_location'] = $values['sitepage_location'];
    
    if (isset($_GET['sitepage_location']) && !empty($_GET['sitepage_location'])) {
      $this->view->sitepage_location =$sitepage_location = $_GET['sitepage_location'];
      $this->view->sitepage_location = $_GET['sitepage_location'] = $sitepage_location;
    }

    //GET VALUE BY POST TO GET DESIRED SITEPAGES
    if (!empty($_GET)) {
      $values = $_GET;
    }

    //FORM GENERATION
    //$form = new Sitepage_Form_Search(array('type' => 'sitepage_page'));
    $form = Zend_Registry::isRegistered('Sitepage_Form_Search') ? Zend_Registry::get('Sitepage_Form_Search') : new Sitepage_Form_Search(array('type' => 'sitepage_page')); 

    if (!empty($_GET))
      $form->populate($_GET);
    $values = $form->getValues();

		//PAGE OFFER IS INSTALLED OR NOT
		$this->view->sitepageOfferEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer');

    //BADGE CODE
    if ((int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge')) {
      if (isset($_POST['badge_id']) && !empty($_POST['badge_id'])) {
        $values['badge_id'] = $_POST['badge_id'];
      }
    }

    if (!empty($_GET['page'])) {
      $values['page'] = $_GET['page'];
    } else {
      $values['page'] = 1;
    }

    //GET LISITNG FPR PUBLIC PAGE SET VALUE
    $values['type'] = 'browse';
    $values['type_location'] = 'browsePage';

    if (@$values['show'] == 2) {

      //GET AN ARRAY OF FRIEND IDS
      $friends = $viewer->membership()->getMembers();

      $ids = array();
      foreach ($friends as $friend) {
        $ids[] = $friend->user_id;
      }

      $values['users'] = $ids;
    }

    //GEO-LOCATION WORK
    if ((int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagegeolocation') && isset($values['has_currentlocation']) && !empty($values['has_currentlocation'])) {

      $session = new Zend_Session_Namespace('Current_location');
      if (!isset($session->latitude) || !isset($session->longitude)) {
        $locationResult = null;
        $apiType = Engine_Api::_()->getApi('core', 'sitepagegeolocation')->getGeoApiType();
        if ($apiType == 1) {
          $locationResult = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getMaxmindCurrentLocation();
        } elseif ($apiType == 2) {
          $locationResult = Engine_Api::_()->getApi('geoLocation', 'seaocore')->getMaxmindGeoLiteCountry();
        }
        if (($apiType == 1 || $apiType == 2) && !empty($locationResult)) {
          $this->view->latitude = $values['latitude'] = $session->latitude = $locationResult['latitude'];
          $this->view->longitude = $values['longitude'] = $session->longitude = $locationResult['longitude'];
        }
      } else {
        $this->view->latitude = $values['latitude'] = $session->latitude;
        $this->view->longitude = $values['longitude'] = $session->longitude;
      }
    }
    $this->view->assign($values);

    //PAGE-RATING IS ENABLED OR NOT
    $this->view->ratngShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview');

    //CUSTOM FIELD WORK
    $customFieldValues = array_intersect_key($values, $form->getFieldElements());
    $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('sitepage', 'show');
    if ($viewer->getIdentity() && !empty($row) && !empty($row->display) && $form->show->getValue() == 3 && !isset($_GET['show'])) {
      @$values['show'] = 3;
    }

		//DON'T SEND CUSTOM FIELDS ARRAY IF EMPTY
		$has_value = 0;
		foreach($customFieldValues as $customFieldValue) {
			if(!empty($customFieldValue)) {
				$has_value = 1;
				break;
			}
		}

		if(empty($has_value)) {
			$customFieldValues = null;
		}

		$values['browse_page'] = 1;    

    // GET SITEPAGES
    $this->view->paginator = $paginator = Engine_Api::_()->sitepage()->getSitepagesPaginator($values, $customFieldValues);

    $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.page', 10);
    $paginator->setItemCountPerPage($items_count);
    $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);
    $this->view->sitepage_generic = Zend_Registry::isRegistered('sitepage_generic') ? Zend_Registry::get('sitepage_generic') : null;

    $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitepage()->enableLocation();
    $this->view->flageSponsored = 0;

    if (!empty($checkLocation) && $paginator->getTotalItemCount() > 0) {
      $ids = array();
      $sponsored = array();
      foreach ($paginator as $sitepage) {
        $id = $sitepage->getIdentity();
        $ids[] = $id;
        $sitepage_temp[$id] = $sitepage;
      }
      $values['page_ids'] = $ids;
      $this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'sitepage')->getLocation($values);

      foreach ($locations as $location) {
        if ($sitepage_temp[$location->page_id]->sponsored) {
          $this->view->flageSponsored = 1;
          break;
        }
      }
      $this->view->sitepage = $sitepage_temp;
    } else {
      $this->view->enableLocation = 0;
    }
    if (empty($categoryname)) {
      $_GET['category'] = $this->view->category_id = 0;
      $_GET['subcategory'] = $this->view->subcategory_id = 0;
      $_GET['subsubcategory'] = $this->view->subsubcategory_id = 0;
      $_GET['categoryname'] = 0;
      $_GET['subcategoryname'] = 0;
      $_GET['subsubcategoryname'] = 0;
    }

    $this->view->search = 0;
    if (!empty($_GET)) {
      $this->view->search = 1;
    }

    //CAN CREATE PAGES OR NOT
    $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('sitepage_page', $viewer, 'create');
    $this->view->formValues = $values;
    if (empty($sitepage_browse)) {
      return $this->setNoRender();
    }
  }

}

?>
