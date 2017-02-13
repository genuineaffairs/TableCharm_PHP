<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Album.php 6590 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_Model_Album extends Core_Model_Item_Collection {

  protected $_parent_type = 'sitepageevent_event';
  protected $_owner_type = 'sitepageevent_event';
  protected $_children_types = array('sitepageevent_photo');
  protected $_collectible_type = 'sitepageevent_photo';

  /**
   * Gets sitepageevent item
   * 
   * @return sitepageevent item
   */   
  public function getAuthorizationItem() {
    
    return $this->getParent('sitepageevent_event');
  }

  /**
   * Deletes the photos
   */      
  protected function _delete() {
    
    $tablePhoto = Engine_Api::_()->getItemTable('sitepageevent_photo');
    $select = $tablePhoto->select()->where('album_id = ?', $this->getIdentity());
    foreach ($tablePhoto->fetchAll($select) as $eventPhotos) {
      $eventPhotos->delete();
    }
    parent::_delete();
  }

}

?>