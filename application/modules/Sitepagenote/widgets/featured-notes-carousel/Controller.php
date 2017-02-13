<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitepagenote_Widget_FeaturedNotesCarouselController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    //SEARCH PARAMETER
    $params = array();
    $params['zero_count'] = 'featured';
    $this->view->category_id = $params['category_id'] = $this->_getParam('category_id',0);
    $this->view->featuredNotes = $featuredNotes = Engine_Api::_()->getDbTable('notes', 'sitepagenote')->widgetNotesData($params);
    $this->view->totalCount_note = count($featuredNotes);
    if (!($this->view->totalCount_note > 0)) {
      return $this->setNoRender();
    }

    $this->view->inOneRow_note = $inOneRow = $this->_getParam('inOneRow', 3);
    $this->view->noOfRow_note = $noOfRow = $this->_getParam('noOfRow', 2);
    $this->view->totalItemShownote = $totalItemShow = $inOneRow * $noOfRow;
    $params['limit'] = $totalItemShow;
    // List List featured
    $this->view->featuredNotes = $this->view->featuredNotes = $featuredNotes = Engine_Api::_()->getDbTable('notes', 'sitepagenote')->widgetNotesData($params);

    // CAROUSEL SETTINGS  
    $this->view->interval = $interval = $this->_getParam('interval', 250);
    $this->view->count = $count = $featuredNotes->count();
    $this->view->heightRow = @ceil($count / $inOneRow);
    $this->view->vertical = $this->_getParam('vertical', 0);
  }

}