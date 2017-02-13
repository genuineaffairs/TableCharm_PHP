<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Epayment
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */

class Epayment_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('epayment_admin_main', array(), 'epayment_admin_main_settings');
    
    $this->view->form = $form = new Epayment_Form_Admin_Global();
    if ( $this->getRequest()->isPost() && $this->view->form->isValid($this->getRequest()->getPost()) ) 
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try
      {
        $values = $form->getValues();
  
        foreach ($values as $key => $value){
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
        
        $db->commit();
        
        $savedChangesNotice = Zend_Registry::get('Zend_Translate')->_("Your changes were saved.");
        $form->addNotice($savedChangesNotice);
        
        
      }
      catch (Exception $e)
      {
        $db->rollback();
        throw $e;
      }
    }
  }


}