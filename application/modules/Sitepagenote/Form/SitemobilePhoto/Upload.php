<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagenote
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Upload.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepagenote_Form_SitemobilePhoto_Upload extends Engine_Form {

  public function init() {

    $this
            ->setTitle('Add New Photos')
            ->setDescription('Choose photos on your computer to add to this note. (2MB maximum)')
            ->setAttrib('id', 'form-upload')
            ->setAttrib('class', 'global_form sitepagenote_form_upload')
            ->setAttrib('name', 'albums_create')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

//    $fancyUpload = new Engine_Form_Element_FancyUpload('file');
//    $fancyUpload->clearDecorators()
//            ->addDecorator('FormFancyUpload')
//            ->addDecorator('viewScript', array(
//                'viewScript' => '_FancyUpload.tpl',
//                'placement' => '',
//            ));
//    Engine_Form::addDefaultDecorators($fancyUpload);
//    $this->addElement($fancyUpload);
//
//    $this->addElement('Hidden', 'fancyuploadfileids');

    // Init file
    $this->addElement('FancyUpload', 'file');
    
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Photos',
        'type' => 'submit',
    ));
  }

}

?>