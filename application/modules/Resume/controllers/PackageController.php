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
 
 
class Resume_PackageController extends Core_Controller_Action_Standard
{
  protected $_navigation;

  public function init()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('resume', null, 'view')->isValid() ) return;
    
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($package_id = (int) $this->_getParam('package_id')) &&
          null !== ($package = Engine_Api::_()->getItem('resume_package', $package_id)) )
      {
        Engine_Api::_()->core()->setSubject($package);
      }
    }


    $this->_helper->requireSubject->setActionRequireTypes(array(
      'view' => 'resume_package',
    ));
  }
  
  
  // NONE USER SPECIFIC METHODS
  public function browseAction()
  {
    $this->_loadNavigations();
    
    $this->view->packages = $packages = Engine_Api::_()->resume()->getPackages(array('enabled'=>1));
    
    $this->_helper->content->setEnabled();   
  }


  public function profileAction()
  {
    $this->_loadNavigations();
    
    $this->view->package = $package = Engine_Api::_()->core()->getSubject('resume_package');
    
    $this->view->packages = $packages = Engine_Api::_()->resume()->getPackages(array('enabled'=>1, 'exclude_package_id'=>$package->getIdentity()));
    
    $this->_helper->content->setEnabled();
  }
  
  public function viewAction()
  {
    //$this->_loadNavigations();
    
    $this->view->package = $package = Engine_Api::_()->core()->getSubject('resume_package');
    
    $this->view->packages = $packages = Engine_Api::_()->resume()->getPackages(array('enabled'=>1, 'exclude_package_id'=>$package->getIdentity()));
    
    $this->_helper->content->setEnabled();
  }


  protected function _loadNavigations()
  {
    /* Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('resume_main');
    */
    
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->can_create = Engine_Api::_()->authorization()->isAllowed('resume', $viewer, 'create'); 
  }
}

