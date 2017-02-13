<?php

class Resume_Model_VideoRating extends SharedResources_Model_Item_Abstract {

  /**
   * Return video rating table object
   *
   * @return table object
   * */
  public function getTable() {
    if (is_null($this->_table)) {
      $this->_table = Engine_Api::_()->getDbtable('videoRatings', 'resume');
    }
    return $this->_table;
  }

  public function getIdentity() {
    return parent::getIdentity();
  }

}
?>