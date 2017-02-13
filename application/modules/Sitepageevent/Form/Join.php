<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Join.php 6590 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_Form_Join extends Engine_Form {

  public function init() {
    
    $this
            ->setTitle('Join_Event')
            ->setDescription('Would you like to join this Page event?')
            ->setMethod('POST')
            ->setAction($_SERVER['REQUEST_URI'])
    ;
   
    $this->addElement('Radio', 'rsvp', array(
        'required' => true,
        'allowEmpty' => false,
        'multiOptions' => array(
            2 => 'Attending',
            1 => 'Maybe Attending',
            0 => 'Not Attending',
        ),
        'value' => 2,
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Join_Event',
        'ignore' => true,
        'decorators' => array('ViewHelper'),
        'type' => 'submit'
    ));

    $this->addElement('Cancel', 'cancel', array(
        'prependText' => ' or ',
        'label' => 'cancel',
        'link' => true,
        'href' => '',
        'onclick' => 'parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        ),
    ));

    $this->addDisplayGroup(array(
        'submit',
        'cancel'
            ), 'buttons');
  }

}

?>