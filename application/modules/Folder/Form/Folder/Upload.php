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
 
 
 
class Folder_Form_Folder_Upload extends Engine_Form
{
  public function init()
  {
    // Init form
    $this
      ->setTitle('Upload New Files')
      ->setDescription('Choose files on your computer to add to this folder. (2MB maximum)')
      ->setAttrib('id', 'form-upload')
      ->setAttrib('class', 'global_form folder_form_upload')
      ->setAttrib('name', 'folders_create')
      ->setAttrib('enctype','multipart/form-data')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;

    $this->addElement('FancyUpload', 'file');

    $this->file->addDecorator('viewScript', array(
                  'viewScript' => '_FancyUpload.tpl',
                  'placement'  => '',
                  ));
    
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Files',
      'type' => 'submit',
    ));
  }
}

