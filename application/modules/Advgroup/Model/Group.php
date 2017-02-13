<?php
class Advgroup_Model_Group extends Core_Model_Item_Abstract
{
  protected $_parent_type = 'user';

  protected $_owner_type = 'user';

  protected $_type = 'group';

  public function getSlug($str = null)
  {
    $str = $this->getTitle();
    if( strlen($str) > 32 ) {
      $str = Engine_String::substr($str, 0, 32) . '...';
    }
    $str = preg_replace('/([a-z])([A-Z])/', '$1 $2', $str);
    $str = strtolower($str);
    $str = preg_replace('/[^a-z0-9-]+/i', '-', $str);
    $str = preg_replace('/-+/', '-', $str);
    $str = trim($str, '-');
    if( !$str ) {
      $str = '-';
    }
    return $str;
  }
  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */


  public function getHref($params = array())
  {
    $slug = $this->getSlug();
    $params = array_merge(array(
      'route' => 'group_profile',
      'reset' => true,
      'id' => $this->getIdentity(),
      'slug' => $slug,
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function getDescription()
  {
    // @todo decide how we want to handle multibyte string functions
    if( isset($this->description) )
    {
     $tmpBody = strip_tags($this->description);
     return Engine_Api::_()->advgroup()->subPhrase($tmpBody,350);
    }
    return '';
  }


  public function isParentGroupOwner(Core_Model_Item_Abstract $owner){
    if(!$owner->getIdentity()) {
      return false;
    }

    $parent_group = $this->getParentGroup();

    if($parent_group && $parent_group->isOwner($owner)) {
      return true;
    }
    else {
      return false;
    }
  }

   public function getParent($recurseType = null)
  {
    return $this->getOwner('user');
  }

  public function countSubGroups(){
  	$table = Engine_Api::_()->getItemtable('group');
	$select = $table->select()->where('parent_id = ?',$this->group_id);
	return count($table->fetchAll($select));
  }

  public function getParentGroup()
  {
   return  Engine_Api::_()->getItem('group', $this->parent_id);
  }

  public function getAllSubGroups(){
    $table = Engine_Api::_()->getItemTable('group');
    $select = $table->select()->where('parent_id = ?',$this->group_id);
    return $sub_groups = $table->fetchAll($select);
  }

  public function getAllGroupsAssoc(){
    $result = array($this->group_id);
    $table = Engine_Api::_()->getItemTable('group');
    $select = $table->select()->where('parent_id = ?',$this->group_id);
    $sub_groups = $table->fetchAll($select);
    foreach($sub_groups as $sub_group){
      $result[] = $sub_group -> group_id;
    }
    return $result;
  }

  public function getSingletonAlbum()
  {
    $table = Engine_Api::_()->getItemTable('advgroup_album');
    $select = $table->select()
      ->where('group_id = ?', $this->getIdentity())
      ->order('album_id ASC')
      ->limit(1);

    $album = $table->fetchRow($select);

    if( null === $album )
   {
      $album = $table->createRow();
      $album->setFromArray(array(
        'group_id' => $this->getIdentity(),
        'title' => 'Group Profile',
        'user_id' => $this->getOwner()->getIdentity(),
      ));
      $album->save();
    }

    return $album;
  }

  public function getOfficerList()
  {
    $table = Engine_Api::_()->getItemTable('advgroup_list');
    $select = $table->select()
      ->where('owner_id = ?', $this->getIdentity())
      ->where('title = ?', 'GROUP_OFFICERS')
      ->limit(1);

    $list = $table->fetchRow($select);

    if( null === $list ) {
      $list = $table->createRow();
      $list->setFromArray(array(
        'owner_id' => $this->getIdentity(),
        'title' => 'GROUP_OFFICERS',
      ));
      $list->save();
    }

    return $list;
  }

  public function getCategory()
  {
    return Engine_Api::_()->getDbtable('categories', 'advgroup')
        ->find($this->category_id)->current();
  }

  public function setPhoto($photo)
  {
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
    } else if( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
    } else {
      throw new Group_Model_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => 'group',
      'parent_id' => $this->getIdentity()
    );

    // Save
    $storage = Engine_Api::_()->storage();

    // Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($path.'/m_'.$name)
      ->destroy();

    // Resize image (profile)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(200, 400)
      ->write($path.'/p_'.$name)
      ->destroy();

    // Resize image (feature)
    $image = new Advgroup_Api_Image();
    @$image->open($file)
      ->resize(350, 200)
      ->write($path.'/fe_'.$name)
      ->destroy();

    // Resize image (normal)
    $image = new Advgroup_Api_Image();
    @$image->open($file)
      ->resize(140, 105)
      ->write($path.'/in_'.$name)
      ->destroy();

    // Resize image (icon)
//    $image = Engine_Image::factory();
//    $image->open($file);
//
//    $size = min($image->height, $image->width);
//    $x = ($image->width - $size) / 2;
//    $y = ($image->height - $size) / 2;
//
//    $image->resample($x, $y, $size, $size, 48, 48)
//      ->write($path.'/is_'.$name)
//      ->destroy();

    // Store
    $iMain = $storage->create($path.'/m_'.$name, $params);
    $iProfile = $storage->create($path.'/p_'.$name, $params);
    $iIconNormal = $storage->create($path.'/in_'.$name, $params);
    $iFeature = $storage->create($path.'/fe_'.$name, $params);
//    $iSquare = $storage->create($path.'/is_'.$name, $params);

    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
//    $iMain->bridge($iSquare, 'thumb.icon');
    $iMain->bridge($iFeature,'thumb.feature');
    // Remove temp files
    @unlink($path.'/p_'.$name);
    @unlink($path.'/m_'.$name);
    @unlink($path.'/in_'.$name);
    @unlink($path.'/fe_'.$name);

    // Update row
    $this->modified_date = date('Y-m-d H:i:s');
    $this->photo_id = $iMain->file_id;
    $this->save();

    // Add to album
    $viewer = Engine_Api::_()->user()->getViewer();
    $photoTable = Engine_Api::_()->getItemTable('advgroup_photo');
    $groupAlbum = $this->getSingletonAlbum();
    $photoItem = $photoTable->createRow();
    $photoItem->setFromArray(array(
      'group_id' => $this->getIdentity(),
      'album_id' => $groupAlbum->getIdentity(),
      'user_id' => $viewer->getIdentity(),
      'file_id' => $iMain->getIdentity(),
      'collection_id' => $groupAlbum->getIdentity(),
    ));
    $photoItem->save();

    return $this;
  }

  public function getEventsPaginator()
  {
    if (Engine_Api::_()->hasModuleBootstrap('event')) {
	    $table = Engine_Api::_()->getDbtable('events', 'event');
    }
    else {
        $table = Engine_Api::_()->getDbtable('events', 'ynevent');
    }
    $select = $table->select()->where('parent_type = ?', 'group');
    $select->where('parent_id = ?', $this->getIdentity())->order('creation_date DESC');
    return  Zend_Paginator::factory($select);
  }

  public function membership()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('membership', 'advgroup'));
  }

