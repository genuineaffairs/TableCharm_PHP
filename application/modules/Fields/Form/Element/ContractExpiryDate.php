<?php
/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */
class Fields_Form_Element_ContractExpiryDate extends Engine_Form_Element_Date
{
  public function getYearMax()
  {
    // 7 years from now
    if( is_null($this->_yearMax) )
    {
      $date = new Zend_Date();
      $this->_yearMax = (int) $date->get(Zend_Date::YEAR) + 7;
    }
    return $this->_yearMax;
  }

  public function getYearMin()
  {
    // This year
    if( is_null($this->_yearMin) )
    {
      $date = new Zend_Date();
      $this->_yearMin = (int) $date->get(Zend_Date::YEAR) - 1;
    }
    return $this->_yearMin;
  }
}