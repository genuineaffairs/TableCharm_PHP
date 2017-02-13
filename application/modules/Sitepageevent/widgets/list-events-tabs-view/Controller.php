<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_Widget_ListEventsTabsViewController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $this->view->is_ajax = $is_ajax = $this->_getParam('isajax', '');
    $this->view->showViewMore = $this->_getParam('showViewMore', 1);
    $this->view->category_id = $category_id = $this->_getParam('category_id',0);
    if (empty($is_ajax)) {
      $this->view->tabs = $tabs = Engine_Api::_()->getItemTable('seaocore_tab')->getTabs(array('module' => 'sitepageevent', 'type' => 'events', 'enabled' => 1));
      $count_tabs = count($tabs);
      if (empty($count_tabs)) {
        return $this->setNoRender();
      }
      $activeTabName = $tabs[0]['name'];
    }
    $this->view->marginPhoto = $this->_getParam('margin_photo', 12);
    $table = Engine_Api::_()->getItemTable('sitepageevent_event');
    $tableEventName = $table->info('name');
    $tablePage = Engine_Api::_()->getDbtable('pages', 'sitepage'); 
    $tablePageName = $tablePage->info('name');
    $select = $table->select()
										->setIntegrityCheck(false)
                    ->from($tableEventName)
                    ->joinLeft($tablePageName, "$tablePageName.page_id = $tableEventName.page_id", array('title AS page_title', 'photo_id as page_photo_id'));
 
    $select = $select
              ->where($tablePageName . '.closed = ?', '0')
              ->where($tablePageName . '.approved = ?', '1')
              ->where($tablePageName . '.declined = ?', '0')
              ->where($tablePageName . '.draft = ?', '1');

    if (!empty($category_id)) {
			$select = $select->where($tablePageName . '.	category_id =?', $category_id);
		}
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $select->where($tablePageName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    } 
            
    $paramTabName = $this->_getParam('tabName', '');

    if (!empty($paramTabName))
      $activeTabName = $paramTabName;

    $activeTab = Engine_Api::_()->getItemTable('seaocore_tab')->getTabs(array('module' => 'sitepageevent', 'type' => 'events', 'enabled' => 1, 'name' => $activeTabName));
    $this->view->activTab = $activTab = $activeTab['0'];

    switch ($activTab->name) {
      case 'recent_pageevents':
       $select->where("$tableEventName.endtime > FROM_UNIXTIME(?)", time());
       $select->order($tableEventName . '.starttime ASC');
        break;
      case 'member_pageevents':
				$select->order($tableEventName .'.member_count DESC');
        if(empty($activTab->show)) {
        $select->where("$tableEventName.endtime > FROM_UNIXTIME(?)", time());
        }
        break;
      case 'viewed_pageevents':
        $select->order($tableEventName .'.view_count DESC');
        if(empty($activTab->show)) {
					$select->where("$tableEventName.endtime > FROM_UNIXTIME(?)", time());
        }
        break;
      case 'featured_pageevents':
        $select->where($tableEventName .'.featured = ?', 1);
        $select->order('Rand()');
        if(empty($activTab->show)) {
        $select->where("$tableEventName.endtime > FROM_UNIXTIME(?)", time());
        }
        break;
      case 'random_pageevents':
        $select->order('Rand()');
        if(empty($activTab->show)) {
        $select->where("$tableEventName.endtime > FROM_UNIXTIME(?)", time());
        }
        break;
    }

    if ($activTab->name != 'featured_pageevents' && $activTab->name != 'random_pageevents') {
      $select->order('creation_date DESC');
    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($activTab->limit);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->count = $paginator->getTotalItemCount(); 
  }

}

?>
