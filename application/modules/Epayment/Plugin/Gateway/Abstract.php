<?php 

/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Epayment
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */

class Epayment_Plugin_Gateway_Abstract
{

  protected $_testMode = false;
  
  protected $_log;
  
  public function __construct()
  {
    $this->init();
  }
  
  public function init()
  {
  }
  
  public function isTestMode()
  {
    return $this->_testMode;
  }
  
}