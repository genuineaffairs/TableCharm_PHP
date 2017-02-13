<?php

class Grandopening_Form_Admin_Edit extends Engine_Form {

    public function init() {
        $this->setTitle('Edit Cover')
                ->setAttrib('class', 'global_form_popup');

        $this->addElement('Date', 'start_date', array(
            'label' => 'Start Date',
            'yearMin' => date('Y') - 1,
            'yearMax' => date('Y') + 5
        ));

        $this->addElement('Date', 'end_date', array(
            'label' => 'End Date',
            'yearMin' => date('Y') - 1,
            'yearMax' => date('Y') + 5
        ));

        $this->addElement('Checkbox', 'enabled', array(
            'label' => 'Enabled',
            'decorators' => array(
                'ViewHelper',
                array('Label', array('placement' => 'APPEND')),
                array('HtmlTag', array('tag' => 'div', 'class' => 'form-wrapper')),
            )
        ));

        $this->addElement('Button', 'execute', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'onclick' => 'parent.Smoothbox.close();',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
        ));

        $this->addDisplayGroup(array('execute', 'cancel'), 'buttons', array());
    }

}