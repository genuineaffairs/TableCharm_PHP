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
class Zulu_View_Helper_FieldGrid extends Zend_View_Helper_Abstract {

  public function fieldGrid($subject, $field = null, $field_value = null) {
    // Try to get field id
    if(is_array($field_value) && array_key_exists('data-field-id', $field_value)) {
      $field_id = $field_value['data-field-id'];
    } elseif($field_value instanceof Fields_Model_Value && isset($field_value->field_id)) {
      $field_id = $field_value->field_id;
    } else {
      throw new Engine_Application_Exception('Unable to get field id');
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $value = json_decode(htmlspecialchars_decode($field_value->value), true);
    
    $data = $db->select()
              ->from('engine4_zulu_fields_xhtml')
              ->where('field_id = ?', $field_id)
              ->query()->fetch();
    
    $decoded_data = json_decode($data['field_data'], true);
    $gridFieldContent = '';
    $haveContent = false;

    $gridFieldContent .= <<<EOF
        <table class="zulu-grid-table grid-edit-table">
          <tr class="grid-header">
EOF;
            foreach($decoded_data['th'] as $th) {
              $gridFieldContent .= <<<EOF
            <th class="normal-col">
              {$th}
            </th>
EOF;
            }
          $gridFieldContent .= <<<EOF
          </tr>
EOF;
          
          foreach($value as $row) {
              $gridFieldContent .= <<<EOF
          <tr>
EOF;
            $col_no = 0;
            foreach($decoded_data['th'] as $th) {
              if(empty($th)) {
                $th = $col_no;
              }
              if(!isset($row[$th])) {
                $row[$th] = '';
              }
              if(!$haveContent && !empty($row[$th])) {
                $haveContent = true;
              }
              $gridFieldContent .= <<<EOF
            <td class="normal-col">{$row[$th]}</td>
EOF;
              $col_no++;
            }
            $gridFieldContent .= <<<EOF
          </tr>
EOF;
          }
          
          $gridFieldContent .= <<<EOF
        </table>
EOF;
    
    // Do not output if grid is empty
    if(!$haveContent) {
      return null;
    }

    return $gridFieldContent;
  }

}
