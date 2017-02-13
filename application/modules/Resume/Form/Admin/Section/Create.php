<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Resume_Form_Admin_Section_Create extends Resume_Form_Admin_Section_Abstract
{

  
  public function init()
  {
    $this->setTitle('Add New Section')
      ->setDescription('Please fill out the form below to create a new section. Note: Please pick the "Data Type" correctly, you will not be able to change it later.')
      ->setAttrib('class', 'global_form_popup radcodes_category_form_popup')
      ;
    
    $this->addElement('Select', 'child_type', array(
      'label' => 'Data Type',
    	'multiOptions' => Resume_Form_Helper::getSectionChildTypeOptions(),
      'allowEmpty' => false,
      'required' => true,    
    ));  
      
    $this->addElement('Text', 'title', array(
      'label' => 'Section Name',
      'allowEmpty' => false,
      'required' => true,
      'attribs' => array(
        'class' => 'text'
      ),
    ));
    
    $this->addElement('Textarea', 'description', array(
      'label' => 'Description',
    ));

    
    /*
    $this->addElement('File', 'photo', array(
      'label' => 'Photo'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif');
		*/
    
    $this->addElement('Checkbox', 'enabled', array(
      'label' => 'Add to new resume by default',
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Add Section',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}