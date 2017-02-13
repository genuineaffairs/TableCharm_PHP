<?php

/**
 * Description of Note
 *
 * @author abakivn
 */
class Zulu_Form_Element_Note extends Zend_Form_Element_Xhtml {

  public $helper = 'formNote';

  public function loadDefaultDecorators() {
    parent::loadDefaultDecorators();

    if (!$this->getValue()) {
      $this->setValue($this->getLabel());
    }

    $this->removeDecorator('Label')
//            ->removeDecorator('HtmlTag')
//            ->getDecorator('HtmlTag2')->setOption('class', 'form-wrapper-heading subheading-form-element')
    ;
  }

}
