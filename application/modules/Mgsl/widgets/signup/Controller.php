<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Mgsl_Widget_SignupController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Do not show if logged in
    if( Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      $this->setNoRender();
      return;
    }
    
    if(!Zend_Controller_Front::getInstance()->getRequest()->isPost()) {
      foreach (Engine_Api::_()->getDbtable('signup', 'zulu')->fetchAll() as $row) {
        if ($row->enable == 1) {
          $class = $row->class;
          $plugin = new $class;
          if (method_exists($plugin, 'resetSession')) {
            $plugin->resetSession();
          }
        }
      }
    }

    // Display form
    $form = $this->view->form = new User_Form_Login(array(
      'mode' => 'column',
    ));;
    $form->setTitle(null)->setDescription(null);
    $form->removeElement('forgot');

    // Facebook login
    if( 'none' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable ) {
      $form->removeElement('facebook');
    }
    
    // Check for recaptcha - it's too fat
    $this->view->noForm = false;
    if( ($captcha = $form->getElement('captcha')) instanceof Zend_Form_Element_Captcha && 
        $captcha->getCaptcha() instanceof Zend_Captcha_ReCaptcha ) {
      $this->view->noForm = true;
      $form->removeElement('email');
      $form->removeElement('password');
      $form->removeElement('captcha');
      $form->removeElement('submit');
      $form->removeElement('remember');
//      $form->removeElement('facebook');
//      $form->removeElement('twitter');
      $form->removeDisplayGroup('buttons');
    }
  }
  
  public function getCacheKey()
  {
    return false;
  }
}
