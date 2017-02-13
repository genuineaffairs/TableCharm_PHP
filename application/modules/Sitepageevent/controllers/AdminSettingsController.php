<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepageevent
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 6590 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_AdminSettingsController extends Core_Controller_Action_Admin {

    public function __call($method, $params) {
        /*
         * YOU MAY DISPLAY ANY ERROR MESSAGE USING FORM OBJECT.
         * YOU MAY EXECUTE ANY SCRIPT, WHICH YOU WANT TO EXECUTE ON FORM SUBMIT.
         * REMEMBER:
         *    RETURN TRUE: IF YOU DO NOT WANT TO STOP EXECUTION.
         *    RETURN FALSE: IF YOU WANT TO STOP EXECUTION.
         */
        if (!empty($method) && $method == 'Sitepageevent_Form_Admin_Global') {

        }
        return true;
    }
    
  //ACTION FOR GLOBAL SETTINGS
  public function indexAction() {
		if( $this->getRequest()->isPost() ) {
			$sitepageKeyVeri = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.lsettings', null);
			if( !empty($sitepageKeyVeri) ) {
				Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage.lsettings', trim($sitepageKeyVeri));
			}
			if( $_POST['sitepageevent_lsettings'] ) {
				$_POST['sitepageevent_lsettings'] = trim($_POST['sitepageevent_lsettings']);
			}
		}
    include_once APPLICATION_PATH . '/application/modules/Sitepageevent/controllers/license/license1.php';
  }

  //ACTION FOR WIDGET SETTINGS
  public function widgetAction() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepageevent_admin_main', array(), 'sitepageevent_admin_main_event_tab');
    $this->view->tabs = Engine_Api::_()->getItemTable('seaocore_tab')->getTabs(array('module' => 'sitepageevent', 'type' => 'events'));
  } 
  
  public function categoriesAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepageevent_admin_main', array(), 'sitepageevent_admin_main_categories');

    $this->view->categories = Engine_Api::_()->getDbtable('categories', 'sitepageevent')->fetchAll();
  }  
  
  public function addCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    // Generate and assign form
    $form = $this->view->form = new Sitepageevent_Form_Admin_Category();
    $form->setAction($this->view->url());
    
    // Check post
    if( !$this->getRequest()->isPost() ) {
      $this->renderScript('admin-settings/form.tpl');
      return;
    }
    
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->renderScript('admin-settings/form.tpl');
      return;
    }
    
    // Process
    $values = $form->getValues();
    
    $categoryTable = Engine_Api::_()->getDbtable('categories', 'sitepageevent');
    $db = $categoryTable->getAdapter();
    $db->beginTransaction();

    try {
      $categoryTable->insert(array(
        'title' => $values['label'],
      ));
      
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
    
    return $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh' => 10,
      'messages' => array('')
    ));
  }

  public function deleteCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->event_id = $id;
    $categoryTable = Engine_Api::_()->getDbtable('categories', 'sitepageevent');
    $eventTable = Engine_Api::_()->getDbtable('events', 'sitepageevent');
    $category = $categoryTable->find($id)->current();
    
    // Check post
    if( !$this->getRequest()->isPost() ) {
      $this->renderScript('admin-settings/delete-category.tpl');
      return;
    }
    
    // Process
    $db = $categoryTable->getAdapter();
    $db->beginTransaction();

    try {
      $category->delete();
      
      $eventTable->update(array(
        'category_id' => 0,
      ), array(
        'category_id = ?' => $category->getIdentity(),
      ));
      
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
    
    return $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh' => 10,
      'messages' => array('')
    ));
  }

  public function editCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->event_id = $id;
    $categoryTable = Engine_Api::_()->getDbtable('categories', 'sitepageevent');
    $category = $categoryTable->find($id)->current();

    // Generate and assign form
    $form = $this->view->form = new Sitepageevent_Form_Admin_Category();
    $form->setAction($this->view->url());
    $form->setField($category);
    
    // Check post
    if( !$this->getRequest()->isPost() ) {
      $this->renderScript('admin-settings/form.tpl');
      return;
    }
    
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->renderScript('admin-settings/form.tpl');
      return;
    }
    
    // Ok, we're good to add field
    $values = $form->getValues();

    $db = $categoryTable->getAdapter();
    $db->beginTransaction();

    try {
      $category->title = $values['label'];
      $category->save();
      
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh' => 10,
      'messages' => array('')
    ));
  }  
  
  //MAKE FAQ ACTION 
  public function locationsAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepageevent_admin_main', array(), 'sitepageevent_admin_main_locations');

    $eventstable = Engine_Api::_()->getDbtable('events', 'sitepageevent');
		$select = $eventstable->select()->where('location <> ?', '')->where('seao_locationid = ?', 0);
	  $this->view->row  = $row = $eventstable->fetchAll($select);
  }
  
   // ACTION FOR CHANGE SETTINGS OF TABBED EVENT WIDZET TAB
  public function editTabAction() {


 $this->view->tabs = $tabs = Engine_Api::_()->getItemTable('seaocore_tab')->getTabs(array('module' => 'sitepageevent', 'type' => 'events', 'enabled' => 1));
    //FORM GENERATION
    $this->view->form = $form = new Sitepageevent_Form_Admin_EditTab();
    $id = $this->_getParam('tab_id');

    $tab = Engine_Api::_()->getItem('seaocore_tab', $id);
    //CHECK POST
    if (!$this->getRequest()->isPost()) {
      $values = $tab->toarray();
      $form->populate($values);
      return;
    }
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $values = $form->getValues();
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $tab->setFromArray($values);
      $tab->save();
      $db->commit();
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('Edit Tab Settings Sucessfully.'))
              ));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  //ACTION FOR UPDATE ORDER  OF EVENTS WIDGTS TAB
  public function updateOrderAction() {
    //CHECK POST
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      $values = $_POST;
      try {
        foreach ($values['order'] as $key => $value) {
          $tab = Engine_Api::_()->getItem('seaocore_tab', (int) $value);
          if (!empty($tab)) {
            $tab->order = $key + 1;
            $tab->save();
          }
        }
        $db->commit();
        $this->_helper->redirector->gotoRoute(array('action' => 'widget'));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }
  }

  //ACTION FOR MAKE TAB ENABLE/DISABLE
  public function enabledAction() {
    $id = $this->_getParam('tab_id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    $tab = Engine_Api::_()->getItem('seaocore_tab', $id);
    try {
      $tab->enabled = !$tab->enabled;
      $tab->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect('admin/sitepageevent/settings/widget');
  }

  //ACTION FOR EVENT OF THE DAY
  public function manageDayItemsAction() {

		//TAB CREATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepageevent_admin_main', array(), 'sitepageevent_admin_main_dayitems');
   
    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Sitepageevent_Form_Admin_Manage_Filter();
    $page = $this->_getParam('page', 1);

    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    $values = array_merge(array(
        'order' => 'start_date',
        'order_direction' => 'DESC',
            ), $values);

    $this->view->assign($values);

    $this->view->eventOfDaysList = $eventOfDay = Engine_Api::_()->getDbtable('itemofthedays', 'sitepage')->getItemOfDayList($values, 'event_id', 'sitepageevent_event');
    $eventOfDay->setItemCountPerPage(50);
    $eventOfDay->setCurrentPageNumber($page);
  }

  //ACTION FOR ADDING EVENT OF THE DAY
  public function addEventOfDayAction() {

    //SET LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //FORM GENERATION
    $form = $this->view->form = new Sitepageevent_Form_Admin_ItemOfDayday();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    $form->setTitle('Add an Event of the Day')
            ->setDescription('Select a start date and end date below and the corresponding Event from the auto-suggest Event field. The selected Event will be displayed as "Event of the Day" for this duration and if more than one events are found to be displayed in the same duration then they will be dispalyed randomly one at a time.');
    $form->getElement('title')->setLabel('Event Name');

    //CHECK POST
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //GET FORM VALUES
      $values = $form->getValues();

      //BEGIN TRANSACTION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //GET ITEM OF THE DAY TABLE
        $dayItemTime = Engine_Api::_()->getDbtable('itemofthedays', 'sitepage');

				//FETCH RESULT FOR resource_id
        $select = $dayItemTime->select()->where('resource_id = ?', $values["resource_id"])->where('resource_type = ?', 'sitepageevent_event');
        $row = $dayItemTime->fetchRow($select);

        if (empty($row)) {
          $row = $dayItemTime->createRow();
          $row->resource_id = $values["resource_id"];
        }
        $row->start_date = $values["starttime"];
        $row->end_date = $values["endtime"];
				$row->resource_type = 'sitepageevent_event';
        $row->save();

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_('The Event of the Day has been added successfully.'))
              ));
    }
  }

   //ACTION FOR EVENT SUGGESTION DROP-DOWN
  public function getEventAction() {
    $title = $this->_getParam('text', null);
    $limit = $this->_getParam('limit', 40);
    $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $pageTableName = $pageTable->info('name');
    $allowTable = Engine_Api::_()->getDbtable('allow', 'authorization');
    $allowName = $allowTable->info('name');
    $eventTable = Engine_Api::_()->getDbtable('events', 'sitepageevent');
    $eventName = $eventTable->info('name');
    $data = array();
    $select = $eventTable->select()
													->setIntegrityCheck(false)
													->from($eventName)
                          ->join($pageTableName, $pageTableName . '.page_id = '. $eventName . '.page_id',array('title AS page_title', 'photo_id as page_photo_id'))
													->join($allowName, $allowName . '.resource_id = '. $pageTableName . '.page_id', array('resource_type','role'))
													->where($allowName.'.resource_type = ?', 'sitepage_page')
													->where($allowName.'.role = ?', 'registered')
													->where($allowName.'.action = ?', 'view')
													->where($eventName.'.search = ?', 1)
													->where($eventName.'.title  LIKE ? ', '%' . $title . '%')
                          ->where("$eventName.endtime > FROM_UNIXTIME(?)", time())
													->limit($limit)
													->order($eventName.'.creation_date DESC');
    $select = $select
              ->where($pageTableName . '.closed = ?', '0')
              ->where($pageTableName . '.approved = ?', '1')
              ->where($pageTableName . '.declined = ?', '0')
              ->where($pageTableName . '.draft = ?', '1');
    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $select->where($pageTableName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
    }
    $events = $eventTable->fetchAll($select);

    foreach ($events as $event) {
      $content_photo = $this->view->itemPhoto($event, 'thumb.normal');
      $data[] = array(
          'id' => $event->event_id,
          'label' => $event->title,
          'photo' => $content_photo
      );
    }
    return $this->_helper->json($data);
  }

  //ACTION FOR DELETE EVENT OF DAY ENTRY
  public function deleteEventOfDayAction() {
    $this->view->id = $this->_getParam('id');
    if ($this->getRequest()->isPost()) {
      Engine_Api::_()->getDbtable('itemofthedays', 'sitepage')->delete(array('itemoftheday_id =?' => $this->_getParam('id')));
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
              ));
    }
    $this->renderScript('admin-settings/delete.tpl');
  }

  //ACTION FOR MULTI DELETE EVENT ENTRIES
  public function multiDeleteEventAction() {
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {

          $sitepageitemofthedays = Engine_Api::_()->getItem('sitepage_itemofthedays', (int) $value);
          if (!empty($sitepageitemofthedays)) {
            $sitepageitemofthedays->delete();
          }
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage-day-items'));
  }

  //ACTION FOR FAQ
  public function faqAction() {
    
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitepageevent_admin_main', array(), 'sitepageevent_admin_main_faq');
  }

  public function readmeAction() {
    
  }
  
  //Sink the event location.
  public function sinkLocationAction() {
  
    //PROCESS
    set_time_limit(0);
    ini_set("max_execution_time", "300");
    ini_set("memory_limit", "256M");
    
    $seLocationsTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');

		$eventstable = Engine_Api::_()->getDbtable('events', 'sitepageevent');
		$select = $eventstable->select()->where('location <> ?', '')->where('seao_locationid = ?', 0)->limit('1500');
	  $this->view->row  = $row = $eventstable->fetchAll($select);
	  
    $this->view->error = 0;

		if ($this->getRequest()->isPost()) {

			foreach ($row as $result) {
			
				//Accrodeing to event  location entry in the seaocore location table.
				if (!empty($result['location'])) {
					$seao_locationid = $seLocationsTable->getLocationItemId($result['location'], '', 'sitepageevent_event', $result['event_id']);

					//event table entry of location id.
					$eventstable->update(array('seao_locationid'=>  $seao_locationid), array('event_id =?' => $result['event_id']));
				}
			}
			$this->view->error = 1;
		}
  }
}