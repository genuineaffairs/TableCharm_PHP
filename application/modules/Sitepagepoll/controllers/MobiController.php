<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MobiController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagepoll_MobiController extends Core_Controller_Action_Standard {

  public function init() {
  	
    //GET PAGE ID
    $page_id = $this->_getParam('page_id');

    //PACKAGE BASE PRIYACY START
    if (!empty($page_id)) {
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagepoll")) {
          return $this->_forward('requireauth', 'error', 'core');
        }
      } else {
        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'spcreate');
        if (empty($isPageOwnerAllow)) {
          return $this->_forward('requireauth', 'error', 'core');
        }
      }
    }
    //PACKAGE BASE PRIYACY END
    else {
      if ($this->_getParam('poll_id') != null) {
        $sitepagepoll = Engine_Api::_()->getItem('sitepagepoll_poll', $this->_getParam('poll_id'));
        $page_id = $sitepagepoll->page_id;
      }
    }
    
    //GET POLL ID
    $poll_id = $this->_getParam('poll_id');
    if ($poll_id) {
      $sitepagepoll = Engine_Api::_()->getItem('sitepagepoll_poll', $poll_id);
      if ($sitepagepoll) {
        Engine_Api::_()->core()->setSubject($sitepagepoll);
      }
    }
  }

  //ACTION FOR VIEW THE POLL
  public function viewAction() {
    
    //CHECK THE VERSION OF THE CORE MODULE
    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if ($coreversion < '4.1.0') {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
              ->setNoRender()
              ->setEnabled()
      ;
    }
  }
  
}

?>