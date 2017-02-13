<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Form_Admin_Global extends Engine_Form {

  public function init() {
    $this
            ->setTitle('Global Settings')
            ->setDescription('These settings affect all members in your community.');

    $this->addElement('Text', 'communityad_lsettings', array(
        'label' => 'Enter License key',
        'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.lsettings'),
    ));

    if( APPLICATION_ENV == 'production' ) {
	    $this->addElement('Checkbox', 'environment_mode', array(
		    'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few pages of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching prevents CSS of new plugins to be rendered immediately after installation.)',
		    'description' => 'System Mode',
		    'value' => 1,
	    )); 
    }else {
	    $this->addElement('Hidden', 'environment_mode', array('order' => 990, 'value' => 0));
    }

    $this->addElement('Button', 'submit_lsetting', array(
        'label' => 'Activate Your Plugin Now',
        'type' => 'submit',
        'ignore' => true
    ));


    // Adding Settings of "Community Ads".
    $this->addElement( 'Dummy' , 'dummy_communityad_title' , array ('label' => 'Community Ads Settings')) ;

    // Title of Communityad.
    $this->addElement('Text', 'communityad_title', array(
        'label' => 'Community Ads Title',
        'allowEmpty' => false,
        'required' => true,
        'description' => 'Enter the default title for Community Ads which will be displayed to the users of your site on their Ad Board.',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.title', 'Community Ads'),
    ));


    // Image Width
    $this->addElement('Text', 'ad_image_width', array(
        'label' => 'Ad Image Width',
        'description' => "Enter the maximum width in pixels of the images that are there in the Ads. (Note that this must be less than the Ad Width.)",
        'attribs' => array('style' => 'width:80px; '),
        'maxlength' => 4,
        'allowEmpty' => false,
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.image.width', 120),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '4')),
            )));

    // Image Height
    $this->addElement('Text', 'ad_image_hight', array(
        'label' => 'Ad Image Height',
        'description' => "Enter the maximum height in pixels of the images that are there in the Ads.",
        'attribs' => array('style' => 'width:80px; '),
        'maxlength' => 4,
        'allowEmpty' => false,
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.image.hight', 90),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '4')),
            )));
    // ADVERTISMENT TITLE LENGTH
    $this->addElement('Text', 'ad_char_title', array(
        'label' => 'Ad Title Length',
        'maxlength' => 3,
        'allowEmpty' => false,
        'required' => true,
        'description' => 'Enter the maximum character length of Ad titles.',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.title', 25),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '3')),
        )
    ));
    // ADVERTISEMENT BODY LENGTH
    $this->addElement('Text', 'ad_char_body', array(
        'label' => 'Ad Body Length',
        'maxlength' => 3,
        'allowEmpty' => false,
        'required' => true,
        'description' => 'Enter the maximum character length of Ad bodies / descriptions.',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.char.body', 135),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '3')),
        )
    ));

    // NUMBER OF ADVERTISMENT ON BLOCK
//    $this->addElement('Text', 'widgets_limit', array(
//        'label' => 'Default Ads per Block',
//        'maxlength' => 3,
//        'allowEmpty' => false,
//        'required' => true,
//        'description' => 'Enter the default value for maximum number of ads to be displayed per ad block. (If you create an ad block from the "Manage Ad Blocks" section, then you will be able to set a custom limit for that ad block.)',
//        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('widgets.limit', 3),
//        'filters' => array(
//            'StripTags',
//            new Engine_Filter_Censor(),
//            new Engine_Filter_StringLength(array('max' => '3')),
//        )
//    ));

    // AD DISPLAY AJAX BASED OR NOT
//    $this->addElement('Radio', 'ad_ajax_based', array(
//        'label' => 'Default Ajax Based Display of Ad Blocks',
//        'description' => 'Do you want to enable ajax based display of ad blocks as default?  (If you create an ad block from the "Manage Ad Blocks" section, then you will be able to select it for that ad block from "Manage Ad Blocks.")',
//        'multiOptions' => array(
//            1 => 'Yes',
//            0 => 'No'
//        ),
//        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.ajax.based', 0),
//    ));

    // footer ads widget on the adboard page
