<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Abstract
 *
 * @author abakivn
 */
abstract class Zulu_Plugin_Common_ProfileSharing_Abstract extends Core_Plugin_FormSequence_Abstract {

  protected $_script = array('form/_profileSharing.tpl', 'zulu');
  protected $_accessTypeMap = array(
      'full' => Zulu_Model_DbTable_AccessLevel::FULL,
      'read_only' => Zulu_Model_DbTable_AccessLevel::READ_ONLY,
      'limited' => Zulu_Model_DbTable_AccessLevel::LIMITED,
  );
  protected $_accessTypeString = array(
      'full' => 'Full Access',
      'read_only' => 'Read Only Access',
      'limited' => 'Emergency Summary'
  );
  protected $_shareSubject = 'Medical Record Access';
  protected $_shareMessageBody = '{subject} has shared {access_level} of his/her Medical Record with you';

  protected function _getMessageBody($params) {
    $ret = $this->_shareMessageBody;
    foreach ($params as $key => $value) {
      $ret = str_replace("{{$key}}", $value, $ret);
    }
    return $ret;
  }

  /**
   * Get user id from db (for registered user), from session (for sign up user)
   * 
   * @return int user_id
   */
  abstract public function getUserId();

  public function onProcess() {
    $user_id = $this->getUserId();

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $form = $this->getForm();
    $post_data = $request->getPost();

    if ($request->isPost() && !empty($post_data) && $form->isValid($form->getValues())) {
      $values = $form->getValues();
      $isNotInTransaction = (Zend_Registry::isRegistered('trans_start') && Zend_Registry::get('trans_start') == 0);

      if ($isNotInTransaction) {
        $adapter = Engine_Db_Table::getDefaultAdapter();
        $adapter->beginTransaction();
      }

      $userList = $this->getHighestAccessLevelOfUsers($values);

      // Get pre-access list
      $profileShareTable = Engine_Api::_()->getDbTable('profileshare', 'zulu');
      $access_list = $profileShareTable->getAccessListOfUser($user_id);

      Engine_Api::_()->getDbTable('profileshare', 'zulu')->delete(array(
          'subject_id = ?' => $user_id
      ));

      // Get the user who shares his medical record
      $user = Engine_Api::_()->getItem('user', $user_id);

      foreach ($userList as $key => $list) {
        // If $accessType exists in Zulu_Model_AccessLevel::$accessTypeMap
        if (is_array($list) && array_key_exists($key, Zulu_Model_DbTable_AccessLevel::$accessTypeMap)) {
          foreach ($list as $viewer_id) {
            if ($viewer_id) {
              Engine_Api::_()
                      ->getDbTable('profileshare', 'zulu')
                      ->insert(
                              array(
                                  'subject_id' => $user_id,
                                  'viewer_id' => $viewer_id,
                                  'access_level' => Zulu_Model_DbTable_AccessLevel::$accessTypeMap[$key],
                              )
              );
              if ($user !== null) {
                // bypass sending messages for users whose access is not changed
                if (array_key_exists($key, $access_list) && in_array($viewer_id, $access_list[$key])) {
                  continue;
                }

                // send message to notify users who have access to this user's medical record
                $zulu = Engine_Api::_()->getDbTable('zulus', 'zulu')->getZuluByUserId($user->getIdentity());
                if ($zulu === null) {
                  $zulu = new Zulu_Model_Zulu(array());
                }

                Engine_Api::_()->getItemTable('messages_conversation')->send(
                        $user, array($viewer_id), $this->_shareSubject, $this->_getMessageBody(array('subject' => $user->getTitle(), 'access_level' => $this->_accessTypeString[$key])), null, // No attachment
                        $zulu
                );
              }
            }
          }
        }
      }

      if ($isNotInTransaction) {
        $adapter->commit();
      }

      $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
    }
    // This is not the best way, but it helps regerate Note Fields Values which are overridden by Plugin Session
    $form->init();
  }

  public function onAdminProcess($form) {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $values = $form->getValues();
    $settings->user_signup = $values;
    if ($values['inviteonly'] == 1) {
      $step_table = Engine_Api::_()->getDbtable('signup', 'user');
      $step_row = $step_table->fetchRow($step_table->select()->where('class = ?', 'User_Plugin_Signup_Invite'));
      $step_row->enable = 0;
    }

    $form->addNotice('Your changes have been saved.');
  }

  public function getHighestAccessLevelOfUsers($userList) {
    // Sort user list by access level value
    uksort($userList, function($ka, $kb) {
      $va = Zulu_Model_DbTable_AccessLevel::$accessTypeMap[$ka];
      $vb = Zulu_Model_DbTable_AccessLevel::$accessTypeMap[$kb];

      if ($va == $vb) {
        return 0;
      }
      return ($va < $vb) ? -1 : 1;
    });

    $exclusion = array();

    // For each level list, users will be excluded from higher level list
    foreach ($userList as $key => &$list) {
      if (array_key_exists($key, Zulu_Model_DbTable_AccessLevel::$accessTypeMap)) {
        if (is_string($list)) {
          $list = explode(',', $list);
        }

        if (!is_array($list)) {
          continue;
        }

        $list = array_diff($list, $exclusion);
        $exclusion = array_merge($exclusion, $list);
      }
    }

    return $userList;
  }

}
