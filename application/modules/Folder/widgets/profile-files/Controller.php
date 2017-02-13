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
class Folder_Widget_ProfileFilesController extends Engine_Content_Widget_Abstract
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
    
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    } 
    $params = array('limit' => 9999);   
    $this->view->paginator = $paginator = $subject->getAttachmentPaginator($params);
    
    $this->view->can_upload = $is_owner = $subject->getOwner()->isSelf($viewer);
    
    $this->view->is_locked = false;
    
    if ($subject->secret_code && !$is_owner)
    {
      $this->view->is_locked = true;

      $codeSession = new Zend_Session_Namespace($subject->getGuid());
      
      /*
      echo " subject=";
      var_dump($subject->secret_code);
      echo " session=";
      var_dump($codeSession->secret_code);
      echo " aa=";
      var_dump($this->view->is_locked);
      */
      if ($codeSession->secret_code == $subject->secret_code) {
        $this->view->is_locked = false;
        return;
      }
      
      //echo ' bb=';
      //var_dump($this->view->is_locked);
      $this->view->form = $form = new Folder_Form_Folder_Password(array('item'=>$subject));
      
      $request = Zend_Controller_Front::getInstance()->getRequest();
      
      if (!$request->isPost())
      {
        return;
      }
      
      //echo ' cc=';
      //var_dump($this->view->is_locked);      
      
      if (!$form->isValid($request->getPost()))
      {
        return;
      }
      //echo ' dd=';
      //var_dump($this->view->is_locked);   
      $codeSession->secret_code = $form->secret_code->getValue();
      
      $this->view->is_locked = false;
    }

    
  }
}