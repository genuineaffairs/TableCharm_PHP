<?php

class Mgslapi_Controller_Action_Helper_ProfileAPI extends Zend_Controller_Action_Helper_Abstract
{

  protected $view;

  const FOLLOW = 0;
  const CANCEL_FOLLOW_REQUEST = 1;
  const UNFOLLOW = 2;
  const ADD_FRIEND = 3;
  const CANCEL_FRIENDSHIP_REQUEST = 4;
  const ACCEPT_FRIENDSHIP_REQUEST = 5;
  const REMOVE_FRIEND = 6;
  const UNIDENTIFIED_ACTION = 7;

  function fieldValueLoop($spec, $partialStructure)
  {

    $arrInfo = array();

    if (empty($partialStructure)) {
      return $arrInfo;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!($spec instanceof Core_Model_Item_Abstract) || !$spec->getIdentity()) {
      return $arrInfo;
    }

    if ($spec instanceof User_Model_User) {
      $subject = $spec;
    } else {
      $subject = Engine_Api::_()->core()->getSubject('user');
    }

    $this->view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    if (!$this->view) {
      return $arrInfo;
    }
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

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
    $lastHeadingTitle = '';
    $lastSubHeadingTitle = '';
    $show_hidden = $viewer->getIdentity() ? ($subject->getOwner()->isSelf($viewer) || 'admin' === Engine_Api::_()->getItem('authorization_level', $viewer->level_id)->type) : false;

    $is_zulu = false;
    if ($spec instanceof Zulu_Model_Zulu) {
      $is_zulu = true;
    }

    // Force show hidden if users belong to appropriate list (defined in engine4_zulu_profileshare table)
    // Apply for Medical Record only
    if (Engine_Api::_()->hasModuleBootstrap('zulu') && !$show_hidden && $is_zulu) {
      $show_hidden = Engine_Api::_()->zulu()->getShowHidden($subject, $viewer);
    }

    foreach ($partialStructure as $map) {

      // Get field meta object
      $field = $map->getChild();
      $value = $field->getValue($spec);
      if (!$field || $field->type == 'profile_type') {
        continue;
      }
      if (!$field->display && !$show_hidden) {
        continue;
      }
      $isHidden = !$field->display;

      $multiple_values = false;
      // Get first value object for reference
      $firstValue = $value;
      if (is_array($value)) {
        $firstValue = $value[0];
        if (count($value) > 1) {
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

      if ($field->type == 'heading') {
        $lastHeadingTitle = $this->view->translate($field->label);
        $arrInfo[$lastHeadingTitle] = array();
        $lastSubHeadingTitle = '';
      } else if ($field->type == 'subheading') {
        $lastSubHeadingTitle = $this->view->translate($field->label);
        $arrInfo[$lastHeadingTitle][$lastSubHeadingTitle] = array('type' => $field->type, 'inside_fields' => array());
      } else if ($lastHeadingTitle != '') {
        // Normal fields
        $tmp = $this->getFieldValueString($field, $value, $spec, $map, $partialStructure, $multiple_values);
        if (!empty($firstValue->value) && !empty($tmp)) {
          if (!$isHidden || $show_hidden) {
            $label = $this->view->translate($field->label);

            if ($label == '_') {
              if ($map->field_id == 0) {
                // Consider '_' questions as subheading
                $lastSubHeadingTitle = $tmp;
                $arrInfo[$lastHeadingTitle][$lastSubHeadingTitle] = array('type' => 'subheading', 'inside_fields' => array());
              }

              // Special handling logic for FAMILY HISTORY section
              if ($lastHeadingTitle === 'FAMILY HISTORY') {
                if ($map->field_id != 0) {
                  $type = 'normal';
                  $arrInfo[$lastHeadingTitle][$lastSubHeadingTitle] = array('type' => $type, 'value' => $tmp);
                }
              }
            } else {
              if ($field->type == 'grid') {
                $tmp = json_decode(htmlspecialchars_decode($value->value));
                $type = 'grid';
                $arrInfo[$lastHeadingTitle][$label] = array('type' => $type, 'value' => $tmp);
              } else {
                $type = 'normal';
                $params = array('type' => $type, 'value' => $tmp);

                if ($field->type == 'file' && $is_zulu) {
                  $params['type'] = $field->type;
                  $params['file'] = $spec->getFileUrl($field->field_id);
                } else if ($field->type == 'fileMulti') {
                  // This is to avoid effect on mobile display at the moment
                  $params['type'] = 'file';
                  $paths = array_filter(explode(',', $value->value));
                  $params['value'] = str_replace('/', '', strrchr($paths[0], '/'));
                  $params['file'] = Engine_Api::_()->zulu()->getRemoteFileUrl($paths[0]);
                }

                if ($lastSubHeadingTitle) {
                  $arrInfo[$lastHeadingTitle][$lastSubHeadingTitle]['inside_fields'][$label] = $params;
                } else {
                  $arrInfo[$lastHeadingTitle][$label] = $params;
                }
              }
            }
          }
        }
      }
    }

    // remove empty sections
    $arrInfo = array_filter($arrInfo);

    // force array output
    $output = $this->_convertJsonObjectToArray($arrInfo);

    return $output;
  }

  /**
   * This function is used to reduce the complication of mobile app when maintaining field order
   * 
   * @param array $input
   * @return array
   */
  protected function _convertJsonObjectToArray($input)
  {
    $output = array();
    // We go from the headings level
    foreach ($input as $key => $item) {
      $tmp = array();
      // To each questions within a heading
      foreach ($item as $sk => $sv) {
        $tmp1 = array();
        // Also modify the questions whithin a subheading
        if ($sv['type'] === 'subheading' && !empty($sv['inside_fields'])) {
          foreach ($sv['inside_fields'] as $skey => $field) {
            $tmp1[] = array($skey => $field);
          }
          $sv['inside_fields'] = $tmp1;
        }
        $tmp[] = array($sk => $sv);
      }
      // Convert the json object attributes to an array of field objects
      $output[] = array($key => $tmp);
    }
    return $output;
  }

  public function getFieldValueString($field, $value, $subject, $map = null, $partialStructure = null, $multiple_values = false)
  {
    if ((!is_object($value) || !isset($value->value)) && !is_array($value)) {
      return null;
    }

    // Temporarily fix bug for date field of Social Engine
    // If value is empty, return null to avoid date exception in field helper
    if ($field->type === 'date' && empty($value->value)) {
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

    if ($multiple_values) {
      $tmp = '';
      foreach ($value as $single_value) {
        if ($single_value->value) {
          $tmp .= $field->getOption($single_value->value)->label . "\r\n";
        }
      }
    } else {
      $tmp = $helper->$helperName($subject, $field, $value);
    }

    unset($helper->structure);
    unset($helper->map);
    unset($helper->field);
    unset($helper->subject);

    return strip_tags($tmp);
  }

  public function getUserRelationshipAction($user, $viewer = null)
  {
    if (null === $viewer) {
      $viewer = Engine_Api::_()->user()->getViewer();
    }

    if (!$viewer || !$viewer->getIdentity() || $user->isSelf($viewer)) {
      return self::UNIDENTIFIED_ACTION;
    }

    $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);

    // Get data
    if (!$direction) {
      $row = $user->membership()->getRow($viewer);
    } else {
      $row = $viewer->membership()->getRow($user);
    }

    // Check if friendship is allowed in the network
    $eligible = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.eligible', 2);
    if ($eligible == 0) {
      return self::UNIDENTIFIED_ACTION;
    }

    // check admin level setting if you can befriend people in your network
    else if ($eligible == 1) {

      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
      $networkMembershipName = $networkMembershipTable->info('name');

      $select = new Zend_Db_Select($networkMembershipTable->getAdapter());
      $select
              ->from($networkMembershipName, 'user_id')
              ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
              ->where("`{$networkMembershipName}`.user_id = ?", $viewer->getIdentity())
              ->where("`{$networkMembershipName}_2`.user_id = ?", $user->getIdentity())
      ;

      $data = $select->query()->fetch();

      if (empty($data)) {
        return self::UNIDENTIFIED_ACTION;
      }
    }

    if (!$direction) {
      // one-way mode
      if (null === $row) {
        return self::FOLLOW;
      } else if ($row->resource_approved == 0) {
        return self::CANCEL_FOLLOW_REQUEST;
      } else {
        return self::UNFOLLOW;
      }
    } else {
      // two-way mode
      if (null === $row) {
        return self::ADD_FRIEND;
      } else if ($row->user_approved == 0) {
        return self::CANCEL_FRIENDSHIP_REQUEST;
      } else if ($row->resource_approved == 0) {
        return self::ACCEPT_FRIENDSHIP_REQUEST;
      } else if ($row->active) {
        return self::REMOVE_FRIEND;
      }
    }

    return self::UNIDENTIFIED_ACTION;
  }

  public function profileAuth()
  {
    $user_id = $this->getActionController()->getRequest()->getParam('user_id');

    $subject = Engine_Api::_()->user()->getUser($user_id);
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer->getIdentity()) {
      $this->getActionController()->jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_SIGN_IN);
    }

    if (!$subject->getIdentity()) {
      $subject = $viewer;
    }

    if (!$subject->authorization()->isAllowed($viewer, 'view')) {
      $this->getActionController()->jsonErrorOutput(Mgslapi_Controller_Action_Helper_Error::NOT_ALLOWED);
    }

    return array('subject' => $subject, 'viewer' => $viewer);
  }

