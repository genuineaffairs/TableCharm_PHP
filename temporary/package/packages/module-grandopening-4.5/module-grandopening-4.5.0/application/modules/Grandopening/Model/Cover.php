<?php

class Grandopening_Model_Cover extends Core_Model_Item_Abstract {

    protected $_searchTriggers = false;

    public function save() {
        $this->_readOnly = false; //hack
        parent::save();
        $this->getTable()->reloadCovers();
    }

    public function delete() {
        $this->_readOnly = false; //hack
        parent::delete();
        $this->getTable()->reloadCovers();
    }

    public function isActive() {
        $now = date('Y-m-d');
        if ((null != $this->start_date && $now >= $this->start_date) && (null == $this->end_date || $now <= $this->end_date) && $this->enabled)
            return true;

        return false;
    }

}