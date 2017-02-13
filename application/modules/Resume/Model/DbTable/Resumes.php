<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Resume_Model_DbTable_Resumes extends SharedResources_Model_DbTable_Abstract
{
  protected $_rowClass = "Resume_Model_Resume";

  
  public function selectParamBuilder($params = array(), $select = null)
  {
    $rName = $this->info('name');
    
    if ($select === null)
    {
      $select = $this->select();
    }
    
    
    if (isset($params['live']) && $params['live'])
    {
      $params['status'] = Resume_Model_Resume::STATUS_APPROVED;
      $params['expire'] = false;
      $params['published'] = 1;
      unset($params['live']);
    }
    
    if (isset($params['user']) && $params['user']) 
    {
      $user = Engine_Api::_()->user()->getUser($params['user']);
      $select->where($rName.'.user_id = ?', $user->getIdentity());
    }

    foreach (array('package') as $field)
    {
      if (isset($params[$field]) && $params[$field])
      {
        $field_id = ($params[$field] instanceof Core_Model_Item_Abstract) ? $params[$field]->getIdentity() : $params[$field];
        $select->where($rName.'.'.$field.'_id = ?', $field_id);
      }
    }
    
    
    if (isset($params['category']) && $params['category'])
    {
      $category_id = ($params['category'] instanceof Core_Model_Item_Abstract) ? $params['category']->getIdentity() : (int) $params['category'];
      
      $category_ids = array($category_id);
      $categories = Engine_Api::_()->getItemTable('resume_category')->getChildrenOfParent($category_id);
      foreach ($categories as $category) {
        $category_ids[] = $category->getIdentity();
      }

      $select->where($rName.'.category_id IN (?)', $category_ids);
    }
    
    foreach (array('featured', 'sponsored', 'search', 'published') as $field)
    {
      if (isset($params[$field]))
      {
        $select->where($rName.".$field = ?", $params[$field] ? 1 : 0);
      }  
    }
    
    
    if( !empty($params['keyword']) )
    {
      $select->where($rName.".title LIKE ? OR ".$rName.".description LIKE ? OR ".$rName.".keywords LIKE ? OR ".$rName.".name LIKE ?", '%'.$params['keyword'].'%');
    }

    foreach (array('status') as $field)
    {
      if (isset($params[$field]) && $params[$field])
      {
        $select->where($rName.".$field = ?", $params[$field]);
      }
    }
    
    if (isset($params['expire']))
    {
      $today_date = date("Y-m-d H:i:s");
      
      // expired
      if ($params['expire'])
      {
        $select->where("$rName.expiration_settings = 1 and $rName.expiration_date < ?", $today_date);
      }
      // no expire
      else {
        $select->where("$rName.expiration_settings = 0 OR ($rName.expiration_settings = 1 and $rName.expiration_date > ?)", $today_date);
      }
    }     
    
    if( !empty($params['start_date']) )
    {
      $select->where($rName.".creation_date >= ?", date('Y-m-d', $params['start_date']));
    }

    if( !empty($params['end_date']) )
    {
      $select->where($rName.".creation_date <= ?", date('Y-m-d', $params['end_date']));
    }
    
    if (isset($params['exclude_resume_ids']) and !empty($params['exclude_resume_ids']))
    {
    	$select->where($rName.".resume_id NOT IN (?)", $params['exclude_resume_ids']);
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
    
    if(isset($params['user_name'])) {
      $select->where($rName.'.`name` LIKE ?', '%' . $params['user_name'] . '%');
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
        case 'pricehighest':
          $order_expr = $rName.".price DESC";
          break;
        case 'pricelowest':
          $order_expr = $rName.".price ASC";
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
  

  public function getResumes($params = array())
  {
    $select = $this->selectParamBuilder($params);
    return $this->fetchAll($select);
  }  
  
  
  public function getMultiOptionsAssoc($params = array())
  {
    $resumes = $this->getResumes($params);
    return $this->toAssoc($resumes);
  }
  
  public function toAssoc($resumes)
  {
    $data = array();
    foreach ($resumes as $resume)
    {
      $data[$resume->getIdentity()] = $resume->getTitle();
    }
    return $data;
  }
  
}