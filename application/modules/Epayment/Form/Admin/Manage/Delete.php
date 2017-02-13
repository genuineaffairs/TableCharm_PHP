<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @package   Application_Extensions
 * @package    Job
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Epayment_Form_Admin_Manage_Delete extends Engine_Form
{
  
  public function init()
  {
    $this
      ->setTitle('Delete Payment?')
      ->setDescription('Are you sure that you want to delete this payment? It will not be recoverable after being deleted.')
      ;

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Delete',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'view',
      'job' => true,
      'prependText' => ' or ',
      'link' => true,
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'view')),
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}