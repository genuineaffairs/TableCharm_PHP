<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Event.php 6590 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_Model_Event extends Core_Model_Item_Abstract {

  protected $_owner_type = 'user';
  protected $_parent_is_owner = true;

  public function getMediaType() {
    return 'event';
  }

  /**
   * Return membership table object
   *
   * @return Engine_ProxyObject
   * */
  public function membership() {

    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('membership', 'sitepageevent'));
  }

  /**
   * Return a trunacte ownername
   *
   * @return truncate ownername
   * */
  public function truncateOwner($owner_name) {

    $tmpBody = strip_tags($owner_name);
    return ( Engine_String::strlen($tmpBody) > 10 ? Engine_String::substr($tmpBody, 0, 10) . '..' : $tmpBody );
  }

  public function getParentPage() {
    return Engine_Api::_()->getItem('sitepage_page', $this->page_id);
    //return $this->getCollection()->getEvent();
  }
  /**
   * Set a photo
   *
   * @param array photo
   * @return photo object
   */
  public function setPhoto($photo) {

    if ($photo instanceof Zend_Form_Element_File) {
      $file = $photo->getFileName();
    } else if (is_array($photo) && !empty($photo['tmp_name'])) {
      $file = $photo['tmp_name'];
    } else if (is_string($photo) && file_exists($photo)) {
      $file = $photo;
    } else {
      $error_msg1 = Zend_Registry::get('Zend_Translate')->_('invalid argument passed to setPhoto');
      throw new Event_Model_Exception($error_msg1);
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
        'parent_id' => $this->getIdentity(),
        'parent_type' => 'sitepageevent_event'
    );

    $storage = Engine_Api::_()->storage();

    $image = Engine_Image::factory();
    $image->open($file)
            ->resize(720, 720)
            ->write($path . '/m_' . $name)
            ->destroy();

    $image = Engine_Image::factory();
    $image->open($file)
            ->resize(200, 400)
            ->write($path . '/p_' . $name)
            ->destroy();

    $image = Engine_Image::factory();
    $image->open($file)
            ->resize(140, 160)
            ->write($path . '/in_' . $name)
            ->destroy();

    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
            ->write($path . '/is_' . $name)
            ->destroy();

    $iMain = $storage->create($path . '/m_' . $name, $params);
    $iProfile = $storage->create($path . '/p_' . $name, $params);
    $iIconNormal = $storage->create($path . '/in_' . $name, $params);
    $iSquare = $storage->create($path . '/is_' . $name, $params);

    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare, 'thumb.icon');

    @unlink($path . '/p_' . $name);
    @unlink($path . '/m_' . $name);
    @unlink($path . '/in_' . $name);
    @unlink($path . '/is_' . $name);

    $this->modified_date = date('Y-m-d H:i:s');
    $this->photo_id = $iMain->file_id;
    $this->save();

    $tablePhoto = Engine_Api::_()->getItemTable('sitepageevent_photo');
    $eventAlbum = Engine_Api::_()->getDbTable('albums', 'sitepageevent')->getSingletonAlbum($this->getIdentity());
    $photoRow = $tablePhoto->createRow();
    $photoRow->setFromArray(array(
        'event_id' => $this->getIdentity(),
        'album_id' => $eventAlbum->getIdentity(),
        'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
        'file_id' => $iMain->getIdentity(),
        'collection_id' => $eventAlbum->getIdentity(),
    ));
    $photoRow->save();
    return $this;
  }

  public function getSingletonAlbum() {
    $table = Engine_Api::_()->getItemTable('sitepageevent_album');
    $select = $table->select()
            ->where('event_id = ?', $this->getIdentity())
            ->order('album_id ASC')
            ->limit(1);

    $album = $table->fetchRow($select);

    if (null === $album) {
      $album = $table->createRow();
      $album->setFromArray(array(
          'event_id' => $this->getIdentity()
      ));
      $album->save();
    }

    return $album;
  }

  /**
   * Return a description
   *
   * @return description
   * */
  public function getDescription() {

    // @TODO DECIDE HOW WE WANT TO HANDLE MULTIBYTE STRING FUNCTIONS
    $tmpBody = strip_tags($this->description);
    return ( Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody );
  }

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array()) {
    $tab_id='';
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
		if(!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
			$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepageevent.sitemobile-profile-sitepageevents', $this->page_id, $layout);
		} else {
			$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepageevent.profile-sitepageevents', $this->page_id, $layout);
		}

    $params = array_merge(array(
        'route' => 'sitepageevent_detail_view',
        'reset' => true,
        'user_id' => $this->user_id,
        'event_id' => $this->event_id,
        'slug' => $this->getSlug(),
        'tab_id' => $tab_id,
            ), $params);

    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);

    return Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble($params, $route, $reset);
  }

  /**
   * Return a page slug
   *
   * @return slug
   * */
  public function getSlug($str = null) {
    
    if( null === $str ) {
      $str = $this->getTitle();
    }

    $slug = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($str))), '-');
    return $slug;
  }

  /**
   * Deletes the photos
   */
  protected function _delete() {

    if ($this->_disableHooks)
      return;

    $this->membership()->removeAllMembers();
    $tablePhoto = Engine_Api::_()->getItemTable('sitepageevent_photo');
    $select = $tablePhoto->select()->where('event_id = ?', $this->getIdentity());
    foreach ($tablePhoto->fetchAll($select) as $eventPhoto) {
      $eventPhoto->delete();
    }

    // Delete create activity feed of event before delete event 
    Engine_Api::_()->getApi('subCore', 'sitepage')->deleteCreateActivityOfExtensionsItem($this, array('sitepageevent_new', 'sitepageevent_admin_new'));

    parent::_delete();
  }

  public function categoryName() {
    $categoryTable = Engine_Api::_()->getDbtable('categories', 'sitepageevent');
    return $categoryTable->select()
                    ->from($categoryTable, 'title')
                    ->where('category_id = ?', $this->category_id)
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
  }

  /**
   * Get how many member is attending the event
   *
   * @return count attending the event
   * */
  public function getAttendingCount() {

    return $this->membership()->getMemberCount(true, Array('rsvp' => 2));
  }

  /**
   * Get how many member may be attending the event
   *
   * @return count may be attending the event
   * */
  public function getMaybeCount() {

    return $this->membership()->getMemberCount(true, Array('rsvp' => 1));
  }

  /**
   * Get how many member may not be attending the event
   *
   * @return count may not be attending the event
   * */
  public function getNotAttendingCount() {

    return $this->membership()->getMemberCount(true, Array('rsvp' => 0));
  }

  /**
   * Get how many member awaiting the event
   *
   * @return count member awaiting the event
   * */
  public function getAwaitingReplyCount() {

    return $this->membership()->getMemberCount(true, Array('rsvp' => 3));
  }

  // Interfaces
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

}

?>