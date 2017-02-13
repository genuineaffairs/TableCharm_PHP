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
class Sitepageevent_Widget_HomefeaturelistSitepageeventsController extends Engine_Content_Widget_Abstract {

  //ACTION FOR SHOWING THE MOST RECENT EVENTS ON PAGE HOME / BROWSE
  public function indexAction() {

    //SEARCH PARAMETER
    $params = array();
    $params['feature_events'] = 1; 
    $params['category_id'] = $this->_getParam('category_id',0);
    $widgetType = 'browseevent';
    $showTab = $this->_getParam("showevent","overall");
    if ($showTab == "upcoming") {
       $params['upcoming'] = 1;
    }
    else {
      $params['overall'] = 1;
    }
    $params['limit'] = $this->_getParam('itemCount', 3);

    //MAKE PAGINATOR
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('events', 'sitepageevent')->widgetEventsData($params,$widgetType);

    //NO RENDER
    if ( (Count($paginator) <= 0 ) ) {
      return $this->setNoRender();
    }
  }

}

?>