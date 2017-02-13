<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @child   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
class Resume_Model_DbTable_SectionChildren extends SharedResources_Model_DbTable_Abstract
{

  public function getChildSelect($params = array())
  {
    $select = $this->select();
    
    if (isset($params['section']))
    {
      $resume_id = $params['section'] instanceof Resume_Model_Section ? $params['section']->getIdentity() : $params['section'];
      $select->where("section_id = ?", $resume_id);
    }    
    
    if (!empty($params['order'])) 
    {
      $select->order($params['order']);
    }
    else
    {
      $select->order('order');
    }
    //echo $select;
    return $select;
  }
  
  
  public function getChild($child_id)
  {
    static $_children = array();
    
    if (!isset($_children[$child_id]))
    {
      $_children[$child_id] = $this->findRow($child_id);
    }
    
    return $_children[$child_id];
  }
  

  public function getChildren($params = array())
  {
    $params = array_merge(array('order' => 'order'), $params);
    $select = $this->getChildSelect($params);
    return $this->fetchAll($select);
  }
  
}
