<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: AdminFieldMeta.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */
class Zulu_View_Helper_ZuluAdminGridFieldMeta extends Zend_View_Helper_Abstract
{
  public function zuluAdminGridFieldMeta($map)
  {
    $meta = $map->getChild();

    if( !($meta instanceof Fields_Model_Meta) ) {
      return '';
    }

    // Prepare translations
    $translate = Zend_Registry::get('Zend_Translate');

    // Prepare params
    if( $meta->type == 'heading' ) {
      $containerClass = 'heading';
    } else {
      $containerClass = 'field';
    }

    $key = $map->getKey();
    $label = $this->view->translate($meta->label);
    $type = $meta->type;
    
    $typeLabel = Engine_Api::_()->fields()->getFieldInfo($type, 'label');
    $typeLabel = $this->view->translate($typeLabel);

    $field_id = $meta->field_id;
    $db = Engine_Db_Table::getDefaultAdapter();
    
    $data = $db->select()
              ->from('engine4_zulu_fields_xhtml')
              ->where('field_id = ?', $field_id)
              ->query()->fetch();
    $decoded_data = json_decode($data['field_data'], true);
    
    if(empty($decoded_data)) {
      $decoded_data = array(
        'th' => array(''),
        'cell_type' => array(''),
        'td' => array(''),
      );
    }
    
    $gridFieldContent = '';
    $supported_input_type = array(
        'text' => 'Text Input',
        'plain_text' => 'Plain Text',
        'dropdown' => 'Dropdown',
    );
    $colnum = count($decoded_data['th']);
    
    $gridFieldContent .= <<<EOF
    <p class="grid-type-description">
      <a class="toggle_gridview_edit buttonlink" href="javascript:void(0);" onclick="void(0);" onmousedown="void(0);">
        Show Grid View Edit
      </a>
    </p>
            
    <div class="zulu_admin_field_dependent_field_wrapper">
      <div class="grid-type-description">
        <table class="zulu-grid-table">
          <tr>
            <th>
              <a class="add_column" href="javascript:void(0);" onclick="void(0);" onmousedown="void(0);">
                Add column
              </a>
            </th>
            <th>
              <a class="add_row" href="javascript:void(0);" onclick="void(0);" onmousedown="void(0);">
                Add row
              </a>
            </th>
          </tr>
        </table>
        <p class="grid-type-description">
          <a class="save_grid_view buttonlink" href="javascript:void(0);" onclick="void(0);" onmousedown="void(0);">
            Save Grid View
          </a>
        </p>
      </div>
      
      <ul>
        <li>For <b>Text Input</b> type, text inputted below will become place holder for text input field</li>
        <li>For <b>Plain Text</b> type, text inputted below will become plain text display to users</li>
        <li>For <b>Dropdown</b> type, each options is separated by enter (line break). If All Options in column are the same, you only need to input Options for the first row</li>
      </ul>
      
      <form class="grid-edit-form">
        <input name="field_id" type="hidden" value="{$field_id}" />

        <table class="zulu-grid-table grid-edit-table">
          <tr class="grid-header">
            <th class="grid-unused">Header</th>
EOF;
            $i = 0;
            foreach($decoded_data['th'] as $th) {
              if($i == 0) {
                $display = 'display:none';
              } else {
                $display = '';
              }
              $gridFieldContent .= <<<EOF
            <th class="normal-col">
              <a style="{$display}" class="remove_column" href="javascript:void(0);" onclick="void(0);" onmousedown="void(0);">
                Remove column
              </a>
              <input class="text" type="text" name="th[]" value="{$th}" />
              <span>Column type:</span>
              <select class="cell_type" name="cell_type[]">
EOF;
              foreach($supported_input_type as $type => $type_label) {
                if($type === $decoded_data['cell_type'][$i]) {
                  $selected = 'selected="selected"';
                } else {
                  $selected = '';
                }
                $gridFieldContent .= <<<EOF
                <option $selected value="{$type}">{$type_label}</option>
EOF;
              }
              $gridFieldContent .= <<<EOF
              </select>
            </th>
EOF;
              $i++;
            }
          $gridFieldContent .= <<<EOF
          </tr>
EOF;
          $i = 0;
          $row_count = 0;
          foreach($decoded_data['td'] as $td) {
            if($i % $colnum === 0) {
              if($row_count === 0) {
                $display = 'display:none';
              } else {
                $display = 'display:block';
              }
              $row_count++;
              $gridFieldContent .= <<<EOF
          <tr>
            <td class="grid-unused">
              <span class="text">Row {$row_count}</span>
              <a style="{$display}" class="remove_row" href="javascript:void(0);" onclick="void(0);" onmousedown="void(0);">
                Remove row
              </a>
            </td>
EOF;
            }
            $gridFieldContent .= <<<EOF
            <td class="normal-col">
              <textarea name="td[]">$td</textarea>
            </td>
EOF;
            if($i % $colnum === $colnum - 1) {
              $gridFieldContent .= <<<EOF
          </tr>
EOF;
            }
            $i++;
          }
          $gridFieldContent .= <<<EOF
        </table>
EOF;
          $checked = $data['user_edit_row'] ? 'checked="checked"' : '';
          $gridFieldContent .= <<<EOF
        <p class="grid-type-description">
          <input {$checked} id="user_edit_row_{$field_id}" name="user_edit_row" type="checkbox" value=1 />
          <label for="user_edit_row_{$field_id}">Allow user to add or remove row ?</label>
        </p>

        <div class="grid-type-description">
          <table class="zulu-grid-table">
            <tr>
              <th>
                <a class="add_column" href="javascript:void(0);" onclick="void(0);" onmousedown="void(0);">
                  Add column
                </a>
              </th>
              <th>
                <a class="add_row" href="javascript:void(0);" onclick="void(0);" onmousedown="void(0);">
                  Add row
                </a>
              </th>
            </tr>
          </table>
          <p class="grid-type-description">
            <a class="save_grid_view buttonlink" href="javascript:void(0);" onclick="void(0);" onmousedown="void(0);">
              Save Grid View
            </a>
          </p>
        </div>
      </form>
    </div>
EOF;
    
    // Generate
    $contentClass = 'admin_field zulu_grid ' . $this->_generateClassNames($key, 'admin_field_');
    $content = <<<EOF
  <li id="admin_field_{$key}" class="{$contentClass}">
    <span class='{$containerClass}'>
      <div class='item_handle'>
        &nbsp;
      </div>
      <div class='item_options'>
        <a href='javascript:void(0);' onclick='void(0);' onmousedown="void(0);">{$translate->_('edit')}</a>
        | <a href='javascript:void(0);' onclick='void(0);' onmousedown="void(0);">{$translate->_('delete')}</a>
      </div>
      <div class='item_title'>
        {$label}
        <span>({$typeLabel})</span>
      </div>
    </span>
    {$gridFieldContent}
  </li>
EOF;
    
    return $content;
  }

  protected function _generateClassNames($key, $prefix = '')
  {
    list($parent_id, $option_id, $child_id) = explode('_', $key);
    return
      $prefix . 'parent_' . $parent_id . ' ' .
      $prefix . 'option_' . $option_id . ' ' .
      $prefix . 'child_' . $child_id
      ;
  }
}