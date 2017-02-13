<?php
class Advgroup_Form_Member_Promote extends Engine_Form
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
		->setTitle('Promote Member')
		->setDescription('Are you sure you want to promote this member to officer?')
		;

		//$this->addElement('Hash', 'token');

		//     $this->addElement('Button', 'submit', array(
		//       'type' => 'submit',
		//       'ignore' => true,
		//       'decorators' => array('ViewHelper'),
		//       'label' => 'Promote Member',
		//     ));

		$this->addElement('Cancel', 'promote', array(

				'label' => 'Promote Member',
				'link' => true,
				'class' => 'group_viewmore',
				'href' => '',
				'onclick' =>"parent.promoteMember($this->_group_id, $this->_user_id );",
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
				'promote',
				'cancel'
		), 'buttons');
	}
}