<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Locationsearch.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepageevent_Form_Locationsearch extends Engine_Form {

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
	  
		$this->addElement('Text', 'title', array(
			'label' => 'Page Title',
			'order' => 1,
			'autocomplete' => 'off',
    ));

		$this->addElement('Text', 'search_event', array(
			'label' => 'Event Title',
			'order' => 2,
		));

	  $this->addElement('Text', 'sitepage_location', array(
			'label' => 'Where',
			'autocomplete' => 'off',
			'description' => '(address, city, state or country)',
			'order' => 3,
			'onclick' => 'locationPage();'
		));
		$this->sitepage_location->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));

		$flage = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.proximity.search.kilometer', 0);
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
			$subform->addElement('Button', 'done', array(
				'label' => 'Search',
				'type' => 'submit',
				'ignore' => true,
			));
			$this->addSubForm($subform, $subform->getName());
		}
		else {
			$subform->addElement('Button', 'done', array(
				'label' => 'Search',
				'type' => 'submit',
				'ignore' => true,
				'onclick' => 'return locationSearch();'
			));
			$this->addSubForm($subform, $subform->getName());
		}
		
		// Element: cancel
    $this->addElement('Cancel', 'advances_search', array(
			'label' => 'Advanced search',
			'ignore' => true,
			'link' => true,
			'order' => 5,
			'onclick' => 'advancedSearchEvents();',
			'decorators' => array('ViewHelper'),
    ));

		$this->addElement('hidden', 'advanced_search', array(
			'value' => 0
		));


    $this->addDisplayGroup(array('advances_search', 'locationmiles', 'done', 'sitepage_location', 'search_event', 'title'), 'grp3');
    $button_group = $this->getDisplayGroup('grp3');
    $button_group->setDecorators(array(
			'FormElements',
			'Fieldset',
			array('HtmlTag', array('tag' => 'div', 'id' => 'group3', 'class' => 'grp_field'))
    ));

    // Start time
    $start = new Engine_Form_Element_CalendarDateTime('starttime');
    $start->setLabel("From:");
    $this->addElement($start);

    // End time
    $end = new Engine_Form_Element_CalendarDateTime('endtime');
    $end->setLabel("To:");
    $this->addElement($end);


		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		$show_multiOptions = array();
		$show_multiOptions["upcoming_event"] = 'Upcoming Events';
		$show_multiOptions["creation_date"] = "Everyone’s Events";

		if(!empty($viewer_id)) {
			$show_multiOptions["my_event"] = 'My Events';
			$show_multiOptions["my_like"] = 'Events of Pages I Like';
		}

		$show_multiOptions["past_event"] = 'Past Events';
		
		$show_multiOptions["featured"] = 'Featured Events';
		$sitepagePackageEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.enable', 1);
		if($sitepagePackageEnable) {
		$show_multiOptions["sponsored_event"] = 'Sponsored Events';
		}
		$enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.network', 0);
		if (empty($enableNetwork)) {
			$networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
			$viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer_id));

			if (!empty($viewerNetwork) || Engine_Api::_()->getApi('subCore', 'sitepage')->pageBaseNetworkEnable()) {
				$show_multiOptions['Networks'] = 'Only My Networks';
				$browseDefaulNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.default.show', 0);

				if (!isset($_GET['show']) && !empty($browseDefaulNetwork)) {
					$value_deault = 3;
				} elseif (isset($_GET['show'])) {
					$value_deault = $_GET['show'];
				}
			}
		}
		
		$this->addElement('Select', 'show', array(
			'label' => 'Show',
			'multiOptions' => $show_multiOptions,
			//'onchange' => 'searchSitepageevents();',
			'order' => 8,
		));
    
		$this->addElement('Select', 'orderby', array(
			'label' => 'Browse By',
			'multiOptions' => array(
				'' => '',
				'creation_date' => 'Most Recent',
				'starttime' => 'Start Time',
				'member_count' => 'Most Popular',
				'view_count' => 'Most Viewed',
			),
			'order' => 9,
			//'onchange' => 'searchSitepageevents();',
		));

// prepare categories
		$categories = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategories();
		if (count($categories) != 0) {
			$categories_prepared[0] = "";
			foreach ($categories as $category) {
				$categories_prepared[$category->category_id] = $category->category_name;
			}

			// category field
			$this->addElement('Select', 'category_id', array(
				'label' => 'Page Category',
				'order' => 10,
				'multiOptions' => $categories_prepared,
				'onchange' => "location_subcategoryies(this.value, '', '', '');",
			));
		}

		$this->addElement('Select', 'subcategory_id', array(
			'RegisterInArrayValidator' => false,
			'order' => 11,
			'decorators' => array(array('ViewScript', array(
				'viewScript' => 'application/modules/Sitepage/views/scripts/_Locationsubcategory.tpl',
				'class' => 'form element')))
		));

 		$this->addElement('Hidden', 'category', array(
      'order' => $i--,
    ));

    $this->addElement('Hidden', 'subcategory', array(
      'order' => $i--,
    ));

    $this->addElement('Hidden', 'subsubcategory', array(
      'order' => $i--,
    ));

    $this->addElement('Hidden', 'categoryname', array(
      'order' => $i--,
    ));

    $this->addElement('Hidden', 'subcategoryname', array(
      'order' => $i--,
    ));

    $this->addElement('Hidden', 'subsubcategoryname', array(
            'order' => $i--,
    ));
    
    $this->addElement('Hidden', 'Latitude', array(
      'order' => $i--,
    ));
    
    $this->addElement('Hidden', 'Longitude', array(
      'order' => $i--,
    ));


    $this->addDisplayGroup(array('starttime', 'endtime','orderby', 'show', 'category_id', 'subcategory_id'), 'grp1');
    $button_group = $this->getDisplayGroup('grp1');
    $button_group->setDecorators(array(
			'FormElements',
			'Fieldset',
			array('HtmlTag', array('tag' => 'div', 'id' => 'group1', 'class' => 'grp_field'))
    ));

    return $this;
  }
}