<?php

/**
 * Radcodes - SocialEngine Module
 *
 * @section   Application_Extensions
 * @package    Resume
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
class Resume_Model_DbTable_Sections extends SharedResources_Model_DbTable_Abstract
{
  protected $_rowClass = 'Resume_Model_Section';
  

  public function getSectionSelect($params = array())
  {
    $select = $this->select();
    
    if (isset($params['enabled']))
    {
      $select->where("enabled = ?", $params['enabled'] ? 1 : 0);
    }
    
    if (isset($params['type']))
    {
      $select->where("type = ?", $params['type']);
    }    
    
    if (isset($params['resume']))
    {
      $resume_id = $params['resume'] instanceof Resume_Model_Resume ? $params['resume']->getIdentity() : $params['resume'];
      $select->where("resume_id = ?", $resume_id);
    }    
    
    if (isset($params['section_id']))
    {
      $select->where("section_id = ?", $params['section_id']);
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
  
  
  public function getSection($section_id)
  {
    static $_sections = array();
    
    if (!isset($_sections[$section_id]))
    {
      $_sections[$section_id] = $this->findRow($section_id);
    }
    
    return $_sections[$section_id];
  }
  
  
  public function getCoreSections($params = array())
  {
    $params = array_merge(array('order' => 'order', 'resume' => 0), $params);
    //print_r($params);
    $select = $this->getSectionSelect($params);
    
    return $this->fetchAll($select);
  }
  
  public function getSections($params = array())
  {
    $params = array_merge(array('order' => 'order'), $params);
    $select = $this->getSectionSelect($params);
    
    return $this->fetchAll($select);
  }
  
  public function getMultiOptionsAssoc($params = array())
  {
    $sections = $this->getSections($params);
    return $this->toAssoc($sections);
  }
  
  public function toAssoc($sections)
  {
    $data = array();
    foreach ($sections as $section)
    {
      $data[$section->getIdentity()] = $section->getTitle();
    }
    return $data;
  }
  
    
}