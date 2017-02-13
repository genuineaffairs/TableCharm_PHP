<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Module.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Form_Admin_Module extends Engine_Form {

  public function init() {
    $this
      ->setTitle('Add New Module for Advertising')
      ->setDescription('Use the form below to enable advertising of content for a module of your site. Start by selecting a content module, and then entering the various database table related field names. In case of doubts regarding any field name, please contact the developer of that content module.');

		$module_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('module_id', null);
		if( empty($module_id) ) {
			$setModules = Engine_Api::_()->getDbTable('modules', 'communityad')->getModuleName();
			$module_table = Engine_Api::_()->getDbTable('modules', 'core');
			$module_name = $module_table->info('name');
			$select = $module_table->select()			
				->from($module_name, array('name', 'title'))
				->where($module_name . '.enabled =?', 1)
				->where($module_name . '.type =?', 'extra');
			$admodules = $select->query()->fetchAll();
			$adModuloeArray = array();
			$adModuloeArray[] = ' -- select --';
			foreach( $admodules as $modules ) {
				$is_inarray = in_array( $modules['name'], $setModules );
				if( empty($is_inarray) ) {
					$adModuloeArray[$modules['name']] = $modules['title'];
				}
			}
			$this->addElement('Hidden', 'is_adedit', array(
				'value' => 0,
				'order' => 998
			));

			$flag = false;

			$item_table_value = '';
			$item_table_title = '';
			$field_owner_value = '';
			$field_title_value = '';
			$field_body_value = '';
			$field_image_value = '';

		}else {
			$module_obj = Engine_Api::_()->getItem('communityad_module', $module_id);
			$adModuloeArray[$module_obj->module_name] = ucfirst($module_obj->module_name);

			$this->addElement('Hidden', 'is_adedit', array(
				'value' => 1,
				'order' => 998
			));
			$flag = true;
			$item_table_value = $module_obj->table_name;
			$item_table_title = $module_obj->module_title;
			$field_owner_value = $module_obj->owner_field;
			$field_title_value = $module_obj->title_field;
			$field_body_value = $module_obj->body_field;

//			$displayable = array();
//			if( 4 & (int) $module_obj->displayable ) {
//				$displayable[] = 4;
//			}
//			if( 2 & (int) $module_obj->displayable ) {
//				$displayable[] = 2;
//			}
//			if( 1 & (int) $module_obj->displayable ) {
//				$displayable[] = 1;
//			}
		}

		$this->addElement('Select', 'admodule_name', array(
			'label' => 'Content Module',
			'multiOptions' => $adModuloeArray,
			'attribs' => array('disable' => $flag)
		));


		$this->addElement('Select', 'admodule_name', array(
			'label' => 'Content Module',
			'multiOptions' => $adModuloeArray,
			'onchange' => "adModuleInfo(this.value)",
		));

		$this->addElement('Text', 'addbtable_title', array(
			'label' => 'Content Title',
			'description' => 'Enter the content name for which you use this module. Ex: You may use the document module for ‘Tutorials’ on your site.',
			'required' => true,
			'value' => $item_table_title
		));

		$this->addElement('Text', 'adtable_name', array(
			'label' => 'Database Table Item',
			'description' => "This is the value of 'items' key in the manifest file of this plugin. To view this value for a desired module, go to the directory of this module, and open the file 'settings/manifest.php'. In this file, search for 'items', and view its value. [Ex in case of blog module: Open file 'application/modules/Blog/settings/manifest.php', and go to around line 62. You will see the 'items' key array with value 'blog'. Thus, the Database Table Item for blog module is: 'blog']",
			'required' => true,
			'value' => $item_table_value
		));

		$this->addElement('Dummy', 'dummy_title', array(
				'label' => 'For the form fields below, please look at the structure of the main database table of this module.',
    ));

		$this->addElement('Text', 'adtable_owner', array(
			'label' => 'Content Owner Field in Table',
			'description' => 'Ex: owner_id or user_id',
			'required' => true,
			'value' => $field_owner_value
		));
		$this->adtable_owner->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));

		$this->addElement('Text', 'adtable_title', array(
			'label' => 'Content Title Field in Table',
			'description' => 'Ex: title',
			'required' => true,
			'value' => $field_title_value
		));
		$this->adtable_title->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));


		$this->addElement('Text', 'adtable_body', array(
			'label' => 'Content Body/Description Field in Table',
			'required' => true,
			'description' => 'Ex: body or description',
			'value' => $field_body_value
		));
		$this->adtable_body->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));


//    $this->addElement('MultiCheckbox', 'displayable', array(
//      'label' => 'Display',
//      'description' => 'Which types of feeds should this item be displayed in? The subject and object are specified in the activity item type text above.',
//      'multiOptions' => array(
//        4 => 'Community ad',
//        2 => 'Sponcerd Story: Page Like',
//        1 => 'Sponcerd Story: Page Post Like',
//      ),
//			'value' => $displayable
//    ));


    $this->addElement('Hidden', 'is_error', array(
      'value' => 0
    ));
	

    $this->addElement('Button', 'submit', array(
            'label' => 'Save Settings',
            'type' => 'submit',
            'ignore' => true
    ));
  }

}