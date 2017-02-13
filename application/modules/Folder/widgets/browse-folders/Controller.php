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
 
 
 
class Folder_Widget_BrowseFoldersController extends Engine_Content_Widget_Abstract
{

  protected $_childCount;
  
  public function indexAction()
  {
    /*
    if( Engine_Api::_()->core()->hasSubject() ) {
      echo "HAS SUBJECT - ";
      $subject = Engine_Api::_()->core()->getSubject();
      
      echo " type=".$subject->getType();
      echo " id=".$subject->getIdentity();
    }
    else {
      echo 'NO SUBJECT';
    }
    */
    $this->view->paginator = $paginator = $this->loadFolderPaginator();
    $this->view->formValues = $params = $this->getQueryParams();
    
    $this->view->assign($params);
    
    // Add count to title if configured
    $this->_childCount = $paginator->getTotalItemCount();    
    
    $this->view->showphoto = $this->_getParam('showphoto', 1);
    $this->view->showdetails = $this->_getParam('showdetails', 1); 
    $this->view->showmeta = $this->_getParam('showmeta', 1); 
    $this->view->showdescription = $this->_getParam('showdescription', 1);     
    
    if (isset($params['category']))
    {
      $this->view->categoryObject = $category = Engine_Api::_()->folder()->getCategory($params['category']);
      if ($category instanceof Folder_Model_Category) 
      {
        $title = $this->view->translate('Folders / %s', $this->view->translate($category->getTitle()));
        $this->getElement()->setTitle($title);
      }
    }
    
    if (!empty($params['tag']))
    {
      $this->view->tagObject = Engine_Api::_()->getItem('core_tag', $params['tag']);
    } 
    
    if (!empty($params['user']))
    {
      $this->view->userObject = Engine_Api::_()->user()->getUser($params['user']);
    }
    
    if (!empty($params['parent']))
    {
      try
      {
        $p = Engine_Api::_()->getItemByGuid($params['parent']);
        if ($p instanceof Core_Model_Item_Abstract && $p->getIdentity() > 0)
        {
          $this->view->parentObject = $p;
        }
      }
      catch (Exception $e)
      {
        // silence
      }
    }
    
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }  
  
  protected function loadFolderPaginator()
  {    
    $queryParams = $this->getQueryParams();
    $forcedParams = $this->getForcedParams();

    $params = array_merge($queryParams, $forcedParams);
    $paginataor = Engine_Api::_()->folder()->getFoldersPaginator($params);

    return $paginataor;

  }

  protected function getForcedParams()
  {
    $force_params = array(
      'search' => 1,
      'limit' => $this->_getParam('max', 10),
      'preorder' => (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('folder.preorder', 1),
    );
    
    return $force_params;
  }
  
  protected function getQueryParams()
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();
    
    foreach (array('action','module','controller','rewrite') as $key) {
      unset($params[$key]);
    }
    
    $params = Engine_Api::_()->getApi('filter','radcodes')->removeKeyEmptyValues($params);
    
    return $params;
  }  
  
}