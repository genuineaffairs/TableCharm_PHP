<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagedocument_Form_Admin_Global extends Engine_Form {
    
    // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
    public $_SHOWELEMENTSBEFOREACTIVATE;

    public function init() {
        $this
                ->setTitle('Global Settings')
                ->setDescription('These settings affect all members in your community.');
        
        $isPluginActivate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.navi.auth', null);
        if (!empty($isPluginActivate)) {
            $required_value = true;
            $this->_SHOWELEMENTSBEFOREACTIVATE = array('submit_lsetting', 'environment_mode', 'include_in_package');
        } else {
            $required_value = false;
            $this->_SHOWELEMENTSBEFOREACTIVATE = array('submit_lsetting', 'environment_mode', 'include_in_package', 'sitepagedocument_api_key', 'sitepagedocument_secret_key');
        }
        

        $this->addElement('Text', 'sitepagedocument_lsettings', array(
            'label' => 'Enter License key',
            'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.lsettings'),
        ));

        $isActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.isActivate', null);
        $global_settings_file = APPLICATION_PATH . '/application/settings/general.php';
        if (file_exists($global_settings_file)) {
            $generalConfig = include $global_settings_file;
        } else {
            $generalConfig = array();
        }

				//GET SCRIBD KEYS IF OTHER DOCUMENT PLUGIN IS ALREADY INSTALLED AND HAVING THESE KEYS.
				$coreApi = Engine_Api::_()->getApi('settings', 'core');
				$scribd_api_key = $coreApi->getSetting('sitepagedocument.api.key');
				$scribd_secret_key = $coreApi->getSetting('sitepagedocument.secret.key');
				if (empty($scribd_api_key) && empty($scribd_secret_key)) {
					Engine_Api::_()->seaocore()->getScribdApiKeys('sitepagedocument');
				}

        $this->addElement('Text', 'sitepagedocument_api_key', array(
            'label' => 'Scribd API Key',
            'required' => true,
            'description' => 'The Scribd API Key for your website. [Please visit the FAQ section to see how to obtain these credentials.]',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.api.key', ''),
        ));

        $this->addElement('Text', 'sitepagedocument_secret_key', array(
            'label' => 'Scribd Secret Key',
            'required' => true,
            'description' => 'The Scribd Secret Key for your website. [Please visit the FAQ section to see how to obtain these credentials.]',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.secret.key', ''),
        ));

        if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
            $this->addElement('Checkbox', 'include_in_package', array(
                'label' => 'Enable documents module for the default package that was created upon installation of the "Directory / Pages Plugin". If enabled, Documents App will also be enabled for the Pages created so far under the default package.',
                'description' => 'Enable Documents Module for Default Package',
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

        // Add submit button
        $this->addElement('Button', 'submit_lsetting', array(
            'label' => 'Activate Your Plugin Now',
            'type' => 'submit',
            'ignore' => true
        ));

        $this->addElement('Radio', 'sitepagedocument_default_visibility', array(
            'label' => 'Visibility for Page Documents',
            'description' => 'Page documents visible only on this website will be private and available only on your website, whereas the ones which will be public on Scribd.com will be available to everyone on Scribd.(Note : This setting will only apply to new Page documents created and not to the existing Page documents on the site.)',
            'multiOptions' => array(
                'public' => 'Public on Scribd.com',
                'private' => 'Only on this website'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.default.visibility', 'private'),
        ));

        $this->addElement('Radio', 'sitepagedocument_visibility_option', array(
            'description' => 'Show visibility option to users. Allow users to choose whether their Page documents will be public or private,i.e., available only on your website, or also on Scribd.com.',
            'multiOptions' => array(
                1 => ' 	Yes',
                0 => ' 	No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.visibility.option', 1),
        ));

        $this->addElement('Radio', 'sitepagedocument_carousel', array(
            'label' => 'Featured Documents Carousel',
            'description' => 'Do you want to show the carousel for featured documents in Pages in the Documents tab of Pages?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.carousel', 1),
        ));

        if (!empty($isActive)) {
            $this->addElement('Text', 'sitepagedocument_number_carousel', array(
                'label' => 'Documents in Featured Carousel',
                'description' => 'How many documents should be shown in the carousel for featured documents (value cannot be empty or zero) ?',
                'required' => $required_value,
                'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.number.carousel', 15),
                'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
                ),
            ));
        }

        $this->addElement('Radio', 'sitepagedocument_featured', array(
            'label' => 'Making Page Documents Highlighted',
            'description' => 'Allow Page Admins to make documents in their Pages as highlighted.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.featured', 1),
        ));

        $this->addElement('Radio', 'sitepagedocument_show_editor', array(
            'label' => 'WYSIWYG Editor',
            'description' => 'Allow WYSIWYG editor for description of Page documents.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.show.editor', 1),
        ));

        $filesize = (int) ini_get('upload_max_filesize') * 1024;
        $show_description = Zend_Registry::get('Zend_Translate')->_('Enter the maximum Page document file size in KB. Valid values are from 1 to %s KB.');
        $show_description = sprintf($show_description, $filesize);
        $this->addElement('Text', 'sitepagedocument_filesize', array(
            'label' => 'Maximum file size',
            'description' => $show_description,
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.filesize', 2048),
        ));

        $this->addElement('Radio', 'sitepagedocument_scribd_viewer', array(
            'label' => 'Default Document Viewer Type',
            'description' => 'Select the default viewer type for documents on your site. (Note: Secure documents can be viewed in Flash reader only because secure documents use access-management technology which is available in Flash only. Therefore, secure documents will always be viewed in Flash reader, even if you choose HTML5 reader as default.)',
            'multiOptions' => array(
                1 => 'Flash Reader',
                0 => 'HTML5 Reader'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.scribd.viewer', 1),
        ));

        $this->addElement('Radio', 'sitepagedocument_fullscreen_button', array(
            'label' => 'Fullscreen option in Flash Reader',
            'description' => "Do you want the 'Fullscreen' option to be available in the Flash Reader?",
            'multiOptions' => array(
                0 => 'Yes',
                1 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.fullscreen.button', 0),
        ));

        $this->addElement('Radio', 'sitepagedocument_flash_mode', array(
            'label' => 'Default View Mode in Flash Reader',
            'description' => 'Choose the default view mode of Flash Reader for viewing documents. Users will be able to change the mode in the viewer.',
            'multiOptions' => array(
                'list' => 'List',
                'book' => 'Book',
                'slide' => 'Slide',
                'tile' => 'Tile'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.flash.mode', 'list'),
        ));

        $this->addElement('Radio', 'sitepagedocument_disable_button', array(
            'label' => 'Disabled links in Reader',
            'description' => 'Do you want the disabled links in the Document Viewer like Download, etc to be hidden?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.disable.button', 0),
        ));

        $this->addElement('Radio', 'sitepagedocument_rating', array(
            'label' => 'Page Document Rating',
            'description' => 'Allow logged-in users to give rating to Page documents.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.rating', 1),
        ));

        $this->addElement('Radio', 'sitepagedocument_report', array(
            'label' => 'Report as inappropriate',
            'description' => 'Allow logged-in users to report Page documents as inappropriate.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.report', 1),
        ));

        $this->addElement('Radio', 'sitepagedocument_share', array(
            'label' => 'Social Sharing Buttons',
            'description' => 'Do you want to show social sharing buttons on the Page document\'s view page?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.share', 1),
        ));

        $this->addElement('Text', 'sitepagedocument_viewer_height', array(
            'label' => 'Document Viewer Height',
            'description' => 'What should be the height in pixels of the document viewer on document view page ?',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.viewer.height', 600),
        ));

        $this->addElement('Text', 'sitepagedocument_viewer_width', array(
            'label' => 'Document Viewer Width',
            'description' => 'What should be the width in pixels of the document viewer on document view page ?',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.viewer.width', 730),
        ));

        $this->addElement('Checkbox', 'sitepagedocument_licensing_option', array(
            'label' => 'Selecting this will allow users to choose the license to be associated with their Page documents.',
            'description' => 'Show license option to users',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.licensing.option', 1),
        ));

        $this->addElement('Select', 'sitepagedocument_licensing_scribd', array(
            'label' => 'License associated with Page documents',
            'description' => 'Choose the license to be associated with the Page documents on your site. The license will be shown on your site, as well as on Scribd.com.',
            'multiOptions' => array(
                'ns' => 'Unspecified - no licensing information associated',
                'by' => 'By attribution (by)',
                'by-nc' => 'By attribution, non-commercial (by-nc)',
                'by-nc-nd' => 'By attribution, non-commercial, non-derivative (by-nc-nd)',
                'by-nc-sa' => 'By attribution, non-commercial, share alike (by-nc-sa)',
                'by-nd' => 'By attribution, non-derivative (by-nd)',
                'by-sa' => 'By attribution, share alike (by-sa)',
                'pd' => 'Public domain',
                'c' => 'Copyright - all rights reserved',
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.licensing.scribd', 'ns'),
        ));
        $this->sitepagedocument_licensing_scribd->getDecorator('Description')->setOption('placement', 'append');

        $this->addElement('Checkbox', 'sitepagedocument_include_full_text', array(
            'label' => "If this option is checked, the full text for a Page document will be visible if downloading is enabled for that Page document and if the iPaper viewer is not visible because of javascript being disabled on user's browser. Also, full text on the page allows search engines to index content, and increases SEO.",
            'description' => 'Include full text on pages',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.include.full.text', 1),
        ));

        $this->addElement('Radio', 'sitepagedocument_visitor_fulltext', array(
            'label' => '',
            'description' => 'Do you want full texts for Page documents to be included on Page document pages for non-logged-in users also? (Choosing "Yes" over here will enable full text on your Page document pages to be indexed by search engines.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.visitor.fulltext', 1),
        ));

        $this->addElement('Checkbox', 'sitepagedocument_save_local_server', array(
            'label' => "Save Page documents on your site's server.",
            'description' => 'Save Page documents on server',
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.save.local.server', 1),
        ));

        $this->addElement('Radio', 'sitepagedocument_download_allow', array(
            'label' => 'Page Document downloading',
            'description' => 'Enable downloading of Page documents.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.download.allow', 1),
        ));

        $this->addElement('Radio', 'sitepagedocument_download_show', array(
            'description' => 'Show option to users during Page document creation to allow downloading of their Page documents.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.download.show', 1),
        ));

        $this->addElement('Select', 'sitepagedocument_download_format', array(
            'label' => 'Download Format',
            'description' => 'Select the format in which the downloadable Page documents will be downloaded in.',
            'multiOptions' => array(
                'pdf' => 'PDF',
                'original' => 'Original Page Document Format',
                'txt' => 'Plain Text'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.download.format', 'pdf'),
        ));

        $this->addElement('Radio', 'sitepagedocument_secure_allow', array(
            'label' => 'Secure iPaper Settings',
            'description' => 'Secure Page document iPaper cannot be embedded on other sites. Note that this is a permanent setting for a Page document during its first upload. Thus any change in this setting will only work for new Page document uploads.(Note : This setting will only apply to new Page documents created and not to the existing Page documents on the site.)',
            'multiOptions' => array(
                1 => 'Make iPaper Page documents secure. Do not allow embedding on other sites. (Note: Secure documents can be viewed in Flash reader only because secure documents use access-management technology which is available in Flash only. Therefore, secure documents will always be viewed in Flash reader, even if you choose HTML5 reader as default.)',
                0 => 'Do not make iPaper Page documents secure. Allow embedding on other sites.'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.secure.allow', 0),
        ));

        $this->addElement('Radio', 'sitepagedocument_secure_show', array(
            'description' => 'Show security option to users. Allow users to choose if their Page documents should be secure or not.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.secure.show', 1),
        ));

        $this->addElement('Radio', 'sitepagedocument_email_allow', array(
            'label' => 'Email attachment Settings',
            'description' => 'Enable emailing as attachments for Page documents on your site.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.email.allow', 1),
        ));

        $this->addElement('Radio', 'sitepagedocument_email_show', array(
            'description' => 'Allow members to choose during Page document creation if their Page documents could be emailed as attachments or not.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.email.show', 1),
        ));

        // Order of page document page
        $this->addElement('Radio', 'sitepagedocument_order', array(
            'label' => 'Default Ordering in Page Documents listing',
            'description' => 'Select the default ordering of documents in Page Documents listing. (This widgetized page will list all Page Documents. Sponsored documents are documents created by paid Pages.)',
            'multiOptions' => array(
                1 => 'All documents in descending order of creation.',
                2 => 'All documents in alphabetical order.',
                3 => 'Featured documents followed by others in descending order of creation.',
                4 => 'Sponsored documents followed by others in descending order of creation.(If you have enabled packages.)',
                5 => 'Featured documents followed by sponsored documents followed by others in descending order of creation.(If you have enabled packages.)',
                6 => 'Sponsored documents followed by featured documents followed by others in descending order of creation.(If you have enabled packages.)',
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.order', 1),
        ));

        $this->addElement('Radio', 'sitepagedocument_document_show_menu', array(
            'label' => 'Documents Link',
            'description' => 'Do you want to show the Documents link on Pages Navigation Menu? (You might want to show this if Documents from Pages are an important component on your website. This link will lead to a widgetized page listing all Page Documents, with a search form for Page Documents and multiple widgets.',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.document.show.menu', 1),
        ));

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $field = 'sitepagedocument_code_share';
        $this->addElement('Dummy', "$field", array(
            'label' => 'Social Share Widget Code',
            'description' => "<a class='smoothbox' href='". $view->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'social-share', 'field' => "$field"), 'admin_default', true) ."'>Click here</a> to add your social share code.",
            'ignore' => true,
        ));
        $this->$field->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));

        if (!empty($isActive)) {
            $this->addElement('Text', 'sitepagedocument_truncation_limit', array(
                'label' => 'Title Truncation Limit',
                'description' => 'What maximum limit should be applied to the number of characters in the titles of items in the widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.)',
                'required' => $required_value,
                'maxLength' => 3,
                'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.truncation.limit', 13),
                'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
                ),
            ));
            $this->addElement('Text', 'sitepagedocument_manifestUrl', array(
                'label' => 'Page Documents URL alternate text for "page-documents"',
                'allowEmpty' => false,
                'required' => $required_value,
                'description' => 'Please enter the text below which you want to display in place of "pagedocuments" in the URLs of this plugin.',
                'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.manifestUrl', "page-documents"),
            ));
        }

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}

?>
