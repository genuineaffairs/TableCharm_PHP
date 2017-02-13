<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminWidgetsController.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_AdminWidgetsController extends Core_Controller_Action_Admin {

  protected $_createWidgetMsg;

  // Function: 'Manage Block Tab' Show the listing of widget settings which set by admin.
  public function manageAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('communityad_admin_main', array(), 'communityad_admin_widget_setting');
    
   
  }



  // Function: Ad-report from report admin tab.
  public function adreportsAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('communityad_admin_main', array(), 'communityad_admin_adreports');
    $sortingColumnName = $this->_getParam('idSorting', 0);
    $page = $this->_getParam('page', 1); // Page id: Controll pagination.
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      if (!empty($values)) {
        foreach ($values as $key => $value) {
          if ($key == 'delete_' . $value) {
            Engine_Api::_()->getDbtable('adcancels', 'communityad')->update(array('is_cancel' => 1), array('adcancel_id =?' => $value));
          }
        }
      }
    }
    $this->view->success_message = $successMessages = $this->_getParam('form_status'); // Status that success message would be show or not.
    $ad_cancel_table = Engine_Api::_()->getItemTable('communityad_adcancel');
    $ad_cancel_name = $ad_cancel_table->info('name');
    $userTable = Engine_Api::_()->getItemTable('user');
    $userName = $userTable->info('name');
    $useradTable = Engine_Api::_()->getItemTable('userads');
    $useradName = $useradTable->info('name');

    $ad_cancel_select = $ad_cancel_table->select()
            ->setIntegrityCheck(false)
            ->from($userName, array('displayname'))
            ->join($ad_cancel_name, "$userName.user_id = $ad_cancel_name.user_id")
            ->join($useradName, "$useradName.userad_id = $ad_cancel_name.ad_id", array('cads_title'))
            ->where($ad_cancel_name . '.is_cancel =?', 0);
    if (empty($sortingColumnName)) {
      $ad_cancel_select->order($ad_cancel_name . '.adcancel_id DESC');
      $this->view->id_orderby = 0;
    } else {
      $ad_cancel_select->order($ad_cancel_name . '.adcancel_id ASC');
      $this->view->id_orderby = 1;
    }		
    $this->view->paginator = Zend_Paginator::factory($ad_cancel_select);
    $this->view->paginator->setItemCountPerPage(10);
    $this->view->paginator->setCurrentPageNumber($page);
  }

  public function reportdiscriptionAction() {
    $reportsId = $this->_getParam('reportId');
    $this->view->ad_cancel_dis = Engine_Api::_()->getItem('communityad_adcancel', $reportsId)->report_description;
  }

  
  public function moduleDeleteAction() {
    $pagesetting_id = $this->_getParam('module_id');
    $this->view->pagesetting_id = $pagesetting_id;
    // Check post
    if ($this->getRequest()->isPost()) {
      $pagesettingTable = Engine_Api::_()->getItem('communityad_module', $pagesetting_id)->delete();
      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => 10,
              'parentRefresh' => 10,
              'messages' => array($this->view->translate('Module settings deleted Successsfully.'))
      ));
    }
  }

  public function deletereportsAction() {
    $pagesetting_id = $this->_getParam('reportId');
    $this->view->pagesetting_id = $pagesetting_id;
    // Check post
    if ($this->getRequest()->isPost()) {
      Engine_Api::_()->getDbtable('adcancels', 'communityad')->update(array('is_cancel' => 1), array('adcancel_id =?' => $pagesetting_id));
      $this->_forward('success', 'utility', 'core', array(
              'smoothboxClose' => 10,
              'parentRefresh' => 10,
              'messages' => array($this->view->translate('Report deleted Successsfully.'))
      ));
    }
  }

