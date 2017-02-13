<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: DisplayController.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_DisplayController extends Core_Controller_Action_Standard {

  protected $_navigation;
  protected $_viewer;
  protected $_viewer_id;
  protected $_session;

  public function init() {

	$this->_session = new Zend_Session_Namespace('Payment_Userads');

	$this->_viewer = Engine_Api::_()->user()->getViewer();
	$this->_viewer_id = $this->_viewer->getIdentity();
	if (!$this->_helper->requireAuth()->setAuthParams('communityad', $this->_viewer, 'view')->isValid()) {
	  return;
	}
  }

// For Cancel advertisment by viewer and for submit reasion
  public function adsaveAction() {
	// Received Parameter from JS file.
	$adCancelReasion = (string) $this->_getParam('adCancelReasion');
	$adsId = (string) $this->_getParam('adsId');
	// Decode a ad id
	$adsId = Engine_Api::_()->communityad()->getEncodeToDecode($adsId);
	$adDescription = (string) $this->_getParam('adDescription');
	//Insert entry in the data base.
	$viewerId = Engine_Api::_()->user()->getViewer()->getIdentity();
	$adcancelTable = Engine_Api::_()->getItemTable('communityad_adcancel');
	$adcancelList = $adcancelTable->createRow();
	$adcancelList->user_id = $viewerId;
	$adcancelList->report_type = $adCancelReasion;
	if (!empty($adDescription)) {
	  $adcancelList->report_description = $adDescription;
	}
	$adcancelList->ad_id = $adsId;
	$adcancelList->save();
	$this->view->showMsg = 1;
  }

  // DISPLAY ADS ON ADBOARD
  public function adboardAction() {
		$this->view->headTitle($this->view->translate("Ad Board"), Zend_View_Helper_Placeholder_Container_Abstract::PREPEND);
		$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
						->getNavigation('communityad_main');
		$limit = Engine_Api::_()->getApi('settings', 'core')->ad_board_limit;
		$this->view->hideCustomUrl = Engine_Api::_()->communityad()->hideCustomUrl();
		$this->view->viewer_object = $viewer = Engine_Api::_()->user()->getViewer();
		$this->view->user_id = $viewer->getIdentity();
		$this->view->baseurl = Zend_Controller_Front::getInstance()->getBaseUrl();
		$this->view->getCommunityadTitle = Engine_Api::_()->communityad()->getCommunityadTitle();
		$this->view->getStoryStatus = Engine_Api::_()->getItemTable('communityad_adtype')->getStatus('sponsored_stories');
		$params = array();
		$params['lim'] = $limit;
		// FEATCH ADS
		$fetch_community_ads = Engine_Api::_()->communityad()->getAdvertisement($params);
		if (!empty($fetch_community_ads)) {
			$this->view->communityads_array = $fetch_community_ads;
		} else {
			$this->view->noResult = 1;
		}
   // if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
			$this->_helper->content
						// ->setNoRender()
							->setEnabled();
  //  }
  }

  // Function: Call when click on 'Like' from the advertisment widget then call this function from the ajax.
  public function globallikesAction() {
	$viewer = Engine_Api::_()->user()->getViewer();
	// GET THE CURRENT UESRID
	$loggedin_user_id = $viewer->getIdentity();
	if (empty($loggedin_user_id)) {
	  return;
	}
	$ad_id = $this->_getParam('ad_id');
	$core_like = $this->_getParam('core_like');
	$ad_id = Engine_Api::_()->communityad()->getEncodeToDecode($ad_id);
	$resource_type = $this->_getParam('resource_type');
	// Make an 'Item Type' acording to the resource type.
	$resource_info = Engine_Api::_()->getDbtable('modules', 'communityad')->getModuleInfo($resource_type);
	if (!empty($resource_info)) {
	  $resource_type = $resource_info['table_name'];
	}
	$resource_id = $this->_getParam('resource_id');
	$owner_id = $this->_getParam('owner_id');
	$like_id = $this->_getParam('like_id');
	$resource_obj = Engine_Api::_()->getItem($resource_type, $resource_id); // Resource Object.
	$is_like = Engine_Api::_()->getDbtable('likes', 'core')->getLike($resource_obj, $viewer); // Check from 'core_likes'.
	//Conditioon: If advertisment is not liked before.
	if (empty($like_id)) {
	  //CHECKING IF USER HAS MAKING DUPLICATE ENTRY OF LIKING AN APPLICATION.
	  $userads = Engine_Api::_()->getItem('userads', $ad_id);
	  $like_id_temp = Engine_Api::_()->communityad()->check_availability($ad_id);
	  if (empty($like_id_temp[0]['like_id']) || !empty($userads->story_type)) {

		// Condition : Only for 'Group' & 'Event' for the 'Activity Like'.
		if ($resource_type == 'group' || $resource_type == 'event') {
		  $activityTable = Engine_Api::_()->getDbtable('actions', 'activity');
		  $activityTableName = $activityTable->info('name');
		  // Queary: Check that entry are already in data base or not.
		  $selectContainer = $activityTable->select()
						  ->from($activityTableName, array('action_id', 'like_count'))
						  ->where('object_type =?', $resource_type)
						  ->where('object_id =?', $resource_id)
						  ->where('type =?', $resource_type . '_create')
						  ->limit(1);
		  $fetchPagesettings = $selectContainer->query()->fetchAll();
		  if (!empty($fetchPagesettings)) {
			$activityTable->update(array('like_count' => $fetchPagesettings[0]['like_count'] + 1), array('action_id =?' => $fetchPagesettings[0]['action_id']));
			$activityTable = Engine_Api::_()->getDbtable('likes', 'activity');
			$likeActivity = $activityTable->createRow();
			$likeActivity->resource_id = $fetchPagesettings[0]['action_id'];
			$likeActivity->poster_type = $viewer->getType();
			$likeActivity->poster_id = $viewer->getIdentity();
			$likeActivity->save();
		  }
		}

		$likesTable = Engine_Api::_()->getDbTable('likes', 'communityad');
		$isExist = $likesTable->isExist($ad_id, $viewer->getIdentity());


		// Increase the "Click Count of the Sponsored story"
		if( !empty($userads->story_type) ) {
		  Engine_Api::_()->communityad()->ad_clickcount($ad_id, 0);
		}


		if (empty($userads->story_type) && empty($isExist)) {
		  $likeTableData = $likesTable->createRow();
		  $likeTableData->poster_id = $viewer->getIdentity();
		  $likeTableData->ad_id = $ad_id;
		  //DATA CAN SAVE IN THE TABLE
		  $like_id = $likeTableData->save();
		}

		// Condition: If content is not 'Like' then insert row in 'like' table and from 'Notification' table.
		if (empty($is_like)) {
		  if ( !empty($resource_obj) && !empty($viewer) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike')) {
		    Engine_Api::_()->sitelike()->setLikeFeed($viewer, $resource_obj);
		  }

		  $likeTable = Engine_Api::_()->getItemTable('core_like');
		  $likeData = $likeTable->createRow();
		  $likeData->resource_type = $resource_type;
		  $likeData->resource_id = $resource_id;
		  $likeData->poster_type = $viewer->getType();
		  $likeData->poster_id = $viewer->getIdentity();
		  $likeData->save();
		  //DATA CAN SAVE IN THE TABLE
		  // Queary: For insert data in 'notification' table for display notifications of the advertisment owner.
		  if (!empty($owner_id) && $owner_id != $viewer->getIdentity()) {
			$notify_table = Engine_Api::_()->getDbtable('notifications', 'activity');
			$label = '{"label":"' . $resource_type . '"}';

			$notifyData = $notify_table->createRow();
			$notifyData->user_id = $owner_id;
			$notifyData->subject_type = $viewer->getType();
			$notifyData->subject_id = $viewer->getIdentity();
			$notifyData->object_type = $resource_type;
			$notifyData->object_id = $resource_id;
			$notifyData->type = 'liked';
			$notifyData->params = $label;
			$notifyData->date = date('Y-m-d h:i:s', time());
			$notifyData->save();
		  }
		}

		// Increase 'Count' in userad table.
		$userads->count_like = $userads->count_like + 1;
		$userads->save();

		//PASS THE VALUE TO .TPL FILE
		$sendLikeResponced = $like_id;
		if (!empty($userads->story_type)) {
		  $sendLikeResponced = 1;
		}
		$this->view->like_id = $sendLikeResponced;
		$like_msg = 'Successfully Liked.';
	  } else {
		$this->view->like_id = $like_id_temp[0]['like_id'];
	  }
	} else { // Condition: If 'Like' before then delete from tables.
	  // Condition: Only in the case of group and event delete from activity like.
	  if ($resource_type == 'group' || $resource_type == 'event') {
		$activityTable = Engine_Api::_()->getDbtable('actions', 'activity');
		$activityTableName = $activityTable->info('name');
		// Queary: Check that entry are already in data base or not.
		$selectContainer = $activityTable->select()
						->from($activityTableName, array('action_id', 'like_count'))
						->where('object_type =?', $resource_type)
						->where('object_id =?', $resource_id)
						->where('type =?', $resource_type . '_create')
						->limit(1);
		$fetchPagesettings = $selectContainer->query()->fetchAll();
		if (!empty($fetchPagesettings) && empty($core_like)) {
		  $activityPosterType = $fetchPagesettings[0]['action_id'];
		  Engine_Api::_()->getDbtable('likes', 'activity')->delete(array('resource_id =?' => $activityPosterType, 'poster_type =?' => $viewer->getType(), 'poster_id =?' => $viewer->getIdentity()));
		  $activityTable->update(array('like_count' => $fetchPagesettings[0]['like_count'] - 1), array('action_id =?' => $fetchPagesettings[0]['action_id']));
		}
	  }

	  // Increase 'Count' in userad table.
	  $userads = Engine_Api::_()->getItem('userads', $ad_id);
	  $userads->count_like = $userads->count_like - 1;
	  $userads->save();

	  // Delete from 'communityad_like' table.
	  if (empty($userads->story_type)) {
		Engine_Api::_()->getDbTable('likes', 'communityad')->delete(array('poster_id =?' => $viewer->getIdentity(), 'ad_id =?' => $ad_id));
	  }
	  if (!empty($is_like) && empty($core_like)) {
		if ( !empty($resource_obj) && !empty($viewer) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitelike')) {
		  Engine_Api::_()->sitelike()->removeLikeFeed($viewer, $resource_obj);
		}
		Engine_Api::_()->getDbTable('likes', 'core')->delete(array('like_id =?' => $is_like->like_id));
	  }
	  $like_msg = 'Successfully Unliked.';
	}
  }

  // Function: Show 'My friend which liked advertisment whom i want to liked'.
  public function adfriendlikeAction() {
	$like_user_str = 0;
	$resource_type = $this->_getParam('resource_type'); // Resource type.
	$communityad_id = $this->_getParam('communityad_id', 0);
	if (empty($communityad_id)) {
	  $ad_id = $this->_getParam('ad_id');
	  $this->view->communityad_id = $ad_id = Engine_Api::_()->communityad()->getEncodeToDecode($ad_id);
	} else {
	  $this->view->communityad_id = $ad_id = $communityad_id;
	}
	$resource_title = $resource_type;
	$resource_info = Engine_Api::_()->getDbtable('modules', 'communityad')->getModuleInfo($resource_type);
	if (!empty($resource_info)) {
	  $resource_type = $resource_info['table_name'];
	  $resource_title = $resource_info['module_title'];
	}
	$ajax_title = $this->_getParam('resource_title');
	if (!empty($ajax_title)) {
	  $resource_title = $ajax_title;
	}
	$this->view->resource_type = $resource_type;
	$this->view->resource_title = $resource_title;

	$this->view->resource_id = $resource_id = $this->_getParam('resource_id'); // Resource id.
  $this->view->getStoryStatus = Engine_Api::_()->getItemTable('communityad_adtype')->getStatus('sponsored_stories');
  $this->view->getCommunityadTitle = Engine_Api::_()->communityad()->getCommunityadTitle();
	$this->view->call_status = $call_status = $this->_getParam('call_status');
	$this->view->page = $page = $this->_getParam('page', 1); // Current page number for the 'ajax pagination'.
	$search = $this->_getParam('search', ''); // Searching value which set by 'ajax searching' in tpl.
	$is_ajax = $this->_getParam('is_ajax', 0);
	$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
	$this->view->is_ajax = $is_ajax;
	if (empty($search)) {
	  $this->view->search = $this->view->translate('Search Members');
	} else {
	  $this->view->search = $search;
	}

	if ($call_status == 'friend') {

	  $sub_status_table = Engine_Api::_()->getItemTable('communityad_like');
	  $sub_status_name = $sub_status_table->info('name');

	  $membership_table = Engine_Api::_()->getDbtable('membership', 'user');
	  $member_name = $membership_table->info('name');

	  $user_table = Engine_Api::_()->getItemTable('user');
	  $user_Name = $user_table->info('name');

	  $sub_status_select = $user_table->select()
					  ->setIntegrityCheck(false)
					  ->from($sub_status_name, array('poster_id'))
					  ->joinInner($member_name, "$member_name . user_id = $sub_status_name . poster_id", NULL)
					  ->joinInner($user_Name, "$user_Name . user_id = $member_name . user_id")
					  ->where($member_name . '.resource_id = ?', $user_id)
					  ->where($member_name . '.active = ?', 1)
					  ->where($sub_status_name . '.ad_id = ?', $ad_id)
					  ->where($sub_status_name . '.poster_id != ?', $user_id)
					  ->where($sub_status_name . '.poster_id != ?', 0)
					  ->where($user_Name . '.displayname LIKE ?', '%' . $search . '%')
					  ->order('	like_id DESC');
	} else if ($call_status == 'public') {

	  // Fetch user which liked this content, in decending order.
	  $sub_status_table = Engine_Api::_()->getItemTable('communityad_like');
	  $sub_status_name = $sub_status_table->info('name');

	  $user_table = Engine_Api::_()->getItemTable('user');
	  $user_Name = $user_table->info('name');

	  $sub_status_select = $user_table->select()
					  ->setIntegrityCheck(false)
					  ->from($sub_status_name, array('poster_id'))
					  ->joinInner($user_Name, "$user_Name . user_id = $sub_status_name . poster_id")
					  ->where($sub_status_name . '.ad_id = ?', $ad_id)
					  ->where($sub_status_name . '.poster_id != ?', 0)
					  ->where($user_Name . '.displayname LIKE ?', '%' . $search . '%')
					  ->order($sub_status_name . '.like_id DESC');
	}

	$fetch_sub = Zend_Paginator::factory($sub_status_select);
	$check_object_result = count($fetch_sub);

	if (!empty($check_object_result)) {
	  $this->view->user_obj = $fetch_sub;
	} else {
	  $this->view->no_result_msg = $this->view->translate('No results were found.');
	}

	$fetch_sub->setCurrentPageNumber($page);
	$fetch_sub->setItemCountPerPage(20);

	// 'Number of friend' which liked this content,
	$resource_object = Engine_Api::_()->getItem($resource_type, $resource_id);
	$public_count = Engine_Api::_()->getDbtable('likes', 'communityad')->getLikeCount($ad_id);
	$this->view->public_count = $public_count; // public count;
	// 'Number of my friend' which liked this content,
	$friend_count = Engine_Api::_()->communityad()->friend_number_of_like($ad_id);
	$this->view->friend_count = $friend_count; //friend count;
	// Find out the title of like.
	if ($resource_type == 'member') {
	  $this->view->like_title = Engine_Api::_()->getItem('user', $resource_id)->displayname;
	} else {
	  $this->view->like_title = Engine_Api::_()->getItem($resource_type, $resource_id)->getTitle();
	}
  }

  // Function: When click on 'Help & Learn More' tab from user section.
  public function helpAndLearnmoreAction() {

	$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
					->getNavigation('communityad_main');
	$this->view->display_faq = $display_faq = $this->_getParam('display_faq');
	$this->view->page_id = $page_id = $this->_getParam('page_id', 0);
	$communityad_getfaq = Zend_Registry::get('communityad_getfaq');
	if (empty($communityad_getfaq)) {
	  return;
	}
	if (empty($page_id)) {
	  $helpInfoTable = Engine_Api::_()->getItemtable('communityad_infopage');
	  $helpInfoTableName = $helpInfoTable->info('name');
	  $select = $helpInfoTable->select()->from($helpInfoTableName)->where('status =?', 1);
	  $fetchHelpTable = $select->query()->fetchAll();
	  if (!empty($fetchHelpTable)) {
		$this->view->pageObject = $fetchHelpTable;
		$default_faq = $fetchHelpTable[0]['faq'];
		$default_contact = $fetchHelpTable[0]['contect_team'];
		$this->view->page_default = $fetchHelpTable[0]['page_default'];
	  }
	} else {
	  $helpInfoTable = Engine_Api::_()->getItemtable('communityad_infopage');
	  $helpInfoTableName = $helpInfoTable->info('name');
	  $select = $helpInfoTable->select()->from($helpInfoTableName, array('infopage_id', 'title', 'package', 'faq', 'contect_team'))->where('status =?', 1);
	  $fetchHelpTable = $select->query()->fetchAll();
	  if (!empty($fetchHelpTable)) {
		$this->view->pageObject = $fetchHelpTable;
		$page_info = Engine_Api::_()->getItem('communityad_infopage', $page_id);
		if (empty($page_info)) {
		  return $this->_forward('notfound', 'error', 'core');
		}
		$display_faq = $default_faq = $page_info->faq;
		$default_contact = $page_info->contect_team;
		$this->view->page_default = $page_info->page_default;
		if (empty($default_faq) && empty($default_contact)) {
		  $this->view->content_data = $page_info->description;
		  $this->view->content_title = $page_info->title;
		}
	  }
	}
	if (empty($display_faq)) {
	  $this->view->display_faq = $display_faq = $default_faq;
	}
	if (!empty($display_faq)) {
	  $pageIdSelect = $helpInfoTable->select()->from($helpInfoTableName, array('*'))
					  ->where('faq =?', $display_faq)->where('status =?', 1)->limit(1);
	  $result = $pageIdSelect->query()->fetchAll();
	  $this->view->faqpage_id = $result[0]['infopage_id'];
	  $communityadFaqTable = Engine_Api::_()->getItemTable('communityad_faq');
	  $communityadFaqName = $communityadFaqTable->info('name');

	  // fetch General or Design or Targeting FAQ according to the selected tab
	  $communityadFaqSelect = $communityadFaqTable->select()->from($communityadFaqName, array('question', 'answer', 'type', 'faq_default'))
					  ->where('status =?', 1)
					  ->where('type =?', $display_faq)
					  ->order('faq_id DESC');
	  $this->view->viewFaq = $communityadFaqSelect->query()->fetchAll();
	} else if (!empty($default_contact)) { // Condition: Fetch data for 'Contact us' type.
	  $contactTeam['numbers'] = Engine_Api::_()->getApi('settings', 'core')->ad_saleteam_con;
	  $contactTeam['emails'] = Engine_Api::_()->getApi('settings', 'core')->ad_saleteam_email;
	  $this->view->contactTeam = $contactTeam;
	}
  }

  // Function: Email to conteact team members if members email address not available then email to siteadmin.
  // Call From: 'Help and Learn More' => 'Contact sales team' .
  public function sendMessagesAction() {
	$this->view->form = $form = new Communityad_Form_Contactus();
	if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
	  $values = $form->getValues();
	  $email = Engine_Api::_()->getApi('settings', 'core')->core_mail_from;
	  $ownerEmail = Engine_Api::_()->getApi('settings', 'core')->ad_saleteam_email;
	  if (!empty($email)) {
		// Condition: If there are no E-mail address available of sales team member then message will go to admin derfault id.
		if (!empty($ownerEmail)) {
		  $ownerEmailArray = explode(",", $ownerEmail);
		  foreach ($ownerEmailArray as $owner_email) {
			Engine_Api::_()->getApi('mail', 'core')->sendSystem($owner_email, 'community_team_contact', array(
				'communityad_name' => $values['name'],
				'communityad_email' => $values['email'],
				'communityad_messages' => $values['message'],
				'email' => $email,
				'queue' => true
			));
		  }
		} else {
		  Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'community_team_contact', array(
			  'communityad_name' => $values['name'],
			  'communityad_email' => $values['email'],
			  'communityad_messages' => $values['message'],
			  'email' => $email,
			  'queue' => true
		  ));
		}
	  }

	  $this->_forward('success', 'utility', 'core', array(
		  'smoothboxClose' => 10,
		  'parentRefresh' => 10,
		  'messages' => array('Successsfully send messages.')
	  ));
	}
  }

