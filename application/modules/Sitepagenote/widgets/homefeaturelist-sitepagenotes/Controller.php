<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_Widget_HomefeaturelistSitepagenotesController extends Engine_Content_Widget_Abstract {

  //ACTION FOR SHOWING THE MOST RECENT NOTES ON PAGE HOME / BROWSE
  public function indexAction() {

    //SEARCH PARAMETER
    $params = array();
    $params['zero_count'] = 'featured';
    $params['category_id'] = $this->_getParam('category_id',0);
    $params['limit'] = $this->_getParam('itemCount', 3);

    //MAKE PAGINATOR
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('notes', 'sitepagenote')->widgetNotesData($params);

    //NO RENDER
    if ( (Count($paginator) <= 0 ) ) {
      return $this->setNoRender();
    }
  }

}

?>