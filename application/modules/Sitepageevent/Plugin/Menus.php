<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 6590 2010-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_Plugin_Menus {

  //SHOWING THE EDIT LINK ON THE LEFT SITE MENU ON EVENT VIEW PAGE
  public function onMenuInitialize_SitepageeventGutterEdit($row) {
    
    //GETTING THE VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GETTING THE SITEPAGEEVENT SUBJECT
    $subject = Engine_Api::_()->core()->getSubject();

    //GETTING THE TAB ID
    $tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab_id', null);

    // SITEPAGEEVENT SUBJECT OR NOT
    if ($subject->getType() !== 'sitepageevent_event') {
      $error_msg1 = Zend_Registry::get('Zend_Translate')->_('Whoops, not a page event!');
      throw new Sitepageevent_Model_Exception($error_msg1);
    }

    //START MANAGE-ADMIN CHECK
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $subject->page_id);
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    
    // SUPERADMIN, EVENT OWNER AND SITEPAGE OWNER CAN EDIT EVENT
    if ($viewer->getIdentity() != $subject->user_id && $can_edit != 1) {
      return false;
    }

    //Return EDIT LINK
    return array(
        'label' => 'Edit_Event_Details',
        'icon' => Zend_Registry::get('Zend_View')->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/icons/edit.png',
        'route' => 'sitepageevent_specific',
        'params' => array(
            'controller' => 'index',
            'action' => 'edit',
            'event_id' => $subject->getIdentity(),
            'page_id' => $subject->page_id,
            'tab_id' => $tab_selected_id
        )
    );
  }

  //SHOWING THE EDIT LINK ON THE LEFT SITE MENU ON EVENT VIEW PAGE
  public function onMenuInitialize_SitepageeventGutterEditlocation($row) {
    
    //GETTING THE VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GETTING THE SITEPAGEEVENT SUBJECT
    $subject = Engine_Api::_()->core()->getSubject();

    //GETTING THE TAB ID
    $tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab_id', null);

    // SITEPAGEEVENT SUBJECT OR NOT
    if ($subject->getType() !== 'sitepageevent_event') {
      $error_msg1 = Zend_Registry::get('Zend_Translate')->_('Whoops, not a page event!');
      throw new Sitepageevent_Model_Exception($error_msg1);
    }

    //START MANAGE-ADMIN CHECK
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $subject->page_id);
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    
    // SUPERADMIN, EVENT OWNER AND SITEPAGE OWNER CAN EDIT EVENT
    if ($viewer->getIdentity() != $subject->user_id && $can_edit != 1) {
      return false;
    }

    //Return EDIT LINK
    return array(
        'label' => 'Edit Location',
        'icon' => Zend_Registry::get('Zend_View')->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/map.png',
        'route' => 'sitepageevent_specific',
        'params' => array(
            'controller' => 'index',
            'action' => 'edit-location',
            'seao_locationid' => $subject->seao_locationid,
            'event_id' => $subject->getIdentity(),
            'page_id' => $subject->page_id,
            'tab_id' => $tab_selected_id
        )
    );
  }


  //SHOWING THE CREATE LINK ON THE LEFT SITE MENU ON EVENT VIEW PAGE
  public function onMenuInitialize_SitepageeventGutterCreate($row) {
    
    //GETTING THE VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GETTING THE SITEPAGEEVENT SUBJECT
    $subject = Engine_Api::_()->core()->getSubject();

    //GETTING THE TAB ID
    $tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab_id', null);

    // SITEPAGEEVENT SUBJECT OR NOT
    if ($subject->getType() !== 'sitepageevent_event') {
      $error_msg2 = Zend_Registry::get('Zend_Translate')->_('Whoops, not a page event!');
      throw new Sitepageevent_Model_Exception($error_msg2);
    }

    //START MANAGE-ADMIN CHECK
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $subject->page_id);
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'secreate');
    if (empty($isManageAdmin)) {
      $can_create = 0;
    } else {
      $can_create = 1;
    }
    //END MANAGE-ADMIN CHECK
    
    //CHECK THE VIEWER IS PAGE OWNER OR NOT
    if ($can_create != 1) {
      return false;
    }

    //RETURN CREATE LINK
    return array(
        'label' => 'Create_Event',
        'icon' => Zend_Registry::get('Zend_View')->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/icons/sitepageevent_create.png',
        'route' => 'sitepageevent_create',
        'params' => array(
            'page_id' => $subject->page_id,
            'tab_id' => $tab_selected_id
        )
    );
  }

  //SHOWING THE MEMBER LINK ON THE LEFT SITE MENU ON EVENT VIEW PAGE
  public function onMenuInitialize_SitepageeventGutterMember() {
    
    //GETTING THE VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GETTING THE TAB ID
    $tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab_id', null);

    //GETTING THE SITEPAGEEVENT SUBJECT
    $subject = Engine_Api::_()->core()->getSubject();

    //GETTING THE SITEPAGE ITEM	
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $subject->page_id);

    //GETTING THE VIEWER Id
    $viewer_id = $viewer->getIdentity();
    if (!empty($viewer_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = 0;
    }

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    
    // SUPERADMIN, EVENT OWNER AND SITEPAGE OWNER CAN DELETE EVENT
    if ($viewer_id == $subject->user_id || $can_edit == 1) {
      $can_delete = 1;
    } else {
      $can_delete = 0;
    }

    //SITEPAGEEVENT SUBJECT OR NOT
    if ($subject->getType() !== 'sitepageevent_event') {
      $error_msg3 = Zend_Registry::get('Zend_Translate')->_('Whoops, not a page event!');
      throw new Sitepageevent_Model_Exception($error_msg3);
    }

    //SITEPAGEEVENT SUBJECT OR NOT
    if (!$viewer->getIdentity()) {
      return false;
    }

    $row = $subject->membership()->getRow($viewer);
    if (null === $row) {
      if ($subject->membership()->isResourceApprovalRequired()) {
        return array(
            //RETURN THE REQUEST INVITE LINK
            'label' => 'Request_Invite',
            'icon' => Zend_Registry::get('Zend_View')->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/icons/sitepageevent_join.png',
            'class' => 'smoothbox',
            'route' => 'sitepageevent_specific',
            'params' => array(
                'controller' => 'index',
                'action' => 'request',
                'event_id' => $subject->getIdentity(),
                'page_id' => $subject->page_id,
                'tab_id' => $tab_selected_id
            ),
        );
      } else {
        //RETURN THE JOIN EVENT LINK
        return array(
            'label' => 'Join_Event',
            'icon' => Zend_Registry::get('Zend_View')->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/icons/sitepageevent_join.png',
            'class' => 'smoothbox',
            'route' => 'sitepageevent_specific',
            'params' => array(
                'controller' => 'index',
                'action' => 'join',
                'event_id' => $subject->getIdentity(),
                'page_id' => $subject->page_id,
                'tab_id' => $tab_selected_id
            ),
        );
      }
    } else if ($row->active) {
      //RETURN THE LEAVE EVENT LINK
      if (!$subject->isOwner($viewer)) {
        return array(
            'label' => 'Leave_Event',
            'icon' => Zend_Registry::get('Zend_View')->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/icons/sitepageevent_leave.png',
            'class' => 'smoothbox',
            'route' => 'sitepageevent_specific',
            'params' => array(
                'controller' => 'index',
                'action' => 'leave',
                'event_id' => $subject->getIdentity(),
                'page_id' => $subject->page_id,
                'tab_id' => $tab_selected_id
            ),
        );
      } elseif ($can_delete) {
        //RETURN THE DELETE EVENT LINK
        return array(
            'label' => 'Delete_Event',
            'icon' => Zend_Registry::get('Zend_View')->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/icons/delete.png',
            'route' => 'sitepageevent_specific',
            'params' => array(
                'controller' => 'index',
                'action' => 'delete',
                'event_id' => $subject->getIdentity(),
                'page_id' => $subject->page_id,
                'tab_id' => $tab_selected_id
            ),
        );
      }
    } else if (!$row->resource_approved && $row->user_approved) {
      //RETURN THE CANCEL EVENT LINK
      return array(
          'label' => 'Cancel_Invite_Request',
          'icon' => Zend_Registry::get('Zend_View')->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/icons/sitepageevent_cancel.png',
          'class' => 'smoothbox',
          'route' => 'sitepageevent_extended',
          'params' => array(
              'controller' => 'index',
              'action' => 'cancel',
              'event_id' => $subject->getIdentity(),
              'page_id' => $subject->page_id,
              'user_id' => $viewer->getIdentity(),
              'tab_id' => $tab_selected_id
          ),
      );
    } else if (!$row->user_approved && $row->resource_approved) {
      //RETURN THE ACCEPT OR IGONRE EVENT LINK
      return array(
          array(
              'label' => 'Accept_Event_Invite',
              'icon' => Zend_Registry::get('Zend_View')->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/icons/sitepageevent_accept.png',
              'class' => 'smoothbox',
              'route' => 'sitepageevent_extended',
              'params' => array(
                  'controller' => 'index',
                  'action' => 'accept',
                  'event_id' => $subject->getIdentity(),
                  'page_id' => $subject->page_id,
                  'user_id' => $viewer->getIdentity(),
                  'tab_id' => $tab_selected_id,
              ),
          ), array(
              'label' => 'Ignore_Event_Invite',
              'icon' => Zend_Registry::get('Zend_View')->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/icons/sitepageevent_reject.png',
              'class' => 'smoothbox',
              'route' => 'sitepageevent_extended',
              'params' => array(
                  'controller' => 'index',
                  'action' => 'reject',
                  'event_id' => $subject->getIdentity(),
                  'page_id' => $subject->page_id,
                  'user_id' => $viewer->getIdentity(),
                  'tab_id' => $tab_selected_id
              ),
          )
      );
    } else {
      $error_msg4 = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      throw new Sitepageevent_Model_Exception($error_msg4);
    }

    return false;
  }

  //SHOWING THE INVITE LINK ON THE LEFT SITE MENU ON EVENT VIEW PAGE
  public function onMenuInitialize_SitepageeventGutterInviteMembers($row) {
    
    //GETTING THE VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    //GETTING THE TAB ID
    $tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab_id', null);

    //GETTING THE SITEPAGEEVENT SUBJECT
    $subject = Engine_Api::_()->core()->getSubject();
	  $page_id = $subject->page_id;
	  
    // SITEPAGEEVENT SUBJECT OR NOT
    if ($subject->getType() !== 'sitepageevent_event') {
      throw new Sitepageevent_Model_Exception('This page event does not exist.');
    }

		$pagemember = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
		if (empty($pagemember)) {
			return false;
		} else {
			$select = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id, $page_id);
			$pageasgroup = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.pageasgroup');
    }
    if (empty($select)) {
			return false;
    }
    if(empty($pageasgroup)) {
			return false;
    }

    //CHECK WHETHER THE PERSON HAS ALLOWES TO INVITE OTHER PEOPLE TO THEIR EVENT.
