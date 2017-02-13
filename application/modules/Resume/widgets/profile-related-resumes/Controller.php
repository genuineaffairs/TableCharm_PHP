<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
class Resume_Widget_ProfileRelatedResumesController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  	
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $this->view->resume = $subject = Engine_Api::_()->core()->getSubject('resume');
    
    if( !($subject instanceof Resume_Model_Resume) ) {
      return $this->setNoRender();
    }    
        
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }
    

    $params = array(
      'search' => 1,
      'limit' => $this->_getParam('max', 5),
      'order' => $this->_getParam('order', 'random'),
      'period' => $this->_getParam('period'),
      'keyword' => $this->_getParam('keyword'),
      'status' => $this->_getParam('status'),
      'media' => $this->_getParam('media'),
      'category' => $this->_getParam('category'),
    );    
    
    if ($this->_getParam('featured', 0)) {
      $params['featured'] = 1;
    }
    
    $this->view->paginator = $paginator = Engine_Api::_()->resume()->getRelatedResumes($subject, $params);
    
    if (empty($paginator)) {
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