<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 6590 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_Form_Create extends Engine_Form {

  protected $_parent_type;
  protected $_parent_id;

  public function setParent_type($value) {
    
    $this->_parent_type = $value;
  }

  public function setParent_id($value) {
    
    $this->_parent_id = $value;
  }

  public function init() {

    $user = Engine_Api::_()->user()->getViewer();
    $viewer_id = $user->getIdentity();
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $tab_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab_id', null);
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $url = $view->item('sitepage_page', $page_id)->getHref(array('tab'=>$tab_id));

    $this->setTitle('Create_New_Event')
            ->setDescription('Create a new event for this Page by filling the information below, then click "Post Event".')
            ->setAttrib('id', 'event_create_form')
            ->setMethod("POST")
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $this->addElement('Text', 'title', array(
        'label' => 'Event_Name',
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('NotEmpty', true),
            array('StringLength', false, array(1, 64)),
        ),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_EnableLinks(),
        ),
    ));

    $title = $this->getElement('title');

//     $this->addElement('Textarea', 'description', array(
//         'label' => 'Description',
//         'maxlength' => '512',
//         'filters' => array(
//             new Engine_Filter_Censor(),
//         ),
//     ));


   $this->addElement('Textarea', 'description', array(
        'label' => 'Description',
//         'required' => true,
         'filters' => array(
              'StripTags',
              new Engine_Filter_HtmlSpecialChars(),
              new Engine_Filter_EnableLinks(),
              new Engine_Filter_Censor(),
          ),
    ));

    $start = new Engine_Form_Element_CalendarDateTime('starttime');
    $start->setLabel("Start Date");
    $start->setAllowEmpty(false);
    $this->addElement($start);

    $end = new Engine_Form_Element_CalendarDateTime('endtime');
    $end->setLabel("End Date");
    $end->setAllowEmpty(false);
    $this->addElement($end);

    $this->addElement('Text', 'host', array(
        'label' => 'Host',
        'filters' => array(
            new Engine_Filter_Censor(),
        ),
    ));

    $this->addElement('Text', 'location', array(
        'label' => 'Location',
        'filters' => array(
            new Engine_Filter_Censor(),
        ),
    ));

    $this->addElement('File', 'photo', array(
        'label' => 'Main Photo'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif');
    
    // Category
    $this->addElement('Select', 'category_id', array(
      'label' => 'Event Category',
      'multiOptions' => array(
        '0' => ' '
      ),
    ));    

    $this->addElement('Checkbox', 'search', array(
        'label' => 'Circle member can search for this Page Event.',
        'value' => True
    ));

    $this->addElement('Checkbox', 'approval', array(
        'label' => 'People must be invited to RSVP for this Page Event.',
    ));

		
		
		$pagemember = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
		if (!empty($pagemember)) {
			$select = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id, $page_id);
			$pageasgroup = Engine_Api::_()->getApi('settings', 'core')->getSetting( 'pagemember.pageasgroup');
		}
		
		
		if ( empty( $pageasgroup ) && empty($pagemember)) {
			$this->addElement('Checkbox', 'auth_invite', array(
				'label' => 'Invited guests can invite other people as well.',
				'value' => True
			));
		}
		elseif (!empty($select)) {
			$this->addElement('Checkbox', 'all_members', array(
				'label' => 'Invite all Page Members.',
				'value' => True
			));
		}


    $this->addElement('Button', 'submit', array(
        'label' => 'Post_Event',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => $url,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper',
        ),
    ));
  }

}

?>