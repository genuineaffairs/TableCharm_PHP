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
 
class Resume_Widget_PackagesController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    $this->view->packages = $packages = Engine_Api::_()->resume()->getPackages(array('enabled'=>1));

    if (empty($packages)) {
      return $this->setNoRender();
    }    
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $can_create = Engine_Api::_()->authorization()->isAllowed('resume', $viewer, 'create'); 
    $create_link = $this->_getParam('create_link', 1);
    
    $this->view->show_create = ($create_link == 2) || ($create_link == 1 && $can_create);
    
    $this->view->showdetails = $this->_getParam('showdetails', 1); 
    $this->view->showdescription = $this->_getParam('showdescription', 1);

  }

}