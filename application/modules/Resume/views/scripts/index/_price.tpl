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
?>

<?php 
$priceunit = Engine_Api::_()->resume()->getPriceUnit();
$pricetype = Engine_Api::_()->resume()->getPriceType();

$price = $this->resume->price;

$options = array(
);

if ($pricetype == 'Int') {
  $options['precision'] = 0;
}

//echo $this->locale()->toCurrency($price, $currency);
try
{
  echo $this->locale()->toCurrency($price, $priceunit, $options);
}
catch (Exception $e) {
  echo $e;
}
?>