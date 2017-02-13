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
 
class Resume_Model_Section extends Core_Model_Item_Abstract
{
  protected $_searchColumns = false;
  
  protected $_parent_type = 'resume';
  
  const CHILD_TYPE_TEXT = 'Text';
  const CHILD_TYPE_EMPLOYMENT = 'Employment';
  const CHILD_TYPE_EDUCATION = 'Education';
  const CHILD_TYPE_SPORTING_HISTORY = 'Sportinghistory';
  const CHILD_TYPE_AWARD = 'Award';
  const CHILD_TYPE_QUALIFICATION = 'Qualification';
  const CHILD_TYPE_COACHING_HISTORY = 'Coachinghistory';

  
  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'resume_extended',
      'controller' => 'section',
      'reset' => true,
      'section_id' => $this->getIdentity(),
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }
  
  public function isChildType($type)
  {
    return strtolower($type) == strtolower($this->child_type);
  }
  
  public function isChildTypeText()
  {
    return $this->isChildType('Text');
  }
  
  
  public function getAuthorizationItem()
  {
    return $this->getParent('resume');
  }  
  
  public function getChildItemType()
  {
    if (!$this->isChildTypeText()) {
      return 'resume_' . strtolower($this->child_type);
    }
  }
  
  /**
   * @return Engine_Db_Table
   */
  public function getChildTable()
  {
    return Engine_Api::_()->getItemTable($this->getChildItemType());
  }
  
  
  public function getChildItems()
  {
    if (!$this->isChildTypeText()) {
      return $this->getChildTable()->getChildren(array('section'=>$this));
    }
  }
  
  public function getChildItem($child_id)
  {
    if (!$this->isChildTypeText()) {
      return $this->getChildTable()->getChild($child_id);
    }
  }
  
  
  protected function _delete()
  {
    if( $this->_disableHooks ) return;

    $children = $this->getChildItems();
    if (!empty($children)) {
      foreach ($children as $child) {
        $child->delete();
      }
    }

    parent::_delete();
  }  
  
} 

