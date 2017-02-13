<?php
/**
 * iPragmatech Solution Pvt. Ltd.
 *
 * @category   Application_Core
 * @package    Pinit
 * @copyright  Copyright 2008-2013 iPragmatech Solution Pvt. Ltd.
 * @license    http://www.ipragmatech.com/license/
 * @version    $Id: AdminSettingsController.php 9747 2013-07-06 02:08:08Z iPrgamtech $
 * @author     iPragmatech
 */



class Ecalendar_AdminSettingsController extends Core_Controller_Action_Admin
{
	public function indexAction()
	{
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
		->getNavigation('ecalendar_admin_main', array(), 'ecalendar_admin_main_settings');
	}
}