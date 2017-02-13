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
 
 
 
class Resume_Form_Award_Create extends Engine_Form
{
  public $_error = array();

  public function init()
  {
    $user = Engine_Api::_()->user()->getViewer();
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
    
    $this->setTitle($this->getTranslator()->translate('Add New Honours or Awards'))
      ->setAttrib('name', 'resume_activity_create');
    
    // Team Name
    $this->addElement('Text', 'award', array(
      'label' => $this->getTranslator()->translate('Honours or Awards'),
      //'description' => '',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '127')),
    )));
    $this->award->getDecorator("Description")->setOption("placement", "append");    
    
    // Year
    $date = new Zend_Date();
    $year = (int) $date->get(Zend_Date::YEAR);
    $ranges = range($year + 10, 1900);
    $year_ranges = array_combine($ranges, $ranges);
    
    $this->addElement('Select', 'year', array(
      'label' => 'Year',
      'allowEmpty' => false,
      'required' => true,
      'multiOptions' => array(""=>"") + $year_ranges
    ));
    
    // Competition or League
    $this->addElement('Text', 'competition', array(
      'label' => $this->getTranslator()->translate('Competition or League'),
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '127')),
    )));
    $this->competition->getDecorator("Description")->setOption("placement", "append");       
    
    // Level
    $this->addElement('Text', 'level', array(
      'label' => $this->getTranslator()->translate('Level'),
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '127')),
    )));
    $this->level->getDecorator("Description")->setOption("placement", "append");       

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => $this->getTranslator()->translate('Save Award'),
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