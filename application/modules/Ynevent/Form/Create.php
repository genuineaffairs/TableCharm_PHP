<?php

class Ynevent_Form_Create extends Engine_Form
{
  protected $_parent_type;

  protected $_parent_id;
    
  
  public function setParent_type($value)
  { 
    $this->_parent_type = $value;
  }

  public function setParent_id($value)
  {
    $this->_parent_id = $value;
  }

  public function isValid($data) {
  	
	if($data['repeat_type']== 1)
	{
		$this->getElement('repeatend')->addValidator('NotEmpty');
	} else {
		unset($data['repeatend']);
	}
	
	return parent::isValid($data);
  }
  public function init()
  {
	 $this
      ->addPrefixPath('Ynevent_Form_Decorator', APPLICATION_PATH . '/application/modules/Ynevent/Form/Decorator', 'decorator')
      ->addPrefixPath('Ynevent_Form_Element', APPLICATION_PATH . '/application/modules/Ynevent/Form/Element', 'element')
      ->addElementPrefixPath('Ynevent_Form_Decorator', APPLICATION_PATH . '/application/modules/Ynevent/Form/Decorator', 'decorator');
	$view = Zend_Registry::get('Zend_View');  	
  	
    $user = Engine_Api::_()->user()->getViewer();

    $this->setTitle('Create New Event')
      ->setAttrib('id', 'ynevent_create_form')
      ->setMethod("POST")
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
      
    // Title
    $this->addElement('Text', 'title', array(
      'label' => 'Event Name',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 64)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));

    $title = $this->getElement('title');

