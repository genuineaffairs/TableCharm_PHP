<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Metas.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Model_DbTable_Metas extends Engine_Db_Table {

  protected $_name = 'user_fields_meta';
  protected $_rowClass = 'Communityad_Model_Meta';

  public function getMetaById($id) {
    $select = $this->select()
                    ->where('field_id=?', $id)
                    ->limit(1);
    return $result = $this->fetchAll($select);
  }

  public function getFields($mp_id = 1) {
    //Pickup the dynamic values in the fields_meta table according to the profile type
    $rmetaName = $this->info('name');
    $maptable = Engine_Api::_()->getItemTable('map');
    $rmapName = $maptable->info('name');
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($this, array($rmetaName . '.field_id', $rmetaName . '.label', $rmetaName . '.type'))
                    ->join($rmapName, $rmapName . '.child_id = ' . $rmetaName . '.field_id', array())
                    ->where($rmapName . '.option_id = ?', $mp_id)
                    ->where($rmetaName . '.type <> ?', 'heading');
    $checkval = $this->fetchAll($select);

    //Dynamic select_option created here
    $storeIndex;
    $selectOption = array();
    foreach ($checkval->toarray() as $key => $value) {

      foreach ($value as $k => $v) {

        if ($k == 'field_id')
          $storeIndex = $v;
        if ($k == 'label')
          $selectOption[$storeIndex]['lable'] = $v;
        if ($k == 'type')
          $selectOption[$storeIndex]['type'] = $v;
      }
    }


    return $selectOption;
  }

// GET USER PROFILE_ID
  public function getUserProfileId($viewer_id=null) {
    if (empty($viewer_id)) {
      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    }
    $rmetaName = $this->info('name');
    $valuetable = Engine_Api::_()->getDbTable('values', 'communityad');
    $rvalueName = $valuetable->info('name');

    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($rmetaName, array($rvalueName . '.value'))
                    ->join($rvalueName, $rvalueName . '.field_id = ' . $rmetaName . '.field_id', null)
                    ->where($rvalueName . '.item_id = ?', $viewer_id)
                    ->where($rvalueName . '.field_id = ?', 1);
    $profile = $this->fetchRow($select);
    if (empty($profile))
      $profileID = 0;
    else
      $profileID=$profile->value;
    return $profileID;
  }

}