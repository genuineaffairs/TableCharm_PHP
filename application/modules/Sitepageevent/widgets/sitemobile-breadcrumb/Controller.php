<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_Widget_SitemobileBreadcrumbController extends Engine_Content_Widget_Abstract {

  //ACTION FOR SHOWING THE BREADCRUMB
  public function indexAction() {

    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SUBJECT OF SITEPAGEEVENT
    $sitepageevent_subject = Engine_Api::_()->core()->getSubject('sitepageevent_event');
    
    $this->view->noShowTitle=$this->_getParam('noShowTitle',0);
    
    if (isset($sitepageevent_subject)) {
      $this->view->sitepageevent = $sitepageevent_subject;
    }

    //GET ITEM OF SITEPAGE
    $this->view->sitepage = Engine_Api::_()->getItem('sitepage_page', $sitepageevent_subject->page_id);
    //SEND TAB ID TO TPL FILE

    $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    
    $this->view->icon = $this->_getParam('icon');
  }

}

?>