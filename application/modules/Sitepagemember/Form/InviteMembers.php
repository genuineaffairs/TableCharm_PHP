<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagemember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: InviteMembers.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 
class Sitepagemember_Form_InviteMembers extends Engine_Form {

  public function init() {
  
		$coreSettings = Engine_Api::_()->getApi('settings', 'core');
		$memberSettings = $coreSettings->getSetting( 'pagemember.automatically.addmember' , 1);
		if (!empty($memberSettings)) {
			$this->setTitle('Add People to this Page');
			$this->setDescription('Select the people you want to add to this page.')
			->setAttrib('id', 'messages_compose');
			$Button = 'Add People';
		} else {
			$this->setTitle('Invite People to this Page');
			$this->setDescription('Select the members you want to invite to this page.')
			->setAttrib('id', 'messages_compose');;
			$Button = 'Invite People';
		}


    // init to
    $this->addElement('Text', 'user_ids',array(
			'label'=>'Start typing the name of the member...',
			'autocomplete'=>'off'
    ));
    Engine_Form::addDefaultDecorators($this->user_ids);

    // Init to Values
    $this->addElement('Hidden', 'toValues', array(
      'label' => '',
      'order' => '5',
      'filters' => array(
        'HtmlEntities'
      ),
    ));
    Engine_Form::addDefaultDecorators($this->toValues);

    $this->addElement('Button', 'submit', array(
      'label' => $Button,
      'ignore' => true,
      'order' => '8',
      'decorators' => array('ViewHelper'),
      'type' => 'submit'
    ));

    $this->addElement('Cancel', 'cancel', array(
      'prependText' => ' or ',
      'label' => 'cancel',
      'link' => true,
      'order' => '9',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      ),
    ));
  }
}