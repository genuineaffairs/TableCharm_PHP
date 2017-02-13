<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagepoll_Widget_HomecommentSitepagepollsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

   
    //NUMBER OF POLLS IN LISTING
    $total_sitepagepolls = $this->_getParam('itemCount', 3);
    $values = array();
    $values['total_sitepagepolls'] = $total_sitepagepolls;
    $values['category_id'] = $this->_getParam('category_id',0);
    $this->view->listCommentPolls = $listCommentPolls = Engine_Api::_()->getDbtable('polls', 'sitepagepoll')->getPollListing('comment_poll', $values);

    if (Count($listCommentPolls) <= 0) {
      return $this->setNoRender();
    }
  }

}
?>