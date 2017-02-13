<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_Form_Create extends Engine_Form {

  public function init() {
    
    //GET PAGE ID
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);

    //GET TAB ID
    $tab_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);

    //GET VIEW
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

    //GET URL
    $url = $view->item('sitepage_page', $page_id)->getHref(array('tab'=>$tab_id));

    $this
            ->setTitle('Write a New Note')
            ->setDescription('Write a new note by filling the information below, then click "Post Note".')
            ->setAttrib('id', 'form-upload-sitepagenote')
            ->setAttrib('name', 'sitepagenote_create')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $this->addElement('Text', 'title', array(
        'label' => 'Note Title',
        'required' => true,
    ));

    $this->addElement('Text', 'tags', array(
        'label' => 'Tags (Keywords)',
        'autocomplete' => 'off',
        'description' => 'Separate tags with commas.',
        'filters' => array(
            new Engine_Filter_Censor(),
        ),
    ));
    $this->tags->getDecorator("Description")->setOption("placement", "append");
    $filter = new Engine_Filter_Html();

    $upload_url = "";
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');
    if (!empty($isManageAdmin)) {
      $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page_id' => $page_id), 'sitepagenote_uploadphoto', true);
    }

    $this->addElement('TinyMce', 'body', array(
        'label' => 'Body',
        'required' => false,
        'allowEmpty' => true,
        'filters' => array(
            new Engine_Filter_Censor(),
            $filter,
        ),
        'editorOptions' => Engine_Api::_()->seaocore()->tinymceEditorOptions($upload_url),
    ));
    
    // Category
    $this->addElement('Select', 'category_id', array(
      'label' => 'Note Category',
      'multiOptions' => array(
        '0' => ' '
      ),
    ));     

    $this->addElement('Select', 'draft', array(
        'label' => 'Status',
        'multiOptions' => array("0" => "Published", "1" => "Saved As Draft"),
        'description' => 'If this entry is published, it cannot be switched back to draft mode.'
    ));
    $this->draft->getDecorator('Description')->setOption('placement', 'append');

    if (Engine_Api::_()->getApi('settings', 'core')->sitepagenote_allow_image) {
      $this->addElement('File', 'photo', array(
          'label' => 'Main Photo'
      ));
      $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
    }

    $this->addElement('Checkbox', 'search', array(
        'label' => "Show this note in search results.",
        'value' => 1
    ));

    $this->addElement('Button', 'execute', array(
        'label' => 'Post Note',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => $url,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array(
        'execute',
        'cancel',
            ), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));
  }

}

?>