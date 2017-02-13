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

class Resume_Plugin_Menus
{

  public function canCreateResumes($row)
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }

    // Must be able to create resumes
    if( !Engine_Api::_()->authorization()->isAllowed('resume', $viewer, 'create') ) {
      return false;
    }
    
    $params = array();
    if(preg_match('/Post New CV Profile/', $row->label)) {
      $params = array(
          'label' => 'Create New CV Profile'
      );
    }
    
    return array_merge($params, $row->params);
  }

  public function canViewResumes()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    // Must be able to view resumes
    if( !Engine_Api::_()->authorization()->isAllowed('resume', $viewer, 'view') ) {
      return false;
    }

    return true;
  }  
  
  public function canSaveResumes()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }    
    
    // Must be able to view resumes
    if( !Engine_Api::_()->authorization()->isAllowed('resume', $viewer, 'view') ) {
      return false;
    }

    return true;
  }  
  
  public function onMenuInitialize_ResumeGutterList($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $resume = Engine_Api::_()->core()->getSubject('resume');

    if( !($resume instanceof Resume_Model_Resume) ) {
      return false;
    } 
    
    // Modify params
    $params = $row->params;
    $params['params']['user'] = $resume->user_id;
    return $params;
  }

  public function onMenuInitialize_ResumeGutterCreate($row)
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }

    // Must be able to create resumes
    if( !Engine_Api::_()->authorization()->isAllowed('resume', $viewer, 'create') ) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_ResumeGutterEdit($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $resume = Engine_Api::_()->core()->getSubject('resume');

    if( !($resume instanceof Resume_Model_Resume) ) {
      return false;
    }    
    
    if( !$resume->authorization()->isAllowed($viewer, 'edit') ) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['resume_id'] = $resume->getIdentity();
    return $params;
  }

  public function onMenuInitialize_ResumeGutterDelete($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $resume = Engine_Api::_()->core()->getSubject('resume');

    if( !($resume instanceof Resume_Model_Resume) ) {
      return false;
    }
    
    if( !$resume->authorization()->isAllowed($viewer, 'delete') ) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['resume_id'] = $resume->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_ResumeGutterPublish($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $resume = Engine_Api::_()->core()->getSubject('resume');

    if( !($resume instanceof Resume_Model_Resume) ) {
      return false;
    }
    
    if ($resume->isPublished()) {
      return false;
    }
    
    if( !$resume->authorization()->isAllowed($viewer, 'edit') ) {
      return false;
    }

    
    // Modify params
    $params = $row->params;
    $params['params']['resume_id'] = $resume->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_ResumeGutterPrint($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $resume = Engine_Api::_()->core()->getSubject('resume');

    if( !($resume instanceof Resume_Model_Resume) ) {
      return false;
    }    

    // Modify params
    $params = $row->params;
    $params['params']['resume_id'] = $resume->getIdentity();
    $params['params']['slug'] = $resume->getSlug();
    $params['params']['version'] = 'print';
    return $params;
  }  
  
  
  public function onMenuInitialize_ResumeDashboardView($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $resume = Engine_Api::_()->core()->getSubject('resume');

    if( !($resume instanceof Resume_Model_Resume) ) {
      return false;
    }
    
    if( !$resume->authorization()->isAllowed($viewer, 'view') ) {
      return false;
    }    
    
    // Modify params
    $params = $row->params;
    $params['params']['resume_id'] = $resume->getIdentity();
    $params['params']['slug'] = $resume->getSlug();
    return $params;
  }  
  
  public function onMenuInitialize_ResumeDashboardEdit($row)
  {
    return $this->onMenuInitialize_ResumeGutterEdit($row);
  }

  public function onMenuInitialize_ResumeDashboardSections($row)
  {
    return $this->onMenuInitialize_ResumeGutterEdit($row);
  }  
  
  public function onMenuInitialize_ResumeDashboardLocation($row)
  {
    return $this->onMenuInitialize_ResumeGutterEdit($row);
  }
  
  public function onMenuInitialize_ResumeDashboardStyle($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $resume = Engine_Api::_()->core()->getSubject('resume');

    if( !($resume instanceof Resume_Model_Resume) ) {
      return false;
    }    
    
    if( !$resume->authorization()->isAllowed($viewer, 'edit') ) {
      return false;
    }

    if (!Engine_Api::_()->authorization()->isAllowed('resume', $viewer, 'style')) {
      return false;
    }
    
    // Modify params
    $params = $row->params;
    $params['params']['resume_id'] = $resume->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_ResumeDashboardPhoto($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $resume = Engine_Api::_()->core()->getSubject('resume');

    if( !($resume instanceof Resume_Model_Resume) ) {
      return false;
    }    
    
    //if (!Engine_Api::_()->authorization()->isAllowed('resume', $viewer, 'photo')) {
    if( !$resume->authorization()->isAllowed($viewer, 'photo') ) {  
      return false;
    }
    
    // Modify params
    $params = $row->params;
    $params['params']['subject'] = $resume->getGuid();
    return $params;
  }
  
  public function onMenuInitialize_ResumeDashboardVideo($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $resume = Engine_Api::_()->core()->getSubject('resume');

    if( !($resume instanceof Resume_Model_Resume) ) {
      return false;
    }    
    
    //if (!Engine_Api::_()->authorization()->isAllowed('resume', $viewer, 'photo')) {
    if( !$resume->authorization()->isAllowed($viewer, 'video') ) {  
      return false;
    }
    
    // Modify params
    $params = $row->params;
    $params['params']['subject'] = $resume->getGuid();
    $params['params']['resume_id'] = $resume->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_ResumeDashboardEpayments($row)
  {
    return $this->onMenuInitialize_ResumeGutterEdit($row);
  }
  
  public function onMenuInitialize_ResumeDashboardDelete($row)
  {
    return $this->onMenuInitialize_ResumeGutterDelete($row);
  }  
  

  
  //////////
  
  protected function _onMenuInitialize_ResumeAdminEpaymentBase($row)
  {
    $epayment = Engine_Api::_()->core()->getSubject('epayment');

    if( !($epayment instanceof Epayment_Model_Epayment) ) {
      return false;
    }    
    
    $params = $row->params;
    $params['params']['epayment_id'] = $epayment->getIdentity();
    return $params;
  }
  
  public function onMenuInitialize_ResumeAdminEpaymentProcess($row)
  {
    return $this->_onMenuInitialize_ResumeAdminEpaymentBase($row);
  }
    
  public function onMenuInitialize_ResumeAdminEpaymentView($row)
  {
    return $this->_onMenuInitialize_ResumeAdminEpaymentBase($row);
  }
  
  public function onMenuInitialize_ResumeAdminEpaymentEdit($row)
  {
    return $this->_onMenuInitialize_ResumeAdminEpaymentBase($row);
  } 
  
  public function onMenuInitialize_ResumeAdminEpaymentDelete($row)
  {
    return $this->_onMenuInitialize_ResumeAdminEpaymentBase($row);
  }

  public function onMenuInitialize_ResumePackagesStart($row)
  {
    return true;
  }  
  
}