<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Resume_Api_Core extends Core_Api_Abstract
{
  const IMAGE_WIDTH = 720;
  const IMAGE_HEIGHT = 720;

  const THUMB_WIDTH = 140;
  const THUMB_HEIGHT = 160;
  
  public function checkLicense()
  {
    $license = Engine_Api::_()->getApi('settings', 'core')->getSetting('resume.license');
    return (trim($license) && $license != 'XXXX-XXXX-XXXX-XXXX');
  }  

  public function getCategoryOptions($params = array())
  {
    return Engine_Api::_()->getItemTable('resume_category')->getMultiOptionsAssoc($params);
  }  
  
  public function getCategories($params = array())
  {
    $categories = Engine_Api::_()->getItemTable('resume_category')->getCategories($params);
    return $categories;
  }
  
  public function getCategory($category_id)
  {
    return Engine_Api::_()->getItemTable('resume_category')->getCategory($category_id);
  }
  
  public function getPackageOptions($params = array())
  {
    return $this->convertPackagesToArray($this->getPackages($params));
  }   
  
  public function getPackages($params = array())
  {
    $table = Engine_Api::_()->getDbtable('packages', 'resume');
    $select = $table->select();
    
    if (isset($params['enabled']))
    {
      $select->where("enabled = ?", $params['enabled'] ? 1 : 0);
    }
    
    if (isset($params['exclude_package_id']))
    {
      $select->where("package_id <> ?", $params['exclude_package_id']);  
    }
    
    $order = 'order';
    if (isset($params['order']))
    {
      $order = $params['order'];
    }
    $select->order($order);
    
    $packages = $table->fetchAll($select);
    
    return $packages;
  }
  
  public function getPackage($package_id)
  {
    static $packages = array();
    
    if (!isset($packages[$package_id]))
    {
      $packages[$package_id] = Engine_Api::_()->getDbtable('packages', 'resume')->find($package_id)->current();
    }
    
    return $packages[$package_id];
  }  
  
  public function convertPackagesToArray($packages)
  {
    return $this->convertItemsToArray($packages);
  }  
  
  public function getResume($resume_id)
  {
    static $resumes = array();
    
    if (!isset($resumes[$resume_id]))
    {
      $resumes[$resume_id] = Engine_Api::_()->getDbtable('resumes', 'resume')->find($resume_id)->current();
    }
    
    return $resumes[$resume_id];
  } 
  
  public function convertCategoriesToArray($categories)
  {
    return $this->convertItemsToArray($categories);
  }
  
  public function convertItemsToArray($items)
  {
    $data = array();
    foreach ($items as $item) {
      $data[$item->getIdentity()] = $item->getTitle();
    }
    return $data;
  }
  
  public function countResumes($params = array())
  {
    $paginator = $this->getResumesPaginator($params);
    return $paginator->getTotalItemCount();  
  }
  
  // Select
  /**
   * Gets a paginator for resumes
   *
   * @param Core_Model_Item_Abstract $user The user to get the messages for
   * @return Zend_Paginator
   */
  public function getResumesPaginator($params = array(), $options = null)
  {
    $paginator = Zend_Paginator::factory($this->getResumesSelect($params, $options));
    if( !empty($params['page']) )
    {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }

  /**
   * Gets a select object for the user's resume entries
   *
   * @param Core_Model_Item_Abstract $user The user to get the messages for
   * @return Zend_Db_Table_Select
   */
  public function getResumesSelect($params = array(), $options = null)
  {
    $table = $this->getResumeTable();
    
    $rName = $table->info('name');

    if (empty($params['order'])) {
      $params['order'] = 'recent';
    }    
    
    $select = $table->selectParamBuilder($params);
    
    
    // Process options
    $tmp = array();
    foreach( $params as $k => $v ) {
      if( null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0) ) {
        continue;
      } else if( false !== strpos($k, '_field_') ) {
        list($null, $field) = explode('_field_', $k);
        $tmp['field_' . $field] = $v;
      } else if( false !== strpos($k, '_alias_') ) {
        list($null, $alias) = explode('_alias_', $k);
        $tmp[$alias] = $v;
      } else {
        $tmp[$k] = $v;
      }
    }
    $params = $tmp; 
        
    // Build search part of query
    $searchParts = Engine_Api::_()->fields()->getSearchQuery('resume', $params);
    if (!empty($searchParts))
    {
      $searchTable = Engine_Api::_()->fields()->getTable('resume', 'search')->info('name');
      
      $select = $select
        ->setIntegrityCheck(false)
        ->from($rName)
        ->joinLeft($searchTable, "$searchTable.item_id = $rName.resume_id")
        ->group("$rName.resume_id");     
      foreach( $searchParts as $k => $v ) 
      {
        $select = $select->where("`{$searchTable}`.{$k}", $v);
      }
    }      
    
    if( !empty($params['tag']) )
    {          
      $tagTable = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');
      
      $select = $select
        ->setIntegrityCheck(false)
        ->from($rName)
        ->joinLeft($tagTable, "$tagTable.resource_id = $rName.resume_id")
        ->where($tagTable.'.resource_type = ?', 'resume')
        ->where($tagTable.'.tag_id  IN (?)', $params['tag']);
        if (is_array($params['tag'])) {
          $select->group("$rName.resume_id");
        }
    }
    
    if (isset($params['location']) && strlen($params['location']))
    {
      $location = $params['location'];
      $distance = empty($params['distance']) ? 50 : $params['distance'];
      
      if (empty($params['distance_unit'])) {
        $params['distance_unit'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('resume.distanceunit', Radcodes_Lib_Helper_Unit::UNIT_MILE);
      }
      
      if (isset($params['distance_unit'])) {
        if ($params['distance_unit'] == Radcodes_Lib_Helper_Unit::UNIT_MILE) {
          $distance = Radcodes_Lib_Helper_Unit::mileToKilometer($distance);
        }
      }     
      $select = Engine_Api::_()->getApi('location', 'radcodes')->getSelectProximity('resume', $location, $distance, $select);

    }    
    
   
    //echo $select->__toString();
    //exit;
    return $select;
  }

  
  public function filterEmptyParams($values)
  {
    foreach ($values as $key => $value)
    {
      if (is_array($value))
      {
        foreach ($value as $value_k => $value_v)
        {
          if (!strlen($value_v))
          {
            unset($value[$value_k]);
          }
        }
      }
      
      if (is_array($value) && count($value) == 0)
      {
        unset($values[$key]);
      }
      else if (!is_array($value) && !strlen($value))
      {
        unset($values[$key]);
      }
    }
    
    return $values;
  }
  
  public function getPopularTags($options = array())
  {
    $resource_type = 'resume';
    
    $tag_table = Engine_Api::_()->getDbtable('tags', 'core');
    $tagmap_table = $tag_table->getMapTable();
    
    $tName = $tag_table->info('name');
    $tmName = $tagmap_table->info('name');
    
    if (isset($options['order']))
    {
      $order = $options['order'];
    }
    else
    {
      $order = 'text';
    }
    
    if (isset($options['sort']))
    {
      $sort = $options['sort'];
    }
    else
    {
      $sort = $order == 'total' ? SORT_DESC : SORT_ASC;
    }
    
    $limit = isset($options['limit']) ? $options['limit'] : 50;
    
    $select = $tag_table->select()
        ->setIntegrityCheck(false)
        ->from($tmName, array('total' => "COUNT(*)"))
        ->join($tName, "$tName.tag_id = $tmName.tag_id")
        ->where($tmName.'.resource_type = ?', $resource_type)
        ->where($tmName.'.tag_type = ?', 'core_tag')
        ->group("$tName.tag_id")
        ->order("total desc")
        ->limit("$limit");

    $params = array('live' => true); 
    $resume_table = $this->getResumeTable();
    $rName = $resume_table->info('name');
    
            
    
    $select->setIntegrityCheck(false)
        ->join($rName, "$tmName.resource_id = $rName.resume_id");
    $select = $resume_table->selectParamBuilder($params, $select);    
    //echo $select;
    
    $tags = $tag_table->fetchAll($select);   
    
    $records = array();
    
    $columns = array();
    if (!empty($tags))
    {
      foreach ($tags as $k => $tag)
      {
        $records[$k] = $tag;
        $columns[$k] = $order == 'total' ? $tag->total : $tag->text; 
      }
    }

    $tags = array();
    if (count($columns))
    {
      if ($order == 'text') {
        natcasesort($columns);
      }
      else {
        arsort($columns);
      }

      foreach ($columns as $k => $name)
      {
        $tags[$k] = $records[$k];
      }
    }

    return $tags;
    
    //////////////
    
    $tags = Engine_Api::_()->radcodes()->getPopularTags('resume', $options);
    return $tags;
  }  
  
  public function getRelatedResumes($resume, $params = array())
  {
    // related resumes
    $tag_ids = array();
    foreach ($resume->tags()->getTagMaps() as $tagMap) {
      $tag = $tagMap->getTag();
      if (!empty($tag->text)) {
        $tag_ids[] = $tag->tag_id;
      }
    }
    //print_r($tag_ids);
    
    if (empty($tag_ids)) {
      return null;
    }
    
    $values = array(
      'tag' => $tag_ids,
      'order' => 'random',
      'limit' => 5,
      'exclude_resume_ids' => array($resume->getIdentity())
    );

    $params = array_merge($values, $params);
    
    $paginator = Engine_Api::_()->resume()->getResumesPaginator($params);
    
    if ($paginator->getTotalItemCount() == 0) {
      return null;
    }
    
    return $paginator;
  }  
  
  
  public function getTopSubmitters($params = array())
  {
    $column = 'user_id';
    
    $table = $this->getResumeTable();
    $rName = $table->info('name');
    
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), array(
      'user_id' => $column,
      'total' => new Zend_Db_Expr('COUNT(*)'),
    ));
    $select->group($column);

    $select->order('total desc');
    
    if (isset($params['limit'])) {
      $select->limit($params['limit']);
      unset($params['limit']);
    }
    
    $select = $table->selectParamBuilder($params, $select);
    
    $rows = $select->query()->fetchAll();
    
    $result = array();
    foreach ($rows as $row) {
      $result[$row[$column]] = $row;
    }
    
    return $result;
  }

  
  /***
   * @return Resume_Model_DbTable_Resumes
   */
  public function getResumeTable()
  {
    return Engine_Api::_()->getDbtable('resumes', 'resume');
  }
  
  
  public function getEpaymentMappingResumeStatus($epayment_status)
  {
    $mapper = array(
      Epayment_Model_Epayment::STATUS_COMPLETED => Resume_Model_Resume::STATUS_APPROVED,
      Epayment_Model_Epayment::STATUS_CANCELLED => Resume_Model_Resume::STATUS_CANCELLED,
      Epayment_Model_Epayment::STATUS_REFUNDED => Resume_Model_Resume::STATUS_REFUNDED,
      Epayment_Model_Epayment::STATUS_FAILED => Resume_Model_Resume::STATUS_REJECTED,
      Epayment_Model_Epayment::STATUS_PENDING => Resume_Model_Resume::STATUS_PENDING,
    );
    
    if (array_key_exists($epayment_status, $mapper)) {
      return $mapper[$epayment_status];
    }
    else {
      throw new Resume_Model_Exception("Epayment has invalid status '$epayment_status'");
    }    
  }
  
  
  public function processEpayment($epayment, $options = array())
  {
    $package = $epayment->getPackage();
    $resume = $epayment->getParent();
    
    if (($package instanceof Resume_Model_Package) && $package->getIdentity())
    {

      if (array_key_exists('base_start_expiration_date', $options) && $options['base_start_expiration_date'] != 'skip')
      {
        $mode = $options['base_start_expiration_date'];
        
        // never expire
        if ($package->isForever())
        {
          $start_date = null;
        }
        else 
        {
          if ($mode == 'expiration_date')
          {
            if ($resume->hasExpirationDate())
            {
              $start_date = $resume->expiration_date;
            }
            else
            {
              $start_date = $epayment->creation_date;
            }
          }
          else if ($mode == 'status_date')
          {
            $start_date = $resume->status_date;
          }
          else if ($mode == 'payment_date')
          {
            $start_date = $epayment->creation_date;
          }
          else if ($mode == 'current_date')
          {
            $start_date = date('Y-m-d H:i:s');
          }
          else {
            if ($resume->package_id == $package->package_id)
            {
              $start_date = $resume->expiration_date;
            }
            else 
            {
              $start_date = date('Y-m-d H:i:s');
            }
          }
        }
        //echo $start_date;
        $resume->updateExpirationDate($package, $start_date, 'yyyy-MM-dd HH:mm:ss');
        
      }
      
      $current_resume_status = $resume->status;
      
      $resume_status = $this->getEpaymentMappingResumeStatus($epayment->status);
      $resume->updateStatus($resume_status);      
      
      $resume->package_id = $epayment->package_id;
      
      $resume->featured = $package->featured;
      $resume->sponsored = $package->sponsored;

      $resume->save();
      
      $epayment->updateProcessed(true);
      $epayment->save();

      if ($resume->isApprovedStatus())
      {
        $this->pushNewPostActivity($resume);
      }
      
      if ($current_resume_status != $resume_status)
      {
        $this->pushStatusUpdateNotification($resume);
      }
      
    }
    else {
      throw new Resume_Model_Exception("Payment has invalid resume package #".$epayment->package_id);
    }
  }
  
  
  public function pushStatusUpdateNotification(Resume_Model_Resume $resume)
  {
  	$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
    $notifyApi->addNotification($resume->getOwner(), $resume, $resume, 'resume_status_update', array(
      'status' => $resume->getStatusText()
    ));
  }
  
  public function pushNewPostActivity(Resume_Model_Resume $resume)
  {
    // Add activity only if resume is published
    $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionsByObject($resume);
    if ($resume->published && count($action->toArray())<=0)
    {
      $owner = $resume->getOwner();
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($owner, $resume, 'resume_new');
      if($action!=null){
        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $resume);
      }
      
      // Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach( $actionTable->getActionsByObject($resume) as $action ) {
        $actionTable->resetActivityBindings($action);
      }
    }
  }
  
  
  public function localToServerTime($local_time, $user = null)
  {
    if (!($user instanceof User_Model_User))
    {
      $user = Engine_Api::_()->user()->getViewer();
    }
    
    $oldTz = date_default_timezone_get();
    date_default_timezone_set($user->timezone);
    $end = strtotime($local_time);
    date_default_timezone_set($oldTz);
    $server_time = date('Y-m-d H:i:s', $end);
    
    return $server_time;
  }
  
  public function serverToLocalTime($server_time, $user = null)
  {
    if (!($user instanceof User_Model_User))
    {
      $user = Engine_Api::_()->user()->getViewer();
    }    
    
    $end = strtotime($server_time);
    $oldTz = date_default_timezone_get();
    date_default_timezone_set($user->timezone);
    $local_time = date('Y-m-d H:i:s', $end);
    date_default_timezone_set($oldTz);
    
    return $local_time;
  }
  
  public function getSpecialAlbum(User_Model_User $user, $type = 'resume')
  {
    $table = Engine_Api::_()->getDbtable('albums', 'album');

    $translate = Zend_Registry::get('Zend_Translate');
    $title = $translate->_(ucfirst($type) . ' Photos');
    
    $select = $table->select()
        ->where('owner_type = ?', $user->getType())
        ->where('owner_id = ?', $user->getIdentity())
        ->where('title = ?', $title)
        ->order('album_id ASC')
        ->limit(1);
    
    $album = $table->fetchRow($select);

    // Create wall photos album if it doesn't exist yet
    if( null === $album )
    {
      $album = $table->createRow();
      $album->owner_type = $user->getType();
      $album->owner_id = $user->getIdentity();
      $album->title = $title;
      //$album->type = $type;

      $album->search = 0;

      $album->save();
      
      // Authorizations
      $auth = Engine_Api::_()->authorization()->context;
      $auth->setAllowed($album, 'everyone', 'view',    true);
      $auth->setAllowed($album, 'everyone', 'comment', true);
        
    }

    return $album;
  }
  
  // START VIDEO FUNCTIONS
  public function createResumevideo($params, $file, $values) {

    if ($file instanceof Storage_Model_File) {
      $params['file_id'] = $file->getIdentity();
    } else {
      //CREATE VIDEO ITEM
      $video = Engine_Api::_()->getDbtable('videos', 'resume')->createRow();
      $file_ext = pathinfo($file['name']);
      $file_ext = $file_ext['extension'];
      $video->code = $file_ext;
      $video->save();

      //STORE VIDEO IN TEMPORARY STORAGE OBJECT FOR FFMPEG TO HANDLE
      $storage = Engine_Api::_()->getItemTable('storage_file');
      $storageObject = $storage->createFile($file, array(
                  'parent_id' => $video->getIdentity(),
                  'parent_type' => $video->getType(),
                  'user_id' => $video->owner_id,
              ));

      //REMOVE TEMPORARY FILE
      @unlink($file['tmp_name']);

      $video->file_id = $storageObject->file_id;
      $video->save();

      //ADD TO JOBS
      Engine_Api::_()->getDbtable('jobs', 'core')->addJob('resume_video_encode', array(
          'video_id' => $video->getIdentity(),
      ));
    }
    return $video;
  }
  
  /**
   * Return count
   *
   * @param string $tablename
   * @param string $modulename
   * @param int $resume_id
   * @param int $title_count
   * @return paginator
   */
  public function getTotalCount($resume_id, $modulename, $tablename) {

    $table = Engine_Api::_()->getDbtable($tablename, $modulename);
    $count = $table
            ->select()
            ->from($table->info('name'), array('count(*) as count'))
            ->where("resume_id = ?", $resume_id)
            ->query()
            ->fetchColumn();

    return (int)$count;
  }
  
  public function getVideosPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getVideosSelect($params));
    if( !empty($params['page']) )
    {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }

  public function getVideosSelect($params = array())
  {
    $table = Engine_Api::_()->getDbtable('videos', 'resume');
    $rName = $table->info('name');

    $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
    $tmName = $tmTable->info('name');
    
    $select = $table->select()
      ->from($table->info('name'))
      ->order( !empty($params['orderby']) ? $params['orderby'].' DESC' : "$rName.creation_date DESC" );
    
    if( !empty($params['text']) ) {
      $searchTable = Engine_Api::_()->getDbtable('search', 'core');
      $db = $searchTable->getAdapter();
      $sName = $searchTable->info('name');
      $select
        ->joinRight($sName, $sName . '.id=' . $rName . '.video_id', null)
        ->where($sName . '.type = ?', 'resume_video')
        ->where(new Zend_Db_Expr($db->quoteInto('MATCH(' . $sName . '.`title`, ' . $sName . '.`description`, ' . $sName . '.`keywords`, ' . $sName . '.`hidden`) AGAINST (? IN BOOLEAN MODE)', $params['text'])))
        //->order(new Zend_Db_Expr($db->quoteInto('MATCH(' . $sName . '.`title`, ' . $sName . '.`description`, ' . $sName . '.`keywords`, ' . $sName . '.`hidden`) AGAINST (?) DESC', $params['text'])))
        ;
    }
      
    if( !empty($params['status']) && is_numeric($params['status']) )
    {
      $select->where($rName.'.status = ?', $params['status']);
    }
    if( !empty($params['search']) && is_numeric($params['search']) )
    {
      $select->where($rName.'.search = ?', $params['search']);
    }
    if( !empty($params['user_id']) && is_numeric($params['user_id']) )
    {
      $select->where($rName.'.owner_id = ?', $params['user_id']);
    }

    if( !empty($params['user']) && $params['user'] instanceof User_Model_User )
    {
      $select->where($rName.'.owner_id = ?', $params['user_id']->getIdentity());
    }
    
    if( !empty($params['category']) )
    {
      $select->where($rName.'.category_id = ?', $params['category']);
    }

    if( !empty($params['tag']) )
    {
      $select
        // ->setIntegrityCheck(false)
        // ->from($rName)
        ->joinLeft($tmName, "$tmName.resource_id = $rName.video_id", NULL)
        ->where($tmName.'.resource_type = ?', 'resume_video')
        ->where($tmName.'.tag_id = ?', $params['tag']);
    }
    
    if(!empty($params['resume_id'])) {
      $select->where($rName.'.resume_id = ?', $params['resume_id']);
    }

    return $select;
  }
  
  public function getMessageTabId() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $resumeProfilePageId = $this->_getProfilePageId();

    $messageTabContentId = $db->select()
            ->from('engine4_core_content', 'content_id')
            ->where('`name` = ?', 'resume.profile-messages')
            ->where('`page_id` = ?', $resumeProfilePageId)
            ->limit(1)
            ->query()
            ->fetchColumn()
    ;

    return $messageTabContentId;
  }
  
  public function getDetailTabId() {
    $db = Engine_Db_Table::getDefaultAdapter();

    $resumeProfilePageId = $this->_getProfilePageId();

    $detailTabContentId = $db->select()
            ->from('engine4_core_content', 'content_id')
            ->where('`name` = ?', 'resume.profile-details')
            ->where('`page_id` = ?', $resumeProfilePageId)
            ->limit(1)
            ->query()
            ->fetchColumn()
    ;

    return $detailTabContentId;
  }
  
  public function getPhotoTabId() {
    $db = Engine_Db_Table::getDefaultAdapter();
    
    $resumeProfilePageId = $this->_getProfilePageId();

    $photoTabContentId = $db->select()
            ->from('engine4_core_content', 'content_id')
            ->where('`name` = ?', 'resume.profile-photos')
            ->where('`page_id` = ?', $resumeProfilePageId)
            ->limit(1)
            ->query()
            ->fetchColumn()
    ;

    return $photoTabContentId;
  }
  
  public function getDocumentTabId() {
    $db = Engine_Db_Table::getDefaultAdapter();
    
    $resumeProfilePageId = $this->_getProfilePageId();

    $tabContentId = $db->select()
            ->from('engine4_core_content', 'content_id')
            ->where('`name` = ?', 'folder.profile-folders')
            ->where('`page_id` = ?', $resumeProfilePageId)
            ->limit(1)
            ->query()
            ->fetchColumn()
    ;

    return $tabContentId;
  }
  
  protected function _getProfilePageId() {
    $db = Engine_Db_Table::getDefaultAdapter();
    
    $resumeProfilePageId = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('`name` = ?', 'resume_profile_index')
            ->limit(1)
            ->query()
            ->fetchColumn()
    ;
    
    return $resumeProfilePageId;
  }

}