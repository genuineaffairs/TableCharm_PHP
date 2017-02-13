<?php

class Zulu_Plugin_Task_Announcement extends Core_Plugin_Task_Abstract {

  public function execute() {

    /* @var $userTable User_Model_DbTable_Users */
    $userTable = Engine_Api::_()->getDbtable('users', 'user');
    $users = $userTable->fetchAll("creation_date > DATE_SUB(NOW(), INTERVAL {$this->getTask()->timeout} SECOND)");

    $mailParams = array(
        'host' => $_SERVER['HTTP_HOST'],
        'object_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'edit', 'action' => 'clinical'), 'zulu_extended', true),
    );

    foreach ($users as $user) {
      $mailParams['recipient_title'] = $user->getTitle();

      Engine_Api::_()->getApi('mail', 'core')->sendSystem(
              $user, 'zulu_announcement', $mailParams
      );
    }

    // This task shouldn't take too long, just set was idle
//    $this->_setWasIdle();
  }

}
