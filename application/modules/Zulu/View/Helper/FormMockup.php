<?php

class Zulu_View_Helper_FormMockup extends Zend_View_Helper_Abstract {
  
  public static $arrTypeFileMap = array(
      'text' => 'single-line_text',
      'textarea' => 'multi-line_text',
      'select' => 'select_box',
      'radio' => 'radio_buttons',
      'checkbox' => 'single_checkbox',
      'multiselect' => 'multi_select',
      'multi_checkbox' => 'multi_checkbox',
      'integer' => 'number',
      'float' => 'number',
      'date' => 'date',
      'grid' => 'grid_view',
  );
  protected $sameExt = true;
  protected $fileExt = '.png';

  public function formMockup($type) {
    $ext = $this->sameExt ? $this->fileExt : '';
    $file = self::$arrTypeFileMap[$type];
    return "<div style='padding:5px;border:1px solid #ccc;width:252px'>"
    . "<image style='max-width:252px' src='{$this->view->layout()->staticBaseUrl}application/modules/Zulu/externals/images/form-mockup/{$file}{$ext}' /></div>";
  }

}
