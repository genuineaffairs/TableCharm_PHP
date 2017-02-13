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
 
 
 
class Epayment_Form_Admin_Manage_Filter extends Engine_Form
{
  
  protected $_type;
  
  public function init()
  {
    $this->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

    $this
      ->setAttribs(array(
        'id' => 'epayment_admin_filter_form',
        'class' => 'global_form_box',
    ));

      
    $this->addElement('Text', 'resource_id', array(
      'label' => 'Resource ID',
    ));
    
    $this->addElement('Text', 'user', array(
      'label' => 'Member',
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
      'filters' => array(
        new Radcodes_Lib_Filter_Null()
      ),    
      'multiOptions' => array(""=>"") + $packageOptions
    ));
    
      
    $this->addElement('Select', 'method', array(
      'label' => 'Method',
      'filters' => array(
        new Radcodes_Lib_Filter_Null()
      ),    
      'multiOptions' => array(""=>"") + Epayment_Model_Epayment::getMethodTypes()
    ));    
    
    $this->addElement('Text', 'payer_name', array(
      'label' => 'Payer Name',
    ));
    
    $this->addElement('Text', 'payer_account', array(
      'label' => 'Payer Account',
    ));    
    
    $this->addElement('Text', 'transaction_code', array(
      'label' => 'Transaction Code',
    ));
    
    $this->addElement('Select', 'status', array(
      'label' => 'Status',
      'filters' => array(
        new Radcodes_Lib_Filter_Null()
      ),    
      'multiOptions' => array(""=>"") + Epayment_Model_Epayment::getStatusTypes()
    ));


    $this->addElement('Select', 'processed', array(
      'label' => 'Processed',  
      'multiOptions' => array(""=>"") + array("1" => "Yes", "0" => "No")
    ));
    
    foreach( $this->getElements() as $fel ) {
      if( $fel instanceof Zend_Form_Element ) {
        
        $fel->clearDecorators()
          ->addDecorator('ViewHelper')
          ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
          ->addDecorator('HtmlTag', array('tag' => 'div', 'id'  => $fel->getName() . '-search-wrapper', 'class' => 'form-search-wrapper'));
        
      }
    }  
    
   // $submit = new Engine_Form_Element_Submit('filtersubmit', array('type' => 'submit'));
    $submit = new Engine_Form_Element_Button('filtersubmit', array('type' => 'submit'));
    $submit
      ->setLabel('Search')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
      ->addDecorator('HtmlTag2', array('tag' => 'div'));

    $this->addElement($submit);
      
    $this->addElement('Hidden', 'order', array(
      'order' => 1001,
    ));

    $this->addElement('Hidden', 'order_direction', array(
      'order' => 1002,
    ));


    // Set default action
    //$this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module'=>'epayment', 'controller'=>'manage'), 'admin_default', true));
  }  
  

}