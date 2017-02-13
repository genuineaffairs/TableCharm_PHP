<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */ 
class Resume_Form_Resume_Create extends Engine_Form
{
  public $_error = array();

  protected $_package;

  /**
   * This defines a list of player's fields
   */
  protected $_playerFieldLabels = array('Position Played', 'Current Competition Level', 'Highest Junior Representative Honours', 'Highest Senior Representative Honours');
  
  /**
   * This defines a list of categories that need to hide player's fields
   */
  protected $_nonPlayerCategoryIds = array(Resume_Model_DbTable_Categories::COACH_CATEGORY_ID, Resume_Model_DbTable_Categories::AGENT_CATEGORY_ID);
  
  /**
   * This defines a list of non-mandatory fields for agent
   */
  protected $_nonMandatoryAgentFieldLabels = array('Gender', 'Date of Birth', 'Contract Status', 'Currently Seeking a Contract', 'Trade or Profession', 'Professional or Amateur', 'Passport');

  public function getpackage()
  {
    return $this->_package;
  }

  public function setpackage($package)
  {
    $this->_package = $package;
    return $this;
  }  
  
  public function init()
  {
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    if($view) {
      $agent_id = Resume_Model_DbTable_Categories::AGENT_CATEGORY_ID;
      
      if (Engine_Api::_()->hasModuleBootstrap('zulu') && Engine_Api::_()->zulu()->isMobileMode()) {
        $headScript = $view->headScriptSM();
        $headScript->appendScript("var is_mobile = true;");
      } else {
        $headScript = $view->headScript();
      }
//      $headScript->appendFile($view->layout()->staticBaseUrl . 'application/modules/Resume/externals/scripts/resume.js');
      $headScript->appendFile($view->layout()->staticBaseUrl . 'application/modules/Resume/externals/scripts/jResume.js');
      $headScript->appendScript("var player_labels = ['" . implode("','", $this->_playerFieldLabels) . "'];");
      $headScript->appendScript("var non_player_category_ids = ['" . implode("','", $this->_nonPlayerCategoryIds) . "'];");
      $headScript->appendScript("var agent_id = '{$agent_id}';");
      $headScript->appendScript("var non_mandatory_agent_fields = ['" . implode("','", $this->_nonMandatoryAgentFieldLabels) . "'];");
    }
    
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $field_order = 10000;
    
    $user = Engine_Api::_()->user()->getViewer();
    $user_level = $user->level_id;    
    
    $this->setTitle('Post New CV Profile')
      ->setDescription('Compose your CV Profile below, then click "Post CV Profile" to submit it. All CV Profile postings may be subject to editorial review.'
              . '<br />To gain maximum exposure on CV Profiler and to be able to fully take advantage of the CV Profiler Advanced Search it is important to fill in as many of these fields below as possible.')
      ->setAttrib('name', 'resumes_create');
    
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setEscape(false);
    
    if ($this->_package instanceof Resume_Model_Package)  
    {
    	$this->addElement('Hidden', 'package_id', array(
    		'value' => $this->_package->getIdentity(),
    		'order' => $field_order++,
    	));
    	$this->addElement('Dummy', 'package_name', array(
    		'label' => 'Package',
    		'content' => $this->_package->toString() . ' ('.$this->_package->getTerm().')'
    	));
    }
    else
    {
	    $packages = Engine_Api::_()->resume()->getPackages(array('enabled'=>1));
	    
	    $packages_prepared = array();
	    foreach ($packages as $package)
	    {
	      $packages_prepared[$package->getIdentity()] = $package->getTitle()
	        . ' ('. $package->getTerm() .')';
	    }
	    
	    //$packages_prepared = Engine_Api::_()->resume()->convertPackagesToArray($packages);
	    
	    $this->addElement('Radio', 'package_id', array(
	      'label' => 'Package',
	      'description' => 'Please select one of the following listing packages:',
	      'multiOptions' => $packages_prepared,
	      'allowEmpty' => false,
	      'required' => true,
	      'validators' => array(
	        array('NotEmpty', true),
	      ),
	      'filters' => array(
	        'Int'
	      ),
	    ));
	  
	    $this->package_id->getDecorator("Description")->setEscape(false);  
    }
    
    $categories_prepared = array(""=>"") + Engine_Api::_()->resume()->getCategoryOptions();
    
    // category field
    $this->addElement('Select', 'category_id', array(
      'label' => 'Participation Level',
      'multiOptions' => $categories_prepared,
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
      ),
      'filters' => array(
       'Int'
      ),
      'onchange' => 'changeFields()',
    ));
  
    
    $this->addElement('Text', 'title', array(
      'label' => 'CV Profile Title',
      'description' => 'Ex: Rugby Coach | John Smith, Company Name, Manager MGSL | John Smith, Company Name | Title | Contact Person',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StringTrim',
        new Engine_Filter_Censor(),
      )
    ));
    $this->title->getDecorator("Description")->setOption("placement", "append");       


    // Description
    $description_max_length = 500;
    $this->addElement('Textarea', 'description', array(
      'label' => 'Short Description',
      'description' => "Enter your pitch here! Sell yourself. Maximum {$description_max_length} characters are allowed.",
      'allowEmpty' => true,
      'required' => false,
      'max_length' => $description_max_length,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, $description_max_length)),
      ),      
    ));
    $this->description->getDecorator("Description")->setOption("placement", "append");        
    
    
    $this->addElement('File', 'photo', array(
      'label' => 'Photo'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');    
    
    
    $this->addElement('Text', 'keywords',array(
      'label'=>'Tags (Keywords)',
      'autocomplete' => 'off',
      'description' => 'Separate tags with commas.',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    $this->keywords->getDecorator("Description")->setOption("placement", "append");    
   
    $this->addElement('Text', 'name', array(
      'label' => 'Full Name',
      'description' => 'Do NOT use a nickname. Ex: John Doe',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StringTrim',
      ),
      'value' => $viewer->getTitle(),
    ));
    $this->name->getDecorator("Description")->setOption("placement", "append");   

    $location_required = true;
    $this->addElement('Text', 'location', array(
      'label' => 'Location Address',
      'description' => 'Ex: 400 S Hill St, Los Angeles, CA 90013',
      'allowEmpty' => !$location_required,
      'required' => $location_required,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 128)),
        new Radcodes_Lib_Validate_Location_Address(),
      ),
    ));       
    $this->location->getDecorator("Description")->setOption("placement", "append");       
    
    $this->addElement('Text', 'phone', array(
      'label' => 'Phone',
      //'description' => 'Ex: (213) 456-7890; 213-456-7890; +1 (213) 456-7890;',
      'filters' => array(
        'StringTrim',
      )
    ));
    $this->phone->getDecorator("Description")->setOption("placement", "append");  
    
    $this->addElement('Text', 'mobile', array(
      'label' => 'Mobile',
      //'description' => 'Ex: (213) 456-7890; 213-456-7890; +1 (213) 456-7890;',
      'filters' => array(
        'StringTrim',
      )
    ));
    $this->mobile->getDecorator("Description")->setOption("placement", "append");  

    $this->addElement('Text', 'fax', array(
      'label' => 'Fax',
      //'description' => 'Ex: (213) 456-7890; 213-456-7890; +1 (213) 456-7890;',
      'filters' => array(
        'StringTrim',
      )
    ));
    $this->fax->getDecorator("Description")->setOption("placement", "append");      
    
    $this->addElement('Text', 'email', array(
      'label' => 'Email',
      'filters' => array(
        'StringTrim',
      ),
      'validators' => array(
        array('EmailAddress', true),
      ),
      'value' => $viewer->email,
    ));
    $this->email->getDecorator("Description")->setOption("placement", "append");  
    
    $this->addElement('Text', 'website', array(
      'label' => 'Website',
      'filters' => array(
        'StringTrim',
      ),
      'validators' => array(
        new Engine_Validate_Callback(array($this, 'validateWebsite')),
      ),
      'description' => 'Ex: http://myglobalsportlink.com'
    ));
    $this->website->getDecorator("Description")->setOption("placement", "append");  
    
    // Add subforms
    if( !$this->_item ) {
      $customFields = new Resume_Form_Custom_Fields();
    } else {
      $customFields = new Resume_Form_Custom_Fields(array(
        'item' => $this->getItem()
      ));
    }
    if( get_class($this) == 'Resume_Form_Resume_Create' ) {
      $customFields->setIsCreation(true);
    }

    $this->addSubForms(array(
      'fields' => $customFields
    ));
    
    // View
    $availableLabels = array(
      'everyone'              => 'Everyone',
      'registered'            => 'All MGSL Members',
      'owner_network'         => 'Friends and Networks',
      'owner_member_member'   => 'Friends of Friends',
      'owner_member'          => 'My Friends',
      'owner'                 => 'Just Me'
    );
    
    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('resume', $user, 'auth_view');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
    // View
    if( !empty($viewOptions) && count($viewOptions) >= 1 ) {
//      // Make a hidden field
//      if(count($viewOptions) == 1) {
//        $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions), 'order' => $field_order++,));
//      // Make select box
//      } else {
//        $this->addElement('Select', 'auth_view', array(
//            'label' => 'Privacy',
//            'description' => 'Who may see this CV Profile?',
//            'multiOptions' => $viewOptions,
//            'value' => key($viewOptions),
//        ));
//        $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
//      }
      $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions), 'order' => $field_order++,));
    }
    
    $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('resume', $user, 'auth_comment');
    $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
    // Comment
    if( !empty($commentOptions) && count($commentOptions) >= 1 ) {
//      // Make a hidden field
//      if(count($commentOptions) == 1) {
//        $this->addElement('hidden', 'auth_comment', array('value' => key($commentOptions), 'order' => $field_order++,));
//      // Make select box
//      } else {
//        $this->addElement('Select', 'auth_comment', array(
//            'label' => 'Comment Privacy',
//            'description' => 'Who may post comments on this CV Profile?',
//            'multiOptions' => $commentOptions,
//            'value' => key($commentOptions),
//        ));
//        $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
//      }
      $this->addElement('hidden', 'auth_comment', array('value' => key($commentOptions), 'order' => $field_order++,));
    }

    $photoOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('resume', $user, 'auth_photo');    
    $photoOptions = array_intersect_key($availableLabels, array_flip($photoOptions));
    // Photo
    if( !empty($photoOptions) && count($photoOptions) >= 1 ) {
      // Make a hidden field
      if(count($photoOptions) == 1) {
        $this->addElement('hidden', 'auth_photo', array('value' => key($photoOptions), 'order' => $field_order++,));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_photo', array(
            'label' => 'Photo Uploads',
            'description' => 'Who may upload photos to this resume?',
            'multiOptions' => $photoOptions,
            'value' => key($photoOptions),
        ));
        $this->auth_photo->getDecorator('Description')->setOption('placement', 'append');
      }
    }
    
    $this->addElement('Checkbox', 'search', array(
      'label' => 'Show this CV Profile in browse and search results',
      'value' => 1
    ));
    
    if(Zend_Controller_Front::getInstance()->getRequest()->getParam('from_app')) {
      $submitLabel = 'Save Changes';
    } else {
      $submitLabel = 'Continue ...';
    }
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => $submitLabel,
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

  
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    
    $this->addDisplayGroup(array('submit','cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->addDecorator('DivDivDivWrapper');    

  }

  public function validateWebsite($value)
  {
    $validator = $this->website->getValidator('Engine_Validate_Callback');
    
    if (!Zend_Uri::check($value)) {
      $validator->setMessage('The Website URL appears to be invalid.');
      return false;
    }    
   
    return true;
  }
  
  public function isValid($data) {
    // If participation level is in non-player-categories, then hide player fields
    if (in_array($data['category_id'], $this->_nonPlayerCategoryIds)) {
      foreach ($this->getSubForm('fields')->getElements() as $el) {
        if (in_array($el->getLabel(), $this->_playerFieldLabels)) {
          $data['fields'][$el->getName()] = '';
          $el->setRequired(false)->setAllowEmpty(true);
        }
      }
    }

    // If participation level is agent, then set fields on the list above as non-mandatory
    if($data['category_id'] == Resume_Model_DbTable_Categories::AGENT_CATEGORY_ID) {
      foreach ($this->getSubForm('fields')->getElements() as $el) {
        if (in_array($el->getLabel(), $this->_nonMandatoryAgentFieldLabels)) {
          $el->setRequired(false)->setAllowEmpty(true);
        }
      }
    }
    
    // Return to normal validation after fields' hacking
    return parent::isValid($data);
  }

}