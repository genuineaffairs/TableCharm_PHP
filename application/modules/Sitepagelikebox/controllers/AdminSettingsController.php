<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagelikebox
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2011-10-10 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagelikebox_AdminSettingsController extends Core_Controller_Action_Admin {

    public function __call($method, $params) {
        /*
         * YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
         * YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
         * REMEMBER:
         *    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
         *    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
         */
        if (!empty($method) && $method == 'Sitepagelikebox_Form_Admin_Global') {

        }
        return true;
    }
    
  public function indexAction() {
		//START LANGUAGE WORK
		Engine_Api::_()->getApi('language', 'sitepage')->languageChanges();
		//END LANGUAGE WORK
    // set_time_limit( 0 ) ;
    include APPLICATION_PATH . '/application/modules/Sitepagelikebox/controllers/license/license1.php' ;
  }

  //ACTION FOR FAQ
  public function faqAction() {

    // GET THE NAVIGATION.
    $this->view->navigation = $navigation = Engine_Api::_()->getApi( 'menus' , 'core' )
            ->getNavigation( 'sitepagelikebox_admin_main' , array ( ) , 'sitepagelikebox_admin_main_faq' ) ;
  }

  //ACTION FOR README
  public function readmeAction() {
  }
}
?>