<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 6590 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_Bootstrap extends Engine_Application_Bootstrap_Abstract {

  protected function _initFrontController() {      $front = Zend_Controller_Front::getInstance();
    $front->registerPlugin(new Sitepageevent_Plugin_Core);
    include APPLICATION_PATH . '/application/modules/Sitepageevent/controllers/license/license.php';
    $this->initViewHelperPath();
  }

}

?>