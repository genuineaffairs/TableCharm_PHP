<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Login.php 10078 2013-08-01 19:20:57Z jung $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Sitemobile_modules_User_Form_Login extends Engine_Form
{
  protected $_mode;
  
  public function setMode($mode)
  {
    $this->_mode = $mode;
    return $this;
  }
  
  public function getMode()
  {
    if( null === $this->_mode ) {
      $this->_mode = 'page';
    }
    return $this->_mode;
  }
  
  protected function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
  }
  
  public function init()
  {
    setcookie ('cookie_test', 1, time() + 600, '/');    
    
    $tabindex = 1;
    
    $description = Zend_Registry::get('Zend_Translate')->_("If you already have an account, please enter your details below. If you don't have one yet, please <a href='%s'>sign up</a> first.");
    $description= sprintf($description, Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_signup', true));

    // Init form
    $this->setTitle('Member Sign In');
    $this->setDescription($description);
    $this->setAttrib('id', 'user_form_login');
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);

    // Init email
        $this->addElement('Text', 'email', array(
        'label' => 'Email Address',
        'autocomplete' => 'off'
    ));
    $this->email->getDecorator('htmlTag2')->setOption('class', 'dnone');
    if (isset($_SESSION['login_email']) && isset($_POST['email'])) {
      $antispam = $_SESSION['login_email'];
    } else {
      $antispam = $this->generateRandomString(20);
      $_SESSION['login_email'] = $antispam;
    }


    $email = Zend_Registry::get('Zend_Translate')->_('Email Address');
    // Init email
    $this->addElement('Text', 'email', array(
        'label' => $email,
        'required' => true,
        'allowEmpty' => false,
        'filters' => array(
            'StringTrim',
        ),
        'validators' => array(
            'EmailAddress'
        ),
        // Fancy stuff
        'tabindex' => $tabindex++,
        'autofocus' => 'autofocus',
        'inputType' => 'text',
        'class' => 'text',
    ));


    $password = Zend_Registry::get('Zend_Translate')->_('Password');
    // Init password
    $this->addElement('Password', 'password', array(
      'label' => $password,
      'required' => true,
      'allowEmpty' => false,
      'tabindex' => $tabindex++,
      'filters' => array(
        'StringTrim',
      ),
    ));

    $this->addElement('Hidden', 'return_url', array(
      
    ));

    $settings = Engine_Api::_()->getApi('settings', 'core');
    if(isset($_SESSION['login_attempt']) && $_SESSION['login_attempt'] > 2){
      if( $settings->sitemobile_spam_login && !Engine_Api::_()->sitemobile()->isApp()) {
        $this->addElement('captcha', 'captcha', Engine_Api::_()->core()->getCaptchaOptions(array(
          'tabindex' => $tabindex++,
        )));
      }      
    }

    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Sign In',
      'type' => 'submit',
      'ignore' => true,
      'tabindex' => $tabindex++,
    ));
    
//    // Init remember me
//    $this->addElement('Checkbox', 'remember', array(
//      'label' => 'Remember Me',
//      'tabindex' => $tabindex++,
//    ));

//    $this->addDisplayGroup(array(
//      'submit',
//      'remember'
//    ), 'buttons');

   

    // Init facebook login link
    if( 'none' != $settings->getSetting('core_facebook_enable', 'none')
        && $settings->core_facebook_secret ) {
      $this->addElement('Dummy', 'facebook', array(
        'content' => User_Model_DbTable_Facebook::loginButton(),
      ));
    }

    // Init twitter login link
    if( 'none' != $settings->getSetting('core_twitter_enable', 'none')
        && $settings->core_twitter_secret ) {
      $this->addElement('Dummy', 'twitter', array(
        'content' => User_Model_DbTable_Twitter::loginButton(),
      ));
    }
    
    // Init janrain login link
    if( 'none' != $settings->getSetting('core_janrain_enable', 'none')
        && $settings->core_janrain_key ) {
      $mode = $this->getMode();
      $this->addElement('Dummy', 'janrain', array(
        'content' => User_Model_DbTable_Janrain::loginButton($mode),
      ));
    }

    // Set default action
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login'));
  }
}
