<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Adcreate.php  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Form_Admin_Adcreate extends Engine_Form {

  public function init() {

    $this->setTitle('Member Package Settings')
            ->setDescription("These settings are applied for targetsettings for ads");

    $adValue = Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.adcreate');

    // Element: auth_view
    $this->addElement('MultiCheckbox', 'auth_module', array(
        'label' => 'Modules show in autosuggest ?',
        'description' => 'Which module do you want to show for ad creation in autosuggest.',
        'multiOptions' => array(
            'group' => 'Group',
            'classified' => 'Classified',
            'video' => 'Video',
            'blog' => 'Blog',
            'event' => 'Event',
            'album' => 'Album',
            'music' => 'Music',
            'poll' => 'Poll',
            'forum' => 'Forum'
        ),
        'value' => unserialize($adValue),
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}