  public function getAlbumCount($user_id){
    $table = Engine_Api::_()->getItemTable('advgroup_album');
    $name = $table->info('name');
    $select = $table->select()
                    ->from($name, 'COUNT(*) AS count')
                    ->where("group_id = $this->group_id")
                    ->where("user_id = $user_id");
    return $select->query()->fetchColumn(0);
  }

    public function getFeatured()
  {
       $group = Engine_Api::_()->getItem('group',$this->group_id);
       if(count($group) <= 0)
            return false;
       else
       {
           if($group->featured == '1')
                return true;
           else
                return false;
       }
       return false;
  }

   /**
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   **/
   public function tags()
   {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
   }

   /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   **/
  public function reports()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('reports', 'core'));
  }

  // Internal hooks
  protected function _postInsert()
  {
    if( $this->_disableHooks ) return;

    parent::_postInsert();

    // Create auth stuff
    $context = Engine_Api::_()->authorization()->context;
    foreach( array('everyone', 'registered', 'member') as $role )
    {
      $context->setAllowed($this, $role, 'view', true);
    }
  }

  protected function _delete()
  {
    if( $this->_disableHooks ) return;

    // Delete all memberships
    $this->membership()->removeAllMembers();

    // Delete officer list
    $this->getOfficerList()->delete();

    // Delete all albums
    $albumTable = Engine_Api::_()->getItemTable('advgroup_album');
    $albumSelect = $albumTable->select()->where('group_id = ?', $this->getIdentity());
    foreach( $albumTable->fetchAll($albumSelect) as $groupAlbum ) {
      $groupAlbum->delete();
    }

    // Delete all topics
    $topicTable = Engine_Api::_()->getItemTable('advgroup_topic');
    $topicSelect = $topicTable->select()->where('group_id = ?', $this->getIdentity());
    foreach( $topicTable->fetchAll($topicSelect) as $groupTopic ) {
      $groupTopic->delete();
    }

    //Delete all links
    $linkTable = Engine_Api::_()->getItemTable('advgroup_link');
    $linkSelect = $linkTable->select()->where('group_id = ?', $this->getIdentity());
    foreach( $linkTable->fetchAll($linkSelect) as $groupLink ) {
      $groupLink->delete();
    }

    //Delete all announcment
    $announcementTable = Engine_Api::_()->getItemTable('advgroup_announcement');
    $announcementSelect = $announcementTable->select()-> where('group_id = ?', $this->getIdentity());
    foreach( $announcementTable->fetchAll($announcementSelect) as $groupAnnouncement){
      $groupAnnouncement->delete();
    }

	//Delete invites
	$inviteTable = Engine_Api::_()->getDbTable('invites','advgroup');
    $inviteSelect = $inviteTable->select()-> where('group_id = ?', $this->getIdentity());
    foreach( $inviteTable->fetchAll($inviteSelect) as $groupInvite){
      $groupInvite->delete();
    }

	//Delete polls
	$pollTable = Engine_Api::_()->getDbTable('polls','advgroup');
    $pollSelect = $pollTable->select()-> where('group_id = ?', $this->getIdentity());
    foreach( $pollTable->fetchAll($pollSelect) as $groupPoll){
      $groupPoll->delete();
    }

	//Delete reports
	$reportTable = Engine_Api::_()->getDbTable('reports','advgroup');
    $reportSelect = $reportTable->select()-> where('group_id = ?', $this->getIdentity());
    foreach( $reportTable->fetchAll($reportSelect) as $groupReport){
      $groupReport->delete();
    }

    //Delete all events
    if (Engine_Api::_()->hasItemType('event'))
    {
      $eventTable = Engine_Api::_()->getItemTable('event');
      $eventSelect = $eventTable->select()->where("parent_type = 'group' and parent_id = ?", $this->getIdentity());
      foreach ($eventTable->fetchAll($eventSelect) as $groupEvent)
      {
        $groupEvent->delete();
      }
    }
    if(Engine_Api::_()->hasItemType('video')){
      $videoTable = Engine_Api::_()->getItemTable('video');
      $videoSelect = $videoTable->select()->where("parent_type = 'group' and parent_id = ?", $this->getIdentity());
      foreach ($videoTable->fetchAll($videoSelect) as $groupVideo)
      {
        $groupVideo->delete();
      }
    }
    parent::_delete();
  }

  public function getGroupMembers() {
  	$select = $this->membership()->getMembersObjectSelect();
  	$models = new User_Model_DbTable_Users();
  	$members = $models->fetchAll($select);

  	return $members;
  }
}