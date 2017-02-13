<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Options.php  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Model_DbTable_Options extends Engine_Db_Table {

  protected $_name = 'user_fields_options';
  protected $_rowClass = 'Communityad_Model_Option';
  /*
   * @params id
   * Returns the corresponding label for the id
   */

  public function getProfileType($id) {
    $select = $this->select()
                    ->where('option_id = ?', $id);
    $result = $this->fetchRow($select);
    return $result->label;
  }

  /*
   * @params id
   * Returns all the profile types
   */

  public function getAllProfileTypes() {
    $select = $this->select()
                    ->where('field_id = ?', 1);
    $result = $this->fetchAll($select);
    return $result;
  }

}