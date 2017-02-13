<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Adtypes.php  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Communityad_Model_DbTable_Adtypes extends Engine_Db_Table {

  protected $_name = 'communityad_adtype';
  protected $_rowClass = 'Communityad_Model_Adtype';

  // RETURN THE VALUE: WHICH PLUGIN STATUS ARE ENABLED.
  public function getAdType($adTypeArray1) {
	if (empty($adTypeArray)) {
	  $adTypeArray = array();
	}
	$adStatuseArray = $resultArray = array();

	$adTypeArray[0] = Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.title', 'Community Ads');
	$adStatuseArray[] = 0;

	$name = $this->info('name');
	$select = $this->select()->from($name);
	$pluginsArray = $select->query()->fetchAll();
	if (!empty($pluginsArray)) {
	  foreach ($pluginsArray as $adtype) {
		if (!in_array($adtype['type'], $adTypeArray1)) {
		  $getTempTitle = $adtype['title'];
		  $adTypeArray[$adtype['adtype_id']] = $getTempTitle;
		  if (!empty($adtype['status'])) {
			$adStatuseArray[] = $adtype['adtype_id'];
		  }
		}
	  }
	}
	$resultArray['multiOptions'] = $adTypeArray;
	$resultArray['value'] = $adStatuseArray;
	return $resultArray;
  }

  // SET THE VALUE IN THE DATA BASE.
  public function setSettings($values) {
	$status = 0;
	$name = $this->info('name');
	$select = $this->select()->from($name, array('adtype_id'));
	$pluginsArray = $select->query()->fetchAll();
	if (!empty($pluginsArray)) {
	  foreach ($pluginsArray as $adtype) {
		$adTypeId = $adtype['adtype_id'];
		if (in_array($adTypeId, $values)) {
		  $status = 1;
		}
		$this->update(array("status" => $status), array("adtype_id =?" => $adTypeId));
	  }
	}
	return;
  }

  public function getEnableAdType() {
	$select = $this->select()
					->where('status = ?', 1);
	return $this->fetchAll($select);
  }

  // SET THE VALUE IN THE DATA BASE.
  public function getStatus($type) {
	$result = false;
	$name = $this->info('name');
	$select = $this->select()->from($name, array('status'))->where('type =?', $type);
	$pluginsArray = $select->query()->fetchAll();
	if (!empty($pluginsArray)) {
	  $result = $pluginsArray[0]['status'];
	}
	return $result;
  }

}