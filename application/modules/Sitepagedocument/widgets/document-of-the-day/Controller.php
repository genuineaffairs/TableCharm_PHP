<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagedocument_Widget_DocumentOfTheDayController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $this->view->documentOfDay = $documentOfDay = Engine_Api::_()->getDbtable('documents', 'sitepagedocument')->documentOfDay();
    if (empty($documentOfDay)) {
      return $this->setNoRender();
    }
  }

}
?>