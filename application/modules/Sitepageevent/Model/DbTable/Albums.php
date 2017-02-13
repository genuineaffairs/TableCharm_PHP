<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Albums.php 6590 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_Model_DbTable_Albums extends Engine_Db_Table {

  protected $_rowClass = 'Sitepageevent_Model_Album';
    
  /**
   * Get album
   *
   * @return album information
   * */
  public function getSingletonAlbum($event_id) {
    
    $select = $this->select()
            ->where('event_id = ?', $event_id)
            ->order('album_id ASC')
            ->limit(1);

    $album = $this->fetchRow($select);
    if (null === $album) {
      $album = $this->createRow();
      $album->setFromArray(array(
          'event_id' => $event_id
      ));
      $album->save();
    }
    return $album;
  }
}

?>