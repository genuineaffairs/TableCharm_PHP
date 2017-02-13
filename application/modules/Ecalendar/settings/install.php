<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event Calendar
 * @copyright  Copyright iPragmatech Solutions Pvt. Ltd.
 * @license    http://www.ipragmatech.com
 * @version    $Id: install.php 06.06.12 11:50 $
 * @author     Gaurav Sharma
 */

class Ecalendar_Installer extends Engine_Package_Installer_Module
{
	public function onPreInstall()
	{
		parent::onPreInstall();
		$result = $this->checkLicense();
		$error_message = "You don't have valid license. Please contact at support@ipragmatech.com or open a ticket";
		if ($result != '"Success"') {
//			return $this->_error($error_message);
		}
	}

	public function checkLicense()
	{
		

		$curl = curl_init();
		$params_str = '?product_id=1234&url='. $_SERVER['SERVER_NAME'];

		$url = "http://www.ipragmatech.com/api/get_product_validation/".$params_str;
        
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($curl);
		curl_close($curl);

		return $result;
	}
        
        public function onInstall() {
          parent::onInstall();
          
          $this->_runCustomQueries();
        }
        
        protected function _runCustomQueries() {
          $db = $this->getDb();
          
          try {
            $params_json = $db->select()
                    ->from('engine4_core_menuitems', 'params')->where('`name` = ?', 'core_main_event')->query()->fetchColumn();
            $params = json_decode($params_json, true);
            $params['route'] = 'ecalendar_general';
            $updated_params_json = json_encode($params);
            $db->query("UPDATE engine4_core_menuitems SET params = '{$updated_params_json}' WHERE `name` = 'core_main_event'");
          } catch(Exception $e) {
            echo $e->getMessage();
            exit;
          }
        }

	
}
