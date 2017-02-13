<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Bootstrap.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Video_Bootstrap extends Engine_Application_Bootstrap_Abstract
{

  protected function _bootstrap($resource = null)
  {
    parent::_bootstrap($resource);

    if (Engine_Api::_()->hasModuleBootstrap('advancedactivity') &&
            !Engine_Api::_()->advancedactivity()->isMobile()) {

      // A simple hack to remove the duplicated video composer
      $engineManifest = Zend_Registry::get('Engine_Manifest');
      unset($engineManifest['video']['composer']);

      Zend_Registry::set('Engine_Manifest', $engineManifest);
    }
  }

}
