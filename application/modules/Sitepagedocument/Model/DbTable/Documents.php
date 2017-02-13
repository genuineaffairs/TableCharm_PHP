<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Documents.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagedocument_Model_DbTable_Documents extends Engine_Db_Table {

  protected $_name = 'sitepagedocument_documents';
  protected $_rowClass = 'Sitepagedocument_Model_Document';

  /**
   * Get sitepagedocument detail
   * @param array $params : contain desirable sitepagedocument info
   * @return  object of sitepagedocument
   */
  public function getSitepagedocumentsPaginator($params = array(), $customParams = null) {
    $paginator = Zend_Paginator::factory($this->getSitepagedocumentsSelect($params, $customParams));
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }

  /**
   * Get sitepagedocuments 
   * @param array $params : contain desirable sitepagedocument info
   * @return  array of sitepagedocuments
   */
  public function getSitepagedocumentsSelect($params = array(), $customParams = null) {


    //GET DOCUMENT TABLE NAME
    $tableDocumentName = $this->info('name');

		$select = $this->select();

    if (isset($params['show_count']) && $params['show_count'] == 1) {

      $searchTable = Engine_Api::_()->fields()->getTable('sitepagedocument_document', 'search')->info('name');
      $select = $select
                      ->setIntegrityCheck(false)
                      ->from($tableDocumentName, array(
                          'COUNT(*) AS show_count'));
      //->group("$tableDocumentName.document_id");
    } else {

				if(!empty($params['orderby'])) {
					$select = $select->order($params['orderby'].' DESC');
				}
        else {
          $select = $select->order('highlighted DESC')
													 ->order('document_id DESC');
        }
				if(!empty($params['orderby']) && $params['orderby'] != 'document_id') {
					$select = $select->order('document_id DESC');
				}

				$searchTable = Engine_Api::_()->fields()->getTable('sitepagedocument_document', 'search')->info('name');
				$select = $select
												->setIntegrityCheck(false)
												->from($tableDocumentName, array('document_id', 'owner_id', 'page_id', 'sitepagedocument_title', 'sitepagedocument_description', 'doc_id', 'thumbnail', 'creation_date', 'rating', 'comment_count', 'like_count', 'views', 'search', 'draft', 'featured','highlighted', 'approved', 'status'))
												->group("$tableDocumentName.document_id");
    }

    if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
      $select->where($tableDocumentName . '.owner_id = ?', $params['user_id']);
    }

    if (!empty($params['user']) && $params['user'] instanceof User_Model_User) {
      $select->where($tableDocumentName . '.owner_id = ?', $params['user_id']->getIdentity());
    }

    if (!empty($params['users'])) {
      $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
      $select->where($tableDocumentName . '.owner_id in (?)', new Zend_Db_Expr($str));
    }

    if (isset($params['owner_id'])) {
      $select->where($tableDocumentName . '.owner_id = ?', $params['owner_id']);
    }

    if (isset($params['draft'])) {
      $select->where($tableDocumentName . '.draft = ?', $params['draft']);
    }

    if (isset($params['approved'])) {
      $select->where($tableDocumentName . '.approved = ?', $params['approved']);
    }

    if (isset($params['page_id'])) {
      $select->where($tableDocumentName . '.page_id = ?', $params['page_id']);
    }

    if (isset($params['featured'])) {
      $select->where($tableDocumentName . '.featured = ?', $params['featured']);
    }

     if (isset($params['highlighted'])) {
      $select->where($tableDocumentName . '.highlighted = ?', $params['highlighted']);
    }

    if (isset($params['status'])) {
      $select->where($tableDocumentName . '.status = ?', $params['status']);
    }

    if (!empty($params['search'])) {
      $select->where($tableDocumentName . ".fulltext LIKE ? OR " . $tableDocumentName . ".sitepagedocument_title LIKE ? OR " . $tableDocumentName . ".sitepagedocument_description LIKE ?", '%' . $params['search'] . '%');
    }

    if (!empty($params['show_document']) && empty($params['search']) && empty($params['featured'])  && empty($params['highlighted'])) {
      $select->where($tableDocumentName . ".draft = ?", 0)
              ->where($tableDocumentName . ".approved = ?", 1)
              ->where($tableDocumentName . ".status = ?", 1)
              ->where($tableDocumentName . ".search = ?", 1)
              ->orwhere($tableDocumentName . ".owner_id = ?", $params['document_owner_id']);
    }

    if (!empty($params['show_document']) && (!empty($params['search']) || !empty($params['featured']) || !empty($params['highlighted']))) {
      $select->where("($tableDocumentName.draft = 0 AND $tableDocumentName.approved = 1 AND $tableDocumentName.status = 1 AND $tableDocumentName.search = 1)  OR ($tableDocumentName.owner_id = " . $params['document_owner_id'] . ")");
    }

    if (isset($params['page_id'])) {
      $select->where($tableDocumentName . '.page_id = ?', $params['page_id']);
    }

    return $select;
  }

	/**
   * Return document data
   *
   * @param array params
   * @return Zend_Db_Table_Select
   */
	public function widgetDocumentsData($params = array(),$widgetType = null) {

		if(isset($params['profile_page_widget']) && !empty($params['profile_page_widget'])) {
			$select = $this->select()
								->from($this->info('name'), array('document_id', 'owner_id', 'doc_id', 'fulltext', 'thumbnail', 'status', 'activity_feed'))
								->where('draft = ?', 0)
								->where('approved = ?', 1)
								->where('status = ?', 0)
								->where('search = ?', 1)
								->where('page_id = ?', $params['page_id']);
      if(isset($params['highlighted']) && !empty($params['highlighted'])) {
        $select->where('highlighted  = ?', 1);
      }
			$select->order('document_id ASC')
							->limit(5);
		}
		elseif(isset($params['view_action']) && !empty($params['view_action'])) {
			$select = $this->select()
								->from($this->info('name'), array('sitepagedocument_title', 'rating', 'owner_id', 'thumbnail',
										'views', 'creation_date', 'modified_date', 'comment_count', 'document_id'))
								->where('page_id = ?', $params['page_id'])
								->where('document_id != ?', $params['document_id'])
								->where('approved = ?', 1)
								->where('draft = ?', 0)
								->where('status = ?', 1)
								->where('search = ?', 1)
								->order('document_id DESC')
								->limit($params['limit']);
		}
		else {
      $tablePage = Engine_Api::_()->getDbtable('pages', 'sitepage');
      $pageTableName = $tablePage->info('name');
      $tableDocumentName = $this->info('name');
      $pagePackagesTable = Engine_Api::_()->getDbtable('packages', 'sitepage');
      $pagePackageTableName = $pagePackagesTable->info('name');
			$select = $this->select()
								->setIntegrityCheck(false)
								->from($this->info('name'), array('document_id', 'page_id', 'owner_id', 'sitepagedocument_title', 'creation_date', 'thumbnail', 'rating', 'comment_count', 'like_count', 'views', 'featured','sitepagedocument_description'))
								->joinLeft($pageTableName, "$pageTableName.page_id = $tableDocumentName.page_id", array('page_id', 'title AS page_title', 'closed', 'approved', 'declined', 'draft', 'expiration_date', 'owner_id', 'photo_id as page_photo_id'))
								->joinLeft($pagePackageTableName, "$pagePackageTableName.package_id = $pageTableName.package_id",array('package_id', 'price'))
								->where($tableDocumentName .'.draft = ?', 0)
								->where($tableDocumentName .'.approved = ?', 1)
								->where($tableDocumentName .'.status = ?', 1)
								->where($tableDocumentName .'.search = ?', 1);
			if(isset($params['zero_count']) && !empty($params['zero_count'])) {
				$select = $select->where($tableDocumentName . '.' . $params['zero_count'].'!= ?', 0);
			}

			if(isset($params['featured']) && !empty($params['featured'])) {
				$select = $select->where($tableDocumentName .'.featured = ?', 1);
			}

      if(isset($params['highlighted']) && !empty($params['highlighted'])) {
				$select = $select->where($tableDocumentName .'.highlighted = ?', 1);
			}
	
			if(isset($params['orderby']) && !empty($params['orderby'])) {
				$select = $select->order($tableDocumentName . '.' . $params['orderby']);
			}
	
			if(isset($params['page_id']) && !empty($params['page_id'])) {
				$select = $select->where($tableDocumentName .'.page_id = ?', $params['page_id']);
			}

      if (!empty($params['title'])) {
				$select->where($pageTableName . ".title LIKE ? ", '%' . $params['title'] . '%');
      }
    
		$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
		if(isset($params['show']) && $params['show'] == 'my_document') {
			$select->where($tableDocumentName . '.owner_id = ?', $viewer_id);
		}
		elseif ((isset($params['show']) && $params['show'] == 'sponsored document') || !empty($params['sponsoreddocument']) || !empty($params['sponsored'])) {
				
				$select->where($pagePackageTableName . '.price != ?', '0.00');
				$select->order($pagePackageTableName . '.price' . ' DESC');
		}
		elseif (isset($params['show']) && $params['show'] == 'Networks') {
				$select = $pageTable->getNetworkBaseSql($select, array('browse_network' => 1));

		}
		elseif((isset($params['show']) && $params['show'] == 'featured') || !empty($params['featureddocument'])) {
			$select = $select
											->where($tableDocumentName . '.featured = ?', 1)
											->order($tableDocumentName .'.creation_date DESC');
		}
    elseif((isset($params['show']) && $params['show'] == 'highlighted') || !empty($params['highlighteddocument'])) {
			$select = $select
											->where($tableDocumentName . '.highlighted = ?', 1)
											->order($tableDocumentName .'.creation_date DESC');
		}
    elseif (isset($params['show']) && $params['show'] == 'my_like') {
			$likeTableName = Engine_Api::_()->getDbtable('likes', 'core')->info('name');
			$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $select
              ->join($likeTableName, "$likeTableName.resource_id = $pageTableName.page_id")
							->where($likeTableName . '.poster_type = ?', 'user')
							->where($likeTableName . '.poster_id = ?', $viewer_id)
              ->where($likeTableName . '.resource_type = ?', 'sitepage_page');
    }
   
		if ((isset($params['orderby_browse']) && $params['orderby_browse'] == 'view_count') || !empty($params['vieweddocument'])) {
			$select = $select
											->order($tableDocumentName .'.views DESC')
											->order($tableDocumentName .'.creation_date DESC');
		} elseif ((isset($params['orderby_browse']) && $params['orderby_browse'] == 'comment_count') || !empty($params['commenteddocument'])) {
			$select = $select
											->order($tableDocumentName .'.comment_count DESC')
											->order($tableDocumentName .'.creation_date DESC');
		} elseif ((isset($params['orderby_browse']) && $params['orderby_browse'] == 'rating') || !empty($params['rateddocument'])) {
			$select = $select
											->order($tableDocumentName .'.rating DESC')
											->order($tableDocumentName .'.creation_date DESC');
			} elseif ((isset($params['orderby_browse']) && $params['orderby_browse'] == 'like_count') || !empty($params['likeddocument'])) {
				$select = $select
												->order($tableDocumentName .'.like_count DESC')
												->order($tableDocumentName .'.creation_date DESC');
		}

    if (!empty($params['search_document'])) {
        $select->where($tableDocumentName . ".sitepagedocument_title LIKE ? OR " . $tableDocumentName . ".sitepagedocument_description LIKE ?", '%' . $params['search_document'] . '%');
				
    }

    if (!empty($params['document_category_id'])) {
      $select->where($tableDocumentName . '.category_id = ?', $params['document_category_id']);
    }

    if (!empty($params['category_id'])) {
      $select->where($pageTableName . '.category_id = ?', $params['category_id']);
    }

		if (!empty($params['subcategory'])) {
      $select->where($pageTableName . '.subcategory_id = ?', $params['subcategory']);
    }

    if (!empty($params['subcategory_id'])) {
      $select->where($pageTableName . '.subcategory_id = ?', $params['subcategory_id']);
    }

    if (!empty($params['subsubcategory'])) {
      $select->where($pageTableName . '.subsubcategory_id = ?', $params['subsubcategory']);
    }

    if (!empty($params['subsubcategory_id'])) {
      $select->where($pageTableName . '.subsubcategory_id = ?', $params['subsubcategory_id']);
    }

    if( empty($params['orderby_browse'])) {
			$order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.order', 1);
			switch ($order) {
				case "1":
					$select->order($tableDocumentName . '.creation_date DESC');
					break;
				case "2":
					$select->order($tableDocumentName . '.sitepagedocument_title');
					break;
				case "3":
					$select->order($tableDocumentName . '.featured' . ' DESC');
					break;
				case "4":
					$select->order($pagePackageTableName . '.price' . ' DESC');
					break;
				case "5":
					$select->order($tableDocumentName . '.featured' . ' DESC');
					$select->order($pagePackageTableName . '.price' . ' DESC');
					break;
				case "6":
					$select->order($pagePackageTableName . '.price' . ' DESC');
					$select->order($tableDocumentName . '.featured' . ' DESC');
					break;
			}
    }

      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
				$select->where($pageTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
      }  
			$select = $select->order($tableDocumentName .'.document_id DESC');

			if(isset($params['limit']) && !empty($params['limit'])) {
        if (!isset($params['start_index']))
        $params['start_index'] = 0;
				$select->limit($params['limit'], $params['start_index']);
				//$select = $select->limit($params['limit']);
			}
		}
    if($widgetType == 'browsedocument') {
      return Zend_Paginator::factory($select);
    }
    else {
			return $this->fetchAll($select);
    }
	}

  /**
   * Return document of the day
   *
   * @return Zend_Db_Table_Select
   */
  public function documentOfDay() {

    //CURRENT DATE TIME
    $date = date('Y-m-d');

    //GET ITEM OF THE DAY TABLE NAME
    $documentOfTheDayTableName = Engine_Api::_()->getDbtable('itemofthedays', 'sitepage')->info('name');

		//GET PAGE TABLE NAME
		$pageTableName = Engine_Api::_()->getDbtable('pages', 'sitepage')->info('name');

    //GET DOCUMENT TABLE NAME
    $documentTableName = $this->info('name');

    //MAKE QUERY
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($documentTableName)
                    ->join($documentOfTheDayTableName, $documentTableName . '.document_id = ' . $documentOfTheDayTableName . '.resource_id')
										->join($pageTableName, $documentTableName . '.page_id = ' . $pageTableName . '.page_id', array(''))
										->where($pageTableName.'.approved = ?', '1')
										->where($pageTableName.'.declined = ?', '0')
										->where($pageTableName.'.draft = ?', '1')
                    ->where('resource_type = ?', 'sitepagedocument_document')
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

    //DOCUMENT TABLE NAME
    $documentTableName = $this->info('name');

    //PAGE TABLE
    $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $pageTableName = $pageTable->info('name');

    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($pageTableName, array('photo_id', 'title as sitepage_title','page_id'))
                    ->join($documentTableName, "$pageTableName.page_id = $documentTableName.page_id", array('COUNT(engine4_sitepage_pages.page_id) AS item_count'))
                    ->where($pageTableName.'.approved = ?', '1')
										->where($pageTableName.'.declined = ?', '0')
										->where($pageTableName.'.draft = ?', '1')
                    ->group($documentTableName . ".page_id")
                    ->order('item_count DESC')
                    ->limit($limit);
    if (!empty($category_id)) {
      $select->where($pageTableName . '.category_id = ?', $category_id);
    }
    return $select->query()->fetchAll();
  }

}

?>