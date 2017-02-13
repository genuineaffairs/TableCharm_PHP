<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IpnController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_IpnController extends Core_Controller_Action_Standard {

  public function __call($method, array $arguments) {

    $params = $this->_getAllParams();
    $gatewayType = $params['action'];
    $gatewayId = (!empty($params['gateway_id']) ? $params['gateway_id'] : null );
    unset($params['module']);
    unset($params['controller']);
    unset($params['action']);
    unset($params['rewrite']);
    unset($params['gateway_id']);
    if (!empty($gatewayType) && 'index' !== $gatewayType) {
      $params['gatewayType'] = $gatewayType;
    } else {
      $gatewayType = null;
    }

		//LOG IPN
    $ipnLogFile = APPLICATION_PATH . '/temporary/log/sitpage-payment-ipn.log';
    file_put_contents($ipnLogFile, date('c') . ': ' .
            print_r($params, true), FILE_APPEND);

		//GET GATEWAYS
    $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'sitepage');
    $gateways = $gatewayTable->fetchAll(array('enabled = ?' => 1));

		//TRY TO DETECT GATEWAY
    $activeGateway = null;
    foreach ($gateways as $gateway) {
      $gatewayPlugin = $gateway->getPlugin();

			//ACTION MATCHES END OF PLUGIN
      if ($gatewayType &&
              substr(strtolower($gateway->plugin), - strlen($gatewayType)) == strtolower($gatewayType)) {
        $activeGateway = $gateway;
      } else if ($gatewayId && $gatewayId == $gateway->gateway_id) {
        $activeGateway = $gateway;
      } else if (method_exists($gatewayPlugin, 'detectIpn') &&
              $gatewayPlugin->detectIpn($params)) {
        $activeGateway = $gateway;
      }
    }

		//GATEWAY COULD NOT BE DETECTED
    if (!$activeGateway) {
      echo 'ERR';
      exit();
    }

		//VALIDATE IPN
    $gateway = $activeGateway;
    $gatewayPlugin = $gateway->getPlugin();

    try {
      $ipn = $gatewayPlugin->createIpn($params);
    } catch (Exception $e) {
      // IPN validation failed
			//IPN VALIDATION FAILED
      if ('development' == APPLICATION_ENV) {
        echo $e;
      }
      echo 'ERR';
      exit();
    }

		//PROCESS IPN
    try {
      $gatewayPlugin->onIpn($ipn);
    } catch (Exception $e) {
      $gatewayPlugin->getGateway()->getLog()->log($e, Zend_Log::ERR);

			//IPN VALIDATION FAILED
      if ('development' == APPLICATION_ENV) {
        echo $e;
      }
      echo 'ERR';
      exit();
    }

		//EXIT
    echo 'OK';
    exit();
  }
}

?>