// after click on ad redirect on the mention url
  public function adRedirectAction() {
	$adId = $this->_getParam('adId');
	$adId = Engine_Api::_()->communityad()->getEncodeToDecode($adId);
	$redirect = Engine_Api::_()->communityad()->ad_clickcount($adId);
	if ($redirect == 'false') {
	  return $this->_forward('notfound', 'error', 'core');
	}
  }

  // Function: Ajax based info return, when select any 'Module' then return all the content from that modules which are created by loggden user.
  // Return: Return selected module content array.
  // Call From: _formModtitle.tpl
  public function contenttypeAction() {
	$resource_type = $this->_getParam('resource_type');
	$story_type = $this->_getParam('story_type', null);
	$calling_from = $this->_getParam('calling_from', null);
	$resource_id = $this->_getParam('resource_id', null);

	$resource_array = array();
	if (!empty($resource_type)) {
	  $resource_array = Engine_Api::_()->communityad()->resource_content($resource_type, $story_type, $calling_from, $resource_id);
	  $getModType = Engine_Api::_()->getDbTable('modules', 'communityad')->getModuleInfo($resource_type);
	}

	$this->view->resource_string = $resource_array;
	$this->view->resource_type = $resource_type;
	if( !empty( $getModType ) && !empty($getModType['module_title']) ) {
	  $this->view->modTitle = $getModType['module_title'];
	}
  }

  // Function: Ajax based info return, when select any content from the drop down then return the information about that content.
  // Return: Return the all information about any content.
  // Call From: _formModtitle.tpl
  public function resourcecontentAction() {
	$resource_type = $this->_getParam('resource_type');
	$resource_id = $this->_getParam('resource_id');
	$is_spocerdStory = $this->_getParam('is_spocerdStory', null);

	$is_document = 0;
	if ($resource_type == 'document') {
	  $is_document = 1;
	}
  
  if( strstr($resource_type, "sitereview") ) {
    // $resource_type = "sitereview";

    $sitereviewExplode = explode("_", $resource_type);
    $tempAdModId = $sitereviewExplode[1];
    $module_info = Engine_Api::_()->getItem("communityad_module", $tempAdModId);
    $tempModName = strtolower($module_info->module_title);
    $tempModName = ucfirst($module_info->module_title);
    
    $content_table = "sitereview_listing";
    $sub_title = "View" . " " . $tempModName;
    $content_data = Engine_Api::_()->getItem($content_table, $resource_id);
  }else {
    $field_info = Engine_Api::_()->getDbTable('modules', 'communityad')->getModuleInfo($resource_type);

    if (!empty($field_info)) {
      $content_data = Engine_Api::_()->getItem($field_info['table_name'], $resource_id);
    }
  }

  $base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
  if( empty($sub_title) ) {
    $sub_title = Engine_Api::_()->communityad()->viewType($resource_type);
  }
	$photo_id_filepath = 0;

	if (empty($is_document)) {
	  $photo_id_filepath = $content_data->getPhotoUrl('thumb.normal');
	} else {
	  $photo_id_filepath = $content_data->thumbnail;
	}

	if (strstr($photo_id_filepath, '?')) {
	  $explode_array = explode("?", $photo_id_filepath);
	  $photo_id_filepath = $explode_array[0];
	}

	$isCDN = Engine_Api::_()->seaocore()->isCdn();

	if (empty($isCDN)) {
	  if (!empty($base_url)) {
		$photo_id_filepath = str_replace($base_url . '/', '', $photo_id_filepath);
	  } else {
		$arrqay = explode('/', $photo_id_filepath);
		unset($arrqay[0]);
		$photo_id_filepath = implode('/', $arrqay);
	  }
	}

	if (!empty($photo_id_filepath)) {
	  if (strstr($photo_id_filepath, 'application/')) {
		$photo_id_filepath = 0;
	  } else {
		$content_photo = $this->upload($photo_id_filepath, $is_document, $isCDN);
	  }
	}
	// Set "Title width" acording to the module.
	$getStoryContentTitle = $title = $content_data->getTitle();
	$title_lenght = strlen($title);
	$tmpTitle = strip_tags($content_data->getTitle());
	$titleTruncationLimit = $title_truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.title', 25);
	if ($title_lenght > $title_truncation_limit) {
	  $title_truncation_limit = $title_truncation_limit - 2;
	  $title = Engine_String::strlen($tmpTitle) > $title_truncation_limit ? Engine_String::substr($tmpTitle, 0, $title_truncation_limit) : $tmpTitle;
	  $title = $title . '..';
	}

	// Set "Body width" acording to the module.
	$body = $content_data->getDescription();
	$body_lenght = strlen($body);
	$tmpBody = strip_tags($content_data->getDescription());
	$body_truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.body', 135);
	if ($body_lenght > $body_truncation_limit) {
	  $body_truncation_limit = $body_truncation_limit - 2;
	  $body = Engine_String::strlen($tmpBody) > $body_truncation_limit ? Engine_String::substr($tmpBody, 0, $body_truncation_limit) : $tmpBody;
	  $body = $body . '..';
	}




	$preview_title = $title . '<div class="cmaddis_adinfo"><a href="javascript:void(0);">' . $sub_title . '</a></div>';
 	//$preview_title = $title . '<div class="cmaddis_adinfo cmad_show_tooltip_wrapper" style="clear:none;"><a href="javascript:void(0);">' . $sub_title . '</a><div class="cmad_show_tooltip"> <img src="./application/modules/Communityad/externals/images/tooltip_arrow.png" />Viewers will be able to like this ad and its content. They will also be able to see how many people like this ad, and which friends like this ad.</div></div>';

	$remaning_body_limit = $body_truncation_limit - strlen($body);
	if ($remaning_body_limit < 0) {
	  $remaning_body_limit = 0;
	}
	$remaning_title_limit = $title_truncation_limit - strlen($title);
	if ($remaning_title_limit < 0) {
	  $remaning_title_limit = 0;
	}

	// Set the default image if no image selected.
	if (empty($content_photo)) {
	  if (empty($is_spocerdStory)) {
		$path = $this->view->layout()->staticBaseUrl . '/application/modules/Communityad/externals/images/blankImage.png';
		$content_photo = '<img src="' . $path . '" alt=" " />';
	  } else {
	    $content_photo = $this->view->itemPhoto($content_data, 'thumb.profile');
	    if( in_array('music', array('music')) && in_array('blog', array('blog')) ) {
	      $content_photo = $this->view->itemPhoto($content_data, 'thumb.icon');
	    }
	  }
	}
	$viewerTruncatedTitle = Engine_Api::_()->communityad()->truncation($this->_viewer->getTitle(), $titleTruncationLimit);
	
	if ($is_spocerdStory == 1) {
	  $storyTrunLimit =  Engine_Api::_()->getApi('settings', 'core')->getSetting('story.char.title', 35);
	  $getStoryContentTitle = Engine_Api::_()->communityad()->truncation($getStoryContentTitle, $storyTrunLimit);
	  $getTooltipTitle = $this->view->translate("_sponsored_viewer_title_tooltip");
	  $getContentTooltipTitle =  $this->view->translate("_sponsored_content_title_tooltip");
	  $viewerTruncatedTitle =  '<span class="cmad_show_tooltip_wrapper"><b><a href="javascript:void(0);">' . $viewerTruncatedTitle . '</a></b><div class="cmad_show_tooltip"><img src="./application/modules/Communityad/externals/images/tooltip_arrow.png" style="width:13px;height:9px;" />'.$getTooltipTitle.'</div></span>';
	  $main_div_title = $this->view->translate('%s likes <a href="javascript:void(0);">%s.</a>', $viewerTruncatedTitle, $getStoryContentTitle);
	  $footer_comment = '';

$content_photo = '<a href="javascript:void(0);">' . $content_photo . '</a><div class="cmad_show_tooltip">
							<img src="./application/modules/Communityad/externals/images/tooltip_arrow.png" />
							'. $this->view->translate("_sponsored_content_photo_tooltip") .'
						</div>
					</div>';
	}else {
	  $title = Engine_Api::_()->communityad()->truncation($title, $titleTruncationLimit);
	}

	if (empty($is_spocerdStory)) {
	  $this->view->id = $content_data->getIdentity();
	  $this->view->title = $title;
	  $this->view->resource_type = $resource_type;
	  $this->view->des = $body;
	  $this->view->page_url = $content_data->getHref();
	  $this->view->photo = $content_photo;
	  $this->view->preview_title = $preview_title;
	  $this->view->remaning_body_text = $remaning_body_limit;
	  $this->view->remaning_title_text = $remaning_title_limit;
	  $this->view->photo_id_filepath = $photo_id_filepath;
	} else {
	  $this->view->main_div_title = $main_div_title;
	  $this->view->photo = $content_photo;
	  $this->view->temp_pre_title = $getStoryContentTitle; 
	  $getStoryContentTitle = str_replace(' ', '&nbsp;', $getStoryContentTitle);
	  $this->view->preview_title = $getStoryContentTitle; 
	  $this->view->footer_comment = $footer_comment;
	  $this->view->remaning_title_text = $remaning_title_limit;
	  $this->view->modTitle = $field_info['module_title'];
	}
  }

  // This function is call from 'resourcecontentAction()' for make a image image in temporary folder.
  public function upload($uploaded_image_path, $is_document, $isCDN) {
	if( empty($isCDN) ) {
	  $file = APPLICATION_PATH . DIRECTORY_SEPARATOR . $uploaded_image_path;
	}else {
	  $file = $uploaded_image_path;
	}
  
        if( empty($isCDN) && strstr($file, "//") ) {
          if( strstr($uploaded_image_path, "/public") ) {
            $tempExplode = explode("public", $uploaded_image_path);
            $tempPath = trim($tempExplode[1],"/");
            $uploaded_image_path = "public/" . $tempPath;
            $file = APPLICATION_PATH . DIRECTORY_SEPARATOR . $uploaded_image_path;
          }
        }
    
	if (!empty($isCDN) || @is_file($file) || !empty($is_document)) {
	  @chmod($file, 0777);
	  @unlink($this->_session->photoName_Temp_module);
	} else {
	  if (isset($this->_session->photoName_Temp_module)) {
		if (is_file($this->_session->photoName_Temp_module)) {
		  @chmod($this->_session->photoName_Temp_module, 0777);
		  @unlink($this->_session->photoName_Temp_module);
		}
		unset($this->_session->photoName_Temp_module);
	  }
	  return;
	}

	$file1 = str_replace('/', '_', $uploaded_image_path);
	$name = $file1;
	$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/communityad/temporary';
	$min = 60;
	$maxW = $createWidth = Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.image.width', 120);
	$maxH = $createHight = Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.image.hight', 90);

	// Recreate image  not delete this code
	$image = Engine_Image::factory();
	$image->open($file);

	$dstW = $image->width;
	$dstH = $image->height;

	$multiplier = min($maxW / $dstW, $maxH / $dstH);
	if ($multiplier > 1) {
	  $dstH *= $multiplier;
	  $dstW *= $multiplier;
	}
	if (($delta = $maxW / $dstW) < 1) {
	  $dstH = round($dstH * $delta);
	  $dstW = round($dstW * $delta);
	}
	if (($delta = $maxH / $dstH) < 1) {
	  $dstH = round($dstH * $delta);
	  $dstW = round($dstW * $delta);
	}

	$createHight = $dstH;
	$createWidth = $dstW;
	if ($createWidth < $min)
	  $createWidth = $min;

	if ($createHight < $min)
	  $createHight = $min;

	// Resize image (icon)
	$image = Engine_Image::factory();
	$image->open($file);
	$image->resample(0, 0, $image->width, $image->height, $createWidth, $createHight)
			->write($path . '/' . $name)
			->destroy();

	$photoName = $this->view->baseUrl() . '/public/communityad/temporary/' . $name;
	$currentIMagePath = $path . '/' . $name;
	if (isset($this->_session->photoName_Temp_module)) {
	  if ($currentIMagePath !== $this->_session->photoName_Temp_module) {
		if (is_file($this->_session->photoName_Temp_module) || !empty($isCDN)) {
		  @chmod($this->_session->photoName_Temp_module, 0777);
		  @unlink($this->_session->photoName_Temp_module);
		}
	  }
	  unset($this->_session->photoName_Temp_module);
	}
	if (isset($this->_session->photoName_Temp)) {
	  if (is_file($this->_session->photoName_Temp) || !empty($isCDN)) {
		@chmod($this->_session->photoName_Temp, 0777);
		@unlink($this->_session->photoName_Temp);
	  }
	  unset($this->_session->photoName_Temp);
	}
	$this->_session->photoName_Temp_module = $path . '/' . $name;
	return '<img  src="' . $photoName . '" alt="" />';
  }

}
?>
