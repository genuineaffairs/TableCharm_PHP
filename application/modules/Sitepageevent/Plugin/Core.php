<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 6590 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_Plugin_Core extends Zend_Controller_Plugin_Abstract {
	
   public function routeShutdown(Zend_Controller_Request_Abstract $request) {

    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('mobi'))
      return;

    //CHECK IF ADMIN
    if (substr($request->getPathInfo(), 1, 5) == "admin") {
      return;
    }

    $mobile = $request->getParam("mobile");
    $session = new Zend_Session_Namespace('mobile');

    if ($mobile == "1") {
      $mobile = true;
      $session->mobile = true;
    } elseif ($mobile == "0") {
      $mobile = false;
      $session->mobile = false;
    } else {
      if (isset($session->mobile)) {
        $mobile = $session->mobile;
      } else {
        //CHECK TO SEE IF MOBILE
        if (Engine_Api::_()->mobi()->isMobile()) {
          $mobile = true;
          $session->mobile = true;
        } else {
          $mobile = false;
          $session->mobile = false;
        }
      }
    }

    if (!$mobile) {
      return;
    }
    $module = $request->getModuleName();
    $controller = $request->getControllerName();
    $action = $request->getActionName();
    if ($module == "sitepageevent") {

      if ($controller == "index" && $action == "view") {

        $request->setControllerName('mobi');
        $request->setActionName('view');
      }
    }

    //CREATE LAYOUT
    $layout = Zend_Layout::startMvc();

    //SET OPTIONS
    $layout->setViewBasePath(APPLICATION_PATH . "/application/modules/Mobi/layouts", 'Core_Layout_View')
        ->setViewSuffix('tpl')
        ->setLayout(null);

  }
  
  //DELETE USERS BELONGINGS BEFORE THAT USER DELETION
  public function onUserDeleteBefore($sitepageevent) {
    
    $payload = $sitepageevent->getPayload();
    if ($payload instanceof User_Model_User) {
      //DELETE MEMBERSHIPS
      $membershipApi = Engine_Api::_()->getDbtable('membership', 'sitepageevent');
      foreach ($membershipApi->getMembershipsOf($payload) as $sitepageevent) {
        $membershipApi->removeMember($sitepageevent, $payload);
      }

      //DELETE EVENT
      $tableEvent = Engine_Api::_()->getDbTable('events', 'sitepageevent');
      $select = $tableEvent->select()->where('user_id = ?', $payload->getIdentity());
      foreach ($tableEvent->fetchAll($select) as $sitepageevent) {
        //DELETE ALBUM AND IMAGE
        Engine_Api::_()->sitepageevent()->deleteContent($sitepageevent->event_id);
      }
    }
  }

  //ATTACH THE ACTIVITY FEED
  public function addActivity($sitepageevent) {
    
    $payload = $sitepageevent->getPayload();
    $subject = $payload['subject'];
    $object = $payload['object'];
    if ($object instanceof Sitepageevent_Model_Sitepageevent &&
            Engine_Api::_()->authorization()->context->isAllowed($object, 'member', 'view')) {
      $sitepageevent->addResponse(array(
          'type' => 'sitepageevent',
          'identity' => $object->getIdentity()
      ));
    }
  }
  
  public function onSitepageeventEventCreateAfter($event) {

    $item = $event->getPayload();
    $front = Zend_Controller_Front::getInstance();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();
    
    if ($controller == 'index' && $action == 'create') {
			//Accrodeing to event  location entry in the seaocore location table.
			if (!empty($item->location)) {
				$seao_locationid = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($item->location, '', 'sitepageevent_event', $item->event_id);

				//event table entry of location id.
				Engine_Api::_()->getDbtable('events', 'sitepageevent')->update(array('seao_locationid'=>  $seao_locationid), array('event_id =?' => $item->event_id));
			}
		}
  }

	public function onSitepageeventEventUpdateAfter($event) {

    $item = $event->getPayload();
    $front = Zend_Controller_Front::getInstance();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();
    
    if ($controller == 'index' && $action == 'edit') {
			if (!empty($item->location)) {
			
				//DELETE THE RESULT FORM THE TABLE.
				Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $item->event_id, 'resource_type = ?' => 'sitepageevent_event'));
			
				$seaoLocation = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($item->location, '', 'sitepageevent_event', $item->event_id);

				//event table entry of location id.
				Engine_Api::_()->getDbtable('events', 'sitepageevent')->update(array('seao_locationid'=>  $seaoLocation), array('event_id =?' => $item->event_id));
			}
	  }
	}
}