//    $this->addElement('Radio', 'adboard_footer', array(
//        'label' => 'Ads widget on Ad Board',
//        'description' => 'Do you want to show the Ads widget on Ad Board page?',
//        'multiOptions' => array(
//            0 => 'No',
//            1 => 'Yes',
//        ),
//        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('adboard.footer', 0),
//    ));

    // Create ad link on the adboard page and other ad block widgets
    $this->addElement('Radio', 'adblock_create_link', array(
        'label' => 'Create an Ad Link for Visitors in Ad Blocks',
        'description' => 'Do you want to show "Create an Ad" links in Ad Blocks to non-logged-in visitors? (If a non-logged-in visitor clicks on a "Create an Ad" link, he will be asked to login first.)',
        'multiOptions' => array(
            0 => 'No',
            1 => 'Yes',
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('adblock.create.link', 1),
    ));

    // AD block width
    $this->addElement('Text', 'ad_block_width', array(
        'label' => 'Ad Width',
        'description' => "Enter the width in pixels of the Ads. (While setting this width, please consider the column dimensions of your site’s theme where the Ads will be placed. The appropriate Ad Width for side-column positioning in default SocialEngine template is: 150px.)",
        'attribs' => array('style' => 'width:80px; '),
        'maxlength' => 4,
        'allowEmpty' => false,
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.block.width', 150),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '4')),
            )));

    // Hide Custom Ad Target URL
    $this->addElement('Radio', 'custom_ad_url', array(
        'label' => 'Custom Ad Target URL',
        'description' => 'Do you want the domain of Ad Target URL to be displayed in Custom Ads? (Choosing "No" over here will hide the URL that comes below the Ad Title in Custom Ads.)',
        'multiOptions' => array(
            0 => 'No',
            1 => 'Yes',
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('custom.ad.url', 0),
    ));

    
    
    $isModuleEnabled = Engine_Api::_()->communityad()->isModuleEnabled("communityadsponsored");
    if( !empty($isModuleEnabled) ) {
      // Adding Settings of "Sponsored Story".
      $this->addElement( 'Dummy' , 'dummy_story_title' , array ('label' => 'Sponsored Stories Settings')) ;

      // ADVERTISMENT TITLE LENGTH
      $this->addElement('Text', 'story_char_title', array(
          'label' => 'Sponsored Story Content Title Length',
          'maxlength' => 3,
          'allowEmpty' => false,
          'required' => true,
          'description' => 'Enter the maximum character length of the titles of main content entities of Sponsored Stories.',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('story.char.title', 35),
          'filters' => array(
              'StripTags',
              new Engine_Filter_Censor(),
              new Engine_Filter_StringLength(array('max' => '3')),
          )
      ));
    }


    // Adding Settings of "Sponsored Story".
    $this->addElement( 'Dummy' , 'dummy_general_title' , array ('label' => 'General Settings')) ;

    // ADVERTISING LINK
    $this->addElement('Radio', 'ad_show_menu', array(
        'label' => 'Advertising Link',
        'description' => 'Select the location of the link for Ad Board page.',
        'multiOptions' => array(
            3 => 'Main Navigation Menu',
            2 => 'Mini Navigation Menu',
            1 => 'Footer Menu',
            0 => 'Member Home Page Left side Navigation'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.show.menu', 3),
    ));
	// Select the type of advertisment. If unselect any of checkbox then we will not show that type will be disabled.
	
//     $getAdTypeArray = Engine_Api::_()->getItemTable('communityad_adtype')->getAdType();
// 
//     $this->addElement('MultiCheckbox', 'communityad_ad_type', array(
//         'label' => 'Ad Types',
//         'description' => "Select the Ad types that you want to be available on your website via this “Advertisements / Community Ads Plugin”. (Users of your site will be able to create advertisements of the below selected ad types. If you unselect an ad type below, the ads of that type that have already been created will be displayed till their expiry.)",
//         'multiOptions' => $getAdTypeArray['multiOptions'],
//         'value' => $getAdTypeArray['value']
//     ));

    // NUMBER OF ADVERTISMENT ON AD BOARD
    $this->addElement('Text', 'ad_board_limit', array(
        'label' => 'Ads in Ad Board',
        'maxlength' => 3,
        'allowEmpty' => false,
        'required' => true,
        'description' => 'Enter the maximum number of ads to be displayed on the Ad Board Page.',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.board.limit', 25),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '3')),
        )
    ));
    
    $this->addElement('Select', 'ad_statistics_limit', array(
        'label' => 'Duration for Statistics & Reports',
        'description' => 'Select from below the duration for which users will be able to see Statistics and Reports for their advertisements on your site. [Note: All the statistics & reports before the selected duration will be deleted automatically and will not be recoverable.]',
        'multiOptions' => array(1 => '1 Year',2=>'2 Years',3 => '3 Years',4 => '4 Years',5=>'5 Years'
            
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ad.statistics.limit', 3)
        
    ));

    // ad cancel on adboard and other ad blocks
    $this->addElement('Radio', 'adcancel_enable', array(
        'label' => 'Report an Ad',
        'description' => 'Do you want to allow members to cancel or report an ad? (If set to no, the cross mark(X) will not appear on the ads.)',
        'multiOptions' => array(
            0 => 'No',
            1 => 'Yes',
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('adcancel.enable', 1),
    ));


     // show "Ad Board" menu in "Advertising Main Navigation Menu
    $this->addElement('Radio', 'show_adboard', array(
        'label' => 'Show "Ad Board" link in main Advertising navigation bar',
        'description' => 'Do you want to show "Ad Board" link in "Advertising Main Navigation Menu" bar ?',
        'multiOptions' => array(           
            1 => 'Yes',
            0 => 'No',
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('show.adboard', 1),
    ));
    
    $this->addElement('Text', 'ad_saleteam_con', array(
        'label' => 'Contact Number(s) of Sales Team',
        'description' => 'Specify the contact number(s) for receiving queries regarding advertisements on your site. These numbers will be displayed on the "Contact Sales Team" page of the "Help & Learn More" section. (Separate multiple numbers by commas.)',
        'value' => Engine_Api::_()->getApi('settings', 'core')->ad_saleteam_con,
    ));

    $this->addElement('Text', 'ad_saleteam_email', array(
        'label' => 'Email Address(es) of Sales Team',
        'description' => 'Specify the email address(es) for receiving queries regarding advertisements on your site. These addresses will be displayed on the "Contact Sales Team" page of the "Help & Learn More" section. (Separate multiple addresses by commas.)',
        'value' => Engine_Api::_()->getApi('settings', 'core')->ad_saleteam_email,
    ));


    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $localeObject = Zend_Registry::get('Locale');
    $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
    $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
    // Element: currency
    $this->addElement('Dummy', 'currency', array(
        'label' => 'Currency',
        'description' => "<b>" . $currencyName . "</b> <br /> <a href='" . $view->url(array('module' => 'payment', 'controller' => 'settings'), 'admin_default', true) . "' target='_blank'>" . Zend_Registry::get('Zend_Translate')->_('edit currency') . "</a>",
    ));
    $this->getElement('currency')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));

    // Element: benefit
    $this->addElement('Radio', 'advertise_benefit', array(
        'label' => 'Payment Status for Ad Activation',
        'description' => "Do you want to activate advertisements immediately after payment, before the payment passes the gateways' fraud checks? This may take any time from 20 minutes to 4 days, depending on the circumstances and the gateway. (Note: If you want to manually activate ads, then you can set this while creating an ad package.)",
        'multiOptions' => array(
            'all' => 'Activate advertisements immediately.',
            'some' => 'Activate if member has an existing successful transaction, wait if this is their first.',
            'none' => 'Wait until the gateway signals that the payment has completed successfully.',
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('advertise.benefit', 'all'),
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}