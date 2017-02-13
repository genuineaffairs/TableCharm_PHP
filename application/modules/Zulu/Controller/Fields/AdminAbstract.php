<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminAbstract
 *
 * @author abakivn
 */
class Zulu_Controller_Fields_AdminAbstract extends Fields_Controller_AdminAbstract {

  protected $_topLevelId = 0;
  protected $_topOptionId = 0;
  protected $_mainTitle = 'E-Medical Record Plugin';
  protected $_defaultAdminFormClass = 'Fields_Form_Admin_Field';

  public function init() {
    parent::init();
    $this->view->main_title = $this->_mainTitle;

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $cssFile = array('grid-fields.css', 'custom-fields.css');
    foreach ($cssFile as $file) {
      $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl . 'application/modules/Zulu/externals/css/' . $file);
    }
    $jsFile = 'admin-grid-fields.js';
    $view->headScript()->appendFile($view->layout()->staticBaseUrl . 'application/modules/Zulu/externals/js/' . $jsFile);
  }

  public function indexAction() {
    // Make navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('zulu_admin_main', array());

    parent::indexAction();
  }

  public function gridFieldAction() {
    $post_data = $this->getRequest()->getPost();

    if (isset($post_data['field_id']) && is_numeric($post_data['field_id'])) {
      $user_edit_row = isset($post_data['user_edit_row']) ? $post_data['user_edit_row'] : '0';
      $field_id = $post_data['field_id'];
      unset($post_data['field_id']);
      unset($post_data['user_edit_row']);
      $db = Engine_Db_Table::getDefaultAdapter();

      if ($db instanceof Zend_Db_Adapter_Abstract) {
        $db->beginTransaction();

        $db->delete('engine4_zulu_fields_xhtml', array(
            'field_id = ?' => $field_id,
        ));

        $db->insert('engine4_zulu_fields_xhtml', array(
            'field_id' => $field_id,
            'field_data' => json_encode($post_data),
            'user_edit_row' => $user_edit_row,
        ));
        $db->commit();
        echo '1';
      }
    } else {
      echo '0';
    }
    exit;
  }

  public function fieldCreateAction() {
    parent::fieldCreateAction();
    $this->_addFieldMockup();
  }

  public function fieldEditAction() {
    // Get field meta alias before changes
    if($this->_getParam('field_id')) {
      $meta = Engine_Api::_()->fields()->getTable($this->_fieldType, 'meta')->getMetaById($this->_getParam('field_id'));
      $metaAlias = $meta->alias;
    }

    parent::fieldEditAction();
    
    // Revert field meta alias changes
    if($metaAlias && $meta instanceof Fields_Model_Meta) {
      $meta->setFromArray(array('alias' => $metaAlias));
      $meta->save();
    }
    
    $this->_addFieldMockup();
  }
  
  public function headingEditAction() {
    // Get field meta alias before changes
    if($this->_getParam('field_id')) {
      $meta = Engine_Api::_()->fields()->getTable($this->_fieldType, 'meta')->getMetaById($this->_getParam('field_id'));
      $metaAlias = $meta->alias;
    }

    parent::headingEditAction();
    
    // Revert field meta alias changes
    if($metaAlias && $meta instanceof Fields_Model_Meta) {
      $meta->setFromArray(array('alias' => $metaAlias));
      $meta->save();
    }
  }

  protected function rebuildDisplaySearchOptions() {
    $form = $this->view->form;

    if ($form) {
      //$form->setTitle('Add Resume Question');

      $display = $form->getElement('display');
      $display->setLabel('Show on EMR page?');
      $display->setOptions(array('multiOptions' => array(
              0 => 'Hide on EMR page',
              1 => 'Show on EMR page',
      )));

      $search = $form->getElement('search');
      $search->setLabel('Show on the search options?');
      $search->setOptions(array('multiOptions' => array(
              0 => 'Hide on the search options',
              1 => 'Show on the search options',
      )));
    }
  }

  protected function _addFieldMockup() {
    $type = $this->_getParam('type');
    if ($type && array_key_exists($type, Zulu_View_Helper_FormMockup::$arrTypeFileMap)) {
      $mockup = new Zulu_Form_Element_Note('mockup', array(
          'value' => $this->view->formMockup($this->_getParam('type')),
          'order' => 2,
          'label' => 'Field Mockup',
      ));

      $mockup->addDecorator('ViewHelper');
      Engine_Form::addDefaultDecorators($mockup);

      if ($this->view->form) {
        $this->view->form->addElement($mockup);
      }
    }
  }

  protected function _addCustomFields() {
    /* @var $form Fields_Form_Admin_Field */
    $form = $this->view->form;

    if ($form && isset($this->_formTitle)) {
      $form->setTitle($this->_formTitle);
    }

    $parent_id = $this->getRequest()->getParam('parent_id');
    $option_id = $this->getRequest()->getParam('option_id');
    // Only add custom field in case of top level fields
    $isTopLevel = (!$parent_id && !$option_id) || ($parent_id == $this->_topLevelId && $option_id == $this->_topOptionId);

    if ($form && $isTopLevel) {
      // --- Add Group Selection Question
      $metaData = Engine_Api::_()->fields()->getFieldsMeta($this->_fieldType);
      $mapData = Engine_Api::_()->fields()->getFieldsMaps($this->_fieldType);

      // On field editing, load selected group from db
      if ($this->_getParam('field_id')) {
        $fieldMap = $mapData->getRowMatching(array(
            'field_id' => $this->_topLevelId,
            'option_id' => $this->_topOptionId,
            'child_id' => $this->_getParam('field_id'),
        ));
        // This is used to store previous heading
        $preMap = null;
        // Heading to which editing question belongs
        $fieldMapHeading = null;
        // Signal when belonging heading is found
        $found = false;
      }

      $options = array();

      foreach ($mapData->getRowsMatching(array('field_id' => $this->_topLevelId, 'option_id' => $this->_topOptionId)) as $map) {
        $meta = $map->getChild();
        if ($meta->isHeading()) {
          $options[$map->getKey()] = $meta->label;

          // On field editing, load selected group from db
          if ($this->_getParam('field_id') && !$found && $fieldMap instanceof Fields_Model_Map) {
            if ($fieldMap->order < $map->order) {
              $fieldMapHeading = $preMap;
              // Found heading which question belong to
              $found = true;
            }
            $preMap = $map;
          }
        }
      }

      $form->addElement('Select', 'group');
      $group = $form->getElement('group');
      $group->setLabel('Please choose which group the question belongs to');
      $group->setOptions(array('multiOptions' => $options));

      // Re-order admin fields
      $group->setOrder(0);
      $form = $this->reOrderFormElement($form);

      if ($this->_getParam('field_id')) {
        if ($fieldMapHeading instanceof Fields_Model_Map) {
          $group->setValue($fieldMapHeading->getKey());
        } elseif (is_null($fieldMapHeading) && $preMap instanceof Fields_Model_Map) {
          $group->setValue($preMap->getKey());
        }
      }

      if ($this->_getParam('group')) {
        $group->setValue($this->_getParam('group'));
      }
    }
  }

  protected function _cleanMetadataCache() {
    Zend_Db_Table_Abstract::getDefaultMetadataCache()->clean(Zend_Cache::CLEANING_MODE_ALL);
  }

  protected function reOrderFormElement(Zend_Form $form) {
    $elements = $form->getElements();
    $i = count($elements);

    // Store the preserved position
    $arrSetOrder = array();

    foreach (array_reverse($elements) as $element) {
      if ($element->getOrder() === null) {
        $i--;
        while (in_array($i, $arrSetOrder)) {
          // Skip position
          $i--;
        }
        $element->setOrder($i);
      } else {
        if ($element->getOrder() > count($elements)) {
          $i--;
        }
        $arrSetOrder[] = $element->getOrder();
      }
    }
    
    if($buttonsGroup = $form->getDisplayGroup('buttons')) {
      $buttonsGroup->setOrder(count($elements));
    }

    return $form;
  }

}
