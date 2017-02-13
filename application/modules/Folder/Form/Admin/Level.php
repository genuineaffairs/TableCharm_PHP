<?php



/**
 * Radcodes - SocialEngine Module
 *
 * @category   Application_Extensions
 * @package    Folder
 * @copyright  Copyright (c) 2009-2010 Radcodes LLC (http://www.radcodes.com)
 * @license    http://www.radcodes.com/license/
 * @version    $Id$
 * @author     Vincent Van <vincent@radcodes.com>
 */
 
 
 
class Folder_Form_Admin_Level extends Authorization_Form_Admin_Level_Abstract
{
  
  public function init()
  {
    parent::init();
    
    $this
      ->setTitle('Member Level Settings')
      ->setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.");
      
    $this->addElement('Radio', 'view', array(
      'label' => 'Allow Viewing of Folders?',
      'description' => 'Do you want to let members view folders? If set to no, some other settings on this page may not apply.',
      'multiOptions' => array(
        2 => 'Yes, allow viewing of all folders, even private ones.',
        1 => 'Yes, allow viewing of folders.',
        0 => 'No, do not allow folders to be viewed.',
      ),
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if( !$this->isModerator() ) {
      unset($this->view->options[2]);
    }
    
    if( !$this->isPublic() ) 
    {
      
	    $this->addElement('Radio', 'create', array(
	      'label' => 'Allow Creation of Folders?',
	      'description' => 'Do you want to let members create folders? If set to no, some other settings on this page may not apply.',
	      'multiOptions' => array(
	        1 => 'Yes, allow creation of folders.',
	        0 => 'No, do not allow folders to be created.'
	      ),
	      'value' => 1,
	    ));    
	    
	    $this->addElement('Radio', 'edit', array(
	      'label' => 'Allow Editing of Folders?',
	      'description' => 'Do you want to let members edit folders? If set to no, some other settings on this page may not apply.',
	      'multiOptions' => array(
	        2 => 'Yes, allow members to edit all folders.',
	        1 => 'Yes, allow members to edit their own folders.',
	        0 => 'No, do not allow members to edit their folders.',
	      ),
	      'value' => ( $this->isModerator() ? 2 : 1 ),
	    ));
      if( !$this->isModerator() ) {
        unset($this->edit->options[2]);
      }
      
	    $this->addElement('Radio', 'delete', array(
	      'label' => 'Allow Deletion of Folders?',
	      'description' => 'Do you want to let members delete folders? If set to no, some other settings on this page may not apply.',
	      'multiOptions' => array(
	        2 => 'Yes, allow members to delete all folders.',
	        1 => 'Yes, allow members to delete their own folders.',
	        0 => 'No, do not allow members to delete their folders.',
	      ),
	      'value' => ( $this->isModerator() ? 2 : 1 ),
	    ));
      if( !$this->isModerator() ) {
        unset($this->delete->options[2]);
      }
	
      // Element: comment
      $this->addElement('Radio', 'comment', array(
        'label' => 'Allow Commenting on Folders?',
        'description' => 'Do you want to let members of this level comment on folders?',
        'multiOptions' => array(
          2 => 'Yes, allow members to comment on all folders, including private ones.',
          1 => 'Yes, allow members to comment on folders.',
          0 => 'No, do not allow members to comment on folders.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->comment->options[2]);
      }

	    // PRIVACY ELEMENTS
	    $this->addElement('MultiCheckbox', 'auth_view', array(
	      'label' => 'Folders View Privacy',
	      'description' => 'Your members can choose from any of the options checked below when they decide who can see their folders. If you do not check any options, everyone will be allowed to view folders.',
	        'multiOptions' => array(
	          'everyone'            => 'Everyone',
	          'registered'          => 'Registered Members',
	          'owner_network'       => 'Friends and Networks',
	          'owner_member_member' => 'Friends of Friends',
	          'owner_member'        => 'Friends Only',
	          'owner'               => 'Just Me'
	        ),
	        'value' => array('everyone', 'registered', 'owner_network','owner_member_member', 'owner_member', 'owner')
	    ));
	
	    $this->addElement('MultiCheckbox', 'auth_comment', array(
	      'label' => 'Folder Comment Options',
	      'description' => 'Your members can choose from any of the options checked below when they decide who can post comments on their folders. If you do not check any options, everyone will be allowed to post comments on folders.',
	      'description' => '',
	        'multiOptions' => array(
	          'registered'          => 'Registered Members',
	          'owner_network'       => 'Friends and Networks',
	          'owner_member_member' => 'Friends of Friends',
	          'owner_member'        => 'Friends Only',
	          'owner'               => 'Just Me'
	        ),
	        'value' => array('registered', 'owner_network','owner_member_member', 'owner_member', 'owner')
	    ));	    

      $this->addElement('Text', 'file_extensions', array(
        'label' => 'Allowed File Extensions',
        'description' => 'If you want to allow specific file extensions, you can enter them below (separated by commas, max 255 characters). Example: doc, xls, txt, png, gif, html. Alternatively, you can enter star (*) character to allow all file extensions.',
        'class' => 'long',
      	'required' => true,
      	'allowEmpty' => false,
	      'filters' => array(
	        'StringTrim'
	      ),      
	      'validators' => array(
	        array('NotEmpty', true),
	      ),
        'value' => Folder_Form_Helper::getDefaultAllowedFileExtensions()
      ));
	    
      $this->addElement('Text', 'max_folders', array(
        'label' => 'Maximum Allowed Folders',
        'description' => 'Enter the maximum number of allowed folders. The field must contain an integer, use zero for unlimited.',
        'class' => 'short',
        'validators' => array(
          array('Int', true),
          new Engine_Validate_AtLeast(0),
        ),
      ));
      
    } // end isPublic()
  }

}