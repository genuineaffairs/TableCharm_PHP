<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_Widget_ProfileMembersController extends Seaocore_Content_Widget_Abstract {

  protected $_childCount;

  //ACTION FOR SHOWING THE TABS OF THE UPDATES FEED AND JOINED MEMBER OF THE EVENT
  public function indexAction() {
  	
    // JUST REMOVE THE TITLE DECORATOR
//    $this->getElement()->removeDecorator('Title');

    //DON'T RENDER IF THERE IS NO SUBJECT
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    // GET SITEPAGE EVENT SUBJECT
    $this->view->sitepageevent_subject = $sitepageevent_subject = Engine_Api::_()->core()->getSubject();
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepageevent_subject->page_id);

    //PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageevent")) {
        return $this->setNoRender();
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'secreate');
      if (empty($isPageOwnerAllow)) {
        return $this->setNoRender();
      }
    }
    //PACKAGE BASE PRIYACY END     
     
    //START MANAGE-ADMIN CHECK    
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK
    
    //GET PARAMS
    $this->view->page = $page = $this->_getParam('page', 1);
    $this->view->search = $search = $this->_getParam('search');
    $this->view->waiting = $waiting = $this->_getParam('waiting');

    //GET MEMBERS
    $members = null;
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($viewer->getIdentity() && $sitepageevent_subject->isOwner($viewer)) {
      $this->view->waitingMembers = Zend_Paginator::factory($sitepageevent_subject->membership()->getMembersSelect(false));
      if ($waiting) {
        $this->view->members = $members = $this->view->waitingMembers;
      }
    }

    if (!$members) {
      $select = $sitepageevent_subject->membership()->getMembersObjectSelect();
      if ($search) {
        $select->where('displayname LIKE ?', '%' . $search . '%');
      }
      $this->view->members = $members = Zend_Paginator::factory($select);
    }

    $paginator = $members;
    $members->setCurrentPageNumber($page);

    //DO NOT RENDER IF NOTHING TO SHOW
    if ($paginator->getTotalItemCount() <= 0 && '' == $search) {
      return $this->setNoRender();
    }

    //ADD COUNT TO TITLE IF CONFIGURED
    if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
  }

  //RETURN THE COUNT OF THE EVENT
  public function getChildCount() {
    return $this->_childCount;
  }

}

?>