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
 
 
 
class Folder_Form_Admin_Category_Delete extends Engine_Form
{
  protected $_item;
  
  public function getItem()
  {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item)
  {
    $this->_item = $item;
    return $this;
  }  
  
  public function init()
  {
    $this
      ->setMethod('post')
      ->setTitle('Delete Folder Category?')
      ->setDescription('Are you sure that you want to delete this category? It will not be recoverable after being deleted. You also have to move existing folders that belong to this deleting category to a new category.')
      ->setAttrib('class', 'global_form_popup')
      ;

    // prepare categories
    $categories = Engine_Api::_()->folder()->getCategories();
    if (count($categories)!=0){
    	$categories_prepared = Engine_Api::_()->folder()->convertCategoriesToArray($categories);
    	$categories_prepared = array(""=>"") + $categories_prepared;
    	
    	if ($this->_item)
    	{
    	  unset($categories_prepared[$this->_item->category_id]);
    	}

      // category field
      $this->addElement('Select', 'new_category_id', array(
            'label' => 'Move Folders To Category',
            'multiOptions' => $categories_prepared,
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
              array('NotEmpty', true),
            ),
            'filters' => array(
             'Int'
            ),
      ));
    }
      
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Delete',
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