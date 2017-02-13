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
class Sitepage_Widget_TagsSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF NOT AUTHORIZED
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SUBJECT AND OTHER TAGS DATA
    $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    $this->view->userTags = $sitepage->tags()->getTagsByTagger($sitepage->getOwner());
  }
}

?>