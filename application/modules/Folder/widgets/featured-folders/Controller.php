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
 
 
 
class Folder_Widget_FeaturedFoldersController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    
    $params = array(
      'search' => 1,
      'featured' => 1,
      'limit' => $this->_getParam('max', 5),
      'order' => $this->_getParam('order', 'random'),
      'period' => $this->_getParam('period'),
      'parent_type' => $this->_getParam('parent_type'),
      'parent_id' => $this->_getParam('parent_id'),
      'user' => $this->_getParam('user'),
      'keyword' => $this->_getParam('keyword'),
      'category' => $this->_getParam('category'),
    );
    
    $this->view->paginator = $paginator = Engine_Api::_()->folder()->getFoldersPaginator($params);
    
    $this->view->showphoto = $this->_getParam('showphoto', 1);
    $this->view->showdetails = $this->_getParam('showdetails', 1);
    $this->view->showmeta = $this->_getParam('showmeta', 1); 
    $this->view->showdescription = $this->_getParam('showdescription', 1); 
       
    if ($paginator->getTotalItemCount() == 0) {
      return $this->setNoRender();
    }

    $this->view->widget_name = 'folder_featuredfolders_'.$this->getElement()->getIdentity();
    $this->view->use_slideshow = $paginator->getTotalItemCount() > 1;
  }

}