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
 
 
 
class Epayment_Form_Admin_Manage_Edit extends Epayment_Form_Admin_Manage_Create
{
  
  protected $_type;
  protected $_item;
  
  public function getItem()
  {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item)
  {
    $this->_item = $item;
    return $this;
  }
  
  public function init()
  {
    parent::init();
    
    $this->setTitle('Edit Payment')
      ->setDescription('Please fill out the form below to update existing payment.')
      ;
      
    $this->submit->setLabel('Save Changes');  
    $this->cancel->setLabel('view');
    $this->cancel->setAttrib('href', Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'view')));
  }
  
  public function validateTransactionCode($value)
  {
    $params = array('resource_type' => $this->_type, 'transaction_code' => $value);
    $epayment = Engine_Api::_()->epayment()->getEpayment($params);
    
    if ($epayment instanceof Core_Model_Item_Abstract && $epayment->getIdentity() != $this->_item->getIdentity())
    {
      $this->transaction_code->getValidator('Engine_Validate_Callback')->setMessage('Duplicate epayment entry found.');
      return false;
    }
    
    return true;
  }  
}