<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Target.php  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Form_Admin_Target extends Engine_Form {

  public function init() {

    $not_addType = array('text', 'textarea', 'select', 'radio', 'checkbox', 'multiselect', 'multi_checkbox', 'integer', 'float', 'date', 'heading');
    $addType = array('first_name', 'last_name', 'website', 'gender', 'aim', 'city', 'country', 'twitter', 'facebook', 'political_views', 'income', 'eye_color', 'currency', 'about_me');

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->setTitle('Ads Targeting Settings')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setDescription("This powerful advertising system enables ads to be targeted to users based on specific profile fields as well as networks. Depending on whether ads targeting has been enabled for a particular ads package, advertisers will be able to target their ads to desired set of users. Below, you can choose the specific profile fields on which you want targeting to be enabled, and also whether networks based targeting should enabled.");

    $this->addElement('Dummy', 'note', array(
        'description' => '<div class="tip"><span>' . Zend_Registry::get('Zend_Translate')->_('Note: If you have selected multiple profile fields of the same type, then targeting will occur on the last created field amongst them.') . '</span></div>',
        'decorators' => array(
            'ViewHelper', array(
                'description', array('placement' => 'APPEND', 'escape' => false)
        ))
    ));

    //Pickup the dynamic values in the fields_meta table according to the profile type
    $options = Engine_Api::_()->getDBTable('options', 'communityad')->getAllProfileTypes();
    if (empty($options)) {
      return;
    }

    $generalbirthdateFlag = 0;
    $insertLable = array();
    $insertType = array();
    foreach ($options->toarray() as $opt) {
      $selectOption = Engine_Api::_()->getDBTable('metas', 'communityad')->getFields($opt['option_id']);
      // ELEMENTS OF PROFILE TYPE SPECIFY

      foreach ($selectOption as $key => $value) {
        if (in_array($value['type'], $not_addType))
          continue;
        $sTypeIndex = -2;
        $sLableIndex = -123;
        if ($value['type'] == 'birthdate') {
          $generalbirthdateFlag = 1;
          $value['lable'] = "Age";
        }
        if (in_array($value['type'], $insertType)) {
          $sTypeIndex = array_search($value['type'], $insertType);
        }
        if (in_array($value['lable'], $insertLable)) {
          $sLableIndex = array_search($value['lable'], $insertLable);
        }

        if ($sTypeIndex === $sLableIndex)
          continue;

        $insertType[] = $value['type'];
        $insertLable[] = $value['lable'];

        $this->addElement('Checkbox', $opt['option_id'] . 'check' . $key, array(
            'label' => $value['lable'] . " (" . $value['type'] . ")",
            'decorators' => array('ViewHelper', array('Label', array('placement' => 'APPEND'),
                    array('HtmlTag', array('tag' => 'div', 'style' => 'float:left;'))))
        ));


        $this->addDisplayGroup(array($opt['option_id'] . 'check' . $key), $opt['option_id'] . 'group' . $key);
        $button_group = $this->getDisplayGroup($opt['option_id'] . 'group' . $key);
        $button_group->setDecorators(array(
            'FormElements',
            'Fieldset',
            array('HtmlTag', array('tag' => 'div', 'style' => 'width:50%;float:left;margin-bottom:15px;', "title" => $value['type']))
        ));
      }
    }

    // ELEMENT TARGET BIRTHDAY
    if ($generalbirthdateFlag) {
      $this->addElement('Dummy', 'generalmptypelabel', array(
          'label' => 'Others',
      ));

      $this->addElement('Checkbox', 'target_birthday', array(
          'label' => 'Birthday',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('target.birthday', 0),
          'decorators' => array('ViewHelper', array('Label', array('placement' => 'APPEND'),
                  array('HtmlTag', array('tag' => 'div'))))
      ));
    }

    // ADVANCED TARGETING
    $addType = array('text', 'textarea', 'select', 'radio', 'checkbox', 'multiselect', 'multi_checkbox');
    $not_addType = array('first_name', 'last_name', 'website', 'gender', 'aim', 'city', 'country', 'twitter', 'facebook', 'political_views', 'income', 'eye_color', 'currency', 'birthdate', 'integer', 'float', 'date', 'heading', 'about_me', 'location', 'zip_code', 'looking_for', 'ethnicity', 'occupation', 'education_level', 'religion', 'relationship_status', 'partner_gender', 'interests');
    $display_genric_tip = 1;

    // ELEMENTS OF PROFILE TYPE SPECIFY
    $this->addElement('Dummy', 'profile_base_targeting', array(
        'label' => 'Advanced Targeting Options',
    ));
    $this->profile_base_targeting->getDecorator('Label')->setOptions(array('style' => "font-weight:bold;font-size:11pt;"));

    $this->addElement('Dummy', 'note_genric', array(
        'label' => 'Profile Types & their Profile Fields Based',
        'description' => '<div class="tip"><span>' . Zend_Registry::get('Zend_Translate')->_("Note: Using Profile Types & their Profile Fields Based - Advanced Targeting Options, advertisers will additionally be able to target their ads to users based on their Profile Types, by selecting 1 profile type to target, or set the ad to be shown to all profile types. Further, they will be able to refine targeting according to the values of generic profile fields selected by you for that profile type.<br />Please note that generic profile fields of types: Integer, Float, Date, Heading cannot be used for targeting."
        ) . '</span></div>',
        'decorators' => array('ViewHelper', array('Label', array('placement' => 'APPEND')),
            array('Description', array('placement' => 'APPEND', 'escape' => false)),
            array('HtmlTag', array('tag' => 'div', 'style' => 'float:left;')))
    ));
    $this->note_genric->getDecorator('Label')->setOptions(array('style' => "font-weight:normal; font-style: italic;"));
    foreach ($options->toarray() as $opt) {
      $selectOption = Engine_Api::_()->getDBTable('metas', 'communityad')->getFields($opt['option_id']);
      // ELEMENTS OF PROFILE TYPE SPECIFY
      $this->addElement('Dummy', $opt['option_id'] . 'mptypelabel', array(
          'label' => $opt['label'],
      ));

      foreach ($selectOption as $key => $value) {
        if (in_array($value['type'], $not_addType))
          continue;
        $this->addElement('Checkbox', $opt['option_id'] . 'check' . $key, array(
            'label' => $value['lable'] . " (" . $value['type'] . ")",
            'decorators' => array('ViewHelper', array('Label', array('placement' => 'APPEND'),
                    array('HtmlTag', array('tag' => 'div', 'style' => 'float:left; '))))
        ));


        $this->addDisplayGroup(array($opt['option_id'] . 'check' . $key), $opt['option_id'] . 'group' . $key);
        $button_group = $this->getDisplayGroup($opt['option_id'] . 'group' . $key);
        $button_group->setDecorators(array(
            'FormElements',
            'Fieldset',
            array('HtmlTag', array('tag' => 'div', 'style' => 'width:50%;float:left;margin-bottom:15px;', "title" => $value['type']))
        ));
      }
    }
    if (Engine_Api::_()->communityad()->hasNetworkOnSite()) {
      $this->addElement('Dummy', 'profile_base_targeting_others', array(
          'label' => "Networks Based",
          'description' => '<div class="tip"><span>' . Zend_Registry::get('Zend_Translate')->_("Note: Using Networks Based - Advanced Targeting Options, advertisers will additionally be able to target their ads to users based on Networks, by selecting one or more networks to target, or by choosing the ad to be shown to all networks.") . '</span></div>',
      ));
      $this->profile_base_targeting_others->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
      $this->profile_base_targeting_others->getDecorator('Label')->setOptions(array('style' => "font-weight:normal; font-style: italic;"));
      $this->addElement('Checkbox', 'community_target_network', array(
          'label' => 'Enable Networks based targeting.',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('community.target.network', 0),
          'decorators' => array('ViewHelper', array('Label', array('placement' => 'APPEND'),
                  array('HtmlTag', array('tag' => 'div'))))
      ));
    }
    // ELEMENT SUBMIT
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
    ));
  }

}