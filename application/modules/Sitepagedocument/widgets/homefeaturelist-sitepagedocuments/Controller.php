<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagedocument_Widget_HomefeaturelistSitepagedocumentsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

		//FETCH DOCUMENTS
		$params = array();
		$params['zero_count'] = 'featured';
    $params['category_id'] = $this->_getParam('category_id',0);
		$params['limit'] = $this->_getParam('itemCount', 3);
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('documents', 'sitepagedocument')->widgetDocumentsData($params);

    if (Count($paginator) <= 0) {
      return $this->setNoRender();
    }
  }

}
?>