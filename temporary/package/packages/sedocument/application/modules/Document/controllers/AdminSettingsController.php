<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
include_once APPLICATION_PATH . '/application/modules/Document/Api/Scribd.php';
class Document_AdminSettingsController extends Core_Controller_Action_Admin
{
	//ACTION FOR GLOBAL SETTINGS
  public function indexAction()
  {
		if( !empty($_POST['document_controllersettings']) ) { $_POST['document_controllersettings'] = trim($_POST['document_controllersettings']); }

		if( isset($_POST['document_api_key']) && !empty($_POST['document_api_key']) ) {
			$_POST['document_api_key'] = trim($_POST['document_api_key']);
		}

		if( isset($_POST['document_secret_key']) && !empty($_POST['document_secret_key']) ) {
			$_POST['document_secret_key'] = trim($_POST['document_secret_key']);
		}

		$document_form_element = array( 'document_default_visibility', 'document_visibility_option', 'document_bbcode', 'document_html', 'document_rating', 'document_report', 'document_share', 'document_button_share', 'document_licensing_scribd', 'document_licensing_option', 'document_include_full_text', 'document_save_local_server', 'document_page', 'document_title_truncation', 'submit', 'document_categorywithslug', 'document_visitor_fulltext', 'document_show_editor', 'document_manifestUrlP', 'document_manifestUrlS', 'document_viewer', 'document_fullscreen_button', 'document_fullscreen_type', 'document_flash_mode', 'document_disable_button','document_code_share', 'document_thumbs', 'document_network', 'document_default_show', 'document_networks_type');
		$this->view->isModsSupport = Engine_Api::_()->document()->isModulesSupport();
		include_once(APPLICATION_PATH ."/application/modules/Document/controllers/license/license1.php");
	}

	//ACTION FOR CHECK THE SCRIBD DETAIL
	public function checkscribd($api_key, $secreat_key)
	{
		$this->scribd = new Scribd($api_key, $secreat_key);
		try {
			$result = $this->scribd->getList();
			return;
		}
		catch(Exception $e) {
			$is_error = 1;
			$code = $e->getCode();
			if ($code == 401) {
				$message = $e->getMessage();				
				$error = $message.$this->view->translate(': Api key is not correct');
				return $error;
			}
		}
	}

	//ACTION FOR LEVEL SETTINGS
  public function levelAction()
  {
  	//MAKE NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('document_admin_main', array(), 'document_admin_main_level');

    //FETCH LEVEL ID 
    if( null !== ($level_id = $this->_getParam('id')) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $level_id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception($this->view->translate('missing level'));
    }

		//GET LEVEL ID
    $level_id = $level->level_id;

    //GENERATE FORM
    $this->view->form = $form = new Document_Form_Admin_Settings_Level(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));

		if(!empty($level_id)) {
			$form->level_id->setValue($level_id);
		}

    //GET AUTHORIZATION
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

    if( !$this->getRequest()->isPost() ) {
      $form->populate($permissionsTable->getAllowed('document', $level_id, array_keys($form->getValues())));
      return;
    }
    
		//FORM VALIDATION
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    //GET POSTED VALUE
    $values = $form->getValues();
		
		$option_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.visibility.option', 1);
		$visibility = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.default.visibility', 'private');
		if($visibility == 'public' && empty($option_show)) {
			unset($values['visibility_hide_options']);
		}

    //CHECK FOR FILESIZE
		if($level->type != 'public') {
			if($values['filesize'] < 0) {
				$values['filesize'] = (int)ini_get('upload_max_filesize')*1024*1024;
			}
		}

		//BEGIN TRANSCATION
    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();

