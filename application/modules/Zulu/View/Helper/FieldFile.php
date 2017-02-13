<?php

class Zulu_View_Helper_FieldFile extends Zend_View_Helper_Abstract {
  public function fieldFile($subject, $field = null, $field_value = null) {
    if($subject instanceof Zulu_Model_Zulu) {
      $fileUrl = $subject->getFileUrl($field->field_id);
      
      return '<a href="' . $fileUrl . '">' . str_replace('/', '', strrchr($fileUrl, '/')) . '</a>';
    }
    return false;
  }
}

