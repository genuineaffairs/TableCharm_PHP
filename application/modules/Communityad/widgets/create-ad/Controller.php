<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Widget_CreateAdController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->viewer_object = $viewer_object = Engine_Api::_()->user()->getViewer();
    $checkCreate = Engine_Api::_()->authorization()->isAllowed('communityad', $viewer_object, 'create');


    if (empty($checkCreate)) {
      return $this->setNoRender();
    }
    $this->view->user_id = $viewer_object->getIdentity();
    $this->view->site_title = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
  }

}
?>