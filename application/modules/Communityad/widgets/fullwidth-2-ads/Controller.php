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
class Communityad_Widget_Fullwidth2AdsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{	$load_content = 0;
	  $this->view->identity = $identity = $this->_getParam('identity', $this->view->identity);
	  if( empty($identity) ) {
	    return $this->setNoRender();
	  }
		$page_setting = Engine_Api::_()->communityad()->getWidgetLimit($this->view->identity, 'communityad.fullwidth-2-ads');
		$this->view->ajax_enabled = $ajax_enabled = $page_setting[0]['ajax_enabled'];
		if (!empty($_GET['load_content']) || empty($ajax_enabled)) {
		  $load_content = 1;
			// Find out the limit.	
			$this->view->limit = $limit = $page_setting[0]['value'];

			$this->view->viewer_object = $viewer_object = Engine_Api::_()->user()->getViewer();
			$this->view->user_id = $viewer_object->getIdentity();
			$params=  array();
			$params['lim'] = $limit ;
			$params['placement_id'] = $page_setting[0]['pagesetting_id'];
			$fetch_community_ads = Engine_Api::_()->communityad()->getAdvertisement($params);
			$this->view->communityad_fullwidth2_ad = $communityad_fullwidth2_ad = Zend_Registry::get( 'communityad_fullwidth2_ad' );
			if( empty($communityad_fullwidth2_ad) ) {
				return $this->setNoRender();
			}
			// Check if ads to be displayed are not empty
			if( !empty($fetch_community_ads) ){
				$this->view->communityads_array = $fetch_community_ads;
				$this->view->hideCustomUrl = Engine_Api::_()->communityad()->hideCustomUrl();
			} else {
				return $this->setNoRender();
			}
		}
		$this->view->load_content = $load_content;
	}
}
?>