//  public function noneWidgetCodeAction() {
//    $adWidgetId = $this->_getParam('adWidgetId');
//    $this->view->none_widget_code_div = $widgetCode = Engine_Api::_()->getDbtable('pagesettings', 'communityad')->widgetCode($adWidgetId, 1);
//    $this->view->none_widget_code_nodiv = $widgetCode = Engine_Api::_()->getDbtable('pagesettings', 'communityad')->widgetCode($adWidgetId, 0);
//  }

  public function adCreateAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('communityad_admin_main', array(), 'communityad_admin_module_manage');

    $this->view->form = $form = new Communityad_Form_Admin_Adcreate();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      if (!empty($values)) {
        $serializedArray = serialize($values['auth_module']);
        Engine_Api::_()->getApi('settings', 'core')->setSetting('communityad_adcreate', $serializedArray);
      }
    }
  }

  public function admoduleManageAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('communityad_admin_main', array(), 'communityad_admin_admodule_manage');
    $this->view->enabled_modules_array = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
    $page = $this->_getParam('page', 1); // Page id: Controll pagination.
    $pagesettingsTable = Engine_Api::_()->getItemTable('communityad_module');
    $pagesettingsTableName = $pagesettingsTable->info('name');
    $pagesettingsSelect = $pagesettingsTable->select();
    $this->view->paginator = Zend_Paginator::factory($pagesettingsSelect);
    $this->view->paginator->setItemCountPerPage(20);
    $this->view->paginator->setCurrentPageNumber($page);
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $obj = Engine_Api::_()->getItem('communityad_module', $value);
          if (empty($obj->is_delete)) {
            $obj->delete();
          }
        }
      }
    }
  }

  // Function: Manage Module - Creation Tab.
  public function admoduleCreateAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('communityad_admin_main', array(), 'communityad_admin_admodule_manage');
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $module_table = Engine_Api::_()->getDbTable('modules', 'communityad');
    $module_name = $module_table->info('name');
    $this->view->form = $form = new Communityad_Form_Admin_Module();
		$this->view->modules_id = $this->_getParam('module_id', 0);
		$this->view->module_form_count = count($form->admodule_name->options);
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      //$values = $this->getRequest()->getPost();
			$values = $form->getValues();
      if (!empty($values)) {
				$itemType = '';
//				$displayable = array();
//				if( !empty($values['displayable']) ) {
//					$displayable = $values['displayable'];
//					$displayable = array_sum($displayable);
//				}
        $moduleName = $values['admodule_name'];
        $moduleTitle = $values['addbtable_title'];
        $itemType = $values['adtable_name'];
        if( strstr($itemType, "sitereview") ) {
          $itemType = "sitereview_listing";
        }
        $field_title = $values['adtable_title'];
        $field_body = $values['adtable_body'];
        //$field_image = $values['adtable_photo'];
        $field_owner = $values['adtable_owner'];
				if( !empty($itemType) ) {
					$hasItemType = Engine_Api::_()->hasItemType( $itemType );
				}
        if (!empty($hasItemType)) {
          $table_name = Engine_Api::_()->getItemTable($itemType)->info('name');

          // Condition: Check owner field is available or not in given table.
          if (!empty($field_owner)) {
            $is_owner = $db->query("SHOW COLUMNS FROM " . $table_name . " LIKE '" . $field_owner . "'")->fetch();
            if (empty($is_owner)) {
              $error_owner = $this->view->translate('Please check the Content Owner Field. A field matching the one specified by you could not be found in the database table.');
            }
          } else {
            $is_owner = 1;
          }

          // Condition: Check title field is available or not in given table.
          if (!empty($field_title)) {
            $is_title = $db->query("SHOW COLUMNS FROM " . $table_name . " LIKE '" . $field_title . "'")->fetch();
            if (empty($is_title)) {
              $error_title = $this->view->translate('Please check the Content Title Field. A field matching the one specified by you could not be found in the database table.');
            }
          } else {
            $is_title = 1;
          }

          // Condition: Check body field is available or not in given table.
          if (!empty($field_body)) {
            $is_body = $db->query("SHOW COLUMNS FROM " . $table_name . " LIKE '" . $field_body . "'")->fetch();
            if (empty($is_body)) {
              $error_body = $this->view->translate('Please check the Content Body Field. A field matching the one specified by you could not be found in the database table.');
            }
          } else {
            $is_body = 1;
          }

          // Condition: Check image field is available or not in given table.
          if (!empty($field_image)) {
            $is_image = $db->query("SHOW COLUMNS FROM " . $table_name . " LIKE '" . $field_image . "'")->fetch();
            if (empty($is_image)) {
              $error_image = $this->view->translate('Please check the Content Image Field. A field matching the one specified by you could not be found in the database table. If this content does not have an image field, then leave this blank. Ad creators will be able to upload their own image.');
            }
          } else {
            $is_image = 1;
          }
          $itemType = $values['adtable_name'];
					include_once(APPLICATION_PATH ."/application/modules/Communityad/controllers/license/license2.php");

        } else {
          $itemError = $this->view->translate('Please enter a correct database table item.');
        }
      }
    }
    // Show the error.
    if (!empty($itemError) || !empty($error_title) || !empty($error_body) || !empty($error_image) || !empty($error_owner)) {
      $this->view->status = false;

      if (!empty($error_owner)) {
        $error_owner = Zend_Registry::get('Zend_Translate')->_($error_owner);
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error_owner);
      }
      if (!empty($itemError)) {
        $itemError = Zend_Registry::get('Zend_Translate')->_($itemError);
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($itemError);
      }
      if (!empty($error_title)) {
        $error_title = Zend_Registry::get('Zend_Translate')->_($error_title);
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error_title);
      }
      if (!empty($error_body)) {
        $error_body = Zend_Registry::get('Zend_Translate')->_($error_body);
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error_body);
      }
      if (!empty($error_image)) {
        $error_image = Zend_Registry::get('Zend_Translate')->_($error_image);
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error_image);
      }
      return;
    }
  }

  // Function: Calling from ( Ajax ): When admin select any module from drop down from 'manage modules tab' then return the table name.
  public function admodulesinfoAction() {
    $admodule = $this->_getParam('module_name');
    $table_name = 0;
    $title_field = 0;
    $owner_field = 0;
    $body_field = 0;
    //$image_field = 0;
    $module_table = Engine_Api::_()->getItemTable('communityad_module');
    $module_name = $module_table->info('name');
    $select = $module_table->select()
            ->from($module_name, array('table_name', 'title_field', 'body_field', 'owner_field'))
            ->where('module_name =?', $admodule);
    $admodules = $select->query()->fetchAll();
    if (!empty($admodules)) {
      $table_name = $admodules[0]['table_name'];
      $title_field = $admodules[0]['title_field'];
      $body_field = $admodules[0]['body_field'];
      $owner_field = $admodules[0]['owner_field'];
    }

    $this->view->table_name = $table_name;
    $this->view->title_field = $title_field;
    $this->view->body_field = $body_field;
    $this->view->owner_field = $owner_field;
  }

	public function footermsgAction(){ }

}