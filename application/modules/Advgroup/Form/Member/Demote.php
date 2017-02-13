<?php
class Advgroup_Form_Member_Demote extends Engine_Form
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
		->setTitle('Demote Member')
		->setDescription('Are you sure you want to demote this member from officer?')
		;

		//$this->addElement('Hash', 'token');

		//     $this->addElement('Button', 'submit', array(
		//       'type' => 'submit',
		//       'ignore' => true,
		//       'decorators' => array('ViewHelper'),
		//       'label' => 'Demote Member',
		//     ));
		$this->addElement('Cancel', 'demote', array(

				'label' => 'Demote Member',
				'link' => true,
				'class' => 'group_viewmore',
				'href' => '',
				'onclick' =>"parent.demoteMember($this->_group_id, $this->_user_id);",
				//'onclick' => '',
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
				'demote',
				'cancel'
		), 'buttons');
	}
}