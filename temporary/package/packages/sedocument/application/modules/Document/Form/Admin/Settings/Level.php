<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Level.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Document_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
		parent::init();

		$visibility = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.default.visibility', 'private');
		$option_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.visibility.option', 1);

		$this
			->setTitle('Member Level Settings')
			->setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.");

		$this->addElement('Radio', 'view', array(
			'label' => 'Allow Viewing of Documents?',
			'description' => 'Do you want to let members view documents? If set to no, some other settings on this page may not apply.',
			'multiOptions' => array(
				2 => 'Yes, allow viewing of all documents, even private ones.',
				1 => 'Yes, allow viewing of documents.',
				0 => 'No, do not allow documents to be viewed.',
			),
			'value' => ( $this->isModerator() ? 2 : 1 ),
		));

		if( !$this->isModerator() ) {
			unset($this->view->options[2]);
		}

		if( !$this->isPublic() ) {
			$this->addElement('Radio', 'create', array(
				'label' => 'Allow Creation of Documents?',
				'description' => 'Do you want to let members create documents? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view documents, but only want certain levels to be able to create documents.',
				'multiOptions' => array(
					1 => 'Yes, allow creation of documents.',
					0 => 'No, do not allow documents to be created.'
				),
				'value' => 1,
			));

			$this->addElement('Radio', 'edit', array(
				'label' => 'Allow Editing of Documents?',
				'description' => 'Do you want to let members edit documents? If set to no, some other settings on this page may not apply.',
				'multiOptions' => array(
					2 => 'Yes, allow members to edit all documents.',
					1 => 'Yes, allow members to edit their own documents.',
					0 => 'No, do not allow members to edit their documents.',
				),
				'value' => ( $this->isModerator() ? 2 : 1 ),
			));
			if( !$this->isModerator() ) {
				unset($this->edit->options[2]);
			}

			$this->addElement('Radio', 'delete', array(
				'label' => 'Allow Deletion of Documents?',
				'description' => 'Do you want to let members delete documents? If set to no, some other settings on this page may not apply.',
				'multiOptions' => array(
					2 => 'Yes, allow members to delete all documents.',
					1 => 'Yes, allow members to delete their own documents.',
					0 => 'No, do not allow members to delete their documents.',
				),
				'value' => ( $this->isModerator() ? 2 : 1 ),
			));
			if( !$this->isModerator() ) {
				unset($this->delete->options[2]);
			}

			$this->addElement('Radio', 'approved', array(
				'label' => 'Documents Approval Moderation',
				'description' => 'Do you want new documents to be automatically approved?',
				'multiOptions' => array(
					1 => 'Yes, automatically approve documents.',
					0 => 'No, site admin approval will be required for all documents.'
				),
				'value' => 1,
			));

			$this->addElement('Radio', 'featured', array(
				'label' => 'Documents Featured Moderation',
				'description' => 'Do you want new documents to be automatically made featured?',
				'multiOptions' => array(
					1 => 'Yes, automatically make documents featured.',
					0 => 'No, site admin will be making documents featured.'
				),
				'value' => 0,
			));

			$this->addElement('Radio', 'sponsored', array(
				'label' => 'Documents Sponsored Moderation',
				'description' => 'Do you want new documents to be automatically made sponsored?',
				'multiOptions' => array(
					1 => 'Yes, automatically make documents sponsored.',
					0 => 'No, site admin will be making documents sponsored.'
				),
				'value' => 0,
			));

			$this->addElement('Radio', 'profile_doc', array(
				'label' => 'Allow Profile Document',
				'description' => "Do you want to let members of this level make their documents as Profile Document? (If enabled, users will see a “Make Profile Document” link option next to their documents in the “My Documents” section. At any time only one document can be made as a Profile Document for a user. A document chosen as a Profile Document is displayed in a tab on user profile. For this, you should have placed the 'Member’s Profile Document' widget on “Member Profile” page.)",
				'multiOptions' => array(
					1 => 'Yes',
					0 => 'No'
				),
				'value' => 1,
			));

			$this->addElement('Radio', 'profile_doc_show', array(
				'label' => 'Make Profile Document during Creation',
				'description' => "Do you want users to be able to choose during document creation if the document should be made their Profile Document?",
				'multiOptions' => array(
					1 => 'Yes',
					0 => 'No'
				),
				'value' => 1,
			));

			$this->addElement('Radio', 'comment', array(
				'label' => 'Allow Commenting on Documents?',
				'description' => 'Do you want to let members of this level comment on documents?',
				'multiOptions' => array(
					1 => 'Yes, allow members to comment on documents.',
					0 => 'No, do not allow members to comment on documents.',
				),
				'value' => 1,
			));

			$this->addElement('MultiCheckbox', 'auth_view', array(
				'label' => 'Document View Privacy',
				'description' => 'Your members can choose from any of the options checked below when they decide who can see their document entries. These options appear on your members\' "Add Entry" and "Edit Entry" pages. If you do not check any options, everyone will be allowed to view documents.',
				'multiOptions' => array(
					'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
					'owner_network'       => 'Friends and Networks',
					'owner_member_member' => 'Friends of Friends',
					'owner_member'        => 'Friends Only',
					'owner'               => 'Just Me'
				),
				'value' => array('everyone', 'registered', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
			));

			$this->addElement('MultiCheckbox', 'auth_comment', array(
				'label' => 'Document Comment Options',
				'description' => 'Your members can choose from any of the options checked below when they decide who can post comments on their entries. If you do not check any options, everyone will be allowed to post comments on entries.',
				'multiOptions' => array(
					'registered'          => 'All Registered Members',
					'owner_network'       => 'Friends and Networks',
					'owner_member_member' => 'Friends of Friends',
					'owner_member'        => 'Friends Only',
					'owner'               => 'Just Me'
				),
				'value' => array('registered', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
			));

			$this->addElement('Text', 'entries', array(
			'label' => 'Maximum allowed documents',
			'description' => 'Enter the maximum number of documents allowed  for users of this level (Enter 0 for unlimited documents).',
			'value'=> '0'
			));
			
			$filesize = (int)ini_get('upload_max_filesize')*1024;
			$description = Zend_Registry::get('Zend_Translate')->_('Enter the maximum document file size in KB allowed for this user level. Valid values are from 1 to %s KB.');
			$description = sprintf($description, $filesize);
			$this->addElement('Text', 'filesize', array(
				'label' => 'Maximum file size',
				'description' => $description,
				'value'=> $filesize
			));

		/*
			COMMENTS: NOW VISIBILITY OPTIONS (IN GLOBAL SETTINGS) AND SECURE IPAPER/DOWNLOAD/EMAIL (IN MEMBER LEVEL SETTINGS) ARE DEPENDENT.

			GLOBAL SETTINGS: VISIBILITY => "PUBLIC ON SCRIBD.COM" && SHOW OPTIONS TO USER => "YES"
			MEMBER LEVEL SETTINGS: SECURE IPAPER, DOWNLOAD AND EMAIL FOR CREATION OPTIONS WILL BE VISIBLE

			GLOBAL SETTINGS: VISIBILITY => "PUBLIC ON SCRIBD.COM" && SHOW OPTIONS TO USER => "NO"
			MEMBER LEVEL SETTINGS: SECURE IPAPER, DOWNLOAD AND EMAIL FOR CREATION OPTIONS WILL NOT BE VISIBLE

			GLOBAL SETTINGS: VISIBILITY => "ONLY ON THIS WEBSITE"
			MEMBER LEVEL SETTINGS: SECURE IPAPER, DOWNLOAD AND EMAIL FOR CREATION OPTIONS WILL BE VISIBLE
		*/

			$this->addElement('Radio', 'view_download', array(
				'label' => 'Document downloading (Viewer)',
				'description' => 'Do you want users of this member level to be shown the link for downloading of downloadable documents?',
				'multiOptions' => array(
					1 => 'Yes',
					0 => 'No'
				),
				'value' => 1,
			));

			if($visibility != 'public' || !empty($option_show)) {
			
				$this->addElement('Radio', 'download_allow', array(
					'label' => 'Document downloading (Creator)',
					'description' => 'Do you want the documents added by members of this level to be downloadable? (Note: Public documents will be downloadable always.)',
					'multiOptions' => array(
						1 => 'Yes',
						0 => 'No'
					),
					'value' => 1,
				));
					
				$this->addElement('Radio', 'download_show', array(
					'description' => 'Show option to users during document creation to allow downloading of their documents. (Note : If enabled, this option will only be visible during document creation and not during editing.)',
					'multiOptions' => array(
						1 => 'Yes',
						0 => 'No'
					),
					'value' => 1,
				));

				$this->addElement('Select', 'download_format', array(
					'label' => 'Download Format',
					'description' => 'Select the format in which the downloadable documents will be downloaded in.',
					'multiOptions' => array(
						'pdf' => 'PDF',
						'original'   => 'Original Document Format',
						'txt'   => 'Plain Text' 
					),
					'value' => 'pdf',
				));

				$this->addElement('Radio', 'secure_allow', array(
					'label' => 'Secure iPaper Settings',
					'description' => 'Secure document iPaper cannot be embedded on other sites. Note that this is a permanent setting for a document during its first upload. Thus any change in this setting will only work for new document uploads.(Note : This setting will only apply to new documents created and not to the existing documents on the site. )',
					'multiOptions' => array(
						1 => 'Make iPaper documents secure. (Note: Secure documents can be viewed in Flash reader only because secure documents use access-management technology which is available in Flash only. Therefore, secure documents will always be viewed in Flash reader, even if you choose HTML5 reader as default.)',
						0 => 'Do not make iPaper documents secure.'
					),
					'value' => 0,
				));
				
				$this->addElement('Radio', 'secure_show', array(
					'description' => 'Show security option to users. Allow users to choose if their documents should be secure or not. (Note : If enabled, this option will only be visible during document creation and not during editing.)',
					'multiOptions' => array(
						1 => 'Yes',
						0 => 'No'
					),
					'value' => 1,
				));
			}

			$this->addElement('Radio', 'view_email', array(
				'label' => 'Email attachment Settings (Viewer)',
				'description' => 'Do you want users of this member level to be shown the link for emailing documents as attachments?',
				'multiOptions' => array(
					1 => 'Yes',
					0 => 'No'
				),
				'value' => 1,
			));

			if($visibility != 'public' || !empty($option_show)) {
				
				$this->addElement('Radio', 'email_allow', array(
					'label' => 'Email attachment Settings (Creator)',
					'description' => 'Do you want the documents added by members of this level to be emailable as attachments? (Note: Public documents will be emailable as attachments always.)',
					'multiOptions' => array(
						1 => 'Yes',
						0 => 'No'
					),
					'value' => 1,
				));
				
				$this->addElement('Radio', 'email_show', array(
					'description' => 'Show option to users during document creation to allow emailing of their documents as attachments. (Note : If enabled, this option will only be visible during document creation and not during editing.)',
					'multiOptions' => array(
						1 => 'Yes',
						0 => 'No'
					),
					'value' => 1,
				));
			}
			else {
				$this->addElement('Dummy', 'visibility_hide_options', array(
					'description' => "Some settings for downloading and emailing as attachments of documents are enableded by default as you have chosen public visibility for documents in 'Global Settings'.",
				));
			}
		
		}
  }
}