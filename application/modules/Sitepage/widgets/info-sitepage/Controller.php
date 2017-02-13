<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_InfoSitepageController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF NOT AUTHORIZED
    $sitepage_info = Zend_Registry::isRegistered('sitepage_info') ? Zend_Registry::get('sitepage_info') : null;
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    //GET SUBJECT
    $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    
		//SEND DATA TO TPL
    $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
    $this->view->widgets = $widgets = Engine_Api::_()->sitepage()->getwidget($layout, $sitepage->page_id);
    $this->view->content_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.info-sitepage', $sitepage->page_id, $layout);
    $this->view->module_tabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    $this->view->identity_temp = $this->view->identity;
    $this->view->showtoptitle = Engine_Api::_()->sitepage()->showtoptitle($layout, $sitepage->page_id);
    $tableCategories = Engine_Api::_()->getDbTable('categories', 'sitepage');
    $this->view->category_name = $this->view->subcategory_name == $this->view->subsubcategory_name = '';
    if($sitepage->category_id) {
        $categoriesNmae = $tableCategories->getCategory($sitepage->category_id);
        if (!empty($categoriesNmae->category_name)) {
          $this->view->category_name = $categoriesNmae->category_name;
        }
        
        if($sitepage->subcategory_id) {
            $subcategory_name = $tableCategories->getCategory($sitepage->subcategory_id);
            if (!empty($subcategory_name->category_name)) {
              $this->view->subcategory_name = $subcategory_name->category_name;
            }
            
            //GET SUB-SUB-CATEGORY
            if($sitepage->subsubcategory_id) {
                $subsubcategory_name = $tableCategories->getCategory($sitepage->subsubcategory_id);
                if (!empty($subsubcategory_name->category_name)) {
                  $this->view->subsubcategory_name = $subsubcategory_name->category_name;
                }
            }
        }
    }
    
    //GET TAGS
    $this->view->sitepageTags = $sitepage->tags()->getTagMaps();

		//CUSTOM FIELD WORK
    $this->view->sitepage_description = Zend_Registry::isRegistered('sitepage_descriptions') ? Zend_Registry::get('sitepage_descriptions') : null;
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitepage/View/Helper', 'Sitepage_View_Helper');
    $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitepage);
    if (empty($sitepage_info)) {
      return $this->setNoRender();
    }
  }
}

?>