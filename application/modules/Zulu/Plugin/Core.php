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
class Zulu_Plugin_Core extends Zend_Controller_Plugin_Abstract {

  public function routeShutdown(Zend_Controller_Request_Abstract $request)
  {
    $module_name = $request->getModuleName();
    if (($module_name === 'user' || $module_name === 'zulu') && $request->getControllerName() === 'profile') {
//            Zend_Controller_Front::getInstance()->removeControllerDirectory($request->getModuleName());
//            $moduleDir = APPLICATION_PATH_MOD . DS . 'Zulu';
//            Zend_Controller_Front::getInstance()->addControllerDirectory($moduleDir, $request->getModuleName());
//            $request->setActionName('index');
//            $request->setModuleName('zulu');
//      Engine_Api::_()->authorization()->addAdapter(Engine_Api::_()->getDbTable('accessLevel', 'zulu'));
    }
  }

  public function onUserDeleteAfter($event)
  {
    $payload = $event->getPayLoad();

    if (isset($payload['identity'])) {
      $user_id = $payload['identity'];

      $profileShareTable = Engine_Api::_()->getDbTable('profileshare', 'zulu');
      $db = $profileShareTable->getAdapter();

      // Clear profile share records
      $where = $db->quoteInto('subject_id = ?', $user_id);
      $profileShareTable->delete($where);
      $where = $db->quoteInto('viewer_id = ?', $user_id);
      $profileShareTable->delete($where);

      // Clear user verification code
      $userVerifyTable = Engine_Api::_()->getDbTable('verify', 'user');
      $where = $db->quoteInto('user_id = ?', $user_id);
      $userVerifyTable->delete($where);

      // Clear zulu records
      /* @var $zuluTable Zulu_Model_DbTable_Zulus */
      $zuluTable = Engine_Api::_()->getDbTable('zulus', 'zulu');

      $zulu = $zuluTable->getZuluByUserId($user_id);

      $db->delete('engine4_zulu_fields_values', array('item_id = ?' => $zulu->zulu_id));
      $db->delete('engine4_zulu_fields_search', array('item_id = ?' => $zulu->zulu_id));

      $where = $db->quoteInto('user_id = ?', $user_id);
      $zuluTable->delete($where);
    }
  }

  public function onFieldsValuesSave($event)
  {
    $payload = $event->getPayLoad();

    // Process special fields saving
    if (Zend_Registry::isRegistered(('Zend_View')) && ($form = Zend_Registry::get('Zend_View')->form)) {
      foreach ($form->getElements() as $element) {
        if ($element->getType() === 'fileMulti') {
          $element->store();
        }
      }
    }
  }

}
