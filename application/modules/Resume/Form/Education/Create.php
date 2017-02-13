<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Education
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Resume_Form_Education_Create extends Engine_Form
{
  public $_error = array();

  public function init()
  {
    $user = Engine_Api::_()->user()->getViewer();
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;    
    
    $this->setTitle('Add New Education')
      ->setDescription('Compose your education below, then click "Save Education" to add your education.')
      ->setAttrib('name', 'resume_education_form');
  
      
    $this->addElement('Text', 'title', array(
      'label' => 'Institution',
      'description' => 'Enter the full name of the school',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_StringLength(array('max' => '127')),
    )));
    $this->title->getDecorator("Description")->setOption("placement", "append");    
    
    
    $date = new Zend_Date();
    $year = (int) $date->get(Zend_Date::YEAR);
    $ranges = range($year + 10, 1900);
    $year_ranges = array_combine($ranges, $ranges);
    
    $this->addElement('Select', 'class_year', array(
      'label' => 'Year  Graduated',
      'allowEmpty' => false,
      'required' => true,
      'multiOptions' => array(""=>"") + $year_ranges
    ));
    
    $this->addElement('Text', 'degree', array(
      'label' => 'Course',
//      'description' => 'Ex: AA, BS, MS, MD, or Ph.D',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '127')),
    )));     
    $this->degree->getDecorator("Description")->setOption("placement", "append");    
    
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
    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Education',
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
  
  
}