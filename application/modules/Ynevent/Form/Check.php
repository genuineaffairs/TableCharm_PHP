<?php

class Ynevent_Form_Check extends Engine_Form
{
  
  public function init()
  {
	 //$this
     // ->addPrefixPath('Ynevent_Form_Decorator', APPLICATION_PATH . '/application/modules/Ynevent/Form/Decorator', 'decorator')
      //->addPrefixPath('Ynevent_Form_Element', APPLICATION_PATH . '/application/modules/Ynevent/Form/Element', 'element')
      //->addElementPrefixPath('Ynevent_Form_Decorator', APPLICATION_PATH . '/application/modules/Ynevent/Form/Decorator', 'decorator');
	
	$this
	  ->setTitle('Please select')
	  ->setAttrib('id', 'ynevent_kind')
	  ->setAttrib('class', 'global_form_popup')	 
      ->setMethod('POST')	  
      ->setAction($_SERVER['REQUEST_URI'])
      ;
	    
	$this -> addElement('Radio', 'apply_for', array(           
            'multiOptions' => array(
                '0' => 'Only this event',
                '1' => 'All events',
                '2' => 'Following events',
            ),
            'value' => 0,   
            'decorators' => array('ViewHelper')
        ));
		
	// Buttons
    $this->addElement('Button', 'Save', array(
      'label' => 'Save Changes',
      'type' => 'button',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
      'onclick'=>'myselect();',
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(     
      'link' => true,
      'prependText' => ' or ',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
	$this->addDisplayGroup(array('Save', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
	/*
    $this->addDisplayGroup(array('Save', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));*/
  }
}