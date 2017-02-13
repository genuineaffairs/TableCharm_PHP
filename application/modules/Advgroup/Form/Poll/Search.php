<?php
class Advgroup_Form_Poll_Search extends Engine_Form
{
  public function init()
  {
   //Form Attribute and Method
    $this->setAttribs(array('id' => 'filter_form',
                            'class' => 'global_form_box',))
         ->setMethod('GET')
         ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page' => null)));


    $this->addElement('Hidden','page');
    //Search Text
    $this->addElement('Text', 'search', array(
      'label' => 'Search Polls',
    ));

    //Closed
    $this->addElement('Select', 'closed', array(
      'label' => 'Status',
      'multiOptions' => array(
        '' => 'All Polls',
        '0' => 'Only Open Polls',
        '1' => 'Only Closed Polls',
      ),
    ));

    //Order
    $this->addElement('Select', 'order', array(
      'label' => 'Browse By',
      'multiOptions' => array(
        'recent' => 'Most Recent',
        'popular' => 'Most Popular',
      ),
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Search',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
  }
}