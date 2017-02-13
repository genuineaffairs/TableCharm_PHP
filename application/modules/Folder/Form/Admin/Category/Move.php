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
 
 
 
class Folder_Form_Admin_Category_Move extends Engine_Form
{
  protected $_field;

  public function init()
  {
    $this
      ->setMethod('post')
      ->setTitle('Move Folder Category')
      ->setDescription('You can move folder category from one to another by completing form below.')
      ->setAttrib('class', 'global_form_popup')
      ;

    $id = new Zend_Form_Element_Hidden('id');

    $this->addElements(array(
      $id
    ));      
      
    // prepare categories
    $categories = Engine_Api::_()->folder()->getCategories();
    $categories_prepared = Engine_Api::_()->folder()->convertCategoriesToArray($categories);
    
    $this->addElement('Select', 'from_category_id', array(
      'label' => 'From Category',
      'multiOptions' => $categories_prepared
    ));
    
    $this->addElement('Select', 'to_category_id', array(
      'label' => 'To Category',
      'multiOptions' => $categories_prepared
    ));
    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Move Folders',
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