<?php

class Zulu_View_Helper_ZuLuFieldValueLoop extends Fields_View_Helper_FieldAbstract {

  public function zuluFieldValueLoop($spec, $partialStructure) {
    if (empty($partialStructure)) {
      return '';
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    if (!($spec instanceof Core_Model_Item_Abstract) || !$spec->getIdentity()) {
      return '';
    }

    if ($spec instanceof User_Model_User) {
      $subject = $spec;
    } else {
      $subject = Engine_Api::_()->core()->getSubject('user');
    }

    // Calculate viewer-subject relationship
    $usePrivacy = ($subject instanceof User_Model_User);
    if ($usePrivacy) {
      $relationship = 'everyone';
      if ($viewer && $viewer->getIdentity()) {
        if ($viewer->getIdentity() == $subject->getIdentity()) {
          $relationship = 'self';
        } else if ($viewer->membership()->isMember($subject, true)) {
          $relationship = 'friends';
        } else {
          $relationship = 'registered';
        }
      }
    }

    // Generate
    $content = '';
    $lastContents = '';
    $lastHeadingTitle = null; //Zend_Registry::get('Zend_Translate')->_("Missing heading");
    $show_hidden = $viewer->getIdentity() ? ($subject->getOwner()->isSelf($viewer) || 'admin' === Engine_Api::_()->getItem('authorization_level', $viewer->level_id)->type) : false;

    $is_zulu = false;
    if($spec instanceof Zulu_Model_Zulu) {
      $is_zulu = true;
    }
    
    // Force show hidden if users belong to appropriate list (defined in engine4_zulu_profileshare table)
    // Apply for Medical Record only
    if (!$show_hidden && $is_zulu) {
      $show_hidden = Engine_Api::_()->zulu()->getShowHidden($subject, $viewer);
    }

    // Make the fields which do not belong to any heading become easier to read
    $li_class = 'zulu-normal-fields';
    
    foreach ($partialStructure as $map) {

      // Get field meta object
      $field = $map->getChild();
      $value = $field->getValue($spec);
      if (!$field || $field->type == 'profile_type')
        continue;
      if (!$field->display && !$show_hidden)
        continue;
      $isHidden = !$field->display;
      
      $multiple_values = false;
      // Get first value object for reference
      $firstValue = $value;
      if (is_array($value)) {
        $firstValue = $value[0];
        if(count($value) > 1) {
          $multiple_values = true;
        }
      }

      // Evaluate privacy
      if ($usePrivacy && !empty($firstValue->privacy) && $relationship != 'self') {
        if ($firstValue->privacy == 'self' && $relationship != 'self') {
          $isHidden = true; //continue;
        } else if ($firstValue->privacy == 'friends' && ($relationship != 'friends' && $relationship != 'self')) {
          $isHidden = true; //continue;
        } else if ($firstValue->privacy == 'registered' && $relationship == 'everyone') {
          $isHidden = true; //continue;
        }
      }

      // Render
      if ($field->type == 'heading') {
        $li_class = '';
        // Heading
        if (!empty($lastContents)) {
          $content .= $this->_buildLastContents($lastContents, $lastHeadingTitle);
          $lastContents = '';
        }
        $lastHeadingTitle = $this->view->translate($field->label);
        $lastSubHeadingTitle = '';
      }
      elseif ($field->type == 'subheading') {
        $lastSubHeadingTitle = <<<EOF
  <li class='separated_list_item'>
    <span>
      <b>{$field->label}</b>
    </span>
  </li>
EOF;
      }
      else {
        // Normal fields
        $tmp = $this->getFieldValueString($field, $value, $spec, $map, $partialStructure, $multiple_values);
        if (!empty($firstValue->value) && !empty($tmp)) {

//          $notice = $isHidden && $show_hidden ? sprintf('<div class="tip"><span>%s</span></div>', $this->view->translate('This field is hidden and only visible to you and MGSL Administrators:')) : '';
          if (!$isHidden || $show_hidden) {
            $separator_tag = $field->type != 'grid' ? '' : '<div style="clear:both"></div>';
            $value_wrapper_tag = $field->type != 'grid' ? 'span' : 'div';
            $value_wrapper_class = $field->type != 'grid' ? '' : 'class="grid-field-value-wrapper"';

            $label = $this->view->translate($field->label);
            
            if(!empty($lastSubHeadingTitle)) {
              $lastContents .= $lastSubHeadingTitle;
              $lastSubHeadingTitle = '';
            }
            
            // Handle with '_' label
            if($label == '_') {
              if($map->field_id == 0) {
                $tmp = "<b>{$tmp}</b>";
              }
              if($lastHeadingTitle != 'FAMILY HISTORY') {
                $extra_class = ' separated_list_item';
              } else {
                $extra_class = '';
              }
              $field_text =
                        "<li class='{$li_class}{$extra_class}' data-field-id={$field->field_id}>
                          <{$value_wrapper_tag} {$value_wrapper_class}>
                            {$tmp}
                          </{$value_wrapper_tag}>";
              if ($lastHeadingTitle != 'FAMILY HISTORY') {
                $field_text .= '</li>';
              } else {
                if ($map->field_id != 0) {
                  $field_text =
                        "<{$value_wrapper_tag} {$value_wrapper_class}>
                            {$tmp}
                          </{$value_wrapper_tag}></li>";
                }
              }
            } else {
              $field_text =
                        "<li class='{$li_class}' data-field-id={$field->field_id}>
                          <span>
                            {$label}
                          </span>
                          {$separator_tag}
                          <{$value_wrapper_tag} {$value_wrapper_class}>
                            {$tmp}
                          </{$value_wrapper_tag}>
                        </li>";
            }
            
            $lastContents .= <<<EOF
    {$field_text}
EOF;
          }
        }
      }
    }

    if (!empty($lastContents)) {
      $content .= $this->_buildLastContents($lastContents, $lastHeadingTitle);
    }

    return $content;
  }

  public function getFieldValueString($field, $value, $subject, $map = null, $partialStructure = null, $multiple_values = false) {
    if ((!is_object($value) || !isset($value->value)) && !is_array($value)) {
      return null;
    }
    
    // Temporarily fix bug for date field of Social Engine
    // If value is empty, return null to avoid date exception in field helper
    if($field->type === 'date' && empty($value->value)) {
      return null;
    }
    
    // @todo This is not good practice:
    // if($field->type =='textarea'||$field->type=='about_me') $value->value = nl2br($value->value);

    $helperName = Engine_Api::_()->fields()->getFieldInfo($field->type, 'helper');
    if (!$helperName) {
      return null;
    }

    $helper = $this->view->getHelper($helperName);
    if (!$helper) {
      return null;
    }

    $helper->structure = $partialStructure;
    $helper->map = $map;
    $helper->field = $field;
    $helper->subject = $subject;
    
    if($multiple_values) {
      $tmp = '';
      foreach($value as $single_value) {
        if($single_value->value) {
          $tmp .= $field->getOption($single_value->value)->label . '<br>';
        }
      }
    } else {
      $tmp = $helper->$helperName($subject, $field, $value);
    }

    unset($helper->structure);
    unset($helper->map);
    unset($helper->field);
    unset($helper->subject);

    return $tmp;
  }

  protected function _buildLastContents($content, $title) {
    if (!$title) {
      return '<ul>' . $content . '</ul>';
    }
    $class = str_replace(" ", "_", strtolower($title));
    return <<<EOF
        <div class="profile_fields profile_fields_{$class}">
          <h4>
            <span>{$title}</span>
          </h4>
          <ul>
            {$content}
          </ul>
        </div>
EOF;
  }

}
