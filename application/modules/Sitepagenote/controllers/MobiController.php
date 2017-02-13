<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MobiController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_MobiController extends Core_Controller_Action_Standard {

  public function init() {
  	
    //GET PAGE ID
    $page_id = $this->_getParam('page_id');

    //PACKAGE BASE PRIYACY START
    if (!empty($page_id)) {
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
        if (!Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagenote")) {
          return $this->_forward('requireauth', 'error', 'core');
        }
      } else {
        $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sncreate');
        if (empty($isPageOwnerAllow)) {
          return $this->_forward('requireauth', 'error', 'core');
        }
      }
    }
    //PACKAGE BASE PRIYACY END    
    else {
      if ($this->_getParam('note_id') != null) {
        $sitepagenote = Engine_Api::_()->getItem('sitepagenote_note', $this->_getParam('note_id'));
        $page_id = $sitepagenote->page_id;
      }
    }
  }

  //ACTION FOR VIEW THE NOTE
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