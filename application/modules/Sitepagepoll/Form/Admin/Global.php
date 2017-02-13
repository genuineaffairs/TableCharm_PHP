<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagepoll_Form_Admin_Global extends Engine_Form {

  // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
  public $_SHOWELEMENTSBEFOREACTIVATE = array(
      'submit_lsetting', 'environment_mode', 'include_in_package'
  );
    
  public function init() {
    $this
            ->setTitle('Global Settings')
            ->setDescription('These settings affect all members in your community.');

    $this->addElement('Text', 'sitepagepoll_lsettings', array(
        'label' => 'Enter License key',
        'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.lsettings'),
    ));

    $global_settings_file = APPLICATION_PATH . '/application/settings/general.php';
    if (file_exists($global_settings_file)) {
      $generalConfig = include $global_settings_file;
    } else {
      $generalConfig = array();
    }

    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $this->addElement('Checkbox', 'include_in_package', array(
          'label' => ' Enable polls module for the default package that was created upon installation of the "Directory / Pages Plugin". If enabled, Polls App will also be enabled for the Pages created so far under the default package.',
          'description' => 'Enable Polls Module for Default Package',
          'value' => 1,
      ));
    }
    
    if ( ( !empty($generalConfig['environment_mode']) ) && ($generalConfig['environment_mode'] != 'development') ) {
      $this->addElement('Checkbox', 'environment_mode', array(
          'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few pages of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching prevents CSS of new plugins to be rendered immediately after installation.)',
          'description' => 'System Mode',
//          'value' => 1,
      ));
    } else {
      $this->addElement('Hidden', 'environment_mode', array('order' => 990, 'value' => 0));
    }

    $this->addElement('Button', 'submit_lsetting', array(
        'label' => 'Activate Your Plugin Now',
        'type' => 'submit',
        'ignore' => true
    ));
    
    $this->addElement('Text', 'sitepagepoll_maxoptions', array(
        'label' => 'Maximum Options',
        'description' => 'How many possible poll answers do you want to allow in Page poll creation?',
        'required' => true,
        'empty' => false,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.maxoptions', 4),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));
    
    $this->addElement('Radio', 'sitepagepoll_poll_show_menu', array(
        'label' => 'Polls Link',
        'description' => 'Do you want to show the Polls link on Pages Navigation Menu? (You might want to show this if Polls from Pages are an important component on your website. This link will lead to a widgetized page listing all Page Polls, with a search form for Page Polls and multiple widgets.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.poll.show.menu', 1),
    ));

     // Order of page poll page
    $this->addElement('Radio', 'sitepagepoll_order', array(
        'label' => 'Default Ordering in Page Polls listing',
        'description' => 'Select the default ordering of polls in Page Polls listing. (This widgetized page will list all Page Polls. Sponsored polls are polls created by paid Pages.)',
        'multiOptions' => array(
            1 => 'All polls in descending order of creation.',
            2 => 'All polls in alphabetical order.',
            3 => 'Sponsored polls followed by others in descending order of creation.(If you have enabled packages.)',
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.order', 1),
    ));

    $this->addElement('Radio', 'sitepagepoll_canchangevote', array(
        'label' => 'Change Vote',
        'description' => 'Do you want to permit your members to change their vote?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No',
        ),
        'value' => (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.canchangevote', false),
    ));

    $this->addElement('Text', 'sitepagepoll_title_truncation', array(
        'label' => 'Title Truncation Limit',
        'description' => 'What maximum limit should be applied to the number of characters in the titles of items in the widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.)',
        'maxlength' => 3,
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.title.truncation', 13),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

		$this->addElement('Text', 'sitepagepoll_manifestUrl', array(
        'label' => 'Page Polls URL alternate text for "page-polls"',
        'allowEmpty' => false,
        'required' => true,
        'description' => 'Please enter the text below which you want to display in place of "pagepolls" in the URLs of this plugin.',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.manifestUrl', "page-polls"),
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}
?>