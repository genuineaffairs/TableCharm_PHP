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
class Sitepagepoll_Widget_SitepageSponsoredpollController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //NUMBER OF POLLS IN LISTING
    $totalPolls = $this->_getParam('itemCount', 3);

    //GET POLL DATAS
    $params = array();
    $values['total_sitepagepolls'] = $totalPolls;
    $params['category_id'] = $this->_getParam('category_id',0);
    $pollType = 'sponsored';
    $this->view->recentlyview = $row = Engine_Api::_()->getDbtable('polls', 'sitepagepoll')->getPollListing($pollType,$params);
     $sitepagePackageEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.package.enable', 1);
    if ( ( Count($row) <= 0 )  | empty($sitepagePackageEnable)) {
      return $this->setNoRender();
    }
  }

}
?>