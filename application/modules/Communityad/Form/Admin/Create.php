<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Form_Admin_Create extends Engine_Form {

  public function init() {

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->setTitle('Create Advertisement Package')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setDescription("Create a new advertisement package over here. Below, you can configure various settings for this package like advertised content, pricing model, etc. Please note that payment parameters (Price, Pricing Model) cannot be edited after creation. If you wish to change these, you will have to create a new package and disable the existing one. Properties of an ad package would depend on the Ad Type that you choose for it.");
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOptions(array('tag' => 'h4', 'placement' => 'PREPEND'));

    //ELEMENT PACKAGE TITLE
    if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityadsponsored')){
      $this->addElement('Select', 'type', array(
          'label' => 'Ad Type',
          'description' => 'Select the Ad Type for which this package will be created.',
          'multiOptions' => array('default' => 'Community Ads', 'sponsored_stories' => 'Sponsored Stories'),
          'onchange' => 'javascript:setTypeBaseContent(this.value);',
      ));
    }else {
      $this->addElement('Hidden', 'type', array(
          'value' => 'default',
          'order' => 897,
      )); 
    }

    //ELEMENT PACKAGE TITLE
    $this->addElement('Text', 'title', array(
        'label' => 'Package Name',
        'allowEmpty' => FALSE,
        'validators' => array(
            array('NotEmpty', true),
        )
    ));
    $localeObject = Zend_Registry::get('Locale');
    $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
    $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);

    // ELEMENT PRICE
    $this->addElement('Text', 'price', array(
        'label' => Zend_Registry::get('Zend_Translate')->_("Price") . " ($currencyName)",
        'autocomplete' => 'off',
        'description' => '(Zero will make this a free package.)',
        'required' => true,
				'allowEmpty' => false,
				'validators' => array(
					array('Float', true),
					new Engine_Validate_AtLeast(0),
				),
				'value' => '0.00',
    ));
    $this->price->getDecorator("Description")->setOption("placement", "append");
    $filter = new Engine_Filter_Html();
    $this->addElement('Textarea', 'desc', array(
        'label' => 'Package Description',
        'required' => true,
        'attribs' => array('rows' => 24, 'cols' => 80, 'style' => 'width:200px; max-width:200px;height:120px;'),
        'allowEmpty' => false,
        'validators' => array(
            array('NotEmpty', true),
        ),
        'filters' => array(
            $filter,
            new Engine_Filter_StringLength(array('max' => 250)),
            new Engine_Filter_Censor(),)
    ));

    // Element: level_id
    $multiOptions = array('0' => 'All Levels');
    foreach (Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll() as $level) {
      if ($level->type == 'public') {
        continue;
      }
      $multiOptions[$level->getIdentity()] = $level->getTitle();
    }
    $this->addElement('Multiselect', 'level_id', array(
        'label' => 'Member Levels',
        'description' => 'Select the Member Levels to which this Ad Package should be available. Only users belonging to the selected Member Levels will be able to create ads of this package.',
        'attribs' => array('style' => 'max-height:100px; '),
        'multiOptions' => $multiOptions,
        'value' => array('0')
    ));

    $urloption_prepared['website'] = ucfirst('Custom Ad');
    $community_ad_modules = Engine_Api::_()->getDbtable('modules', 'communityad')->getModuleName();
    // Getting the list of plugins enabled on the site
    $enabledModuleNames = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();

    // Which widget are enable we are tacking in array.
    $moduleArray = array_intersect($community_ad_modules, $enabledModuleNames);
    $moduleArray = array_unique($moduleArray);
    foreach ($moduleArray as $module) {

      $getInfo = Engine_Api::_()->getDbtable('modules', 'communityad')->getModuleInfo($module);
      if (!empty($getInfo)) {
        if( strstr($module, "sitereview") ) {
          foreach($getInfo as $modGetInfo) {
            $urloption_prepared[$module . '_' . $modGetInfo['module_id']] = $modGetInfo['module_title'];
          }
        }else {
          $urloption_prepared[$module] = $getInfo['module_title'];
        }
      }
    }

    $this->addElement('Multiselect', 'urloption', array(
        'label' => 'Content Advertised in this Package',
        'description' => 'Select the content types that you want to be advertised in this package. Choosing “Custom Ad” will enable the advertiser to create a custom ad. (Press Ctrl and click to select multiple types.)',
        'attribs' => array('style' => 'max-height:100px; '),
        'multiOptions' => $urloption_prepared,
        'value' => array('website')
    ));
    $this->urloption->getDecorator('Description')->setOptions(array('tag' => 'p', 'id' => 'urloption_description','placement' => 'PREPEND'));

    $model_prepared = array(
        "Pay/click" => "Pay for Clicks",
        "Pay/view" => "Pay for Views",
        "Pay/period" => "Pay for Days"
    );

    // ELEMENT PRICE MODEL
    $this->addElement('Select', 'price_model', array(
        'label' => 'Pricing Model',
        'description' => 'Select the pricing model for this package.',
        'multiOptions' => $model_prepared,
        'onchange' => 'javascript:setModelDetail(this.value,null);',
    ));
    // ELEMENT MODEL CLICK
    $this->addElement('Text', 'model_click', array(
        'label' => 'Clicks Limit',
        'value' => -1,
        'description' => '(-1 for unlimited clicks) Note: A change in this setting later on will only apply on new ads that are created in this package.',
        'filters' => array(
            new Engine_Filter_Censor(),
        ),
    ));
    $this->model_click->getDecorator("Description")->setOption("placement", "append");
    // ELEMENT MODEL VIEWS
    $this->addElement('Text', 'model_view', array(
        'label' => 'Views Limit',
        'value' => -1,
        'description' => '(-1 for unlimited views) Note: A change in this setting later on will only apply on new ads that are created in this package.',
        'filters' => array(
            new Engine_Filter_Censor(),
        ),
    ));
    $this->model_view->getDecorator("Description")->setOption("placement", "append");
    // ELEMENT MORE PERIOD
    $this->addElement('Text', 'model_period', array(
        'label' => 'Period (in days)',
        'value' => -1,
        'description' => '(-1 for unlimited days) Note: A change in this setting later on will only apply on new ads that are created in this package.',
        'filters' => array(
            new Engine_Filter_Censor(),
        ),
    ));
    $this->model_period->getDecorator("Description")->setOption("placement", "append");


    // sponsored
    $this->addElement('Checkbox', 'sponsored', array(
        'label' => 'Display ads of this package in Sponsored Ads blocks. Note: A change in this setting later on will only apply on new ads that are created in this package.',
        'value' => 0,
    ));

    //featured
    $this->addElement('Checkbox', 'featured', array(
        'label' => 'Display ads of this package in Featured Ads blocks. Note: A change in this setting later on will only apply on new ads that are created in this package.',
        'value' => 0,
    ));




    //network
    $this->addElement('Checkbox', 'network', array(
        'label' => 'Allow ads of this package to be targeted. Ad creators will be able to target their ads based on the Targeting Settings configured by you in the Targeting Settings section.',
        'value' => 1,
    ));

    //network
    $this->addElement('Checkbox', 'public', array(
        'label' => 'Show ads of this package to non-logged-in visitors. Note: A change in this setting later on will only apply on new ads that are created in this package.',
        'value' => 1,
    ));

    // auto aprove
    $this->addElement('Checkbox', 'auto_aprove', array(
        'label' => 'Auto-Approve advertisements of this package. These advertisements will not need admin moderation approval before going live.',
        'value' => 0,
    ));
    // renew
    $this->addElement('Checkbox', 'renew', array(
        'label' => 'Enable Ad Renewal. Ad creators will be able to renew their ads of this package before expiry.',
        'value' => 0,
        'onclick' => 'javascript:setRenewBefore();',
    ));

    $this->addElement('Text', 'renew_before', array(
        'label' => 'Renewal Frame before Ad Expiry',
        'value' => 0,
        'description' => Zend_Registry::get('Zend_Translate')->_('Show ad renewal link these many ') . '<span id="renew_before_msg"> clicks</span>' . Zend_Registry::get('Zend_Translate')->_(' before expiry.'),
        'filters' => array(
            new Engine_Filter_Censor(),
        ),
    ));
    $this->renew_before->getDecorator("Description")->setOption("placement", "append");
    $this->renew_before->getDecorator("Description")->setOption("escape", false);

    $this->addElement('Hidden', 'model_detail', array(
        'order' => 1000,
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Create Package',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));
  }

}