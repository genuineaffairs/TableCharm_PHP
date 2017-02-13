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
 
 
 
class Resume_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');
    
    $this->addElement('Text', 'resume_license', array(
      'label' => 'Resume License Key',
      'description' => 'Enter the your license key that is provided to you when you purchased this plugin. If you do not know your license key, please contact Radcodes support team.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('resume.license', 'XXXX-XXXX-XXXX-XXXX'),
      'filters' => array(
        'StringTrim'
      ),
      'allowEmpty' => false,
      'validators' => array(
        new Radcodes_Lib_Validate_License('resume'),
      ),
    ));
     
      
    $this->addElement('Text', 'resume_perpage', array(
      'label' => 'Resumes Per Page',
      'description' => 'How many resumes will be shown per page? (Enter a number between 1 and 100)',
      'class' => 'short',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('resume.perpage', 10),
      'validators' => array(
        'Digits',
        new Zend_Validate_Between(1,100),
      ),
    ));

    $this->addElement('Radio', 'resume_preorder', array(
      'label' => 'Pre-Ordering Priority',
      'description' => "You can force sponsored / featured resumes to display on top of browse page by selecting one of the following",
      'multiOptions' => array(
				0 => "User preference",
        1 => "Sponsored resumes, then user preference",
        2 => "Sponsored resumes, featured resumes, then user preference",
				3 => "Featured resumes, then user preference",
				4 => "Featured resumes, sponsored resumes, then user preference",
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('resume.preorder', 1),
    ));
    
    
    $this->addElement('Radio', 'resume_distanceunit', array(
      'label' => 'Distance Unit',
      'description' => "What unit would be used for proximity search?",
      'multiOptions' => array(
        Radcodes_Lib_Helper_Unit::UNIT_MILE => "Mile (ml)",
        Radcodes_Lib_Helper_Unit::UNIT_KILOMETER => "Kilometer (km)",
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('resume.distanceunit', Radcodes_Lib_Helper_Unit::UNIT_MILE),
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}