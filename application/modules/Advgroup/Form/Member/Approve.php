<?php
class Advgroup_Form_Member_Approve extends Engine_Form
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
    $this
      ->setTitle('Approve Group Membership Request')
      ->setDescription('Would you like to approve the request for membership in this group?')
      ->setMethod('POST')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    //$this->addElement('Hash', 'token');

//     $this->addElement('Button', 'submit', array(
//       'label' => 'Approve Request',
//       'ignore' => true,
//       'decorators' => array('ViewHelper'),
//       'type' => 'submit'
//     ));
    $this->addElement('Cancel', 'approveRequest', array(
    
    		'label' => 'Approve Request',
    		'link' => true,
    		'class' => 'group_viewmore',
    		'href' => '',
    		'onclick' =>"parent.approveRequest($this->_group_id, $this->_user_id);",
    		'decorators' => array(
    				'ViewHelper'
				),
    		));

    $this->addElement('Cancel', 'cancel', array(
      'prependText' => ' or ',
      'label' => 'cancel',
      'link' => true,
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      ),
    ));

    $this->addDisplayGroup(array(
      'approveRequest',
      'cancel'
    ), 'buttons');
  }
}