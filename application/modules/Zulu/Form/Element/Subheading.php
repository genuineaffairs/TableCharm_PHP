<?php

class Zulu_Form_Element_SubHeading extends Engine_Form_Element_Heading {

  public function loadDefaultDecorators() {
    parent::loadDefaultDecorators();
    
    $this->setValue($this->getLabel());

    $this->removeDecorator('Label')
            ->removeDecorator('HtmlTag')
            ->getDecorator('HtmlTag2')->setOption('class', 'form-wrapper-heading subheading-form-element');
  }

}
