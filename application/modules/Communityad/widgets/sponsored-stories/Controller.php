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

class Communityad_Widget_SponsoredStoriesController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
	$load_content = 0;
	$getStoryStatus = Engine_Api::_()->getItemTable('communityad_adtype')->getStatus('sponsored_stories');
	if( empty($getStoryStatus) ) {
	  return $this->setNoRender();
	}
	$this->view->ajax_enabled = $ajax_enabled = $this->_getParam('isAjaxEnabled', 0);
	$this->view->limit = $limit = $this->_getParam('itemCount', 5);
	$this->view->rootTitleTruncationLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.title', 25);
	$this->view->titleTruncationLimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('story.char.title', 35);
	if (!empty($_GET['load_content']) || empty($ajax_enabled)) {
	  $load_content = 1;
	  $this->view->viewer_object = $viewer_object = Engine_Api::_()->user()->getViewer();
	  $this->view->user_id = $viewer_object->getIdentity();
	  $params = array();
	  $params['limit'] = $limit;

	  $fetch_community_ads = Engine_Api::_()->getApi('SponcerdStories', 'communityad')->getSponcerdStories($params);

	  // Check if ads to be displayed are not empty
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