<?php

class Zulu_View_Helper_FieldFileMulti extends Zend_View_Helper_Abstract {

  public function fieldFileMulti($subject, $field = null, $value = null)
  {
    // build the element
    $xhtml = '';
    $remote_paths = array_filter(explode(',', $value->value));
    
    $xhtml .= '<div class="zulu-old-file-rows">';

    foreach ($remote_paths as $path) {
      $xhtml .= '<div class="old-file-row">';
      $fileUrl = Engine_Api::_()->zulu()->getRemoteFileUrl($path);
      $xhtml .= '<a class="old-file" href="' . $fileUrl . '">' . Engine_Api::_()->zulu()->extractFileNameFromURL($fileUrl) . '</a>';
      $xhtml .= '</div>';
    }
    return $xhtml;
  }
}
