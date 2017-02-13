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
 
 
 
class Folder_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');
    
    $this->addElement('Text', 'folder_license', array(
      'label' => 'Folder License Key',
      'description' => 'Enter the your license key that is provided to you when you purchased this plugin. If you do not know your license key, please contact Radcodes support team.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('folder.license', 'XXXX-XXXX-XXXX-XXXX'),
      'filters' => array(
        'StringTrim'
      ),
      'allowEmpty' => false,
      'validators' => array(
        new Radcodes_Lib_Validate_License('folder'),
      ),
    ));
     
      
    $this->addElement('Text', 'folder_perpage', array(
      'label' => 'Folders Per Page',
      'description' => 'How many folders will be shown per page? (Enter a number between 1 and 100)',
      'class' => 'short',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('folder.perpage', 10),
      'validators' => array(
        'Digits',
        new Zend_Validate_Between(1,100),
      ),
    ));
    
    
    $this->addElement('Radio', 'folder_preorder', array(
      'label' => 'Pre-Ordering Priority',
      'description' => "You can force sponsored / featured folders to display on top of browse page by selecting one of the following",
      'multiOptions' => array(
        0 => "User preference",
        1 => "Sponsored folders, then user preference",
        2 => "Sponsored folders, featured folders, then user preference",
        3 => "Featured folders, then user preference",
        4 => "Featured folders, sponsored folders, then user preference",
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('folder.preorder', 0),
    ));
    
    $this->addElement('Text', 'folder_enabletypes', array(
      'label' => 'Allowed Parent Types',
      'description' => 'You can optionally only allow certain item types that can be integreted with folder/file by entering them in field below, seperated each by comma. For example: user,group,event,classified,blog,article,job,listing,pet etc.. Leave it blank to skip this option.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('folder.enabletypes', ''),
    ));
    /*
    $this->addElement('Text', 'folder_disabletypes', array(
      'label' => 'Disabled Parent Types',
      'description' => 'You can optionally disable certain item types from having folder/file integration by entering them in field below, seperated each by comma. For example: activity,comment,tag etc.. Leave it blank to skip this option.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('folder.enabletypes', ''),
    ));    
    */
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}