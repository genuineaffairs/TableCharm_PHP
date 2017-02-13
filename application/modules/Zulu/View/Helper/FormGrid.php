<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FormGrid
 *
 * @author abakivn
 */
class Zulu_View_Helper_FormGrid extends Zend_View_Helper_FormElement {

  public function formGrid($name, $value = null, $attribs = null, $options = null) {
    // Try to get field id
    if(is_array($attribs) && array_key_exists('data-field-id', $attribs)) {
      $field_id = $attribs['data-field-id'];
    } elseif($attribs instanceof Fields_Model_Value && isset($attribs->field_id)) {
      $field_id = $attribs->field_id;
    } else {
      throw new Engine_Application_Exception('Unable to get field id');
    }
    if(is_string($value)) {
      $value = json_decode($value, true);
    }

    $db = Engine_Db_Table::getDefaultAdapter();

    $data = $db->select()
              ->from('engine4_zulu_fields_xhtml')
              ->where('field_id = ?', $field_id)
              ->query()->fetch();
    
    $decoded_data = json_decode($data['field_data'], true);
    $gridFieldContent = '';
    $supported_input_type = array(
        'text' => 'Text Input',
        'plain_text' => 'Plain Text',
        'dropdown' => 'Dropdown',
    );
    $total_colnum = count($decoded_data['th']);
    $htmlAttribs = $this->_htmlAttribs($attribs);
    
    $gridFieldContent .= <<<EOF
        <table {$htmlAttribs}>
          <tr class="grid-header">
            <th class="grid-unused"></th>
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
              {$th}
            </th>
EOF;
              $i++;
            }
          $gridFieldContent .= <<<EOF
          </tr>
EOF;
          $i = 0;
          $row_count = 0;
          
          foreach($value as $row) {
            $gridFieldContent .= <<<EOF
            <tr>
EOF;
            if($row_count === 0 || !$data['user_edit_row']) {
              $display = 'display:none';
            } else {
              $display = 'display:block';
            }
            $row_count++;

            $gridFieldContent .= <<<EOF
              <td class="grid-unused">
EOF;
            $gridFieldContent .= <<<EOF
                <span class="text">{$row_count}</span>
EOF;
            $gridFieldContent .= <<<EOF
                <a style="{$display}" class="remove_row" href="javascript:void(0);" onclick="void(0);" onmousedown="void(0);">
                  Remove row
                </a>
              </td>
EOF;
            $col_no = 0;
            foreach($decoded_data['th'] as $th) {
              if(empty($th)) {
                $th = $col_no;
              }
              if(!array_key_exists($th, $row)) {
                $row[$th] = '';
              }
              $input_name = "{$name}[{$th}][]";
                
              $gridFieldContent .= <<<EOF
              <td class="normal-col">
EOF;
              if($decoded_data['cell_type'][$col_no] == 'text') {
                // Text Input type column

                if($decoded_data['td'][$i] == $row[$th]) {
                  $text_val = '';
                } else {
                  $text_val = $row[$th];
                }

                $gridFieldContent .= <<<EOF
                <textarea placeholder="{$decoded_data['td'][$i]}" name="{$input_name}">{$text_val}</textarea>
EOF;
              } elseif($decoded_data['cell_type'][$col_no] == 'dropdown') {
                // Dropdown type column
                // 
                // Get field options
                $decoded_data['td'][$i] = trim($decoded_data['td'][$i]);
                if(!empty($decoded_data['td'][$i])) {
                  $opts = explode("\n", $decoded_data['td'][$i]);
                } else {
                  $opts = explode("\n", $decoded_data['td'][$i % $total_colnum]);
                }

                if(!empty($opts)) {
                    $gridFieldContent .= <<<EOF
                <select name="{$input_name}">
EOF;
                  foreach($opts as $opt) {
                    $opt = preg_replace('/[\p{C}]+/u', '', $opt);
                    $row[$th] = preg_replace('/[\p{C}]+/u', '', $row[$th]);

                    if(strlen($opt) > 0) {
                      if($opt == $row[$th]) {
                        $selected = 'selected="selected"';
                      } else {
                        $selected = '';
                      }
                      $gridFieldContent .= <<<EOF
                  <option {$selected} value="{$opt}">{$opt}</option>
EOF;
                    }
                  }
                  $gridFieldContent .= <<<EOF
                </select>
EOF;
                } else {
                    $gridFieldContent .= <<<EOF
                <input class="text" type="text" name="{$input_name}" value="{$row[$th]}" />
EOF;
                }
              } else {
                // Plain Text type column
                if(!empty($row[$th])) {
                  $plain_text = $row[$th];
                } else {
                  $plain_text = $decoded_data['td'][$i];
                }

                $gridFieldContent .= <<<EOF
                {$plain_text}
                <input name="{$input_name}" type="hidden" value="{$plain_text}" />
EOF;
              }
              $gridFieldContent .= <<<EOF
              </td>
EOF;
              $i++;
              $col_no++;
            }
            $gridFieldContent .= <<<EOF
            </tr>
EOF;
          }
          
          // Undisplay add row button if not allowed
          if(!$data['user_edit_row']) {
            $display = 'display:none';
          } else {
            $display = 'display:block';
          }
          $gridFieldContent .= <<<EOF
        </table>
        <p class="grid-type-description" style="{$display}"><a class="add_row" href="javascript:void(0);" onclick="void(0);" onmousedown="void(0);">Add row</a></p>
EOF;
    
    return $gridFieldContent;
  }

}
