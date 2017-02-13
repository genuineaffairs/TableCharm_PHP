<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Account.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Zulu_Plugin_Signup_Account extends User_Plugin_Signup_Account
{
  protected $_formClass = 'Zulu_Form_Signup_Account';

  protected $_script = array('signup/form/account.tpl', 'zulu');
  
  function onProcess() {
    parent::onProcess();
    $data = $this->getSession()->data;
    
    if(isset($data['parental_email']) && !empty($data['parental_email'])) {
      $this->_registry->parentalEmail = $data['parental_email'];
    }
  }
}