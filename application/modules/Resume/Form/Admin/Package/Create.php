<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @package   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Resume_Form_Admin_Package_Create extends Engine_Form
{

  public function init()
  {
    $this->setTitle('Create New Package')
      ->setDescription('Please fill out the form below to create a new resume package.')
      ;

    $this->addElement('Text', 'title', array(
      'label' => 'Package Name',
      'allowEmpty' => false,
      'required' => true,
      'attribs' => array(
        'class' => 'text'
      ),
    ));
    
    $this->addElement('Textarea', 'description', array(
      'label' => 'Description',
      'filters' => array(
        'StripTags',
      ),    
    ));

    $this->addElement('TinyMce', 'body', array(
      'label' => 'Full Description',
      //'required' => true,
      //'allowEmpty' => false,
      'filters' => array(
        new Engine_Filter_Censor(),
       // new Radcodes_Lib_Filter_Html(array('allowedTags'=>$allowed_html,'useDefaultLists'=>false,'allowedAttributes'=>$allowed_htmlattrs))
       ),
      'editorOptions' => array(
        'mode' => 'exact',
        'elements' => 'body',
        'width' => 450,
        'height' => 260,
        'plugins' => "emotions, table, fullscreen, preview, paste, style, layer, xhtmlxtras",
        'theme_advanced_buttons1' => "cut,copy,paste,pastetext,pasteword,|,undo,redo,|,link,unlink,anchor,charmap,image,|,hr,removeformat,cleanup,code",
        'theme_advanced_buttons2' => "bold,italic,underline,strikethrough,|,bullist,numlist,|,outdent,indent,blockquote,|,sub,sup,|,justifyleft,justifycenter,justifyright,justifyfull,|,preview",
        'theme_advanced_buttons3' => "formatselect,fontselect,fontsizeselect,|,forecolor,backcolor,|,insertlayer,moveforward,movebackward,absolute",
        'theme_advanced_buttons4' => "tablecontrols,|,styleprops,attribs,|,cite,del,ins",
       
      )
    )); 

	     
    $core_module = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    if (version_compare($core_module->version, '4.7.0', '>=')) {
      $editorOptions = array('elements'=>'body');
      $this->body->editorOptions = Engine_Api::_()->radcodes()->getTinyMceEditorOptions($editorOptions);
    }

    
    $this->addElement('Text', 'price', array(
      'label' => 'Price',
      'description' => 'How much would you like to charge your member for posting under this package? If it is free to post, then enter 0 (zero) and payment would not be required.',
      'class' => 'short',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('Float', true),
        new Engine_Validate_AtLeast(0),
      ),
      'value' => '0.00',
    ));
    
    $this->addElement('Duration', 'duration', array(
      'label' => 'Duration',
      'description' => 'When should resume posted under this package expire?',
      'required' => true,
      'allowEmpty' => false,
      'value' => array('0', 'forever'),
    ));    

    $this->addElement('Radio', 'featured', array(
      'label' => 'Featured',
      'description' => 'Would you like to mark resume as Featured when created?',
      'multiOptions' => array(
        1 => 'Yes, mark resume as Featured when created.',
        0 => 'No, do not mark resume as Featured when created.'
      ),
      'value' => 0,
    ));      
    
    $this->addElement('Radio', 'sponsored', array(
      'label' => 'Sponsored',
      'description' => 'Would you like to mark resume as Sponsored when created?',
      'multiOptions' => array(
        1 => 'Yes, mark resume as Sponsored when created.',
        0 => 'No, do not mark resume as Sponsored when created.'
      ),
      'value' => 0,
    )); 
    
    $this->addElement('Radio', 'auto_process', array(
      'label' => 'Auto Process Payment?',
      'description' => 'Would you like to auto process payments upon receiving them?',
      'multiOptions' => array(
        1 => 'Yes, automatically update resume status with payment status',
        0 => 'No, administrator will manually process each payment'
      ),
      'value' => 1,
    ));
    
    $this->addElement('Radio', 'allow_renew', array(
      'label' => 'Allow Renew?',
      'description' => 'Would you like existing resumes to be able to renew this package? This setting does not apply to FREE package, hence existing resumes cannot renew FREE package.',
      'multiOptions' => array(
        1 => 'Yes, allow renewing of this package',
        0 => 'No, do not allow renew this package (such as trial package)'
      ),
      'value' => 1,
    ));
    /*
    $this->addElement('Radio', 'renew_base_startdate', array(
      'label' => 'Renew - Base Starting Date',
      'description' => 'When a resume is renewed from this package, which of the following would you like to be used as base starting date for expiration date calculation? Formular is BASE-START-DATE + PACKAGE DURATION = EXPIRE DATE. This setting only apply to package with fixed duration (ie, has expiration date). If the resume is currently has no expiration, then the base starting date would be the payment receiving date.',
      'multiOptions' => array(
        'payment_date' => 'Payment Date + duration',
        'expiration_date' => 'Current Expiration Date + duration (recommended)',
      ),
      'value' => 'expiration_date',
    ));
    */
    $this->addElement('Radio', 'allow_upgrade', array(
      'label' => 'Allow Upgrade?',
      'description' => 'Would you like existing resumes from other packages to be able to upgrade to this package? This setting does not apply to FREE package, hence existing resumes cannot upgrade to FREE package.',
      'multiOptions' => array(
        1 => 'Yes, allow other packages to upgrade to this package',
        0 => 'No, only allow new posting (such as trial package)'
      ),
      'value' => 1,
    ));
    /*
    $this->addElement('Radio', 'upgrade_base_startdate', array(
      'label' => 'Upgrade - Base Starting Date',
      'description' => 'When a resume is upgraded to this package, which of the following would you like to be used as base starting date for expiration date calculation? Formular is BASE-START-DATE + PACKAGE DURATION = EXPIRE DATE. This setting only apply to package with fixed duration (ie, has expiration date). If the resume is currently has no expiration, then the base starting date would be the payment receiving date.',
      'multiOptions' => array(
        'status_date' => 'Current Status Date + duration (not recommended)',
        'payment_date' => 'Payment Date + duration (recommended)',
        'expiration_date' => 'Current Expiration Date + duration',
      ),
      'value' => 'payment_date',
    ));
    */
    $this->addElement('File', 'photo', array(
      'label' => 'Photo',
      'description' => 'This photo would be shown on package profile page, and on resume profile pages, which belong to this package, if you have "Profile Icon Package" widget enabled in Layout Editor :: Resume Profile Page'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif');

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Create Package',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Checkbox', 'enabled', array(
      'label' => 'Enabled?',
      'value' => 1,
    ));
    
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'prependText' => ' or ',
      'ignore' => true,
      'link' => true,
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index', 'package_id' => null)),
      //'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));


    // DisplayGroup: buttons
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      )
    ));
  }

}