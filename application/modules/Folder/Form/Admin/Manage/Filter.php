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
 
 
 
class Folder_Form_Admin_Manage_Filter extends Engine_Form
{
  
  public function init()
  {
    $this->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

    $this
      ->setAttribs(array(
        'id' => 'folder_admin_manage_filter',
        'class' => 'global_form_box',
      ));

    // Get available types
    $availableTypes = Engine_Api::_()->folder()->getAvailableParentTypes();
    if( is_array($availableTypes) && count($availableTypes) > 0 ) {
    	
      $options = array();
      foreach( $availableTypes as $index => $type ) {
        $options[$type] = strtoupper('ITEM_TYPE_' . $type);
      }
      
	    $this->addElement('Select', 'parent_type', array(
	      'label' => 'Type',
	      'multiOptions' => array_merge(array(''=>'Everything'), $options),
	     // 'order' => $i++,
	      'decorators' => array(
	        'ViewHelper',
	        array('Label', array('tag' => 'span')),
	        array('HtmlTag', array('tag' => 'li'))
	      ),
	    ));

    }        
      
    $this->addElement('Text', 'parent_id', array(
      'label' => 'Parent ID',
    ));  
    
    $this->addElement('Text', 'user', array(
      'label' => 'User',
    )); 
    
    
    $this->addElement('Text', 'keyword', array(
      'label' => 'Keywords',
    ));     
    
    $this->addElement('Select', 'category', array(
      'label' => 'Category',
      'multiOptions' => array(""=>"") + Engine_Api::_()->folder()->getCategoryOptions(),
    ));     

    
    
    $this->addElement('Select', 'featured', array(
      'label' => 'Featured',
      'multiOptions' => array(
        '' => '',
        '1' => 'Yes',
        '0' => 'No',
      ),      
    ));      

    $this->addElement('Select', 'sponsored', array(
      'label' => 'Sponsored',
      'multiOptions' => array(
        '' => '',
        '1' => 'Yes',
        '0' => 'No',
      ),      
    ));     
 
    
    /*
    $this->addElement('Select', 'period', array(
      'label' => 'Time Period',
      'multiOptions' => array(
        '' => '',
        '24hrs' => '24 Hours',
        'week' => '7 Days',
        'month' => '30 Days',
        'quarter' => '3 Months',
        'year' => '12 Months',
        'all' => 'All Time',
      ),
    ));
    */
    
    $this->addElement('Select', 'order', array(
      'label' => 'Sort By',
      'multiOptions' => array(
        '' => '',
        'recent' => 'Most Recent',
      	'lastupdated' => 'Last Updated',
      	'alphabet' => 'Folder Name',
        'mostviewed' => 'Most Viewed',
        'mostcommented' => 'Most Commented',
        'mostliked' => 'Most Liked',
      ),
    ));
    foreach( $this->getElements() as $fel ) {
      if( $fel instanceof Zend_Form_Element ) {
        
        $fel->clearDecorators()
          ->addDecorator('ViewHelper')
          ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
          ->addDecorator('HtmlTag', array('tag' => 'div', 'id'  => $fel->getName() . '-search-wrapper', 'class' => 'form-search-wrapper'));
        
      }
    }  
    
    $submit = new Engine_Form_Element_Button('filtersubmit', array('type' => 'submit'));
    $submit
      ->setIgnore(true)
      ->setLabel('Search')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
      ->addDecorator('HtmlTag2', array('tag' => 'div'));

    $this->addElement($submit);
      /*
    $this->addElement('Hidden', 'order', array(
      'order' => 1001,
    ));
*/
    $this->addElement('Hidden', 'order_direction', array(
      'order' => 1002,
    ));
      
      
          
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module'=>'folder', 'controller'=>'manage'), 'admin_default', true));
      
  }
  
}