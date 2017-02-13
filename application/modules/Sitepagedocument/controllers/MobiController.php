<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MobiController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
include_once APPLICATION_PATH . '/application/modules/Sitepagedocument/Api/Scribdsitepage.php';

class Sitepagedocument_MobiController extends Core_Controller_Action_Standard {

  public function init() {
  	
    //SET SCRIBD API AND SCECRET KEY
    $this->scribd_api_key = Engine_Api::_()->getApi('settings', 'core')->sitepagedocument_api_key;
    $this->scribd_secret = Engine_Api::_()->getApi('settings', 'core')->sitepagedocument_secret_key;
    $this->scribdsitepage = new Scribdsitepage($this->scribd_api_key, $this->scribd_secret);
  }

  //ACTION FOR VIEW THE DOCUMENT
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