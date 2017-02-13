<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: FieldBirthdate.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */
class Fields_View_Helper_FieldContractExpiryDate extends Fields_View_Helper_FieldAbstract
{
  public function fieldContractExpiryDate($subject, $field, $value)
  {
    $label = $this->view->locale()->toDate($value->value, array(
      'size' => 'long',
      'timezone' => false,
    ));
    //$str = $this->view->date($value->value);
    $parts = @explode('-', $value->value);

    // Error if not filled out
    if( count($parts) < 3 || count(array_filter($parts)) < 3 ) {
      //$this->addError('Please fill in your birthday.');
      return false;
    }

    $value = mktime(0, 0, 0, $parts[1], $parts[2], $parts[0]);

    // Error if too low
    $date = new Zend_Date($value);
    $age = (int)(- $date->sub(time())  / 365 / 86400);

    return $this->encloseInLink($subject, $field, $age, $label, true);
  }
}