<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: LocationController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitetagcheckin_Form_Locationsearch extends Engine_Form {

  protected $_searchForm;

//   protected $_value;
// 
//   public function getValue() {
//     return $this->_value;
//   }
// 
//   public function setValue($item) {
//     $this->_value = $item;
//     return $this;
//   }


  public function init() {
  
    //$this->_value = unserialize($this->_value);
  
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName(); 
    $controller = $front->getRequest()->getControllerName(); 
    $action = $front->getRequest()->getActionName();

    // Add custom elements
    $this->setAttribs(array(
			'id' => 'filter_form',
			'class' => '',
		))
		->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
		->setMethod('POST');
		
		$this->getAdditionalOptionsElement();

    parent::init();

    $this->loadDefaultDecorators();
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    if ($module == 'sitetagcheckin' && $controller == 'index' && $action != 'by-locations') {
			$this->setAction($view->url(array('action' => 'by-locations'), 'sitetagcheckin_bylocation', true))->getDecorator('HtmlTag')->setOption('class', '');
    }
  }

  public function getAdditionalOptionsElement() {

    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName(); 
    $controller = $front->getRequest()->getControllerName(); 
    $action = $front->getRequest()->getActionName();

		$subform = new Zend_Form_SubForm(array(
			'name' => 'extra',
			'order' => 19999999,
			'decorators' => array(
				'FormElements',
			)
		));
		Engine_Form::enableForm($subform);
		
	  $i = -5000;

		// prepare categories
    $finalArray[0]  = "All Categories";
    if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'event' )) {
			$categories = Engine_Api::_()->getDbTable('categories', 'event')->getCategoriesAssoc();
		} elseif(Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'ynevent' )) {
			$categories = Engine_Api::_()->getDbTable('categories', 'ynevent')->getCategoriesAssoc();
		}
		$finalArray = array_merge($finalArray, $categories); 

		$this->addElement('Select', 'category_id', array(
			'label' => 'Category',
			'order' => 1,
			'multiOptions' => $finalArray,
		));


		$this->addElement('Text', 'search', array(
			'label' => 'What',
			'autocomplete' => 'off',
			'description' => '(Enter keywords or Event name)',
			'order' => 2,
		));
		$this->search->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
		
		
	  $this->addElement('Text', 'sitepage_location', array(
			'label' => 'Where',
			'autocomplete' => 'off',
			'description' => '(address, city, state or country)',
			'order' => 3,
			'onclick' => 'locationPage();'
		));
		$this->sitepage_location->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

		//$flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.proximity.search.kilometer', 0);
		
		$flage = Engine_Api::_()->seaocore()->geoUserSettings('sitetagcheckin');
		if ($flage) {
			$locationLable = "Within Kilometers";
			$locationOption = array(
				'0' => '',
				'1' => '1 Kilometer',
				'2' => '2 Kilometers',
				'5' => '5 Kilometers',
				'10' => '10 Kilometers',
				'20' => '20 Kilometers',
				'50' => '50 Kilometers',
				'100' => '100 Kilometers',
				'250' => '250 Kilometers',
				'500' => '500 Kilometers',
				'750' => '750 Kilometers',
				'1000' => '1000 Kilometers',
			);
		} else {
			$locationLable = "Within Miles";
			$locationOption = array(
				'0' => '',
				'1' => '1 Mile',
				'2' => '2 Miles',
				'5' => '5 Miles',
				'10' => '10 Miles',
				'20' => '20 Miles',
				'50' => '50 Miles',
				'100' => '100 Miles',
				'250' => '250 Miles',
				'500' => '500 Miles',
				'750' => '750 Miles',
				'1000' => '1000 Miles',
			);
		}
		$this->addElement('Select', 'locationmiles', array(
			'label' => $locationLable,
			'multiOptions' => $locationOption,
			'value' => '0',
			'order' => 4,
		));


    //Check for Location browse page.
		if ($module == 'sitepage' && $controller == 'index' && $action != 'map') {
			$this->addElement('Button', 'done', array(
				'label' => 'Search',
				'type' => 'submit',
				'order' => 5,
				'ignore' => true,
			));
			//$this->addSubForm($subform, $subform->getName());
		}
		else {
			$this->addElement('Button', 'done', array(
				'label' => 'Search',
				'type' => 'submit',
				'order' => 5,
				'ignore' => true,
				'onclick' => 'return locationSearch();'
			));
			//$this->addSubForm($subform, $subform->getName());
		}
		
		// Element: cancel
    $this->addElement('Cancel', 'advances_search', array(
			'label' => 'Advanced search',
			'ignore' => true,
			'link' => true,
			'order' => 6,
			'onclick' => 'advancedSearchEvents();',
			'decorators' => array('ViewHelper'),
    ));

		$this->addElement('hidden', 'advanced_search', array(
			'value' => 0
		));


    $this->addDisplayGroup(array('advances_search', 'done', 'locationmiles', 'category_id', 'done', 'sitepage_location', 'search'), 'grp3');
    $button_group = $this->getDisplayGroup('grp3');
    $button_group->setDecorators(array(
			'FormElements',
			'Fieldset',
			array('HtmlTag', array('tag' => 'div', 'id' => 'group3', 'class' => 'grp_field'))
    ));

    $group2 = array();

    //if (!empty($this->_value['street'])) {
			$this->addElement('Text', 'sitepage_street', array(
				'label' => 'Street',
				'autocomplete' => 'off',
				'order' => 7,
			));
			$group2[] = 'sitepage_street';
		//}

		//if (!empty($this->_value['city'])) {
			$this->addElement('Text', 'sitepage_city', array(
				'label' => 'City',
				'autocomplete' => 'off',
				'order' => 8,
			));
			$group2[] = 'sitepage_city';
		//}

		//if (!empty($this->_value['state'])) {
			$this->addElement('Text', 'sitepage_state', array(
				'label' => 'State',
				'autocomplete' => 'off',
				'order' => 9,
			));
			$group2[] = 'sitepage_state';
		//}

		//if (!empty($this->_value['country'])) {
			$this->addElement('Text', 'sitepage_country', array(
				'label' => 'Country',
				'autocomplete' => 'off',
				'order' => 10,
			));
			$group2[] = 'sitepage_country';
		//}

		if(!empty($group2)) {
			$this->addDisplayGroup($group2, 'grp2');
			$button_group = $this->getDisplayGroup('grp2');
			$button_group->setDecorators(array(
				'FormElements',
				'Fieldset',
				array('HtmlTag', array('tag' => 'div', 'id' => 'group2', 'class' => 'grp_field'))
			));
		}
		
		$show_multiOptions = array();
		$show_multiOptions["2"] = 'Upcoming Events';
		$show_multiOptions["0"] = 'Everyone\'s Events';
		$show_multiOptions["3"] = 'Past Events';

	  $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		if (!empty($viewer_id)) {
			$show_multiOptions["1"] = 'Only My Friends\' Events';
    }
    
    $this->addElement('Select', 'view_view', array(
      'label' => 'View:',
      'order' => 6,
      'multiOptions' => $show_multiOptions,
    ));
    
    $this->addElement('Select', 'order', array(
      'label' => 'List By:',
      'order' => 7,
      'multiOptions' => array(
        '0' => 'Proximity',
        '1' => 'Start Time',
        '2' => 'Recently Created',
        '3' => 'Most Popular',
      ),
//       'value' => 'creation_date DESC',
//       'onchange' => '$(this).getParent("form").submit();',
    ));

    // Start time
    $start = new Engine_Form_Element_CalendarDateTime('starttime');
    $start->setLabel("From:");
    $this->addElement($start);

    // End time
    $end = new Engine_Form_Element_CalendarDateTime('endtime');
    $end->setLabel("To:");
    $this->addElement($end);
    
    $this->addElement('Hidden', 'Latitude', array(
      'order' => $i--,
    ));
    
    $this->addElement('Hidden', 'Longitude', array(
      'order' => $i--,
    ));

    $this->addDisplayGroup(array('starttime', 'endtime','order', 'view_view'), 'grp1');
    $button_group = $this->getDisplayGroup('grp1');
    $button_group->setDecorators(array(
			'FormElements',
			'Fieldset',
			array('HtmlTag', array('tag' => 'div', 'id' => 'group1', 'class' => 'grp_field'))
    ));

    return $this;
  }
}