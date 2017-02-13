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
class Sitepageevent_Widget_SitepageSponsoredeventController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //NUMBER OF EVENTS IN LISTING
    $totalEvents = $this->_getParam('itemCount', 3);

    //GET EVENT DATAS
    $params = array();
    $params['category_id'] = $this->_getParam('category_id',0);
    $params['limit'] = $totalEvents;
    $widgetType = 'browseevent';
    $showTab = $this->_getParam("showevent","overall");
    if ($showTab == "upcoming") {
       $params['upcoming'] = 1;
    }
    else {
      $params['overall'] = 1;
    }
    $eventType = 'sponsored';
    $this->view->recentlyview = $row = Engine_Api::_()->getDbtable('events', 'sitepageevent')->widgetEventsData($params,$eventType);
		$sitepagePackageEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.enable', 1);
    if ( ( Count($row) <= 0 ) || empty($sitepagePackageEnable) ) {
      return $this->setNoRender();
    }
  }

}
?>