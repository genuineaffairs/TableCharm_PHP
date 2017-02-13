<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Form_Create extends Engine_Form {

  public $_error = array();
  protected $_item;
  protected $_copy;
  protected $_packageId;

  public function getCopy() {
    return $this->_copy;
  }

  public function setCopy($item) {
    $this->_copy = $item;
    return $this;
  }

  public function getItem() {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item) {
    $this->_item = $item;
    return $this;
  }

  public function getPackageId() {
    return $this->_packageId;
  }

  public function setPackageId($id) {
    $this->_packageId = $id;
    return $this;
  }

  public function init() {
    parent::init();

    $changeLink = '';
    $levels_prepared = Engine_Api::_()->communityad()->enabled_module_content($this->_packageId);
    if (!empty($levels_prepared)) {
      $is_customAs_enabled = $levels_prepared[0];
      $modulesArray = $levels_prepared[1];
      $is_moduleAds_enabled = $levels_prepared[2];
    }

    $module_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('type_id', 0);
    $this->addElement('Hidden', 'module_id', array('value' => $module_id, 'order' => 840));

    $resourceType = Zend_Controller_Front::getInstance()->getRequest()->getParam('resource_type', null);
    $resource_value_array = array();
    if (!empty($resourceType)) {
      $resource_value_array = Engine_Api::_()->communityad()->resource_content($resourceType);
    }

    // Need to set array($sub_title_array) and flag($is_edit_value) for 'Sub Title' only in the case of edit when page will render first time.
    $is_edit_value = 0;
    $sub_title_str = 0;
    if (!empty($this->_item)) {
      if ($this->_item->like) {
        $sub_title_str = Engine_Api::_()->communityad()->resource_content($this->_item->resource_type);
        $is_edit_value = Engine_Api::_()->communityad()->viewType($this->_item->resource_type);
      }
    }

    $this->setName('titleForm');
    $this->setTitle(Zend_Registry::get('Zend_Translate')->_('Design Your Ad'));

    if ($this->getCopy()) {
      $userads = $this->getItem();
      $useradsTable = Engine_Api::_()->getDbtable('userads', 'communityad');
      $packageTable = Engine_Api::_()->getDbtable('packages', 'communityad');
      $useradsName = $useradsTable->info('name');
      $packageName = $packageTable->info("name");

      $select = $useradsTable->select()
              ->from($useradsName, array('userad_id', 'cads_title'))
              ->join($packageName, $packageName . ".package_id = " . $useradsName . ".package_id", null)
              ->where("$packageName.enabled = ?", 1)
              ->where('owner_id = ?', $userads->owner_id);

      $resultAdsList = $useradsTable->fetchAll($select);

      foreach ($resultAdsList as $adVal)
        $adList[$adVal->userad_id] = ucfirst($adVal->cads_title);

      $this->addElement('Select', 'copy_ads_list', array(
          'label' => Zend_Registry::get('Zend_Translate')->_('Copy an existing ad'),
          'multiOptions' => $adList,
          'order' => '0',
          'value' => $userads->userad_id,
          'onchange' => "copyredirect()",
      ));
    }

    //ELEMENT PACKAGE
    $this->addElement('Dummy', 'package_name', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Ad Package'),
    ));
    $this->package_name->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
    $owner_id = null;
    if (!empty($this->_item))
      $owner_id = $this->_item->owner_id;

    $ownerCampaigns = Engine_Api::_()->communityad()->getUserCampaigns($owner_id);

    $campaignsList = array('0' => 'Create a New Campaign');

    foreach ($ownerCampaigns as $campaign) {
      $campaignsList[$campaign->adcampaign_id] = $campaign->name;
    }
    //ELEMENT CAMPAGIN_ID
    $this->addElement('Select', 'campaign_id', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Select Campaign'),
        'multiOptions' => $campaignsList,
        'onchange' => "updateTextFields()",
    ));

    //ELEMENT CAMPAGIN NAME
    $this->addElement('Text', 'campaign_name', array(
        'Label' => Zend_Registry::get('Zend_Translate')->_('Campaign Name'),
        'maxlength' => 100,
        'description' => Zend_Registry::get('Zend_Translate')->_('This is only for your indicative purpose and not visible to viewers.')
    ));
    $this->campaign_name->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));


    // For display, that what is enter by user in the field.
    $this->addElement('Image', 'current', array(
        'ignore' => true,
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_cardPictureImage.tpl',
                    'class' => 'form element',
                    'testing' => 'testing'
            )))
    ));



    if (!empty($is_customAs_enabled) && !empty($is_moduleAds_enabled)) {
      $changeLink = '<a href="javascript:void(0);" onclick="changOption(1);" id="change_module_1">' . Zend_Registry::get('Zend_Translate')->_('I want to create my custom ad.') . '</a><br/>' . Zend_Registry::get('Zend_Translate')->_('Select the type of content you want to advertise.');
    }

    $changeLink = sprintf($changeLink);
    $this->addElement('Select', 'create_feature', array(
        'label' => 'Content Type',
        'description' => $changeLink,
        'multiOptions' => $modulesArray,
        'onchange' => "subcontent(this.value);"
    ));
    $this->create_feature->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

