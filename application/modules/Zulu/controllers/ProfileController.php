<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: ProfileController.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Zulu_ProfileController extends Core_Controller_Action_Standard {

  public function init() {
    // @todo this may not work with some of the content stuff in here, double-check
    $subject = null;
    if (!Engine_Api::_()->core()->hasSubject()) {
      $id = $this->_getParam('id');

      // use viewer ID if not specified
      //if( is_null($id) )
      //  $id = Engine_Api::_()->user()->getViewer()->getIdentity();

      if (null !== $id) {
        $subject = Engine_Api::_()->user()->getUser($id);
        if ($subject->getIdentity()) {
          Engine_Api::_()->core()->setSubject($subject);
        }
      }
    }

    $this->_helper->requireSubject('user');
    $this->_helper->requireAuth()->setNoForward()->setAuthParams(
            $subject, Engine_Api::_()->user()->getViewer(), 'view'
    );
  }

  public function indexAction() {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    // check public settings
    $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_profile;
    if (!$require_check && !$this->_helper->requireUser()->isValid()) {
      return;
    }

    // Check enabled
    if (!$subject->enabled && !$viewer->isAdmin()) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    // Check block
    if ($viewer->isBlockedBy($subject) && !$viewer->isAdmin()) {
      return $this->_forward('requireauth', 'error', 'core');
    }

    // Increment view count
    if (!$subject->isSelf($viewer)) {
      $subject->view_count++;
      $subject->save();
    }


    // Check to see if profile styles is allowed
    $style_perm = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $subject->level_id, 'style');
    if ($style_perm) {
      // Get styles
      $table = Engine_Api::_()->getDbtable('styles', 'core');
      $select = $table->select()
              ->where('type = ?', $subject->getType())
              ->where('id = ?', $subject->getIdentity())
              ->limit();

      $row = $table->fetchRow($select);
      if (null !== $row && !empty($row->style)) {
        $this->view->headStyle()->appendStyle($row->style);
      }
    }

    // Render
    $this->_helper->content
            ->setNoRender()
            ->setEnabled()
    ;
  }

  public function printAction() {

    if (Zend_Registry::isRegistered('Zend_View')) {
      $view = Zend_Registry::get('Zend_View');

      if ($view) {
        $files = array('print.css', 'main.css');
        foreach ($files as $file) {
          $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl . 'application/modules/Zulu/externals/css/' . $file, 'screen, print');
        }
      }
    }

    $viewer = Engine_Api::_()->user()->getViewer();

    if (Engine_Api::_()->core()->hasSubject()) {
      $subject = Engine_Api::_()->core()->getSubject('user');
    } else {
      return $this->_helper->requireAuth()->forward();
    }

    $zulu = Engine_Api::_()->getDbTable('zulus', 'zulu')->getZuluByUserId($subject->user_id);

    if (!Engine_Api::_()->getDbTable('accessLevel', 'zulu')->isAllowed($zulu, $viewer, 'print')) {
      return $this->_helper->requireAuth()->forward();
    }

    $user = $subject;
    $userMetaData = Engine_Api::_()->fields()->getFieldsMeta('user');
    $zuluMetaData = Engine_Api::_()->fields()->getFieldsMeta('zulu');

    $structureTop = Engine_Api::_()->fields()->getFieldStructureTop($zulu);

    $preHeader = '';

    $childhoodDiseases = $immunisations = $allergies = $operations = $medications = $others = $overseas_travel = $personal_history = $physical_history = $family_history = array();
    $diseasesCount = $immunisationsCount = $allergiesCount = $operationsCount = $medicationsCount = $personal_history_count = $othersCount = $family_history_count = 0;

    $dateFormat = 'd/m/Y';

    foreach ($structureTop as $map) {
      /* @var $field Fields_Model_Meta */
      $field = $map->getChild();
      if ($field->isHeading()) {
        $preHeader = $field->label;
      }

      // Get field value
      $value = $field->getValue($zulu);

      if (is_array($value)) {
        $value = $value[0];
      }

      // Handle data for personal history
      if (!$field->isHeading() && $preHeader === 'PERSONAL HISTORY') {
        // If field has value
        if (!empty($value) && !empty($value->value)) {
          $options = $field->getOptions();
          $label = $options[0]->label;
          $currentlyAffected = false;
          $date = '';

          $childMaps = $field->getParentMaps();

          foreach ($childMaps as $childMap) {
            $childField = $childMap->getChild();
            $childValue = $childField->getValue($zulu);

            if (is_array($childValue)) {
              $childValue = $childValue[0];
            }

            if (!empty($childValue) && !empty($childValue->value)) {
              // Dangerous when data changes
              if (preg_match('/currently affected/', $childField->alias)) {
                $selectedOptionId = $childValue->value;

                if (strtolower($childField->getOption($selectedOptionId)->label) === 'yes') {
                  $currentlyAffected = true;
                }
              }

              // Dangerous when data changes
              if (preg_match('/first occur/', $childField->alias)) {
                $date = $childValue->value;
              }
            }
          }

          if ($label) {
            $childhoodDiseases[$diseasesCount]['label'] = $label;
            $childhoodDiseases[$diseasesCount]['currentlyAffected'] = $currentlyAffected;
            if ($date) {
              $date = date($dateFormat, strtotime($date));
            }
            $childhoodDiseases[$diseasesCount]['date'] = $date;
          }

          $diseasesCount++;
        }
      }

      // Handle data for immunisation
      if (!$field->isHeading() && $preHeader === 'IMMUNISATIONS') {
        $immunisationData = json_decode(html_entity_decode($value->value), true);

        foreach ($immunisationData as $immunisation) {
          // Dangerous when data changes
          $immunisations[$immunisationsCount]['items'] = $immunisation;
          $immunisations[$immunisationsCount]['label'] = $immunisation['Type'];
          $immunisations[$immunisationsCount]['date'] = $immunisation['Approximate date'];
          $immunisationsCount++;
        }
        $immunisations['headings'] = array_keys($immunisations[0]['items']);
      }

      // Handle data for allergies
      if (!$field->isHeading() && $preHeader === 'ALLERGIES') {
        // If field has value
        if (!empty($value) && !empty($value->value)) {
          $options = $field->getOptions();
          $label = $options[0]->label;
          $currentlyAffected = false;
          $date = '';

          $childMaps = $field->getParentMaps();

          if ($label) {
            $allergies[$allergiesCount]['label'] = $label;
//            $allergies[$allergiesCount]['actionTaken'] = $actionTaken;
            foreach ($childMaps as $childMap) {
              $childField = $childMap->getChild();
              $childValue = $childField->getValue($zulu);

              if (is_array($childValue)) {
                $childValue = $childValue[0];
              }

              if (!empty($childValue) && !empty($childValue->value)) {
                // Dangerous when data changes
//                if (preg_match('/action taken/', $childField->alias)) {
//                  $actionTaken = $childValue->value;
//                }
                $allergies[$allergiesCount][$childField->label] = $childValue->value;
              }
            }
          }

          $allergiesCount++;
        }
      }

      // Handle data for operations
      if (!$field->isHeading() && $preHeader === 'OPERATIONS') {
        $operationData = json_decode(html_entity_decode($value->value), true);

        foreach ($operationData as $operation) {
          if (!$operation['Date'] && !$operation['Detail']) {
            continue;
          }
          // Dangerous when data changes
          $operations[$operationsCount]['date'] = $operation['Date'];
          $operations[$operationsCount]['details'] = $operation['Detail'];
          $operationsCount++;
        }
      }

      // Handle data for medications
      if (!$field->isHeading() && $preHeader === 'MEDICATIONS') {
        $medications['taking_medications'] = false;
        $medications['list'] = null;

        // If field has value
        if (!empty($value) && !empty($value->value)) {
          // If taking medications is yes
          if ($field->alias === 'taking medications' && strtolower($field->getOption($value->value)->label) === 'yes') {
            $childMaps = $field->getParentMaps();

            $medications['taking_medications'] = true;
            $medications['list'] = array();

            foreach ($childMaps as $map) {
              $childField = $map->getChild();
              if ($childField->alias === 'medications list') {
                $childValue = $childField->getValue($zulu);

                if (is_array($childValue)) {
                  $childValue = $childValue[0];
                }

                if (!empty($childValue) && !empty($childValue->value)) {
                  $medicationData = json_decode(html_entity_decode($childValue->value), true);

                  foreach ($medicationData as $medication) {
                    // Dangerous when data changes
                    if ($medication['Medication']) {
                      foreach ($medication as $key => $value) {
                        if (!empty($value)) {
                          $medications['list'][$medicationsCount][$key] = $medication[$key];
                        }
                      }
                      $medicationsCount++;
                    }
                  }
                }
                break;
              }
            }
            $medications['headings'] = array_keys($medications['list'][0]);
          }
        }
      }

      // Handle data for others
      if (!$field->isHeading() && $preHeader === 'OTHERS') {
        if ($field->alias && !empty($field->getValue($zulu)->value)) {
          if (count($field->getOptions()) > 0) {
            $others[str_replace(' ', '_', trim($field->alias))]['value'] = $field->getOption($field->getValue($zulu)->value)->label;

            $childMaps = Engine_Api::_()->fields()->getFieldsStructureFull($zulu, $field->field_id, $field->getValue($zulu)->value);

            if ($others[str_replace(' ', '_', trim($field->alias))]['value'] === 'Yes' && count($childMaps) > 0) {
              $childMap = reset($childMaps);
              if ($childMap) {
                $others[str_replace(' ', '_', trim($field->alias))]['reason'] = $childMap->getChild()->getValue($zulu)->value;
              }
            }
          } else {
            $others[str_replace(' ', '_', trim($field->alias))]['value'] = $field->getValue($zulu)->value;
          }
        }
      }

      // Handle data for international travel insurance
      if (!$field->isHeading() && $preHeader === 'INTERNATIONAL TRAVEL INSURANCE') {
        $decoded_data_rows = json_decode(html_entity_decode($value->value), true);

        $travel_insurance['headings'] = array_keys($decoded_data_rows[0]);

        foreach ($decoded_data_rows as $row) {
          $row_values = array_values(array_filter($row));
          if (empty($row_values)) {
            continue;
          }
          $travel_insurance['rows'][] = array_values($row);
        }
      }

      // Handle data for next of kin
      if (!$field->isHeading() && $preHeader === 'NEXT OF KIN') {
        $decoded_data_rows = json_decode(html_entity_decode($value->value), true);

        $next_of_kin['headings'] = array_keys($decoded_data_rows[0]);

        foreach ($decoded_data_rows as $row) {
          $row_values = array_values(array_filter($row));
          if (empty($row_values)) {
            continue;
          }
          $next_of_kin['rows'][] = array_values($row);
        }
      }

      // Handle data for blood type
      if (!$field->isHeading() && $preHeader === 'BLOOD TYPE') {
        $blood_type['heading'] = 'Blood type';
        $blood_type['value'] = (string) $field->getOption($value->value)->label;
      }

      // Handle data for overseas travel
      if (!$field->isHeading() && $preHeader === 'OVERSEAS TRAVEL') {
        $value_string = '';
        foreach ($field->getValue($zulu) as $value) {
          $value_string .= $field->getOption($value->value)->label . '<br>';
        }
        $value_string = preg_replace('/\<br\>$/', '', $value_string);

        $overseas_travel[$field->label] = $value_string;
      }

      // Handle data for personal history (again)
      if (!$field->isHeading() && $preHeader === 'PERSONAL HISTORY') {
        if (!empty($value) && !empty($value->value)) {
          $options = $field->getOptions();
          $label = $options[0]->label;

          $childMaps = $field->getParentMaps();

          if ($label) {
            $personal_history[$personal_history_count]['Disease'] = $label;

            foreach ($childMaps as $childMap) {
              $childField = $childMap->getChild();
              $childValue = $childField->getValue($zulu);

              $child_label = $childField->label;

              if ($childField->type === 'radio') {
                $child_value = $childField->getOption($childValue->value)->label;
              } else {
                $child_value = $childValue->value;
              }

              if ($childField->type === 'date') {
                $child_value = date($dateFormat, strtotime($child_value));
              }

              if (!empty($childValue) && !empty($childValue->value)) {
                $personal_history[$personal_history_count][$child_label] = $child_value;
              }
            }

            $personal_history_count++;
          }
        }
      }

      // Handle data for physical history
      if (!$field->isHeading() && $preHeader === 'PHYSICAL HISTORY') {
        $value_str = $field->getOption($value->value)->label;

        $physical_history[$field->label] = array('value' => $value_str);

        if (strtolower(trim($physical_history[$field->label]['value'])) === 'yes') {
          $physical_history[$field->label]['list'] = array();

          $childMaps = $field->getParentMaps();
          foreach ($childMaps as $childMap) {
            $childField = $childMap->getChild();
            $childValue = $childField->getValue($zulu);

            if ($childField->type === 'grid') {
              $decoded_data_rows = json_decode(html_entity_decode($childValue->value), true);
              $physical_history[$field->label]['list'] = $decoded_data_rows;
              $physical_history[$field->label]['list_title'] = $childField->label;
            }
          }
        }
      }

      // Handle data for family history
      if (!$field->isHeading() && $preHeader === 'FAMILY HISTORY') {
        if (!empty($value) && !empty($value->value)) {
          $options = $field->getOptions();
          $label = $options[0]->label;

          $childMaps = $field->getParentMaps();

          if ($label) {
            $family_history[$family_history_count]['Disease'] = $label;
            $family_history[$family_history_count]['Affected Family Members'] = '';

            foreach ($childMaps as $childMap) {
              $childField = $childMap->getChild();
              $childValue = $childField->getValue($zulu);

              if ($childField->type === 'multi_checkbox') {
                foreach ($childValue as $subValue) {
                  $family_history[$family_history_count]['Affected Family Members'] .= $childField->getOption($subValue->value)->label . ', ';
                }
                $family_history[$family_history_count]['Affected Family Members'] = preg_replace('/, $/', '', $family_history[$family_history_count]['Affected Family Members']);
              }
            }

            $family_history_count++;
          }
        }
      }

      // Handle data for lifestyle history
      if (!$field->isHeading() && $preHeader === 'LIFESTYLE HISTORY' && $field->type != 'subheading') {
        $value_str = '';

        if ($field->type === 'radio') {
          $value_str = $field->getOption($value->value)->label;
        } else if ($field->type === 'multi_checkbox') {
          foreach ($field->getValue($zulu) as $value) {
            $value_str .= $field->getOption($value->value)->label . '<br>';
          }
          $value_str = preg_replace('/\<br\>$/', '', $value_str);
        } else {
          $value_str = nl2br($value->value);
        }

        $lifestyle_history[$field->label] = $value_str;
      }
    }

    $is_allow_read_full = Engine_Api::_()->zulu()->getShowHidden($subject, $viewer);
    if ($this->getRequest()->getParam('mode') == 'emergency') {
      $show_hidden = false;
    } else {
      if ($is_allow_read_full) {
        $show_hidden = true;
      }
    }

    $this->view->bodyData = array(
        'is_allow_read_full' => $is_allow_read_full,
        'show_hidden' => $show_hidden,
        'zulu' => $zulu,
        'user' => $user,
        'userMetaData' => $userMetaData,
        'zuluMetaData' => $zuluMetaData,
        'dateFormat' => $dateFormat,
        // form part data for rendering in printing document
        'formPartData' => array(
            'childhoodDiseases' => $childhoodDiseases,
            'immunisations' => $immunisations,
            'allergies' => $allergies,
            'operations' => $operations,
            'medications' => $medications,
            'others' => $others,
            'travel_insurance' => $travel_insurance,
            'next_of_kin' => $next_of_kin,
            'blood_type' => $blood_type,
            'overseas_travel' => $overseas_travel,
            'personal_history' => $personal_history,
            'physical_history' => $physical_history,
            'family_history' => $family_history,
            'lifestyle_history' => $lifestyle_history,
        )
    );

    $this->_helper->layout->disableLayout();
    $this->renderScript('profile/print.tpl');
  }

}
