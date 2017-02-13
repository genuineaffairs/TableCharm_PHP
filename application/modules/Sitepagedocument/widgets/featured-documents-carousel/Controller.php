<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepagedocument_Widget_FeaturedDocumentsCarouselController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    //SEARCH PARAMETER
    $params = array();
    $params['zero_count'] = 'featured';
    $this->view->category_id = $params['category_id'] = $this->_getParam('category_id',0);
    $this->view->featuredDocuments = $featuredDocuments = Engine_Api::_()->getDbTable('documents', 'sitepagedocument')->widgetDocumentsData($params);
    $this->view->totalCount_document = count($featuredDocuments);
    if (!($this->view->totalCount_document > 0)) {
      return $this->setNoRender();
    }

    $this->view->inOneRow_document = $inOneRow = $this->_getParam('inOneRow', 3);
    $this->view->noOfRow_document = $noOfRow = $this->_getParam('noOfRow', 2);
    $this->view->totalItemShow_document = $totalItemShow = $inOneRow * $noOfRow;
    $params['limit'] = $totalItemShow;
    // List List featured
    $this->view->featuredDocuments = $featuredDocuments = Engine_Api::_()->getDbTable('documents', 'sitepagedocument')->widgetDocumentsData($params);

    // CAROUSEL SETTINGS  
    $this->view->interval = $interval = $this->_getParam('interval', 250);
    $this->view->count = $count = $featuredDocuments->count();
    $this->view->heightRow = @ceil($count / $inOneRow);
    $this->view->vertical = $this->_getParam('vertical', 0);
  }

}