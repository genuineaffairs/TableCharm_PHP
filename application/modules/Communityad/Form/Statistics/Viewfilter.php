<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Viewfilter.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Form_Statistics_Viewfilter extends Engine_Form {

  public function init() {
    $this
        ->setTitle('Daily Stats')      
        ->setDescription('Choose a start date and end date below to view the daily statistics of your Ad.');

    $start_cal = new Engine_Form_Element_CalendarDateTime('start_cal');
    $start_cal->setLabel("From");

    $this->addElement($start_cal);

    $end_cal = new Engine_Form_Element_CalendarDateTime('end_cal');
    $end_cal->setLabel("To");
    $end_cal->setValue(date('Y-m-d H:i:s'));

    $this->addElement($end_cal);

    $this->addElement('Hidden', 'ajax_filter', array(
            'value' => '1',
    ));

    // Init submit
    $this->addElement('Button', 'submit', array(
            'label' => 'Show Stats',
            'type' => 'submit',
        // 'onclick' => 'submitForm()',
    ));
  }

}