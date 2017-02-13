<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Categories.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Model_DbTable_Categories extends Engine_Db_Table {

  protected $_rowClass = 'Sitepage_Model_Category';
  protected $_categories = array();

  /**
   * Return subcaregories
   *
   * @param int category_id
   * @return all sub categories
   */
  public function getSubCategories($category_id) {
    if (empty($category_id)) {
      return;
    }
    $select = $this->select()->from($this->info('name'), array('category_name', 'category_id', 'cat_order', 'cat_dependency'))->where('cat_dependency = ?', $category_id)->order('cat_order');
    return $this->fetchAll($select);
  }

  /**
   * Return category name for the page
   *
   * @param int category_id
   * @return Zend_Db_Table_Select
   */
  public function getCategory($category_id) {
      
    if (empty($category_id)) {
      return;
    }
    
    if (!array_key_exists($category_id, $this->_categories)) {
      $this->_categories[$category_id] = $this->find($category_id)->current();
    }
    return $this->_categories[$category_id];    
//    
//    $select = $this->select()->from($this->info('name'), array('category_name', 'category_id'))->where('category_id =?', $category_id)->order('cat_order');
//    return $this->fetchRow($select);
  }

  /**
   * Return categories
   *
   * @param int $home_page_display
   * @return categories
   */
  public function getCategories($home_page_display=0) {
    $cateName = $this->info('name');
    $select = $this->select()->where('cat_dependency =?', 0)->order('cat_order');
    if (!empty($home_page_display)) {
      $table = Engine_Api::_()->getDbtable('pages', 'sitepage');
      $rName = $table->info('name');
      $select->setIntegrityCheck(false)
              ->from($cateName)
              ->joinLeft($rName, $rName . '.category_id = ' . $cateName . '.category_id', array('count(' . $rName . '.category_id ) as count'))
              ->group($cateName . '.category_id')
              ->order('cat_order ASC');
    }
    return $this->fetchAll($select);
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

  /**
   * Return slug
   *
   * @param int $categoryname
   * @return categoryname
   */
  public function getCategorySlug($categoryname) {
    //$showslug = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.categorywithslug', 1);
    $slug = $categoryname;
//     if (!empty($showslug)) {
// 			setlocale(LC_CTYPE, 'pl_PL.utf8');
// 			$slug = @iconv('UTF-8', 'ASCII//TRANSLIT', $slug);
// 			$slug = strtolower($slug);
// 			$slug = strtr($slug, array('&' => '-', '"' => '-', '&' . '#039;' => '-', '<' => '-', '>' => '-', '\'' => '-'));
// 			$slug = preg_replace('/^[^a-z0-9]{0,}(.*?)[^a-z0-9]{0,}$/si', '\\1', $slug);
// 			$slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
// 			$slug = preg_replace('/[\-]{2,}/', '-', $slug);
//     } 
    return Engine_Api::_()->seaocore()->getSlug($slug, 225);
  }

  /**
   * Gets all categories and subcategories
   *
   * @param string $category_id
   * @param string $fieldname
   * @param int $pageCondition
   * @param string $page
   * @param  all categories and subcategories
   */
  public function getAllCategories($category_id, $fieldname, $pageCondition, $page, $subcat = null, $limit = 0) {
    $tableCategoriesName = $this->info('name');
    $tablePage = Engine_Api::_()->getDbtable('pages', 'sitepage');
    $tablePageName = $tablePage->info('name');
    $select = $this->select()->setIntegrityCheck(false)
            ->from($tableCategoriesName);
    if ($subcat == 1) {
      $select = $select->joinLeft($tablePageName, $tablePageName . '.' . $fieldname . '=' . $tableCategoriesName . '.category_id', array('count(DISTINCT ' . $tablePageName . '.' . $page . ' ) as count'));
    } else {
      $select = $select->joinLeft($tablePageName, $tablePageName . '.' . $fieldname . '=' . $tableCategoriesName . '.category_id', array('count(DISTINCT ' . $tablePageName . '.page_id ) as count'));
      // $select = $select->joinLeft($tablePageName, $tablePageName . '.' . $fieldname . '=' . $tableCategoriesName . '.category_id', array('count( ' . $tablePageName . '.' . $fieldname . ' ) as count'));
    }

    $select = $select->where($tableCategoriesName . '.cat_dependency = ' . $category_id)
            ->group($tableCategoriesName . '.category_id')
            ->order('cat_order');

    if (!empty($limit)) {
      $select = $select->limit($limit);
    }
    
    if ($pageCondition == 1) {
      $select->where($tablePageName . '.closed = ?', '0')
              ->where($tablePageName . '.approved = ?', '1')
              ->where($tablePageName . '.draft = ?', '1');
      $select->where($tablePageName . ".search = ?", 1);
      if (Engine_Api::_()->sitepage()->hasPackageEnable())
        $select->where($tablePageName . '.expiration_date  > ?', date("Y-m-d H:i:s"));
      //START NETWORK WORK
      // if ($subcat == 1) {
      $select = $tablePage->getNetworkBaseSql($select, array('not_groupBy' => 1));
      // }
      //END NETWORK WORK
    }

    return $this->fetchAll($select);
  }

  public function getCatDependancyArray() {

    $cat_dependency = $this->select()->from($this->info('name'), 'cat_dependency')->where('cat_dependency <>?', 0)->group('cat_dependency')->query()->fetchAll(Zend_Db::FETCH_COLUMN);

    return $cat_dependency;
  }

  public function getSubCatDependancyArray() {

    $subcat_dependency = $this->select()->from($this->info('name'), 'subcat_dependency')->where('subcat_dependency <>?', 0)->group('subcat_dependency')->query()->fetchAll(Zend_Db::FETCH_COLUMN);

    return $subcat_dependency;
  }
}

?>