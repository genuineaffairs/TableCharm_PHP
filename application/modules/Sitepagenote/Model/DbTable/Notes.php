<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Notes.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_Model_DbTable_Notes extends Engine_Db_Table {

  protected $_rowClass = "Sitepagenote_Model_Note";

  public function getNoteType() {

    global $sitepagenote_noteType;
    return $sitepagenote_noteType;
  }

  /**
   * Gets total notes
   *
   * @param int $page_id
   * @return count
   */
  public function getTotalNote($page_id) {
    $count = 0;
    $count = $this
            ->select()
            ->from($this->info('name'), array('count(*) as count'))
            ->where("page_id = ?", $page_id)
            ->query()
            ->fetchColumn();

    return $count;
  }

  /**
   * Gets notes data
   *
   * @param array $params
   * @return Zend_Db_Table_Select
   */
  public function widgetNotesData($params = array(),$noteType = null) {

    $tableNoteName = $this->info('name');  
    $tablePage = Engine_Api::_()->getDbtable('pages', 'sitepage');
		$tablePageName = $tablePage->info('name');

    if(isset($params['profile_page_widget']) && !empty($params['profile_page_widget'])) {

      $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($tableNoteName, array('note_id', 'page_id', 'owner_id', 'title', 'view_count', 'comment_count', 'total_photos', 'like_count', 'creation_date', 'modified_date', 'photo_id','body','featured'))
											->join($tablePageName, "$tablePageName.page_id = $tableNoteName.page_id", array('title AS page_title', 'photo_id as page_photo_id'))
                ->where($tableNoteName . '.search = ?', '1')
								->where($tableNoteName . '.draft = ?', '0')
                ->where($tableNoteName .'.page_id = ?', $params['page_id'])
								->limit($params['limit']);
      if (isset($params['zero_count']) && !empty($params['zero_count'])) {
				$select = $select->where($tableNoteName . '.' . $params['zero_count'] . '!= ?', 0);
			}

			if (isset($params['orderby']) && !empty($params['orderby'])) {
				$select = $select->order($tableNoteName . '.' . $params['orderby']);
			}
      $select = $select->order('note_id DESC');
    }
    elseif(isset($params['view_action']) && !empty($params['view_action'])) {
    
			// Get tags for this note
			$tagMapsTable = Engine_Api::_()->getDbtable('tagMaps', 'core');
			$tagsTable = Engine_Api::_()->getDbtable('tags', 'core');
			
			// Get tags
			$tags = $tagMapsTable->select()
				->from($tagMapsTable, 'tag_id')
				->where('resource_type = ?', $params['resource_type'])
				->where('resource_id = ?', $params['resource_id'])
				->query()
				->fetchAll(Zend_Db::FETCH_COLUMN);

			// No tags
			if( !empty($tags) ) {
			// Get other with same tags
			$select = $this->select()
								->setIntegrityCheck(false)
								->joinLeft($tagMapsTable->info('name'), 'resource_id=note_id', null)
								->where('resource_type = ?', $params['resource_type'])
								->where('resource_id != ?', $params['resource_id'])
								->where('tag_id IN(?)', $tags)
								->limit($params['limit'])
								->order($this->info('name') . '.creation_date DESC')
								->group($this->info('name') . '.note_id');
			}
      else {
          return;
        }
    }
    else {
			$tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
			$tmName = $tmTable->info('name'); 
			$pagePackagesTable = Engine_Api::_()->getDbtable('packages', 'sitepage');
			$pagePackageTableName = $pagePackagesTable->info('name');
			$select = $this->select()
											->setIntegrityCheck(false)
											->from($tableNoteName, array('note_id', 'page_id', 'owner_id', 'title', 'view_count', 'comment_count', 'total_photos', 'like_count', 'creation_date', 'modified_date', 'photo_id','body','featured'))
											->joinLeft($tablePageName, "$tablePageName.page_id = $tableNoteName.page_id", array('title AS page_title', 'photo_id as page_photo_id'))
											->join($pagePackageTableName, "$pagePackageTableName.package_id = $tablePageName.package_id",array('package_id', 'price'))
											->where($tableNoteName . '.search = ?', '1')
											->where($tableNoteName . '.draft = ?', '0');

			if (isset($params['zero_count']) && !empty($params['zero_count'])) {
				$select = $select->where($tableNoteName . '.' . $params['zero_count'] . '!= ?', 0);
			}     
      
      if (isset($params['note_category_id']) && !empty($params['note_category_id'])) {
				$select = $select->where($tableNoteName . '.	category_id =?', $params['note_category_id']);
			}       

      if (isset($params['category_id']) && !empty($params['category_id'])) {
				$select = $select->where($tablePageName . '.	category_id =?', $params['category_id']);
			}

			if (isset($params['orderby']) && !empty($params['orderby'])) {
				$select = $select->order($tableNoteName . '.' . $params['orderby']);
			}

			if ($noteType == 'sponsored') {
					$select->where($pagePackageTableName . '.price != ?', '0.00');
					$select->order($pagePackageTableName . '.price' . ' DESC');
					$select ->limit($params['limit']);
			}

			if (!empty($params['title'])) {
				$select->where($tablePageName . ".title LIKE ? ", '%' . $params['title'] . '%');
			}

			if (!empty($params['search_note'])) {
				$noteTableName = $this->info('name');
				$tagName = Engine_Api::_()->getDbtable('Tags', 'core')->info('name');
				$select
								->setIntegrityCheck(false)
								->joinLeft($tmName, "$tmName.resource_id = $noteTableName.note_id and " . $tmName . ".resource_type = 'sitepagenote_note'", null)
								->joinLeft($tagName, "$tagName.tag_id = $tmName.tag_id", array($tagName . ".text"));
				$select->where($this->info('name') . ".title LIKE ? OR " . $this->info('name') . ".body LIKE ? OR " . $tagName . ".text LIKE ? ", '%' . $params['search_note'] . '%');
			}
	
				$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
				if(isset($params['show']) && $params['show'] == 'my_note') {
					$select->where($tableNoteName . '.owner_id = ?', $viewer_id);
				}
				elseif ((isset($params['show']) && $params['show'] == 'sponsored note') || !empty($params['sponsorednote'])) {
					$select->where($pagePackageTableName . '.price != ?', '0.00');
					$select->order($pagePackageTableName . '.price' . ' DESC');
				}
				elseif((isset($params['show']) && $params['show'] == 'featured') || !empty($params['featurednote'])) {
				$select = $select
												->where($tableNoteName . '.featured = ?', 1)
												->order($tableNoteName .'.creation_date DESC');
				}
				elseif (isset($params['show']) && $params['show'] == 'Networks') {
						$select = $tablePage->getNetworkBaseSql($select, array('browse_network' => 1));

				}
				elseif (isset($params['show']) && $params['show'] == 'my_like') {
					$likeTableName = Engine_Api::_()->getDbtable('likes', 'core')->info('name');
					$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
					$select
								->join($likeTableName, "$likeTableName.resource_id = $tablePageName.page_id")
								->where($likeTableName . '.poster_type = ?', 'user')
								->where($likeTableName . '.poster_id = ?', $viewer_id)
								->where($likeTableName . '.resource_type = ?', 'sitepage_page');
				}

				if (isset($params['orderby_browse']) && $params['orderby_browse'] == 'view_count') {
					$select = $select
													->order($this->info('name') .'.view_count DESC')
													->order($this->info('name') .'.creation_date DESC');
				} elseif ((isset($params['orderby_browse']) && $params['orderby_browse'] == 'comment_count') || !empty($params['commentednote'])) {
					$select = $select
													->order($this->info('name') .'.comment_count DESC')
													->order($this->info('name') .'.creation_date DESC');
				} elseif ((isset($params['orderby_browse']) && $params['orderby_browse'] == 'like_count') || !empty($params['likednote'])) {
					$select = $select
													->order($this->info('name') .'.like_count DESC')
													->order($this->info('name') .'.creation_date DESC');
				} 
        
      
      if (isset($params['note_category_id']) && !empty($params['note_category_id'])) {
				$select = $select->where($tableNoteName . '.	category_id =?', $params['note_category_id']);
			}         
		
			if (!empty($params['category'])) {
				$select->where($tablePageName . '.category_id = ?', $params['category']);
			}

			if (!empty($params['category_id'])) {
				$select->where($tablePageName . '.category_id = ?', $params['category_id']);
			}

			if (!empty($params['subcategory'])) {
				$select->where($tablePageName . '.subcategory_id = ?', $params['subcategory']);
			}

			if (!empty($params['subcategory_id'])) {
				$select->where($tablePageName . '.subcategory_id = ?', $params['subcategory_id']);
			}

			if (!empty($params['subsubcategory'])) {
				$select->where($tablePageName . '.subsubcategory_id = ?', $params['subsubcategory']);
			}

			if (!empty($params['subsubcategory_id'])) {
				$select->where($tablePageName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
			}

			if(empty($params['orderby_browse'])) {
				$order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagenote.order', 1);
				switch ($order) {
					case "1":
						$select->order($tableNoteName . '.creation_date DESC');
						break;
					case "2":
						$select->order($tableNoteName . '.title');
						break;
					case "3":
						$select->order($tableNoteName . '.featured' . ' DESC');
						break;
					case "4":
						$select->order($pagePackageTableName . '.price' . ' DESC');
						break;
					case "5":
						$select->order($tableNoteName . '.featured' . ' DESC');
						$select->order($pagePackageTableName . '.price' . ' DESC');
						break;
					case "6":
						$select->order($pagePackageTableName . '.price' . ' DESC');
						$select->order($tableNoteName . '.featured' . ' DESC');
						break;
				}
			}
			
			if (!empty($params['tag']) || Zend_Controller_Front::getInstance()->getRequest()->getParam('tag', null)) {
				if( !empty($params['tag']) ) {
					$tag = $params['tag'];
				} elseif(Zend_Controller_Front::getInstance()->getRequest()->getParam('tag', null)) {
					$tag = Zend_Controller_Front::getInstance()->getRequest()->getParam('tag', null); 
				}
				
				$tmTable = Engine_Api::_()->getDbtable('tagMaps', 'core');
				$tmName = $tmTable->info('name');
				//Get tags
				$tags = $tmTable->select()
					->from($tmName, 'tag_id')
					->where('resource_type = ?', 'sitepagenote_note')
					->query()
					->fetchAll(Zend_Db::FETCH_COLUMN);
				if(!empty($tags)) {
					$join = "$tmName.resource_id= $tableNoteName.note_id";
					$select = $select
								->joinLeft($tmName, $join, null)
								->where($tmName . '.resource_type = ?', 'sitepagenote_note')
								->where($tmName . '.tag_id = ?', $tag);
				}
			}    
			
			$select = $select->order('note_id DESC');

			if (isset($params['limit']) && !empty($params['limit'])) {
				if (!isset($params['start_index']))
					$params['start_index'] = 0;
				$select->limit($params['limit'], $params['start_index']);
			}

			$select = $select
								->where($tablePageName . '.closed = ?', '0')
								->where($tablePageName . '.approved = ?', '1')
								->where($tablePageName . '.declined = ?', '0')
                ->where($tablePageName . '.search = ?', '1')
								->where($tablePageName . '.draft = ?', '1');
			if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
				$select->where($tablePageName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
			}      
    }
//    //Start Network work
//    if (!isset($params['page_id']) || empty($params['page_id'])) {
//      $select = $tablePage->getNetworkBaseSql($select, array('not_groupBy' => 1, 'extension_group' => $tableNoteName . ".note_id"));
//    }
//    //End Network work

    //$select->group($tableNoteName . ".note_id");

    if(isset($params['note_content']) && !empty($params['note_content'])) {
      return Zend_Paginator::factory($select);
    }
    else {
			return $this->fetchAll($select);
    }
  }

  /**
   * Gets similar notes
   *
   * @param int note
   * @param int limit
   * @return similar notes
   */
  public function getTagNotes($note, $limit) {

    $sitepageTags = $note->tags()->getTagMaps();
    $tagString = '';
    foreach ($sitepageTags as $value) {
      $tagString .= "'" . $value->tag_id . "',";
    }
    $tagString = trim($tagString, ",");
    $tagMapsTableName = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');
    $tagName = Engine_Api::_()->getDbtable('Tags', 'core')->info('name');
    $tableNoteName = $this->info('name');
    $paginators = array();
    if (!empty($tagString)) {
      $select = $this->select()
              ->order($tableNoteName . '.creation_date DESC');
      $select->from($tableNoteName)
              ->setIntegrityCheck(false)
              ->joinInner($tagMapsTableName, "$tagMapsTableName.resource_id = $tableNoteName.note_id")
              ->joinInner($tagName, "$tagName.tag_id = $tagMapsTableName.tag_id", null)
              ->where($tagMapsTableName . '.resource_type = ?', 'sitepagenote_note');

      $select->where($tagMapsTableName . '.tag_id IN(' . $tagString . ')')
              ->where($tableNoteName . '.note_id != ?', $note->note_id)
              ->where($tableNoteName . '.page_id = ?', $note->page_id)
              ->where($tableNoteName . '.draft = ?', 0)
              ->where($tableNoteName . '.search = ?', 1)
              ->limit($limit)
              ->group($tableNoteName . '.note_id');
      $paginators = $this->fetchAll($select);
    }
    return $paginators;
  }

  /**
   * Get sitepagenotes
   *
   * @param array $params : contain desirable sitepagenotes info
   * @return  array of sitepagenotes
   */
  public function getSitepagenotesSelect($params = array()) {

    $tableNoteName = $this->info('name');
    $tagMapsName = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');

    if (isset($params['show_count']) && $params['show_count'] == 1) {
      $select = $this->select();
      $select = $select
              ->setIntegrityCheck(false)
              ->from($tableNoteName, array(
          'COUNT(*) AS show_count'));
    } else {
      $select = $this->select();
      if (!empty($params['orderby'])) {
        if ($params['orderby'] == 'featured') {
        $select
								->where($tableNoteName . '.featured = ?', 1)
								->order('creation_date DESC');
				}
        else {
					$select->order($params['orderby'] . ' DESC')
					->order('creation_date DESC');
        }
      }

      $select = $select
              ->setIntegrityCheck(false)
              ->from($tableNoteName, array('note_id', 'page_id', 'owner_id', 'title', 'creation_date', 'view_count', 'comment_count', 'like_count', 'creation_date', 'body', 'modified_date', 'photo_id', 'search', 'draft', 'total_photos','featured'))
              ->group("$tableNoteName.note_id");
    }

    if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
      $select->where($tableNoteName . '.owner_id = ?', $params['user_id']);
    }

    if (!empty($params['user']) && $params['user'] instanceof User_Model_User) {
      $select->where($tableNoteName . '.owner_id = ?', $params['user_id']->getIdentity());
    }

    if (!empty($params['users'])) {
      $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
      $select->where($tableNoteName . '.owner_id in (?)', new Zend_Db_Expr($str));
    }

    if (isset($params['owner_id'])) {
      $select->where($tableNoteName . '.owner_id = ?', $params['owner_id']);
    }

    if (isset($params['draft'])) {
      $select->where($tableNoteName . '.draft = ?', $params['draft']);
    }
    if (isset($params['page_id'])) {
      $select->where($tableNoteName . '.page_id = ?', $params['page_id']);
    }

    if (!empty($params['search'])) {
      $select->where($tableNoteName . ".title LIKE ? OR " . $tableNoteName . ".body LIKE ?", '%' . $params['search'] . '%');
    }

    if (!empty($params['text'])) {
      $tagName = Engine_Api::_()->getDbtable('Tags', 'core')->info('name');
      $select
              ->setIntegrityCheck(false)
              ->joinLeft($tagMapsName, "$tagMapsName.resource_id = $tableNoteName.note_id and " . $tagMapsName . ".resource_type = 'sitepagenote_note'", null)
              ->joinLeft($tagName, "$tagName.tag_id = $tagMapsName.tag_id", null)
              ->where($tableNoteName . ".title LIKE ? OR " . $tableNoteName . ".body LIKE ? OR " . $tagName . ".text LIKE ? ", '%' . $params['text'] . '%');
    }

    if (!empty($params['show_pagenotes']) && empty($params['search'])) {
      $select->where($tableNoteName . ".draft = ?", 0)
              ->where($tableNoteName . ".search = ?", 1)
              ->orwhere($tableNoteName . ".owner_id = ?", $params['note_owner_id']);
    }

    if (!empty($params['show_pagenotes']) && (!empty($params['search']))) {
      $select->where("($tableNoteName.draft = 0)  AND ($tableNoteName.search = 1) OR ($tableNoteName.owner_id = " . $params['note_owner_id'] . ")");
    }

    if (isset($params['page_id'])) {
      $select->where($tableNoteName . '.page_id = ?', $params['page_id']);
    }

    if (!empty($params['tag'])) {
      $select
              ->joinLeft($tagMapsName, "$tagMapsName.resource_id = $tableNoteName.note_id", NULL)
              ->where($tagMapsName . '.resource_type = ?', 'sitepagenote_note')
              ->where($tagMapsName . '.tag_id = ?', $params['tag']);
    }
    
    return $select;
  }

  /**
   * Get sitepagenote detail
   *
   * @param array $params : contain desirable sitepagenote info
   * @return  object of sitepagenote
   */
  public function getSitepagenotesPaginator($params = array()) {

    $paginator = Zend_Paginator::factory($this->getSitepagenotesSelect($params));
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }
  
  public function getTagCloud($limit=100, $count_only = 0) {

    $tableTagmaps = 'engine4_core_tagmaps';
    $tableTags = 'engine4_core_tags';

    $tableSitepagenotes = $this->info('name');
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($tableSitepagenotes, 'title')
            ->joinInner($tableTagmaps, "$tableSitepagenotes.note_id = $tableTagmaps.resource_id", array('COUNT(engine4_core_tagmaps.resource_id) AS Frequency'))
            ->joinInner($tableTags, "$tableTags.tag_id = $tableTagmaps.tag_id", array('text', 'tag_id'))
            ->where($tableSitepagenotes . '.draft = ?', "0");

    $select->where($tableSitepagenotes . ".search = ?", 1);
    $select = $select            
            ->where($tableTagmaps . '.resource_type = ?', 'sitepagenote_note')
            ->group("$tableTags.text")
            ->order("Frequency DESC");

		if(!empty($count_only)) {
			$total_results = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
			return Count($total_results);
		}

		$select = $select->limit($limit);

    return $select->query()->fetchAll();
  }

  /**
   * Return note of the day
   *
   * @return Zend_Db_Table_Select
   */
  public function noteOfDay() {

    //CURRENT DATE TIME
    $date = date('Y-m-d');

    //GET ITEM OF THE DAY TABLE NAME
    $noteOfTheDayTableName = Engine_Api::_()->getDbtable('itemofthedays', 'sitepage')->info('name');

		//GET PAGE TABLE NAME
		$pageTableName = Engine_Api::_()->getDbtable('pages', 'sitepage')->info('name');

    //GET NOTE TABLE NAME
    $noteTableName = $this->info('name');

    //MAKE QUERY
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($noteTableName, array('note_id', 'title', 'page_id', 'owner_id', 'body','photo_id'))
                    ->join($noteOfTheDayTableName, $noteTableName . '.note_id = ' . $noteOfTheDayTableName . '.resource_id')
										->join($pageTableName, $noteTableName . '.page_id = ' . $pageTableName . '.page_id', array(''))
										->where($pageTableName.'.approved = ?', '1')
										->where($pageTableName.'.declined = ?', '0')
										->where($pageTableName.'.draft = ?', '1')
                    ->where('resource_type = ?', 'sitepagenote_note')
                    ->where('start_date <= ?', $date)
                    ->where('end_date >= ?', $date)
                    ->order('Rand()');

		//PAGE SHOULD BE AUTHORIZED
    if (Engine_Api::_()->sitepage()->hasPackageEnable())
      $select->where($pageTableName.'.expiration_date  > ?', date("Y-m-d H:i:s"));

		//PAGE SHOULD BE AUTHORIZED
    $stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.status.show', 1);
    if ($stusShow == 0) {
      $select->where($pageTableName.'.closed = ?', '0');
    }

    //RETURN RESULTS
    return $this->fetchRow($select);
  }

  public function topcreatorData($limit = null,$category_id) {

    //NOTE TABLE NAME
    $noteTableName = $this->info('name');

    //PAGE TABLE
    $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $pageTableName = $pageTable->info('name');

    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($pageTableName, array('photo_id', 'title as sitepage_title','page_id'))
                    ->join($noteTableName, "$pageTableName.page_id = $noteTableName.page_id", array('COUNT(engine4_sitepage_pages.page_id) AS item_count'))
                    ->where($pageTableName.'.approved = ?', '1')
										->where($pageTableName.'.declined = ?', '0')
										->where($pageTableName.'.draft = ?', '1')
                    ->group($noteTableName . ".page_id")
                    ->order('item_count DESC')
                    ->limit($limit);
    if (!empty($category_id)) {
      $select->where($pageTableName . '.category_id = ?', $category_id);
    }
    return $select->query()->fetchAll();
  }

}

?>