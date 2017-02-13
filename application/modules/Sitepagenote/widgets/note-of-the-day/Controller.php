<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_Widget_NoteOfTheDayController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $this->view->noteOfDay = $noteOfDay = Engine_Api::_()->getDbtable('notes', 'sitepagenote')->noteOfDay();
    if (empty($noteOfDay)) {
      return $this->setNoRender();
    }
  }

}
?>