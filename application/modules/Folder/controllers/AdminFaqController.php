<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Folder
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 

class Folder_AdminFaqController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('folder_admin_main', array(), 'folder_admin_main_faq');
      
    $faq_remote = "http://www.radcodes.com/lib/rest/faq/?module=folder";
    $this->view->faq = file_get_contents($faq_remote);  
  }
  
}