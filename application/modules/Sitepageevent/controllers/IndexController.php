<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 6590 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_IndexController extends Seaocore_Controller_Action_Standard {

  public function init() {

    //GET PAGE ID
    $page_id = $this->_getParam('page_id');

    //PACKAGE BASE PRIYACY START
    if (!empty($page_id)) {
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageevent")) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        }
      } else {
        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'secreate');
        if (empty($isPageOwnerAllow)) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        }
      }
    }
    //PACKAGE BASE PRIYACY END
    else {
      if ($this->_getParam('event_id') != null) {
        $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', $this->_getParam('event_id'));
        if ($sitepageevent) {
          $page_id = $sitepageevent->page_id;
        }
      }
    }

    //GET EVENT ID
    $event_id = $this->_getParam('event_id');
    if ($event_id) {
      $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', $event_id);
      if ($sitepageevent) {
        Engine_Api::_()->core()->setSubject($sitepageevent);
      }
    }
  }

  //ACTION FOR CREATE THE EVENT
  public function createAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser->isValid())
      return;

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();

    //PAGE ID
    $page_id = $this->_getParam('page_id');

    //SEND TAB ID TO TPL FILE
    $this->view->tab_selected_id = $this->_getParam('tab_id');

    //SET PARENT TYPE
    $parent_type = 'sitepage_page';

    $getPackageevent = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepageevent');

    //GET SITEPAGE ITEM
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $sitepageevent_isCraete = Zend_Registry::isRegistered('sitepageevent_isCraete') ? Zend_Registry::get('sitepageevent_isCraete') : null;
    if (empty($sitepageevent_isCraete)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $this->view->can_edit = $can_edit = 0;
    } else {
      $this->view->can_edit = $can_edit = 1;
    }

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'secreate');
    if (empty($isManageAdmin) && empty($can_edit)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

    //SHOW EVENT CREATE FORM
    $this->view->form = $form = new Sitepageevent_Form_Create(array(
        'parent_type' => $parent_type,
        'parent_id' => $page_id
    ));

    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      $form->addElement("dummy", "dummy", array('label' => 'Main Photo', 'description' => 'Sorry, the browser you are using does not support Photo uploading. We recommend you to create an Event from your mobile / tablet without uploading a main photo for it. You can later upload the main photo from your Desktop.', 'order' => 6, 'style' => 'display:none;'));
      if (preg_match('/' . 'iPad' . '/i', $_SERVER['HTTP_USER_AGENT'])) {
        if (isset($form->photo)) {
          $form->removeElement('photo');
        }
      } else {
        if (isset($form->photo)) {
          $form->photo->setAttrib('accept', "image/*");
        }
      }
    }

    // Populate with categories
    $categories = Engine_Api::_()->getDbtable('categories', 'sitepageevent')->getCategoriesAssoc();
    asort($categories, SORT_LOCALE_STRING);
    $categoryOptions = array('0' => '');
    foreach ($categories as $k => $v) {
      $categoryOptions[$k] = $v;
    }
    if (sizeof($categoryOptions) <= 1) {
      $form->removeElement('category_id');
    } else {
      $form->category_id->setMultiOptions($categoryOptions);
    }

    $get_event = Engine_Api::_()->getItemTable('sitepageevent_event')->getEventUserType();

    $sitepageModHostName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));

    if (empty($get_event)) {
      return;
    }

    //IF NOT POST OR FORM NOT VALID, RETURN
    if ((!$this->getRequest()->isPost())) {
      return;
    }

    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      $tempPost = $this->getRequest()->getPost();
      if (isset($tempPost['photo']))
        $form->removeElement('photo');
    }
    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //PROCESS
    $values = empty($getPackageevent) ? null : $form->getValues();
    if (empty($values)) {
      return $this->_helper->redirector->gotoRoute(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id), 'tab' => $this->view->tab_selected_id), 'sitepage_entry_view', true);
    }

    $isModType = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepageevent.set.type', 0);
    if (empty($isModType)) {
//      Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepageevent.keyword', convert_uuencode($sitepageModHostName));
    }

    //SET USER ID AND PARENT TYPE
    $values['user_id'] = $viewer->getIdentity();
    $values['parent_type'] = $parent_type;

    //CONVERT TIMES
    $oldTz = date_default_timezone_get();
    date_default_timezone_set($viewer->timezone);
    $start = strtotime($values['starttime']);
    $end = strtotime($values['endtime']);
    date_default_timezone_set($oldTz);
    $values['starttime'] = date('Y-m-d H:i:s', $start);
    $values['endtime'] = date('Y-m-d H:i:s', $end);

    //PROCESS
    $db = Engine_Api::_()->getDbTable('events', 'sitepageevent')->getAdapter();
    $db->beginTransaction();
    try {
      //CREATE EVENT      
      $tableEvent = Engine_Api::_()->getDbTable('events', 'sitepageevent');
      $sitepageevent = $tableEvent->createRow();
      $sitepageevent->setFromArray($values);
      $sitepageevent->save();

      //ADD OWNER AS MEMBER
      $sitepageevent->membership()->addMember($viewer)
              ->setUserApproved($viewer)
              ->setResourceApproved($viewer);

      //ADD OWNER RSVP
      $sitepageevent->membership()
              ->getMemberInfo($viewer)
              ->setFromArray(array('rsvp' => 2))
              ->save();

      //SAVE VALUES     
      $sitepageevent->page_id = $page_id;
      $sitepageevent->view_count = 1;
      $sitepageevent->save();

      //PROCESS PRIVACY
      $auth = Engine_Api::_()->authorization()->context;
      if (isset($values['auth_invite']))
        $auth->setAllowed($sitepageevent, 'member', 'invite', $values['auth_invite']);

      //COMMENT PRIVACY
      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      $auth_view = "everyone";
      $viewMax = array_search($auth_view, $roles);
      foreach ($roles as $i => $role) {
        $auth->setAllowed($sitepageevent, $role, 'view', ($i <= $viewMax));
      }

      //ADD PHOTO
      if (!empty($values['photo'])) {
        $sitepageevent->setPhoto($form->photo);
      }

      //INSERT ACTIVITY IF EVENT IS SEARCHABLE
      if ($sitepageevent->search == 1) {
        $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
        $activityFeedType = null;
        if (Engine_Api::_()->sitepage()->isPageOwner($sitepage) && Engine_Api::_()->sitepage()->isFeedTypePageEnable())
          $activityFeedType = 'sitepageevent_admin_new';
        elseif ($sitepage->all_post || Engine_Api::_()->sitepage()->isPageOwner($sitepage))
          $activityFeedType = 'sitepageevent_new';

        if ($activityFeedType) {
          $action = $actionTable->addActivity($viewer, $sitepage, $activityFeedType);
          Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
        }
        if ($action != null) {
          $actionTable->attachActivity($action, $sitepageevent);
        }

        //SENDING ACTIVITY FEED TO FACEBOOK.
        $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
        if (!empty($enable_Facebooksefeed)) {
          $event_array = array();
          $event_array['type'] = 'sitepageevent_new';
          $event_array['object'] = $sitepageevent;
          Engine_Api::_()->facebooksefeed()->sendFacebookFeed($event_array);
        }
      }

      if (!empty($values['all_members'])) {
        Engine_Api::_()->sitepageevent()->sendInviteEmail($sitepageevent, null, 'sitepageevent_invite', 'SITEPAGEEVENT_INVITE_EMAIL', 'Pageevent Invite');
      }

      //PAGE EVENT CREATE NOTIFICATION AND EMAIL WORK

      if (Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')) {
        if (!empty($action)) {
          $sitepageVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitepage')->version;
          if ($sitepageVersion >= '4.3.0p1') {
            Engine_Api::_()->sitepage()->sendNotificationEmail($sitepageevent, $action, 'sitepageevent_create', 'SITEPAGEEVENT_CREATENOTIFICATION_EMAIL', 'Pageevent Invite');

            $isPageAdmins = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->isPageAdmins($viewer->getIdentity(), $page_id);
            if (!empty($isPageAdmins)) {

              //NOTIFICATION FOR ALL FOLLWERS.
              Engine_Api::_()->sitepage()->sendNotificationToFollowers($sitepageevent, $action, 'sitepageevent_create');
            }
          }
        }
      }

      //COMMIT
      $db->commit();

      //REDIRECTING TO THE EVENT VIEW PAGE
      return $this->_helper->redirector->gotoUrl($sitepageevent->getHref(), array('prependBase' => false));
    } catch (Engine_Image_Exception $e) {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  //ACTION FOR EDIT LOCATION
  public function editLocationAction() {

    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();

    //SEND TAB ID TO TPL FILE
    $this->view->tab_selected_id = $this->_getParam('tab_id');

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

    //GET EVENT ID AND EVENT ITEM  
    $this->view->event_id = $event_id = $this->_getParam('event_id');
    $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', $event_id);

    //GET PAGE ID, PAGE OBJECT AND THEN CHECK PAGE VALIDATION
    $this->view->seao_locationid = $seao_locationid = $this->_getParam('seao_locationid');
    $this->view->page_id = $page_id = $this->_getParam('page_id');
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK
    Engine_Api::_()->sitepageevent()->setEventPackages();
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //EVENT OWNER, PAGE OWNER AND MANAGE ADMIN CAN EDIT EVENT
    if ($viewer->getIdentity() != $sitepageevent->user_id && $can_edit != 1) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    $value['id'] = $seao_locationid;
    $this->view->location = $location = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocations($value);

    //Get form
    if (!empty($location)) {

      $this->view->form = $form = new Seaocore_Form_Location(array(
          'item' => $sitepage,
          'location' => $location->location
      ));

      if (!$this->getRequest()->isPost()) {
        $form->populate($location->toarray()
        );
        return;
      }

      //FORM VALIDAITON
      if (!$form->isValid($this->getRequest()->getPost())) {
        return;
      }

      //FORM VALIDAITON
      if ($form->isValid($this->getRequest()->getPost())) {

        $values = $form->getValues();
        unset($values['submit']);
        unset($values['location']);

        $seLocationsTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
// 
// 				$select = $seLocationsTable->select()->where('formatted_address LIKE ?', $values['formatted_address']);
// 				$results = $seLocationsTable->fetchRow($select);
// 
// 				if(empty($results->location_id)) {
// 					  //Accrodeing to event  location entry in the seaocore location table.
// 						$row = $seLocationsTable->createRow();
// 						$row->location = $location->location;
// 						$row->formatted_address = $values['formatted_address'];
// 						$row->latitude = $values['latitude'];
// 						$row->longitude = $values['longitude'];
// 						$row->address = $values['address'];
// 						$row->city = $values['city'];
// 						$row->zipcode = $values['zipcode'];
// 						$row->state = $values['state'];
// 						$row->country = $values['country'];
// 						$row->zoom = $values['zoom'];
// 						$row->save();
// 
// 						//event table entry of location id.
// 						$eventstable = Engine_Api::_()->getDbtable('events', 'sitepageevent');
// 						$eventstable->update(array('seao_locationid'=>  $row['location_id']), array('event_id =?' => $event_id));
// 				} else {
// 					//event table entry of location id.
// 					$eventstable = Engine_Api::_()->getDbtable('events', 'sitepageevent');
// 					$eventstable->update(array('seao_locationid'=>  $results->location_id), array('event_id =?' => $event_id));
// 				}
        $seLocationsTable->update($values, array('locationitem_id =?' => $seao_locationid));
      }
      $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
    }
    $this->view->location = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocations($value);
    //$this->view->location = Engine_Api::_()->getDbtable('locations', 'sitepage')->getLocation($value);
  }

  //ACTION FOR EDIT ADDRESS
  public function editAddressAction() {

    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET PAGE ID, PAGE OBJECT AND THEN CHECK PAGE VALIDATION
    $seao_locationid = $this->_getParam('seao_locationid');
    $tab_id = $this->_getParam('tab_id');
    $event_id = $this->_getParam('event_id');
    $page_id = $this->_getParam('page_id');
    $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', $event_id);

    $this->view->form = $form = new Sitepageevent_Form_Address(array('item' => $sitepageevent));

    //POPULATE FORM
    if (!$this->getRequest()->isPost()) {
      $form->populate($sitepageevent->toArray());
      return;
    }

    //FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {

      $values = $form->getValues();

      $sitepageevent->location = $values['location'];
      if (empty($values['location'])) {
        //DELETE THE RESULT FORM THE TABLE.
        Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $event_id, 'resource_type = ?' => 'event'));
        $sitepageevent->seao_locationid = '0';
      }
      $sitepageevent->save();
      unset($values['submit']);

      if (!empty($values['location'])) {

        //DELETE THE RESULT FORM THE TABLE.
        Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $event_id, 'resource_type = ?' => 'sitepageevent_event'));

        $seaoLocation = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($values['location'], '', 'sitepageevent_event', $event_id);

        //event table entry of location id.
        Engine_Api::_()->getDbtable('events', 'sitepageevent')->update(array('seao_locationid' => $seaoLocation), array('event_id =?' => $event_id));
      }

      $db->commit();
      $this->_forwardCustom('success', 'utility', 'core', array(
          'smoothboxClose' => 500,
          'parentRedirect' => $this->_helper->url->url(array('action' => 'edit-location', 'seao_locationid' => $seaoLocation, 'event_id' => $event_id, 'page_id' => $page_id, 'tab_id' => $tab_id), 'sitepageevent_specific', true),
          'parentRedirectTime' => '2',
          'format' => 'smoothbox',
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your page event location has been modified successfully.'))
      ));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  //ACTION FOR EDIT THE EVENT
  public function editAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();

    //GET EVENT ID AND EVENT ITEM  
    $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', $this->getRequest()->getParam('event_id'));

    //PAGE ID
    $page_id = $sitepageevent->page_id;

    //SEND TAB ID TO TPL FILE
    $this->view->tab_selected_id = $this->_getParam('tab_id');

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

    //GENERATE EDIT FORM
    $this->view->form = $form = new Sitepageevent_Form_Edit(array('parent_type' => $sitepageevent->parent_type, 'parent_id' => $page_id));

    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      $form->addElement("dummy", "dummy", array('label' => 'Main Photo', 'description' => 'Sorry, the browser you are using does not support Photo uploading. We recommend you to edit an Event from your mobile / tablet without uploading a main photo for it. You can later upload the main photo from your Desktop.', 'order' => 6, 'style' => 'display:none;'));

      if (preg_match('/' . 'iPad' . '/i', $_SERVER['HTTP_USER_AGENT'])) {
        if (isset($form->photo)) {
          $form->removeElement('photo');
        }
      } else {
        if (isset($form->photo)) {
          $form->photo->setAttrib('accept', "image/*");
        }
      }
    }

    // Populate with categories
    $categories = Engine_Api::_()->getDbtable('categories', 'sitepageevent')->getCategoriesAssoc();
    asort($categories, SORT_LOCALE_STRING);
    $categoryOptions = array('0' => '');
    foreach ($categories as $k => $v) {
      $categoryOptions[$k] = $v;
    }
    if (sizeof($categoryOptions) <= 1) {
      $form->removeElement('category_id');
    } else {
      $form->category_id->setMultiOptions($categoryOptions);
    }

    //GET SITEPAGE ITEM    
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK
    Engine_Api::_()->sitepageevent()->setEventPackages();
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //EVENT OWNER, PAGE OWNER AND MANAGE ADMIN CAN EDIT EVENT
    if ($viewer->getIdentity() != $sitepageevent->user_id && $can_edit != 1) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$this->getRequest()->isPost()) {
      $auth = Engine_Api::_()->authorization()->context;
      $pagemember = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
      if (empty($pagemember)) {
        $form->auth_invite->setValue($auth->isAllowed($sitepageevent, 'member', 'invite'));
      }

      //POPULATE
      $form->populate($sitepageevent->toArray());

      //CONVERT AND RE-POPULATE TIMES
      $start = strtotime($sitepageevent->starttime);
      $end = strtotime($sitepageevent->endtime);
      $oldTz = date_default_timezone_get();
      date_default_timezone_set($viewer->timezone);
      $start = date('Y-m-d H:i:s', $start);
      $end = date('Y-m-d H:i:s', $end);
      date_default_timezone_set($oldTz);
      $form->populate(array(
          'starttime' => $start,
          'endtime' => $end,
      ));
      return;
    }

    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      $tempPost = $this->getRequest()->getPost();
      if (isset($tempPost['photo']))
        $form->removeElement('photo');
    }
    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //GET FORM VALUES
    $values = $form->getValues();

    //CONVERT TIMES
    $oldTz = date_default_timezone_get();
    date_default_timezone_set($viewer->timezone);
    $start = strtotime($values['starttime']);
    $end = strtotime($values['endtime']);
    date_default_timezone_set($oldTz);
    $values['starttime'] = date('Y-m-d H:i:s', $start);
    $values['endtime'] = date('Y-m-d H:i:s', $end);

    //PROCESS
    $db = Engine_Api::_()->getItemTable('sitepageevent_event')->getAdapter();
    $db->beginTransaction();

    try {
      //SET EVENT INFORMATION
      $sitepageevent->setFromArray($values);
      $sitepageevent->save();

      $auth = Engine_Api::_()->authorization()->context;
      if (isset($values['auth_invite']))
        $auth->setAllowed($sitepageevent, 'member', 'invite', $values['auth_invite']);

      //ADD PHOTO
      if (!empty($values['photo'])) {
        $sitepageevent->setPhoto($form->photo);
      }

      //COMMIT
      $db->commit();
    } catch (Engine_Image_Exception $e) {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //REDIRECTING TO THE EVENT VIEW PAGE
    return $this->_redirectCustom($sitepageevent->getHref(), array('prependBase' => false));
  }

  //ACTION FOR VIEW THE EVENT
  public function viewAction() {

    //IF SITEPAGEEVENT SUBJECT IS NOT THEN RETURN
    if (!$this->_helper->requireSubject('sitepageevent_event')->isValid())
      return;

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();

    //GET EVENT ITEM
    $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', $this->getRequest()->getParam('event_id'));

    //GET SITEPAGE ITEM
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepageevent->page_id);

    //INCLUDE CSS FILE
    $this->view->headLink()
            ->appendStylesheet($this->view->baseUrl()
                    . '/application/modules/Sitepageevent/externals/styles/style_sitepageevent.css');

    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.common.css') && (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup'))) {
      $this->view->headLink()->appendStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/common_style_page_business_group.css');
    } elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.common.css') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness')) {
      $this->view->headLink()->appendStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/common_style_page_business.css');
    } elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.common.css') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup')) {
      $this->view->headLink()->appendStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/common_style_page_group.css');
    } else {
      $this->view->headLink()->appendStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/style_sitepage.css');
    }

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageevent")) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'secreate');
      if (empty($isPageOwnerAllow)) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }
    //PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'secreate');
    if (empty($isManageAdmin)) {
      $this->view->can_create = 0;
    } else {
      $this->view->can_create = 1;
    }

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //CHECKING THE USER HAVE THE PERMISSION TO VIEW THE EVENT OR NOT
    if ($viewer_id != $sitepageevent->user_id && $can_edit != 1 && $sitepageevent->search != 1) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //INCREMENT IN NUMBER OF VIEWS
    $owner = $sitepageevent->getOwner();
    if (!$owner->isSelf($viewer)) {
      $sitepageevent->view_count++;
    }

    //SAVE VALUES
    $sitepageevent->save();

    //NAVIGATION WORK FOR FOOTER.(DO NOT DISPLAY NAVIGATION IN FOOTER ON VIEW PAGE.)
    if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
      if (!Zend_Registry::isRegistered('sitemobileNavigationName')) {
        Zend_Registry::set('sitemobileNavigationName', 'setNoRender');
      }
    }

    //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;

    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }
  }

  //ACTION FOR DELETE EVENT
  public function deleteAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();

    //PAGE ID
    $page_id = $this->_getParam('page_id');

    //SEND TAB ID TO TPL FILE
    $this->view->tab_selected_id = $this->_getParam('tab_id');

    //GET SITEPAGE ITEM
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main');

    //GET EVENT ITEM
    $this->view->sitepageevent = $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', $this->_getParam('event_id'));

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($isManageAdmin)) {
      $can_edit = 0;
    } else {
      $can_edit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //EVENT OWNER AND PAGE OWNER CAN DELETE EVENT
    if ($viewer->getIdentity() != $sitepageevent->user_id && $can_edit != 1) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }

    //IF NOT POST OR FORM NOT VALID
    if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {
      //DELETE EVENT, ALBUM AND EVENT IMAGES
      Engine_Api::_()->sitepageevent()->deleteContent($this->_getParam('event_id'));

      //REDIRECTING TO THE SITEPAGE VIEW PAGE
      return $this->_gotoRouteCustom(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($page_id), 'tab' => $this->view->tab_selected_id), 'sitepage_entry_view', true);
    }
  }

  //ACTION FOR INVITE THE PEOPLE FOR EVENT
  public function inviteAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //IF SITEPAGEEVENT SUBJECT IS NOT THEN RETURN
    if (!$this->_helper->requireSubject('sitepageevent_event')->isValid())
      return;

    //PREPARE DATA
    $viewer = Engine_Api::_()->user()->getViewer();
    $sitepageevent = Engine_Api::_()->core()->getSubject();
    $friends = $viewer->membership()->getMembers();

    //PREPARE FORM
    $this->view->form = $form = new Sitepageevent_Form_Invite();

    $count = 0;
    foreach ($friends as $friend) {
      if ($sitepageevent->membership()->isMember($friend, null)) {
        continue;
      }
      $form->users->addMultiOption($friend->getIdentity(), $friend->getTitle());
      $count++;
    }
    $this->view->count = $count;

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$this->getRequest()->isPost()) {
      return;
    }

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //PROCESS
    $db = $sitepageevent->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      $usersIds = $form->getValue('users');
      foreach ($friends as $friend) {
        if (!in_array($friend->getIdentity(), $usersIds)) {
          continue;
        }
        $sitepageevent->membership()->addMember($friend)->setResourceApproved($friend);

        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($friend, $viewer, $sitepageevent, 'sitepageevent_invite');
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //SHOWING THE SUCCESS MESSAGE
    return $this->_forwardCustom('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Members invited.')),
                'layout' => 'default-simple',
                'parentRefresh' => 10,
                'smoothboxClose' => 10
    ));
  }

  //ACTION FOR JOIN THE EVENT
  public function joinAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //IF SITEPAGEEVENT SUBJECT IS NOT THEN RETURN  
    if (!$this->_helper->requireSubject()->isValid())
      return;

    //MAKE FORM
    $this->view->form = $form = new Sitepageevent_Form_Join();

    //PROCESS FORM
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET LOGGED IN USER INFORMATION
      $viewer = Engine_Api::_()->user()->getViewer();

      //GETTING THE SITEPAGEEVENT SUBJECT
      $sitepageevent_subject = Engine_Api::_()->core()->getSubject();

      //START: SUGGESTION WORK
      $is_suggestion_enabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
      if (!empty($is_suggestion_enabled)) {
        $is_moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage');
        //HERE WE ARE DELETE THIS EVENT SUGGESTION IF VIEWER HAVE.
        if (!empty($is_moduleEnabled)) {
          Engine_Api::_()->getApi('suggestion', 'sitepage')->deleteSuggestion($viewer->getIdentity(), 'page_event', $sitepageevent_subject->event_id, 'page_event_suggestion');
        }
      }
      //END: SUGGESTION WORK
      //PROCESS
      $db = $sitepageevent_subject->membership()->getReceiver()->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        //SAVE VALUES
        $sitepageevent_subject->membership()
                ->addMember($viewer)
                ->setUserApproved($viewer);

        $row = $sitepageevent_subject->membership()
                ->getRow($viewer);

        $row->rsvp = $form->getValue('rsvp');
        $row->save();

        //GET SITEPAGE ITEM
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepageevent_subject->page_id);

        //MAKING THE LINK OF THE PAGE
