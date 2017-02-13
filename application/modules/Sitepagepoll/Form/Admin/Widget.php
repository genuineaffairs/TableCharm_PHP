<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagepoll
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Widget.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagepoll_Form_Admin_Widget extends Engine_Form {

  public function init() {
    $this
            ->setTitle('Widget Settings')
            ->setDescription('Configure the general settings for the various widgets available with this plugin.');

    $this->addElement('Text', 'sitepagepoll_comment_widgets', array(
        'label' => 'Page Profile Most Commented Polls',
        'maxlength' => '3',
        'description' => 'How many polls should be shown in the Page Profile Most Commented Polls Widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.comment.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitepagepoll_view_widgets', array(
        'label' => 'Page Profile Most Viewed Polls',
        'maxlength' => '3',
        'description' => 'How many polls should be shown in the Page Profile Most Viewed Polls Widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.view.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitepagepoll_recent_widgets', array(
        'label' => 'Page Profile Most Recent Polls',
        'maxlength' => '3',
        'description' => 'How many polls should be shown in the Page Profile Most Recent Polls Widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.recent.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitepagepoll_vote_widgets', array(
        'label' => 'Page Profile Most Voted Polls',
        'maxlength' => '3',
        'description' => 'How many polls should be shown in the Page Profile Most Voted Polls Widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.vote.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitepagepoll_like_widgets', array(
        'label' => 'Page Profile Most Liked Polls',
        'maxlength' => '3',
        'description' => 'How many polls should be shown in the Page Profile Most Liked Polls Widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagepoll.like.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}
?>