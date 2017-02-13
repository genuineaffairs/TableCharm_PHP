<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Grid
 *
 * @author abakivn
 */
class Zulu_Form_Element_Grid extends Zend_Form_Element_Xhtml {

  public $helper = 'formGrid';
  protected $_fieldData;

  /**
   * Load default decorators
   *
   * @return void
   */
  public function loadDefaultDecorators() {
    if ($this->loadDefaultDecoratorsIsDisabled()) {
      return;
    }

    $decorators = $this->getDecorators();
    if (empty($decorators)) {
      $this->addDecorator('ViewHelper');
      Engine_Form::addDefaultDecorators($this);
      $this->addDecorator('HtmlTag3', array('tag' => 'div', 'class' => 'grid-form-element'));
    }
  }

  public function setValue($value) {
    if (is_array($value)) {
      $value = json_encode($value);
    }

    return parent::setValue($value);
  }

  public function getValue() {
    $this->_getFieldData();
    $value = json_decode(parent::getValue(), true);

    if (!$this->_isRefinedData($value)) {
      $value = $this->_refineData($value);
    }

    if (!$this->_isChangedDimesionData($value)) {
      // Convert to appropriate array for display in fields form
      $value = $this->_changeDimension($value);
    }

    $value = json_encode($value);

    return $value;
  }

  protected function _changeDimension($value) {
    $convertedArray = array();

    foreach ($value as $col => $rowValues) {
      foreach ($rowValues as $row_no => $val) {
        $convertedArray[$row_no][$col] = $val;
      }
    }

    return $convertedArray;
  }

  /**
   * @param array $value
   * @return array
   */
  protected function _refineData($value) {

    $refinedData = array();
    $i = 0;
    $col_cnt = count($this->_fieldData['th']);

    $value = $this->_fieldData['td'];

    foreach ($value as $data) {
      $col_no = $i % $col_cnt;
      $row_no = $i / $col_cnt;

      $refinedData[$this->_fieldData['th'][$col_no]][$row_no] = $data;

      $i++;
    }

    return $refinedData;
  }
  
  protected function _isChangedDimesionData($value) {
    $intersect = array();
    // Compute the intersection between columns in values and columns in field data
    if(is_array($value[0])) {
      $intersect = array_intersect(array_keys($value[0]), $this->_fieldData['th']);
    }

    return !empty($intersect);
  }

  protected function _isRefinedData($value) {
    // If first Column Group contains an array
    // Or keys of first row are the same with grid headings
    // Refined Data
    return is_array($value[$this->_fieldData['th'][0]]) || $this->_isChangedDimesionData($value);
  }

  protected function _getFieldData() {
    if (!$this->_fieldData) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $field_id = $this->{'data-field-id'};
      $data = $db->select()
                      ->from('engine4_zulu_fields_xhtml')
                      ->where('field_id = ?', $field_id)
                      ->query()->fetch();

      $this->_fieldData = json_decode($data['field_data'], true);
    }

    return $this->_fieldData;
  }

}
