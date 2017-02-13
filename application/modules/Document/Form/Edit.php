<?php

class Document_Form_Edit extends Document_Form_Document {
    private $_document;

    public function setDocument($value)
    {
        $this->_document = $value;
    }

    protected function initValueForElements() {

        // auto-fill the form fields with the document's data
        $this->populate($this->_document->toArray());

        // set view authentication
        $authViewElement = $this->getElement('auth_view');

        $auth = Engine_Api::_()->authorization()->context;
        if ($authViewElement) {
            foreach ($this->_roles as $key => $role) {
                if ($auth->isAllowed($this->_document, $key, 'view')) {
                    $authViewElement->setValue($key);
                    break;
                }
            }
        }
    }
}