    // Brief Description
    $this->addElement('Textarea', 'brief_description', array(
      'label' => 'Brief Description',
      'description' => 'Max 250 characters',
      'maxlength' => '250',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_StringLength(array('max' => 250)),
      ),
    ));
    $this->brief_description->getDecorator('Description')->setOption('placement', 'append');
	
	
    // Description
    $this->addElement('Textarea', 'description', array(
      'label' => 'Event Description',
      'maxlength' => '10000',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_StringLength(array('max' => 10000)),
      ),
    ));

    // Start time
    $start = new Engine_Form_Element_CalendarDateTime('starttime');
    $start->setLabel("Start");
    $start->setAllowEmpty(false);
    $this->addElement($start);

    // End time
    $end = new Engine_Form_Element_CalendarDateTime('endtime');
    $end->setLabel("End");
    $end->setAllowEmpty(false);
    $this->addElement($end);
    
	$this -> addElement('Radio', 'repeat_type', array(
            'label' => 'Please Select',
            'multiOptions' => array(
                '0' => 'One Time Event',
                '1' => 'Repeating Event'
            ),
            'value' => 0,
            'onclick'=>'isrepeat(this)',
        ));
		
    
	$end_repeat = new Engine_Form_Element_CalendarDateTime('repeatend');
    $end_repeat->setLabel("End Repeat");
    $end_repeat->setAllowEmpty(true);
    $this->addElement($end_repeat);
	
	$this -> addElement('Select', 'repeat_frequency', array(
            'label' => 'Repeat',
            'multiOptions' => array(
                '1' => 'Daily',
                '7' => 'Weekly',
                '30' => 'Monthly',				
            ),
        ));
	
    // Capacity
    $this->addElement('Text', 'capacity', array(
      'label' => 'Event Capacity',
      'description' => 'Set 0 for unlimited participants',
      'allowEmpty' => false,
      'required' => true,
      'value' => 0,
      'validators'  => array(
			array('Int', true),
			new Engine_Validate_AtLeast(0),
	  ),
    ));
    $this->capacity->getDecorator('Description')->setOption('placement', 'append');
	
    // Capacity
    $this->addElement('Text', 'price', array(
      'label' => 'Price',
      'description' => 'Set 0 for free',
      'allowEmpty' => false,
      'required' => true,
      'value' => 0,
      'validators'  => array(
			array('Float', true),
			new Engine_Validate_AtLeast(0),
	  ),
    ));
    $this->price->getDecorator('Description')->setOption('placement', 'append');
    
    // Location
	$this -> addElement('Text', 'location', array(
		'label' => 'Location Name',
		'required' => false,
		'filters' => array(new Engine_Filter_Censor())
	));
    
    // Address
	$this -> addElement('Text', 'full_address', array(
		'label' => 'Full Address',
		'required' => false,
		'readonly' => true,
		'style' => 'width: 400px;',
		'description' => $view -> htmlLink(array(
			'action' => 'add-location',
			'route' => 'event_general',
			'reset' => true,
		), $view -> translate('Add address/city/zip/country'), array('class' => 'smoothbox')),
		'filters' => array(new Engine_Filter_Censor())
	));
	$this -> full_address -> getDecorator("Description") -> setOption("placement", "append") -> setEscape(FALSE);
        
    // Host
    if ($this->_parent_type == 'user') 
    {
      $this->addElement('Text', 'host', array(
        'label' => 'Host',
        'filters' => array(
          new Engine_Filter_Censor(),
        ),
      ));
    }

    // Photo
    $this->addElement('File', 'photo', array(
      'label' => 'Main Photo'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');

    // Category
    $this->addElement('MultiLevel2', 'category_id', array(
      'label' => 'Event Category',
         'multiOptions' => (array)Engine_Api::_()->getDbTable('categories','ynevent')->getMultiOptions('..','All'),
         'model'=>'Ynevent_Model_DbTable_Categories',
         'isSearch'=>0,
         'module'=>'ynevent',
    ));
    
    // Email
    $this->addElement('Text', 'email', array(
      'label' => 'Email',
      'allowEmpty' => true,
      'required' => false,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
      'validators' => array(
        array('NotEmpty', true),
        array('EmailAddress', true),
      )
    ));
    
    // Url
    $this->addElement('Text', 'url', array(
      'label' => 'Url',
      'allowEmpty' => true,
      'required' => false,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
    
    // Phone
    $this->addElement('Text', 'phone', array(
      'label' => 'Phone',
      'allowEmpty' => true,
      'required' => false,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
    
    // Contact Information
    $this->addElement('Text', 'contact_info', array(
      'label' => 'Contact Information',
      'allowEmpty' => true,
      'required' => false,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
    
    // Search
    $this->addElement('Checkbox', 'search', array(
      'label' => 'People can search for this event',
      'value' => True
    ));

    // Approval
    $this->addElement('Checkbox', 'approval', array(
      'label' => 'People must be invited to RSVP for this event',
    ));

    // Invite
    $this->addElement('Checkbox', 'auth_invite', array(
      'label' => 'Invited guests can invite other people as well',
      'value' => True
    ));
    
    
    // Invite
    $this->addElement('Checkbox', 'group_invite', array(
      'label' => 'Invited guests can invite groups as well',
      'value' => True
    ));
    
    // Privacy
    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('event', $user, 'auth_view');
    $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('event', $user, 'auth_comment');
    $photoOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('event', $user, 'auth_photo');
    $videoOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('event', $user, 'auth_video');
    
    if( $this->_parent_type == 'user' ) {
      $availableLabels = array(
        'everyone'            => 'Everyone',
        'registered'          => 'All Registered Members',
        'owner_network'       => 'Friends and Networks',
        'owner_member_member' => 'Friends of Friends',
        'owner_member'        => 'Friends Only',
        'member'              => 'Event Guests Only',
        'owner'               => 'Just Me'
      );
      $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
      $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
      $photoOptions = array_intersect_key($availableLabels, array_flip($photoOptions));
      $videoOptions = array_intersect_key($availableLabels, array_flip($videoOptions));

    } else if( $this->_parent_type == 'group' ) {

      $availableLabels = array(
        'everyone'      => 'Everyone',
        'registered'    => 'All Registered Members',
        'parent_member' => 'Group Members',
        'member'        => 'Event Guests Only',
        'owner'         => 'Just Me',
      );
      $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
      $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
      $photoOptions = array_intersect_key($availableLabels, array_flip($photoOptions));
      $videoOptions = array_intersect_key($availableLabels, array_flip($videoOptions));
    }

    // View
    if( !empty($viewOptions) && count($viewOptions) >= 1 ) {
      // Make a hidden field
      if(count($viewOptions) == 1) {
        $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_view', array(
            'label' => 'Privacy',
            'description' => 'Who may see this event?',
            'multiOptions' => $viewOptions,
            'value' => key($viewOptions),
        ));
        $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    // Comment
    if( !empty($commentOptions) && count($commentOptions) >= 1 ) {
      // Make a hidden field
      if(count($commentOptions) == 1) {
        $this->addElement('hidden', 'auth_comment', array('value' => key($commentOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_comment', array(
            'label' => 'Comment Privacy',
            'description' => 'Who may post comments on this event?',
            'multiOptions' => $commentOptions,
            'value' => key($commentOptions),
        ));
        $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
      }
    }

    // Photo
    if( !empty($photoOptions) && count($photoOptions) >= 1 ) {
      // Make a hidden field
      if(count($photoOptions) == 1) {
        $this->addElement('hidden', 'auth_photo', array('value' => key($photoOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_photo', array(
            'label' => 'Photo Uploads',
            'description' => 'Who may upload photos to this event?',
            'multiOptions' => $photoOptions,
            'value' => key($photoOptions)
        ));
        $this->auth_photo->getDecorator('Description')->setOption('placement', 'append');
      }
    }
	
  	// Video
    if( !empty($videoOptions) && count($videoOptions) >= 1 ) {
      // Make a hidden field
      if(count($videoOptions) == 1) {
        $this->addElement('hidden', 'auth_video', array('value' => key($videoOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_video', array(
            'label' => 'Video Creation',
            'description' => 'Who may create videos for this event?',
            'multiOptions' => $videoOptions,
            'value' => key($videoOptions),
        ));
        $this->auth_video->getDecorator('Description')->setOption('placement', 'append');
      }
    }

	$this -> addElement('Hidden', 'address', array('order' => '1'));
	$this -> addElement('Hidden', 'city', array('order' => '2'));
	$this -> addElement('Hidden', 'country', array('order' => '3'));
	$this -> addElement('Hidden', 'zip_code', array('order' => '4'));
	$this -> addElement('Hidden', 'latitude', array('order' => '5'));
	$this -> addElement('Hidden', 'longitude', array('order' => '6'));
	$this -> addElement('Hidden', 'apply_for_action', array('order' => '7'));
	$this -> addElement('Hidden', 'f_repeat_type', array('order' => '8'));
	$this -> addElement('Hidden', 'g_repeat_type', array('order' => '9'));
	
    // Buttons
    $this->addElement('Button', 'save_change', array(
      'label' => 'Save Changes',
      'type' => 'button',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
      'onclick'=>'check();',
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('save_change', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}