//ELEMENT TITLE
    $this->addElement('Select', 'title', array(
        'RegisterInArrayValidator' => false,
        'allowEmpty' => true,
        'required' => false,
        'label' => 'Select Content',
        'description' => '',
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formModtitle.tpl',
                    'class' => 'form element'))),
    ));




    if (!empty($is_customAs_enabled) && !empty($is_moduleAds_enabled)) {
      $site_name = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title');
      $changeLink = '<a href="javascript:void(0);" onclick="changOption(0);">' . Zend_Registry::get('Zend_Translate')->_('I want to advertise something I have on ') . $site_name . '.</a><br/>' . Zend_Registry::get('Zend_Translate')->_('Example: http://www.yourwebsite.com/');
    } else {
      $changeLink = Zend_Registry::get('Zend_Translate')->_('Example: http://www.yourwebsite.com/');
    }
    //ELEMENT CADS_URL
    $this->addElement('Text', 'cads_url', array(
        'label' => 'Your URL',
        'description' => $changeLink,
        'value' => 'http://',
        'required' => true,
        'allowEmpty' => false,
        'validators' => array(
            array('NotEmpty', true),
            )));
    $this->cads_url->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
    $this->cads_url->getValidator('NotEmpty')->setMessage('Please enter a valid email address.', 'isEmpty');

    //ELEMENT NAME
    $description = '<span id="profile_address"><span id="profile_address_text"></span></span>' . Zend_Registry::get('Zend_Translate')->_(' characters left.');
    $description = sprintf($description);
    $this->addElement('Text', 'name', array(
        'label' => 'Title',
        'maxlength' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.title', 25),
        'description' => $description,
        'required' => true,
        'allowEmpty' => false,
        'validators' => array(
            array('NotEmpty', true),
            array('StringLength', false, array(1, Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.title', 25))),
        ),
        'filters' => array(
            new Engine_Filter_StringLength(array('max' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.title', 25))),
        ),
    ));

    $this->name->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

    //ELEMENT BODY
    $description1 = '<span id="profile_address1"><span id="profile_address_text1"></span></span>' . Zend_Registry::get('Zend_Translate')->_(' characters left.');
    $description1 = sprintf($description1);
    $this->addElement('Textarea', 'cads_body', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Ad Body Text'),
        'style' => 'width: 20em; height: 6em;',
        'description' => $description1,
        'required' => true,
        'allowEmpty' => false,
        'wrap' => "hard",
        'validators' => array(
            array('NotEmpty', true),
        ),
    ));
    $this->cads_body->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

    $changeLink = '<span id="remove_image_link" style="display:none;" ><a href="javascript:void(0);" onclick="removeImage();">' . Zend_Registry::get('Zend_Translate')->_('Remove uploaded image.') . '</a></span>';
    $changeLink = sprintf($changeLink);

    $this->addElement('File', 'image', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Ad Image'),
        'description' => Zend_Registry::get('Zend_Translate')->_("Browse and choose an image for your ad. Max file size allowed : ") . (int) ini_get('upload_max_filesize') . Zend_Registry::get('Zend_Translate')->_(" MB. File types allowed: jpg, jpeg, png, gif.") . "<span id='loading_image' style='display:none;'></span> " . $changeLink,
        'validators' => array(
            array('Extension', false, 'jpg,png,gif,jpeg')
        ),
        'onchange' => 'imageupload()',
    ));
    $this->image->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

    // for submit
    $this->addElement('Button', 'continue_target', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Continue'),
    ));
    $this->addElement('Hidden', 'is_custom', array(
        'value' => $is_customAs_enabled,
        'order' => 899
    ));

    $this->addElement('Hidden', 'is_module', array(
        'value' => $is_moduleAds_enabled,
        'order' => 890
    ));

    $this->addElement('Hidden', 'resource_image', array(
        'order' => 990
    ));

    $this->addElement('Hidden', 'resource_type', array(
        'order' => 989
    ));

    $this->addElement('Hidden', 'resource_id', array(
        'order' => 988
    ));

    $this->addElement('Hidden', 'temp_resource_id', array(
        'order' => 988
    ));

    $this->addElement('Hidden', 'imageName', array(
        'order' => 992
    ));

    $this->addElement('Hidden', 'imageenable', array(
        'value' => 0,
        'order' => 991
    ));

    $this->addElement('Hidden', 'like', array(
        'value' => 0,
        'order' => 999
    ));

    $this->addElement('Hidden', 'owner_id', array(
        'order' => 994
    ));
    $this->addElement('Hidden', 'package_id', array(
        'order' => 993
    ));
    $this->addElement('Hidden', 'ad_type', array(
        'order' => 992
    ));
    $this->addElement('Hidden', 'mode', array(
        'order' => 987,
        'value' => "1"
    ));

    $this->addElement('Hidden', 'preview_title', array(
        'value' => 0,
        'order' => 850
    ));


    $this->addElement('Hidden', 'preview_body', array(
        'value' => 0,
        'order' => 851
    ));

    $this->addElement('Hidden', 'subtitle_string', array(
        'value' => $sub_title_str,
        'order' => 852
    ));

    $this->addElement('Hidden', 'is_edit', array(
        'value' => $is_edit_value,
        'order' => 853
    ));

    $this->addElement('Hidden', 'content_title', array(
        'value' => '',
        'order' => 853
    ));

    $this->addElement('Hidden', 'photo_id_filepath', array(
        'value' => 0,
        'order' => 854
    ));
  }

}
