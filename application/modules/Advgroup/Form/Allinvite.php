<?php
class Advgroup_Form_Allinvite extends Engine_Form
{
	public function init()
	{
		$this
		->setTitle('Invite Members')
		->setDescription('Choose the people you want to invite to this group.')
		->setAttrib('id', 'group_form_users_invite')
		->setAttrib('action','javascript:;')
		;

		$this->addElement('Text','users_search',array(
				'description' =>'(filter the search box and press Enter to search)',
				'onkeypress' => 'updateUserList(event, "users")',
		));
		$this->users_search->getDecorator("Description")->setOption("placement", "append");

		$this->addElement('Checkbox', 'all', array(
				'id' => 'userselectall',
				'label' => 'Choose All',
				'ignore' => true
		));

		$this->addElement('MultiCheckbox', 'users', array(
				'label' => 'Members',
		));

		$this->addElement('Button', 'button', array(
				'label' => 'Send Invites',
				'onClick'=>'submitForm("users")',
				'ignore' => true,
				'decorators' => array(
						'ViewHelper',
				),
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

		$this->addDisplayGroup(array('button', 'cancel'), 'users_buttons');
	}
}