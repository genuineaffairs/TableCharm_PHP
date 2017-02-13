<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Folder
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */ 
class Folder_Form_Folder_Password extends Engine_Form
{
  public $_error = array();
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
    $this->clearDecorators();
    
    $this->setTitle('Password Protected Folder')
      ->setDescription("Please enter the secret code below to unlock this folder's content")
      ->setAttrib('name', 'folders_password')
      ->setAttrib('class', 'folders_password');

    $this->addElement('Password', 'secret_code', array(
      'label' => 'Secret Code',
      'allowEmpty' => false,
      'required' => true,
    ));
      
    $codeValidator = new Engine_Validate_Callback(array($this, 'checkSecretCode'));
    $codeValidator->setMessage("This secret code is invalid");    
    $this->secret_code->addValidator($codeValidator);

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Unlock Folder',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    /*
    $this->addDisplayGroup(array('submit'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->addDecorator('DivDivDivWrapper');    
	*/
  }

  public function checkSecretCode($value)
  {
    return $this->getItem()->secret_code == $value;
  }  
  
}