  /**
   * This function is used to parse string inputs from mobile app to proper values which are stored in db
   */
  public function parseFieldInput($field_type = '')
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();
    $fieldValueMaps = include_once APPLICATION_PATH . "/application/modules/Mgslapi/settings/{$field_type}field-value-maps.php";

    foreach ($fieldValueMaps as $fieldName => $fieldInfo) {
      if (array_key_exists($fieldName, $params)) {
        $found_value = array_search($params[$fieldName], $fieldInfo['values']);

        if ($found_value !== false) {
//        $request->setParam($fieldName, $found_value);
          $request->setPost($fieldInfo['field_name'], (string) $found_value);
        }
        // Sub field handling
        if (array_key_exists('subfield', $fieldInfo)) {
          $subField = $fieldInfo['subfield'];
          $subFieldName = $subField['common_name'];
          if (array_key_exists($subFieldName, $params)) {
            if(array_key_exists($params[$fieldName], $subField['options'])) {
              // Translate from app field name to real sub field name
              $subFieldRealName = $subField['options'][$params[$fieldName]]['field_name'];
              // Translate from app string value to real value
              $subFieldRealValue = array_search($params[$subFieldName], $subField['options'][$params[$fieldName]]['values']);
              $request->setPost($subFieldRealName, (string) $subFieldRealValue);
            }
          }
        }
      }
    }
  }

}
