<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Folder
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */ 
class Folder_Form_Folder_Create extends Engine_Form
{
  public $_error = array();

  protected $_parent;

  public function getParent()
  {
    return $this->_parent;
  }

  public function setParent($parent)
  {
    $this->_parent = $parent;
    return $this;
  }  
  
  public function init()
  {
    $user = Engine_Api::_()->user()->getViewer();
    $user_level = $user->level_id;    
    
    $this->setTitle('Share New Files')
      ->setDescription('Please fill out the form below, then click "Continue" to move on to next step.')
      ->setAttrib('name', 'folders_create');

    $translate = Zend_Registry::get('Zend_Translate');
    $this->addElement('Hidden', 'parent', array(
      'value' => $this->_parent->getGuid(),
      'order' => 1000,
    ));
//    $this->addElement('Dummy', 'parent_name_type', array(
//      'label' => 'Type',
//      'content' => $translate->translate(strtoupper('ITEM_TYPE_' . $this->_parent->getType())),
//    ));
//    $this->addElement('Dummy', 'parent_name_title', array(
//      'label' => 'Item',
//      'content' => $this->_parent->toString(),
//    ));
      
      
//    $categories = Engine_Api::_()->folder()->getCategories();
//    $categories_prepared = Engine_Api::_()->folder()->convertCategoriesToArray($categories);
//    $categories_prepared = array(""=>"") + $categories_prepared;
    
    // category field
//    $this->addElement('Select', 'category_id', array(
//      'label' => 'Category',
//      'multiOptions' => $categories_prepared,
//      'allowEmpty' => false,
//      'required' => true,
//      'validators' => array(
//        array('NotEmpty', true),
//      ),
//      'filters' => array(
//       'Int'
//      ),
//    ));
    $this->addElement('Hidden', 'category_id', array(
      'value' => Folder_Model_DbTable_Categories::GENERAL_CATEGORY_ID
    ));
   
    $this->addElement('Text', 'title', array(
      'label' => 'Folder Name',
      'description' => 'Example: "Marketing Brochures"',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StringTrim',
        new Engine_Filter_Censor(),
      )
    ));
    $this->title->getDecorator("Description")->setOption("placement", "append");       

    
    // Description
    $this->addElement('Textarea', 'description', array(
      'label' => 'Description',
      'description' => 'Provide a brief info about content of this folder',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 1000)),
      ),      
    ));
    $this->description->getDecorator("Description")->setOption("placement", "append");        
    
    
//    $this->addElement('File', 'photo', array(
//      'label' => 'Photo'
//    ));
//    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');    
//    
//    
//    $this->addElement('Text', 'keywords',array(
//      'label'=>'Tags (Keywords)',
//      'autocomplete' => 'off',
//      'description' => 'Separate tags with commas.',
//      'filters' => array(
//        new Engine_Filter_Censor(),
//      ),
//    ));
//    $this->keywords->getDecorator("Description")->setOption("placement", "append");    
       
    
//    $this->addElement('Text', 'secret_code', array(
//      'label' => 'Secret Code',
//      'description' => "You can optionally protect this folder by providing a secret code above. Viewers will be prompt to enter this secret code to reveal folder's content.",
//      'filters' => array(
//        'StringTrim',
//      )    
//    ));
//    $this->secret_code->getDecorator("Description")->setOption("placement", "append"); 
    
    
    // Add subforms
    if( !$this->_item ) {
      $customFields = new Folder_Form_Custom_Fields();
    } else {
      $customFields = new Folder_Form_Custom_Fields(array(
        'item' => $this->getItem()
      ));
    }
    if( get_class($this) == 'Folder_Form_Folder_Create' ) {
      $customFields->setIsCreation(true);
    }

    $this->addSubForms(array(
      'fields' => $customFields
    ));
        
    // View
//    $availableLabels = array(
//      'everyone'              => 'Everyone',
//      'registered'            => 'Registered Members',
//      'owner_network'         => 'Friends and Networks',
//      'owner_member_member'   => 'Friends of Friends',
//      'owner_member'          => 'Friends Only',
//      'owner'                 => 'Just Me'
//    );
//    
//    
//    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('folder', $user, 'auth_view');
//    $options = array_intersect_key($availableLabels, array_flip($options));
//
//    $this->addElement('Select', 'auth_view', array(
//      'label' => 'Privacy',
//      'description' => 'Who may see this folder?',
//      'multiOptions' => $options,
//      'value' => 'everyone',
//    ));
//    $this->auth_view->getDecorator('Description')->setOption('placement', 'prepend');
    $this->addElement('Hidden', 'auth_view', array(
      'value' => 'registered',
      'order' => 1001
    ));

//    $options =(array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('folder', $user, 'auth_comment');
//    $options = array_intersect_key($availableLabels, array_flip($options));
//
//    // Comment
//    $this->addElement('Select', 'auth_comment', array(
//      'label' => 'Comment Privacy',
//      'description' => 'Who may post comments on this folder?',
//      'multiOptions' => $options,
//      'value' => 'registered',
//    ));
//    $this->auth_comment->getDecorator('Description')->setOption('placement', 'prepend');
    $this->addElement('Hidden', 'auth_comment', array(
      'value' => 'owner_member',
      'order' => 1002
    ));
    
//    $this->addElement('Checkbox', 'search', array(
//      'label' => 'Show this folder in search results',
//      'value' => 1
//    ));
    $this->addElement('Hidden', 'search', array(
      'value' => 1,
      'order' => 1003
    ));
    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Continue',
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

}