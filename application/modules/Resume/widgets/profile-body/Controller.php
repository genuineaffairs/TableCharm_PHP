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
class Resume_Widget_ProfileBodyController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $is_app = $request->getParam('from_app') == 1 && ($resume_id = $request->getParam('resume_id'));

    if ($is_app) {
      $this->view->is_app = $is_app;
      $subject = Engine_Api::_()->getItem('resume', $resume_id);
    } else {
      if( !Engine_Api::_()->core()->hasSubject() ) {
        return $this->setNoRender();
      }
      // Get subject and check auth
      $subject = Engine_Api::_()->core()->getSubject('resume');
    }
    $this->view->resume = $subject;
    
    if( !($subject instanceof Resume_Model_Resume) ) {
      return $this->setNoRender();
    }    
        
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }
    
  }
}