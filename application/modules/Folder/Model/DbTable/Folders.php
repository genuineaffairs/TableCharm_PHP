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
 
 
 
class Folder_Model_DbTable_Folders extends Engine_Db_Table
{
  protected $_rowClass = "Folder_Model_Folder";

  
  public function selectParamBuilder($params = array(), $select = null)
  {
    $rName = $this->info('name');
    
    if ($select === null)
    {
      $select = $this->select();
    }
    
    if (isset($params['user']) && $params['user']) 
    {
      $user = Engine_Api::_()->user()->getUser($params['user']);
      $select->where($rName.'.user_id = ?', $user->getIdentity());
    }

    foreach (array('category') as $field)
    {
      if (isset($params[$field]) && $params[$field])
      {
        $field_id = ($params[$field] instanceof Core_Model_Item_Abstract) ? $params[$field]->getIdentity() : $params[$field];
        $select->where($rName.'.'.$field.'_id = ?', $field_id);
      }
    }
     // , 'parent_type', 'parent_id'
     
    if (isset($params['parent']))
    {
      if ($params['parent'] instanceof Core_Model_Item_Abstract)
      {
        $params['parent_type'] = $params['parent']->getType();
        $params['parent_id'] = $params['parent']->getIdentity();
      }
      else 
      {
        $guid = $params['parent'];
        $guid = explode('_', $guid);
        if( count($guid) > 2 )
        {
          $id = array_pop($guid);
          $guid = array(join('_', $guid), $id);
        }
        $params['parent_type'] = isset($guid[0]) ? $guid[0] : '';
        $params['parent_id'] = isset($guid[1]) ? $guid[1] : '';
      }
    }
    
    foreach (array('parent_type', 'parent_id') as $field)
    {
      if (isset($params[$field]) && strlen($params[$field]))
      {
        $select->where($rName.".$field = ?", $params[$field]);
      }
    }
    
    foreach (array('featured', 'sponsored', 'search') as $field)
    {
      if (isset($params[$field]))
      {
        $select->where($rName.".$field = ?", $params[$field] ? 1 : 0);
      }  
    }
    
    if( !empty($params['keyword']) )
    {
      $select->where($rName.".title LIKE ? OR ".$rName.".description LIKE ? OR ".$rName.".keywords LIKE ?", '%'.$params['keyword'].'%');
    }
        
    if( !empty($params['start_date']) )
    {
      $select->where($rName.".creation_date >= ?", date('Y-m-d', $params['start_date']));
    }

    if( !empty($params['end_date']) )
    {
      $select->where($rName.".creation_date <= ?", date('Y-m-d', $params['end_date']));
    }
    
    if (isset($params['exclude_folder_ids']) and !empty($params['exclude_folder_ids']))
    {
      $select->where($rName.".folder_id NOT IN (?)", $params['exclude_folder_ids']);
    }    
    
    if( !empty($params['period']))
    {
      $period_maps = array(
        '24hrs' => 1,
        'week' => 7,
        'month' => 30,
        'quarter' => 120,
        'year' => 365,
      );
      if (isset($period_maps[$params['period']]) && $period_maps[$params['period']])
      {
        $select->where($rName.".creation_date >= ?", date('Y-m-d', time() - $period_maps[$params['period']] * 86400));
      }
    }   

    if (isset($params['order'])) 
    {
      switch ($params['order'])
      {
        case 'random':
          $order_expr = new Zend_Db_Expr('RAND()');
          break;
        case 'recent':
          $order_expr = $rName.".creation_date DESC";
          break;
        case 'lastupdated':
          $order_expr = $rName.".modified_date DESC";
          break;
        case 'mostcommented':
          $order_expr = $rName.".comment_count DESC";
          break;
        case 'mostliked':
          $order_expr = $rName.".like_count DESC";
          break;  
        case 'mostviewed':
          $order_expr = $rName.".view_count DESC";
          break;
        case 'alphabet':
          $order_expr = $rName.".title ASC";
          break;

        default:
          $order_expr = !empty($params['order']) ? $params['order'] : $rName.'.creation_date DESC';
          
          if (!empty($params['order_direction'])) {
            $order_expr .= " " .$params['order_direction'];
          }
          
          if (!is_array($order_expr) && !($order_expr instanceof Zend_Db_Expr) and strpos($order_expr, '.') === false) {
            $order_expr = $rName.".".trim($order_expr);
          }
          break;
      }

      if (isset($params['preorder']) && $params['preorder'])
      {
        $pre_orders = array(
          1 => array("{$rName}.sponsored DESC"), // Sponsored listings, then user preference",
          2 => array("{$rName}.sponsored DESC","{$rName}.featured DESC"), // "Sponsored listings, featured listings, then user preference",
          3 => array("{$rName}.featured DESC"), // "Featured listings, then user preference",
          4 => array("{$rName}.featured DESC","{$rName}.sponsored DESC"), // "Featured listings, sponsored listings, then user preference",
        );
        if (array_key_exists($params['preorder'], $pre_orders))
        {
          $order_expr = array_merge($pre_orders[$params['preorder']], array($order_expr));
        }
      }

      $select->order( $order_expr );
      unset($params['order']);
    }
    
    return $select;
  }
  
  
  public function countFolders($params = array())
  {
    
    $select = $this->select()->from($this, new Zend_Db_Expr('COUNT(folder_id)'));
    $select = $this->selectParamBuilder($params, $select)->limit(1);
    //echo $select;
    $total = $select->query()->fetchColumn();
    /*
    $total = $this->selectParamBuilder($params)->
      ->limit(1)
      ->query()
      ->fetchColumn();
    */
    return $total;
  }
}