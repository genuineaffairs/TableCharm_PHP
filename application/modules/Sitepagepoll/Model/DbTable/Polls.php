<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Polls.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagepoll_Model_DbTable_Polls extends Engine_Db_Table {

  protected $_rowClass = 'Sitepagepoll_Model_Poll';

  /**
   * Return poll data
   *
   * @param array params
   * @param string listtype
   * @return Zend_Db_Table_Select
   */
  public function getPollListing($listtype, $params = array()) {

    $pollTableName = $this->info('name');
    if(isset($params['total_sitepagepolls'])) {
			$total_sitepagepolls = $params['total_sitepagepolls'];
    }
    else {
      $total_sitepagepolls = 0;
    }
    if(isset($params['profile_page_widget']) && !empty($params['profile_page_widget'])) {
      $select = $this->select()
                ->setIntegrityCheck(false)
								->from($pollTableName, array('poll_id', 'owner_id', 'page_id', 'title', 'views', 'comment_count', 'like_count', 'vote_count','description'))
                ->where($pollTableName .'.page_id = ?', $params['page_id'])
                ->limit($params['total_sitepagepolls']);
      if ($listtype == 'Most Recent') {
        $select = $select->order($pollTableName .'.poll_id DESC');
      }
      elseif ($listtype == 'Most Viewed') {
        $select = $select->where($pollTableName .'.views != ?', 0)
													->order($pollTableName .'.views DESC')
													->order($pollTableName .'.poll_id DESC');
      }
      elseif ($listtype == 'Most Voted') {
        $select = $select->where($pollTableName .'.vote_count != ?', 0)
													->order($pollTableName .'.vote_count DESC')
													->order($pollTableName .'.poll_id DESC');
      }
      elseif ($listtype == 'Most Commented') {
        $select = $select->where($pollTableName .'.comment_count != ?', 0)
													->order($pollTableName .'.comment_count DESC')
													->order($pollTableName .'.poll_id DESC');
      }
      elseif ($listtype == 'Most Liked') {
				$select = $this->getlikes($select);
      }
      $select->limit($total_sitepagepolls);
    }  
    else {
			$tablePage = Engine_Api::_()->getDbtable('pages', 'sitepage');
			$tablePageName = $tablePage->info('name');
			$pagePackagesTable = Engine_Api::_()->getDbtable('packages', 'sitepage');
			$pagePackageTableName = $pagePackagesTable->info('name');
			$select = $this->select()
								->setIntegrityCheck(false)
								->from($pollTableName, array('poll_id', 'owner_id', 'page_id', 'title', 'views', 'comment_count', 'like_count', 'vote_count','description'))
								->joinLeft($tablePageName, "$tablePageName.page_id = $pollTableName.page_id", array('page_id', 'title AS page_title', 'closed', 'approved', 'declined', 'draft', 'expiration_date', 'photo_id as page_photo_id'))
								->join($pagePackageTableName, "$pagePackageTableName.package_id = $tablePageName.package_id",array('package_id', 'price'))
								->where($pollTableName .'.approved != ?', 0)
								->where($pollTableName .'.search != ?', 0);

			if($listtype == 'recent_poll') {
				$select = $select->order($pollTableName .'.poll_id DESC');

			} elseif ($listtype == 'comment_poll') {
					$select = $select->where($pollTableName .'.comment_count != ?', 0)
														->order($pollTableName .'.comment_count DESC')
														->order($pollTableName .'.poll_id DESC');

			} elseif ($listtype == 'like_poll') {
					$select = $this->getlikes($select);
					
			} elseif ($listtype == 'view_poll') {
					$select = $select->where($pollTableName .'.views != ?', 0)
														->order($pollTableName .'.views DESC')
														->order($pollTableName .'.poll_id DESC');

			} elseif($listtype == 'vote_poll') {
					$select = $select->where($pollTableName .'.vote_count != ?', 0)
														->order($pollTableName .'.vote_count DESC')
														->order($pollTableName .'.poll_id DESC');
			}
							
			if ($listtype == 'sponsored') {
				$select->where($pagePackageTableName . '.price != ?', '0.00')
				       ->order($pagePackageTableName . '.price' . ' DESC');
			}

			$select->limit($total_sitepagepolls);

			if (!empty($params['title'])) {

				$select->where($tablePageName . ".title LIKE ? ", '%' . $params['title'] . '%');
			}

			if (!empty($params['search_poll'])) {
				$select->where($pollTableName . ".title LIKE ? ", '%' . $params['search_poll'] . '%');
			}

			$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
			if(isset($params['show']) && $params['show'] == 'my_poll') {
				$select->where($pollTableName . '.owner_id = ?', $viewer_id);
			}
			elseif ((isset($params['show']) && $params['show'] == 'sponsored poll') || !empty($params['sponsoredpoll'])){
				$select->where($pagePackageTableName . '.price != ?', '0.00');
				$select->order($pagePackageTableName . '.price' . ' DESC');
			}
			elseif (isset($params['show']) && $params['show'] == 'Networks') {
				$select = $tablePage->getNetworkBaseSql($select, array('browse_network' => 1));

			}
			elseif (isset($params['show']) && $params['show'] == 'my_like') {
				$likeTableName = Engine_Api::_()->getDbtable('likes', 'core')->info('name');
				$viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
				$select->join($likeTableName, "$likeTableName.resource_id = $tablePageName.page_id")
								->where($likeTableName . '.poster_type = ?', 'user')
								->where($likeTableName . '.poster_id = ?', $viewer_id)
								->where($likeTableName . '.resource_type = ?', 'sitepage_page');
			}
			
			if ((isset($params['orderby']) && $params['orderby'] == 'view_count') || !empty($params['viewedpoll'])) {
				$select = $select->order($pollTableName .'.views DESC')
													->order($pollTableName .'.creation_date DESC');
			}
			elseif ((isset($params['orderby']) && $params['orderby'] == 'comment_count') || !empty($params['commentedpoll'])) {
				$select = $select->order($pollTableName .'.comment_count DESC')
													->order($pollTableName .'.creation_date DESC');
			} 
			elseif ((isset($params['orderby']) && $params['orderby'] == 'like_count') || !empty($params['likedpoll'])) {
				$select = $this->getlikes($select);
			} 
			elseif ((isset($params['orderby']) && $params['orderby'] == 'vote_count') || !empty($params['votedpoll'])) {
				$select = $select->order($pollTableName .'.vote_count DESC')
												->order($pollTableName .'.creation_date DESC');
			} 
			elseif (isset($params['orderby']) && $params['orderby'] == 'creation_date') {
				$select = $select->order($pollTableName .'.creation_date DESC');
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

			if(empty($params['orderby'])) {
				$order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.order', 1);
				switch ($order) {
					case "1":
						$select->order($pollTableName . '.creation_date DESC');
						break;
					case "2":
						$select->order($pollTableName . '.title');
						break;
					case "3":
						$select->order($pagePackageTableName . '.price' . ' DESC');
						$select->order($pollTableName . '.creation_date DESC');
						break;
				}   
			}
		
			$select = $select->where($tablePageName . '.closed = ?', '0')
												->where($tablePageName . '.approved = ?', '1')
												->where($tablePageName . '.declined = ?', '0')
												->where($tablePageName . '.draft = ?', '1');
    }

    if(isset($params['poll_content']) && !empty($params['poll_content'])) {
      return Zend_Paginator::factory($select);
    }
    else {
			return $this->fetchAll($select);
    }
  }

   /**
   * Get pagepoll list
   *
   * @param array $params
   * @return array $paginator;
   */
  public function getsitepagepollsPaginator($params = array()) {
    $paginator = Zend_Paginator::factory($this->getsitepagepollsSelect($params));
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }

  /**
   * Get page poll select query
   *
   * @param array $params
   * @return string $select;
   */
  public function getsitepagepollsSelect($params = array()) {

    $pollTable = Engine_Api::_()->getDbtable('polls', 'sitepagepoll');
    $pollTableName = $pollTable->info('name');

    if (isset($params['show_count']) && $params['show_count'] == 1) {
      $select = $pollTable->select();
      $select = $select
                      ->setIntegrityCheck(false)
                      ->from($pollTableName, array(
                          'COUNT(*) AS show_count'));
    } else {

      if ($params['orderby'] == 'views') {
        $select = $pollTable->select()
                        ->order('views DESC')
                        ->order('creation_date DESC');
      } elseif ($params['orderby'] == 'comment_count') {
        $select = $pollTable->select()
                        ->order('comment_count DESC')
                        ->order('creation_date DESC');
      } elseif ($params['orderby'] == 'like_count') {
        $select = $pollTable->select()
                        ->order('like_count DESC')
                        ->order('creation_date DESC');
      } elseif ($params['orderby'] == 'vote_count') {
        $select = $pollTable->select()
                        ->order('vote_count DESC')
                        ->order('creation_date DESC');
      } else {
        $select = $pollTable->select()
                        ->order(!empty($params['orderby']) ? $params['orderby'] . ' DESC' : 'creation_date DESC');
      }

      $select = $select
                      ->setIntegrityCheck(false)
                      ->from($pollTableName)
                      ->group("$pollTableName.poll_id");
    }

    if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
      $select->where($pollTableName . '.owner_id = ?', $params['user_id']);
    }

    if (!empty($params['user']) && $params['user'] instanceof User_Model_User) {
      $select->where($pollTableName . '.owner_id = ?', $params['user_id']->getIdentity());
    }

    if (!empty($params['users'])) {
      $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
      $select->where($pollTableName . '.owner_id in (?)', new Zend_Db_Expr($str));
    }

    if (isset($params['owner_id'])) {
      $select->where($pollTableName . '.owner_id = ?', $params['owner_id']);
    }

    if (isset($params['page_id'])) {
      $select->where($pollTableName . '.page_id = ?', $params['page_id']);
    }

    if (isset($params['approved'])) {
      $select->where($pollTableName . '.approved = ?', $params['approved']);
    }

    if (!empty($params['search'])) {
      $select->where($pollTableName . ".title LIKE ? OR " . $pollTableName . ".description LIKE ?", '%' . $params['search'] . '%');
    }

    if (!empty($params['show_poll']) && empty($params['search'])) {
      $select->where($pollTableName . ".approved = ?", 1)
              ->where($pollTableName . ".search = ?", 1)
              ->orwhere($pollTableName . ".owner_id = ?", $params['poll_owner_id']);
    }

    if (!empty($params['show_poll']) && (!empty($params['search']) )) {
      $select->where("($pollTableName.approved = 1)  AND ($pollTableName.search = 1) OR ($pollTableName.owner_id = " . $params['poll_owner_id'] . ")");
    }

    if (isset($params['page_id'])) {
      $select->where($pollTableName . '.page_id = ?', $params['page_id']);
    }

    return $select;
  }

  public function getlikes($select) {
		$table_likes = Engine_Api::_()->getDbtable('likes', 'core');
		$table_likes_name = $table_likes->info('name');
    $tablePoll = Engine_Api::_()->getDbtable('polls', 'sitepagepoll');
    $pollTableName = $tablePoll->info('name');
		//RETUN THE NAME OF TABLE
		return $select
						->join($table_likes_name, "$pollTableName.poll_id = $table_likes_name.resource_id   ", array('COUNT( ' . $table_likes_name . '.resource_id ) as count_likes'))
						->where($table_likes_name . '.resource_type = ?', 'sitepagepoll_poll')
						->where($pollTableName .'.approved != ?', 0)
						->where($pollTableName .'.search != ?', 0)
						->group($table_likes_name . '.resource_id')
						->order('count_likes DESC')
						->order($pollTableName .'.poll_id DESC');
  }

  public function topcreatorData($limit = null,$category_id) {

    //POLL TABLE NAME
    $pollTableName = $this->info('name');

    //PAGE TABLE
    $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $pageTableName = $pageTable->info('name');

    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($pageTableName, array('photo_id', 'title as sitepage_title','page_id'))
                    ->join($pollTableName, "$pageTableName.page_id = $pollTableName.page_id", array('COUNT(engine4_sitepage_pages.page_id) AS item_count'))
                    ->where($pageTableName.'.approved = ?', '1')
										->where($pageTableName.'.declined = ?', '0')
										->where($pageTableName.'.draft = ?', '1')
                    ->group($pollTableName . ".page_id")
                    ->order('item_count DESC')
                    ->limit($limit);
    if (!empty($category_id)) {
      $select->where($pageTableName . '.category_id = ?', $category_id);
    }
    return $select->query()->fetchAll();
  }

}
?>