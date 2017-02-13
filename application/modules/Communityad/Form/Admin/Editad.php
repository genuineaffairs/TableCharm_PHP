<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Editad.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Form_Admin_Editad extends Engine_Form {

  public $_error = array();
  protected $_item;

  public function getItem() {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item) {
    $this->_item = $item;
    return $this;
  }

  public function init() {
    parent::init();

    $this->setTitle('Edit Advertisement')
            ->setDescription('Edit the Ad below, and then click "Save Changes". You can change various parameters like payment status, approval, weight, minimum CTR, etc.');

    // DUMMY ELEMENS   
    $this->addElement('Dummy', 'ad_type', array(
         'label' => 'Ad Type',
    ));
    $this->ad_type->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
    $this->addElement('Dummy', 'ad_content', array(
    ));
    $this->ad_content->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));
    $this->addElement('Dummy', 'ad_title', array(
        'label' => 'Title',
    ));
    $this->ad_title->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));

    $this->addElement('Dummy', 'campaign_title', array(
        'label' => 'Campaign Name',
    ));
    $this->campaign_title->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));


    $this->addElement('Dummy', 'package_name', array(
        'label' => 'Package',
    ));
    $this->package_name->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));

    $this->addElement('Dummy', 'status', array(
        'label' => 'Status',
    ));
    $this->status->getDecorator('Description')->setOptions(array('placement' => 'APPEND', 'escape' => false));

    // ELEMENT PAYMENT STATUS
    $flage = true;
    if (!empty($this->getItem()->getPackage()->price))
      $flage = false;
    $this->addElement('Select', 'payment_status', array(
        'label' => 'Payment Status',
        'description' => 'You can change the payment status of this ad below.',
        'attribs' => array('disable' => $flage),
        'multiOptions' => array(
            'initial' => 'No',
            'active' => 'Yes',
            'pending' => 'Pending',
            'overdue' => 'Overdue',
            'refunded' => 'Refunded',
            'cancelled' => 'Cancelled',
            'free' => 'Not Required',
        ),
    ));

    $this->addElement('Select', 'approved', array(
        'label' => 'Approved',
        'multiOptions' => array(
            '1' => 'Yes',
            '0' => 'No',
        ),
    ));

    $flage = true;
    if (!empty($this->getItem()->approve_date))
      $flage = false;
    if ($flage) {
      $this->addElement('Select', 'declined', array(
          'label' => 'Decline',
          'description' => 'You can decline and reject this ad before it becomes activated.',
          'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No',
          ),
      ));
    }

    $this->addElement('Select', 'enable', array(
        'label' => 'Pause',
        'description' => 'You can pause this ad. In this case, it will not be visible to anyone till you start it again.',
        'attribs' => array('disable' => $flage),
        'multiOptions' => array(
            '0' => 'Yes',
            '1' => 'No',
        ),
    ));

    $this->addElement('Text', 'weight', array(
        'label' => 'Weight',
        'maxlength' => '3',
        'description' => "Enter the weight that you want to associate with this ad (Enter an integer between 0 to 999.). This will work as a reference to the ad's priority. Higher an ad's weight, higher will be its chances to be shown. Note: You should assign a non-zero weight to an ad only in exceptional cases, and you are suggested to change the weight of the ad back to zero after the purpose is achieved. One such case might be if an ad owner complaints about very less impressions/views of his ad.",
        'value' => 0
    ));

    $start_cal = new Engine_Form_Element_CalendarDateTime('cads_start_date');
    $start_cal->setLabel("Start Date");
    $date = (string) date('Y-m-d');
    $start_cal->setValue($date . ' 00:00:00');
    $this->addElement($start_cal);

    $this->addElement('Radio', 'end_settings', array(
        'id' => 'end_settings',
        'onclick' => "endDateField()",
        'multiOptions' => array(
            "0" => "Don't end this advertisement on a specific date.",
            "1" => "End this advertisement on a specific date."),
        'value' => 0
    ));

    $end_cal = new Engine_Form_Element_CalendarDateTime('cads_end_date');
    $end_cal->setLabel("End Date");
    $end_cal->setValue($date . ' 00:00:00');
    $this->addElement($end_cal);

    $price_model = $this->getItem()->price_model;
    // Limits
    if ($price_model == 'Pay/view') {
      if (!$this->getItem()->approved) {
        $flage = true;
        $description = 'Note: This ad is currently dis-approved, and hence this field is not editable.';
      } else {
        $flage = false;
        $description = 'The Ad will end when this number of views is reached. Enter "-1" for unlimited views.';
      }
      $this->addElement('Text', 'limit_view', array(
          'label' => 'Total Views Allowed',
          'description' => $description,
          'attribs' => array('disable' => $flage),
          'class' => 'short',
          'value' => '0'
      ));
      $this->limit_view->getDecorator('Description')->setOption('placement', 'append');
    } elseif ($price_model == 'Pay/click') {

      if (!$this->getItem()->approved) {
        $flage = true;
        $description = 'Note: This ad is currently dis-approved, and hence this field is not editable.';
      } else {
        $flage = false;
        $description = 'The Ad will end when this number of clicks is reached. Enter "-1" for unlimited clicks.';
      }

      $this->addElement('Text', 'limit_click', array(
          'label' => 'Total Clicks Allowed',
          'attribs' => array('disable' => $flage),
          'description' => $description,
          'class' => 'short',
          'value' => '0'
      ));
      $this->limit_click->getDecorator('Description')->setOption('placement', 'append');
    }

    $this->addElement('Text', 'min_ctr', array(
        'label' => 'Minimum CTR',
        'description' => "If you specify a minimum CTR (click through rate, which is the ratio of clicks to views) and the Ad's CTR goes below your limit, the Ad will end. If you decide to specify a minimum CTR limit, you should enter it as a percentage of clicks to views (e.g. 0.05%). To create an Ad with no definite end, don't specify limits and the ad will continue until you choose to end it, or its expiry limit is reached.",
        'class' => 'short',
        'value' => '0'
    ));
    $this->min_ctr->getDecorator('Description')->setOption('placement', 'append');


    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'communityad', 'controller' => 'viewad', 'action' => 'index'), 'admin_default', true),
        'decorators' => array(
            'ViewHelper'
        )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}