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
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Photo.php 9339 2011-09-29 23:03:01Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Resume
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Resume_Model_Album extends Core_Model_Item_Collection
{
  protected $_parent_type = 'resume';

  protected $_owner_type = 'resume';

  protected $_children_types = array('resume_photo');

  protected $_collectible_type = 'resume_photo';

  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'resume_profile',
      'reset' => true,
      'id' => $this->getResume()->getIdentity(),
      //'album_id' => $this->getIdentity(),
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function getResume()
  {
    return $this->getOwner();
    //return Engine_Api::_()->getItem('resume', $this->resume_id);
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('resume');
  }

  protected function _delete()
  {
    // Delete all child posts
    $photoTable = Engine_Api::_()->getItemTable('resume_photo');
    $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
    foreach( $photoTable->fetchAll($photoSelect) as $resumePhoto ) {
      $resumePhoto->delete();
    }

    parent::_delete();
  }
}