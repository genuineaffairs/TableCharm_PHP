<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Membership.php 6590 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_Model_DbTable_Membership extends Core_Model_DbTable_Membership {

  protected $_type = 'sitepageevent_event';

  public function isResourceApprovalRequired(Core_Model_Item_Abstract $resource) {
    
    return $resource->approval;
  }
  
	/**
   * Return join members
   *
   * @param int $page_id
   * @param int $viewer_id
   * @param int $ownerId
   * @return Zend_Db_Table_Select
   */
  public function getInvitedMembers($event_id, $viewer_id = null, $ownerId = null) {

		$tableMemberName = $this->info('name');
    $userTable = Engine_Api::_()->getDbtable('users', 'user');
    $userTableName = $userTable->info('name');
		$select = $this->select()
							->setIntegrityCheck(false)
							->from($tableMemberName, array('user_id'))
							->join($userTableName, $userTableName . '.user_id = ' . $tableMemberName . '.user_id', null)
							->where($tableMemberName . '.resource_id = ?', $event_id);
							
		if (!empty($viewer_id))  {
			$select->where($tableMemberName . '.user_id <> ?', $ownerId)
			       ->where($tableMemberName . '.user_id <> ?', $viewer_id);
		}
	  $result = $this->fetchAll($select);
		return $result;
  }
}

?>