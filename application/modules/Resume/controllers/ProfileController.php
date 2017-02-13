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
class Resume_ProfileController extends Core_Controller_Action_Standard
{
  public function init()
  {
    // @todo this may not work with some of the content stuff in here, double-check
    $subject = null;
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      $id = $this->_getParam('resume_id');
      if( null !== $id )
      {
        $subject = Engine_Api::_()->getItem('resume', $id);
        if( $subject && $subject->getIdentity() )
        {
          Engine_Api::_()->core()->setSubject($subject);
        }
      }
    }

    $this->_helper->requireSubject('resume');
    
    if (Engine_Api::_()->core()->hasSubject())
    {    
      //$this->_helper->requireAuth()->setNoForward()->setAuthParams(
      $this->_helper->requireAuth()->setAuthParams(
        $subject,
        Engine_Api::_()->user()->getViewer(),
        'view'
      );
    }
  }

  public function indexAction()
  {
    $this->view->headLink()->appendStylesheet(
      $this->view->layout()->staticBaseUrl . 'application/modules/Resume/externals/styles/resume.css', 'screen, print'
    );
    
    $this->view->resume = $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    $error = '';
    
    if (!$subject->isPublished()) {
      $error = 'draft';
    }
    else if (!$subject->isApprovedStatus()) {
      $error = 'not_approved';
    }
    else if ($subject->isExpired())
    {
      $error = 'expired';
    }
    
    // hack to work around SE v4.1.8 User::isAdmin bug "Registry is already initialized"
    try
    {
    	$is_admin = $viewer->isAdmin();
    } 
    catch (Exception $ex)
    {
      $is_admin = Engine_Api::_()->getApi('core', 'authorization')->isAllowed('admin', null, 'view');	
    }
    
    if ($error && !$is_admin && !$viewer->isSelf($subject->getOwner())) 
    {
      $this->view->error = $error;
      return;
    }
    
    // Increment view count
    if( !$subject->getOwner()->isSelf($viewer) )
    {
      $subject->view_count++;
      $subject->save();
    }

      // Get styles
    $table = Engine_Api::_()->getDbtable('styles', 'core');
    $select = $table->select()
      ->where('type = ?', $subject->getType())
      ->where('id = ?', $subject->getIdentity())
      ->limit();

    $row = $table->fetchRow($select);

    if( null !== $row && !empty($row->style) ) {
      $this->view->headStyle()->appendStyle($row->style);
    }
    
    // Fix error: hidden tab selected
    if ($viewer->isSelf($subject->getOwner()) && !$this->_getParam('tab')) {
      $this->getRequest()->setParam('tab', Engine_Api::_()->resume()->getDetailTabId());
    }

    if ($this->_getParam('version') == 'print') {
      $this->_helper->layout->disableLayout();
      $this->renderScript('profile/print.tpl');
    }
    else {
      // Render
      $this->_helper->content
          ->setNoRender()
          ->setEnabled()
          ;
    }
  }
}