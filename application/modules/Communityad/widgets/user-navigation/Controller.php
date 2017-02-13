<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Widget_UserNavigationController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
//  	$this->view->navigation = $this->getNavigation();
      $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation("communityad_main");
  }
}
?>