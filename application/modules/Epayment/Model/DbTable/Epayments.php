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

class Epayment_Model_DbTable_Epayments extends Engine_Db_Table
{
  protected $_rowClass = 'Epayment_Model_Epayment';
  
  protected $_serializedColumns = array('data');

  public function addEpayment(Core_Model_Item_Abstract $resource, $values)
  {
    
    $values = array_merge($values, array(
      'resource_id' => $resource->getIdentity(),
      'resource_type' => $resource->getType()
    ));
    
    if (!isset($values['user_id'])) {
    	$values['user_id'] = $resource->getOwner('user')->getIdentity();
    }
    
    $row = $this->createRow();
    $row->setFromArray($values);
    $row->save();
    
    return $row;
  }
  
  
  public function hasEpayment(Core_Model_Item_Abstract $resource)
  {
    return ( null !== $this->getFirstEpayment($resource) );
  }

  
  public function getRecentEpayment(Core_Model_Item_Abstract $resource, $params = array())
  {
    $params = array_merge($params, array('order'=>'recent'));
  	$epayment = $this->getEpayment($resource, $params);
  	return $epayment;
  }
  
  public function getEpayment(Core_Model_Item_Abstract $resource, $params = array())
  {
  	$select = $this->getEpaymentSelect($resource, $params);
  	$select->limit(1);

  	$epayment = $this->fetchRow($select);
  	
  	return $epayment;
  }
  
  public function getEpaymentSelect(Core_Model_Item_Abstract $resource, $params = array())
  {
    $params = array_merge($params, array('resource' => $resource));
    $select = Engine_Api::_()->epayment()->getEpaymentsSelect($params);   

    return $select;
  }

  public function getEpaymentPaginator(Core_Model_Item_Abstract $resource, $params = array())
  {
    $params = array_merge($params, array('resource' => $resource));
    $paginator = Engine_Api::_()->epayment()->getEpaymentsPaginator($params);
    
    return $paginator;
  }

  public function getEpaymentCount(Core_Model_Item_Abstract $resource, $params = array())
  {
    $paginator = $this->getEpaymentPaginator($resource, $params);
    return $paginator->getTotalItemCount(); 
    
    /*
    $select = new Zend_Db_Select($this->getAdapter());
    $select
      ->from($this->info('name'), new Zend_Db_Expr('COUNT(1) as count'));

    $select->where('resource_type = ?', $resource->getType());
    $select->where('resource_id = ?', $resource->getIdentity());
    
    $data = $select->query()->fetchAll();
    return (int) $data[0]['count'];
    */
  }



  
}