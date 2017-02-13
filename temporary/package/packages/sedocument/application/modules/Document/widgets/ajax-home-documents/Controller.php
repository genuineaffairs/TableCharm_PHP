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
class Document_Widget_AjaxHomeDocumentsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//GET QUERY DATA
    $showTabArray = $this->_getParam('layouts_tabs', array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => '5'));
    $ShowViewArray = $this->_getParam('layouts_views', array("0" => "1", "1" => "2", "2" => "3"));
    $defaultOrder = $this->_getParam('layouts_oder', 1);

		//GET DOCUMENT TABLE
		$documentTable = Engine_Api::_()->getDbtable('documents', 'document');

    $this->view->list_view = 0;
    $this->view->grid_view = 0;
    $this->view->defaultView = -1;
    $list_limit = 0;
    $grid_limit = 0;
    if (in_array("1", $ShowViewArray)) {
      $this->view->list_view = 1;
      $list_limit = $this->_getParam('list_limit', 10);
      if ($this->view->defaultView == -1 || $defaultOrder == 1)
        $this->view->defaultView = 0;
    }
    if (in_array("2", $ShowViewArray)) {
      $this->view->grid_view = 1;
      $grid_limit = $this->_getParam('grid_limit', 15);
      if ($this->view->defaultView == -1 || $defaultOrder == 2)
        $this->view->defaultView = 1;
    }
    if (in_array("3", $ShowViewArray)) {
      $this->view->map_view = 1;
      $list_limit = $this->_getParam('list_limit', 10);
      if ($this->view->defaultView == -1 || $defaultOrder == 3)
        $this->view->defaultView = 2;
    }

    $documentRecently = array();
    $documentViewed = array();
    $documentRandom = array();
    $documentFeatured = array();
    $documentSponosred = array();

    if (in_array("1", $showTabArray)) {
			$params = array();
			$params['limit'] = 1;
      $documentRecently = $documentTable->widgetDocumentsData($params);
    }

    if (in_array("2", $showTabArray)) {
			$params = array();
			$params['limit'] = 1;
      $params['orderby'] = 'views DESC';
			$params['zero_count'] = 'views';
      $documentViewed = $documentTable->widgetDocumentsData($params);
    }

    if (in_array("3", $showTabArray)) {
			$params = array();
			$params['limit'] = 1;
			$params['orderby'] = 'RAND() DESC';
      $documentRandom = $documentTable->widgetDocumentsData($params);
    }

    if (in_array("4", $showTabArray)) {
			$params = array();
			$params['limit'] = 1;
      $params['orderby'] = 'featured DESC';
			$params['featured'] = 1;
      $documentFeatured = $documentTable->widgetDocumentsData($params);
    }

    if (in_array("5", $showTabArray)) {
			$params = array();
			$params['limit'] = 1;
      $params['orderby'] = 'sponsored DESC';
			$params['sponsored'] = 1;
      $documentSponosred = $documentTable->widgetDocumentsData($params);
    }

    if ((!(Count($documentRecently) > 0) && !(Count($documentViewed) > 0) && !(Count($documentRandom) > 0 ) && !(Count($documentFeatured) > 0 ) && !(Count($documentSponosred) > 0 )) || ($this->view->defaultView == -1)) {
      return $this->setNoRender();
    }

    $tabsOrder = array();
    $tabs = array();
    $menuTabs = array();
    if (Count($documentRecently) > 0) {
      $tabs['recent'] = array('title' => 'Recent', 'tabShow' => 'Recently Posted');
      $tabsOrder['recent'] = $this->_getParam('recent_order', 1);
    }
    if (Count($documentViewed) > 0) {
      $tabs['popular'] = array('title' => 'Most Popular', 'tabShow' => 'Most Viewed');
      $tabsOrder['popular'] = $this->_getParam('popular_order', 2);
    }
    if (Count($documentRandom) > 0) {
      $tabs['random'] = array('title' => 'Random', 'tabShow' => 'Random');
      $tabsOrder['random'] = $this->_getParam('random_order', 3);
    }

    if (Count($documentFeatured) > 0) {
      $tabs['featured'] = array('title' => 'Featured', 'tabShow' => 'Featured');
      $tabsOrder['featured'] = $this->_getParam('featured_order', 4);
    }
    if (Count($documentSponosred) > 0) {
      $tabs['sponosred'] = array('title' => 'Sponsored', 'tabShow' => 'Sponosred');
      $tabsOrder['sponosred'] = $this->_getParam('sponosred_order', 5);
    }
    @asort($tabsOrder);
    $firstIndex = key($tabsOrder);
    foreach ($tabsOrder as $key => $value) {
      $menuTabs[$key] = $tabs[$key];
    }

    $this->view->tabs = $menuTabs;
    $this->view->active_tab_list = $list_limit;
    $this->view->active_tab_image = $grid_limit;
    $limit = $list_limit > $grid_limit ? $list_limit : $grid_limit;

    if ($menuTabs[$firstIndex]['tabShow'] == 'Most Viewed') {
			$params = array();
      $params['orderby'] = 'views DESC';
			$params['zero_count'] = 'views';
			$params['limit'] = $limit;
    }
    elseif ($menuTabs[$firstIndex]['tabShow'] == 'Featured') {
			$params = array();
      $params['orderby'] = 'featured DESC';
			$params['featured'] = 1;
			$params['limit'] = $limit;
    }
    elseif ($menuTabs[$firstIndex]['tabShow'] == 'Sponosred') {
			$params = array();
      $params['orderby'] = 'sponsored DESC';
			$params['sponsored'] = 1;
			$params['limit'] = $limit;
    }
    elseif ($menuTabs[$firstIndex]['tabShow'] == 'Random') {
			$params = array();
      $params['orderby'] = 'RAND() DESC';
			$params['limit'] = $limit;
    }
		else {
			$params = array();
			$params['limit'] = $limit;
		}

		//SEND DATA TO TPL
    $this->view->documents = $documentTable->widgetDocumentsData($params);
  }
}
?>