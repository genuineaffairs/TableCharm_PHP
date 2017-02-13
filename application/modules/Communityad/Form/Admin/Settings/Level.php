<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Level.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {

  public function init() {
    parent::init();

    // My stuff
    $this
            ->setTitle('Member Level Settings')
            ->setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.");

    // Element: view
    $this->addElement('Radio', 'view', array(
        'label' => 'Allow Viewing of Advertisements?',
        'description' => 'Do you want to let members view advertisements? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
            1 => 'Yes, allow viewing of advertisements.',
            0 => 'No, do not allow advertisements to be viewed.',
        ),
        'value' => 1,
    ));

    if (!$this->isPublic()) {

      // Element: create
      $this->addElement('Radio', 'create', array(
          'label' => 'Allow Creation of Advertisements?',
          'description' => 'Do you want to let members create advertisements?',
          'multiOptions' => array(
              1 => 'Yes, allow creation of advertisements.',
              0 => 'No, do not allow advertisements to be created.'
          ),
          'value' => 1,
      ));

      // Element: edit
      $this->addElement('Radio', 'edit', array(
          'label' => 'Allow Editing of Advertisements?',
          'description' => 'Do you want to let members to edit advertisements ?',
          'multiOptions' => array(
              2 => 'Yes, allow members to edit all advertisements.',
              1 => 'Yes, allow members to edit their own advertisements.',
              0 => 'No, do not allow members to edit their advertisements.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->edit->options[2]);
      }

      // Element: edit
      $this->addElement('Radio', 'showdetail', array(
          'label' => 'Allow Viewing details of Advertisements ?',
          'description' => 'Do you want to let members view the details of their advertisements ?',
          'multiOptions' => array(
              2 => 'Yes, allow members to view details of all advertisements.',
              1 => 'Yes, allow members to view details of their own advertisements.',
              0 => 'No, do not allow members to view details of their advertisements.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->showdetail->options[2]);
      }

      // Element: delete
      $this->addElement('Radio', 'delete', array(
          'label' => 'Allow Deletion of Advertisements?',
          'description' => 'Do you want to let members delete their advertisements ?',
          'multiOptions' => array(
              2 => 'Yes, allow members to delete all advertisements.',
              1 => 'Yes, allow members to delete their own advertisements.',
              0 => 'No, do not allow members to delete their advertisements.',
          ),
          'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if (!$this->isModerator()) {
        unset($this->delete->options[2]);
      }
    }
  }

}