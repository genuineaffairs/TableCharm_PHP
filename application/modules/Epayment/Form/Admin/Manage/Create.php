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
 
 
 
class Epayment_Form_Admin_Manage_Create extends Engine_Form
{
  
  protected $_type;
  
  public function init()
  {
    $this->setTitle('Add New Payment')
      ->setDescription('Please fill out the form below to add new payment.')
      ;

    $i = -1000;  
      
    $this->addElement('Hidden', 'resource_type', array(
      'value' => $this->_type,
      'order' => $i++,
    ));
      
      
    $this->addElement('Text', 'resource_id', array(
      'label' => 'Resource ID',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
    //    'Int',
        new Engine_Validate_Callback(array($this, 'validateResourceId')),
      ),
    ));

    try
    {
      $api = Engine_Api::_()->getItemApi($this->_type);
      $packageOptions = $api->convertPackagesToArray($api->getPackages());
    }
    catch (Exception $e)
    {
      $packageOptions = Engine_Api::_()->getItemTable($this->_type.'_package')->getMultiOptionsAssoc();
    }
    
    $this->addElement('Select', 'package_id', array(
      'label' => 'Package',
      'allowEmpty' => false,
      'required' => true,
      'multiOptions' => array(""=>"") + $packageOptions
    ));
   
    $this->addElement('Select', 'method', array(
      'label' => 'Method',
      'allowEmpty' => false,
      'required' => true,
      'multiOptions' => Epayment_Model_Epayment::getMethodTypes()
    )); 
    
    $this->addElement('Text', 'payer_name', array(
      'label' => 'Payer Name',
      'allowEmpty' => false,
      'required' => true,    
    ));
    
    $this->addElement('Text', 'payer_account', array(
      'label' => 'Payer Account',
      'description' => 'For PayPal, use payer email address',
      'allowEmpty' => false,
      'required' => true,
    ));    
    
    $this->addElement('Text', 'transaction_code', array(
      'label' => 'Transaction Code',
      'description' => 'Enter unique ID to identify this transaction',
      'allowEmpty' => false,
      'required' => true,   
      'validators' => array(
        new Engine_Validate_Callback(array($this, 'validateTransactionCode')),
      ),
    ));

    $this->addElement('Text', 'amount', array(
      'label' => 'Amount',
      'class' => 'short',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('Float', true),
        new Engine_Validate_AtLeast(0),
      ),
      'value' => '0.00',
    ));    
    
    $currencies = Engine_Api::_()->epayment()->getCurrencies();
    $this->addElement('Select', 'currency', array(
      'label' => 'Currency',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('epayment.currency', 'USD'),
      'multiOptions' => $currencies,
      'allowEmpty' => false,
      'required' => true,
    ));
    
    $this->addElement('Select', 'status', array(
      'label' => 'Status',
      'required' => true,
      'allowEmpty' => false,  
      'multiOptions' => array(""=>"") + Epayment_Model_Epayment::getStatusTypes()
    ));
      

    $this->addElement('Textarea', 'notes', array(
      'label' => 'Notes'
    ));
    
    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Add Payment',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'prependText' => ' or ',
      'ignore' => true,
      'link' => true,
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index')),
      //'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));


    // DisplayGroup: buttons
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      )
    ));

  }  
  
  public function validateTransactionCode($value)
  {
    $params = array('resource_type' => $this->_type, 'transaction_code' => $value);
    $epayment = Engine_Api::_()->epayment()->getEpayment($params);
    
    if ($epayment instanceof Core_Model_Item_Abstract)
    {
      $this->transaction_code->getValidator('Engine_Validate_Callback')->setMessage('Duplicate epayment entry found.');
      return false;
    }
    
    return true;
  }
  
  public function validateResourceId($value)
  {
    $item = Engine_Api::_()->getItem($this->_type, $value);
    
    if (!($item instanceof Core_Model_Item_Abstract) || !$item->getIdentity())
    {
      $this->resource_id->getValidator('Engine_Validate_Callback')->setMessage('ID does not exist.');
      return false;
    }
    
    return true;
  }
}