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
 
 
 
class Folder_Form_Search extends Fields_Form_Search
{
  protected $_fieldType = 'folder';
  
  public function init()
  {
    parent::init();

    $this->loadDefaultDecorators();

    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'folders_browse_filters field_search_criteria',
      ))
      ->getDecorator('HtmlTag')
        ->setOption('class', 'browsefolders_criteria folders_browse_filters');
    

    // Add custom elements
    $this->getAdditionalOptionsElement();
    
  }

  public function getAdditionalOptionsElement()
  {
    $i = -5000;
    
    $this->addElement('Hidden', 'page', array(
      'order' => $i++,
    ));

    
    $this->addElement('Hidden', 'parent', array(
      'order' => $i++,
    ));
    
    $this->addElement('Hidden', 'user', array(
      'order' => $i++,
    ));
    
    $this->addElement('Text', 'keyword', array(
      'label' => 'Keywords',
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
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
	      'order' => $i++,
	      'decorators' => array(
	        'ViewHelper',
	        array('Label', array('tag' => 'span')),
	        array('HtmlTag', array('tag' => 'li'))
	      ),
	    ));

    }    
    
    $categories = Engine_Api::_()->folder()->getCategories();
    $categories_prepared = Engine_Api::_()->folder()->convertCategoriesToArray($categories);
    $categories_prepared = array(""=>"") + $categories_prepared;
    
    $this->addElement('Select', 'category', array(
      'label' => 'Category',
      'multiOptions' => $categories_prepared,
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));     
   
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
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));
    
    /*
    $seperator1 = $this->getElement('separator1');
    $this->removeElement('separator1');
    $seperator1->setOrder($i++);
    $this->addElement($seperator1);
		*/
    if (count($this->_fieldElements)) {
      $this->_order['separator1'] = $i++;
    }
    else {
      $this->removeElement('separator1');
    }
    
    $j = 10000000;  
    
    $this->addElement('Button', 'done', array(
      'label' => 'Search',
      'type' => 'submit',
      'ignore' => true,
      'order' => $j++,
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));
  }
  
  public static function getDistanceOptions()
  {
    $unit = Engine_Api::_()->getApi('settings', 'core')->getSetting('folder.distanceunit', Radcodes_Lib_Helper_Unit::UNIT_MILE);
    
    $distances = array(''=>"");
    $distance_ranges = array(5,10,25,50,100,250,500);
    foreach ($distance_ranges as $distance) {
      $distances[$distance] = "$distance $unit";
    }
    
    return $distances;
  }
}