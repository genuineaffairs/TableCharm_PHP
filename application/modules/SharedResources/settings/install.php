<?php

/**
 * Description of install
 *
 * @author Tristan
 */
class SharedResources_Installer extends Engine_Package_Installer_Module
{

  protected $_sharedResources = array('resume');

  public function onInstall()
  {
    parent::onInstall();

    $db = $this->getDb();

    foreach ($this->_sharedResources as $res) {
      $tableName = "engine4_{$res}_{$res}s";

      try {
        $info = $db->describeTable($tableName);
      } catch (Exception $ex) {
        $tableName = "engine4_{$res}s";
        $info = $db->describeTable($tableName);
      }

      if (!array_key_exists('site_id', $info)) {
        $db->query("ALTER TABLE {$tableName} ADD COLUMN site_id int(11) NOT NULL DEFAULT '1'");
      }
    }
  }

}
