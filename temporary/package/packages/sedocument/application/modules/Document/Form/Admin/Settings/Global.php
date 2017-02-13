<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Document_Form_Admin_Settings_Global extends Engine_Form
{
  public function init()
  {
    $this
    ->setTitle('Global Settings')
    ->setDescription('These settings affect all members in your community.');

		$document_lsettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.lsettings');
		if ( !empty($document_lsettings) ) {
			Engine_Api::_()->getApi('settings', 'core')->setSetting('document.controllersettings', $document_lsettings);
		}

   	$this->addElement('Text', 'document_controllersettings', array(
      'label' => 'Enter License key',
      'required' => true,
      'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.controllersettings'),
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
    
    //GET SCRIBD KEYS IF OTHER DOCUMENT PLUGIN IS ALREADY INSTALLED AND HAVING THESE KEYS.
    $coreApi = Engine_Api::_()->getApi('settings', 'core');
    $scribd_api_key = $coreApi->getSetting('document.api.key');
    $scribd_secret_key = $coreApi->getSetting('document.secret.key');
    if (empty($scribd_api_key) && empty($scribd_secret_key)) {
      Engine_Api::_()->seaocore()->getScribdApiKeys('document');
    }

    $this->addElement('Text', 'document_api_key', array(
      'label' => 'Scribd API Key',
       'required' => true,
      'description' => 'The Scribd API Key for your website. [Please visit the FAQ section to see how to obtain these credentials.]',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.api.key', ''),
    ));
    
    $this->addElement('Text', 'document_secret_key', array(
      'label' => 'Scribd Secret Key',
    	'required' => true,
      'description' => 'The Scribd Secret Key for your website. [Please visit the FAQ section to see how to obtain these credentials.]',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.secret.key', ''),
    ));

		$this->addElement('Button', 'submit_lsetting', array(
			'label' => 'Activate Your Plugin Now',
			'type' => 'submit',
			'ignore' => true
		));
   
    $this->addElement('Radio', 'document_default_visibility', array(
      'label' => 'Visibility for documents',
      'description' => 'Documents visible only on this website will be private and available only on your website, whereas the ones which will be public on Scribd.com will be available to everyone on Scribd.(Note : This setting will only apply to new documents created and not to the existing documents on the site.)',
      'multiOptions' => array(
        'public'  => 'Public on Scribd.com. (Such documents will be downloadable and emailable as attachments always.)',
        'private' => 'Only on this website'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.default.visibility', 'private'),
    ));
    
    $this->addElement('Radio', 'document_visibility_option', array(
      'description' => 'Show visibility option to users. Allow users to choose whether their documents will be public or private,i.e., available only on your website, or also on Scribd.com. (Note : If enabled, this option will only be visible during document creation and not during editing.)',
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.visibility.option', 1),
    ));
    
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->addElement('Radio', 'document_network', array(
        'label' => 'Browse by Networks',
        'description' => "Do you want to show documents according to viewer's network if he has selected any? (If set to no, all the documents will be shown.)",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'onclick' => 'showDefaultNetwork(this.value)',
        'value' => $settings->getSetting('document.network', 0),
    ));

    $this->addElement('Radio', 'document_default_show', array(
        'label' => 'Set Only My Networks as Default in search',
        'description' => 'Do you want to set "Only My Networks" option as default for Show field in the search form widget? (This widget appears on the documents browse and home pages, and enables users to search and filter documents.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'onclick' => 'showDefaultNetworkType(this.value)',
        'value' => $settings->getSetting('document.default.show', 0),
    ));

    $this->addElement('Radio', 'document_networks_type', array(
        'label' => 'Network selection for Documents',
        'description' => "You have chosen that viewers should only see Documents of their network(s). How should a Document's network(s) be decided?",
        'multiOptions' => array(
            0 => "Document Owner's network(s) [If selected, only members belonging to document owner's network(s) will see the Documents.]",
            1 => "Selected Networks [If selected, document owner will be able to choose the networks of which members will be able to see their Document.]"
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.networks.type', 0),
    ));    
    
		$this->addElement('Radio', 'document_show_editor', array(
    	'label' => 'WYSIWYG Editor',
			'description' => 'Allow WYSIWYG editor for description of documents.',
      'multiOptions' => array(
				1 => 'Yes',
				0 => 'No'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.show.editor', 1),
    ));

		$this->addElement('Radio', 'document_rating', array(
    	'label' => 'Document Rating',
      'description' => 'Logged-in users can rate documents.',
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.rating', 1),
    ));

    $this->addElement('Radio', 'document_report', array(
    	'label' => 'Report as inappropriate',
      'description' => 'Allow logged-in users to report documents as inappropriate.',
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.report', 1),
    ));
    
    $this->addElement('Radio', 'document_share', array(
    	'label' => 'Document Sharing',
      'description' => 'Logged-in users can share documents within the site.',
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.share', 1),
    ));

		$this->addElement('Radio', 'document_button_share', array(
    	'label' => 'Social Sharing Buttons',
      'description' => 'Do you want to show social sharing buttons on the main document page ?',
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.button.share', 1),
    ));

    $this->addElement('Radio', 'document_categorywithslug', array(
        'label' => 'Slug URL',
        'description' => "Do you want to replace blank-space in your category name by '-' in URL?",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.categorywithslug', 1),
    ));

    $this->addElement('Radio', 'document_viewer', array(
        'label' => 'Default Document Viewer Type',
        'description' => 'Select the default viewer type for documents on your site. (Note: Secure documents can be viewed in Flash reader only because secure documents use access-management technology which is available in Flash only. Therefore, secure documents will always be viewed in Flash reader, even if you choose HTML5 reader as default. To see more about secure documents and the setting for them, please go to Member Level Settings in this plugin.)',
        'multiOptions' => array(
						1 => 'Flash Reader',
            0 => 'HTML5 Reader'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.viewer', 1),
    ));

    $this->addElement('Radio', 'document_fullscreen_button', array(
        'label' => 'Fullscreen option in Flash Reader',
        'description' => "Do you want the 'Fullscreen' option to be available in the Flash Reader?",
        'multiOptions' => array(
						0 => 'Yes',
            1 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.fullscreen.button', 0),
    ));

    $this->addElement('Radio', 'document_flash_mode', array(
        'label' => 'Default View Mode in Flash Reader',
        'description' => 'Choose the default view mode of Flash Reader for viewing documents. Users will be able to change the mode in the viewer.',
        'multiOptions' => array(
						'list' => 'List',
            'book' => 'Book',
            'slide' => 'Slide',
						'tile' => 'Tile'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.flash.mode', 'list'),
    ));

    $this->addElement('Radio', 'document_disable_button', array(
        'label' => 'Disabled links in Reader',
        'description' => 'Do you want the disabled links in the Document Viewer like Download, etc to be hidden?',
        'multiOptions' => array(
						1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.disable.button', 0),
    ));

    $this->addElement('Radio', 'document_thumbs', array(
      'label' => "Document Thumbnails",
			'description' => "Do you want to save the thumbnails of documents on your site's server? (Note: If enabled, this setting will import the thumbnails of existing documents on site from Scribd, when the documents are viewed. Changing this to 'No' will not affect those documents for which thumbnails are already on site's server.)",
        'multiOptions' => array(
						0 => "No, the source of thumbnail images will be at Scribd.",
            1 => "Yes, save the document thumbnail images on the site's server."
        ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.thumbs', 0),
    ));

    $this->addElement('Checkbox', 'document_licensing_option', array(
      'label' => 'Selecting this will allow users to choose the license to be associated with their documents.',
			'description' => 'Show license option to users',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.licensing.option', 1),
    ));
    
    $this->addElement('Select', 'document_licensing_scribd', array(
      'label' => 'License associated with documents',
			'description' => 'Choose the license to be associated with the documents on your site. The license will be shown on your site, as well as on Scribd.com.',
      'multiOptions' => array(
        'ns' => 'Unspecified - no licensing information associated',
        'by'   => 'By attribution (by)',
        'by-nc'   => 'By attribution, non-commercial (by-nc)',
        'by-nc-nd'   => 'By attribution, non-commercial, non-derivative (by-nc-nd)',
        'by-nc-sa'   => 'By attribution, non-commercial, share alike (by-nc-sa)',
        'by-nd'   => 'By attribution, non-derivative (by-nd)',
        'by-sa'   => 'By attribution, share alike (by-sa)',
        'pd'   => 'Public domain',
        'c'   => 'Copyright - all rights reserved',
      ),
     'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.licensing.scribd', 'ns'),
    ));
		$this->document_licensing_scribd->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Checkbox', 'document_include_full_text', array(
      'label' => "If this option is checked, the full text for a document will be visible if downloading is enabled for that document and if the iPaper viewer is not visible because of javascript being disabled on user's browser. Also, full text on the page allows search engines to index content, and increases SEO.",
		  'description' => 'Include full text on pages',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.include.full.text', 1),
    ));

		$this->addElement('Radio', 'document_visitor_fulltext', array(
			'label' => '',
			'description' => ' Do you want full texts for documents to be included on document pages for non-logged-in users also? (Choosing "Yes" over here will enable full text on your document pages to be indexed by search engines.)',
			'multiOptions' => array(
				1 => 'Yes',
				0 => 'No'
			),
			'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.visitor.fulltext', 1),
		));

    $this->addElement('Checkbox', 'document_save_local_server', array(
      'label' => "Save documents on your site's server",
			'description' => 'Save documents on server',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.save.local.server', 1),
    ));

    $this->addElement('Text', 'document_manifestUrlP', array(
        'label' => "Document pages URL alternate text for 'documents'",
        'allowEmpty' => false,
        'required' => true,
        'description' => "Please enter the text below which you want to display in place of 'documents' in the URLs of this plugin.",
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.manifestUrlP', "documents"),
    ));

    $this->addElement('Text', 'document_manifestUrlS', array(
        'label' => "Documents pages URL alternate text for 'document'",
        'allowEmpty' => false,
        'required' => true,
        'description' => "Please enter the text below which you want to display in place of 'document' in the URLs of this plugin.",
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.manifestUrlS', "document"),
    ));
    
    $this->addElement('Text', 'document_page', array(
      'label' => 'Documents Per Page',
      'description' => 'Enter the number of documents to be shown per page on the Documents listing pages. (Enter a number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.page', 10),
    ));

    $social_share_default_code = '<div class="addthis_toolbox addthis_default_style ">
		<a class="addthis_button_preferred_1"></a>
		<a class="addthis_button_preferred_2"></a>
		<a class="addthis_button_preferred_3"></a>
		<a class="addthis_button_preferred_4"></a>
		<a class="addthis_button_preferred_5"></a>
		<a class="addthis_button_compact"></a>
		<a class="addthis_counter addthis_bubble_style"></a>
		</div>
		<script type="text/javascript">
		var addthis_config = {
							services_compact: "facebook, twitter, linkedin, google, digg, more",
							services_exclude: "print, email"
		}
		</script>
		<script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js"></script>';

    $this->addElement('Textarea', 'document_code_share', array(
				'label' => 'Social Share Widget Code',
        'description' => "Personalize the code for social sharing buttons by adding your own code generated from: <a href='http://www.addthis.com/' target='_blank'>http://www.addthis.com/</a>. If you do not want to show these buttons, then you can simply empty this field.",
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.code.share', $social_share_default_code),
    ));
		$this->document_code_share->addDecorator('Description', array('placement' => 'PREPEND','class' => 'description', 'escape' => false));

		$this->addElement('Radio', 'document_title_truncation', array(
			'label' => 'Show Full Title Without Truncation',
			'description' => 'Do you want to show the full document title without truncation on various pages like widgets, Browse Documents, My Documents etc..',
			'multiOptions' => array(
				1 => 'Yes',
				0 => 'No'
			),
			'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.title.truncation', 0),
		));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
