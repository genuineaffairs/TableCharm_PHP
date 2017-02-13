<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Resume_Form_Coachinghistory_Create extends Engine_Form
{
  public $_error = array();

  public function init()
  {
    $user = Engine_Api::_()->user()->getViewer();
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
    
    $this->setTitle($this->getTranslator()->translate('Add New Coaching History'))
      ->setDescription($this->getTranslator()->translate('Compose your history below, then click "Save History" to add your history.'))
      ->setAttrib('name', 'resume_history_create');
    
    // Team Name
    $this->addElement('Text', 'team_name', array(
      'label' => $this->getTranslator()->translate('Team'),
      //'description' => '',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '127')),
    )));
    $this->team_name->getDecorator("Description")->setOption("placement", "append");    
    
    // Position
    $this->addElement('Text', 'position', array(
      'label' => $this->getTranslator()->translate('Position Held'),
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '127')),
    )));
    $this->position->getDecorator("Description")->setOption("placement", "append");       
    
    // Location
    $location_required = true;
    $this->addElement('Text', 'location', array(
      'label' => 'Location',
      'description' => 'Example: Los Angeles, CA 90071',
      'allowEmpty' => !$location_required,
      'required' => $location_required,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 128)),
        new Radcodes_Lib_Validate_Location_Address(),
      ),
      'filters' => array(
        'StripTags'
      ),
    ));       
    $this->location->getDecorator("Description")->setOption("placement", "append");
    
    // Grade of Competition
    $this->addElement('Text', 'competition', array(
      'label' => $this->getTranslator()->translate('Grade of Competition'),
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '127')),
    )));
    $this->competition->getDecorator("Description")->setOption("placement", "append");       

    
    $this->addElement('Checkbox', 'is_current', array(
      'label' => 'I currently work here',
      'value' => 1,
    ));
    
    $this->addElement('Date', 'start_date', array(
      'label' => 'Start Date',
    ));
    
    $this->addElement('Date', 'end_date', array(
      'label' => 'End Date',
    ));    

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => $this->getTranslator()->translate('Save Coaching History'),
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
    $button_group->addDecorator('DivDivDivWrapper');    

    
  }
  
  public function getValues()
  {
    $values = parent::getValues();
    if (!$values['start_date']) {
      $values['start_date'] = '0000-00-00';
    }
    if (!$values['end_date'] || $values['is_current']) {
      $values['end_date'] = '0000-00-00';
    }
    return $values;
  }
}