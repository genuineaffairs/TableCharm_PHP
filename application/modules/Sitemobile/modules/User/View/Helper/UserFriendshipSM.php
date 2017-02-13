<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: UserFriendshipSM.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class User_View_Helper_UserFriendshipSM extends Zend_View_Helper_Abstract {

  public function userFriendshipSM($user, $viewer = null) {//echo "asdfsadf";die;
    if (null === $viewer) {
      $viewer = Engine_Api::_()->user()->getViewer();
    }

    if (!$viewer || !$viewer->getIdentity() || $user->isSelf($viewer)) {
      return '';
    }

    $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);

    // Get data
    if (!$direction) {
      $row = $user->membership()->getRow($viewer);
    }
    else
      $row = $viewer->membership()->getRow($user);

    // Render
    // Check if friendship is allowed in the network
    $eligible = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.eligible', 2);
    if ($eligible == 0) {
      return '';
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
        return '';
      }
    }

    if (!$direction) {
      // one-way mode
      if (null === $row) {
        return $this->view->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'add', 'user_id' => $user->user_id), $this->view->translate('Follow'), array(
                    'class' => 'smoothbox  userlink userlink-cancel',
                    'onclick' => "sm4.activity.addFriend(this);return false;"
                ));
      } else if ($row->resource_approved == 0) {
        return $this->view->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'cancel', 'user_id' => $user->user_id), $this->view->translate('Cancel Follow Request'), array(
                    'class' => 'smoothbox  userlink userlink-add',
                    'onclick' => "sm4.activity.addFriend(this);return false;"
                ));
      } else {
        return $this->view->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'remove', 'user_id' => $user->user_id), $this->view->translate('Unfollow'), array(
                    'class' => 'smoothbox  userlink userlink-remove',
                    'onclick' => "sm4.activity.addFriend(this);return false;"
                ));
      }
    } else {
      // two-way mode
      if (null === $row || empty($row->resource_id)) {
        return $this->view->htmlLink(array(
                    'route' => 'user_extended',
                    'controller' => 'friends',
                    'action' => 'add',
                    'user_id' => $user->user_id,
                        ), $this->view->translate('Add'), array(
                    'class' => 'smoothbox userlink userlink-add',
                    'onclick' => "sm4.activity.addFriend(this);return false;"
                ));
      } else if ($row->user_approved == 0) {
        return $this->view->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'cancel', 'user_id' => $user->user_id), $this->view->translate('Cancel Request'), array(
                    'class' => 'smoothbox userlink userlink-cancel',
                    'onclick' => "sm4.activity.addFriend(this);return false;"
                ));
      } else if ($row->resource_approved == 0) {
        return $this->view->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'confirm', 'user_id' => $user->user_id), $this->view->translate('Accept Request'), array(
                    'class' => 'smoothbox userlink userlink-accept',
                    'onclick' => "sm4.activity.addFriend(this);return false;"
                ));
      } else if ($row->active) {
        return $this->view->htmlLink(array(
                    'route' => 'user_extended',
                    'controller' => 'friends',
                    'action' => 'remove',
                    'user_id' => $user->user_id,
                        ), $this->view->translate('Remove'), array(
                    'class' => 'smoothbox userlink userlink-remove',
                    'onclick' => "sm4.activity.addFriend(this);return false;"
                ));
      }
    }

    return '';
  }

}