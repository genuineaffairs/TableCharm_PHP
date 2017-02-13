<?php
class Document_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');

     // Number of documents per page
    $this->addElement('Text', 'document_page', array(
      'label' => 'Documents Per Page',
      'description' => 'How many documents will be shown per page? (Enter a number between 1 and 999)',
      'allowEmpty' => false,
      'validators' => array(
            array('Int',true),
            array('Between',true,array(1,999)),
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('document.page', 10),
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }

   public function saveValues()
  {
    $values = $this->getValues();
    Engine_Api::_()->getApi('settings','core')->setSetting('document.page', $values['document_page']);

    $this ->addNotice('Your changes have been saved!');
  }
}