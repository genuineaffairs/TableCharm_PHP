<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Document_Widget_SlideshowFeaturedDocumentsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//FETCH DOCUMENTS
    $params = array();
    $params['orderby'] = 'featured DESC';
		$params['featured'] = 1;
		$params['featured_slideshow'] = 1;
    $params['limit'] = $this->_getParam('itemCount', 10);
		$params['category_id'] = $this->_getParam('category_id', 0);
    $this->view->show_slideshow_object = Engine_Api::_()->getDbtable('documents', 'document')->widgetDocumentsData($params);

		//SET NO RENDER IF DATA IS EMPTY
    if (Count($this->view->show_slideshow_object) <= 0) {
      return $this->setNoRender();
    }
  }
}
?>