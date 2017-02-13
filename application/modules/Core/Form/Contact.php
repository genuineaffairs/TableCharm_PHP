<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Contact.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Core_Form_Contact extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Contact Us')
      ->setDescription('_CORE_CONTACT_DESCRIPTION')
      ->setDescription('Need to contact us? My Global Sport Link promises to respond to your needs as quickly as possible. Before you contact us, be sure to check the MGSL Help Circle on the site which has the answers to the most commonly asked questions.
	If the Help Circle doesn’t address your question(s), please complete the fields below:
	Our normal business hours are 9am-5pm Australian Eastern Standard Time, Monday-Friday, and you can expect a response from one of our team members within 24 hours during this time. Emails received late on Friday, or over the weekend, will receive a response on Monday.')
      ->setAction($_SERVER['REQUEST_URI'])
      ;
    
    $this->addElement('Text', 'name', array(
      'label' => 'Name',
      'required' => true,
      'notEmpty' => true,
    ));
    
    $this->addElement('Text', 'email', array(
      'label' => 'Email Address',
      'required' => true,
      'notEmpty' => true,
      'validators' => array(
        'EmailAddress'
      )
    ));

    $this->addElement('Textarea', 'body', array(
      'label' => 'Message',
      'required' => true,
      'notEmpty' => true,
    ));

    $show_captcha = Engine_Api::_()->getApi('settings', 'core')->core_spam_contact;
    if( $show_captcha && ($show_captcha > 1 || !Engine_Api::_()->user()->getViewer()->getIdentity() ) ) {
      $this->addElement('captcha', 'captcha', Engine_Api::_()->core()->getCaptchaOptions());
    }

    $this->addElement('Button', 'submit', array(
      'label' => 'Send Message',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}