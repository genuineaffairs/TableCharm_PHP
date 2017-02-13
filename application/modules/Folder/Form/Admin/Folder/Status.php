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
 
 
 
class Folder_Form_Admin_Folder_Status extends Engine_Form
{

  public function init()
  {
    $this->setTitle('Update Folder Status')
      ->setDescription("This form allows you to modify the folder's status.")
      ->setAttrib('class', 'global_form_popup')
      ;

    $this->addElement('Select', 'status', array(
      'label' => 'Status',
      'allowEmpty' => false,
      'required' => true,
      'multiOptions' => Folder_Model_Folder::getStatusTypes(),
    ));
    
    $this->addElement('Radio', 'status_settings', array(
      'label' => 'Change Date',
      'multiOptions' => array(
        0 => 'Update to current date time',
        1 => 'Use custom date time below',
      ),
      'value' => 1
    ));    
    
    $this->addElement('CalendarDateTime', 'status_date', array(
      'label' => 'Custom Date',
      'allowEmpty' => false,
      'required' => true,
    ));
    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'folder' => true,
      'prependText' => ' or ',
      'href' => '',
      'link' => true,
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}