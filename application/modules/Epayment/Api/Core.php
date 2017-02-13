<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Epayment
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */

class Epayment_Api_Core extends Core_Api_Abstract
{

  public function getEpaymentTable()
  {
    return Engine_Api::_()->getDbtable('epayments', 'epayment');
  }
  
  public function getEpayment($params = array())
  {
    if (is_array($params))
    {
      $select = $this->getEpaymentsSelect($params);
      $select->limit(1);
  	  $epayment = $this->getEpaymentTable()->fetchRow($select);
    }
    else 
    {
      $epayment = $this->getEpaymentTable()->find($params)->current();
    }
    return $epayment;
  }
  
  public function countEpayments($params = array())
  {
    $paginator = $this->getEpaymentsPaginator($params);
    return $paginator->getTotalItemCount();      
  }
  
  /**
   * Gets a paginator for epayments
   *
   * @param Core_Model_Item_Abstract $user The user to get the messages for
   * @return Zend_Paginator
   */
  public function getEpaymentsPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getEpaymentsSelect($params));
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
  
  
  public function getEpaymentsSelect($params = array())
  {
    $table = Engine_Api::_()->getDbtable('epayments', 'epayment');
    
    $select = $table->select();
    
    if (isset($params['user']))
    {
      $user = Engine_Api::_()->user()->getUser($params['user']);
      $select->where('user_id = ?', $user->getIdentity());
    }    
    
    if (isset($params['resource']))
    {
      if (!($params['resource'] instanceof Core_Model_Item_Abstract))
      {
        $resource = Engine_Api::_()->getItemByGuid($params['resource']);
      }
      else 
      {
        $resource = $params['resource'];
      }
      $params['resource_type'] = $resource->getType();
      $params['resource_id'] = $resource->getIdentity();
    }
    
    foreach (array('resource_type', 'resource_id', 'package_id', 'transaction_code', 'method', 'status', 'amount', 'currency') as $key)
    {
      if (isset($params[$key]))
      {
        $select->where("$key = ?", $params[$key]);
      }
    }
    
    if (isset($params['processed']))
    {
      $select->where("processed = ?", $params['processed'] ? 1 : 0);
    }
    
    foreach (array('payer_name', 'payer_account') as $key)
    {
      if (isset($params[$key]))
      {
        $select->where("$key LIKE ?", '%'.$params[$key].'%');
      }
    }
    
    if (empty($params['order'])) 
    {
      $params['order'] = 'recent';
    }    
    
    if (isset($params['order'])) 
    {
      switch ($params['order'])
      {
        case 'recent':
          $order_expr = "creation_date DESC";
          break;
        case 'oldest':
          $order_expr = "creation_date ASC";
          break;
        default:
          $order_expr = !empty($params['order']) ? $params['order'] : 'creation_date DESC';
          
          if (!empty($params['order_direction'])) {
            $order_expr .= " " .$params['order_direction'];
          }
      }
      $select->order( $order_expr );
    }    
    //echo $select;
    return $select;
  }

  public function getCurrencies()
  {
    $currencies = array('AUD', 'BRL', 'CAD', 'CHF', 'DKK', 'EUR', 'GBP', 'HKD', 'JPY', 'MXN', 'NOK', 'NZD', 'SEK', 'USD');
    $currencies = array('AUD','BRL','CAD','CZK','DKK','EUR','HKD','HUF','ILS','JPY','MYR','MXN','NOK','NZD','PHP','PLN','GBP','SGD','SEK','CHF','TWD','THB','TRY','USD');
    $translationList = Zend_Locale::getTranslationList('nametocurrency', Zend_Registry::get('Locale'));
    $currencies = array_intersect_key($translationList, array_flip($currencies));
    
    return $currencies;
  }
  
}