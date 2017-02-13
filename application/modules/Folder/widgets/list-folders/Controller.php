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
 
 
 
class Folder_Widget_ListFoldersController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    
    //$request = Zend_Controller_Front::getInstance()->getRequest();
  	//print_r($request->getParams());
    // Don't render this if not authorized
    
    $viewer = Engine_Api::_()->user()->getViewer();

    $params = array(
      'search' => 1,
      'limit' => $this->_getParam('max', 5),
      'order' => $this->_getParam('order', 'recent'),
      'period' => $this->_getParam('period'),
      'parent_type' => $this->_getParam('parent_type'),
      'parent_id' => $this->_getParam('parent_id'),
      'user' => $this->_getParam('user'),
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
    
    $this->view->showphoto = $this->_getParam('showphoto', $this->view->display_style == 'narrow' ? 1 : 1);
    $this->view->showdetails = $this->_getParam('showdetails', $this->view->display_style == 'narrow' ? 0 : 1); 
    $this->view->showmeta = $this->_getParam('showmeta', $this->view->display_style == 'narrow' ? 1 : 1); 
    $this->view->showdescription = $this->_getParam('showdescription', $this->view->display_style == 'narrow' ? 0 : 1); 
    
    $this->view->order = $params['order'];
    
    if ($paginator->getTotalItemCount() == 0 && !$this->_getParam('showemptyresult', false)) {
      return $this->setNoRender();
    }
  }

}