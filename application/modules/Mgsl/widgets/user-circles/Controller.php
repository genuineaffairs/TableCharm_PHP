<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Mgsl_Widget_UserCirclesController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    // // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();

    // // Get paginator
    // // $subject = Engine_Api::_()->core()->getSubject('user');
    // $membership = Engine_Api::_()->getDbtable('membership', 'group');
    // $this->view->paginator = $paginator = Zend_Paginator::factory($membership->getMembershipsOfSelect($viewer));

    // // // Set item count per page and current page number
    // $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
    // $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    // // Add count to title if configured
    // if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
    //   $this->_childCount = $paginator->getTotalItemCount();
    // }

    // get ids of circles the viewing user is a member of
    $memTable = Engine_Api::_()->getDbtable('membership', 'advgroup');
    $select = $memTable->select()
                ->where('user_id = ?',$viewer->getIdentity())
                ->where('active = 1');
    $memberships = $memTable->fetchAll($select);
    $group_ids = array(0);
    foreach($memberships as $membership) {
      $group_ids[] = $membership->resource_id;
    }
    $params['group_ids'] = $group_ids;

    //Get data
    $this->view->paginator = $paginator =  Engine_Api::_()->getItemTable('group')->getGroupPaginator($params);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $itemsPerPage = Engine_Api::_()->getApi('settings', 'core')->getSetting('advgroup.page', 10);
    $paginator->setItemCountPerPage($itemsPerPage);
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}
