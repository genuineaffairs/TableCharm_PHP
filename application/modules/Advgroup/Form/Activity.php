<?php
/**
 *
 * YouNet Company
 * 
 * @category   Application_Extensions
 * @package    Yngroupactivity
 * @author    YouNet Company
 */

class Advgroup_Form_Activity extends Engine_Form
{
  public function init()
  {
    $this -> setTitle('Public Activities')
          -> setDescription('Choose the activity action types that you want to public on the group activity.')
          -> setAttrib('id', 'group_form_activity');
    
    $this->addElement('Checkbox', 'all', array(
      'id' => 'selectall',
      'label' => 'Choose All Activities',
      'ignore' => true
    ));

    $this->addElement('MultiCheckbox', 'activities', array(
      'label' => 'Activities',
//      'required' => true,
//      'allowEmpty' => 'false',
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper',),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}