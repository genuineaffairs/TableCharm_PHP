<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminFieldsController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_AdminFieldsController extends Fields_Controller_AdminAbstract {

  protected $_fieldType = 'sitepage_page';
  protected $_requireProfileType = true;

	//ACTION FOR SHOWING THE PROFILE FIELDS
  public function indexAction() {

		//GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_fields');

    include APPLICATION_PATH . '/application/modules/Sitepage/controllers/license/license2.php';
  }

	//ACTION FOR PROFILE FIELD CREATION
  public function fieldCreateAction() {
    parent::fieldCreateAction();

    //GENERATE FORM
    $form = $this->view->form;

    if ($form) {

      //$form->setTitle('Add Profile Question');
      $form->removeElement('show');
      $form->addElement('hidden', 'show', array('value' => 0));

      $display = $form->getElement('display');
      $display->setLabel('Show on profile page?');

      $display->setOptions(array('multiOptions' => array(
              1 => 'Show on profile page',
              0 => 'Hide on profile page'
              )));

      $search = $form->getElement('search');
      $search->setLabel('Show on the search options?');

      $search->setOptions(array('multiOptions' => array(
              0 => 'Hide on the search options',
              1 => 'Show on the search options'
              )));
    }
  }

	//ACTION FOR PROFILE FIELD EDITION
  public function fieldEditAction() {
    parent::fieldEditAction();

    //GENERATE FORM
    $form = $this->view->form;

    if ($form) {

      $form->setTitle('Edit Profile Question');
      $form->removeElement('show');
      $form->addElement('hidden', 'show', array('value' => 0));
      $display = $form->getElement('display');
      $display->setLabel('Show on profile page?');

      $display->setOptions(array('multiOptions' => array(
              1 => 'Show on profile page',
              0 => 'Hide on profile page'
              )));

      $search = $form->getElement('search');
      $search->setLabel('Show on the search options?');

      $search->setOptions(array('multiOptions' => array(
              0 => 'Hide on the search options',
              1 => 'Show on the search options'
              )));
    }
  }

	//ACTION FOR HEADING CREATION
  public function headingCreateAction() {
    parent::headingCreateAction();

    //GENERATE FORM
    $form = $this->view->form;

    if ($form) {
      $form->removeElement('show');
      $form->addElement('hidden', 'show', array('value' => 0));

      $form->removeElement('display');
      $form->addElement('hidden', 'display', array('value' => 1));
    }
  }

	//ACTION FOR HEADING EDITION
  public function headingEditAction() {
    parent::headingEditAction();

    //GENERATE FORM
    $form = $this->view->form;

    if ($form) {
      $form->removeElement('show');
      $form->addElement('hidden', 'show', array('value' => 0));

      $form->removeElement('display');
      $form->addElement('hidden', 'display', array('value' => 1));
    }
  }

  public function typeCreateAction() {
    $field = Engine_Api::_()->fields()->getField($this->_getParam('field_id'), $this->_fieldType);

    // Validate input
    if ($field->type !== 'profile_type') {
      throw new Exception(sprintf('invalid input, type is "%s", expected "profile_type"', $field->type));
    }

    // Create form
    $this->view->form = $form = new Sitepage_Form_Admin_Type();

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    // Create New Profile Type from Duplicate of Existing
    if ($form->getValue('duplicate') != 'null') {
      // Create New Option in engine4_sitepage_page_fields_options
      $option = Engine_Api::_()->fields()->createOption($this->_fieldType, $field, array(
          'field_id' => $field->field_id,
          'label' => $form->getValue('label'),
              ));
      // Get New Option ID
      $db = Engine_Db_Table::getDefaultAdapter();
      $new_option_id = $db->select('option_id')
              ->from('engine4_sitepage_page_fields_options')
              ->where('label = ?', $form->getValue('label'))
              ->query()
              ->fetchColumn();
      // Get list of Field IDs From Duplicated member Type
      $field_map_array = $db->select()
              ->from('engine4_sitepage_page_fields_maps')
              ->where('option_id = ?', $form->getValue('duplicate'))
              ->query()
              ->fetchAll();

      $field_map_array_count = count($field_map_array);
      // Check if the Member type is blank
      if ($field_map_array_count == 0) {
        // Create new blank option
        $option = Engine_Api::_()->fields()->createOption($this->_fieldType, $field, array(
            'field_id' => $field->field_id,
            'label' => $form->getValue('label'),
                ));
        $this->view->option = $option->toArray();
        $this->view->form = null;
        return;
      }

      for ($c = 0; $c < $field_map_array_count; $c++) {
        $child_id_array[] = $field_map_array[$c]['child_id'];
      }
      unset($c);

      $field_meta_array = $db->select()
              ->from('engine4_sitepage_page_fields_meta')
              ->where('field_id IN (' . implode(', ', $child_id_array) . ')')
              ->query()
              ->fetchAll();

      // Copy each row
      for ($c = 0; $c < $field_map_array_count; $c++) {
        $db->insert('engine4_sitepage_page_fields_meta', array(
            'type' => $field_meta_array[$c]['type'],
            'label' => $field_meta_array[$c]['label'],
            'description' => $field_meta_array[$c]['description'],
            'alias' => $field_meta_array[$c]['alias'],
            'required' => $field_meta_array[$c]['required'],
            'display' => $field_meta_array[$c]['display'],
            //'publish' => $field_meta_array[$c]['publish'],
            'search' => $field_meta_array[$c]['search'],
            'show' => $field_meta_array[$c]['show'],
            'order' => $field_meta_array[$c]['order'],
            'config' => $field_meta_array[$c]['config'],
            'validators' => $field_meta_array[$c]['validators'],
            'filters' => $field_meta_array[$c]['filters'],
            'style' => $field_meta_array[$c]['style'],
            'error' => $field_meta_array[$c]['error'],
                )
        );
        // Add original field_id to array => new field_id to new corresponding row
        $child_id_reference[$field_meta_array[$c]['field_id']] = $db->lastInsertId();
      }
      unset($c);

      // Create new map from array using new field_id values and new Option ID
      $map_count = count($field_map_array);
      for ($i = 0; $i < $map_count; $i++) {
        $db->insert('engine4_sitepage_page_fields_maps', array(
            'field_id' => $field_map_array[$i]['field_id'],
            'option_id' => $new_option_id,
            'child_id' => $child_id_reference[$field_map_array[$i]['child_id']],
            'order' => $field_map_array[$i]['order'],
                )
        );
      }
    } else {
      // Create new blank option
      $option = Engine_Api::_()->fields()->createOption($this->_fieldType, $field, array(
          'field_id' => $field->field_id,
          'label' => $form->getValue('label'),
              ));
    }
    $this->view->option = $option->toArray();
    $this->view->form = null;

    // Get data
    $mapData = Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType);
    $metaData = Engine_Api::_()->fields()->getFieldsMeta($this->_fieldType);
    $optionData = Engine_Api::_()->fields()->getFieldsOptions($this->_fieldType);

    // Flush cache
    $mapData->getTable()->flushCache();
    $metaData->getTable()->flushCache();
    $optionData->getTable()->flushCache();
  }  

	//ACTION FOR PROFILE DELETION
  public function typeDeleteAction() {
    $option_id = $this->_getParam('option_id');

    if (!empty($option_id)) {

      //DELETE FIELD ENTRIES IF EXISTS
      $fieldmapsTable = Engine_Api::_()->fields()->getTable('sitepage_page', 'maps');
      $select = $fieldmapsTable->select()->where('option_id =?', $option_id);
      $metaData = $fieldmapsTable->fetchAll($select)->toArray();
      if (!empty($metaData)) {
        foreach ($metaData as $key => $child_ids) {
          $child_id = $child_ids['child_id'];

          //DELETE FIELD ENTRIES IF EXISTS
          $fieldmetaTable = Engine_Api::_()->fields()->getTable('sitepage_page', 'meta');
          $fieldmetaTable->delete(array(
              'field_id = ?' => $child_id,
          ));
        }
      }

      $fieldmapsTable = Engine_Api::_()->fields()->getTable('sitepage_page', 'maps');
      $fieldmapsTable->delete(array(
          'option_id = ?' => $option_id,
      ));

      $sitepagetable = Engine_Api::_()->getDbtable('pages', 'sitepage');
      $select = $sitepagetable->select()
              ->from($sitepagetable->info('name'), array('page_id'))
              ->where('profile_type = ?', $option_id);
      $rows = $sitepagetable->fetchAll($select)->toArray();
      if (!empty($rows)) {
        foreach ($rows as $key => $sitepage_ids) {
          $sitepage_id = $sitepage_ids['page_id'];

          $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepage_id);
          $sitepage->profile_type = 0;
          $sitepage->save();

          //DELETE FIELD ENTRIES IF EXISTS
          $fieldvalueTable = Engine_Api::_()->fields()->getTable('sitepage_page', 'values');
          $fieldvalueTable->delete(array(
              'item_id = ?' => $sitepage_id,
          ));

          $fieldsearchTable = Engine_Api::_()->fields()->getTable('sitepage_page', 'search');
          $fieldsearchTable->delete(array(
              'item_id = ?' => $sitepage_id,
          ));
        }
      }

			//DELETE MAPPING
			Engine_Api::_()->getDbtable('profilemaps', 'sitepage')->delete(array('profile_type = ?' => $option_id));
    }
    parent::typeDeleteAction();
  }
}
?>
