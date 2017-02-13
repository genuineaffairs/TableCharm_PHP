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
class Resume_Form_Photo_Delete extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Delete Resume Photo')
      ->setDescription('Are you sure you want to delete this photo?')
      ;

    $this->addElement('Button', 'submit', array(
      'type' => 'submit',
      'label' => 'Delete Photo',
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_resume = $this->getDisplayGroup('buttons');
  }
}