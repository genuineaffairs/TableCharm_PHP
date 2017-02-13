<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Authorization
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: RequireAuth.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Authorization
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_Controller_Action_Helper_RequireUser extends
Core_Controller_Action_Helper_RequireAbstract {

  protected $_errorAction = array('requireuser', 'error', 'sitemobile');

  public function checkRequire() {
    try {
      $viewer = Engine_Api::_()->user()->getViewer();
    } catch (Exception $e) {
      $viewer = null;
    }

    $ret = false;
    if ($viewer instanceof Core_Model_Item_Abstract && $viewer->getIdentity()) {
      $ret = true;
    }
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStack()->ViewRenderer;
    $viewRenderer->setViewBasePathSpec(':moduleDir/views');
    if (!$ret && APPLICATION_ENV == 'development' && Zend_Registry::isRegistered('Zend_Log') && ($log = Zend_Registry::get('Zend_Log')) instanceof Zend_Log) {
      $target = $this->getRequest()->getModuleName() . '.' .
              $this->getRequest()->getControllerName() . '.' .
              $this->getRequest()->getActionName();
      $log->log('Require class ' . get_class($this) . ' failed check for: ' . $target, Zend_Log::DEBUG);
    }

    return $ret;
  }

}