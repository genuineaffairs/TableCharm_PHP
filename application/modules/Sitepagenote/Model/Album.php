<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Album.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_Model_Album extends Core_Model_Item_Collection {

  protected $_parent_type = 'sitepagenote_note';
  protected $_owner_type = 'sitepagenote_note';
  protected $_searchTriggers = false;
  protected $_children_types = array('sitepagenote_photo');
  protected $_collectible_type = 'sitepagenote_photo';

  /**
   * Gets sitepagenote item
   * 
   * @return sitepagenote item
   */
  public function getAuthorizationItem() {
    
    return $this->getParent('sitepagenote_note');
  }

  /**
   * Deletes the photos
   */
  protected function _delete() {
    
    $photoTable = Engine_Api::_()->getItemTable('sitepagenote_photo');
    $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
    foreach ($photoTable->fetchAll($photoSelect) as $sitepagenotePhoto) {
      $sitepagenotePhoto->delete();
    }
    parent::_delete();
  }

}

?>