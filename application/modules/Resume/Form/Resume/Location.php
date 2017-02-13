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
class Resume_Form_Resume_Location extends Engine_Form
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
    self::enableForm($this);
    
    $this
      ->setTitle('Edit Resume Location')
      ->setDescription('Location informations below are pulled from Google Geocoding Service. You can modify these values using form below for more accuracy.')
      ;

    $view = $this->getView();
    $location_edit = $view->htmlLink($this->_item->getEditHref(),
      $view->translate('Change resume location')
    );      
      
    $this->addElement('Dummy', 'location', array(
      'label' => 'Location',
      'description' => $this->_item->location,
      'content' => $location_edit,
    ));       
    $this->location->getDecorator("Description")->setOption("placement", "prepend");  

    $this->addElement('Text', 'formatted_address', array(
     'label' => 'Standardized Address',
     'description' => 'Formatted by Google Geo Coding Service',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 255)),
      ),
    ));       
    $this->formatted_address->getDecorator("Description")->setOption("placement", "append"); 
    
    $this->addElement('Heading','heading', array(
      'value' => 'Location Details'
    ));
    $this->heading->removeDecorator('Label')
          ->removeDecorator('HtmlTag')
          ->getDecorator('HtmlTag2')->setOption('class', 'form-wrapper-heading');
    
    $this->addElement('Text', 'street_address', array(
      'label' => 'Street Address',
      'validators' => array(
        array('StringLength', false, array(1, 128)),
      ),
    ));       
    $this->street_address->getDecorator("Description")->setOption("placement", "append"); 
    
    $this->addElement('Text', 'city', array(
      'label' => 'City',
      'validators' => array(
        array('StringLength', false, array(1, 64)),
      ),
    ));       
    $this->city->getDecorator("Description")->setOption("placement", "append"); 
    
    $this->addElement('Text', 'state', array(
      'label' => 'State',
      'validators' => array(
        array('StringLength', false, array(1, 64)),
      ),
    ));       
    $this->state->getDecorator("Description")->setOption("placement", "append");     
    
    $this->addElement('Text', 'zip', array(
      'label' => 'Zip Code',
      'validators' => array(
        array('StringLength', false, array(1, 64)),
      ),
    ));       
    $this->state->getDecorator("Description")->setOption("placement", "append");     
    
    $this->addElement('Country', 'country', array(
      'label' => 'Country',
      'validators' => array(
        array('StringLength', false, array(1, 64)),
      ),
    ));       
    $this->state->getDecorator("Description")->setOption("placement", "append");      
    
    $this->addElement('Heading','heading2', array(
      'value' => 'Location Geocode'
    ));
    $this->heading2->removeDecorator('Label')
          ->removeDecorator('HtmlTag')
          ->getDecorator('HtmlTag2')->setOption('class', 'form-wrapper-heading');    
    
    $this->addElement('Text', 'lat', array(
      'label' => 'Latitude',
      'description' => 'Enter decimal point from -90 to 90',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        new Zend_Validate_Float(),
        new Zend_Validate_Between(-90,90)
      ),
    ));
    $this->lat->getDecorator("Description")->setOption("placement", "append");
            
    $this->addElement('Text', 'lng', array(
      'label' => 'Longitude',
      'description' => 'Enter decimal point from -180 to 180',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        new Zend_Validate_Float(),
        new Zend_Validate_Between(-180,180)
      ),
    ));
    $this->lng->getDecorator("Description")->setOption("placement", "append");
    
		$map = new Resume_Form_Element_Map('map', array(
			'item'  => $this->getItem(),
			'attribs'	=> array(
				'width' => 360,
				'height' => 300,
				'draggable' => true
			),
			'label' => 'Map Preview',
			'description' => 'Drag the marker to desired position to adjust its coordinate.'
		));
		$this->addElement($map);
    
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->addDecorator('DivDivDivWrapper');    
  }
  
  public static function enableForm(Zend_Form $form)
  {
    $form
      ->addPrefixPath('Fields_Form_Element', APPLICATION_PATH . '/application/modules/Fields/Form/Element', 'element');
  } 
  
}