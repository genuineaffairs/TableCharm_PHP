<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Zulu
 *
 * @author abakivn
 */
class Zulu_Model_Zulu extends Core_Model_Item_Abstract {

  protected $_parent_type = 'user';
  protected $_owner_type = 'user';
  protected $_searchTriggers = array('search', 'title', 'description');
  protected $_modifiedTriggers = array('search', 'title', 'description', 'status');
  protected $_parent_is_owner = true;
  protected $_concussionMode = false;

  public function getFileUrl($field_id) {

    if (Engine_Api::_()->zulu()->isModEnabled('storage')) {
      $values = Engine_Api::_()->fields()->getFieldsValues($this);

      $valueRow = $values->getRowMatching(array(
          'field_id' => $field_id,
          'item_id' => $this->getIdentity(),
          'index' => 0
      ));

      if ($valueRow && !empty($valueRow->value)) {
        $model = new Storage_Model_File(array('data' => array('storage_path' => $valueRow->value)));

        $service = Engine_Api::_()->getDbtable('services', 'storage')->getService();

        $file_url = $service->map($model);

        return $file_url;
      }
    }

    return null;
  }

  public function getUserFieldValueString($fieldLabel) {
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    if (!$view) {
      return null;
    }
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

    $metaData = Engine_Api::_()->fields()->getFieldsMeta('user');

    $field = $metaData->getRowMatching(array('label' => $fieldLabel));
    
    // If cannot find field by label, try to search by alias
    if(!$field) {
      $field = $metaData->getRowMatching(array('alias' => $fieldLabel));
    }
    
    if(!$field) {
      return '';
    }

    // If cannot get Owner
    if (!(($user = $this->getOwner()) instanceof User_Model_User)) {
      return '';
    }

    $value = $field->getValue($user);

    $helperName = Engine_Api::_()->fields()->getFieldInfo($field->type, 'helper');

    $helper = $view->getHelper($helperName);

    return $helper->$helperName($user, $field, $value);
  }
  
  public function hasConcussionTest() {
    return $this->_concussionMode && $this->has_concussion_test;
  }

}
