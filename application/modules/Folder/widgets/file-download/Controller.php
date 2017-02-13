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
class Folder_Widget_FileDownloadController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $this->view->attachment = $attachment = Engine_Api::_()->core()->getSubject('folder_attachment');
    
    if( !($attachment instanceof Folder_Model_Attachment) ) {
      return $this->setNoRender();
    } 
    
    $subject = $attachment->getParent();
    
    $is_owner = $subject->getOwner()->isSelf($viewer);
    
    $this->view->is_locked = false;
    
    if ($subject->secret_code && !$is_owner)
    {
      $this->view->is_locked = true;

      $codeSession = new Zend_Session_Namespace($subject->getGuid());
      
      if ($codeSession->secret_code == $subject->secret_code) {
        $this->view->is_locked = false;
        return;
      }
      
      $this->view->form = $form = new Folder_Form_Attachment_Password(array('item'=>$subject));
      $request = Zend_Controller_Front::getInstance()->getRequest();
      
      if (!$request->isPost())
      {
        return;
      }

      if (!$form->isValid($request->getPost()))
      {
        return;
      }

      $codeSession->secret_code = $form->secret_code->getValue();
      
      $this->view->is_locked = false;
    }
    
  }
}