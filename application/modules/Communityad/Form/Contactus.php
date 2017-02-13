<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Contactus.php  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Form_Contactus extends Engine_Form {

  public function init() {
    $this->setTitle('Contact Sales Team');
    $this->setDescription('If you want to ask us a question directly, please submit your message with the following form.');
    //ELEMENT NAME
    $this->addElement('Text', 'name', array(
        'label' => 'Name',
        'required' => true
    ));
    //ELEMENT EMAIL
    $this->addElement('Text', 'email', array(
        'label' => 'Email Address',
        'required' => true
    ));
    //ELEMENT MESSAGE
    $this->addElement('textarea', 'message', array(
        'label' => 'Message',
        'required' => true
    ));
    //ELEMENT SUBMIT
    $this->addElement('Button', 'submit', array(
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formbuttons.tpl',
                    'class' => 'form element'
            )))
    ));
  }

}