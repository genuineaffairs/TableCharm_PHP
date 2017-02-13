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
class Communityad_Widget_FooterAdsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{ 
		$front = Zend_Controller_Front::getInstance();
		$module = $front->getRequest()->getModuleName();
		$action = $front->getRequest()->getActionName();
		$controller = $front->getRequest()->getControllerName();
		if($controller == 'display' && $action == 'adboard' && $module == 'communityad') {
			$render_at_adboard = Engine_Api::_()->getApi('settings', 'core')->getSetting('adboard.footer', 0);
			if(empty($render_at_adboard)) {
				return $this->setNoRender();
			}
		}
		$load_content = 0;
		$this->view->identity = $identity = $this->_getParam('identity', $this->view->identity);
		if( empty($identity) ) {
		  return $this->setNoRender();
		}
		$page_setting = Engine_Api::_()->communityad()->getWidgetLimit($this->view->identity, 'communityad.footer-ads');
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
			$this->view->footer_info = $communityad_footer = Zend_Registry::get( 'communityad_footer' );
			if( empty($communityad_footer) ) {
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