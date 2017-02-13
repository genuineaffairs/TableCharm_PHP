<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Widget_SitemobileOptionsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $moduleName = $request->getModuleName();  
    $controllerName = $request->getControllerName(); 
		$actionName = $request->getActionName();
    $allParams = $request->getParams();
    $params['name'] = $moduleName . '_' . $controllerName . '_' . $actionName;  
    if(isset($allParams['listingtype_id']) && !empty($allParams['listingtype_id']) && ($moduleName == 'sitereview' && $controllerName == 'index' && $actionName == 'view')) {
      $params['name'] = $params['name'].'_listtype_'.$allParams['listingtype_id']; 
    }
    
    if( $params['name'] == 'sitelike_sitemobile-index_memberlike' ) {
      $params['name'] = 'message_all';
    }

    $navigationTable = Engine_Api::_()->getDbtable('navigation', 'sitemobile');
    $searchRow = $navigationTable->getNavigation($params);
    if(empty($searchRow)) {
      $params['name'] = $moduleName;
      $searchRow = $navigationTable->getNavigation($params);
      if(empty($searchRow)) 
        return $this->setNoRender();
    }

		if ($params['name'] != 'message_all' && $searchRow->subject_type && !Engine_Api::_()->core()->hasSubject($searchRow->subject_type)) {
			return $this->setNoRender();
		}

    //CHECK FOR REVIEW PLUGIN FAVOURITE / WISHLIST PROFILE PAGE - DO NOT RENDER OPTIONS
    $favouriteSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitereview.favourite', 0);
    if ($favouriteSetting && $params['name'] == "sitereview_wishlist_profile"){
      return $this->setNoRender();
    }
    $menusApi = Engine_Api::_()->getApi('menus', 'sitemobile');
    $this->view->navigation = $menusApi->getNavigation($searchRow->menu);

  }

}