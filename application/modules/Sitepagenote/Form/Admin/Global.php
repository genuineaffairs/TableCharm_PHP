<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_Form_Admin_Global extends Engine_Form {

  // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
  public $_SHOWELEMENTSBEFOREACTIVATE = array(
      'submit_lsetting', 'environment_mode', 'include_in_package'
  );
    
  public function init() {

    $this
            ->setTitle('Global Settings')
            ->setDescription('These settings affect all members in your community.');

    $this->addElement('Text', 'sitepagenote_lsettings', array(
        'label' => 'Enter License key',
        'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagenote.lsettings'),
    ));

    $global_settings_file = APPLICATION_PATH . '/application/settings/general.php';
    if (file_exists($global_settings_file)) {
      $generalConfig = include $global_settings_file;
    } else {
      $generalConfig = array();
    }

    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
      $this->addElement('Checkbox', 'include_in_package', array(
          'label' => 'Enable notes module for the default package that was created upon installation of the "Directory / Pages Plugin". If enabled, Notes App will also be enabled for the Pages created so far under the default package.',
          'description' => 'Enable Notes Module for Default Package',
          'value' => 1,
      ));
    }
    
    if ((!empty($generalConfig['environment_mode']) ) && ($generalConfig['environment_mode'] != 'development')) {
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

    $this->addElement('Radio', 'sitepagenote_allow_image', array(
        'label' => 'Note Photos',
        'description' => 'Do you want to enable users to upload and associate photos to the notes of their Pages? (This is like having a mini-note associated with every Page-note.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagenote.allow.image', 1),
    ));

		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $description = sprintf(Zend_Registry::get('Zend_Translate')->_('The settings for the Advanced Lightbox Viewer have been moved to the SocialEngineAddOns Core Plugin. Please %1svisit here%2s to see and configure these settings.'),
    "<a href='" . $view->baseUrl() . "/admin/seaocore/settings/lightbox"."' target='_blank'>", "</a>");
    $this->addElement('Dummy', 'sitepagenote_photolightbox_show', array(
        'label' => 'Photos Lightbox Viewer',
        'description' => $description,
    ));

    $this->getElement('sitepagenote_photolightbox_show')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));

    $this->addElement('Text', 'sitepagenote_truncation_limit', array(
        'label' => 'Title Truncation Limit',
        'description' => 'What maximum limit should be applied to the number of characters in the titles of items in the widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.)',
        'required' => true,
        'maxlength' => 3,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagenote.truncation.limit', 13),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));
    
   $this->addElement('Radio', 'sitepagenote_note_show_menu', array(
        'label' => 'Notes Link',
        'description' => 'Do you want to show the Notes link on Pages Navigation Menu? (You might want to show this if Notes from Pages are an important component on your website. This link will lead to a widgetized page listing all Page Notes, with a search form for Page Notes and multiple widgets.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagenote.note.show.menu', 1),
    ));

     // Order of page note page
    $this->addElement('Radio', 'sitepagenote_order', array(
        'label' => 'Default Ordering in Page Notes listing',
        'description' => 'Select the default ordering of notes in Page Notes listing. (This widgetized page will list all Page Notes. Sponsored notes are notes created by paid Pages.)',
        'multiOptions' => array(
            1 => 'All notes in descending order of creation.',
            2 => 'All notes in alphabetical order.',
            3 => 'Featured notes followed by others in descending order of creation.',
            4 => 'Sponsored notes followed by others in descending order of creation.(If you have enabled packages.)',
            5 => 'Featured notes followed by sponsored notes followed by others in descending order of creation.(If you have enabled packages.)',
            6 => 'Sponsored notes followed by featured notes followed by others in descending order of creation.(If you have enabled packages.)',
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagenote.order', 1),
    ));

    $this->addElement('Radio', 'sitepagenote_featured', array(
        'label' => 'Making Page Notes Featured',
        'description' => 'Allow Page Admins to make notes in their Pages as featured.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagenote.featured', 1),
    ));

    $this->addElement('Text', 'sitepagenote_manifestUrl', array(
        'label' => 'Page Notes URL alternate text for "page-notes"',
        'allowEmpty' => false,
        'required' => true,
        'description' => 'Please enter the text below which you want to display in place of "pagenotes" in the URLs of this plugin.',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagenote.manifestUrl', "page-notes"),
    ));
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}

?>