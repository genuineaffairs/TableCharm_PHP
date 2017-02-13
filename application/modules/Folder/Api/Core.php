<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Folder
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Folder_Api_Core extends Core_Api_Abstract
{
  const IMAGE_WIDTH = 720;
  const IMAGE_HEIGHT = 720;

  const THUMB_WIDTH = 140;
  const THUMB_HEIGHT = 160;
  
  protected $_types;
  
  public function getAvailableParentTypes()
  {
    if( null === $this->_types ) {
    	$table = $this->getFolderTable();
    	$rName = $table->info('name');
    	
      $this->_types = $this->getFolderTable()->getAdapter()
        ->query("SELECT DISTINCT `parent_type` FROM `$rName` ORDER BY `parent_type` ASC")
        ->fetchAll(Zend_Db::FETCH_COLUMN);
    }

    return $this->_types;
  }

  public function getCategoryOptions($params = array())
  {
    return $this->convertCategoriesToArray($this->getCategories($params));
  }  
  
  public function getCategories($params = array())
  {
    $table = Engine_Api::_()->getDbtable('categories', 'folder');
    $categories = $table->fetchAll($table->select()->order('order'));
    
    return $categories;
  }
  
  public function getCategory($category_id)
  {
    static $categories = array();
    
    if (!isset($categories[$category_id]))
    {
      $categories[$category_id] = Engine_Api::_()->getDbtable('categories', 'folder')->find($category_id)->current();
    }
    
    return $categories[$category_id];
  }
  


  public function getFolder($folder_id)
  {
    static $folders = array();
    
    if (!isset($folders[$folder_id]))
    {
      $folders[$folder_id] = Engine_Api::_()->getDbtable('folders', 'folder')->find($folder_id)->current();
    }
    
    return $folders[$folder_id];
  } 
  
  public function convertCategoriesToArray($categories)
  {
    return $this->convertItemsToArray($categories);
  }
  
  public function convertItemsToArray($items)
  {
    $data = array();
    foreach ($items as $item) {
      $data[$item->getIdentity()] = $item->getTitle();
    }
    return $data;
  }
  
  public function countFolders($params = array())
  {
    $paginator = $this->getFoldersPaginator($params);
    return $paginator->getTotalItemCount();  
  }
  
  // Select
  /**
   * Gets a paginator for folders
   *
   * @param Core_Model_Item_Abstract $user The user to get the messages for
   * @return Zend_Paginator
   */
  public function getFoldersPaginator($params = array(), $options = null)
  {
    $paginator = Zend_Paginator::factory($this->getFoldersSelect($params, $options));
    if( !empty($params['page']) )
    {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }

  /**
   * Gets a select object for the user's folder entries
   *
   * @param Core_Model_Item_Abstract $user The user to get the messages for
   * @return Zend_Db_Table_Select
   */
  public function getFoldersSelect($params = array(), $options = null)
  {
    $table = $this->getFolderTable();
    
    $rName = $table->info('name');

    if (empty($params['order'])) {
      $params['order'] = 'recent';
    }    
    
    $select = $table->selectParamBuilder($params);
    
    // Process options
    $tmp = array();
    foreach( $params as $k => $v ) {
      if( null == $v || '' == $v || (is_array($v) && count(array_filter($v)) == 0) ) {
        continue;
      } else if( false !== strpos($k, '_field_') ) {
        list($null, $field) = explode('_field_', $k);
        $tmp['field_' . $field] = $v;
      } else if( false !== strpos($k, '_alias_') ) {
        list($null, $alias) = explode('_alias_', $k);
        $tmp[$alias] = $v;
      } else {
        $tmp[$k] = $v;
      }
    }
    $params = $tmp;     
    
    // Build search part of query
    $searchParts = Engine_Api::_()->fields()->getSearchQuery('folder', $params);
    if (!empty($searchParts))
    {
      $searchTable = Engine_Api::_()->fields()->getTable('folder', 'search')->info('name');
      
      $select = $select
        ->setIntegrityCheck(false)
        ->from($rName)
        ->joinLeft($searchTable, "$searchTable.item_id = $rName.folder_id")
        ->group("$rName.folder_id");     
      foreach( $searchParts as $k => $v ) 
      {
        $select = $select->where("`{$searchTable}`.{$k}", $v);
      }
    }      
    
    if( !empty($params['tag']) )
    {          
      $tagTable = Engine_Api::_()->getDbtable('TagMaps', 'core')->info('name');
      
      $select = $select
        ->setIntegrityCheck(false)
        ->from($rName)
        ->joinLeft($tagTable, "$tagTable.resource_id = $rName.folder_id")
        ->where($tagTable.'.resource_type = ?', 'folder')
        ->where($tagTable.'.tag_id  IN (?)', $params['tag']);
        if (is_array($params['tag'])) {
          $select->group("$rName.folder_id");
        }
    }

   
    //echo $select->__toString();
    //exit;
    return $select;
  }

  
  public function filterEmptyParams($values)
  {
    foreach ($values as $key => $value)
    {
      if (is_array($value))
      {
        foreach ($value as $value_k => $value_v)
        {
          if (!strlen($value_v))
          {
            unset($value[$value_k]);
          }
        }
      }
      
      if (is_array($value) && count($value) == 0)
      {
        unset($values[$key]);
      }
      else if (!is_array($value) && !strlen($value))
      {
        unset($values[$key]);
      }
    }
    
    return $values;
  }
  
  public function getPopularTags($options = array())
  {
    $resource_type = 'folder';
    
    $tag_table = Engine_Api::_()->getDbtable('tags', 'core');
    $tagmap_table = $tag_table->getMapTable();
    
    $tName = $tag_table->info('name');
    $tmName = $tagmap_table->info('name');
    
    if (isset($options['order']))
    {
      $order = $options['order'];
    }
    else
    {
      $order = 'text';
    }
    
    if (isset($options['sort']))
    {
      $sort = $options['sort'];
    }
    else
    {
      $sort = $order == 'total' ? SORT_DESC : SORT_ASC;
    }
    
    $limit = isset($options['limit']) ? $options['limit'] : 50;
    
    $select = $tag_table->select()
        ->setIntegrityCheck(false)
        ->from($tmName, array('total' => "COUNT(*)"))
        ->join($tName, "$tName.tag_id = $tmName.tag_id")
        ->where($tmName.'.resource_type = ?', $resource_type)
        ->where($tmName.'.tag_type = ?', 'core_tag')
        ->group("$tName.tag_id")
        ->order("total desc")
        ->limit("$limit");

    $params = array('live' => true); 
    $folder_table = $this->getFolderTable();
    $rName = $folder_table->info('name');
    
            
    
    $select->setIntegrityCheck(false)
        ->join($rName, "$tmName.resource_id = $rName.folder_id");
    $select = $folder_table->selectParamBuilder($params, $select);    
    //echo $select;
    
    $tags = $tag_table->fetchAll($select);   
    
    $records = array();
    
    $columns = array();
    if (!empty($tags))
    {
      foreach ($tags as $k => $tag)
      {
        $records[$k] = $tag;
        $columns[$k] = $order == 'total' ? $tag->total : $tag->text; 
      }
    }

    $tags = array();
    if (count($columns))
    {
      if ($order == 'text') {
        natcasesort($columns);
      }
      else {
        arsort($columns);
      }

      foreach ($columns as $k => $name)
      {
        $tags[$k] = $records[$k];
      }
    }

    return $tags;
  }  
  
  public function getRelatedFolders($folder, $params = array())
  {
    // related folders
    $tag_ids = array();
    foreach ($folder->tags()->getTagMaps() as $tagMap) {
      $tag = $tagMap->getTag();
      if (!empty($tag->text)) {
        $tag_ids[] = $tag->tag_id;
      }
    }
    //print_r($tag_ids);
    
    if (empty($tag_ids)) {
      return null;
    }
    
    $values = array(
      'tag' => $tag_ids,
      'order' => 'random',
      'limit' => 5,
      'exclude_folder_ids' => array($folder->getIdentity())
    );

    $params = array_merge($values, $params);
    
    $paginator = Engine_Api::_()->folder()->getFoldersPaginator($params);
    
    if ($paginator->getTotalItemCount() == 0) {
      return null;
    }
    
    return $paginator;
  }  
  
  
  public function getTopSubmitters($params = array())
  {
    $column = 'user_id';
    
    $table = $this->getFolderTable();
    $rName = $table->info('name');
    
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), array(
      'user_id' => $column,
      'total' => new Zend_Db_Expr('COUNT(*)'),
    ));
    $select->group($column);

    $select->order('total desc');
    
    if (isset($params['limit'])) {
      $select->limit($params['limit']);
      unset($params['limit']);
    }
    
    $select = $table->selectParamBuilder($params, $select);
    
    $rows = $select->query()->fetchAll();
    
    $result = array();
    foreach ($rows as $row) {
      $result[$row[$column]] = $row;
    }
    
    return $result;
  }

  
  /***
   * @return Folder_Model_DbTable_Folders
   */
  public function getFolderTable()
  {
    return Engine_Api::_()->getDbtable('folders', 'folder');
  }
  

  
}