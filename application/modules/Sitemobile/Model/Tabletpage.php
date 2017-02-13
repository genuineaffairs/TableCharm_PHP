<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Tabletpage.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemobile_Model_Tabletpage extends Core_Model_Item_Abstract {

  protected $_searchTriggers = false;
  protected $_type = 'sitemobile_tablet_page';
  protected $_identity;
  protected $_shortType='page';

  public function __construct(array $config) {

    parent::__construct($config);
    $this->_identity = $this->page_id;
  }

  public function getType($inflect = false) {
    if ($inflect) {
      return str_replace(' ', '', ucwords(str_replace('_', ' ', $this->_type)));
    }

    return $this->_type;
  }

  /**
   * Gets the numeric unique identifier for this object
   *
   * @return integer|mixed
   */
  public function getIdentity() {
    return (int) $this->_identity;
  }
  
  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array()) {
    // identified
    if (!empty($this->url)) {
      $id = str_replace(array('_', ' '), '-', $this->url);
    } else if (!empty($this->name)) {
      $id = str_replace(array('_', ' '), '-', $this->name);
    } else {
      $id = $this->page_id;
    }

    $params = array_merge(array(
        'route' => 'default',
        'reset' => true,
        'module' => 'core',
        'controller' => 'pages',
        'action' => $id
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
                    ->assemble($params, $route, $reset);
  }

  public function allowedToView($viewer) {
    // Check if empty
    if (empty($this->levels)) {
      return true;
    }

    // Check if not array
    $allowedLevels = Zend_Json::decode($this->levels);
    if (!is_array($allowedLevels)) {
      return true;
    }

    // set up current $viewer's level_id
    if (!empty($viewer->level_id)) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id;
    }

    // Check if allowed
    if (in_array($level_id, $allowedLevels)) {
      return true;
    } else {
      return false;
    }
  }

}