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
class Sitepageevent_Widget_UpcomingSitepageeventController extends Engine_Content_Widget_Abstract {

  //ACTION FOR SHOWING THE UPCOMING EVENTS ON PAGE HOME / BROWSE
  public function indexAction() {

    $sitepageevent_upcommingPage = Zend_Registry::isRegistered('sitepageevent_upcommingPage') ? Zend_Registry::get('sitepageevent_upcommingPage') : null;

    //SEARCH PARAMETER
    $params = array();
    $params['category_id'] = $this->_getParam('category_id',0);
    $params['limit'] = $this->_getParam('itemCount', 3);

    //MAKE PAGINATOR
    $this->view->paginator = $paginator = $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('events', 'sitepageevent')->widgetEventsData($params);

    //NO RENDER
    if ((Count($paginator) <= 0) || empty($sitepageevent_upcommingPage)) {
      return $this->setNoRender();
    }
  }

}

?>