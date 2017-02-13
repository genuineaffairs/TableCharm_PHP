<?php

class Zulu_View_Helper_FieldNote extends Zend_View_Helper_Abstract {
  public function fieldNote($subject, $field = null, $value = null) {
    return $value->value;
  }
}