<?php
/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Employment
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
?>
<?php 
list ($start_year, $start_month, $start_day) = explode('-', $this->employment->start_date);
$start_date = "";
if ($start_month > 0) {
  $start_date = $this->translate(date('M',mktime(0,0,0,$start_month,1,date('Y'))));
}
if ($start_year > 0) {
  if ($start_date) $start_date .= " ";
  $start_date .= $start_year;
}

if ($this->employment->is_current) {
  $end_date = $this->translate('present');
}
else {
  $end_year = $end_month = $end_day = 0;
  if ($this->employment->end_date) {
    $date_info = explode('-', $this->employment->end_date);
    if (!empty($date_info[0])) {
      $end_year = $date_info[0];
    }
    if (!empty($date_info[1])) {
      $end_month = $date_info[1];
    }
    if (!empty($date_info[2])) {
      $end_day = $date_info[2];
    }
    //list ($end_year, $end_month, $end_day) = explode('-', $this->employment->end_date);
  }
  $end_date = "";

  if ($end_month > 0) {
    $end_date = $this->translate(date('M',mktime(0,0,0,$end_month,1,date('Y'))));
  }
  if ($end_year > 0) {
    if ($end_date) $end_date .= " ";
    $end_date .= $end_year;
  }
}

$start_date = trim($start_date);
$end_date = trim($end_date);

$out = "";

if ($start_date && $end_date) {
  $out = $this->translate('%1$s to %2$s', $start_date, $end_date);
}
else if ($start_date) {
  $out = $this->translate('from %s', $start_date);
}
else if ($end_date) {
  $out = $this->translate('to %s', $end_date);
}

echo $out;