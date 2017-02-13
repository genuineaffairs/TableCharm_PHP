<?php
class Advgroup_Form_Member_Remove extends Engine_Form
{
	
	protected $_group_id;
	public function setGroup($group_id) {
		$this->_group_id = $group_id;
	}
	
	protected $_user_id;
	public function setUser($user_id) {
		$this->_user_id = $user_id;
	}
	protected $_ftitle;
	public function setFtitle($title) {
		$this->_ftitle = $title;
	}
	
	public function init()
	{
		
		
		$this->setTitle('Remove Member');
		if($this->_ftitle == 1)	
			$this->setDescription("Are you sure you want to remove this member from the group? It will remove this member from all it 's sub-groups too.");
		elseif($this->_ftitle == 2)
			$this->setDescription("Are you sure you want to remove this member from the group?");
		else
			$this->setDescription("None");
		//$this->addElement('Hash', 'token');

		$this->addElement('Cancel', 'remove', array(

				'label' => 'Remove Member',
				'link' => true,
				'class' => 'group_viewmore',
				'href' => '',
				'onclick' =>"parent.removeMember($this->_group_id, $this->_user_id, $this->_ftitle );",				
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
				'remove',
				'cancel'
		), 'buttons');
	}
}