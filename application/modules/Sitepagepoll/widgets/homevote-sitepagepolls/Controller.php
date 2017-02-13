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
class Sitepagepoll_Widget_HomevoteSitepagepollsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //VOTING IS ALLOW OR NOT
    $isAllowVote = Zend_Registry::isRegistered('sitepagepoll_isAllowVote') ? Zend_Registry::get('sitepagepoll_isAllowVote') : null;

    //NUMBER OF POLLS IN LISTING
    $total_sitepagepolls = $this->_getParam('itemCount', 3);
    $values = array();
    $values['total_sitepagepolls'] = $total_sitepagepolls;
    $values['category_id'] = $this->_getParam('category_id',0);
    $this->view->listVotedPolls = $listVotedPolls = Engine_Api::_()->getDbtable('polls', 'sitepagepoll')->getPollListing('vote_poll', $values);

    //NO RENDER IF VOTING IS NOT ALLOWED OR NO POLLS HAVE ANY VOTEING
    if ((Count($listVotedPolls) <= 0) || empty($isAllowVote)) {
      return $this->setNoRender();
    }
  }

}
?>