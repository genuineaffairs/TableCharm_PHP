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
class Sitepage_Widget_AjaxCarouselSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    
    $this->view->vertical = $this->_getParam('viewType', 1);
    $values = array();

    $this->view->category_id = $values['category_id'] = $this->_getParam('category_id', 0);

    $this->view->interval = $this->_getParam('interval', 300);
    $this->view->blockHeight = $this->_getParam('blockHeight', 240);
    $this->view->blockWidth = $this->_getParam('blockWidth', 150);
    //$this->view->showOptions = $this->_getParam('showOptions', array("category","rating","review"));

    $statisticsElement = array("likeCount" , "followCount", "viewCount" , "commentCount");
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
			$statisticsElement['']="reviewCount";
		}
		if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
			$statisticsElement['']="memberCount";
		}
    $this->view->statistics = $this->_getParam('statistics', $statisticsElement);

    $this->view->title_truncation = $this->_getParam('truncation', 50);
    $this->view->viewType = $this->_getParam('viewType', 0);
    $this->view->limit = $values['limit'] = $this->_getParam('itemCount', 3);
    $this->view->sponsoredIcon = $this->_getParam('sponsoredIcon', 1);
    $this->view->featuredIcon = $this->_getParam('featuredIcon', 1);
    $this->view->popularity = $values['popularity'] = $this->_getParam('popularity', 'page_id');
    $this->view->fea_spo = $fea_spo = $this->_getParam('fea_spo', 'sponsored');
    if ($fea_spo == 'featured') {
      $values['featured'] = 1;
    } elseif ($fea_spo == 'sponsored') {
      $values['sponsored'] = 1;
    } elseif ($fea_spo == 'fea_spo') {
      $values['sponsored'] = 1;
      $values['featured'] = 1;
    }         

    //FETCH SPONSERED LISTINGS
    $this->view->listings = $listing = Engine_Api::_()->getDbTable('pages', 'sitepage')->getListing('', $values);

    //GET LIST COUNT
    $this->view->totalCount = $listing->getTotalItemCount();
    if ( ($this->view->totalCount <= 0)) {
      return $this->setNoRender();
    }
  }

}
