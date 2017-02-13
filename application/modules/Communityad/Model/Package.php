<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Package.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Model_Package extends Core_Model_Item_Abstract {

  // Properties
  protected $_parent_type = 'package';
  protected $_searchColumns = array();
  protected $_parent_is_owner = true;
  protected $_product;
  protected $_searchTriggers = false;
  protected $_modifiedTriggers = false;

  public function getPackageDescription() {
    $translate = Zend_Registry::get('Zend_Translate');
    $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
    $view = Zend_Registry::get('Zend_View');
    $priceStr = $view->locale()->toCurrency($this->price, $currency);
    switch ($this->price_model) {
      case 'Pay/click':
        if ($this->price == 0 && $this->model_detail != -1) {
          $str = sprintf($translate->translate('This package is free. Advertisers will not have to pay for Ads created in this package. Ads of this package will have a life of %1$s clicks.'), $this->model_detail);
        } elseif ($this->price == 0 && $this->model_detail == -1) {
          $str = $translate->translate('This package is free. Advertisers will not have to pay for Ads created in this package. Ads of this package will be able to get unlimited clicks.');
        } elseif ($this->price != 0 && $this->model_detail != -1) {
          $str = sprintf($translate->translate('The price of this package is %1$s. Advertisers will have to pay this amount to receive %2$s clicks on their ads of this package.'), $priceStr, $this->model_detail);
        } elseif ($this->price != 0 && $this->model_detail == -1) {
          $str = sprintf($translate->translate('The price of this package is %1$s. Advertisers will have to pay this amount to create ads of this package. Ads of this package will be able to get unlimited clicks.'), $priceStr);
        }
        break;

      case 'Pay/view':
        if ($this->price == 0 && $this->model_detail != -1) {
          $str = sprintf($translate->translate('This package is free. Advertisers will not have to pay for Ads created in this package. Ads of this package will have a life of %1$s views.'), $this->model_detail);
        } elseif ($this->price == 0 && $this->model_detail == -1) {
          $str = $translate->translate('This package is free. Advertisers will not have to pay for Ads created in this package. Ads of this package will be able to get unlimited views.');
        } elseif ($this->price != 0 && $this->model_detail != -1) {
          $str = sprintf($translate->translate('The price of this package is %1$s. Advertisers will have to pay this amount to receive %2$s views on their ads of this package.'), $priceStr, $this->model_detail);
        } elseif ($this->price != 0 && $this->model_detail == -1) {
          $str = sprintf($translate->translate('The price of this package is %1$s. Advertisers will have to pay this amount to create ads of this package. Ads of this package will be able to get unlimited views.'), $priceStr);
        }
        break;

      case 'Pay/period':
        if ($this->price == 0 && $this->model_detail != -1) {
          $str = sprintf($translate->translate('This package is free. Advertisers will not have to pay for Ads created in this package. Ads of this package will be able to run for %1$s days.'), $this->model_detail);
        } elseif ($this->price == 0 && $this->model_detail == -1) {
          $str = $translate->translate('This package is free. Advertisers will not have to pay for Ads created in this package. Ads of this package will be able to run for unlimited days.');
        } elseif ($this->price != 0 && $this->model_detail != -1) {
          $str = sprintf($translate->translate('The price of this package is %1$s. Advertisers will have to pay this amount to make this ad run for %2$s days.'), $priceStr, $this->model_detail);
        } elseif ($this->price != 0 && $this->model_detail == -1) {
          $str = sprintf($translate->translate('The price of this package is %1$s. Advertisers will have to pay this amount to create ads of this package. Ads of this package will be able to run for unlimited days.'), $priceStr);
        }
        break;
    }


    return $str;
  }

  public function isFree() {
    return ( $this->price <= 0 );
  }

  public function getProductParams() {
    $string = strip_tags($this->desc);
    $desc = Engine_String::strlen($string) > 250 ? Engine_String::substr($string, 0, (247)) . '...' : $string;
    return array(
        'title' => $this->title,
        'description' => $desc,
        'price' => $this->price,
        'extension_type' => 'userads',
        'extension_id' => $this->package_id,
    );
  }

  public function getProduct() {
    if (null === $this->_product) {
      $productsTable = Engine_Api::_()->getDbtable('products', 'payment');
      $this->_product = $productsTable->fetchRow($productsTable->select()
                              ->where('extension_type = ?', 'userads')
                              ->where('extension_id = ?', $this->getIdentity())
                              ->limit(1));
      // Create a new product?
      if (!$this->_product) {
        $this->_product = $productsTable->createRow();
        $this->_product->setFromArray($this->getProductParams());
        $this->_product->save();
      }
    }

    return $this->_product;
  }

  public function getGatewayIdentity() {
    return $this->getProduct()->sku;
  }

  public function getGatewayParams() {
    $params = array();

    // General
    $params['name'] = $this->title;
    $params['price'] = $this->price;
    $params['description'] = strip_tags($this->desc);
    $params['vendor_product_id'] = $this->getGatewayIdentity();
    $params['tangible'] = false;
    // Non-recurring
    $params['recurring'] = false;

    return $params;
  }

  public function getLevelString() {
    $translate = Zend_Registry::get('Zend_Translate');
    $levelTitle = array();
    $levelarray = explode(",", $this->level_id);
    foreach (Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level) {
      if ($level->type == 'public' || !in_array($level->getIdentity(), $levelarray)) {
        continue;
      }
      $levelTitle[] = $translate->translate($level->getTitle());
    }
    return implode(", ", $levelTitle);
  }

}