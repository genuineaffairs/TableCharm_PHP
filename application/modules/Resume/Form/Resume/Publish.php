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
 
 
 
class Resume_Form_Resume_Publish extends Engine_Form
{

  public function init()
  {
    $this
      ->setTitle('Publish Resume?')
      ->setDescription("Would you like to publish this resume now? Once it has been published, you cannot set it to DRAFT mode again. Please review your resume carefully before publishing it.")
      ;

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Publish Now',
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