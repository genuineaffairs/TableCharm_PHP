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

class Epayment_Form_Checkout extends Engine_Form
{
  protected $_item;	
	
  protected $_package;
  
  
  public function init()
  {
    $auth = Engine_Api::_()->authorization()->context;
    $user = Engine_Api::_()->user()->getViewer();

/*
 * 
  <form method="POST" name="epayment_gateway_form" action="https://www.paypal.com/cgi-bin/webscr" >
      <input type="hidden" name="rm" value="2" />
      <input type="hidden" name="cmd" value="_xclick" />
      <input type="hidden" name="business" value="paypal@radcodes.com" />
      <input type="hidden" name="currency_code" value="USD" />
      <input type="hidden" name="return" value="http://se3.radcodes.net/epayment_done.php?gateway_type=paypal&item_type=item&item_id=90" />

      <input type="hidden" name="cancel_return" value="http://se3.radcodes.net/epayment_cancel.php?gateway_type=paypal&item_type=item&item_id=90" />
      <input type="hidden" name="notify_url" value="http://se3.radcodes.net/epayment_ipn.php?gateway_type=paypal&item_type=item&item_id=90" />
      <input type="hidden" name="custom" value="item" />
      <input type="hidden" name="item_number" value="90" />
      <input type="hidden" name="item_name" value="Juicy Fruit" />
      <input type="hidden" name="amount" value="0.01" />
    
    <div style="font-size: 12px">If you are not automatically redirected to payment website within 5 seconds... </div>
    <br />

      <input type="submit" value="Click Here" />
    
  </form>
 */
    
    $view = Zend_Registry::get('Zend_View');
        
    $this->setTitle('Payment Checkout')
      ->setDescription('Please review and confirm the item you are about to check out, then press "Pay by PayPal" button to proceed.')
      ->setAction('https://www.paypal.com/cgi-bin/webscr');

    $this->addHiddenFields();
      
    $this->addElement('Dummy', 'display_item_type', array(
    	'label' => 'Type',
    	'content' => $view->translate('ITEM_TYPE_'.strtoupper($this->_item->getType())),
    ));
    
    $this->addElement('Dummy', 'display_item_name', array(
    	'label' => 'Item',
    	'content' => $this->_item->__toString(),
    ));
    
    $this->addElement('Dummy', 'display_package_name', array(
    	'label' => 'Package',
    	'content' => $this->getPackage()->toString(),
    ));
    
    $this->addElement('Dummy', 'display_package_term', array(
    	'label' => 'Term',
    	'content' => $this->getPackage()->getTerm(),
    ));    
    
    // Submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Pay by PayPal',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'type' => 'submit'
    ));

    $this->addElement('Cancel', 'cancel', array(
      'prependText' => ' or ',
      'label' => 'cancel',
      'link' => true,
      //'href' => 'javascr',
      //'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      ),
    ));

    $this->addDisplayGroup(array(
      'submit',
      'cancel'
    ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
    

  }
  
  
  protected function addHiddenFields()
  {
  	$baseUrl = $this->_getBaseUrl();
      
    $i = -5000;  
    $this->addElement('Hidden', 'rm', array(
      'value' => 2,
      'order' => $i++,
    ));  
    $this->addElement('Hidden', 'cmd', array(
      'value' => '_xclick',
      'order' => $i++,
    )); 
    $this->addElement('Hidden', 'business', array(
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('epayment.paypalemail'),
      'order' => $i++,
    )); 
    $this->addElement('Hidden', 'currency_code', array(
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('epayment.currency', 'USD'),
      'order' => $i++,
    )); 
    $this->addElement('Hidden', 'return', array(
      'value' => $baseUrl . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'success', 'subject'=>$this->_item->getGuid()), 'epayment_general', true),
      'order' => $i++,
    )); 
    $this->addElement('Hidden', 'cancel_return', array(
      'value' => $baseUrl . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'cancel-return', 'subject'=>$this->_item->getGuid()), 'epayment_general', true),
      'order' => $i++,
    ));
    $this->addElement('Hidden', 'notify_url', array(
      'value' => $baseUrl . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'notify', 'subject'=>$this->_item->getGuid()), 'epayment_general', true),
      'order' => $i++,
    )); 
    $this->addElement('Hidden', 'custom', array(
      'value' => $this->getPackage()->getIdentity(),
      'order' => $i++,
    )); 
    $this->addElement('Hidden', 'item_number', array(
      'value' => $this->_item->getGuid(),
      'order' => $i++,
    )); 
    $this->addElement('Hidden', 'item_name', array(
      'value' => $this->_item->getTitle(),
      'order' => $i++,
    ));
    $this->addElement('Hidden', 'amount', array(
      'value' => $this->getPackage()->price,
      'order' => $i++,
    ));
  }
  
  public function getItem()
  {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item)
  {
    $this->_item = $item;
    return $this;
  }   
  
  public function getPackage()
  {
    if (!($this->_package instanceof Core_Model_Item_Abstract))
    {
      return $this->_item->getPackage();
    }
    return $this->_package;
  }

  public function setPackage(Core_Model_Item_Abstract $package)
  {
    $this->_package = $package;
    return $this;
  }  
  
  protected function _getBaseUrl()
  {
  	$baseUrl = (constant('_ENGINE_SSL') ? 'https://' : 'http://') 
      . Zend_Controller_Front::getInstance()->getRequest()->getHttpHost();

    return $baseUrl;
  }
}