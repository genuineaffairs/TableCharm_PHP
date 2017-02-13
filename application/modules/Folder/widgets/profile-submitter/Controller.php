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
class Folder_Widget_ProfileSubmitterController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $this->view->folder = $subject = Engine_Api::_()->core()->getSubject('folder');
    
    if( !($subject instanceof Folder_Model_Folder) ) {
      return $this->setNoRender();
    }    
    
    $this->view->owner = $subject->getOwner();
    $this->view->totalFolders = Engine_Api::_()->folder()->countFolders(array('user'=>$subject->user_id, 'search'=>1));
  }
}