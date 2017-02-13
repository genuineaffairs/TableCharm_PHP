<?php

class Ynevent_Widget_ProfileFollowController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }
    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('event');
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    // Must be a member
//     if( !$subject->membership()->isMember($viewer, true) )
//     {
//       return $this->setNoRender();
//     }
	if ($viewer->getIdentity() == 0) {
		return $this->setNoRender();
	}
    
    // Build form
    $this->view->form = new Ynevent_Form_Follow();
    $followTable = Engine_Api::_()->getDbTable('follow','ynevent');
    $row = $followTable->getFollowEvent($subject->getIdentity(),$viewer->getIdentity());
    //$row = $subject->membership()->getRow($viewer);
    $this->view->viewer_id = $viewer->getIdentity();

	$r = Zend_Controller_Front::getInstance()->getRequest();
	if( $r->isPost() ) {
	    if( !$row ) {
		      $row = $followTable->createRow();
		      $row->resource_id = $subject->getIdentity();
		      $row->user_id = $viewer->getIdentity();
		      $row->follow = 0;
		      $row->save();
	    } else {
	    	$row->follow = !($row->follow);
	    	$row->save();
    }
	}
	
    $this->view->follow = $row->follow;
  }
}