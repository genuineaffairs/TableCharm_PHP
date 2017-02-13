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
 
 
 
class Resume_Form_Section_Edit extends Engine_Form
{

  
  public function init()
  {
    $this->setTitle('Edit Section')
      ->setDescription('Please fill out the form below to update this resume section.')
      ;

    $this->addElement('Text', 'title', array(
      'label' => 'Section Name',
      'allowEmpty' => false,
      'required' => true,
      'attribs' => array(
        'class' => 'text'
      ),
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
    $this->addElement('File', 'photo', array(
      'label' => 'Photo'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif');
		
    $this->addElement('Checkbox', 'show', array(
      'label' => 'Default created for new resume',
    ));
		*/
    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
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
  }

}