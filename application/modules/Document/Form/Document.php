<?php

class Document_Form_Document extends Engine_Form
{
    protected $_parent_type;

    protected $_parent_id;

    public function setParent_type($value) {
        $this->_parent_type = $value;
    }

    public function setParent_id($value) {
        $this->_parent_id = $value;
    }

    protected $_roles;

    public function init() {
        // init form
        $this
            ->setAttrib('id', 'form-upload')
            ->setAttrib('name', 'document_create')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
        ;

        $user = Engine_Api::_()->user()->getViewer();

        // init title
        $this->addElement('Text', 'title', array(
            'label' => 'Document Title',
            'maxlength' => '100',
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '100')),
            )
        ));

        // init description
        $this->addElement('Textarea', 'description', array(
            'label' => 'Document Description',
            'maxlength' => '10000',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_EnableLinks(),
                new Engine_Filter_StringLength(array('max' => 10000))
            ),
        ));

        // view auth
        if ($this->_parent_type == 'group') {
            $this->_roles = array(
                'everyone' => 'Everyone',
                'registered' => 'All Registered Members',
                'parent_member' => 'Group Members',
                'owner' => 'Just Me',
            );
        } else {
            $this->_roles = array(
                'everyone' => 'Everyone',
                'registered' => 'All Registered Members',
                'owner_network' => 'Friends and Networks',
                'owner_member_member' => 'Friends of Friends',
                'owner_member' => 'Friends Only',
                'owner' => 'Just Me',
            );
        }

        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('document', $user, 'auth_view');
        $viewOptions = array_intersect_key($this->_roles, array_flip($viewOptions));

        if (!empty($viewOptions) && count($viewOptions) >= 1) {
            if (count($viewOptions) == 1) {
                $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions)));
            } else {
                $this->addElement('Select', 'auth_view', array(
                    'label' => 'Privacy',
                    'description' => 'Who may see this document?',
                    'multiOptions' => $viewOptions,
                    'value' => key($viewOptions),
                ));
                $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        // init submit
        $this->addElement('Button', 'upload', array(
            'label' => 'Save Document',
            'type' => 'submit',
        ));

        $this->initValueForElements();
    }

    protected function initValueForElements() {

    }
}
