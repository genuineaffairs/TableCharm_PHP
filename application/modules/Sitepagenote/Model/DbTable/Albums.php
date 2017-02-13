<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Albums.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_Model_DbTable_Albums extends Engine_Db_Table {

  protected $_rowClass = 'Sitepagenote_Model_Album';

  /**
   * Gets a album
   *
   * @return album object
   */
  public function getSingletonAlbum($note_id, $title = null) {

    $select = $this->select()
            ->where('note_id = ?', $note_id)
            ->order('album_id ASC')
            ->limit(1);

    $album = $this->fetchRow($select);

    if (null === $album) {
      $album = $this->createRow();
      $album->setFromArray(array(
          'title' => $title,
          'note_id' => $note_id,
      ));
      $album->save();
    }
    return $album;
  }

}

?>