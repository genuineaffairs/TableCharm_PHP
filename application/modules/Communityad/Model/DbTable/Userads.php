<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Userads.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Model_DbTable_Userads extends Engine_Db_Table {

  protected $_name = 'communityad_userads';
  protected $_rowClass = 'Communityad_Model_Userad';

  // DISAPROVED AFTER EXPIRY LISTING
  public function updateApproved($params=array()) {
    $rName = $this->info('name');
    // for free package advertiesment
    $select = $this->select()
                    ->where('expiry_date <= ?', date('Y-m-d'))
                    ->where('payment_status = ? or payment_status="active"', 'free')
                    ->where('status <= ?', 2);
    foreach ($this->fetchAll($select) as $userad) {
      $this->update(array(
          'approved' => 0,
          'enable' => 0,
          'status' => 3
              ), array(
          'expiry_date <=?' => date('Y-m-d'),
          'status <= ?' => 2,
      ));
      if ($userad->payment_status == 'active')
        $this->update(array(
            'payment_status' => 'expired'
                ), array(
            'expiry_date <=?' => date('Y-m-d'),
            'status <= ?' => 2,
        ));
      Engine_Api::_()->communityad()->sendMail("EXPIRED", $userad->userad_id);
    }

    // after completing end date change status
    $this->update(array(
        'status' => 3
            ), array(
        'cads_end_date <=?' => date('Y-m-d H:i:s'),
        'status <= ?' => 2,
    ));

    Engine_Api::_()->getApi('settings', 'core')->setSetting('communityad.update.approved', date('Y-m-d'));
  }

// 	public function getObject($getIds) {
// 		if( empty($getIds) ){ return; }
// 		$getIdsStr = implode(',', $getIds);
// 		$tableName = $this->info('name');
// 		$select = $this->select()
// 								->where("userad_id IN (". $getIdsStr .")");
// 		$getObj = $this->fetchAll($select);
// 		if( !empty($getObj) ){ return $getObj; }
// 	}

	// RETURN THAT "PASSING PARAMETER" ROW EXIST OR NOT [ IF ROW NOT EXIST THEN WE ARE NOT SHOWING THAT CONTENT IN DROP DOWN IN THE TIME OF "SPONCERD STORY" CREATION. ]
	public function isStory( $resourceType, $resourceId, $storyType ) {
		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		$tableName = $this->info('name');
		$select = $this->select()
			->where("resource_type =?", $resourceType)
			->where("resource_id =?", $resourceId)
			->where("owner_id =?", $viewer_id)
			->where("story_type =?", $storyType);
		$getObj = $this->fetchAll($select);
		return $getObj->COUNT();
	}
}