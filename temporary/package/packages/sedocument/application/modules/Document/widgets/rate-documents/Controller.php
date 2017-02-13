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
class Document_Widget_RateDocumentsController extends Engine_Content_Widget_Abstract
{ 
	public function indexAction()
  {
		//CHECK THAT RATING IS ALLOWED OR NOT
    $show_rate = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.rating', 1);
		if(empty($show_rate)) {
      return $this->setNoRender();
    }

    //FETCH DOCUMENTS
    $params = array();
    $params['orderby'] = 'rating DESC';
    $params['zero_count'] = 'rating';
    $params['limit'] = $this->_getParam('itemCount', 3);
    $this->view->paginator = Engine_Api::_()->getDbtable('documents', 'document')->widgetDocumentsData($params);

    if (Count($this->view->paginator) <= 0) {
      return $this->setNoRender();
    }
  }
}
?>