<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: FormFile.php 16541 2009-07-07 06:59:03Z bkarwin $
 */
/**
 * Abstract class for extension
 */
// require_once 'Zend/View/Helper/FormElement.php';

/**
 * Helper to generate a "file" element
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zulu_View_Helper_FormFileMulti extends Zend_View_Helper_FormElement {

  /**
   * Generates a 'file' element.
   *
   * @access public
   *
   * @param string|array $name If a string, the element name.  If an
   * array, all other parameters are ignored, and the array elements
   * are extracted in place of added parameters.
   *
   * @param array $attribs Attributes for the element tag.
   *
   * @return string The element XHTML.
   */
  public function formFileMulti($name, $value = null, $attribs = null)
  {
    $info = $this->_getInfo($name, $value, $attribs);
    extract($info); // name, id, value, attribs, options, listsep, disable
    // is it disabled?
    $disabled = '';
    if ($disable) {
      $disabled = ' disabled="disabled"';
    }

    // XHTML or HTML end tag?
    $endTag = ' />';
    if (($this->view instanceof Zend_View_Abstract) && !$this->view->doctype()->isXhtml()) {
      $endTag = '>';
    }

    // build the element
    $xhtml = '';
    $remote_paths = array_filter(explode(',', $value));

    $xhtml .= '<div class="zulu-old-file-rows">';

    if (count($remote_paths)) {
      $xhtml .= '<div class="zulu-file-list-title">'
              . 'List of uploaded files: (files will not be deleted until you save your changes)'
              . '</div>';
    }

    foreach ($remote_paths as $path) {
      $xhtml .= '<div class="old-file-row">';
      $fileUrl = Engine_Api::_()->zulu()->getRemoteFileUrl($path);
      $xhtml .= '<a class="old-file" href="' . $fileUrl . '">' . Engine_Api::_()->zulu()->extractFileNameFromURL($fileUrl) . '</a>';
      $xhtml .= '<a file-data="' . $path . '" class="delete_file" href="javascript:void(0);" onclick="void(0);" onmousedown="void(0);">'
              . Zend_Registry::get('Zend_Translate')->_('delete') . '</a>'
              . '</a>';
      $xhtml .= '</div>';
    }
    $xhtml .= '</div>';

    $xhtml .= '<div class="zulu-file-button">'
            . '<a class="add_row" href="javascript:void(0);" onclick="void(0);" onmousedown="void(0);">'
            . Zend_Registry::get('Zend_Translate')->_('Add more upload') . '</a>'
            . '</div>';

    $xhtml .= '<input type="hidden"'
            . ' name="' . $this->view->escape($name) . '"'
            . ' value="' . $value . '"'
            . $disabled
            . $endTag;

    $xhtml .= '<input type="hidden"'
            . ' name="' . $this->view->escape($name) . '_files_delete"'
            . ' class="files_delete"'
            . $disabled
            . $endTag;

    $xhtml .= '<div class="zulu-file-rows">';

    if (array_key_exists('data-field-id', $attribs)) {
      unset($attribs['data-field-id']);
    }
    $xhtml .= '<div class="file-row">'
            . '<input type="file"'
            . ' name="' . $this->view->escape($name) . '_files[]"'
            . $disabled
            . $this->_htmlAttribs($attribs)
            . $endTag
            . '<a class="remove_row" href="javascript:void(0);" onclick="void(0);" onmousedown="void(0);">'
            . Zend_Registry::get('Zend_Translate')->_('remove') . '</a>'
            . '</a>'
            . '</div>';

    $xhtml .= '</div>';

    return $xhtml;
  }
}
