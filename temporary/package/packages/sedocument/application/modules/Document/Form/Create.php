<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Document
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 6590 2010-08-11 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Document_Form_Create extends Engine_Form
{

  protected $_defaultProfileId;

  public function getDefaultProfileId() {
    return $this->_defaultProfileId;
  }

  public function setDefaultProfileId($default_profile_id) {
    $this->_defaultProfileId = $default_profile_id;
    return $this;
  }

  public function init()
  {  
		$this
		->setTitle('Create New Document')
		->setDescription("Create your new document by filling the information below, then click 'Submit'.")
		->setAttrib('id',      'form-upload-document')
		->setAttrib('name',    'document_create')
		->setAttrib('enctype', 'multipart/form-data')
		->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

		$document_settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.like.api');
      
		//GET VIEWER DETAIL
		$viewer = Engine_Api::_()->user()->getViewer();
		$level_id = $viewer->level_id;
		
		$this->addElement('Text', 'document_title', array(
			'label' => 'Document Title',
			'required' => true,
		));

		$filter = new Engine_Filter_Html();
		$editorAllow = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.show.editor', 1);
    if($editorAllow) {
			$this->addElement('TinyMce', 'document_description', array(
				'label' => 'Document description',
				'required' => false,
				'allowEmpty' => true,
				'filters' => array(
					new Engine_Filter_Censor(),
					$filter,
					    ),
				  'editorOptions' => array(
							'theme_advanced_buttons1' => "preview,code,|,cut,copy,paste,pastetext,pasteword,|,undo,redo,|,link,unlink,anchor,charmap,image,media,|,hr,removeformat,cleanup",
							'theme_advanced_buttons2' => "bold,italic,underline,strikethrough,|,bullist,numlist,|,outdent,indent,blockquote,|,justifyleft,justifycenter,justifyright,justifyfull,|,sub,sup",
							'theme_advanced_buttons3' => "formatselect,fontselect,fontsizeselect,|,forecolor,backcolor"),
			));
	  }
    else
    {
      $this->addElement('textarea', 'document_description', array(
				'label' => 'Document description',
        'attribs' => array('rows'=>24, 'cols'=>80, 'style'=>'width:300px; max-width:553px;height:120px;'),
        'filters' => array(
          $filter,
          new Engine_Filter_Censor(),
        ),
      ));
    }
    
    $categories = Engine_Api::_()->getDbTable('categories', 'document')->getCategories(0, 0);
    if (count($categories) != 0) {
      $categories_prepared[0] = "";
      foreach ($categories as $category) {
        $categories_prepared[$category->category_id] = $category->category_name;
      }

			$defaultProfileId = "0_0_".$this->getDefaultProfileId();

      $this->addElement('Select', 'category_id', array(
          'label' => 'Category',
          'allowEmpty' => false,
          'required' => true,
          'multiOptions' => $categories_prepared,
          'onchange' => " var profile_type = getProfileType($(this).value); 
                          if(profile_type == 0) profile_type = '';
													$('$defaultProfileId').value = profile_type;
													changeFields($('$defaultProfileId'));
													subcategories(this.value, '', '');",
      ));
    
			$this->addElement('Select', 'subcategory_id', array(
					'RegisterInArrayValidator' => false,
					'allowEmpty' => true,
					'required' => false,
					'decorators' => array(array('ViewScript', array(
											'viewScript' => 'application/modules/Document/views/scripts/_formSubcategory.tpl',
											'class' => 'form element')))
			));

			$this->addElement('Select', 'subsubcategory_id', array(
					'RegisterInArrayValidator' => false,
					'allowEmpty' => true,
					'required' => false,
					'decorators' => array(array('ViewScript', array(
																	'viewScript' => 'application/modules/Document/views/scripts/_formSubcategory.tpl',
																	'class' => 'form element')))
			));
		}

		$customFields = new Document_Form_Custom_Standard(array(
										'item' => 'document',
										'decorators' => array(
														'FormElements'
										)));
		$customFields->removeElement('submit');

		$customFields->getElement($defaultProfileId)           
            ->clearValidators()
            ->setRequired(false)
            ->setAllowEmpty(true);

    $this->addSubForms(array(
      'fields' => $customFields
    ));
    		
	  $document_licensing_option = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.licensing.option', 1);
		if($document_licensing_option == 1) {
			$this->addElement('Select', 'document_license', array(
				'label' => 'License Associated',
				'style'=>'max-width:none;',
				'description' => 'LICENSES-INFO',	
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
			'value' => 'ns',
			));
			$this->document_license->addDecorator('Description', array('placement' => 'APPEND', 'document_license' => 'label', 'class' => 'null', 'escape' => false, 'for' => 'document_license'));
		}

		$filesize = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'filesize');
		$description = Zend_Registry::get('Zend_Translate')->_('Browse and choose a file for your document. Maximum permissible size: %s KB and allowed file types: pdf, txt, ps, rtf, epub, odt, odp, ods, odg, odf, sxw, sxc, sxi, sxd, doc, ppt, pps, xls, docx, pptx, ppsx, xlsx, tif, tiff');
		$description = sprintf($description, $filesize);
		$this->addElement('File', 'filename', array(
				'label' => 'Document File',
				'required' => true,	
				'description' =>  $description
		));
		$this->filename->getDecorator('Description')->setOption('placement', 'append');
    
    $this->addElement('Text', 'tags',array(
      'label'=>'Tags (Keywords)',
      'autocomplete' => 'off',
      'description' => 'Separate tags with commas.',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    $this->tags->getDecorator("Description")->setOption("placement", "append");
    
    $this->addElement('Select', 'draft', array(
      'label' => 'Status',
      'multiOptions' => array("0"=>"Published", "1"=>"Saved As Draft"),
      'description' => 'If this entry is published, it cannot be switched back to draft mode.'
    ));
    $this->draft->getDecorator('Description')->setOption('placement', 'append');

		$profile_doc = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'profile_doc');
		$profile_doc_show = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'profile_doc_show');
		if($profile_doc == 1 && $profile_doc_show == 1){
			$this->addElement('Radio', 'profile_doc', array(
				'label' => 'Make Profile Document',
				'multiOptions' => array(
					1 => 'Yes, make this your Profile Document. (Note: At any time only one document can be showcased as your Profile Document. Thus, if you have made any document as your profile document currently, then it will be changed to this one.)',
					0 => 'No, do not make this your Profile Document.'
				),
				'value' => 0,
			));
		}

		/*
			COMMENTS: WE WILL NOT SHOW VISIBILITY, SECURE IPAPER, DOWNLOAD AND EMAIL OPTIONS ON EDIT DOCUMENT. BECAUSE WE SEND SOME SETTINGS TO SCRIBD WHEN WE UPLOAD A DOCUMENT ON SCRIBD. SO AFTER DOCUMENT CREATION IF ADMIN CHANGE SOME SETTINGS FROM GLOBAL/MEMBER LEVEL SETTINGS THEN OUR DOCUMENT SETTINGS REMAIN SAME.

			GLOBAL SETTINGS: VISIBILITY => "PUBLIC ON SCRIBD.COM" && SHOW OPTIONS TO USER => "YES"
			DOCUMENT CREATION: SECURE IPAPER, DOWNLOAD EMAIL SETTINGS WILL COME

			GLOBAL SETTINGS: VISIBILITY => "PUBLIC ON SCRIBD.COM" && SHOW OPTIONS TO USER => "NO"
			DOCUMENT CREATION: SECURE IPAPER, DOWNLOAD EMAIL SETTINGS WILL NOT COME

			GLOBAL SETTINGS: VISIBILITY => "ONLY ON THIS WEBSITE"
			DOCUMENT CREATION: SECURE IPAPER, DOWNLOAD EMAIL SETTINGS WILL COME

			DOCUMENT CREATION: VISIBILITY => "PUBLIC ON SCRIBD.COM"
			HIDE ALL OPTIONS: SECURE IPAPER, DOWNLOAD, EMAIL [IT ALSO DEPEND ON MEMBER LEVEL SETTINGS]

			DOCUMENT CREATION: VISIBILITY => "ONLY ON THIS WEBSITE"
			SHOW ALL OPTIONS: SECURE IPAPER, DOWNLOAD, EMAIL [IT ALSO DEPEND ON MEMBER LEVEL SETTINGS]

			AT EVERY TIME DEFAULT VALUES FOR SECURE IPAPER, DOWNLOAD AND EMAIL (IN ANY CASE OPTIONS WILL VISIBLE OR NOT)
			SECURE => No
			DOWNLOAD => YES
			EMAIL => YES

		*/

    $document_default_visibility = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.default.visibility', 'private');
    $document_visibility_option = Engine_Api::_()->getApi('settings', 'core')->getSetting('document.visibility.option', 1);
    if($document_default_visibility == 'public' && $document_visibility_option == 1) {
	    $this->addElement('Select', 'default_visibility', array(
	      'multiOptions' => array('private'=>"Only on this website", 'public'=>"Public on Scribd.com"),
	     'decorators' => array(array('ViewScript', array(
        'viewScript' => '_formvisibility.tpl',
        'class'      => 'form element'
        )))
      ));
    }

    if($document_default_visibility != 'public' || !empty($document_visibility_option)) {
			$secure_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'secure_allow');
			$secure_show = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'secure_show');
			if($secure_allow == 1 && $secure_show == 1){
				$this->addElement('Radio', 'secure_allow', array(
					'label' => 'Secure iPaper Document',
					'multiOptions' => array(
						1 => 'Make iPaper document secure. Do not allow embedding on other sites.',
						0 => 'Do not make iPaper document secure. Allow embedding on other sites.'
					),
					'value' => 0,
				));
			}

			$download_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'download_allow');
			$download_show = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'download_show');
				if($download_allow == 1 && $download_show == 1) {
					$this->addElement('Radio', 'download_allow', array(
						'label' => 'Allow Document Download',
						'multiOptions' => array(
						1 => 'Yes, allow document download.',
						0 => 'No, do not allow document download.'
					),
					'value' => 1,
				));
			}
			
			$email_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'email_allow');
			$email_show = Engine_Api::_()->authorization()->getPermission($level_id, 'document', 'email_show');
			if($email_allow == 1 && $email_show == 1) {
				$this->addElement('Radio', 'email_allow', array(
					'label' => 'Allow Email Attachment',
					'multiOptions' => array(
					1 => 'Yes, allow document to be emailed as attachment.',
					0 => 'No, do not allow document to be emailed as attachment.'
				),
				'value' => 1,
				));
			}
		}
    
    //NETWORK BASE PAGE VIEW PRIVACY
    if (Engine_Api::_()->document()->documentBaseNetworkEnable()) {
      // Make Network List
      $table = Engine_Api::_()->getDbtable('networks', 'network');
      $select = $table->select()
              ->from($table->info('name'), array('network_id', 'title'))
              ->order('title');
      $result = $table->fetchAll($select);

      $networksOptions = array('0' => 'Everyone');
      foreach ($result as $value) {
        $networksOptions[$value->network_id] = $value->title;
      }

      if (count($networksOptions) > 0) {
        $this->addElement('Multiselect', 'networks_privacy', array(
            'label' => 'Networks Selection',
            'description' => Zend_Registry::get('Zend_Translate')->_("Select the networks, members of which should be able to see your document. (Press Ctrl and click to select multiple networks. You can also choose to make your document viewable to everyone.)"),
            'multiOptions' => $networksOptions,
            'value' => array(0)
        ));
      } else {
        
      }
    }    

    $this->addElement('Checkbox', 'search', array(
      'label' => "Show this document in search results.",
      'value' => 1
    ));
    
    $availableLabels = array(
      'everyone'       => 'Everyone',
      'registered'          => 'All Registered Members',
      'owner_network' => 'Friends and Networks',
      'owner_member_member'  => 'Friends of Friends',
      'owner_member'         => 'Friends Only',
      'owner'          => 'Just Me'
    );
		
		$options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('document', $viewer, 'auth_view');
    $options = array_intersect_key($availableLabels, array_flip($options));

    if(!empty($options)) {
	    $this->addElement('Select', 'auth_view', array(
	      'label' => 'View Privacy',
	      'description' => 'Who may see this document?',
	      'multiOptions' => $options,
	      'value' => 'everyone',
	    ));
	    $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
    }

    $options =(array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('document', $viewer, 'auth_comment');
    $options = array_intersect_key($availableLabels, array_flip($options));

    if(!empty($options)) {
	    $this->addElement('Select', 'auth_comment', array(
	      'label' => 'Comment Privacy',
	      'description' => 'Who may post comments on this document?',
	      'multiOptions' => $options,
	      'value' => 'registered',
	    ));
	    $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
    }  

		if( empty($document_settings) ) {
			exit();
		}

		$this->addElement('Button', 'submit', array(
			'label' => 'Submit',
			'type' => 'submit',
			'decorators' => array(array('ViewScript', array(
				'viewScript' => '_formButtonCancel.tpl',
				'class'      => 'form element')))
		));
  }
}