<?php

class Document_Form_Delete extends Engine_Form {

    public function init() {
        $this->setTitle('Delete Document')
                ->setDescription('Are you sure you want to delete this document?')
                ->setAttrib('class', 'global_form_popup')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setMethod('POST');
        ;

        //$this->addElement('Hash', 'token');
        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Delete Document',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            )
        ));
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');
    }

}