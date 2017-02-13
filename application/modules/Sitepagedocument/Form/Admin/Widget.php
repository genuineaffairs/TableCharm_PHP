<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Widget.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagedocument_Form_Admin_Widget extends Engine_Form {

  public function init() {
    $this
            ->setTitle('Widget Settings')
            ->setDescription('Configure the general settings for the various widgets available with this plugin.');

    $this->addElement('Text', 'sitepagedocument_comment_widgets', array(
        'label' => 'Page Profile Most Commented Documents',
        'maxlength' => '3',
        'description' => 'How many documents should be shown in the page profile most commented documents widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.comment.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitepagedocument_like_widgets', array(
        'label' => 'Page Profile Most Liked Documents',
        'maxlength' => '3',
        'description' => 'How many documents should be shown in the page profile most liked documents widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.like.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitepagedocument_featurelist_widgets', array(
        'label' => 'Page Profile Featured Documents',
        'maxlength' => '3',
        'description' => 'How many documents should be shown in the page profile featured documents widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.featurelist.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitepagedocument_popular_widgets', array(
        'label' => 'Page Profile Popular Documents',
        'maxlength' => '3',
        'description' => 'How many documents should be shown in the page profile popular documents widget (value cannot be empty or zero) ? [Note: Popularity here is based on the number of views.]',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.popular.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitepagedocument_rate_widgets', array(
        'label' => 'Page Profile Top Rated Documents',
        'maxlength' => '3',
        'description' => 'How many documents should be shown in the top page profile top rated documents widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.rate.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitepagedocument_recent_widgets', array(
        'label' => 'Page Profile Recent Documents',
        'maxlength' => '3',
        'description' => 'How many documents should be shown in the page profile recent documents widget (value cannot be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.recent.widgets', 3),
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