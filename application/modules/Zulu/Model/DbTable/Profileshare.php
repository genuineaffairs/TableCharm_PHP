<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProfileShare
 *
 * @author abakivn
 */
class Zulu_Model_DbTable_Profileshare extends SharedResources_Model_DbTable_Abstract {

  /**
   * Get profile access level by subject id and viewer id
   * 
   * @param mixed $subject
   * @param mixed $viewer
   * @return access level
   */
  public function getAccessLevel($subject, $viewer) {

    if ($subject instanceof User_Model_User) {
      $subject_id = $subject->getIdentity();
    } elseif (is_numeric($subject)) {
      $subject_id = $subject;
    } elseif ($subject instanceof Zulu_Model_Zulu) {
      $subject_id = $subject->user_id;
    }

    if ($viewer instanceof User_Model_User) {
      $viewer_id = $viewer->getIdentity();
    } elseif (is_numeric($viewer)) {
      $viewer_id = $viewer;
    }

    if (!is_numeric($viewer_id) || !is_numeric($subject_id)) {
      return false;
    }

    $access_level = $this->select()
            ->from($this->info('name'), 'access_level')
            ->where('subject_id = ?', $subject_id)
            ->where('viewer_id = ?', $viewer_id)
            ->limit(1)
            ->query()
            ->fetchColumn();

    return $access_level;
  }

  public function isShowHidden($subject_id, $viewer_id) {
    $access_level = $this->getAccessLevel($subject_id, $viewer_id);

    return (!empty($access_level) && $access_level <= Zulu_Model_DbTable_AccessLevel::READ_ONLY);
  }

  /**
   * Get Access List of User
   * 
   * @param int $user_id
   */
  public function getAccessListOfUser($user_id) {
    $select = $this->select()->where('subject_id = ?', $user_id);

    // Get accesss list data
    $access_list_data = $this->fetchAll($select);

    // Access list
    $access_list = array();

    // Reversed array of Zulu_Model_DbTable_AccessLevel::$accessTypeMap
    $reversedAccessTypeMap = array_flip(Zulu_Model_DbTable_AccessLevel::$accessTypeMap);

    // Group user ids in access list data by access level
    foreach ($access_list_data as $row) {
      // String display of access level
      $access_level_str = $reversedAccessTypeMap[$row->access_level];
      $access_list[$access_level_str][] = $row->viewer_id;
    }

    return $access_list;
  }

}
