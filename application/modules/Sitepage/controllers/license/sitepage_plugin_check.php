<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sitepage_plugin_version.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 
$db = $this->getDb();
$sitepage_version_correct = true;

$errorMsg = '';
$base_url = Zend_Controller_Front::getInstance()->getBaseUrl();

$finalModules = '';
$select = new Zend_Db_Select($db);
$select->from('engine4_core_modules',array('title', 'version'))
	->where('name = ?', "sitepage")
	->where('enabled = ?', 1);
$getModVersion = $select->query()->fetchObject();
if (!empty($getModVersion)) {
$isModSupport = strcasecmp($getModVersion->version, $sitepage_plugin_version); 
}
if ($isModSupport < 0) {
	$errorMsg .= '<div class="tip"><span style="background-color: #da5252;color:#FFFFFF;">Note: You do not have the latest version of the Directory / Pages Plugin. Please upgrade Directory / Pages Plugin on your website to the latest version available in your SocialEngineAddOns Client Area to enable its integration with "Directory / Pages Plugin".<br/> Please <a class="" href="' . $base_url . '/manage">Click here</a> to go Manage Packages.</span></div>';
  $sitepage_version_correct = false;
	return $this->_error($errorMsg);
}