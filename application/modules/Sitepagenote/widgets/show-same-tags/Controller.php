<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_Widget_ShowSameTagsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
     $note_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('note_id', $this->_getParam('note_id', null));
    $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $note_id);

    if (empty($sitepagenote)) {
      return $this->_forward('notfound', 'error', 'core');
    }

     //GET SUBJECT
    $subject = Engine_Api::_()->getItem('sitepage_page', $sitepagenote->page_id);

    //GET TAB ID
    $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');
    $this->view->page_id = $sitepagenote->page_id;

    // PACKAGE BASE PRIYACY START
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      if (!Engine_Api::_()->sitepage()->allowPackageContent($subject->package_id, "modules", "sitepagenote")) {
        return $this->setNoRender();
      }
    } else {
      $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($subject, 'sncreate');
      if (empty($isPageOwnerAllow)) {
        return $this->setNoRender();
      }
    }


//     $sitepagenote_getlike = Zend_Registry::isRegistered('sitepagenote_getlike') ? Zend_Registry::get('sitepagenote_getlike') : null;
//     if (empty($sitepagenote_getlike)) {
//       return $this->setNoRender();
//     }
    // PACKAGE BASE PRIYACY END
    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK
    //FETCH NOTES
    $params = array();
    $widgetType = 'showsametag';
    $params['resource_type'] = $sitepagenote->getType();
    $params['resource_id'] = $sitepagenote->getIdentity();
    $params['note_id'] = $sitepagenote->getIdentity();
    $params['view_action'] = 1;
    $params['limit'] = $this->_getParam('itemCount', 3);

		$this->view->paginator = $paginator = Engine_Api::_()->getDbtable('notes', 'sitepagenote')->widgetNotesData($params,$widgetType);
    $this->view->count_note = Count($paginator);
    $this->view->limit_sitepagenote = $this->_getParam('itemCount', 3);

    if( Count($paginator) <= 0 ) {
      return $this->setNoRender();
    }
  }
}