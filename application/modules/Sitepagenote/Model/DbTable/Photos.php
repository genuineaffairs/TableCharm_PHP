<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Photos.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_Model_DbTable_Photos extends Engine_Db_Table {

  protected $_rowClass = 'Sitepagenote_Model_Photo';
  
  const IMAGE_WIDTH = 720;
  const IMAGE_HEIGHT = 720;
  const THUMB_WIDTH = 140;
  const THUMB_HEIGHT = 160;

  /**
   * Gets note photos
   *
   * @param int note_id
   * @return note images
   */
  public function getNotePhotos($note_id) { 	

    $selectImage = $this->select()
            ->from($this->info('name'), array('photo_id', 'user_id', 'album_id', 'file_id', 'title'))
            ->where('note_id 	 = ?', $note_id);

    return $this->fetchAll($selectImage);
  }
  
 /**
   * Get photo detail
   *
   * @param array $params : contain desirable photo info
   * @param array $file
   * @return  object of photo
   */
  public function createPhoto($params, $file) {
    if ($file instanceof Storage_Model_File) {
      $params['file_id'] = $file->getIdentity();
    } else {
      $name = basename($file['tmp_name']);
      $path = dirname($file['tmp_name']);
      $extension = ltrim(strrchr($file['name'], '.'), '.');

      $mainName = $path . '/m_' . $name . '.' . $extension;
      $thumbName = $path . '/t_' . $name . '.' . $extension;

      $image = Engine_Image::factory();
      $image->open($file['tmp_name'])
              ->resize(self::IMAGE_WIDTH, self::IMAGE_HEIGHT)
              ->write($mainName)
              ->destroy();

      $image = Engine_Image::factory();
      $image->open($file['tmp_name'])
              ->resize(self::THUMB_WIDTH, self::THUMB_HEIGHT)
              ->write($thumbName)
              ->destroy();

      $photo_params = array(
          'parent_id' => $params['note_id'],
          'parent_type' => 'sitepagenote_note',
      );

      $photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);
      $thumbFile = Engine_Api::_()->storage()->create($thumbName, $photo_params);
      $photoFile->bridge($thumbFile, 'thumb.normal');
      $params['file_id'] = $photoFile->file_id;
      $params['photo_id'] = $photoFile->file_id;

      @unlink($mainName);
      @unlink($thumbName);
    }

    $row = Engine_Api::_()->getDbtable('photos', 'sitepagenote')->createRow();
    $row->setFromArray($params);
    $row->save();
    return $row;
  }
  
}

?>