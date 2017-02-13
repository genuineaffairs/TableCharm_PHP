<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagedocument
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagedocument_Form_Edit extends Engine_Form {

  protected $_item;

  public function getItem() {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item) {
    $this->_item = $item;
    return $this;
  }

  public function init() {
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $tab_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    // $url = $view->url($view->item('sitepage_page', $page_id)->getHref(array('tab'=>$tab_id)));
    $url = $view->item('sitepage_page', $page_id)->getHref(array('tab' => $tab_id));
    $this
            ->setTitle('Edit Page Document')
            ->setDescription('Edit the information of your document using the form below.')
            ->setAttrib('id', 'form-upload-sitepagedocument')
            ->setAttrib('name', 'sitepagedocument_edit')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
    ;

    $this->addElement('Text', 'sitepagedocument_title', array(
        'label' => 'Document Title',
        'required' => true,
    ));

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $filter = new Engine_Filter_Html();

    if ($settings->getSetting('sitepagedocument.show.editor', 1)) {
      $this->addElement('TinyMce', 'sitepagedocument_description', array(
          'label' => 'Description',
          'required' => false,
          'allowEmpty' => true,
          'filters' => array(
              new Engine_Filter_Censor(),
              $filter,
          ),
          'editorOptions' => Engine_Api::_()->seaocore()->tinymceEditorOptions(),
      ));
    } else {
      $this->addElement('textarea', 'sitepagedocument_description', array(
          'label' => 'Document description',
          'required' => false,
          'allowEmpty' => true,
          'attribs' => array('rows' => 24, 'cols' => 80, 'style' => 'width:300px; max-width:553px;height:120px;'),
          'filters' => array(
              $filter,
              new Engine_Filter_Censor(),
          ),
      ));
    }

    $sitepagedocument_default_visibility = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.default.visibility', 'private');
    $sitepagedocument_visibility_option = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.visibility.option', 1);
    if ($sitepagedocument_default_visibility == 'public' && $sitepagedocument_visibility_option == 1) {
      $this->addElement('Select', 'default_visibility', array(
          'multiOptions' => array('private' => "Only on this website", 'public' => "Public on Scribd.com"),
          'decorators' => array(array('ViewScript', array(
                      'viewScript' => '_formvisibility.tpl',
                      'class' => 'form element'
                  )))
      ));
    }

    $sitepagedocument_licensing_option = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.licensing.option', 1);
    if ($sitepagedocument_licensing_option == 1) {
      $this->addElement('Select', 'sitepagedocument_license', array(
          'label' => 'License Associated',
          'style' => 'max-width:none;',
          'description' => 'LICENSES-INFO',
          'multiOptions' => array(
              'ns' => 'Unspecified - no licensing information associated',
              'by' => 'By attribution (by)',
              'by-nc' => 'By attribution, non-commercial (by-nc)',
              'by-nc-nd' => 'By attribution, non-commercial, non-derivative (by-nc-nd)',
              'by-nc-sa' => 'By attribution, non-commercial, share alike (by-nc-sa)',
              'by-nd' => 'By attribution, non-derivative (by-nd)',
              'by-sa' => 'By attribution, share alike (by-sa)',
              'pd' => 'Public domain',
              'c' => 'Copyright - all rights reserved',
          )
      ));
      $this->sitepagedocument_license->addDecorator('Description', array('placement' => 'APPEND', 'sitepagedocument_license' => 'label', 'class' => 'null', 'escape' => false, 'for' => 'sitepagedocument_license'));
    }

    $filesize = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.filesize', 2048);
    $description = Zend_Registry::get('Zend_Translate')->_('Browse and choose a file for your document. Maximum permissible size: %s KB and allowed file types: pdf, txt, ps, rtf, epub, odt, odp, ods, odg, odf, sxw, sxc, sxi, sxd, doc, ppt, pps, xls, docx, pptx, ppsx, xlsx, tif, tiff');
    $description = sprintf($description, $filesize);
    $this->addElement('File', 'filename', array(
        'label' => 'Document File',
        'description' => $description
    ));
    $this->filename->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Select', 'draft', array(
        'label' => 'Status',
        'multiOptions' => array("0" => "Published", "1" => "Saved As Draft"),
        'description' => 'If this entry is published, it cannot be switched back to draft mode.'
    ));
    $this->draft->getDecorator('Description')->setOption('placement', 'append');

    // Add subforms
    if (!$this->_item) {
      $customFields = new Sitepagedocument_Form_Custom_Fields();
    } else {
      $customFields = new Sitepagedocument_Form_Custom_Fields(array(
          'item' => $this->getItem()
      ));
    }
    if (get_class($this) == 'Sitepagedocument_Form_Edit') {
      $customFields->setIsCreation(true);
    }

    $this->addSubForms(array(
        'fields' => $customFields
    ));


    $download_allow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.download.allow', 1);
    $download_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.download.show', 1);
    if ($download_allow == 1 && $download_show == 1) {
      $this->addElement('Radio', 'download_allow', array(
          'label' => 'Allow Document Download',
          'multiOptions' => array(
              1 => 'Yes, allow document download.',
              0 => 'No, do not allow document download.'
          ),
          'value' => 1,
      ));
    }

    $email_allow = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.email.allow', 1);
    $email_show = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagedocument.email.show', 1);
    if ($email_allow == 1 && $email_show == 1) {
      $this->addElement('Radio', 'email_allow', array(
          'label' => 'Allow Email Attachment',
          'multiOptions' => array(
              1 => 'Yes, allow document to be emailed as attachment.',
              0 => 'No, do not allow document to be emailed as attachment.'
          ),
          'value' => 1,
      ));
    }

    $this->addElement('Checkbox', 'search', array(
        'label' => "Show this document in search results.",
        'value' => 1
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'onclick' => "javascript:showlightbox();",
        'decorators' => array(
            'ViewHelper',
        ),
    ));


    // Cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => $url,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array(
        'submit',
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