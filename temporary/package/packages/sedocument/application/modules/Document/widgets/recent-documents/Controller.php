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
class Document_Widget_RecentDocumentsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    //FETCH DOCUMENTS
    $params = array();
    $params['limit'] = $this->_getParam('itemCount', 3);
    $this->view->paginator = Engine_Api::_()->getDbtable('documents', 'document')->widgetDocumentsData($params);

    if (Count($this->view->paginator) <= 0) {
      return $this->setNoRender();
    }
  }
}
?>