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

 
class Folder_AttachmentController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($attachment_id = (int) $this->_getParam('attachment_id')) &&
          null !== ($attachment = Engine_Api::_()->getItem('folder_attachment', $attachment_id)) )
      {
        Engine_Api::_()->core()->setSubject($attachment);
      }

      else if( 0 !== ($folder_id = (int) $this->_getParam('folder_id')) &&
          null !== ($folder = Engine_Api::_()->getItem('folder', $folder_id)) )
      {
        Engine_Api::_()->core()->setSubject($folder);
      }
    }
    /*
    this will give error when object's privacy get to require login, and the visitor is NOT logged in
    $this->_helper->requireUser->addActionRequires(array(
      'edit',
      'delete',
    ));
    */
    
    $this->_helper->requireSubject->setActionRequireTypes(array(
      'view' => 'folder_attachment',
      'edit' => 'folder_attachment',
      'delete' => 'folder_attachment',
      'download' => 'folder_attachment',
    ));
    
  }

  
  public function downloadAction()
  {
    @set_time_limit(0);
    
    $attachment = Engine_Api::_()->core()->getSubject();
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    
    $this->view->folder = $folder = $attachment->getFolder();
    
    if( !$this->_helper->requireAuth()->setAuthParams($folder, $viewer, 'view')->isValid()) return;
    
    $is_owner = $folder->getOwner()->isSelf($viewer);
    if ($folder->secret_code && !$is_owner)
    {
      $codeSession = new Zend_Session_Namespace($folder->getGuid());
      if ($codeSession->secret_code != $folder->secret_code) {
        return $this->_redirectCustom($attachment);
      }
    }    
    // Increment view count
    //if( !$subject->getOwner()->isSelf($viewer) )
    //{
      $attachment->download_count++;
      $attachment->save();
    //}
    
    $file = $attachment->getFile();
    
    $service = $file->getStorageService();
    
    if ($service instanceof Storage_Service_Local)
    {
	    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . $attachment->getFile()->storage_path;
	    
	    if( file_exists($path) && is_file($path) ) {

	      // Kill zend's ob
	      while( ob_get_level() > 0 ) {
	        ob_end_clean();
	      }
        header("Content-Encoding: none"); // fix ob_start("ob_gzhandler") in SE v4.2.6
	      header("Content-Disposition: attachment; filename=" . urlencode($file->name), true);
	      header("Content-Transfer-Encoding: Binary", true);
	      header("Content-Type: application/force-download", true);
	      header("Content-Type: application/octet-stream", true);
	      header("Content-Type: application/download", true);
	      header("Content-Description: File Transfer", true);
	      header("Content-Length: " . filesize($path), true);
	      flush();
	
	      $fp = fopen($path, "r");
	      while( !feof($fp) )
	      {
	        echo fread($fp, 65536);
	        flush();
	      }
	      fclose($fp);
	    }

    }
    else 
    {
      while( ob_get_level() > 0 ) {
        ob_end_clean();
      }

      $string = file_get_contents($attachment->getAttachmentUrl());
      
      header("Content-Encoding: none"); // fix ob_start("ob_gzhandler") in SE v4.2.6
      header("Content-Disposition: attachment; filename=" . urlencode($file->name), true);
      header("Content-Transfer-Encoding: Binary", true);
      header("Content-Type: application/force-download", true);
      header("Content-Type: application/octet-stream", true);
      header("Content-Type: application/download", true);
      header("Content-Description: File Transfer", true);
      header("Content-Length: " . $file->size, true);
      flush();

      //echo file_get_contents($attachment->getAttachmentUrl());
      
      $bufferSize = 65536; // 8192
      
      $chars = strlen($string)-1;
			for ($start=0; $start <= $chars; $start += $bufferSize) {
				echo substr($string,$start,$bufferSize);
				flush();
			}

    }

    exit();
    
  }

  
  public function viewAction()
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $folder = $subject->getFolder();
        
    if( !$this->_helper->requireAuth()->setAuthParams($folder, $viewer, 'view')->isValid()) return;
    
    // Increment view count
    if( !$subject->getOwner()->isSelf($viewer) )
    {
      $subject->view_count++;
      $subject->save();
    }

    // Render
    $this->_helper->content
        ->setNoRender()
        ->setEnabled()
        ;
  }
  
  
  public function editAction()
  {
  	
    $this->view->attachment = $attachment = Engine_Api::_()->core()->getSubject();
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->folder = $folder = $attachment->getFolder();
    
    if( !$this->_helper->requireAuth()->setAuthParams($folder, $viewer, 'edit')->isValid()) return;
        
    $this->view->form = $form = new Folder_Form_Attachment_Edit();

    if( !$this->getRequest()->isPost() )
    {
      $form->populate($attachment->toArray());
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('attachments', 'folder')->getAdapter();
    $db->beginTransaction();

    try
    {
      $attachment->setFromArray($form->getValues())->save();

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved')),
      'layout' => 'default-simple',
      'parentRefresh' => true,
      'closeSmoothbox' => true,
    ));
  }


  
  public function deleteAction()
  { 
    $attachment = Engine_Api::_()->core()->getSubject();
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    
    $this->view->folder = $folder = $attachment->getFolder();
    if( !$this->_helper->requireAuth()->setAuthParams($folder, $viewer, 'edit')->isValid()) return;
    
    $this->view->form = $form = new Folder_Form_Attachment_Delete();

    if( !$this->getRequest()->isPost() )
    {
      $form->populate($attachment->toArray());
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('attachments', 'folder')->getAdapter();
    $db->beginTransaction();

    try
    {
      $attachment->delete();

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
		
    /*
    $parentRedirect = Zend_Controller_Front::getInstance()->getRouter()
      ->assemble(array('controller'=>'attachment', 'action' => 'list', 'subject' => $folder->getGuid()), 'folder_extended', true);
    
    $parentRedirect = $folder->getHref();
    */
      
    return $this->_forward('success', 'utility', 'core', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('File deleted')),
      'layout' => 'default-simple',
      'parentRefresh' => true,
      'closeSmoothbox' => true,
    ));  
    
  }
    

  
}