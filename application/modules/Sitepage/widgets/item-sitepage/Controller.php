<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_ItemSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		$this->view->dayitem = Engine_Api::_()->getDbtable('pages', 'sitepage')->getItemOfDay();

    //DONT RENDER IF SITEPAGE COUNT ZERO
    if (!(count($this->view->dayitem) > 0)) {
      return $this->setNoRender();
    }
  }
}
?>