//         $linked_page_title = '<a href = http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($sitepageevent_subject->page_id)), 'sitepage_entry_view') . "><b>$sitepage->title</b></a>";
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $linked_page_title = '<b>' . $view->htmlLink($sitepage->getHref(), $sitepage->getTitle(), array('target' => '_parent')) . '</b>';
        $api = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $api->addActivity($viewer, $sitepageevent_subject, 'sitepageevent_join', null, array('linked_page_title' => $linked_page_title));

        //MAKE SURE ACTION EXISTS BEFOR ATTACHING THE EVENT TO THE ACTIVITY
        if ($action != null) {
          Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $sitepageevent_subject);
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      //SHOWING THE SUCCESS MESSAGE
      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Page event joined.')),
                  'layout' => 'default-simple',
                  'parentRefresh' => 300,
                  'smoothboxClose' => 300
      ));
    }
  }

  //ACTION FOR REQUEST FOR EVENT
  public function requestAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //IF SITEPAGEEVENT SUBJECT IS NOT THEN RETURN  
    if (!$this->_helper->requireSubject()->isValid())
      return;

    //MAKE FORM
    $this->view->form = $form = new Sitepageevent_Form_Request();

    //PROCESS FORM
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //GET LOGGED IN USER INFORMATION
      $viewer = Engine_Api::_()->user()->getViewer();

      //GETTING THE SITEPAGEEVENT SUBJECT
      $sitepageevent_subject = Engine_Api::_()->core()->getSubject();

      //PROCESS
      $db = $sitepageevent_subject->membership()->getReceiver()->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        //SAVE VALUES
        $sitepageevent_subject->membership()->addMember($viewer)->setUserApproved($viewer);
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      //SHOWING THE SUCCESS MESSAGE
      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your invite request has been sent.')),
                  'layout' => 'default-simple',
                  'parentRefresh' => 300,
                  'smoothboxClose' => 300
      ));
    }
  }

  //ACTION FOR CANCEL THE EVENT
  public function cancelAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //IF SITEPAGEEVENT SUBJECT IS NOT THEN RETURN  
    if (!$this->_helper->requireSubject()->isValid())
      return;

    //MAKE FORM
    $this->view->form = $form = new Sitepageevent_Form_Cancel();

    //PROCESS FORM
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //GET USER ID
      $user_id = $this->getRequest()->getParam('user_id');

      //GET USER ITEM
      $user = Engine_Api::_()->getItem('user', $user_id);

      //GETTING THE SITEPAGEEVENT SUBJECT
      $sitepageevent_subject = Engine_Api::_()->core()->getSubject();

      //PROCESS
      $db = $sitepageevent_subject->membership()->getReceiver()->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        //SAVE VALUES
        $sitepageevent_subject->membership()->removeMember($user);
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      //SHOWING THE SUCCESS MESSAGE
      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your invite request has been cancelled.')),
                  'layout' => 'default-simple',
                  'parentRefresh' => 300,
                  'smoothboxClose' => 300
      ));
    }
  }

  //ACTION FOR LEAVE THE EVENT
  public function leaveAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //IF SITEPAGEEVENT SUBJECT IS NOT THEN RETURN    
    if (!$this->_helper->requireSubject()->isValid())
      return;

    //MAKE FORM
    $this->view->form = $form = new Sitepageevent_Form_Leave();

    //IF NOT POST OR FORM NOT VALID
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //GETTING THE SITEPAGEEVENT SUBJECT
      $sitepageevent_subject = Engine_Api::_()->core()->getSubject();

      //PROCESS
      $db = $sitepageevent_subject->membership()->getReceiver()->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        //SAVE VALUES
        $sitepageevent_subject->membership()->removeMember(Engine_Api::_()->user()->getViewer());
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      //SHOWING THE SUCCESS MESSAGE
      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Page event left.')),
                  'layout' => 'default-simple',
                  'parentRefresh' => 300,
                  'smoothboxClose' => 300
      ));
    }
  }

  //ACTION FOR ACCEPT THE EVENT REQUEST
  public function acceptAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //IF SITEPAGEEVENT SUBJECT IS NOT THEN RETURN    
    if (!$this->_helper->requireSubject()->isValid())
      return;

    //MAKE FORM
    $this->view->form = $form = new Sitepageevent_Form_Join();

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Method');
      return;
    }

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
      return;
    }

    //GET LOGGED IN USER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();

    //GETTING THE SITEPAGEEVENT SUBJECT
    $sitepageevent_subject = Engine_Api::_()->core()->getSubject();

    //PROCESS 
    $db = $sitepageevent_subject->membership()->getReceiver()->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      //SAVE VALUES
      $sitepageevent_subject->membership()->setUserApproved($viewer);
      $row = $sitepageevent_subject->membership()
              ->getRow($viewer);
      $row->rsvp = $form->getValue('rsvp');
      $row->save();

      //SET THE REQUEST AS HANDLED
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
              $viewer, $sitepageevent_subject, 'sitepageevent_invite');
      if ($notification) {
        $notification->mitigated = true;
        $notification->save();
      }

      //GET SITEPAGE ITEM
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepageevent_subject->page_id);

      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
      $linked_page_title = '<b>' . $view->htmlLink($sitepage->getHref(), $sitepage->getTitle(), array('target' => '_parent')) . '</b>';

      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $api->addActivity($viewer, $sitepageevent_subject, 'sitepageevent_join', null, array('linked_page_title' => $linked_page_title));

      //MAKE SURE ACTION EXISTS BEFOR ATTACHING THE EVENT TO THE ACTIVITY
      if ($action != null) {
        Engine_Api::_()->getApi('subCore', 'sitepage')->deleteFeedStream($action);
        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $sitepageevent_subject);
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->view->status = true;
    $this->view->error = false;
    $this->view->message = sprintf(Zend_Registry::get('Zend_Translate')->_('You have accepted the invite to the page event %s.'), $sitepageevent_subject->__toString());

    //SHOWING THE SUCCESS MESSAGE
    return $this->_forwardCustom('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Page event invite accepted.')),
                'layout' => 'default-simple',
                'parentRefresh' => 300,
                'smoothboxClose' => 300
    ));
  }

  //ACTION FOR REJECT THE EVENT REQUEST
  public function rejectAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //IF SITEPAGEEVENT SUBJECT IS NOT THEN RETURN    
    if (!$this->_helper->requireSubject()->isValid())
      return;

    //MAKE FORM
    $this->view->form = $form = new Sitepageevent_Form_Reject();

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Method');
      return;
    }

    //IF NOT POST OR FORM NOT VALID, RETURN
    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->view->status = false;
      $this->view->error = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid Data');
      return;
    }

    //GET USER
    if (0 === ($user_id = (int) $this->_getParam('user_id')) ||
            null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
      return $this->_helper->requireSubject->forward();
    }

    //GETTING THE SITEPAGEEVENT SUBJECT
    $sitepageevent_subject = Engine_Api::_()->core()->getSubject();

    //PROCESS
    $db = $sitepageevent_subject->membership()->getReceiver()->getTable()->getAdapter();
    $db->beginTransaction();
    try {
      //SAVE VALUES
      $sitepageevent_subject->membership()->removeMember($user);

      //SET THE REQUEST AS HANDLED
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationByObjectAndType(
              $user, $sitepageevent_subject, 'sitepageevent_invite');
      if ($notification) {
        $notification->mitigated = true;
        $notification->save();
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->view->status = true;
    $this->view->error = false;
    $this->view->message = sprintf(Zend_Registry::get('Zend_Translate')->_('You have ignored the invite to the page event %s.'), $sitepageevent_subject->__toString());

    //SHOWING THE SUCCESS MESSAGE
    return $this->_forwardCustom('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Page event invite rejected.')),
                'layout' => 'default-simple',
                'parentRefresh' => 300,
                'smoothboxClose' => 300
    ));
  }

  //ACTION FOR REMOVE THE MAMBER FROM THE EVENT
  public function removeAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //IF SITEPAGEEVENT SUBJECT IS NOT THEN RETURN    
    if (!$this->_helper->requireSubject()->isValid())
      return;

    //GET USER
    if (0 === ($user_id = (int) $this->_getParam('user_id')) ||
            null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
      return $this->_helper->requireSubject->forward();
    }

    //GET SITEPAGEEVENT SUBJECT
    $sitepageevent_subject = Engine_Api::_()->core()->getSubject();

    if (!$sitepageevent_subject->membership()->isMember($user)) {
      $erroe_msg1 = Zend_Registry::get('Zend_Translate')->_('Cannot remove a non-member');
      throw new Event_Model_Exception($erroe_msg1);
    }

    //MAKE FORM
    $this->view->form = $form = new Sitepageevent_Form_Remove();

    //IF NOT POST OR FORM NOT VALID
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //PROCESS
      $db = $sitepageevent_subject->membership()->getReceiver()->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        //REMOVE MEMBERSHIP
        $sitepageevent_subject->membership()->removeMember($user);
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      //SHOWING THE SUCCESS MESSAGE
      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Page event member removed.')),
                  'layout' => 'default-simple',
                  'parentRefresh' => 300,
                  'smoothboxClose' => 300
      ));
    }
  }

  //ACTION FOR APPROVE THE EVENT
  public function approveAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //IF SITEPAGEEVENT SUBJECT IS NOT THEN RETURN    
    if (!$this->_helper->requireSubject()->isValid())
      return;

    //GET USER
    if (0 === ($user_id = (int) $this->_getParam('user_id')) ||
            null === ($user = Engine_Api::_()->getItem('user', $user_id))) {
      return $this->_helper->requireSubject->forward();
    }

    //MAKE FORM
    $this->view->form = $form = new Sitepageevent_Form_Approve();

    //IF NOT POST OR FORM NOT VALID
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //GET LOGGED IN USER INFORMATION
      $viewer = Engine_Api::_()->user()->getViewer();

      //GETTING THE SITEPAGEEVENT SUBJECT
      $sitepageevent_subject = Engine_Api::_()->core()->getSubject();

      //PROCESS
      $db = $sitepageevent_subject->membership()->getReceiver()->getTable()->getAdapter();
      $db->beginTransaction();
      try {
        //SAVE VALUES
        $sitepageevent_subject->membership()->setResourceApproved($user);
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $sitepageevent_subject, $viewer, 'sitepageevent_accepted');
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      //SHOWING THE SUCCESS MESSAGE
      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Page event request approved.')),
                  'layout' => 'default-simple',
                  'parentRefresh' => 300,
                  'smoothboxClose' => 300
      ));
    }
  }

  public function browseAction() {

    //CHECK VIEW PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'view')->isValid())
      return;

    //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }
  }

  //ACTION FOR MAKE THE SITEPAGEEVENT FEATURED/UNFEATURED
  public function featuredAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET EVENT ID AND OBJECT
    $event_id = $this->view->event_id = $this->_getParam('event_id');
    $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', $event_id);

    $this->view->featured = $sitepageevent->featured;

    //GET PAGE OBJECT
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepageevent->page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    $this->view->canEdit = 0;
    if (!empty($isManageAdmin)) {
      $this->view->canEdit = 1;
    }
    //END MANAGE-ADMIN CHECK
    //SMOOTHBOX
    if (null === $this->_helper->ajaxContext->getCurrentContext()) {
      $this->_helper->layout->setLayout('default-simple');
    } else {//NO LAYOUT
      $this->_helper->layout->disableLayout(true);
    }

    if (!$this->getRequest()->isPost())
      return;

    //GET VIEWER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');

    //CHECK THAT FEATURED ACTION IS ALLOWED BY ADMIN OR NOT
    //CHECK CAN MAKE FEATURED OR NOT(ONLY SITEPAGE EVENT CAN MAKE FEATURED/UN-FEATURED)
    if ($viewer_id == $sitepageevent->user_id || !empty($this->view->canEdit)) {
      $this->view->permission = true;
      $this->view->success = false;
      $db = Engine_Api::_()->getDbtable('events', 'sitepageevent')->getAdapter();
      $db->beginTransaction();
      try {
        if ($sitepageevent->featured == 0) {
          $sitepageevent->featured = 1;
        } else {
          $sitepageevent->featured = 0;
        }

        $sitepageevent->save();
        $db->commit();
        $this->view->success = true;
      } catch (Exception $e) {
        $db->rollback();
        throw $e;
      }
    } else {
      $this->view->permission = false;
    }

    if ($sitepageevent->featured) {
      $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Event successfully made featured.'));
    } else {
      $suc_msg = array(Zend_Registry::get('Zend_Translate')->_('Event successfully made un-featured.'));
    }

    $this->_forwardCustom('success', 'utility', 'core', array(
        'smoothboxClose' => 2,
        'parentRedirect' => $this->_helper->url->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($sitepageevent->page_id), 'tab' => $tab_selected_id), 'sitepage_entry_view', true),
        'parentRedirectTime' => '2',
        'format' => 'smoothbox',
        'messages' => $suc_msg
    ));
  }

  //ACTION FOR ADDING EVENT OF THE DAY
  public function addEventOfDayAction() {
    //FORM GENERATION
    $form = $this->view->form = new Sitepageevent_Form_ItemOfDayday();
    $event_id = $this->_getParam('event_id');
    // $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();

      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //GET ITEM OF THE DAY TABLE
        $dayItemTime = Engine_Api::_()->getDbtable('itemofthedays', 'sitepage');

        //FETCH RESULT FOR resource_id
        $select = $dayItemTime->select()->where('resource_id = ?', $event_id)->where('resource_type = ?', 'sitepageevent_event');
        $row = $dayItemTime->fetchRow($select);

        if (empty($row)) {
          $row = $dayItemTime->createRow();
          $row->resource_id = $event_id;
        }
        $row->start_date = $values["starttime"];
        $row->end_date = $values["endtime"];
        $row->resource_type = 'sitepageevent_event';
        $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forwardCustom('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Event of the Day has been added successfully.'))
      ));
    }
  }

  // ACTION FOR FEATURED EVENTS CAROUSEL AFTER CLICK ON BUTTON 
  public function featuredEventsCarouselAction() {
    //RETRIVE THE VALUE OF ITEM VISIBLE
    $this->view->itemsVisible = $limit = (int) $_GET['itemsVisible'];

    //RETRIVE THE VALUE OF NUMBER OF ROW
    $this->view->noOfRow = (int) $_GET['noOfRow'];
    //RETRIVE THE VALUE OF ITEM VISIBLE IN ONE ROW
    $this->view->inOneRow = (int) $_GET['inOneRow'];

    // Total Count Featured Photos
    $totalCount = (int) $_GET['totalItem'];

    //RETRIVE THE VALUE OF START INDEX
    $startindex = $_GET['startindex'] * $limit;

    if ($startindex > $totalCount) {
      $startindex = $totalCount - $limit;
    }
    if ($startindex < 0)
      $startindex = 0;

    $params = array();
    $params['category_id'] = $_GET['category_id'];
    $params['feature_events'] = 1;

    //RETRIVE THE VALUE OF BUTTON DIRECTION
    $direction = $_GET['direction'];
    $this->view->offset = $params['start_index'] = $startindex;

    //GET Featured Photos with limit * 2
    $this->view->totalItemsInSlide = $params['limit'] = $limit * 2;
    $this->view->featuredEvents = $this->view->featuredEvents = $featuredEvents = Engine_Api::_()->getDbTable('events', 'sitepageevent')->widgetEventsData($params);

    //Pass the total number of result in tpl file
    $this->view->count = count($featuredEvents);

    //Pass the direction of button in tpl file
    $this->view->direction = $direction;
  }

  public function homeAction() {

    //CHECK VIEW PRIVACY
    if (!$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'view')->isValid())
      return;

    //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }
  }

  //ACTION FOR BROWSE LOCATION PAGES.
  public function byLocationsAction() {
    $this->_helper->content->setEnabled();
  }

  //ACTION FOR BROWSE LOCATION PAGES.
  public function mobilebyLocationsAction() {
    $this->_helper->content->setEnabled();
  }

  public function inviteMembersAction() {

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //IF SITEPAGEEVENT SUBJECT IS NOT THEN RETURN
    if (!$this->_helper->requireSubject('sitepageevent_event')->isValid())
      return;

    $page_id = $this->_getParam('page_id');
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    $event_id = $this->_getParam('event_id');
    $SUBJECT = $sitepageevent = Engine_Api::_()->getItem('sitepageevent_event', $event_id);
    $this->view->pagevent_title = $SUBJECT->title;
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    $modContentId = $this->_getParam("modContentId", null);

    $eventMembershipTable = Engine_Api::_()->getDbtable('membership', 'sitepageevent');
    $this->view->user_ids = $eventMembershipTable->select()
                    ->from($eventMembershipTable->info('name'), array('user_id'))
                    ->where('resource_id = ?', $event_id)
                    ->query()->fetchAll(Zend_Db::FETCH_COLUMN);

    //$this->view->modError = $modSetError = 1; //$this->_getParam("modError", 0);
    //$this->view->modItemType = $modItemType = null; //$this->_getParam("modItemType", null);
    //$this->view->notificationType = $notificationType = 0; //$this->_getParam("notificationType", 0);
    //$findFriendFunName = null; //$this->_getParam("findFriendFunName", null);
    // 	if (empty($findFriendFunName)) {
    // 	  $findFriendFunName = $notificationType;
    // 	}
    // 	if (empty($findFriendFunName)) {
    // 	  $findFriendFunName = $_GET['findFriendFunName'];
    // 	}
    //$this->view->findFriendFunName = null;
    //$mod_notify_type = null;
    //$mod_entity = $this->_getParam("entity", null);
    //$item_type = $this->_getParam("item_type", null);
// 		if (empty($mod_notify_type) && !empty($_GET['notification_type'])) {
// 			$mod_notify_type = $_GET['notification_type'];
// 		}
// 		if (empty($mod_entity) && !empty($_GET['entity'])) {
// 			$mod_entity = $_GET['entity'];
// 		}
// 		if (empty($item_type) && !empty($_GET['item_type'])) {
// 			$item_type = $_GET['item_type'];
// 		}
    //$this->view->notification_type = $mod_notify_type;
    //$this->view->entity = $mod_entity;
    //$this->view->item_type = $item_type;
    //$this->view->mod_set_error = $modSetError;
    $this->view->search_true = false;
    $entityId = array();
    if ($this->getRequest()->isPost()) {
      // Send suggestion of the friends, which loggden user select in popup.
      $userFriendArray = $this->getRequest()->getPost();
      foreach ($userFriendArray as $flag => $ownerId) {
        if (strpos($flag, 'check_') !== FALSE) {
          $entity = $userFriendArray['entity'];
          $entityId[] = $ownerId;
          $friend = Engine_Api::_()->user()->getUser($ownerId);
          $SUBJECT->membership()->addMember($friend)->setResourceApproved($friend);
        }
      }
      if (!empty($entityId)) {
        Engine_Api::_()->sitepageevent()->sendInviteEmail($sitepageevent, $action, 'sitepageevent_invite', 'SITEPAGEEVENT_INVITE_EMAIL', 'InviteMembers', $entityId);
      }

      $this->_forwardCustom('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'messages' => array($this->view->translate("Members invited."))
              )
      );
    } else {
      // Set variables for JS. when open popups.
      $this->view->modContentId = $event_id;
      //$modFunName = null;
      $modParem = 'modContentId';
      if (!empty($_GET['task'])) {
        $getTask = $_GET['task'];
      }
      if (!empty($_GET['selected_checkbox'])) {
        $getSelectCheckbox = $_GET['selected_checkbox'];
      }
      if (!empty($_GET['page'])) {
        $getPage = $_GET['page'];
      }
      if (!empty($_GET['searchs'])) {
        $getSearch = $_GET['searchs'];
      }
      if (!empty($_GET['show_selected'])) {
        $getShowSelected = $_GET['show_selected'];
      }
      if (!empty($_GET['action_id'])) {
        $getActionId = $_GET['action_id'];
      }
      if (!empty($_GET['selected_friend_flag'])) {
        $this->view->selectedFriendFlag = $_GET['selected_friend_flag'];
      }
      // Assign variables for resolving log error.
      if (empty($getTask)) {
        $getTask = null;
      }
      if (empty($getSelectCheckbox)) {
        $getSelectCheckbox = null;
      }
      if (empty($getPage)) {
        $getPage = null;
      }
      if (empty($getSearch)) {
        $getSearch = null;
      }
      if (empty($getShowSelected)) {
        $getShowSelected = null;
      }
      if (empty($getActionId)) {
        $getActionId = null;
      }
      $veiw = $this->openPopupContent($getTask, $getSelectCheckbox, $getPage, $getSearch, $getShowSelected, $getActionId, $modContentId, $modParem, $sitepageevent, $viewer_id);

      foreach ($veiw as $key => $value) {
        $this->view->$key = $value;
      }
    }
  }

  public function openPopupContent($getTask, $getSelectCheckbox, $getPage, $getSearch, $getShowSelected, $getActionId, $modId, $modParem, $sitepageevent, $viewer_id) {

    $page_id = $sitepageevent->page_id;
    $event_id = $sitepageevent->event_id;
    $ownerId = $sitepageevent->user_id;
    //THIS IS WHEN DO SOME ACTIVITY ON THE SUGGESTION PAGE.
    if (!empty($getTask)) {
      $view['search_true'] = true;
    }

    if (!empty($getSelectCheckbox)) {
      $view['selected_checkbox'] = $getSelectCheckbox;
      $getSelectCheckbox = trim($getSelectCheckbox, ',');
      $modStrId_array = explode(",", $getSelectCheckbox);
      $view['friends_count'] = @COUNT($modStrId_array);
    } else {
      $view['selected_checkbox'] = '';
      $view['friends_count'] = $selected_friend_count = 0;
    }

    $view['page'] = $page = !empty($getPage) ? $getPage : 1;
    $view['search'] = $search = !empty($getSearch) ? $getSearch : '';


    //IF THE REQUEST IS FOR SHOWING ONLY SELECTED FRIENDS.
    if (!empty($getShowSelected)) {
      $search = '';
      $view['show_selected'] = $selected_friend_show = 1;
      $view[$modParem] = $modId = $getActionId;
      $modId_array = $modStrId_array;
    }
    //IF THE REQUEST IS FOR SHOWING ALL FRIENDS.
    else {
      if (empty($modId)) {
        $view[$modParem] = $modId = $getActionId;
      }
      //$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      //$sugg_popups_limit = 0;
      //  would be 'friend' only when "Add a Friend popup" & "Few friend popup"
      $view['members'] = $fetch_member_myfriend = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinMembers($page_id, $viewer_id, $ownerId);

      if (!empty($page)) {
        $fetch_member_myfriend->setCurrentPageNumber($page);
      }
      $fetch_member_myfriend->setItemCountPerPage(40);

      $modId_array = array();

      foreach ($fetch_member_myfriend as $modRow) {
        $modId_array[] = $modRow['user_id'];
      }
      $view['show_selected'] = $selected_friend_show = 0;
    }
    			$inviteMemberArray = Engine_Api::_()->getDbtable('membership', 'sitepageevent')->getInvitedMembers($event_id, $viewer_id, $ownerId); 

			foreach ($inviteMemberArray as $inviteMember) {
				$inviteMember_array[] = $inviteMember['user_id'];
			}
			
			if($modId_array && $inviteMember_array)
				$result = array_diff($modId_array, $inviteMember_array);
				
			if($result) 
				$view['mod_combind_path'] = $result;
			elseif(empty($inviteMember_array))
				$view['mod_combind_path'] = $modId_array;

    //HERE WE ARE CHECKING IF THE REQUEST IS FOR ONLY SHOW SELECTED FRIENDS THEN WE WILL MAKE PAGINATION OF USER OBJECT OTHERWISE WE WILL SIMPLY USER FETCHALL QUERY.
    if (!empty($modId_array)) {

// 			$tempSelectedFriend = '';
// 			if (!empty($modId_array) && !empty($modStrId_array)) {
// 				foreach ($modId_array as $values) {
// 					if (in_array($values, $modStrId_array)) {
// 					$tempSelectedFriend .= ',' . $values;
// 					}
// 				}
// 			}
      //$view['tempSelectedFriend'] = $tempSelectedFriend;
      

			if($result) 
				$view['suggest_user_id'] = $result;
			else
				$view['suggest_user_id'] = $modId_array;


      if ($selected_friend_show) {
        $view['suggest_user'] = $view['members'] = $selected_friends = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinMembers($page_id, $viewer_id, $ownerId);
        $selected_friends->setCurrentPageNumber($page);
        $selected_friends->setItemCountPerPage(100);
      } else {
        //$view['suggest_user'] = $memberArray = Engine_Api::_()->suggestion()->suggestion_users_information($modId_array, $selected_friend_show, '');
        $memberArray = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinMembers($page_id, $viewer_id, $ownerId); 
        $memberArray->setCurrentPageNumber($page);
        $memberArray->setItemCountPerPage(100);

				foreach ($memberArray as $joinMember) {
					$joinMembers[] = $joinMember['user_id'];
				}

							
				if($joinMembers && $inviteMember_array)
					$result = array_diff($joinMembers, $inviteMember_array); 
					
				if($result) 
					$view['suggest_user'] = $result;
				elseif(empty($inviteMember_array))
					$view['suggest_user'] = $joinMembers;
      }
    }

    if (!empty($_GET['getArray'])) {
      $view['getArray'] = $_GET['getArray'];
    } else {
      $view['getArray'] = array();
    }
    return $view;
  }
  
  public function pageEventsAction() {
    if(!Engine_Api::_()->core()->hasSubject()) {
      return $this->_helper->content->setNoRender();
    }
    
    $data = Engine_Api::_()->sitepageevent()->getCircleCalendarEventsData();

    return $this->_helper->json($data);
  }
}