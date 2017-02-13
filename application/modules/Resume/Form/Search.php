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
 
 
 
class Resume_Form_Search extends Fields_Form_Search
{
  protected $_fieldType = 'resume';
  
  public function init()
  {
    parent::init();

    $this->addDecorators(array(
      'FormElements',
      array('FormErrors', array('placement' => 'PREPEND')),  
      array(array('li' => 'HtmlTag'), array('tag' => 'ul')),
      array('HtmlTag', array('tag' => 'div', 'class' => 'field_search_criteria')),
      'Form',
    ));    
    
    $this->loadDefaultDecorators();

    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'resumes_browse_filters field_search_criteria',
      ))
      ->getDecorator('HtmlTag')
        ->setOption('class', 'browseresumes_criteria resumes_browse_filters');
    

    // Add custom elements
    $this->getAdditionalOptionsElement();
    
  }

  public function getAdditionalOptionsElement()
  {
    $i = -5000;
    
    $this->addElement('Hidden', 'page', array(
      'order' => $i++,
    ));

    /*
    $this->addElement('Hidden', 'tag', array(
      'order' => $i++,
    ));
    */
    
    $this->addElement('Hidden', 'user', array(
      'order' => $i++,
    ));
    /*
    $this->addElement('Text', 'keyword', array(
      'label' => 'Keywords',
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));     
    */

    /*
	$this->addElement('Text', 'location', array(
      'label' => 'Location',
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));    

    $this->addElement('Select', 'distance', array(
      'label' => 'Within Distance',
      'order' => $i++,
      'filters' => array(
        new Radcodes_Lib_Filter_Null()
      ),    
      'multiOptions' => $this->getDistanceOptions(),
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));    
    */
    
    $this->addElement('Text', 'user_name', array(
      'label' => 'Name',
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
    ));
    
    $categories_prepared = array(""=>"") + Engine_Api::_()->resume()->getCategoryOptions();
    
    $this->addElement('Select', 'category', array(
      'label' => 'Participation Level',
      'multiOptions' => $categories_prepared,
      'order' => $i++,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
      'onchange' => 'changeFields();'
    ));     
   
    $this->addElement('Select', 'order', array(
      'label' => 'Sort By',
      'multiOptions' => array('' => '') + Resume_Form_Helper::getOrderOptions(),
      'order' => 999999,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'span')),
        array('HtmlTag', array('tag' => 'li'))
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('resume.listorder', 'trending'),
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
    $unit = Engine_Api::_()->getApi('settings', 'core')->getSetting('resume.distanceunit', Radcodes_Lib_Helper_Unit::UNIT_MILE);
    
    $distances = array(''=>"");
    $distance_ranges = array(5,10,25,50,100,250,500);
    foreach ($distance_ranges as $distance) {
      $distances[$distance] = "$distance $unit";
    }
    
    return $distances;
  }
}