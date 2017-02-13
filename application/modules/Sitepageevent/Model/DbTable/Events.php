<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Events.php 6590 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_Model_DbTable_Events extends Engine_Db_Table {

  protected $_rowClass = "Sitepageevent_Model_Event";

  public function getEventUserType() {

    global $sitepageevent_getUserInfo;
    $isReturn = empty($sitepageevent_getUserInfo) ? null : 'owner, owner_member, owner_member_member, owner_network, registered, everyone';
    return $isReturn;
  }

  /**
   * Gets events data
   *
   * @param array $params
   * @return Zend_Db_Table_Select
   */
  public function widgetEventsData($params, $widgetType = NULL, $listType = NULL) {

    $tableEventName = $this->info('name');

    if (isset($params['view_action']) && !empty($params['view_action'])) {
      $select = $this->select()
              ->from($tableEventName)
              ->where($tableEventName . '.page_id = ?', $params['page_id']);
    } else {
      $pagePackagesTable = Engine_Api::_()->getDbtable('packages', 'sitepage');
      $pagePackageTableName = $pagePackagesTable->info('name');
      $tablePage = Engine_Api::_()->getDbtable('pages', 'sitepage');
      $tablePageName = $tablePage->info('name');

      $select = $this->select()
              ->setIntegrityCheck(false)
              ->from($tableEventName);
      if ($widgetType != 'browseevent') {
        $select->where("$tableEventName.endtime > FROM_UNIXTIME(?)", time());
        $select->order($tableEventName . '.starttime ASC');
      }
      $select->join($tablePageName, "$tablePageName.page_id = $tableEventName.page_id", array('page_id', 'title AS page_title', 'closed', 'approved', 'declined', 'draft', 'expiration_date', 'owner_id', 'photo_id as page_photo_id'))
              ->join($pagePackageTableName, "$pagePackageTableName.package_id = $tablePageName.package_id", array('package_id', 'price'));

      if (isset($params['page_id']) && !empty($params['page_id'])) {
        $select = $select->where($tableEventName . '.page_id = ?', $params['page_id']);
      }

      if (!empty($params['title'])) {

        $select->where($tablePageName . ".title LIKE ? ", '%' . $params['title'] . '%');
      }

      if (!empty($params['search_event'])) {
        $select->where($tableEventName . ".title LIKE ?", '%' . $params['search_event'] . '%');
      }
      if (!empty($params['event_category_id'])) {
        $select->where($tableEventName . '.category_id = ?', $params['event_category_id']);
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


      if (isset($params['orderby']) && $params['orderby'] == 'view_count') {
        $select = $select
                ->order($tableEventName . '.view_count DESC')
                ->order($tableEventName . '.creation_date DESC')
        ;
      } elseif (isset($params['orderby']) && $params['orderby'] == 'member_count') {
        $select = $select
                ->order($tableEventName . '.member_count DESC');
      } elseif (isset($params['orderby']) && $params['orderby'] == 'creation_date') {
        $select = $select
                ->order($tableEventName . '.creation_date DESC');
      } elseif (isset($params['orderby']) && $params['orderby'] == 'starttime') {
        $select = $select
                ->order(!empty($params['orderby']) ? $params['orderby'] . ' ASC' : $tableEventName . '.starttime ASC');
      }

      $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      if (isset($params['show']) && $params['show'] == 'my_event') {
        $select->where($tableEventName . '.user_id = ?', $viewer_id);
      } elseif (isset($params['show']) && $params['show'] == 'past_event') {
        $select->where("$tableEventName.endtime < FROM_UNIXTIME(?)", time());
      } elseif ((isset($params['show']) && $params['show'] == 'upcoming_event') || !empty($params['upcomingevent'])) {
        $select->where("$tableEventName.endtime > FROM_UNIXTIME(?)", time());
        $select->order($tableEventName . '.starttime ASC');
      } elseif ((isset($params['show']) && $params['show'] == 'sponsored event') || !empty($params['sponsoredevent']) || ($widgetType == 'sponsored')) {

        $select->where($pagePackageTableName . '.price != ?', '0.00');
        $select->order($pagePackageTableName . '.price' . ' DESC');
      } elseif (isset($params['show']) && $params['show'] == 'Networks') {
        $select = $tablePage->getNetworkBaseSql($select, array('browse_network' => 1));
      } elseif ((isset($params['show']) && $params['show'] == 'featured') || !empty($params['featuredevent'])) {
        $select = $select
                ->where($tableEventName . '.featured = ?', 1)
                ->order($tableEventName . '.creation_date DESC');
      } elseif (isset($params['show']) && $params['show'] == 'my_like') {
        $likeTableName = Engine_Api::_()->getDbtable('likes', 'core')->info('name');
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $select
                ->join($likeTableName, "$likeTableName.resource_id = $tablePageName.page_id")
                ->where($likeTableName . '.poster_type = ?', 'user')
                ->where($likeTableName . '.poster_id = ?', $viewer_id)
                ->where($likeTableName . '.resource_type = ?', 'sitepage_page');
      }

      if (empty($params['orderby'])) {
        $order = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepageevent.order', 1);
        switch ($order) {
          case "1":
            $select->order($tableEventName . '.creation_date DESC');
            break;
          case "2":
            $select->order($tableEventName . '.title');
            break;
          case "3":
            $select->order($tableEventName . '.featured' . ' DESC');
            break;
          case "4":
            $select->order($pagePackageTableName . '.price' . ' DESC');
            break;
          case "5":
            $select->order($tableEventName . '.featured' . ' DESC');
            $select->order($pagePackageTableName . '.price' . ' DESC');
            break;
          case "6":
            $select->order($pagePackageTableName . '.price' . ' DESC');
            $select->order($tableEventName . '.featured' . ' DESC');
            break;
        }
      }

      $select = $select->order($tableEventName . '.event_id DESC');
      if (isset($params['limit']) && !empty($params['limit'])) {
        if (!isset($params['start_index']))
          $params['start_index'] = 0;
        $select->limit($params['limit'], $params['start_index']);
      }
      if (isset($params['feature_events']) && !empty($params['feature_events'])) {
        if (isset($params['upcoming']) && !empty($params['upcoming']))
          $select->where("$tableEventName.endtime > FROM_UNIXTIME(?)", time());
        else if (isset($params['overall']) && !empty($params['overall']))
          $select->order($tableEventName . '.starttime ASC');
        $select->where($tableEventName . '.featured = ?', 1);
      }

      $select = $select
              ->where($tablePageName . '.search = ?', '1')
              ->where($tablePageName . '.closed = ?', '0')
              ->where($tablePageName . '.approved = ?', '1')
              ->where($tablePageName . '.declined = ?', '0')
              ->where($tablePageName . '.draft = ?', '1');
      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
        $select->where($tablePageName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
      }

      //Start Network work
      if (!isset($params['page_id']) || empty($params['page_id'])) {
        $select = $tablePage->getNetworkBaseSql($select, array('not_groupBy' => 1, 'extension_group' => $tableEventName . ".event_id"));
      }
      //End Network work
    }
    if ($widgetType == 'browseevent' && $listType == 'listingevent') {
      return Zend_Paginator::factory($select);
    } else {
      return $this->fetchAll($select);
    }
  }

  /**
   * Get sitepageevent detail
   *
   * @param array $params : contain desirable sitepageevent info
   * @return object of sitepageevent
   */
  public function getSitepageeventsPaginator($params = array()) {

    $paginator = Zend_Paginator::factory($this->getSitepageeventsSelect($params));
    if (!empty($params['page'])) {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if (!empty($params['limit'])) {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }

  /**
   * Get sitepageevent
   *
   * @param array $params : contain desirable sitepageevent info
   * @return array of sitepageevents
   */
  public function getSitepageeventsSelect($params = array()) {

    //$tableEvent = Engine_Api::_()->getDbTable('events', 'sitepageevent');
    $tableEventName = $this->info('name');

    if (isset($params['show_count']) && $params['show_count'] == 1) {
      if (isset($params['orderby']) && $params['orderby'] == 'view_count') {
        $select->where("$tableEventName.endtime > FROM_UNIXTIME(?)", time());
        $select = $this->select()
                ->where("$tableEventName.endtime > FROM_UNIXTIME(?)", time());
      } elseif (isset($params['orderby']) && $params['orderby'] == 'member_count') {
        $select = $this->select()
                ->where("$tableEventName.endtime > FROM_UNIXTIME(?)", time());
      } elseif (isset($params['orderby']) && $params['orderby'] == 'creation_date') {
        $select = $this->select()
                ->where("$tableEventName.endtime > FROM_UNIXTIME(?)", time());
      } elseif (isset($params['orderby']) && $params['orderby'] == 'featured') {
        $select = $this->select()
                ->where($tableEventName . '.featured = ?', 1)
                ->where("$tableEventName.endtime > FROM_UNIXTIME(?)", time());
      } else {
        $select = $this->select();
      }
      $select = $select
              ->setIntegrityCheck(false)
              ->from($tableEventName, array(
          'COUNT(*) AS show_count'));
    } else {
      if (isset($params['orderby']) && $params['orderby'] == 'view_count') {
        $select->where("$tableEventName.endtime > FROM_UNIXTIME(?)", time());
        $select = $this->select()
                ->where("$tableEventName.endtime > FROM_UNIXTIME(?)", time())
                ->order('view_count DESC')
                ->order('creation_date DESC')
        ;
      } elseif (isset($params['orderby']) && $params['orderby'] == 'member_count') {
        $select = $this->select()
                ->where("$tableEventName.endtime > FROM_UNIXTIME(?)", time())
                ->order('member_count DESC');
      } elseif (isset($params['orderby']) && $params['orderby'] == 'creation_date') {
        $select = $this->select()
                ->where("$tableEventName.endtime > FROM_UNIXTIME(?)", time())
                ->order('creation_date DESC');
      } elseif (isset($params['orderby']) && $params['orderby'] == 'featured') {
        $select = $this->select()
                ->where($tableEventName . '.featured = ?', 1)
                ->where("$tableEventName.endtime > FROM_UNIXTIME(?)", time());
      } else {
        $select = $this->select()
                ->order(!empty($params['orderby']) ? $params['orderby'] . ' ASC' : 'starttime ASC');
      }

      $select = $select
              ->setIntegrityCheck(false)
              ->from($tableEventName, array('event_id', 'page_id', 'user_id', 'title', 'creation_date', 'view_count', 'description', 'search', 'starttime', 'endtime', 'location', 'host', 'member_count', 'photo_id', 'approval', 'invite', 'featured', 'seao_locationid', 'category_id'))
              ->group("$tableEventName.event_id");
    }

    if (isset($params['selectedbox']) && $params['selectedbox'] == 'allmyevent') {
      $membership = Engine_Api::_()->getDbtable('membership', 'sitepageevent');
      $select = $membership->getMembershipsOfSelect(Engine_Api::_()->user()->getViewer());
      if (isset($params['page_id'])) {
        $select->where($tableEventName . '.page_id = ?', $params['page_id']);
      }

      if (!empty($params['search'])) {
        $select->where($tableEventName . ".title LIKE ? OR " . $tableEventName . ".description LIKE ?", '%' . $params['search'] . '%');
      }
      $select
              ->order(!empty($params['orderby']) ? $params['orderby'] . ' ASC' : 'starttime ASC');
      return $select;
    }

    if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
      $select->where($tableEventName . '.user_id = ?', $params['user_id']);
    }

    if (!empty($params['user']) && $params['user'] instanceof User_Model_User) {
      $select->where($tableEventName . '.user_id = ?', $params['user_id']->getIdentity());
    }

    if (!empty($params['users'])) {
      $str = (string) ( is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users'] );
      $select->where($tableEventName . '.user_id in (?)', new Zend_Db_Expr($str));
    }

    if (isset($params['user_id'])) {
      $select->where($tableEventName . '.user_id = ?', $params['user_id']);
    }

    if (!empty($params['search'])) {
      $select->where($tableEventName . ".title LIKE ? OR " . $tableEventName . ".description LIKE ?", '%' . $params['search'] . '%');
    }

    if (isset($params['orderby']) && $params['orderby'] == 'starttime') {
      $select->where("$tableEventName.endtime > FROM_UNIXTIME(?)", time());
    }

    if (isset($params['orderby']) && $params['orderby'] == 'endtime') {
      $select->where("$tableEventName.endtime < FROM_UNIXTIME(?)", time());
    }

    if (isset($params['page_id'])) {
      $select->where($tableEventName . '.page_id = ?', $params['page_id']);
    }

    if (!empty($params['show_event']) && empty($params['search'])) {
      $select
              ->where($tableEventName . ".search = ?", 1)
              ->orwhere($tableEventName . ".user_id = ?", $params['event_owner_id']);
    }

    if (!empty($params['show_event']) && (!empty($params['search']) )) {
      $select->where("($tableEventName.search = 1) OR ($tableEventName.user_id = " . $params['event_owner_id'] . ")");
    }

    if (isset($params['orderby']) && $params['orderby'] == 'starttime') {
      $select->where("$tableEventName.endtime > FROM_UNIXTIME(?)", time());
    }

    if (isset($params['orderby']) && $params['orderby'] == 'endtime') {
      $select->where("$tableEventName.endtime < FROM_UNIXTIME(?)", time());
    }

    if (isset($params['page_id'])) {
      $select->where($tableEventName . '.page_id = ?', $params['page_id']);
    }
    return $select;
  }

  /**
   * Return event of the day
   *
   * @return Zend_Db_Table_Select
   */
  public function eventOfDay() {

    //CURRENT DATE TIME
    $date = date('Y-m-d');

    //GET ITEM OF THE DAY TABLE NAME
    $eventOfTheDayTableName = Engine_Api::_()->getDbtable('itemofthedays', 'sitepage')->info('name');

    //GET PAGE TABLE NAME
    $pageTableName = Engine_Api::_()->getDbtable('pages', 'sitepage')->info('name');

    //GET EVENT TABLE NAME
    $eventTableName = $this->info('name');

    //MAKE QUERY
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($eventTableName, array('event_id', 'title', 'page_id', 'user_id', 'description', 'photo_id'))
            ->join($eventOfTheDayTableName, $eventTableName . '.event_id = ' . $eventOfTheDayTableName . '.resource_id')
            ->join($pageTableName, $eventTableName . '.page_id = ' . $pageTableName . '.page_id', array(''))
            ->where($pageTableName . '.approved = ?', '1')
            ->where($pageTableName . '.declined = ?', '0')
            ->where($pageTableName . '.draft = ?', '1')
            ->where('resource_type = ?', 'sitepageevent_event')
            ->where('start_date <= ?', $date)
            ->where("$eventTableName.endtime > FROM_UNIXTIME(?)", time())
            ->where('end_date >= ?', $date)
            ->order('Rand()');

    //PAGE SHOULD BE AUTHORIZED
    if (Engine_Api::_()->sitepage()->hasPackageEnable())
      $select->where($pageTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));

    //PAGE SHOULD BE AUTHORIZED
    $stusShow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.status.show', 1);
    if ($stusShow == 0) {
      $select->where($pageTableName . '.closed = ?', '0');
    }

    //RETURN RESULTS
    return $this->fetchRow($select);
  }

  public function topcreatorData($limit = null, $category_id) {

    //EVENT TABLE NAME
    $eventTableName = $this->info('name');

    //PAGE TABLE
    $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $pageTableName = $pageTable->info('name');

    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($pageTableName, array('photo_id', 'title as sitepage_title', 'page_id'))
            ->join($eventTableName, "$pageTableName.page_id = $eventTableName.page_id", array('COUNT(engine4_sitepage_pages.page_id) AS item_count'))
            ->where($pageTableName . '.approved = ?', '1')
            ->where($pageTableName . '.declined = ?', '0')
            ->where($pageTableName . '.draft = ?', '1')
            ->group($eventTableName . ".page_id")
            ->order('item_count DESC')
            ->limit($limit);
    if (!empty($category_id)) {
      $select->where($pageTableName . '.category_id = ?', $category_id);
    }
    return $select->query()->fetchAll();
  }

}

?>