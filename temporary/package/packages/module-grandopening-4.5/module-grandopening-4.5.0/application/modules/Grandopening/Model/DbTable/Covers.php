<?php

class Grandopening_Model_DbTable_Covers extends Engine_Db_Table {

    protected $_rowClass = 'Grandopening_Model_Cover';
    protected $_cache;
    protected $_covers;
    public $isEmpty = false;

    public function init() {
        $this->_cache = Zend_Registry::get('Zend_Cache');
        $this->_loadCovers();
    }

    public function reloadCovers() {
        $this->_cache->remove('covers');
        $this->_covers = null;
        $this->_loadCovers();
    }

    public function findByTitle($title) {
        $select = $this->select();
        $select->from($this, array('cover_id', new Zend_Db_Expr('DATE(start_date) as start_date'), new Zend_Db_Expr('DATE(end_date) as end_date'), 'title', 'enabled'));
        $select->where('title = ?', $title);

        return $this->fetchRow($select);
    }

    public function getCover() {
        $covers = array();
        foreach ($this->_covers as $cover) {
            if ($cover['enabled']) {
                $now = date('Y-m-d');
                if ((null != $cover['start_date'] && $now >= $cover['start_date']) && (null == $cover['end_date'] || $now <= $cover['end_date']))
                    $covers[] = $cover;
            }
        }
        if (count($covers) == 0)
            return null;
        elseif (count($covers) > 1)
            return $covers[array_rand($covers)];
        else
            return $covers[0];
    }

    //Utility
    protected function _loadCovers() {
        if (!($data = $this->_cache->load('covers'))) {
            $select = $this->select();
            $select->from($this, array('cover_id', new Zend_Db_Expr('DATE(start_date) as start_date'), new Zend_Db_Expr('DATE(end_date) as end_date'), 'title', 'enabled'));

            $data = $this->fetchAll($select);

            if (count($data) > 0) {
                $this->_covers = $data->toArray();
                $this->_saveCovers();
            }
            else
                $this->isEmpty = true;
        }
        else
            $this->_covers = $data;
    }

    protected function _saveCovers() {
        $this->_cache->save($this->_covers, 'covers');
    }

}