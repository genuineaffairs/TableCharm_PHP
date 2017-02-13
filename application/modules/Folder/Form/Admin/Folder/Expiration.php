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
 
 
 
class Folder_Form_Admin_Folder_Expiration extends Engine_Form
{

  public function init()
  {
    $this->setTitle('Update Folder Expiration')
      ->setDescription("This form allows you to change the folder's expiration date. The changes that you make on this page will overwrite the package's duration settings.")
      ->setAttrib('class', 'global_form_popup')
      ;

    $this->addElement('Radio', 'expiration_settings', array(
      'label' => 'Expiration',
      'multiOptions' => array(
        0 => 'Unlimited - no expiration',
        1 => 'Will be expired on selected date below',
      ),
    ));
    
    $this->addElement('CalendarDateTime', 'expiration_date', array(
      'label' => 'Date',
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