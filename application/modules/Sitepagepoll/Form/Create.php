<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagepoll_Form_Create extends Engine_Form {

  public function init() {
    $auth = Engine_Api::_()->authorization()->context;
    $user = Engine_Api::_()->user()->getViewer();
    $this->setTitle('Create New Poll')
            ->setDescription("Create a new poll in this Page by filling the information below, then click 'Create  Poll' to start the poll.")
            ->setAttrib('id', 'sitepagepoll_create_form')
            ->setAttrib('name', 'sitepagepoll_create')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $this->addElement('text', 'title', array(
        'label' => 'Poll Title',
        'required' => true,
        'maxlength' => 63,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '63'))
        ),
    ));

    $this->addElement('textarea', 'description', array(
        'label' => 'Description',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '400'))
        ),
    ));

    $this->addElement('textarea', 'options', array(
        'label' => 'Possible Answers',
        'style' => 'display:none;',
    ));

    $this->addElement('Radio', 'end_settings', array(
        'id' => 'end_settings',
        'label' => 'Voting End',
        'description' => 'When should voting end for this poll?',
        'onclick' => "updateTextFields(this.value)",
        'multiOptions' => array(
            "0" => "No end date.",
            "1" => "End voting on a specific date.(Please select date by clicking on the calendar icon below.)",
        ),
        'value' => 0
    ));
    $date = (string) date('Y-m-d');
    $this->addElement('CalendarDateTime', 'end_time', array(
        'value' => $date . ' 00:00:00',
    ));

    $this->addElement('Checkbox', 'search', array(
        'label' => "Show this poll in search results.",
        'value' => 1,
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Create Poll',
        'type' => 'submit',
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formButtonCancel.tpl',
                    'class' => 'form element')))
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper',
        ),
    ));
  }

}
?>