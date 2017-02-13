<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Packages.php  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Model_DbTable_Packages extends Engine_Db_Table {

  protected $_name = 'communityad_package';
  protected $_rowClass = 'Communityad_Model_Package';

  public function getVal($package_id) {
    // Get type
    $select = $this->select()
            ->where('package_id = ?', $package_id);

    $result = $this->fetchRow($select);

    return $result;
  }

  public function getAllPackages() {
    return $this->fetchAll()->toArray();
  }

  public function setValue($data) {
    $row = null;
    if (isset($data['package_id']) && !empty($data['package_id']))
      $row = $this->getVal($data['package_id']);

    if ($row == null) {
      $row = $this->createRow();
    }
    if (null !== $row) {

      $row->setFromArray($data);
    }
    $row->save();
    return $row->package_id;
  }

  public function getEnabledPackageCount() {
    return $this->select()
                    ->from($this, new Zend_Db_Expr('COUNT(*)'))
                    ->where('enabled = ?', 1)
                    ->query()
                    ->fetchColumn();
  }

  public function getEnabledPackageList($type = null) {
    $select = $this->select()
            ->from($this, array('package_id', 'title'))
            ->where('enabled = ?', 1)
            ->order('order');
    if ($type)
      $select->where ('type =?', $type);
    $packages = array();
    foreach ($this->fetchAll($select) as $package)
      $packages[$package->package_id] = $package->title;
    return $packages;
  }

  public function getEnabledNonFreePackageCount() {
    return $this->select()
                    ->from($this, new Zend_Db_Expr('COUNT(*)'))
                    ->where('enabled = ?', 1)
                    ->where('price > ?', 0)
                    ->query()
                    ->fetchColumn();
  }

}