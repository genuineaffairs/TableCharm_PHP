<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Photo.php 6590 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_Model_Photo extends Core_Model_Item_Collectible {

  protected $_parent_type = 'sitepageevent_album';
  protected $_owner_type = 'user';
  protected $_collection_type = 'sitepageevent_album';

  public function getMediaType() {
    return 'photo';
  }

  public function getHref($params = array()) {
    $params = array_merge(array(
        'route' => 'sitepageevent_photo_extended',
        'reset' => true,
     //   'controller' => 'photo',
        'action' => 'view',
        'event_id' => $this->getCollection()->getOwner()->getIdentity(),
        //'album_id' => $this->collection_id,
        'photo_id' => $this->getIdentity(),
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble($params, $route, $reset);
  }

  public function isSearchable() {
    $collection = $this->getCollection();
    if (!$collection instanceof Core_Model_Item_Abstract) {
      return false;
    }
    return $collection->isSearchable();
  }

  public function getEvent() {
    return Engine_Api::_()->getItem('sitepageevent_event', $this->event_id);
    //return $this->getCollection()->getEvent();
  }

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @parmas int $type
   * @return url
   */
  public function getPhotoUrl($type = null) {

    if (empty($this->file_id)) {
      return null;
    }

    $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, $type);
    if (!$file) {
      return null;
    }

    return $file->map();
  }

  /**
   * Gets sitepageevent item
   * 
   * @return sitepageevent item
   */
  public function getAuthorizationItem() {

    return $this->getParent('sitepageevent_event');
  }

   /**
   * Set photo
   *
   * @param array photo
   * @return photo object
   */
  public function setPhoto($photo) {

    if ($photo instanceof Zend_Form_Element_File) {
      $file = $photo->getFileName();
      $fileName = $file;
    } else if ($photo instanceof Storage_Model_File) {
      $file = $photo->temporary();
      $fileName = $photo->name;
    } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
      $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
      $file = $tmpRow->temporary();
      $fileName = $tmpRow->name;
    } else if (is_array($photo) && !empty($photo['tmp_name'])) {
      $file = $photo['tmp_name'];
      $fileName = $photo['name'];
    } else if (is_string($photo) && file_exists($photo)) {
      $file = $photo;
      $fileName = $photo;
    } else {
      throw new User_Model_Exception('invalid argument passed to setPhoto');
    }

    if (!$fileName) {
      $fileName = $file;
    }

    $name = basename($file);
    $extension = ltrim(strrchr($fileName, '.'), '.');
    $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
        'parent_type' => $this->getType(),
        'parent_id' => $this->getIdentity(),
        'owner_id' => $this->user_id,
        'name' => $fileName,
    );

    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

    $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
            ->resize(720, 720)
            ->write($mainPath)
            ->destroy();

    $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
            ->resize(140, 160)
            ->write($normalPath)
            ->destroy();

    try {
      $iMain = $filesTable->createFile($mainPath, $params);
      $iIconNormal = $filesTable->createFile($normalPath, $params);

      $iMain->bridge($iIconNormal, 'thumb.normal');
    } catch (Exception $e) {
      @unlink($mainPath);
      @unlink($normalPath);
      // Throw
      if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
        throw new Album_Model_Exception($e->getMessage(), $e->getCode());
      } else {
        throw $e;
      }
    }

    @unlink($mainPath);
    @unlink($normalPath);

    $this->modified_date = date('Y-m-d H:i:s');
    $this->file_id = $iMain->file_id;
    $this->save();

    if (!empty($tmpRow)) {
      $tmpRow->delete();
    }

    return $this;
  }
  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   * */
  public function comments() {

    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   * */
  public function likes() {

    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  /**
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   * */
  public function tags() {

    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }

  /**
   * Deletes the photos
   */
  protected function _postDelete() {

    if ($this->_disableHooks)
      return;
    try {
      $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id);
      $file->remove();
      $file = Engine_Api::_()->getItemTable('storage_file')->getFile($this->file_id, 'thumb.normal');
      $file->remove();
      $album = $this->getCollection();
      if ((int) $album->photo_id == (int) $this->getIdentity()) {
        $album->photo_id = $this->getNextCollectible()->getIdentity();
        $album->save();
      }
    } catch (Exception $e) {
      throw $e;
    }
  }

}

?>