<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Documents.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Document_Model_DbTable_Documents extends Engine_Db_Table
{
	protected $_name = 'documents';
  protected $_rowClass = 'Document_Model_Document';
  protected $_serializedColumns = array('networks_privacy');

  /**
   * Get document detail
   * @param array $params : contain desirable document info
   * @return  object of document
   */
  public function getDocumentsPaginator($params = array(), $customParams = null) {

		//GET PAGINATOR
    $paginator = Zend_Paginator::factory($this->getDocumentsSelect($params, $customParams));

    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }

    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }

    return $paginator;
  }

  /**
   * Get documents 
   * @param array $params : contain desirable document info
   * @return  array of documents
   */
  public function getDocumentsSelect($params = array(), $customParams = null) {

		//GET DOCUMENT TABLE INFO
    $tableDocument = Engine_Api::_()->getDbtable('documents', 'document');
    $tableDocumentName = $tableDocument->info('name');

    //GET TAG MAPS TABLE NAME
    $tableTagmapsName = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');

		if(!empty($params['orderby']) && $params['orderby'] == 'document_title') {
      $select = $tableDocument->select()
                      ->order($tableDocumentName.'.'.$params['orderby'].' ASC')
											->order($tableDocumentName.'.document_id DESC');
    }
		elseif(!empty($params['orderby']) && $params['orderby'] == 'spfesp') {
      $select = $tableDocument->select()
                      ->order($tableDocumentName.'.'.'sponsored'.' DESC')
											->order($tableDocumentName.'.'.'featured'.' DESC')
											->order($tableDocumentName.'.document_id DESC');
    }
		elseif(!empty($params['orderby']) && $params['orderby'] == 'fespfe') {
      $select = $tableDocument->select()
											->order($tableDocumentName.'.'.'featured'.' DESC')
                      ->order($tableDocumentName.'.'.'sponsored'.' DESC')
											->order($tableDocumentName.'.document_id DESC');
    }
		elseif(!empty($params['orderby']) && $params['orderby'] != 'document_id') {
      $select = $tableDocument->select()
                      ->order($tableDocumentName.'.'.$params['orderby'].' DESC')
											->order($tableDocumentName.'.document_id DESC');
    }
		else {
			$select = $tableDocument->select()
											->order($tableDocumentName.'.document_id DESC');
		}

		$searchTable = Engine_Api::_()->fields()->getTable('document', 'search')->info('name');
    $select = $select
                      ->setIntegrityCheck(false)
                      ->from($tableDocumentName, array('document_id', 'owner_id', 'document_title', 'document_description', 'doc_id', 'thumbnail', 'creation_date', 'rating', 'comment_count', 'like_count', 'views', 'featured', 'sponsored', 'search', 'draft', 'featured', 'approved', 'status', 'category_id', 'profile_doc', 'photo_id'))
                      ->joinLeft($searchTable, "$searchTable.item_id = $tableDocumentName.document_id")
                      ->group("$tableDocumentName.document_id");

    if (isset($customParams)) {

			$coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
			$coreversion = $coremodule->version;
			if ($coreversion > '4.1.7') {

				//PROCESS OPTIONS
				$tmp = array();
				foreach ($customParams as $k => $v) {
					if (null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0)) {
						continue;
					} else if (false !== strpos($k, '_field_')) {
						list($null, $field) = explode('_field_', $k);
						$tmp['field_' . $field] = $v;
					} else if (false !== strpos($k, '_alias_')) {
						list($null, $alias) = explode('_alias_', $k);
						$tmp[$alias] = $v;
					} else {
						$tmp[$k] = $v;
					}
				}
				$customParams = $tmp;
			}

			$searchParts = Engine_Api::_()->fields()->getSearchQuery('document', $customParams);
      foreach( $searchParts as $k => $v ) {
        $select->where("`{$searchTable}`.{$k}", $v);
      }     
    }
	
    if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
      $select->where($tableDocumentName.'.owner_id = ?', $params['user_id']);
    }

    if (!empty($params['user']) && $params['user'] instanceof User_Model_User) {
      $select->where($tableDocumentName.'.owner_id = ?', $params['user_id']->getIdentity());
    }

    if (!empty($params['users'])) {
      $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
      $select->where($tableDocumentName.'.owner_id in (?)', new Zend_Db_Expr($str));
    }

    if (!empty($params['tag'])) {
      $select
              ->setIntegrityCheck(false)	
              ->joinLeft($tableTagmapsName, "$tableTagmapsName.resource_id = $tableDocumentName.document_id")
              ->where($tableTagmapsName.'.resource_type = ?', 'document')
              ->where($tableTagmapsName.'.tag_id = ?', $params['tag']);
    }

    if (isset($params['owner_id'])) {
      $select->where($tableDocumentName.'.owner_id = ?', $params['owner_id']);
    }
		
    if ((isset($params['category']) && !empty($params['category']))) {
      $select->where($tableDocumentName.'.category_id = ?', $params['category']);
    }

    if ((isset($params['subcategory']) && !empty($params['subcategory']))) {
      $select->where($tableDocumentName.'.subcategory_id = ?', $params['subcategory']);
    }

    if ((isset($params['subsubcategory']) && !empty($params['subsubcategory']))) {
      $select->where($tableDocumentName.'.subsubcategory_id = ?', $params['subsubcategory']);
    }

		if((isset($params['category_id']) && !empty($params['category_id']))) {
			$select->where($tableDocumentName.'.category_id = ?', $params['category_id']);
		}

    if ((isset($params['subcategory_id']) && !empty($params['subcategory_id']))) {
      $select->where($tableDocumentName.'.subcategory_id = ?', $params['subcategory_id']);
    }

    if ((isset($params['subsubcategory_id']) && !empty($params['subsubcategory_id']))) {
      $select->where($tableDocumentName.'.subsubcategory_id = ?', $params['subsubcategory_id']);
    }

    if (isset($params['draft'])) {
      $select->where($tableDocumentName.'.draft = ?', $params['draft']);
    }

    if (isset($params['approved'])) {
      $select->where($tableDocumentName.'.approved = ?', $params['approved']);
    }

    if (isset($params['status'])) {
      $select->where($tableDocumentName.'.status = ?', $params['status']);
    }

    if (isset($params['searchable'])) {
      $select->where($tableDocumentName.'.search = ?', $params['searchable']);
    }

    if (isset($params['featured'])) {
      $select->where($tableDocumentName.'.featured = ?', $params['featured']);
    }

    if (!empty($params['search'])) {
      $select->where($tableDocumentName.".fulltext LIKE ? OR " . $tableDocumentName.".document_title LIKE ? OR " . $tableDocumentName.".document_description LIKE ?", '%' . $params['search'] . '%');
    }

    if (!empty($params['start_date'])) {
      $select->where($tableDocumentName.".creation_date > ?", date('Y-m-d', $params['start_date']));
    }

    if (!empty($params['end_date'])) {
      $select->where($tableDocumentName.".creation_date < ?", date('Y-m-d', $params['end_date']));
    }
    
    if(isset($params['network_based_content']) && !empty($params['network_based_content'])) {
      $select = $this->getNetworkBaseSql($select, array('browse_network' => (isset($params['show']) && $params['show'] == "3")));    
    }

    return $select;
  }

  /**
   * Documents for scribd conversion
   * @param int $owner_id : owner id
   * @return documents of status zero
   */
  public function updateDocs($owner_id = 0) {
		
		//MAKE QUERY
    $select = $this->select()
                    ->from($this->info('name'), array('document_id', 'document_title', 'owner_id', 'doc_id', 'fulltext', 'thumbnail', 'status', 'activity_feed', 'photo_id'))
										->where('status = ?', 0);


		if(!empty($owner_id)) {
			$select = $select->where('owner_id = ?', $owner_id);
		}

    $select->order('document_id DESC')->limit(5);

		//RETURN RESULTS
    return $this->fetchAll($select);
  }

  /**
   * Documents for Thumbnails import
   * @param int $owner_id : owner id
   * @return documents of status zero
   */
  public function updateThumbs() {
		
		//MAKE QUERY
    $select = $this->select()
									->from($this->info('name'), array('document_id'))
									->where('status = ?', 1)
									->where('photo_id = ?', 0)
									->order('document_id DESC')
									->limit(5);

		//FETCH DOCUMENTS
    $thumbDocs = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
		foreach($thumbDocs as $document_id) {
			$photo_id = Engine_Api::_()->getItem('document', $document_id)->setPhoto();
			$this->update(array('photo_id' => $photo_id), array('document_id = ?' => $document_id));
		}
  }

 /**
   * Return same user documents data
   *
   * @param int owner_id
   * @param int document_id
   * @return Zend_Db_Table_Select
   */
	public function sameUserDocuments($owner_id, $document_id) {

		//MAKE QUERY
    $selectUserDocs = $this->select()
                    ->from($this->info('name'), array('document_title', 'rating', 'owner_id', 'thumbnail',
                        'views', 'creation_date', 'modified_date', 'comment_count', 'like_count', 'document_id', 'photo_id'))
                    ->where('owner_id = ?', $owner_id)
                    ->where('document_id != ?', $document_id)
                    ->where('approved = ?', 1)
                    ->where('draft = ?', 0)
                    ->where('status = ?', 1)
										->where('search = ?', 1)
                    ->order('document_id DESC')
                    ->limit(4);

		//RETURN RESULTS
    return $this->fetchAll($selectUserDocs);
	}

	/**
   * Give number of documents
   * @param int $user_id : user id
   * @return number of doucments corresponding to that owner
   */
  public function totalUserDocuments($owner_id) {

		//FETCH DATA
		$document_total = 0;
    $document_total = $this->select()
                    ->from($this->info('name'), array('COUNT(*) AS count'))
                    ->where('owner_id = ?', $owner_id)
										->query()
                    ->fetchColumn();
    
		//RETURN DATA
    return $document_total;
  }

 /**
   * Return document data
   *
   * @param array params
   * @return Zend_Db_Table_Select
   */
  public function widgetDocumentsData($params = array()) {

    $select = $this->select();

		if(isset($params['featured_slideshow']) && !empty($params['featured_slideshow'])) {
			$select = $select->from($this->info('name'), array('document_id', 'owner_id', 'document_title', 'document_description', 'creation_date', 'thumbnail', 'rating', 'comment_count', 'like_count', 'views', 'featured', 'category_id', 'photo_id'));
		}
		else {
			$select = $select->from($this->info('name'), array('document_id', 'owner_id', 'document_title', 'creation_date', 'thumbnail', 'rating', 'comment_count', 'like_count', 'views', 'featured', 'sponsored', 'category_id', 'photo_id'));
		}

		//SELECT ONLY AUTHENTICATE DOCUMENTS
		$select = $select->where('draft = ?', 0)
                    ->where('approved = ?', 1)
                    ->where('status = ?', 1)
                    ->where('search = ?', 1);

    if (isset($params['zero_count']) && !empty($params['zero_count'])) {
      $select = $select->where($params['zero_count'] . '!= ?', 0);
    }

    if (isset($params['owner_id']) && !empty($params['owner_id'])) {
      $select = $select->where('owner_id = ?', $params['owner_id']);
    }

    if (isset($params['document_id']) && !empty($params['document_id'])) {
      $select = $select->where('document_id != ?', $params['document_id']);
    }

    if (isset($params['featured']) && !empty($params['featured'])) {
      $select = $select->where('featured = ?', 1);
    }

    if (isset($params['sponsored']) && !empty($params['sponsored'])) {
      $select = $select->where('sponsored = ?', '1');
    }

		if (isset($params['category_id']) && !empty($params['category_id'])) {
			$select = $select->where('category_id = ?', $params['category_id']);
		}

    if (isset($params['orderby']) && !empty($params['orderby'])) {
      $select = $select->order($params['orderby']);
    }

    $select = $select->order('document_id DESC');

    if (isset($params['limit']) && !empty($params['limit'])) {
      $select = $select->limit($params['limit']);
    }

		if (isset($params['totalDocuments'])) {
			$limit = (int) $params['totalDocuments'] * 2;
		}
    
    $select = $this->getNetworkBaseSql($select);    

    return $this->fetchAll($select);
  }

  /**
   * Return total number of documents
   *
   * @return number of documents
   */
  public function onStatisticsData()
  {
    //MAKE QUERY
		$total_rows = 0;
    $total_rows = $this->select()
                    ->from($this->info('name'), array('COUNT(*) AS count'))
										->where('draft = ?', 0)
										->where('status = ?', 1)
										->where('approved = ?', 1)
										->query()
									  ->fetchColumn();

		//RETURN RESULTS
		return $total_rows;
  }

  /**
   * Return documents of user
   *
   * @param int $owner_id
   * @return Zend_Db_Table_Select
   */
	public function getOwnerDocuments($owner_id) {
		
		//MAKE QUERY
		$select = $this->select()
									 ->from($this->info('name'), 'document_id')
									 ->where('owner_id = ?', $owner_id);

		//RETURN RESULTS
		return $this->fetchAll($select);
	}

  /**
   * Handle archive list
   * @param array $results : document owner archive list array
   * @return documents list with detail.
   */
	public function getArchiveList($spec)
  {
    if( !($spec instanceof User_Model_User) ) {
      return null;
    }
    
    $localeObject = Zend_Registry::get('Locale');
    if( !$localeObject ) {
      $localeObject = new Zend_Locale();
    }
    
    $select = $this->select()
        ->from($this->info('name'), 'creation_date')
        ->where('owner_type = ?', 'user')
        ->where('owner_id = ?', $spec->getIdentity())
        ->where('draft = ?', 0)
				->where('status = ?', 1)
				->where('approved = ?', 1)
        ->order('document_id DESC');
    
    $select = $this->getNetworkBaseSql($select);
    
    $dates = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);    

    $time = time();
    
    $archive_list = array();
    foreach( $dates as $date ) {
      
      $date = strtotime($date);
      $ltime = localtime($date, true);
      $ltime["tm_mon"] = $ltime["tm_mon"] + 1;
      $ltime["tm_year"] = $ltime["tm_year"] + 1900;

      // LESS THAN A YEAR AGO - MONTHS
      if( $date + 31536000 > $time ) {
        $date_start = mktime(0, 0, 0, $ltime["tm_mon"], 1, $ltime["tm_year"]);
        $date_end = mktime(0, 0, 0, $ltime["tm_mon"] + 1, 1, $ltime["tm_year"]);
        $type = 'month';
        
        $dateObject = new Zend_Date($date);
        $format = $localeObject->getTranslation('yMMMM', 'dateitem', $localeObject);
        $label = $dateObject->toString($format, $localeObject);
      }
      // MORE THAN A YEAR AGO - YEARS
      else {
        $date_start = mktime(0, 0, 0, 1, 1, $ltime["tm_year"]);
        $date_end = mktime(0, 0, 0, 1, 1, $ltime["tm_year"] + 1);
        $type = 'year';
        
        $dateObject = new Zend_Date($date);
        $format = $localeObject->getTranslation('yyyy', 'dateitem', $localeObject);
        if( !$format ) {
          $format = $localeObject->getTranslation('y', 'dateitem', $localeObject);
        }
        $label = $dateObject->toString($format, $localeObject);
      }

      if( !isset($archive_list[$date_start]) ) {
        $archive_list[$date_start] = array(
          'type' => $type,
          'label' => $label,
          'date' => $date,
          'date_start' => $date_start,
          'date_end' => $date_end,
          'count' => 1
        );
      } else {
        $archive_list[$date_start]['count']++;
      }
    }
    
    return $archive_list;
  }

  /**
   * Get documents based on category
   * @param string $title : search text
   * @param int $category_id : category id
   * @param char $popularity : result sorting based on views, likes, comments
   * @param char $interval : time interval
   * @param string $sqlTimeStr : Time durating string for where clause 
   */
  public function documentsByCategory($category_id, $popularity, $interval, $sqlTimeStr, $totalDocuments = 5) {

    $documentTableName = $this->info('name');

    if ($interval == 'overall' || $popularity == 'views') {

      $select = $this->select()
              ->from($documentTableName, array('document_id', 'document_title', 'owner_id', 'thumbnail', "$popularity AS populirityCount", 'photo_id'))
              ->where($documentTableName . '.category_id = ?', $category_id)
              ->where($documentTableName . '.approved = ?', 1)
              ->where($documentTableName . '.status = ?', 1)
              ->where($documentTableName . '.draft = ?', 0)
							->where($documentTableName . '.search = ?', 1)
              ->order("$popularity DESC")
              ->order("creation_date DESC")
              ->limit($totalDocuments);
    } elseif ($popularity != 'views' && $interval != 'overall') {

      if ($popularity == 'like_count') {
        $popularityType = 'like';
      } else {
        $popularityType = 'comment';
      }

      $id = $popularityType . "_id";

      $popularityTable = Engine_Api::_()->getDbtable("$popularityType" . "s", 'core');
      $popularityTableName = $popularityTable->info('name');

      $select = $this->select()
              ->setIntegrityCheck(false)
              ->from($documentTableName, array('document_id', 'document_title', 'owner_id', 'thumbnail', "$popularity AS populirityCount", 'photo_id'))
              ->joinLeft($popularityTableName, $popularityTableName . '.resource_id = ' . $documentTableName . '.document_id', array("COUNT($id) as total_count"))
              ->where($documentTableName . '.category_id = ?', $category_id)
              ->where($documentTableName . '.approved = ?', 1)
              ->where($documentTableName . '.status = ?', 1)
              ->where($documentTableName . '.draft = ?', 0)
							->where($documentTableName . '.search = ?', 1)
              ->where($popularityTableName . "$sqlTimeStr  or " . $popularityTableName . '.creation_date is null')
              ->group($documentTableName . '.document_id')
              ->order("total_count DESC")
              ->order($documentTableName . ".creation_date DESC")
              ->limit($totalDocuments);
    }
    
    $select = $this->getNetworkBaseSql($select);

    return $this->fetchAll($select);
  }


  /**
   * Get documents to add as item of the day
   * @param string $title : search text
   * @param int $limit : result limit
   */
  public function getDayItems($title, $limit=10) {

    //MAKE QUERY
    $select = $this->select()
            ->from($this->info('name'), array('document_id', 'owner_id', 'document_title', 'thumbnail', 'photo_id'))
            ->where('document_title  LIKE ? ', '%' . $title . '%')
						->where('draft = ?', 0)
						->where('status = ?', 1)
						->where('approved = ?', 1)
            ->order('document_title ASC')
            ->limit($limit);

    //FETCH RESULTS
    return $this->fetchAll($select);
  }

  /**
   * Get document of the day
   */
  public function getItemOfDay() {

		//GET DOCUMENT TABLE NAME
		$documentTableName = $this->info('name');

		//GET DOCUMENT OF THE DAY TABLE
    $itemofthedaytable = Engine_Api::_()->getDbtable('itemofthedays', 'document');
    $itemofthedayName = $itemofthedaytable->info('name');

		//MAKE QUERY
    $document_id = $this->select()
								->setIntegrityCheck(false)
								->from($documentTableName, array('document_id'))
								->join($itemofthedayName, $documentTableName . ".document_id = " . $itemofthedayName . '.document_id')
								->where($itemofthedayName . '.start_date <=?', date('Y-m-d'))
								->where($itemofthedayName . '.end_date >=?', date('Y-m-d'))
								->where($documentTableName.'.draft = ?', 0)
								->where($documentTableName.'.status = ?', 1)
								->where($documentTableName.'.approved = ?', 1)
								->order('RAND()')
								->query()
								->fetchColumn();

		//RETURN RESULTS
		return $document_id;
  }

  /**
   * Get list of document of the day items
   * @param array $params : contain ordering info
   * @param int $document_id : item id
   */
  public function getItemOfDayList($params=array(), $document_id) {

		//GET ITEM OF THE DAY TABLE NAME
    $itemTableName = $this->info('name');
	
		//GET DOCUMENT TABLE INFO
    $itemofthedayTable = Engine_Api::_()->getItemTable('document_itemofthedays');
		$itemofthedayName = $itemofthedayTable->info('name');

		//MAKE QUERY
    $select = $this->select()
            ->setIntegrityCheck(false)
						->from($itemTableName, array('document_id', 'owner_id', 'document_title'))
            ->join($itemofthedayName, $itemTableName . ".$document_id = " . $itemofthedayName . '.document_id')
						->order((!empty($params['order']) ? $params['order'] : 'start_date' ) . ' ' . (!empty($params['order_direction']) ? $params['order_direction'] : 'DESC' ));

		//RETURN RESULTS
    return $paginator = Zend_Paginator::factory($select);
  }

  /**
   * REMOVE OTHER DOCUMENT AS A PROFILE DOCUMENT
   *
   * @param Int owner_id
   */
  public function removeProfileDoc($owner_id) {

		//RETURN IF OWNER ID IS EMPTY
    if (empty($owner_id)) {
      return;
    }

		//REMOVE OTHER DOCUMENT AS A PROFILE DOCUMENT
		$this->update(array('profile_doc' => 0,), array('owner_id = ?' => $owner_id));
  }

  /**
   * Return document id which is make as profile document
   *
   * @param Int owner_id
   */
	public function getProfileDocId($owner_id) {

		$document_id = 0;

		//RETURN IF OWNER ID IS EMPTY
    if (empty($owner_id)) {
      return $document_id;
    }

		//FETCH DATA
    $document_id = $this->select()
                    ->from($this->info('name'), 'document_id')
                    ->where('owner_id = ?', $owner_id)
                    ->where('profile_doc = ?', 1)
										->query()
                    ->fetchColumn();
	
		//RETURN DATA
    return $document_id;
	}

  /**
   * Return documents which have this category and this mapping
   *
   * @param int category_id
   * @return Zend_Db_Table_Select
   */
  public function getCategoryDocument($category_id) {

		//RETURN IF CATEGORY ID IS NULL
		if(empty($category_id)) {
			return null;
		}

		//MAKE QUERY
    $select = $this->select()
            ->from($this->info('name'), 'document_id')
            ->where('category_id = ?', $category_id);

		//GET DATA
		$categoryData = $this->fetchAll($select);

		if(!empty($categoryData)) {
			return $categoryData->toArray();
		}

		return null;
  }
  
  public function getNetworkBaseSql($select, $params = array()) {

    if (empty($select))
      return;

    //GET DOCUMENT TABLE NAME
    $documentTableName = $this->info('name');

    //START NETWORK WORK
    $enableNetwork = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.network', 0);
    if (!empty($enableNetwork) || (isset($params['browse_network']) && !empty($params['browse_network']))) {
      $viewer = Engine_Api::_()->user()->getViewer();
      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
      if (!Zend_Registry::isRegistered('viewerNetworksIdsSR')) {
        $viewerNetworkIds = $networkMembershipTable->getMembershipsOfIds($viewer);
        Zend_Registry::set('viewerNetworksIdsSR', $viewerNetworkIds);
      } else {
        $viewerNetworkIds = Zend_Registry::get('viewerNetworksIdsSR');
      }

      if (!Engine_Api::_()->document()->documentBaseNetworkEnable()) {
        if (!empty($viewerNetworkIds)) {
          if (isset($params['setIntegrity']) && !empty($params['setIntegrity'])) {
            $select->setIntegrityCheck(false)
                    ->from($documentTableName);
          }
          $networkMembershipName = $networkMembershipTable->info('name');
          $select
                  ->join($networkMembershipName, "`{$documentTableName}`.owner_id = `{$networkMembershipName}`.user_id  ", null)
                  ->where("`{$networkMembershipName}`.`resource_id`  IN (?) ", (array) $viewerNetworkIds);
          if (!isset($params['not_groupBy']) || empty($params['not_groupBy'])) {
            $select->group($documentTableName . ".document_id");
          }
        }
      } else {
        
        $str = array();
        $columnName = "`{$documentTableName}`.networks_privacy";
        foreach ($viewerNetworkIds as $networkId) {
          $str[] = '\'%"' . $networkId . '"%\'';
        }
        if (!empty($str)) {
          $likeNetworkVale = (string) ( join(" or $columnName  LIKE ", $str) );
          $select->where($columnName . ' LIKE ' . $likeNetworkVale . ' or ' . $columnName . " IS NULL");
        } else {
          $select->where($columnName . " IS NULL");
        }
      }
    }
    //END NETWORK WORK
    
    //RETURN QUERY
    return $select;
  }  
  
}