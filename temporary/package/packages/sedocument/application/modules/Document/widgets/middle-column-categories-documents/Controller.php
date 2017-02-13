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
class Document_Widget_MiddleColumnCategoriesDocumentsController extends Seaocore_Content_Widget_Abstract {

	public function indexAction() {
  
		$this->view->show3rdlevelCategory = $show3rdlevelCategory = $this->_getParam('show3rdlevelCategory', 0);
		$this->view->show2ndlevelCategory = $show2ndlevelCategory = $this->_getParam('show2ndlevelCategory', 1);
		$showAllCategories = !$this->_getParam('showAllCategories', 0);

		$this->view->tableCategory = $tableCategory = Engine_Api::_()->getDbtable('categories', 'document');

    $this->view->categories = $categories = array();
   
    $category_info = $tableCategory->getAllCategories(0, 'category_id', $showAllCategories, 'document_id', 1, 0, 1);

		foreach ($category_info as $value) {

			if(!empty($show2ndlevelCategory)) {

				$sub_cat_array = array();
				$category_info2 = $tableCategory->getAllCategories($value['category_id'], 'subcategory_id', $showAllCategories, 'subcategory_id', 0, 0, 1);

				foreach ($category_info2 as $subresults) {

					if (!empty($show3rdlevelCategory)) {

						$subcategory_info2 = $tableCategory->getAllCategories($subresults['category_id'], 'subsubcategory_id', $showAllCategories, 'subsubcategory_id', 0, 0, 1);
						$treesubarrays[$subresults->category_id] = array();
						foreach ($subcategory_info2 as $subvalues) {

							$treesubarrays[$subresults['category_id']][] = $treesubarray = array('tree_sub_cat_id' => $subvalues->category_id,
									'tree_sub_cat_name' => $subvalues->category_name,
									'count' => $subvalues->count,
									'order' => $subvalues->cat_order,
							);
						}

						$sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
								'sub_cat_name' => $subresults->category_name,
								'tree_sub_cat' => $treesubarrays[$subresults->category_id],
								'count' => $subresults->count,
								'order' => $subresults->cat_order);
					} 
					else {
						$sub_cat_array[] = $tmp_array = array('sub_cat_id' => $subresults->category_id,
								'sub_cat_name' => $subresults->category_name,
								'count' => $subresults->count,
								'order' => $subresults->cat_order);
					}

				}

				$categories[] = $category_array = array('category_id' => $value->category_id,
						'category_name' => $value->category_name,
						'order' => $value->cat_order,
						'count' => $value->count,
						'sub_categories' => $sub_cat_array
				);
		 }
		 else {
				$categories[] = $category_array = array('category_id' => $value->category_id,
						'category_name' => $value->category_name,
						'order' => $value->cat_order,
						'count' => $value->count,
				);
		 }

		}

    $this->view->categories = $categories;

    //SET NO RENDER
    if (!(count($this->view->categories) > 0)) {
      return $this->setNoRender();
    }
  }
}
