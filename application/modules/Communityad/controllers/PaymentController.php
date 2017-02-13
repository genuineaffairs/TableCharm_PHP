<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: PaymentController.php  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_PaymentController extends Core_Controller_Action_Standard {

  protected $_navigation;
  protected $_user_id;
  //User_Model_User
  protected $_user;
  // Zend_Session_Namespace
  protected $_session;
  // Payment_Model_Order
  protected $_order;
  // Payment_Model_Gateway
  protected $_gateway;
  // Communityad_Model_Subscription
  protected $_subscription;
  // Payment_Model_Userad
  protected $_userad;
  // Communityad_Model_Package
  protected $_package;
  protected $_success;

  public function init() {
    // It will show the navigation bar.
    $this->_user = Engine_Api::_()->user()->getViewer();

    if (!$this->_helper->requireAuth()->setAuthParams('communityad', $this->_user, 'view')->isValid()) {
      return;
    }
    $this->_user_id = $this->_user->getIdentity();
    $this->view->navigation = $this->_navigation = $this->getNavigation();
    // If there are no enabled gateways or packages, disable
    if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0 ||
        Engine_Api::_()->getDbtable('packages', 'communityad')->getEnabledNonFreePackageCount() <= 0) {
      return $this->_helper->redirector->gotoRoute(array(), 'communityad_campaigns', true);
    }

    // Get user and session
    $this->_session = new Zend_Session_Namespace('Payment_Userads');
    $this->_success = new Zend_Session_Namespace('Payment_Success');
    // Check viewer and user
    if (!$this->_userad) {
      if (!empty($this->_session->userad_id)) {
        $this->_userad = Engine_Api::_()->getItem('userads', $this->_session->userad_id);
      }
    }
  }

  public function indexAction() {

    return $this->_forward('gateway');
  }

  public function gatewayAction() {

    if (!$this->_userad) {
      $this->_session->unsetAll();
      return $this->_helper->redirector->gotoRoute(array(), 'communityad_campaigns', true);
    }

    $viewer_id = $this->_user_id;
    // Get advertiesment id
    $userAdId = $this->_getParam('userad_id', $this->_session->userad_id);
    // Get advertiesment
    $userAd = $this->_userad;

    if (!$this->_userad ||
        !( $userAdId) ||
        !($userAd) ||
        $userAd->owner_id != $this->_user->getIdentity() ||
        !($package = Engine_Api::_()->getItem('package', $userAd->package_id))) {
      return $this->_helper->redirector->gotoRoute(array(), 'communityad_campaigns', true);
    }

    $this->view->userad = $userAd;
    $this->view->package = $package;
    if ($this->_checkUseradsStatus($userAd)) {
      return;
    }
    // Unset certain keys
    unset($this->_session->gateway_id);
    unset($this->_session->order_id);

    // Gateways
    $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
    $gatewaySelect = $gatewayTable->select()
            ->where('enabled = ?', 1);
    $gateways = $gatewayTable->fetchAll($gatewaySelect);

    $gatewayPlugins = array();
    foreach ($gateways as $gateway) {

      $gatewayPlugins[] = array(
              'gateway' => $gateway,
              'plugin' => $gateway->getGateway(),
      );
    }
    $this->view->gateways = $gatewayPlugins;
  }

  public function processAction() {
    if (!$this->_userad) {
      $this->_session->unsetAll();
      return $this->_helper->redirector->gotoRoute(array(), 'communityad_campaigns', true);
    }
    // Get gateway
    $gatewayId = $this->_getParam('gateway_id', $this->_session->gateway_id);
    if (!$gatewayId ||
        !($gateway = Engine_Api::_()->getItem('communityad_gateway', $gatewayId)) ||
        !($gateway->enabled)) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'gateway'));
    }
    $this->view->gateway = $gateway;

    // Get advertiesment
    $useradId = $this->_getParam('userad_id', $this->_session->userad_id);
    if (!$useradId ||
        !($userAd = Engine_Api::_()->getItem('userads', $useradId))) {
      return $this->_helper->redirector->gotoRoute(array(), 'communityad_campaigns', true);
    }
    $this->view->userad = $userAd;

    // Get package
    $package = Engine_Api::_()->getItem('package', $userAd->package_id);
    if (!$package) {
      return $this->_helper->redirector->gotoRoute(array(), 'communityad_campaigns', true);
    }

    $this->view->package = $package;

    // Check advertiesment?
    if ($this->_checkUseradsStatus($userAd)) {
      return;
    }

    // Process
    // Create order
    $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
    if (!empty($this->_session->order_id)) {
      $previousOrder = $ordersTable->find($this->_session->order_id)->current();
      if ($previousOrder && $previousOrder->state == 'pending') {
        $previousOrder->state = 'incomplete';
        $previousOrder->save();
      }
    }
    $ordersTable->insert(array(
            'user_id' => $this->_user->getIdentity(),
            'gateway_id' => $gateway->gateway_id,
            'state' => 'pending',
            'creation_date' => new Zend_Db_Expr('NOW()'),
            'source_type' => 'userads',
            'source_id' => $userAd->userad_id,
    ));
    $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

    // Unset certain keys
    unset($this->_session->package_id);
    unset($this->_session->gateway_id);


    // Get gateway plugin
    $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();
    $plugin = $gateway->getPlugin();


    // Prepare host info
    $schema = 'http://';
    if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
      $schema = 'https://';
    }
    $host = $_SERVER['HTTP_HOST'];

    // Prepare transaction
    $params = array();
    $params['language'] = $this->_user->language;
    $params['vendor_order_id'] = $order_id;

    $params['return_url'] = $schema . $host
        . $this->view->url(array('action' => 'return', 'controller' => 'payment', 'module' => 'communityad'), 'default')
        . '?order_id=' . $order_id
        . '&state=' . 'return';
    $params['cancel_url'] = $schema . $host
        . $this->view->url(array('action' => 'return', 'controller' => 'payment', 'module' => 'communityad'), 'default')
        . '?order_id=' . $order_id
        . '&state=' . 'cancel';
    $params['ipn_url'] = $schema . $host
        . $this->view->url(array('action' => 'index', 'controller' => 'ipn', 'module' => 'payment'), 'default')
        . '?order_id=' . $order_id;

    // Process transaction
    $transaction = $plugin->createUseradTransaction($this->_user,
            $userAd, $package, $params);

    // Pull transaction params
    $this->view->transactionUrl = $transactionUrl = $gatewayPlugin->getGatewayUrl();
    $this->view->transactionMethod = $transactionMethod = $gatewayPlugin->getGatewayMethod();
    $this->view->transactionData = $transactionData = $transaction->getData();

    unset($this->_session->userad_id);

    // Handle redirection
    if ($transactionMethod == 'GET') {
      $transactionUrl .= '?' . http_build_query($transactionData);
      return $this->_helper->redirector->gotoUrl($transactionUrl, array('prependBase' => false));
    }

    // Post will be handled by the view script
  }

  public function returnAction() {

    // Get order
    if (!$this->_user ||
        !($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
        !($order = Engine_Api::_()->getItem('payment_order', $orderId)) ||
        $order->user_id != $this->_user->getIdentity() ||
        $order->source_type != 'userads' ||
        !($userad = $order->getSource()) ||
        !($package = $userad->getPackage()) ||
        !($gateway = Engine_Api::_()->getItem('communityad_gateway', $order->gateway_id))) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    // Get gateway plugin
    $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();
    $plugin = $gateway->getPlugin();

    // Process return
    unset($this->_session->errorMessage);
    
    try {
      $status = $plugin->onUseradTransactionReturn($order, $this->_getAllParams());
    } catch (Payment_Model_Exception $e) {
      $status = 'failure';
      $this->_session->errorMessage = $e->getMessage();
    }
    $this->_success->succes_id = $userad->userad_id;
    return $this->_finishPayment($status);
  }

  public function finishAction() {
    $this->view->status = $status = $this->_getParam('state');
    $this->view->error = $this->_session->errorMessage;
    if (isset($this->_success->succes_id)) {
      $this->view->id = $this->_success->succes_id;
      Engine_Api::_()->communityad()->sendAdminMail("DISAPPROVED_NOTIFICATION",  $this->view->id);
      unset($this->_success->succes_id);
    }
  }

  protected function _checkUseradsStatus(
  Zend_Db_Table_Row_Abstract $userad = null) {
    if (!$this->_user) {
      return false;
    }
    if (!$userad) {
      return false;
    }
    return false;
  }

  protected function _finishPayment($state = 'active') {
    $viewer = Engine_Api::_()->user()->getViewer();
    $user = $this->_user;

    // No user?
    if (!$this->_user) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    // Log the user in, if they aren't already
    if (($state == 'active' || $state == 'free') &&
        $this->_user &&
        !$this->_user->isSelf($viewer) &&
        !$viewer->getIdentity()) {
      Zend_Auth::getInstance()->getStorage()->write($this->_user->getIdentity());
      Engine_Api::_()->user()->setViewer();
    }

    // Clear session
    $errorMessage = $this->_session->errorMessage;
    $userIdentity = $this->_session->user_id;
    $this->_session->unsetAll();
    $this->_session->user_id = $userIdentity;
    $this->_session->errorMessage = $errorMessage;

    // Redirect
    if ($state == 'free') {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    } else {
      return $this->_helper->redirector->gotoRoute(array('action' => 'finish', 'state' => $state));
    }
  }

  // Utility

  public function getNavigation($active = false) {
    if (is_null($this->_navigation)) {
      $navigation = $this->_navigation = new Zend_Navigation();
      if (Engine_Api::_()->authorization()->isAllowed('communityad', $this->_user, 'view')) {
        $navigation->addPage(array(
                'label' => $this->view->translate('Ad Board'),
                'route' => 'communityad_display',
                'action' => 'adboard',
                'controller' => 'display',
                'module' => 'communityad'
        ));
      }
      if (Engine_Api::_()->authorization()->isAllowed('communityad', $this->_user, 'edit')) {
        $navigation->addPage(array(
                'label' => $this->view->translate('My Campaigns'),
                'route' => 'communityad_campaigns',
                'module' => 'communityad',
                'controller' => 'statistics',
                'action' => 'index'
        ));
      }
      if (Engine_Api::_()->authorization()->isAllowed('communityad', $this->_user, 'create')) {
        $navigation->addPage(array(
                'label' => $this->view->translate('Create an Ad'),
                'route' => 'communityad_listpackage'
        ));
      }
      $navigation->addPage(array(
              'label' => $this->view->translate('Help & Learn More'),
              'route' => 'communityad_help_and_learnmore',
              'action' => 'help-and-learnmore',
              'controller' => 'display',
              'module' => 'communityad'
      ));
    }

    return $this->_navigation;
  }

}
