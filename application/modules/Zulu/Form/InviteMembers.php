<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: InviteMembers.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Zulu_Form_InviteMembers extends Engine_Form {
  
  protected $_addPeopleDescription = array(
    'full' => 'People belong to this list can view and modify all of information in your medical record.',
    'read_only' => 'People belong to this list can view all of information in your medical record.',
    'limited' => 'People belong to this list can view parts of information in your medical record.',
  );

  public function init() {
    
    $accessType = 'limited';
    
    if(Zend_Registry::isRegistered('Zend_View') && !empty(Zend_Registry::get('Zend_View')->access_type)) {
      $accessType = Zend_Registry::get('Zend_View')->access_type;
    }

    $this->setTitle('Add people to list');
    $this->setDescription($this->_addPeopleDescription[$accessType])
            ->setAttrib('id', 'messages_compose');
    $Button = 'Add People';


    // init to
    $this->addElement('Text', 'user_ids', array(
        'label' => 'Start typing the name of the member...',
        'autocomplete' => 'off'
    ));
    Engine_Form::addDefaultDecorators($this->user_ids);

    // Init to Values
    $this->addElement('Hidden', 'toValues', array(
        'label' => '',
        'order' => '5',
        'filters' => array(
            'HtmlEntities'
        ),
    ));
    Engine_Form::addDefaultDecorators($this->toValues);

    $this->addElement('Button', 'submit', array(
        'label' => $Button,
        'ignore' => true,
        'order' => '8',
        'decorators' => array('ViewHelper'),
        'type' => 'submit'
    ));

    $this->addElement('Cancel', 'cancel', array(
        'prependText' => ' or ',
        'label' => 'cancel',
        'link' => true,
        'order' => '9',
        'href' => '',
        'onclick' => 'parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        ),
    ));
  }

}
