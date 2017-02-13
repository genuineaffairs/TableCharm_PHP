<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Fields.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
abstract class Zulu_Plugin_Common_ClinicalFields_Abstract extends Core_Plugin_FormSequence_Abstract {

  /**
   * Get User
   * 
   * @return User_Model_User
   */
  abstract function getUser();

  public function getForm() {
    if (is_null($this->_form)) {
      /* @var $user User_Model_User */
      $user = $this->getUser();
      $config = array();

      if ($user instanceof Core_Model_Item_Abstract) {

        /* @var $zuluTable Zulu_Model_DbTable_Zulus */
        $zuluTable = Engine_Api::_()->getItemTable('zulu');

        /* @var $zulu Zulu_Model_Zulu */
        $zulu = $zuluTable->getZuluByUserId($user->getIdentity());

        // If system cannot find Zulu record belong to user (most likely in case of registering)
        if (is_null($zulu)) {
          // Then create a new default Zulu record
          $zulu = $zuluTable->createRow();
        }

        $config = array('item' => $zulu);
      }

      Engine_Loader::loadClass($this->_formClass);
      $class = $this->_formClass;
      $this->_form = new $class($config);
      $data = $this->getSession()->data;
      if (is_array($data)) {
        $this->_form->populate($data);
      }
    }

    return $this->_form;
  }

  public function onProcess() {
    /* @var $user User_Model_User */
    $user = $this->getUser();

    if (!($user instanceof Core_Model_Item_Abstract)) {
      throw new Engine_Application_Exception('Cannot get user to process');
    }

    // Get form
    Zend_Registry::get('Zend_View')->form = $form = $this->getForm();
    // Get form values
    $values = $form->getValues();
    // Get form item
    $zulu = $form->getItem();
    // Get request object
    $request = Zend_Controller_Front::getInstance()->getRequest();

    if ($request->isPost() && $form->isValid($values)) {
      $isNotInTransaction = (Zend_Registry::isRegistered('trans_start') && Zend_Registry::get('trans_start') == 0);

      if ($isNotInTransaction) {
        $zuluTable = Engine_Api::_()->getItemTable('zulu');
        $db = $zuluTable->getAdapter();
        $db->beginTransaction();
      }

      $zulu->setFromArray($values);

      if (is_null($zulu->user_id)) {
        $zulu->user_id = $user->getIdentity();
      }
      $zulu->modified_date = date('Y-m-d H:i:s');
      $zulu->save();

      $form->setItem($zulu);
      $form->saveValues();

      if ($isNotInTransaction) {
        $db->commit();
      }

      $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
    }
  }

}
