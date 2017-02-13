<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Targets.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Model_DbTable_Targets extends Engine_Db_Table {

  protected $_name = 'communityad_target';
  protected $_rowClass = 'Communityad_Model_Target';
  /* Returns all the fields set to the corresponding package_id
   */

  public function getFields() {
    // Get type
    $select = $this->select()
            ->order('mp_id ASC')
            ->order('field_id ASC');
    $result = $this->fetchAll($select);
    return $result;
  }

  public function setVal($field_id, $mp_id) {
    
    $row = $this->createRow();
    $row->field_id = $field_id;
    $row->mp_id = $mp_id;

    $row->save();
    return $this;
  }

}