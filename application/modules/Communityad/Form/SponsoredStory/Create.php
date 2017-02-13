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
class Communityad_Form_SponsoredStory_Create extends Engine_Form {

  public $_error = array();
  protected $_item;
  protected $_copy;
  protected $_packageId;

  public function getPackageId() {
    return $this->_packageId;
  }

  public function setPackageId($id) {
    $this->_packageId = $id;
    return $this;
  }

  public function init() {
    parent::init();

    $this->setName('titleForm');
    $this->setTitle(Zend_Registry::get('Zend_Translate')->_('Design Your Sponsored Story'));
    $this->setAttribs(array(
        'id' => 'create_sponsoredstory',
        'class' => 'global_form'
    ));
    //ELEMENT PACKAGE
    $this->addElement('Dummy', 'package_name', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Ad Package'),
    ));
     $this->package_name->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
    $owner_id = null;
    $ownerCampaigns = Engine_Api::_()->communityad()->getUserCampaigns($owner_id);

    $campaignsList = array('0' => 'Create a New Campaign');

    foreach ($ownerCampaigns as $campaign) {
      $campaignsList[$campaign->adcampaign_id] = $campaign->name;
    }
    //ELEMENT CAMPAGIN_ID
    $this->addElement('Select', 'campaign_id', array(
        'label' => Zend_Registry::get('Zend_Translate')->_('Select Campaign'),
        'multiOptions' => $campaignsList,
        'onchange' => "javascript:updateTextFields()",
    ));

    //ELEMENT CAMPAGIN NAME
    $this->addElement('Text', 'campaign_name', array(
        'Label' => Zend_Registry::get('Zend_Translate')->_('Campaign Name'),
        'maxlength' => 100,
        'description' => Zend_Registry::get('Zend_Translate')->_('This is only for your indicative purpose and not visible to viewers.')
    ));
    $this->campaign_name->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));


//    $this->addElement('Select', 'story_type', array(
//        'label' => 'Story Type',
//        'description' => 'Select Story Type',
//        'multiOptions' => array(1 => 'Page Like', 2 => 'Page Post'),
//        'onchange' => "getStory(this.value);"
//    ));
    $this->addElement('Hidden', 'story_type', array(
        'value' => 1,
        'order' => 995
    ));


    $modulesArray = $resource_value_array = array();
    $modulesArray = Engine_Api::_()->communityad()->enabled_module_content($this->getPackageId());
    $this->addElement('Select', 'resource_type', array(
        'label' => 'Content Type',
        'description' => 'Select the type of content you want to advertise.',
        'multiOptions' => $modulesArray[1],
        'onchange' => "getResource(this.value, 1);"
    ));
    $this->resource_type->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

//ELEMENT TITLE
    $this->addElement('Select', 'resource_id', array(
        'RegisterInArrayValidator' => false,
        'allowEmpty' => true,
        'required' => false,
        'label' => 'Select Content',
        'description' => '',
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_partialSponsoredStoryCreate.tpl',
                    'class' => 'form element'))),
    ));


    $this->addElement('Text', 'name', array(
        'Label' => Zend_Registry::get('Zend_Translate')->_('Name'),
        'maxlength' => 100,
        'description' => Zend_Registry::get('Zend_Translate')->_('This is only for your indicative purpose and not visible to viewers.')
    ));
    $this->name->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

    // For display, that what is enter by user in the field.
    $this->addElement('Image', 'preview', array(
        'ignore' => true,
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_sponsoredStoryPreview.tpl',
                    'class' => 'form element',
                    'testing' => 'testing'
            )))
    ));
    $this->addElement('Hidden', 'owner_id', array(
        'order' => 994
    ));

    $this->addElement('Hidden', 'mode', array(
        'order' => 994,
        'value' => 1
    ));


    $this->addElement('Hidden', 'package_id', array(
        'order' => 993
    ));
    $this->addElement('Hidden', 'ad_type', array(
        'order' => 992
    ));
    $this->addElement('Hidden', 'temp_resource_type', array(
        'order' => 994
    ));
    $this->addElement('Hidden', 'temp_resource_id', array(
        'order' => 995
    ));
    $this->addElement('Hidden', 'flag', array(
        'order' => 996
    ));
    $this->addElement('Hidden', 'editFlag', array(
        'order' => 997
    ));
    $this->addElement('Hidden', 'editTitle', array(
        'order' => 998
    ));
//     $this->addElement('Hidden', 'editModName', array(
//         'order' => 998
//     ));
//     $this->addElement('Hidden', 'editModId', array(
//         'order' => 998
//     ));
    /*  $this->addElement('Button', 'continue_next', array(
      'Label' => Zend_Registry::get('Zend_Translate')->_('Continue'),
      //'type' => 'submit',
      //'ignore' => true,
      )); */
  }

}
