<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9812 2012-11-01 02:14:01Z matthew $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_Widget_ProfileFriendsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {

    //General Friend settings
    $this->view->make_list = Engine_Api::_()->getApi('settings', 'core')->user_friends_lists;

    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }    
    
    // Don't render this if friendships are disabled
    if( !Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('user');
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }
    
    // Multiple friend mode
//    $select = $subject->membership()->getMembersOfSelect();

    $userMembershipTable = $subject->membership()->getReceiver();
    $userTable = Engine_Api::_()->getDbTable('users', 'user');
    $usersSitesTable = Engine_Api::_()->getDbTable('usersSites', 'sharedResources');
    
    $adapter = $userTable->getAdapter();
    $defaultFetchMode = $adapter->getFetchMode();
    $adapter->setFetchMode(Zend_Db::FETCH_OBJ);
    $select = $adapter->select();
    $select->from(array('um' => $userMembershipTable->info('name')))
            ->join(array('u' => $userTable->info('name')), "u.user_id = um.resource_id")
            ->join(array('us' => $usersSitesTable->info('name')), "u.user_id = us.user_id");

    // Get current site id
    $site_id = Engine_Api::_()->getApi('core', 'sharedResources')->getSiteId();

    $select->where('um.active = 1')
            ->where('um.user_id = ?', $subject->getIdentity())
            ->where('us.site_id = ?', $site_id);
    $select->order('displayname');

    $this->view->friends = $friends = $paginator = Zend_Paginator::factory($select);
    
    // Set item count per page and current page number
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 9));
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // Get stuff
    $ids = array();
    foreach( $friends as $friend ) {
      $ids[] = $friend->resource_id;
    }
    $adapter->setFetchMode($defaultFetchMode);
    $this->view->friendIds = $ids;

    // Get the items
    $friendUsers = array();
    foreach( Engine_Api::_()->getItemTable('user')->find($ids) as $friendUser ) {
      $friendUsers[$friendUser->getIdentity()] = $friendUser;
    }
    $this->view->friendUsers = $friendUsers;

    // Get lists if viewing own profile
    if( $viewer->isSelf($subject) ) {
      // Get lists
      $listTable = Engine_Api::_()->getItemTable('user_list');
      $this->view->lists = $lists = $listTable->fetchAll($listTable->select()->where('owner_id = ?', $viewer->getIdentity()));

      $listIds = array();
      foreach( $lists as $list ) {
        $listIds[] = $list->list_id;
      }

      // Build lists by user
      $listItems = array();
      $listsByUser = array();
      if( !empty($listIds) ) {
        $listItemTable = Engine_Api::_()->getItemTable('user_list_item');
        $listItemSelect = $listItemTable->select()
          ->where('list_id IN(?)', $listIds)
          ->where('child_id IN(?)', $ids);
        $listItems = $listItemTable->fetchAll($listItemSelect);
        foreach( $listItems as $listItem ) {
          //$list = $lists->getRowMatching('list_id', $listItem->list_id);
          //$listsByUser[$listItem->child_id][] = $list;
          $listsByUser[$listItem->child_id][] = $listItem->list_id;
        }
      }
      $this->view->listItems = $listItems;
      $this->view->listsByUser = $listsByUser;
    }

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}