<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Widget_SitemobileNotificationRequestMessagesController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    // Don't render this if not logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      return $this->setNoRender();
    }

    $this->view->showContent = $this->_getParam('showContent', array('request', 'updates', 'message'));
    $this->view->browseLayoutTypes = $this->_getParam('browseLayoutTypes', 'dashboard');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
    $this->view->showCartIcon = Engine_Api::_()->getDbtable('modules', 'sitemobile')->isModuleEnabled('sitestoreproduct');
    if (!$require_check) {
      if ($viewer->getIdentity()) {
        $this->view->search_check = true;
      } else {
        $this->view->search_check = false;
      }
    }
    else
      $this->view->search_check = true;

    $this->view->messagePermission = false;
    $this->view->loadingViaAjax = $this->_getParam('loadingViaAjax', 1);

    if ($viewer->getIdentity()) {
      $this->view->notificationCount = Engine_Api::_()->getDbtable('notifications', 'sitemobile')->hasNotifications($viewer);
      $this->view->updateSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.notificationupdate');

      // Get permission setting
      $permission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'create');
      if (Authorization_Api_Core::LEVEL_DISALLOW !== $permission) {
        $this->view->messagePermission = true;
      }

      $this->view->messageCount = Engine_Api::_()->messages()->getUnreadMessageCount($viewer);

      $this->view->requestsCount = $requests = Engine_Api::_()->getDbtable('notifications', 'sitemobile')->hasRequests($viewer);
      if ($this->view->showCartIcon) {
        $getCartId = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->getCartId($viewer->getIdentity());
        if (!empty($getCartId)) {
          $productTable = Engine_Api::_()->getDbtable('products', 'sitestoreproduct');
          $getCart = $productTable->getCart($getCartId, false);
          $this->view->cartProductCounts = $cartProductCounts = Engine_Api::_()->getDbtable('carts', 'sitestoreproduct')->getProductCounts($getCartId);
        }
      }
    }
  }

}