//     if (!$subject->authorization()->isAllowed($viewer, 'invite')) {
//       return false;
//     }

    // Modify params
    $params = $row->params;
    $params['params']['page_id'] = $page_id;
    $params['params']['event_id'] = $subject->getIdentity();
    return $params;
  }
  
  //SHOWING THE INVITE LINK ON THE LEFT SITE MENU ON EVENT VIEW PAGE
  public function onMenuInitialize_SitepageeventGutterInvite() {
    
    //GETTING THE VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GETTING THE TAB ID
    $tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab_id', null);

    //GETTING THE SITEPAGEEVENT SUBJECT
    $subject = Engine_Api::_()->core()->getSubject();

    // SITEPAGEEVENT SUBJECT OR NOT
    if ($subject->getType() !== 'sitepageevent_event') {
      throw new Sitepageevent_Model_Exception('This page event does not exist.');
    }

    //CHECK WHETHER THE PERSON HAS ALLOWES TO INVITE OTHER PEOPLE TO THEIR EVENT.
    if (!$subject->authorization()->isAllowed($viewer, 'invite')) {
      return false;
    }
    
		$pagemember = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
		$pageasgroup = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.pageasgroup');
		if (!empty($pagemember) && !empty($pageasgroup)) {
			return false;
		}

		
    //RETURN INVITE LINK
    return array(
        'label' => 'Invite_Guests',
        'icon' => Zend_Registry::get('Zend_View')->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/icons/invite.png',
        'class' => 'smoothbox',
        'route' => 'sitepageevent_specific',
        'params' => array(
            'controller' => 'index',
            'action' => 'invite',
            'event_id' => $subject->getIdentity(),
            'page_id' => $subject->page_id,
            'format' => 'smoothbox',
            'tab_id' => $tab_selected_id,
        ),
    );
  }

  public function onMenuInitialize_SitepageeventGutterShare() {
    
    //GETTING THE VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GETTING THE TAB ID
    $tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab_id', null);

    //GETTING THE SITEPAGEEVENT SUBJECT
    $subject = Engine_Api::_()->core()->getSubject();

    //SITEPAGEEVENT SUBJECT OR NOT
    if ($subject->getType() !== 'sitepageevent_event') {
      $error_msg5 = Zend_Registry::get('Zend_Translate')->_('This page event does not exist.');
      throw new Sitepageevent_Model_Exception($error_msg5);
    }

    //CHECK WHETHER THE PERSON IS THERE OR NOT
    if (!$viewer->getIdentity()) {
      return false;
    }

    //RETURN SHARE LINK
    return array(
        'label' => 'Share_This_Event',
        'icon' => Zend_Registry::get('Zend_View')->layout()->staticBaseUrl.'application/modules/Sitepageevent/externals/images/share.png',
        'class' => 'smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'share',
            'type' => $subject->getType(),
            'id' => $subject->getIdentity(),
            'format' => 'smoothbox',
            'tab_id' => $tab_selected_id
        ),
    );
  }

  public function onMenuInitialize_SitepageeventGutterBacktopage() {
    
    //GETTING THE VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GETTING THE TAB ID
    $tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab_id', null);

    //GETTING THE SITEPAGEEVENT SUBJECT
    $subject = Engine_Api::_()->core()->getSubject();
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $subject->page_id);   
    // SITEPAGEEVENT SUBJECT OR NOT
    if ($subject->getType() !== 'sitepageevent_event') {
      $error_msg6 = Zend_Registry::get('Zend_Translate')->_('This page event does not exist.');
      throw new Sitepageevent_Model_Exception($error_msg6);
    }

    $page_url = $sitepage->getHref(array('tab' => $tab_selected_id));
   
    //RETURN BACK TO PAGE LINK
    return array(
        'label' => 'Back to Page',
        'icon' => Zend_Registry::get('Zend_View')->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/icons/back16.gif',
        'uri' =>  $page_url
        
    );
  }

  public function onMenuInitialize_SitepageeventGutterDay() {
    
    //GETTING THE VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    //GETTING THE SITEPAGEEVENT SUBJECT
    $subject = Engine_Api::_()->core()->getSubject();
    $allowView = false;
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $subject->page_id);
    if (($viewer->getIdentity()) && ($viewer->level_id == 1)) {
      $auth = Engine_Api::_()->authorization()->context;
      $allowView = $auth->isAllowed($sitepage, 'everyone', 'view') === 1 ? true : false ||$auth->isAllowed($sitepage, 'registered', 'view') === 1 ? true : false;
    } 
    if(empty($allowView)) {
      return false;
   }
   return array(
				'route' => 'default',
         'class' => 'smoothbox',
        'icon' => Zend_Registry::get('Zend_View')->layout()->staticBaseUrl.'application/modules/Sitepage/externals/images/icons/sitepageevent.png',
				'params' => array(
						'module' => 'sitepageevent',
						'controller' => 'index',
						'action' => 'add-event-of-day',
						'event_id' => $subject->event_id,
				),
    );
  }


  public function canViewEvents() {

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepageevent.event.show.menu', 1)) {
      return false;
    }

    $table = Engine_Api::_()->getDbtable('events', 'sitepageevent');
    $rName = $table->info('name');
    $table_pages = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $rName_pages = $table_pages->info('name');
    $select = $table->select()
                    ->setIntegrityCheck(false)
                    ->from($rName_pages, array('photo_id', 'title as sitepage_title'))
                    ->join($rName, $rName . '.page_id = ' . $rName_pages . '.page_id')
                    ->where($rName .'.search = ?', 1);

    $select = $select
                    ->where($rName_pages . '.closed = ?', '0')
                    ->where($rName_pages . '.approved = ?', '1')
                    ->where($rName_pages . '.search = ?', '1')
                    ->where($rName_pages . '.declined = ?', '0')
                    ->where($rName_pages . '.draft = ?', '1');
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $select->where($rName_pages . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
    $row = $table->fetchAll($select);
    $count = count($row);
    if (empty($count)) {
      return false;
    }
    return true;
  }


 //PHOTO VIEW PAGE OPTIONS
  public function onMenuInitialize_SitepageeventPhotoEdit($row) {

     //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $subject = Engine_Api::_()->core()->getSubject();
    //GET ALBUM ID
   $event_id = $subject->event_id;
    
    $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', $event_id);
    
    //GET PAGE ID
    $page_id = $sitepageevent->page_id;

    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
 
    //PHOTO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT PHOTO
    if ($viewer_id != $subject->user_id && $can_edit != 1) {
      return false;
    }


    return array(
        'label' => 'Edit',
        'route' => 'sitepageevent_photo_extended',
        'class' => 'ui-btn-action smoothbox',
        'params' => array(
          'action' => 'photo-edit',
            'photo_id' => $subject->photo_id,
            'event_id' => $event_id,
            'page_id' => $page_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitepageeventPhotoDelete($row) {

     //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    $subject = Engine_Api::_()->core()->getSubject();
    //GET ALBUM ID
   $event_id = $subject->event_id;
    
    $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', $event_id);
    
    //GET PAGE ID
    $page_id = $sitepageevent->page_id;

    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
 
    //PHOTO OWNER, PAGE OWNER AND SUPER-ADMIN CAN EDIT PHOTO
    if ($viewer_id != $subject->user_id && $can_edit != 1) {
      return false;
    }
    
    return array(
        'label' => 'Delete',
        'route' => 'sitepageevent_photo_extended',
        'class' => 'ui-btn-danger smoothbox',
        'params' => array(
            'action' => 'remove',
            'photo_id' => $subject->photo_id,
            'event_id' => $event_id,
            'page_id' =>  $page_id,
            'owner_id' => $subject->user_id,
            'tab' => Zend_Controller_Front::getInstance()->getRequest()->getParam('tab')
        )
    );
  }

  public function onMenuInitialize_SitepageeventPhotoShare($row) {
    $subject = Engine_Api::_()->core()->getSubject();
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    if(!$viewer_id){
      return false;
    }
    if (!SEA_PHOTOLIGHTBOX_SHARE) {
      return false;
    }
    return array(
        'label' => 'Share',
        'class' => 'ui-btn-action smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'activity',
            'action' => 'share',
            'type' => $subject->getType(),
            'id' => $subject->getIdentity(),
        )
    );
  }

  public function onMenuInitialize_SitepageeventPhotoReport($row) {
    $subject = Engine_Api::_()->core()->getSubject();
    //GET VIEWER ID
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    
    if(!$viewer_id){
      return false;
    }
    if (!SEA_PHOTOLIGHTBOX_REPORT) {
      return false;
    }
    return array(
        'label' => 'Report',
        'class' => 'ui-btn-action smoothbox',
        'route' => 'default',
        'params' => array(
            'module' => 'core',
            'controller' => 'report',
            'action' => 'create',
            'subject' => $subject->getGuid(),
        )
    );
  }

}

?>