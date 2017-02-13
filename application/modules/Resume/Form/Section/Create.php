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
 
 
 
class Resume_Form_Section_Create extends Engine_Form
{

  
  public function init()
  {
    $this->setTitle('Add New Section')
      ->setDescription('Please select new section to add to your resume.')
      ;

    $resume = Engine_Api::_()->core()->getSubject();
    
    $sectionTable = Engine_Api::_()->getItemTable('resume_section');
    $sections = $sectionTable->getCoreSections(array('enabled' => 1));
    $sectionOptions = array();

    foreach ($sections as $section) {
      $default_in_categories = json_decode($section->default_in_categories, true);
      // Only insert sections related to participation level
      if (is_array($default_in_categories) && in_array($resume->category_id, $default_in_categories)) {
        $sectionOptions[$section->getIdentity()] = $section->getTitle();
      }
    }

    // If no section was inserted, then insert player's sections by default
    if (empty($sectionOptions)) {
      $player_category_id = Resume_Model_DbTable_Categories::PLAYER_CATEGORY_ID;
      foreach ($sections as $section) {
        $default_in_categories = json_decode($section->default_in_categories, true);
        if (is_array($default_in_categories) && in_array($player_category_id, $default_in_categories)) {
          $sectionOptions[$section->getIdentity()] = $section->getTitle();
        }
      }
    }

    $this->addElement('Select', 'section_id', array(
      'label' => 'Section Type',
      'allowEmpty' => false,
      'required' => true,
      'attribs' => array(
        'class' => 'text'
      ),
      'multiOptions' => array(""=>"") + $sectionOptions
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