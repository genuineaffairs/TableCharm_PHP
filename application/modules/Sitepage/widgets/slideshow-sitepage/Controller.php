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
class Sitepage_Widget_SlideshowSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $params = array();
    $params['totalpages'] = $this->_getParam('itemCount', 10);
    $params['category_id'] = $this->_getParam('category_id', 0); 
    
    //GET PAGE DATAS
    $columnsArray = array('page_id', 'title', 'body','page_url', 'owner_id', 'category_id', 'photo_id', 'price', 'location', 'creation_date', 'featured', 'sponsored', 'view_count', 'comment_count', 'like_count', 'follow_count');
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
        $columnsArray[] = 'member_count';
    }
    $columnsArray[] = 'member_title';

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview')) {
        $columnsArray[] = 'review_count';
        $columnsArray[] = 'rating';
    }             
    $this->view->show_slideshow_object = $sitepage = Engine_Api::_()->getDbTable('pages', 'sitepage')->getListings('Featured Slideshow', $params, null, null, $columnsArray);
    $this->view->sitepage_featured = $sitepage_featured = Zend_Registry::isRegistered('sitepage_featuredslide') ? Zend_Registry::get('sitepage_featuredslide') : null;

    $this->view->num_of_slideshow = count($sitepage);
    if ( !(count($sitepage) > 0) || empty($sitepage_featured) ) {
      return $this->setNoRender();
    }
  }

}
?>