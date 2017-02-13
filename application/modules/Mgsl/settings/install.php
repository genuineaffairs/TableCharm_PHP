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
 
 
 
class Mgsl_Installer extends Engine_Package_Installer_Module
{

  private function runCustomQueries() {
    $db = $this->getDb();

    $path = $this->_operation->getPrimaryPackage()->getBasePath() . '/'
            . $this->_operation->getPrimaryPackage()->getPath() . '/'
            . 'settings/custom-queries';

    $files = array(
        'custom.sql' => function() {
          return true;
        }
    );
    
    $db->beginTransaction();

    foreach ($files as $file => $callback) {
      if (call_user_func($callback) && file_exists($path . '/' . $file)) {
        $contents = file_get_contents($path . '/' . $file);
        foreach (Engine_Package_Utilities::sqlSplit($contents) as $sqlFragment) {
          try {
            $db->getConnection()->query($sqlFragment);
          } catch (Exception $e) {
            return $this->_error('Query failed with error: ' . $e->getMessage());
          }
        }
      }
    }
    $db->commit();
  }

  public function onInstall()
  {
    parent::onInstall();
    
    $this->runCustomQueries();
  }
}
