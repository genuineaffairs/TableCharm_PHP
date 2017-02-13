<?php

/**
 * Hooks for API and mobile mode
 * 
 * @author Chips Invincible <gachip11589@gmail.com> :))
 */
class Mgslapi_Plugin_Core extends Zend_Controller_Plugin_Abstract {

  public function onActivityNotificationCreateAfter($event) {
    $item = $event->getPayload();
    $currentModuleCoreApi = Engine_Api::_()->getApi('core', 'mgslapi');
    $notificationInfoArray = $currentModuleCoreApi->getNotificationsHtml($item);
    $table = Engine_Api::_()->getDbtable('devices', $currentModuleCoreApi->getModuleName());

    // common API helper
    $helper = new Mgslapi_Controller_Action_Helper_CommonAPI();

    $body_template = $item->getTypeInfo()->body;
    $subject = $item->getSubject();
    $object = $item->getObject();

    $subject_info = (object) $helper->getBasicInfoFromItem($subject);
    $object_info = (object) $helper->getBasicInfoFromItem($object);

    // If cannot find object in body template
    if (strpos($body_template, 'object') === false) {
      $target_object_info = $subject_info;
    } else {
      $target_object_info = $object_info;
    }

    $select = $table->select()
            ->where('user_id = ?', $item->user_id)
            ->where('allow = ?', 1);
    $data = $table->fetchAll($select);
    foreach ($data as $row) {
      try {
        $pushMessage = $notificationInfoArray['body'];
        $deviceToken = $row->device_token;
        $deviceType = $row->device_type;
        $customPushData = array(
            'subject_info' => json_encode($subject_info),
            'target_object_info' => json_encode($target_object_info),
            'notification_type' => $notificationInfoArray['type']
        );
        $receiver_id = $row->user_id;
        $currentModuleCoreApi->sendPushNotification($deviceToken, $deviceType, $pushMessage, $customPushData, $receiver_id);
      } catch (Exception $ex) {
        continue;
      }
    }
  }

  public function routeShutdown(Zend_Controller_Request_Abstract $request) {
    
    $session = new Zend_Session_Namespace('Tristan_App');

    if ($request->getParam('from_app')) {
      $session->from_app = $request->getParam('from_app');
    }

    if ($session->from_app) {
      $request->setParam('from_app', 1);
    }

    if (Engine_Api::_()->hasModuleBootstrap('zulu') && (Engine_Api::_()->zulu()->isMobileMode() && $request->getParam('from_app'))) {
      // Create layout
      $layout = Zend_Layout::startMvc();
      // Set options
      $layout->setViewScriptPath(APPLICATION_PATH . "/application/modules/Mgslapi/layouts/scripts")
              ->setViewSuffix('tpl')
              ->setLayout(null);
    }
    // Add script to hide navigations in app web view
    if ($request->getParam('from_app') == 1) {
      $view = Zend_Registry::get('Zend_View');
      $view->from_app = 1;
      $view->headLinkSM()->appendStylesheet($view->layout()->staticBaseUrl . 'application/modules/Mgslapi/externals/styles/app.css');
    }
    Zend_Controller_Action_HelperBroker::addHelper(new Mgslapi_Controller_Action_Helper_MgslapiHooks);
  }

  public function dispatchLoopStartup(\Zend_Controller_Request_Abstract $request) {
    parent::dispatchLoopStartup($request);

    if (Engine_Api::_()->hasModuleBootstrap('zulu') && Engine_Api::_()->zulu()->isMobileMode()) {
      // Attempt to make the mobile view helpers prioritized
      Zend_Registry::get('Zend_View')->getPluginLoader('helper')
              ->removePrefixPath('Sitemobile_View_Helper_')
              ->addPrefixPath('Sitemobile_View_Helper_', APPLICATION_PATH . '/application/modules/Sitemobile/View/Helper');
    }
  }

}
