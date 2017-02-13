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
 
 
 
class Epayment_Form_Admin_Manage_Process extends Engine_Form
{
  
  protected $_type;
  
  /*
   * @var Epayment_Model_Epayment
   */
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
    $view = Zend_Registry::get('Zend_View');
    
    $this->setTitle('Process Payment')
      ->setDescription('To process this payment, please click on the "Process Payment" button below. After payment is processed, various of item\'s settings would be updated corresponding to the purchased packaged.')
      ->setAttrib('class', 'epayment_process_form')
      ;

    $this->_addResourceFields();
    $this->_addEpaymentFields();
    $this->_addSettingFields();
    $this->_addButtons();
  }
  
  protected function _addResourceFields()
  {
  	$view = Zend_Registry::get('Zend_View');
  	
    $this->addElement('Heading', 'resource_header', array(
      'label' => 'Item Info:',
      'value' => 'Item Info:',
    ));  
    
    $this->resource_header->removeDecorator('Label')
          ->removeDecorator('HtmlTag')
          ->getDecorator('HtmlTag2')->setOption('class', 'form-wrapper-heading');
    
    $this->addElement('Dummy', 'resource_title', array(
      'label' => 'Title',
      'content' => $this->_item->getParent()->__toString()
    ));  

    $this->addElement('Dummy', 'package', array(
      'label' => 'Current Package',
      'content' => $this->_item->getParent()->getPackage()->__toString()
    ));    
    
    $this->addElement('Dummy', 'resource_status', array(
      'label' => 'Current Status',
      'content' => $this->_item->getParent()->getStatusText().' ('.$view->locale()->toDateTime($this->_item->getParent()->status_date).')',
    ));
    
    $this->addElement('Dummy', 'expiration_info', array(
      'label' => 'Current Expiration',
      'content' => $this->_item->getParent()->hasExpirationDate() ?
          $view->locale()->toDateTime($this->_item->getParent()->expiration_date)
        : 'Never'
    ));      

  }
  
  protected function _addEpaymentFields()
  {
  	$view = Zend_Registry::get('Zend_View');
  	
    $this->addElement('Heading', 'epayment_header', array(
      'label' => 'Payment Info:',
      'value' => 'Payment Info:',
    ));  
      
    $this->epayment_header->removeDecorator('Label')
          ->removeDecorator('HtmlTag')
          ->getDecorator('HtmlTag2')->setOption('class', 'form-wrapper-heading');    
    
    $this->addElement('Dummy', 'identifier', array(
      'label' => 'Payment ID',
      'content' => $this->_item->getIdentity(),
    ));
      
    $this->addElement('Dummy', 'epayment_status', array(
      'label' => 'Payment Status',
      'content' => $this->_item->getStatusText().' ('.$view->locale()->toDateTime($this->_item->creation_date).')'
    ));
    
    $this->addElement('Dummy', 'epayment_package', array(
      'label' => 'Payment Package',
      'content' => $this->_item->getPackage()->__toString() . ' ('.$this->_item->getPackage()->getTerm().') '
    ));
    
    if ($this->_item->processed)
    {
    $this->addElement('Dummy', 'epayment_processed_date', array(
      'label' => 'Last Processed Date',
      'content' => $view->locale()->toDateTime($this->_item->processed_date)
    ));
    }
    
  }
  
	protected function _addButtons()
	{
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Process Payment',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'view',
      'prependText' => ' or ',
      'ignore' => true,
      'link' => true,
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'view')),
      //'onClick'=> 'javascript:window.history.back();',
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
  
  protected function _addSettingFields()
  {
    
  }
}