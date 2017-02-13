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
class Sitepageevent_Widget_EventOfTheDayController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $this->view->eventOfDay = $eventOfDay = Engine_Api::_()->getDbtable('events', 'sitepageevent')->eventOfDay();
    if (empty($eventOfDay)) {
      return $this->setNoRender();
    }
  }

}
?>