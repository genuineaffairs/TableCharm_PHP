<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Note.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_Model_Note extends Core_Model_Item_Abstract {

  protected $_parent_type = 'sitepage_page';
  protected $_searchColumns = array('title', 'body');
  //GLOBAL SEARCH
  protected $_parent_is_owner = true;

	public function getMediaType() {
		return 'note';
	}
	
  /**
   * Return page object
   *
   * @return page object
   * */
  public function getParent($recurseType = null) {
    
    if($recurseType == null) $recurseType = 'sitepage_page';
    
    return Engine_Api::_()->getItem($recurseType, $this->page_id);
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
			$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagenote.sitemobile-profile-sitepagenotes', $this->page_id, $layout);
		} else {
			$tab_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepagenote.profile-sitepagenotes', $this->page_id, $layout);
		}

    $params = array_merge(array(
                'route' => 'sitepagenote_detail_view',
                'reset' => true,
                'user_id' => $this->owner_id,
                'note_id' => $this->note_id,
                'slug' => $this->getSlug(),
                'tab' => $tab_id,
                    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
            ->assemble($params, $route, $reset);
  }

  /**
   * Return a truncate ownername
   *
   * @param int ownername 
   * @return truncate ownername
   * */
  public function truncateOwner($owner_name) {

    $tmpBody = strip_tags($owner_name);
    return ( Engine_String::strlen($tmpBody) > 10 ? Engine_String::substr($tmpBody, 0, 10) . '..' : $tmpBody );
  }

  /**
   * Return a truncate description
   *
   * @return truncate description
   * */
  public function getDescription() {

    //@TODO DECIDE HOW WE WANT TO HANDLE MULTIBYTE STRING FUNCTIONS
    $tmpBody = strip_tags($this->body);
    return ( Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody );
  }

  /**
   * Return keywords
   *
   * @param char separator 
   * @return keywords
   * */
  public function getKeywords($separator = ' ') {

    $keywords = array();
    foreach ($this->tags()->getTagMaps() as $tagmap) {
      $tag = $tagmap->getTag();
      $keywords[] = $tag->getTitle();
    }
    if (null === $separator) {
      return $keywords;
    }
    return join($separator, $keywords);
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
      throw new Sitepagenote_Model_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
        'parent_type' => 'sitepagenote_note',
        'parent_id' => $this->getIdentity()
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

    $viewer = Engine_Api::_()->user()->getViewer();
    $photoTable = Engine_Api::_()->getItemTable('sitepagenote_photo');
    $sitepagenoteAlbum = Engine_Api::_()->getDbTable('albums', 'sitepagenote')->getSingletonAlbum($this->getIdentity(), $this->getTitle());
    $photoItem = $photoTable->createRow();
    $photoItem->setFromArray(array(
        'note_id' => $this->getIdentity(),
        'album_id' => $sitepagenoteAlbum->getIdentity(),
        'user_id' => $viewer->getIdentity(),
        'file_id' => $iMain->getIdentity(),
        'collection_id' => $sitepagenoteAlbum->getIdentity(),
    ));
    $photoItem->save();
    $this->modified_date = date('Y-m-d H:i:s');
    $this->photo_id = $photoItem->file_id;
    $this->save();
    return $this;
  }

  /**
   * Return a note title
   *
   * @return title
   * */
  public function getTitle() {

    return $this->title;
  }
  
  public function categoryName() {
    $categoryTable = Engine_Api::_()->getDbtable('categories', 'sitepagenote');
    return $categoryTable->select()
                    ->from($categoryTable, 'title')
                    ->where('category_id = ?', $this->category_id)
                    ->limit(1)
                    ->query()
                    ->fetchColumn();
  }  

  //Interfaces

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

  protected function _delete() {
    
    // Delete create activity feed of note before delete note 
    Engine_Api::_()->getApi('subCore', 'sitepage')->deleteCreateActivityOfExtensionsItem($this, array('sitepagenote_new', 'sitepagenote_admin_new'));
    parent::_delete();
  }

}
?>