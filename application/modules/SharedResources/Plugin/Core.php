<?php

class SharedResources_Plugin_Core
{

  public function onUserCreateAfter($event)
  {
    $payload = $event->getPayload();
    if ($payload instanceof User_Model_User && ($user_id = $payload->getIdentity())) {
      // Get current site id
      $site_id = Engine_Api::_()->getApi('core', 'sharedResources')->getSiteId();

      $usersSitesTable = Engine_Api::_()->getDbTable('usersSites', 'sharedResources');

      // Insert users - sites information
      $usersSitesTable->insert(array(
          'user_id' => $user_id,
          'site_id' => $site_id
      ));
    }
  }

  public function onUserLoginAfter($event)
  {
    $payload = $event->getPayload();
    if ($payload instanceof User_Model_User && ($user_id = $payload->getIdentity())) {
      // Get current site id
      $site_id = Engine_Api::_()->getApi('core', 'sharedResources')->getSiteId();
      $usersSitesInfo = array(
          'user_id = ?' => $user_id,
          'site_id = ?' => $site_id
      );

      // If user does not belong to the current site, make him/her site member
      $usersSitesTable = Engine_Api::_()->getDbTable('usersSites', 'sharedResources');

      $row = $usersSitesTable->fetchRow($usersSitesInfo);

      if (null === $row) {
        $usersSitesTable->insert(array(
            'user_id' => $user_id,
            'site_id' => $site_id
        ));
      }
    }
  }

  public function onUserDeleteAfter($event)
  {
    $payload = $event->getPayLoad();

    if (isset($payload['identity'])) {
      $user_id = $payload['identity'];

      /* @var $usersSitesTable Engine_Db_Table */
      $usersSitesTable = Engine_Api::_()->getDbTable('usersSites', 'sharedResources');
      $usersSitesTable->delete(array(
          'user_id = ?' => $user_id
      ));
    }
  }

}
