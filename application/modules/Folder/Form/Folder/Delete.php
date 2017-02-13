<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @industry   Application_Extensions
 * @package    Folder
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Folder_Form_Folder_Delete extends Engine_Form
{

  public function init()
  {
    $this
      ->setTitle('Delete Folder?')
      ->setDescription('Are you sure that you want to delete this folder? It will not be recoverable after being deleted.')
      ;

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Delete',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));


    $this->addDisplayGroup(array('submit'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}