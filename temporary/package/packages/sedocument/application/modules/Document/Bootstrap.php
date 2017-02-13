<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Bootstrap.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Document_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  protected function _initFrontController() {

    $this->initActionHelperPath();

    $front = Zend_Controller_Front::getInstance();
    $front->registerPlugin(new Document_Plugin_Core);
  }

	public function __construct($application)
  {
    parent::__construct($application);
		include APPLICATION_PATH . '/application/modules/Document/controllers/license/license.php';
  }
}