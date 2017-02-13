<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Graph.php  2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Form_Admin_Settings_Graph extends Engine_Form {

  public function init() {
    parent::init();

    // My stuff
    $this
            ->setTitle('Statistical Graphs Settings')
            ->setDescription("Advertisers can view graphical statistics of various performance metrics like Views, Clicks and CTR of their campaigns and ads over different time periods. Below, you can customize the theme and other parameters of the graphs.");

    // COLOR VALUE FOR GRAPH BACKGROUND
    $this->addElement('Text', 'communityad_graph_bgcolor', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbowGraphbg.tpl',
                    'class' => 'form element'
            )))
    ));

    // Element: GRAPH VIEWS LINES WIDTH
    $this->addElement('Text', 'communityad_graphview_width', array(
        'label' => 'Views Line Width',
        'description' => 'Enter the width of the lines in pixels which are used to represent Views in the graphs. (Enter a number between 1 and 9.)',
        'maxlength' => 1,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('Int', true),
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.graphview.width', 3),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '1')),
        )
    ));

    // COLOR VALUE FOR VIEWS LINE OF GRAPH
    $this->addElement('Text', 'communityad_graphview_color', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbowViews.tpl',
                    'class' => 'form element'
            )))
    ));

    // Element: GRAPH CLICKS LINES WIDTH
    $this->addElement('Text', 'communityad_graphclick_width', array(
        'label' => 'Clicks Line Width',
        'description' => 'Enter the width of the lines in pixels which are used to represent Clicks in the graphs. (Enter a number between 1 and 9.)',
        'maxlength' => 1,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('Int', true),
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.graphclick.width', 3),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '1')),
        )
    ));

    // COLOR VALUE FOR CLICKS LINE OF GRAPH
    $this->addElement('Text', 'communityad_graphclick_color', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbowClicks.tpl',
                    'class' => 'form element'
            )))
    ));

    // Element: GRAPH CTR LINES WIDTH
    $this->addElement('Text', 'communityad_graphctr_width', array(
        'label' => 'CTR Line Width',
        'description' => 'Enter the width of the lines in pixels which are used to represent CTR in the graphs. (Enter a number between 1 and 9.)',
        'maxlength' => 1,
        'allowEmpty' => false,
        'required' => true,
        'validators' => array(
            array('Int', true),
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('communityad.graphctr.width', 3),
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '1')),
        )
    ));

    // COLOR VALUE FOR CTR LINE OF GRAPH
    $this->addElement('Text', 'communityad_graphctr_color', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbowCtr.tpl',
                    'class' => 'form element'
            )))
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}