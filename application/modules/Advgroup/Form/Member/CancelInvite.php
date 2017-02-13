<?php
class Advgroup_Form_Member_CancelInvite extends Engine_Form
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
      ->setTitle('Cancel Invitation')
      ->setDescription('Would you like to cancel your invitation for member in this group?')
      ->setMethod('POST')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    //$this->addElement('Hash', 'token');

    $this->addElement('Button', 'submit', array(
      'label' => 'Cancel Invite',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'type' => 'submit'
    ));
/*
    $this->addElement('Cancel', 'cancelInvite', array(

    		'label' => 'Cancel Invite',
    		'link' => true,
    		'class' => 'group_viewmore',
    		'href' => '',
    		'onclick' =>"parent.cancelInvite($this->_group_id, $this->_user_id );",
    		//'onclick' => '',
    		'decorators' => array(
    				'ViewHelper'
    		),
    ));
*/
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
      'submit',
      'cancel'
    ), 'buttons');
  }
}