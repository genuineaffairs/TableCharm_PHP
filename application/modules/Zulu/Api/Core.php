<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Core
 *
 * @author abakivn
 */
class Zulu_Api_Core extends Core_Api_Abstract {

  protected $_enabledMod = array();

  /**
   * get show hidden
   * 
   * @param User_Model_User $subject
   * @param User_Model_User $viewer
   */
  public function getShowHidden(User_Model_User $subject, User_Model_User $viewer)
  {
    // always show hidden fields to admin or owner
    if (($subject->getOwner()->isSelf($viewer) || 'admin' === Engine_Api::_()->getItem('authorization_level', $viewer->level_id)->type)) {
      return true;
    }

    $show_hidden = Engine_Api::_()->getDbTable('profileshare', 'zulu')->isShowHidden($subject->getIdentity(), $viewer->getIdentity());

    return $show_hidden;
  }

  /**
   * Get user id of login user
   * 
   * @return int
   */
  public function getSigninUserId()
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $user_id = $subject->getIdentity();

    return (is_numeric($user_id)) ? $user_id : null;
  }

  /**
   * This function is used to check mobile mode based on Session created by Social Engine Addons Mobile / Tablet Plugin
   * 
   * @return boolean
   */
  public function isMobileMode()
  {
//        return Engine_Api::_()->sitemobile()->isSiteMobileModeEnabled(); // This may cause exception if Mobile Plugin is not installed
    $session = new Zend_Session_Namespace('siteViewModeSM');
    return (isset($session->siteViewModeSM) && in_array($session->siteViewModeSM, array("mobile", "tablet")));
  }

  /**
   * Change field order - Under construction (Unused yet)
   * 
   * @param Fields_Model_Meta $field
   * @param string $position
   * @param string $fieldType
   * @throws Engine_Application_Exception
   */
  public function changeFieldOrder($field, $position, $fieldType = 'zulu')
  {
    $db = $field->getTable()->getAdapter();
    $position_ids = explode('_', $position);

    $db->beginTransaction();

    $mapData = Engine_Api::_()->fields()->getFieldsMaps($fieldType);

    if (!($field instanceof Fields_Model_Meta)) {
      throw new Engine_Application_Exception('Invalid field');
    }

    $positionMap = $mapData->getRowMatching(array(
        'field_id' => $position_ids[0],
        'option_id' => $position_ids[1],
        'child_id' => $position_ids[2],
    ));

    $nextGroup = null;

    foreach ($mapData as $map) {
      if ($map->getChild()->isHeading() && $map->order > $positionMap->order) {
        $nextGroup = $map;
        break;
      }
    }

    // $this->view->field = $field->toArray();

    if (!is_null($nextGroup)) {
      // Create slot to append new field
      $db->query("UPDATE {$positionMap->getTable()->info('name')} SET `order` = `order` + 1 WHERE `order` >= {$nextGroup->order}");
      // Update order of new field
      $db->query("UPDATE {$positionMap->getTable()->info('name')} SET `order` = {$nextGroup->order} WHERE child_id = {$field->field_id}");
    }
    // In case of field editing, move editing field to the end of question list
    elseif (Zend_Controller_Front::getInstance()->getRequest()->getParam('field_id')) {
      $db->query("UPDATE {$positionMap->getTable()->info('name')} "
              . "SET `order` = (SELECT MAX(`order`) + 1 FROM (SELECT `order` FROM {$positionMap->getTable()->info('name')}) AS T)"
              . "WHERE child_id = {$field->field_id}");
    }

    $mapData->getTable()->flushCache();

    // Re-order options belong to fields
    $mapData = Engine_Api::_()->fields()->getFieldsMaps($fieldType);

    $options = array();
    foreach ($mapData as $map) {
      $meta_options = $map->getChild()->getOptions();
      if (is_array($meta_options)) {
        $options = array_merge($options, $meta_options);
      }
    }

    $i = 0;
    foreach ($options as $option) {
      if ($option instanceof Fields_Model_Option) {
        $option->order = ++$i;
        $option->save();
      }
    }

    $db->commit();
  }

  public function addFields(array $fields)
  {
    $fieldApi = Engine_Api::_()->fields();
    $fieldInfo = $fieldApi->getFieldInfo();

    $fieldInfo['fields'] = array_merge($fieldInfo['fields'], $fields);

    $reflectionClass = new ReflectionClass('Fields_Api_Core');
    $reflectionProperty = $reflectionClass->getProperty('_fieldTypeInfo');
    $reflectionProperty->setAccessible(true);
    $reflectionProperty->setValue($fieldApi, $fieldInfo);
  }

  public function removeField($name)
  {
    $fieldApi = Engine_Api::_()->fields();
    $fieldInfo = $fieldApi->getFieldInfo();

    unset($fieldInfo['fields'][$name]);

    $reflectionClass = new ReflectionClass('Fields_Api_Core');
    $reflectionProperty = $reflectionClass->getProperty('_fieldTypeInfo');
    $reflectionProperty->setAccessible(true);
    $reflectionProperty->setValue($fieldApi, $fieldInfo);
  }

  /**
   * Check if module is enabled
   */
  public function isModEnabled($name)
  {
    if (!isset($this->_enabledMod[$name])) {
      $db = Engine_Db_Table::getDefaultAdapter();

      $select = new Zend_Db_Select($db);
      $module = $select->from('engine4_core_modules')
                      ->where('name = ?', $name)
                      ->where('enabled = ?', 1)
                      ->query()->fetchObject();

      $this->_enabledMod[$name] = !empty($module);
    }

    return $this->_enabledMod[$name];
  }

  public function convertLabelToVarName($str)
  {
    return preg_replace('/[\s\n]+/', '_', strtolower($str));
  }

  public function convertVarNameToLabel($str)
  {
    return str_replace('_', ' ', ucfirst($str));
  }

  public function getClinicalProfileTabId()
  {
    $db = Engine_Db_Table::getDefaultAdapter();
    // Get tab id of Medical Record in view profile page
    $tab_id = $db->select()
            ->from('engine4_core_content', 'content_id')
            ->where('`name` = ?', 'zulu.clinical-fields')
            ->query()
            ->fetchColumn();
    return $tab_id;
  }

  /**
   * 
   * @param string $email child's email
   * @return int
   */
  public function getParentIdByEmail($email)
  {
    $db = Engine_Db_Table::getDefaultAdapter();

    $parent_id = $db->select()
            ->from('engine4_users', 'parent_id')
            ->where('`email` = ?', $email)
            ->query()
            ->fetchColumn();

    return $parent_id;
  }

  /**
   * 
   * @param string $email child's email
   * @return User_Model_User
   */
  public function getParentAccountByEmail($email)
  {
    $parent_id = $this->getParentIdByEmail($email);

    $parent = Engine_Api::_()->user()->getUser($parent_id);

    return $parent;
  }

  public function getRemoteFileUrl($path)
  {
    if (Engine_Api::_()->zulu()->isModEnabled('storage')) {
      if ($path) {
        $model = new Storage_Model_File(array('data' => array('storage_path' => $path)));
        $service = Engine_Api::_()->getDbtable('services', 'storage')->getService();
        $file_url = $service->map($model);

        return $file_url;
      }
    }
    return '';
  }
  
  public function extractFileNameFromURL($fileURL)
  {
    return preg_replace('/\?.*$/', '', str_replace('/', '', strrchr($fileURL, '/')));
  }

}
