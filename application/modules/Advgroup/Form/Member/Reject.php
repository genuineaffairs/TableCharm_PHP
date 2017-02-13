<?php
class Advgroup_Form_Member_Reject extends Engine_Form
{
	
	protected $_group_id;
	public function setGroup($group_id) {
		$this->_group_id = $group_id;
	}
	
	protected $_user_id;
	public function setUser($user_id) {
		$this->_user_id = $user_id;
	}
  public function init()
  {
    $this->setTitle('Reject Group Invitation')
      ->setDescription('Would you like to reject the invitation to this group?')
      ->setAttrib('class', 'global_form_popup')
      ->setMethod('POST')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;

    $this->addElement('Hash', 'token');

    $this->addElement('Button', 'submit', array(
      'label' => 'Reject Invitation',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'type' => 'submit'
    ));

//     $this->addElement('Cancel', 'rejectInvitation', array(
    
//     		'label' => 'Reject Invitation',
//     		'link' => true,
//     		'class' => 'group_viewmore',
//     		'href' => '',
//     		'onclick' =>"parent.rejectInvitation($this->_group_id, $this->_user_id);",
//     		'decorators' => array(
//     				'ViewHelper'
//     		),
//     ));
    
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
    
  }
}