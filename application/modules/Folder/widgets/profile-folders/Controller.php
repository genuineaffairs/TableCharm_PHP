<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Folder
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
class Folder_Widget_ProfileFoldersController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject();
    
    if (!($subject instanceof Core_Model_Item_Abstract)) {
      return $this->setNoRender();
    }
    
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }   
    
    $this->view->is_owner = false;
    $owner = $subject->getOwner('user');
    if (!($owner instanceof User_Model_User) || !$owner->getIdentity()) {
    	return $this->setNoRender();
    }
    else {
    	$this->view->is_owner = $owner->isSelf($viewer);
    }
    
    $params = array(
      'parent' => $subject,
      'search' => 1,
      'limit' => $this->_getParam('max', 5),
      'order' => $this->_getParam('order', 'recent'),
      'period' => $this->_getParam('period'),
      'keyword' => $this->_getParam('keyword'),
      'category' => $this->_getParam('category'),
    );
    
    if ($this->_getParam('featured', 0)) {
      $params['featured'] = 1;
    }
    
    if ($this->_getParam('sponsored', 0)) {
      $params['sponsored'] = 1;
    }
    
    $this->view->paginator = $paginator = Engine_Api::_()->folder()->getFoldersPaginator($params);

    $this->view->display_style = $this->_getParam('display_style', 'wide');
    
    $this->view->showphoto = $this->_getParam('showphoto', 1);
    $this->view->showdetails = $this->_getParam('showdetails', 1); 
    $this->view->showmeta = $this->_getParam('showmeta', 1); 
    $this->view->showdescription = $this->_getParam('showdescription', 1); 
            
    $this->view->order = $params['order'];

    
    // Add count to title if configured
    $this->_childCount = $paginator->getTotalItemCount();
    
    if ($paginator->getTotalItemCount() <= 0 && !$this->view->is_owner) {
      return $this->setNoRender();
    }
    
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}