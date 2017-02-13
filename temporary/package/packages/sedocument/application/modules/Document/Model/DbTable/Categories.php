<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Categories.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Document_Model_DbTable_Categories extends Engine_Db_Table
{
  protected $_rowClass = 'Document_Model_Category';

  /**
   * Return subcaregories
   *
   * @param int category_id
   * @return all sub categories
   */
  public function getSubCategories($category_id) {

		//RETURN IF CATEGORY ID IS EMPTY
    if (empty($category_id)) {
      return;
    }

		//MAKE QUERY
    $select = $this->select()
										->from($this->info('name'), array('category_name', 'category_id'))
										->where('cat_dependency = ?', $category_id)
										->order('cat_order');

		//RETURN DATA
    return $this->fetchAll($select);
  }

  /**
   * Get all user categories
   * @param int $user_id : user id
   * @return all category list belongs to user id
   */
  public function getUserCategories($user_id) {
    
		//GET DOCUMENT TABLE
    $tableDocumentName = Engine_Api::_()->getDbtable('documents', 'document')->info('name');

		//GET CATEGORY TABLE NAME
    $tableCategoryName = $this->info('name');

		//MAKE QUERY
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from($tableCategoryName, array('category_name', 'category_id'))
                    ->joinLeft($tableDocumentName, "$tableDocumentName.category_id = $tableCategoryName.category_id", array(''))
                    ->group("$tableCategoryName.category_id")
                    ->where($tableDocumentName . '.draft = ?', "0")
                    ->where($tableDocumentName . '.approved = ?', "1")
                    ->where($tableDocumentName . '.status = ?', "1")
                    ->where($tableDocumentName . '.owner_id = ?', $user_id)
										->order($tableCategoryName.'.category_name ASC');

		//RETURN RESULTS
    return $this->fetchAll($select);
  }

  /**
   * Return categories
   *
   * @param int $countDisplay
   * @return all categories
   */
  public function getCategories($countDisplay = 0, $document_owner_id = 0) {

		//GET CATEGORY TABLE NAME
    $categoryTableName = $this->info('name');

		//MAKE QUERY
    $select = $this->select()->where('cat_dependency = ?', 0);

    if (!empty($countDisplay)) {

			//GET DOCUMENT TABLE NAME
      $tableDocumentName = Engine_Api::_()->getDbtable('documents', 'document')->info('name');

			//MAKE QUERY
      $select = $select->setIntegrityCheck(false)
											 ->from($categoryTableName)
											 ->joinLeft($tableDocumentName, $tableDocumentName . '.category_id = ' . $categoryTableName . '.category_id', array('count(' . $tableDocumentName . '.category_id ) as count'))
											 ->group($categoryTableName . '.category_id');

			//CODING FOR DOCUMENT MAIN VIEW PAGE
			if(!empty($document_owner_id)) {
				$select = $select->where($tableDocumentName . '.owner_id = ?',  $document_owner_id)
													->where($tableDocumentName . '.draft = ?', 0)
													->where($tableDocumentName . '.approved = ?', 1)
													->where($tableDocumentName . '.status = ?', 1);
											
			}
    }

		$select = $select->order('cat_order ASC');

		//RETURN DATA
    return $this->fetchAll($select);
  }

  /**
   * Get category object
   * @param int $category_id : category id
   * @return category object
   */
  public function getCategory($category_id) {

		//RETURN CATEGORY OBJECT
    return $this->find($category_id)->current();
  }

  /**
   * Gets all categories and subcategories
   *
   * @param string $category_id
   * @param string $fieldname
   * @param int $documentCondition
   * @param string $document
   * @param  all categories and subcategories
   */
  public function getAllCategories($category_id, $fieldname, $documentCondition, $document, $subcat = null, $limit = 0, $network_based_content = 0) {

		//GET CATEGORY TABLE NAME
    $tableCategoriesName = $this->info('name');

		//GET DOCUMENT TABLE
    $tableDocument = Engine_Api::_()->getDbtable('documents', 'document');
    $tableDocumentName = $tableDocument->info('name');

		//MAKE QUERY
    $select = $this->select()->setIntegrityCheck(false)->from($tableCategoriesName);

    if ($subcat == 1) {
      $select = $select->joinLeft($tableDocumentName, $tableDocumentName . '.' . $fieldname . '=' . $tableCategoriesName . '.category_id', array('count(DISTINCT ' . $tableDocumentName . '.' . $document . ' ) as count'));
    } else {
      $select = $select->joinLeft($tableDocumentName, $tableDocumentName . '.' . $fieldname . '=' . $tableCategoriesName . '.category_id', array('count(' . $tableDocumentName . '.' . $fieldname . ' ) as count'));
    }

    $select = $select->where($tableCategoriesName . '.cat_dependency = ' . $category_id)
            ->group($tableCategoriesName . '.category_id')
            ->order('cat_order');

    if (!empty($limit)) {
      $select = $select->limit($limit);
    }

    if ($documentCondition == 1) {
      $select->where($tableDocumentName . '.status = ?', 1)
              ->where($tableDocumentName . '.approved = ?', 1)
              ->where($tableDocumentName . '.draft = ?', 0)
							->where($tableDocumentName . ".search = ?", 1);   
    }
    
    if($network_based_content) {
      $select = $tableDocument->getNetworkBaseSql($select); 
    }

		//RETURN DATA
    return $this->fetchAll($select);
  }

  /**
   * Return category mapping data
   *
   */
	public function categoryMappingData() {

		//GET PROFILEMAPS TABLE NAME
    $tableProfilemapsName = Engine_Api::_()->getDbtable('profilemaps', 'document')->info('name');

		//GET CATEGORY TABLE NAME
    $tableCategoryName = $this->info('name');

		//GET FIELD OPTION TABLE NAME
		$tableFieldOptionsName = Engine_Api::_()->getDbtable('options', 'document')->info('name');

		//MAKE QUERY
    $select = $this->select()
            ->setIntegrityCheck(false)
            ->from($tableCategoryName, array('category_id', 'category_name'))
            ->joinLeft($tableProfilemapsName, "$tableCategoryName.category_id = $tableProfilemapsName.category_id", array('profile_type', 'profilemap_id'))
            ->joinLeft($tableFieldOptionsName, "$tableFieldOptionsName.option_id = $tableProfilemapsName.profile_type", array('label'))
            ->where($tableCategoryName . ".cat_dependency = ?", 0);

		//RETURN DATA
    return Zend_Paginator::factory($select);
	}

  /**
   * Return slug corrosponding to category name
   *
   * @param int $categoryname
   * @return categoryname
   */
  public function getCategorySlug($categoryname) {

		//GET SETTING
    $showslug = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.categorywithslug', 1);

    if(!empty($showslug)) {
      return trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($categoryname))), '-');
    }
    else {
      return $categoryname;
    }
  }
  
    /**
     * Return categories
     *
     * @param int $home_page_display
     * @return categories
     */
    public function getCategoriesByLevel($level = null) {

        $select = $this->select()->order('cat_order');
        switch ($level) {
            case 'category':
                $select->where('cat_dependency =?', 0);
                break;
            case 'subcategory':
                $select->where('cat_dependency !=?', 0);
                $select->where('subcat_dependency =?', 0);
                break;
            case 'subsubcategory':
                $select->where('cat_dependency !=?', 0);
                $select->where('subcat_dependency !=?', 0);
                break;
        }

        return $this->fetchAll($select);
    }
}