    try {
			 $permissionsTable->setAllowed('document', $level_id, $values);
       $db->commit();
    }
    catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
  }

  //ACTION FOR GETTING THE CATGEORIES, SUBCATEGORIES AND 3RD LEVEL CATEGORIES
  public function categoriesAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('document_admin_main', array(), 'document_admin_main_categories');

    //GET TASK
    if (isset($_POST['task'])) {
      $task = $_POST['task'];
    } elseif (isset($_GET['task'])) {
      $task = $_GET['task'];
    } else {
      $task = "main";
    }

    //GET CATEGORIES TABLE
    $tableCategory = Engine_Api::_()->getDbTable('categories', 'document');
    $tableCategoryName = $tableCategory->info('name');

    //GET DOCUMENT TABLE
    $tableDocument = Engine_Api::_()->getDbtable('documents', 'document');

    if ($task == "savecat") {

      //GET CATEGORY ID
      $category_id = $_GET['cat_id'];

      $cat_title_withoutparse = $_GET['cat_title'];

      //GET CATEGORY TITLE
      $cat_title = Engine_Api::_()->document()->parseString($_GET['cat_title']);

      //GET CATEGORY DEPENDANCY
      $cat_dependency = $_GET['cat_dependency'];
      $subcat_dependency = $_GET['subcat_dependency'];
      if ($cat_title == "") {
        if ($category_id != "new") {
          if ($cat_dependency == 0) {
            $row_ids = $tableCategory->getSubCategories($category_id);
            foreach ($row_ids as $values) {
              $tableCategory->delete(array('subcat_dependency = ?' => $values->category_id, 'cat_dependency = ?' => $values->category_id));
              $tableCategory->delete(array('category_id = ?' => $values->category_id));
            }

						//SELECT DOCUMENTS WHICH HAVE THIS CATEGORY
						$rows = $tableDocument->getCategoryDocument($category_id);

						if (!empty($rows)) {
							foreach ($rows as $key => $document_ids) {
								$document_id = $document_ids['document_id'];

								//DELETE ALL MAPPING VALUES FROM FIELD TABLES
								Engine_Api::_()->fields()->getTable('document', 'values')->delete(array('item_id = ?' => $document_id));
								Engine_Api::_()->fields()->getTable('document', 'search')->delete(array('item_id = ?' => $document_id));

								//UPDATE THE PROFILE TYPE OF ALREADY CREATED DOCUMENTS
								$tableDocument->update(array('profile_type' => 0), array('document_id = ?' => $document_id));
							}
						}

            $tableDocument->update(array('category_id' => 0, 'subcategory_id' => 0, 'subsubcategory_id' => 0), array('category_id = ?' => $category_id));
            $tableCategory->delete(array('category_id = ?' => $category_id));

          } else {
            $tableCategory->update(array('category_name' => $cat_title), array('category_id = ?' => $category_id, 'cat_dependency = ?' => $cat_dependency));

						//SELECT DOCUMENTS WHICH HAVE THIS CATEGORY
						$rows = $tableDocument->getCategoryDocument($category_id);

						if (!empty($rows)) {
							foreach ($rows as $key => $document_ids) {
								$document_id = $document_ids['document_id'];

								//DELETE ALL MAPPING VALUES FROM FIELD TABLES
								Engine_Api::_()->fields()->getTable('document', 'values')->delete(array('item_id = ?' => $document_id));
								Engine_Api::_()->fields()->getTable('document', 'search')->delete(array('item_id = ?' => $document_id));

								//UPDATE THE PROFILE TYPE OF ALREADY CREATED DOCUMENTS
								$tableDocument->update(array('profile_type' => 0), array('document_id = ?' => $document_id));
							}
						}

						$tableDocument->update(array('subcategory_id' => 0, 'subsubcategory_id' => 0), array('subcategory_id = ?' => $category_id));
						$tableDocument->update(array('subsubcategory_id' => 0), array('subsubcategory_id = ?' => $category_id));

            $tableCategory->delete(array('cat_dependency = ?' => $category_id, 'subcat_dependency = ?' => $category_id));
            $tableCategory->delete(array('category_id = ?' => $category_id));

          }
        }
        //SEND AJAX CONFIRMATION
        echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
        echo "window.parent.removecat('$category_id');";
        echo "</script></head><body></body></html>";
        exit();
      } else {
        if ($category_id == 'new') {
          $row_info = $tableCategory->fetchRow($tableCategory->select()->from($tableCategoryName, 'max(cat_order) AS cat_order'));
          $cat_order = $row_info['cat_order'] + 1;
          $row = $tableCategory->createRow();
          $row->category_name = $cat_title_withoutparse;
          $row->cat_order = $cat_order;
          $row->cat_dependency = $cat_dependency;
          $row->subcat_dependency = $subcat_dependency;
          $newcat_id = $row->save();
        } else {
          $tableCategory->update(array('category_name' => $cat_title_withoutparse), array('category_id = ?' => $category_id));
          $newcat_id = $category_id;
        }

        //SEND AJAX CONFIRMATION
        echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=UTF-8'><script type='text/javascript'>";
        echo "window.parent.savecat_result('$category_id', '$newcat_id', '$cat_title', '$cat_dependency', '$subcat_dependency');";
        echo "</script></head><body></body></html>";
        exit();
      }
    } elseif ($task == "changeorder") {
      $divId = $_GET['divId'];
      $documentOrder = explode(",", $_GET['documentorder']);
      //RESORT CATEGORIES
      if ($divId == "categories") {
        for ($i = 0; $i < count($documentOrder); $i++) {
          $category_id = substr($documentOrder[$i], 4);
          $tableCategory->update(array('cat_order' => $i + 1), array('category_id = ?' => $category_id));
        }
      } elseif (substr($divId, 0, 7) == "subcats") {
        for ($i = 0; $i < count($documentOrder); $i++) {
          $category_id = substr($documentOrder[$i], 4);
          $tableCategory->update(array('cat_order' => $i + 1), array('category_id = ?' => $category_id));
        }
      } elseif (substr($divId, 0, 11) == "treesubcats") {
        for ($i = 0; $i < count($documentOrder); $i++) {
          $category_id = substr($documentOrder[$i], 4);
          $tableCategory->update(array('cat_order' => $i + 1), array('category_id = ?' => $category_id));
        }
      }
    }

    $categories = array();
    $category_info = $tableCategory->getCategories(1, 0);
    foreach ($category_info as $value) {
      $sub_cat_array = array();
      $subcategories = $tableCategory->getAllCategories($value->category_id, 'subcategory_id', 0, 'subcategory_id', 0, 0);
      foreach ($subcategories as $subresults) {
        $subsubcategories = $tableCategory->getAllCategories($subresults->category_id, 'subsubcategory_id', 0, 'subsubcategory_id', 0, 0);
        $treesubarrays[$subresults->category_id] = array();
        foreach ($subsubcategories as $subsubcategoriesvalues) {
          $treesubarrays[$subresults->category_id][] = $treesubarray = array('tree_sub_cat_id' => $subsubcategoriesvalues->category_id,
              'tree_sub_cat_name' => $subsubcategoriesvalues->category_name,
              'order' => $subsubcategoriesvalues->cat_order,
              'count' => $subsubcategoriesvalues->count,);
        }

         $sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
            'sub_cat_name' => $subresults->category_name,
            'tree_sub_cat' => $treesubarrays[$subresults->category_id],
            'count' => $subresults->count,
            'order' => $subresults->cat_order);
      }

      $categories[] = $category_array = array('category_id' => $value->category_id,
          'category_name' => $value->category_name,
          'order' => $value->cat_order,
          'count' => $value->count,
          'sub_categories' => $sub_cat_array);
    }

		$this->view->categories = $categories;
  }

  //ACTINO FOR SEARCH FORM TAB
  public function formSearchAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('document_admin_main', array(), 'document_admin_main_form_search');

		//GET SEARCH TABLE
    $tableSearchForm = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore');

    //CHECK POST
    if ($this->getRequest()->isPost()) {
		
			//BEGIN TRANSCATION
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      $values = $_POST;
      $rowCategory = $tableSearchForm->getFieldsOptions('document', 'category_id');
      $defaultCategory = 0;
			$defaultAddition = 0;
      try {
        foreach ($values['order'] as $key => $value) {
          $tableSearchForm->update(array('order' =>  $defaultAddition + $defaultCategory + $key + 1), array('module = ?' => 'document', 'searchformsetting_id = ?' => (int) $value));

          if (!empty($rowCategory) && $value == $rowCategory->searchformsetting_id) {
            $defaultCategory = 1;
						$defaultAddition = 10000000;
					}
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
    }

		//MAKE QUERY
		$select = $tableSearchForm->select()->where('module = ?', 'document')->order('order');

		//SEND DATA TO TPL
    $this->view->searchForm = $tableSearchForm->fetchAll($select);
  }

  //ACTION FOR DISPLAY/HIDE FIELDS OF SEARCH FORM
  public function diplayFormAction() {
  	
    $field_id = $this->_getParam('id');
    $display = $this->_getParam('display');
    if (!empty($field_id)) {
      Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->update(array('display' => $display), array('module = ?' => 'document', 'searchformsetting_id =?' => (int) $field_id));
    }
    $this->_redirect('admin/document/settings/form-search');
  }

	//ACTION FOR FAQ SECTION
  public function faqAction()
  {
		//GET NAVIGATION
  	$this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      	 ->getNavigation('document_admin_main', array(), 'document_admin_main_faq');

		$this->view->show = $this->_getParam('show');
  }

	//ACTION WHICH CALLS ONLY AT THE TIME OF PLUGIN INSTALLATION
  public function readmeAction() { }
}
