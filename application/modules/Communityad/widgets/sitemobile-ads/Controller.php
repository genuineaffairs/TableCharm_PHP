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
class Communityad_Widget_SitemobileAdsController extends Engine_Content_Widget_Abstract {

  //ACTION FOR SHOWING THE AD 
  public function indexAction() {

    $load_content = 0;
    $this->view->communityad_id = $communityad_id = $this->_getParam('communityadid', null);
    $this->view->isajax = $isajax = $this->_getParam('isajax', null);
    $this->view->limit = $limit = $this->_getParam('limit', 3);
		$this->view->columnHeight = $columnHeight = $this->_getParam('columnHeight', 235); 
		$this->view->carouselView = $this->_getParam('carouselView', '0');
    if ($this->view->identity) {
      $this->view->identity_temp = $identity = $this->view->identity;
      $this->view->communityad_id = $communityad_id = $this->_getParam('communityadid', "communityadid_widget_showads_$identity");
    }
    $this->view->ajaxView = $ajaxView = $this->_getParam('ajaxView', 0);
    $this->view->show_ads = $show_ads = $this->_getParam('show_ads', 0);
    if(empty($ajaxView)) {
      $_GET['load_content'] = 1;
    } 
    if (!empty($_GET['load_content']) || empty($communityad_id) || !empty($isajax)) {
      $load_content = 1;
      if ($limit == 0 && empty($this->view->identity)) {
        return $this->setNoRender();
      }

      $this->view->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $params = array();
      $params['lim'] = $limit;
      if($show_ads == 1) {
        $params['featured'] = 1;
      } elseif($show_ads == 2) {
        $params['sponsored'] = 1;
      }

      if (Engine_Api::_()->core()->hasSubject()) {
        $subject = Engine_Api::_()->core()->getSubject();
        Engine_Api::_()->core()->clearSubject();
      }

      $fetch_community_ads = Engine_Api::_()->communityad()->getAdvertisement($params);
      if (!empty($subject)) {
        Engine_Api::_()->core()->clearSubject();
        Engine_Api::_()->core()->setSubject($subject);
      }

      if (!empty($fetch_community_ads)) {
        $this->view->communityads_array = $fetch_community_ads;
      } else {
        return $this->setNoRender();
      }
    }

    $this->view->load_content = $load_content;
  }

}

?>