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
 
 
 
class Folder_Form_Admin_Folder_Package extends Engine_Form
{

  public function init()
  {
    $this->setTitle('Update Folder Package')
      ->setDescription("To change a folder's package to another one, just select the new package you would to assign the folder to.")
      ->setAttrib('class', 'global_form_popup')
      ;

    $this->addElement('Select', 'package_id', array(
      'label' => 'Package',
      'allowEmpty' => false,
      'required' => true,
      'multiOptions' => Engine_Api::_()->folder()->getPackageOptions(),
    ));
    
    
    $this->addElement('Radio', 'featured_update', array(
      'label' => 'Featured',
      'multiOptions' => array(
        0 => "Do not update - leave as it is",
        1 => "Use selected package's featured setting",
      ),
      'value' => 0
    ));    
    
    $this->addElement('Radio', 'sponsored_update', array(
      'label' => 'Sponsored',
      'multiOptions' => array(
        0 => "Do not update - leave as it is",
        1 => "Use selected package's sponsored setting",
      ),
      'value' => 0
    ));
    
    $this->addElement('Radio', 'expiration_update', array(
      'label' => 'Expiration',
      'multiOptions' => array(
        0 => "Do not update - leave as it is",
        1 => "Update using with today as starting date",
        2 => "Update using with status date as starting date",
      ),
      'value' => 0
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