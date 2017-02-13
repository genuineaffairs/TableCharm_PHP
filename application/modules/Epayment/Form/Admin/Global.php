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

class Epayment_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');

    $currencies = Engine_Api::_()->epayment()->getCurrencies();
    
    $this->addElement('Select', 'epayment_currency', array(
      'label' => 'Currency',
      'description' => 'Please select currency you would like to use',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('epayment.currency', 'USD'),
      'multiOptions' => $currencies,
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
         array('NotEmpty', true),
      ),
    ));
    
    $this->addElement('Text', 'epayment_paypalemail', array(
      'label' => 'Paypal Email',
      'description' => 'Please enter your Paypal account email',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('epayment.paypalemail'),
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
         //array('NotEmpty', true),
         'EmailAddress'
      ),
      'filters' => array(
        'StringTrim'
      ),
    ));    
    
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }

  public function checkChannelLogoUri($uri)
  {
    return Zend_Uri::check($uri);
  }
}