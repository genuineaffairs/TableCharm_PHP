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
class Folder_Widget_FileTitleController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $this->view->attachment = $subject = Engine_Api::_()->core()->getSubject('folder_attachment');
    
    if( !($subject instanceof Folder_Model_Attachment) ) {
      return $this->setNoRender();
    } 
    
    $folder = $subject->getFolder();
    $this->view->total_folders = Engine_Api::_()->getItemTable('folder')->countFolders(array('parent'=>$folder->getParent()));
  }
}