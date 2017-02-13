<?php

class Ynevent_AdminManageController extends Core_Controller_Action_Admin {

    public function indexAction() {
    	$viewer = Engine_Api::_()->user()->getViewer();
        $table = Engine_Api::_()->getDbTable("remind", "ynevent");
        $reminds = $table->getRemindEvents($viewer->getIdentity());
        if (count($reminds)) {
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

            foreach ($reminds as $event) {
               
                $date = $this->view->locale()->toDateTime($event->starttime);
                $params = array("label" => $date);
                $notifyApi->addNotification($viewer, $viewer, $event, 'ynevent_remind', $params);
                //set remind is read
                $remind = $table->getRemindRow($event->event_id, $viewer->getIdentity());
                $remind->is_read = 1;
                $remind->save();
            }
        }
		
        $this->view->headLink()->appendStylesheet($this->view->baseUrl() . '/application/modules/Ynevent/externals/styles/main.css');

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('ynevent_admin_main', array(), 'ynevent_admin_main_manage');

        $this->view->form = $form = new Ynevent_Form_Admin_Manage_Events();

        $values = array();
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    $event = Engine_Api::_()->getItem('event', $value);
                    $event->delete();
                }
            }
        }

        $page = $this->_getParam('page', 1);
		$values['isAdmin'] = 1;
        $this->view->paginator = Engine_Api::_()->getItemTable('event')->getEventPaginator($values);
        $this->view->paginator->setItemCountPerPage(25);
        $this->view->paginator->setCurrentPageNumber($page);
        if (!isset($values['order'])) {
            $values['order'] = "engine4_event_events.starttime";
        }

        if (!isset($values['direction'])) {
            $values['direction'] = "asc";
        }
        $this->view->formValues = $values;
    }

    public function deleteAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->event_id = $id;
        // Check post
        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $event = Engine_Api::_()->getItem('event', $id);
                $event->delete();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
            ));
        }
        // Output
        $this->renderScript('admin-manage/delete.tpl');
    }
	
	public function featuredAction(){
	 //Get params
      $event_id = $this->_getParam('event_id'); 
      $status = $this->_getParam('status'); 
		
      //Get contest need to set featured
      $table = Engine_Api::_()->getItemTable('ynevent_event');
      $select = $table->select()->where("event_id = ?",$event_id); 
    	
	  $event = $table->fetchRow($select);
	  $event->featured = $status ;		
      $event->save();
	  
      }
}