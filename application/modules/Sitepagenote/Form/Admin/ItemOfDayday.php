<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ItemOfDayday.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_Form_Admin_ItemOfDayday extends Engine_Form {

  protected $_field;

  public function init() {
    $this->setMethod('post');
    $this->setTitle('Add a Note of the Day')
            ->setDescription('Select a start date and end date below and the corresponding Note from the auto-suggest Note field. The selected Note will be displayed as "Note of the Day" for this duration and if more than one notes are found to be displayed in the same duration then they will be dispalyed randomly one at a time.');

    $label = new Zend_Form_Element_Text('title');
    $label->setLabel('Note')
            ->addValidator('NotEmpty')
            ->setRequired(true)
            ->setAttrib('class', 'text')
            ->setAttrib('style', 'width:300px;');

    // init to
    $this->addElement('Hidden', 'resource_id', array());

    $this->addElements(array(
        $label,
    ));

    $starttime = new Engine_Form_Element_CalendarDateTime('starttime');
    $starttime->setLabel("Start Date");
    $starttime->setAllowEmpty(false);
    $starttime->setValue(date('Y-m-d H:i:s'));
    $this->addElement($starttime);


    //Start End date work
    $endtime = new Engine_Form_Element_CalendarDateTime('endtime');
    $endtime->setLabel("End Date");
    $endtime->setAllowEmpty(false);
    $endtime->setValue(date('Y-m-d H:i:s'));
    $this->addElement($endtime);
    //End End date work
    // Buttons
    $this->addElement('Button', 'submit', array(
        'label' => 'Add Item',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        //'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'sitepage_general', true),
        'onclick' => 'javascript:parent.Smoothbox.close()',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}
?>