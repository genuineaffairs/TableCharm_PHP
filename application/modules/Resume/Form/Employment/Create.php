<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Resume_Form_Employment_Create extends Engine_Form
{
  public $_error = array();

  public function init()
  {
    $user = Engine_Api::_()->user()->getViewer();
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;    
    
    $this->setTitle($this->getTranslator()->translate('Add New Employment'))
      ->setDescription($this->getTranslator()->translate('Compose your employment below, then click "Save Employment" to add your employment.'))
      ->setAttrib('name', 'resume_employment_create');

    $this->addElement('Text', 'company', array(
      'label' => $this->getTranslator()->translate('Company'),
      //'description' => '',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '127')),
    )));
    $this->company->getDecorator("Description")->setOption("placement", "append");    
    
    
    $this->addElement('Text', 'title', array(
      'label' => $this->getTranslator()->translate('Position'),
//      'description' => 'Enter your job title, ex: Software Engineer',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '127')),
    )));
    $this->title->getDecorator("Description")->setOption("placement", "append");       

    $location_required = true;
    $this->addElement('Text', 'location', array(
      'label' => 'Location',
      'description' => 'Example: Los Angeles, CA 90071',
      'allowEmpty' => !$location_required,
      'required' => $location_required,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 128)),
        new Radcodes_Lib_Validate_Location_Address(),
      ),
      'filters' => array(
        'StripTags'
      ),
    ));       
    $this->location->getDecorator("Description")->setOption("placement", "append");
    
    
    $this->addElement('Checkbox', 'is_current', array(
      'label' => 'I currently work here',
      'value' => 1,
    ));
    
    $this->addElement('Date', 'start_date', array(
      'label' => 'Start Date',
    ));
    
    $this->addElement('Date', 'end_date', array(
      'label' => 'End Date',
    ));    

    $this->addElement('TinyMce', 'description', array(
      'disableLoadDefaultDecorators' => true,
      'decorators' => array(
        'ViewHelper'
      ),
      'editorOptions' => array(
        'remove_script_host' => '',
        'convert_urls' => '',
        'relative_urls' => '',
        'mode' => 'exact',
        'elements' => 'description',
        'width' => 460,
        'height' => 120,
        'media_strict' => false,
        'extended_valid_elements' => '*[*],**,object[width|height|classid|codebase|id|name],param[name|value],embed[src|type|width|height|flashvars|wmode|id|name],iframe[src|style|width|height|scrolling|marginwidth|marginheight|frameborder|id|name|class],video[src|type|width|height|flashvars|wmode|class|poster|preload|id|name],source[src]',
        'plugins' => "preview, paste, xhtmlxtras",
        'theme_advanced_buttons1' => "cut,copy,paste,|,bold,italic,underline,strikethrough,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,code",
      ),
      'filters' => array(
        new Engine_Filter_Censor(),
      )
    ));     
    
	     
    $core_module = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    if (version_compare($core_module->version, '4.7.0', '>=')) {
      $editorOptions = array('elements'=>'description');
      $this->description->editorOptions = Engine_Api::_()->radcodes()->getTinyMceEditorOptions($editorOptions);
    }
        
    
    /*
    // Description
    $this->addElement('Textarea', 'description', array(
      'label' => 'Description',
      'description' => 'Tell us more your job description',
      'filters' => array(
        new Engine_Filter_Censor(),
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
    */
    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => $this->getTranslator()->translate('Save Employment'),
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->addDecorator('DivDivDivWrapper');    

    
  }
  
  public function getValues()
  {
    $values = parent::getValues();
    if (!$values['start_date']) {
      $values['start_date'] = '0000-00-00';
    }
    if (!$values['end_date'] || $values['is_current']) {
      $values['end_date'] = '0000-00-00';
    }
    return $values;
  }
}