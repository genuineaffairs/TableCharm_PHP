<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Seaocore
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: FormCalendarDateTime.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageevent_View_Helper_FormCalendarDateTime extends Seaocore_View_Helper_FormCalendarDateTime {

    public function formCalendarDateTime($name, $value = null, $attribs = null, $options = null, $listsep = "<br />\n") {
      $return = parent::formCalendarDateTime($name, $value, $attribs, $options, $listsep);
      
      if(preg_match('/[start|end]time/', $name)) {
        $time_label = preg_replace_callback('/([start|end]+)time/',
                function($matches) {
                  return ucfirst($matches[1]) . ' Time';
                },
                $name);
      } else {
        $time_label = '';
      }
      
      // Add Start/End Time label (if have)
      $return = preg_replace('/(\<select.*)/', '<span class="custom_label">' . $time_label . '</span> $1', $return, 1);
      // Remove 'Select a date' sentence
      $return = str_replace($this->view->translate('Select a date'), '', $return);
      return $return;
    }

}
