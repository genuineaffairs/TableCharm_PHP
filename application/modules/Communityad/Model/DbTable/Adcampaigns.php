<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Adcampaigns.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Model_DbTable_Adcampaigns extends Engine_Db_Table {

  protected $_name = 'communityad_adcampaigns';
  protected $_rowClass = 'Communityad_Model_Adcampaign';

  public function getCampsByText($text = null, $limit = 10) {

    $select = $this->select()
            ->order('name ASC')
            ->limit($limit);
    if ($text) {
      $select->where('name LIKE ?', '%' . $text . '%');
    }

    return $this->fetchAll($select);
  }

  public function getCampTable() {
    return $this;
  }

  public function getUserCampaigns($owner_id) {
    $select = $this->select()
            ->where('owner_id = ?', $owner_id)
            ->order('name ASC');
    return $this->fetchAll($select);
  }

  public function getCampaignsIds($owner_id = null) {
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($this, "adcampaign_id");
    if ($owner_id)
      $select->where('owner_id = ?', $owner_id);
    return $select->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);
    ;
  }

}