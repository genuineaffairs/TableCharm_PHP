<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Communityad
 * @copyright  Copyright 2009-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Helppagecreate.php 2011-02-16 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Communityad_Form_Admin_Helppagecreate extends Engine_Form {

  public function init() {
    // Conditions: When click on 'edit' from the admin-help & learnmore-manage page for showing prefields for selected ID.
    $pageId = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    if (empty($pageId)) {
      $this->setTitle('Create a new Advertising Help Page')->setDescription("Create a new advertising help page using the rich editor below.");
    } else {
      $this->setTitle('Edit Advertising Help Page')->setDescription("Edit advertising help page using the rich editor below.");
    }


    if (!empty($pageId)) {
      $pageObj = Engine_Api::_()->getItem('communityad_infopage', $pageId);
      $title = $pageObj->title;
      $description = $pageObj->description;
      $setHiddenValue = $pageId;
    } else {
      $title = '';
      $description = '';
      $setHiddenValue = 0;
    }

    $this->addElement('Text', 'title', array(
        'label' => 'Page Title',
        'description' => 'Enter the heading of the page.',
        'required' => true,
        'value' => $title
    ));

		$this->addElement( 'Dummy' , 'text_flag' , array (
				'label'	=>	' Language Suport',
     ) ) ;

    $this->addElement('Textarea', 'text_description', array(
      'label' => 'Page Body',
      'description' => 'Not sure about what kind of information should come on this page? See the FAQ section for some helpful URLs from where you can get an idea of the text you can have on the advertising help pages.',
			'value' => $description
    ));

    $this->addElement('TinyMce', 'description', array(
        'label' => 'Page Body',
        'description' => 'Not sure about what kind of information should come on this page? See the FAQ section for some helpful URLs from where you can get an idea of the text you can have on the advertising help pages.',
        // 'required' => true,
        'value' => $description,
        'attribs' => array('rows' => 24, 'cols' => 80, 'style' => 'width:200px; max-width:200px; height:120px;'),
        'allowEmpty' => false,
        'filters' => array(
            new Engine_Filter_Html(),
            new Engine_Filter_Censor()),
         'editorOptions' => Engine_Api::_()->seaocore()->tinymceEditorOptions(),
    ));

    $this->addElement('Hidden', 'is_opration', array(
        'value' => $setHiddenValue
    ));


    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));
  }

}