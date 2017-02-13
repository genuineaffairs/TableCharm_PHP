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

class Document_Widget_CategorizedDocumentsController extends Engine_Content_Widget_Abstract {

  public function indexAction() {  	

		//GET PARAMETERS FOR SORTING THE RESULTS
    $current_time = date("Y-m-d H:i:s");
		$categoryCount = $this->_getParam('itemCount', 0);
		$popularity = $this->_getParam('popularity', 'views');
		$interval = $this->_getParam('interval', 'overall');
		$totalDocuments = $this->_getParam('documentCount', 5);

		//MAKE TIMING STRING
		if($interval == 'week') {
			$time_duration = date('Y-m-d H:i:s', strtotime('-7 days'));
			$sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" ;
		}
		elseif($interval == 'month') {
			$time_duration = date('Y-m-d H:i:s', strtotime('-1 months'));
			$sqlTimeStr = ".creation_date BETWEEN " . "'" . $time_duration . "'" . " AND " . "'" . $current_time . "'" . "";
		}
		else {
			$sqlTimeStr = '';
		}

		$this->view->tableCategory = $tableCategory = Engine_Api::_()->getDbtable('categories', 'document');

		//GET CATEGORIES
    $categories = array();
		if(!empty($categoryCount)) {
			$category_info = $tableCategory->getAllCategories(0, 'category_id', 1, 'document_id', 1, $categoryCount, 1);
		}
		else {
			$category_info = $tableCategory->getAllCategories(0, 'category_id', 1, 'document_id', 1, 0, 1);
		}

    foreach ($category_info as $value) {
      $category_documents_array = array();

			//GET DOCUMENT RESULTS
			$category_documents_info = $category_documents_info = Engine_Api::_()->getDbtable('documents', 'document')->documentsByCategory($value['category_id'], $popularity, $interval, $sqlTimeStr, $totalDocuments);

      foreach ($category_documents_info as $result_info) {
	
				if(!empty($result_info->photo_id)) {
					$thumb_src = $result_info->getPhotoUrl('thumb.icon');
				}
				else {
					$thumb_src = Engine_Api::_()->document()->sslThumbnail($result_info->thumbnail);
				}

        $category_documents_array[] = $tmp_array = array('document_id' => $result_info->document_id,
						'imageSrc' => $thumb_src,
            'document_title' => $result_info->document_title,
            'owner_id' => $result_info->owner_id,
						'populirityCount' => $result_info->populirityCount,
            'slug' => $result_info->getSlug());
      }

      $categories[] = $category_array = array('category_id' => $value->category_id,
          'category_name' => $value->category_name,
          'order' => $value->cat_order,
          'count' => $value->count,
          'category_documents' => $category_documents_array
      );
    }
    $this->view->categories = $categories;

    //SET NO RENDER
    if (!(count($this->view->categories) > 0)) {
      return $this->setNoRender();
    }
  }
}
?>