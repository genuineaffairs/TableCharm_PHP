<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepageevent_Widget_FeaturedEventsCarouselController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    //SEARCH PARAMETER
    $params = array();
    $params['feature_events'] = 1;
    $this->view->category_id = $params['category_id'] = $this->_getParam('category_id',0);
    $this->view->featuredEvents = $featuredEvents = Engine_Api::_()->getDbTable('events', 'sitepageevent')->widgetEventsData($params);
    $this->view->totalCount_event = count($featuredEvents);
    if (!($this->view->totalCount_event > 0)) {
      return $this->setNoRender();
    }

    $this->view->inOneRow_event = $inOneRow = $this->_getParam('inOneRow', 3);
    $this->view->noOfRow_event = $noOfRow = $this->_getParam('noOfRow', 2);
    $this->view->totalItemShow_event = $totalItemShow = $inOneRow * $noOfRow;
    $params['limit'] = $totalItemShow;
    // List List featured
    $this->view->featuredEvents = $this->view->featuredEvents = $featuredEvents = Engine_Api::_()->getDbTable('events', 'sitepageevent')->widgetEventsData($params);

    // CAROUSEL SETTINGS  
    $this->view->interval = $interval = $this->_getParam('interval', 250);
    $this->view->count = $count = $featuredEvents->count();
    $this->view->heightRow = @ceil($count / $inOneRow);
    $this->view->vertical = $this->_getParam('vertical', 0);
  }

}