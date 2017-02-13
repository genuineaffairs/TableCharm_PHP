<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagedocument_Widget_OptionsDocumentsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

	 //GET PAGE DOCUMNET MODEL
   $sitepageDocument = Engine_Api::_()->getItem('sitepagedocument_document', Zend_Controller_Front::getInstance()->getRequest()->getParam('document_id'));
    if (empty($sitepageDocument)) {
      return $this->setNoRender();
    }

    //GET NAVIGATION
    $this->view->gutterNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepagedocument_gutter');
  }
}
?>