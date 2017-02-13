<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminWidgetController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagepoll_AdminWidgetsController extends Core_Controller_Action_Admin {

  //ACTION FOR WIDGET SETTINGS
  public function indexAction() {

    //TAB CREATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitepagepoll_admin_main', array(), 'sitepagepoll_admin_widget_settings');
   
  }

}
?>