<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Bootstrap.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Messages_Bootstrap extends Engine_Application_Bootstrap_Abstract {

  protected function _initRequest() {
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl . 'application/modules/Messages/externals/styles/global.css');
    
    $view->headScript()->appendFile($view->layout()->staticBaseUrl . 'application/modules/Messages/externals/js/global.js');
    
    Zend_Registry::get('Zend_Controller_Front')->registerPlugin(new Messages_Plugin_Core());
  }
}
