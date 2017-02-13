<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Widget_MobileAdsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Find out the limit.
    $this->view->limit = $limit = $page_setting = $this->_getParam('WidLimit', 5);
    $this->view->imageDisplay = $this->_getParam('imageDisplay', 1);

    $this->view->viewer_object = $viewer_object = Engine_Api::_()->user()->getViewer();
    $this->view->user_id = $viewer_object->getIdentity();
    $params=  array();
    $params['lim'] = $limit ;
    $params['placement_id'] = 1;
    $fetch_community_ads = Engine_Api::_()->communityad()->getAdvertisement($params);
    
    // Check if ads to be displayed are not empty
    if( !empty($fetch_community_ads) ){
	    $this->view->communityads_array = $fetch_community_ads;
	    $this->view->hideCustomUrl = Engine_Api::_()->communityad()->hideCustomUrl();
    } else {
      return $this->setNoRender();
    }
  }
}
?>